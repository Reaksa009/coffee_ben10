<?php

namespace App\Services;

use App\Models\Order;
use Carbon\CarbonInterface;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;
use KHQR\Models\IndividualInfo;
use KHQR\Models\MerchantInfo;
use Illuminate\Support\Facades\Http;

class KHQRService
{
    protected string $provider;
    protected string $bakongAccountId;
    protected string $accountName;
    protected string $merchantCity;
    protected int $currency;
    protected string $apiBaseUrl;
    protected ?string $apiKey;
    protected ?string $merchantId;
    protected ?string $acquiringBank;
    protected int $dynamicQrExpiresIn;

    public function __construct()
    {
        $this->provider = (string) config('khqr.provider', 'local');
        $this->bakongAccountId = $this->requiredString(
            'KHQR_BAKONG_ACCOUNT_ID',
            config('khqr.bakong_account_id')
        );
        $this->accountName = (string) (config('khqr.account_name') ?? 'POS System');
        $this->merchantCity = (string) (config('khqr.merchant_city') ?? 'PHNOM PENH');
        $this->currency = $this->currencyCode((string) (config('khqr.currency') ?? 'USD'));
        $this->apiBaseUrl = rtrim((string) config('khqr.api_base_url', 'https://api.khqr.link'), '/');
        $this->apiKey = config('khqr.api_key');
        $this->merchantId = config('khqr.merchant_id');
        $this->acquiringBank = config('khqr.acquiring_bank');
        $this->dynamicQrExpiresIn = (int) config('khqr.dynamic_qr_expires_in', 180);
    }

    public function createPaymentRequest(Order $order): array
    {
        if ($this->usesKhqrLink()) {
            return $this->createKhqrLinkPaymentRequest($order);
        }

        return $this->createLocalPaymentRequest($order);
    }

    public function checkPaymentStatus(string $md5): array
    {
        $request = Http::acceptJson()->timeout(15);

        if ($this->apiKey) {
            $request = $request->withHeaders(['X-API-Key' => $this->apiKey]);
        }

        $response = $request->get($this->apiBaseUrl . '/v1/khqr/check', [
            'md5' => $md5,
            'bakongid' => $this->bakongAccountId,
        ]);

        if (! $response->ok()) {
            throw new \RuntimeException('KHQR Link status check failed with HTTP ' . $response->status() . '.');
        }

        return $response->json() ?? [];
    }

    public function usesKhqrLink(): bool
    {
        return strtolower($this->provider) === 'khqr_link';
    }

    private function createLocalPaymentRequest(Order $order): array
    {
        $amount = $this->formatAmount((float) $order->total_amount);
        $reference = $this->paymentReference($order);
        $createdAt = now();
        $expiresAt = $createdAt->copy()->addSeconds($this->dynamicQrExpiresIn);

        $optionalData = [
            'currency' => $this->currency,
            'amount' => $amount,
            'billNumber' => $reference,
            'purposeOfTransaction' => $this->orderDescription($order),
        ];

        if ($this->merchantId && $this->acquiringBank) {
            $khqrInfo = MerchantInfo::withOptionalArray(
                $this->bakongAccountId,
                $this->accountName,
                $this->merchantCity,
                $this->merchantId,
                $this->acquiringBank,
                $optionalData
            );
            $response = BakongKHQR::generateMerchant($khqrInfo);
        } else {
            $khqrInfo = IndividualInfo::withOptionalArray(
                $this->bakongAccountId,
                $this->accountName,
                $this->merchantCity,
                $optionalData
            );
            $response = BakongKHQR::generateIndividual($khqrInfo);
        }

        $qrData = $response->data['qr'] ?? null;
        if (! $qrData) {
            throw new \RuntimeException('Bakong KHQR generator did not return a QR payload.');
        }

        $qrData = $this->withDynamicExpiration($qrData, $createdAt, $expiresAt);

        return [
            'provider' => 'khqr',
            'qr_data' => $qrData,
            'qr_image_url' => null,
            'payment_url' => url('/pos/checkout/confirm?order=' . $order->id),
            'reference' => $reference,
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_in_seconds' => $this->dynamicQrExpiresIn,
            'md5' => md5($qrData),
            'credential' => $this->bakongAccountId,
            'account_name' => $this->accountName,
            'raw_payload' => [
                'bakong_account_id' => $this->bakongAccountId,
                'merchant_name' => $this->accountName,
                'merchant_city' => $this->merchantCity,
                'amount' => floatval($amount),
                'currency' => $this->currency === KHQRData::CURRENCY_KHR ? 'KHR' : 'USD',
                'reference' => $reference,
                'description' => $this->orderDescription($order),
                'created_at' => $createdAt->toIso8601String(),
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ];
    }

    private function createKhqrLinkPaymentRequest(Order $order): array
    {
        $amount = $this->formatAmount((float) $order->total_amount);
        $request = Http::acceptJson()->timeout(15);

        if ($this->apiKey) {
            $request = $request->withHeaders(['X-API-Key' => $this->apiKey]);
        }

        $response = $request->get($this->apiBaseUrl . '/v1/khqr/create', [
            'amount' => $amount,
            'bakongid' => $this->bakongAccountId,
            'merchantname' => $this->accountName,
        ]);

        if (! $response->ok()) {
            throw new \RuntimeException('KHQR Link create failed with HTTP ' . $response->status() . '.');
        }

        $data = $response->json() ?? [];
        if (($data['status'] ?? null) !== 'success' || empty($data['qr']) || empty($data['md5'])) {
            throw new \RuntimeException($data['message'] ?? 'KHQR Link did not return a usable payment response.');
        }

        $expiresAt = isset($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : null;
        $createdAt = isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : now();

        return [
            'provider' => 'khqr_link',
            'qr_data' => null,
            'qr_image_url' => $this->secureUrl((string) $data['qr']),
            'payment_url' => null,
            'reference' => $data['tran'] ?? $this->paymentReference($order),
            'expires_at' => $expiresAt?->toIso8601String(),
            'expires_in_seconds' => $expiresAt ? max(0, now()->diffInSeconds($expiresAt, false)) : null,
            'md5' => $data['md5'],
            'tran' => $data['tran'] ?? null,
            'credential' => $this->bakongAccountId,
            'account_name' => $this->accountName,
            'raw_payload' => [
                'bakong_account_id' => $this->bakongAccountId,
                'merchant_name' => $this->accountName,
                'merchant_city' => $this->merchantCity,
                'amount' => (float) ($data['amount'] ?? $amount),
                'currency' => $data['currency'] ?? ($this->currency === KHQRData::CURRENCY_KHR ? 'KHR' : 'USD'),
                'reference' => $data['tran'] ?? null,
                'description' => $this->orderDescription($order),
                'created_at' => $createdAt->toIso8601String(),
                'expires_at' => $expiresAt?->toIso8601String(),
            ],
            'khqr_link_response' => $data,
        ];
    }

    private function currencyCode(string $currency): int
    {
        return strtoupper($currency) === 'KHR'
            ? KHQRData::CURRENCY_KHR
            : KHQRData::CURRENCY_USD;
    }

    private function requiredString(string $key, ?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            throw new \RuntimeException("Missing {$key}. Set it to your Bakong account ID, for example name@bank.");
        }

        return $value;
    }

    private function formatAmount(float $amount): float
    {
        if ($this->currency === KHQRData::CURRENCY_KHR) {
            return (float) round($amount);
        }

        return (float) number_format($amount, 2, '.', '');
    }

    private function paymentReference(Order $order): string
    {
        return substr(
            'K' . $order->id . '-' . now()->format('ymdHis') . random_int(100, 999),
            0,
            25
        );
    }

    private function orderDescription(Order $order): string
    {
        return 'Order '.$order->display_order_label;
    }

    private function withDynamicExpiration(string $qrData, CarbonInterface $createdAt, CarbonInterface $expiresAt): string
    {
        $crcTagPosition = strlen($qrData) - 8;
        if ($crcTagPosition < 0 || substr($qrData, $crcTagPosition, 4) !== '6304') {
            throw new \RuntimeException('Generated KHQR payload has an invalid CRC segment.');
        }

        $payload = substr($qrData, 0, $crcTagPosition);
        $timestampBounds = $this->findTimestampBounds($payload);
        $timestamp = $this->timestampSegment($createdAt, $expiresAt);

        if ($timestampBounds) {
            [$start, $end] = $timestampBounds;
            $payload = substr($payload, 0, $start) . $timestamp . substr($payload, $end);
        } else {
            $payload .= $timestamp;
        }

        $payloadWithCrcTag = $payload . '6304';

        return $payloadWithCrcTag . Utils::crc16($payloadWithCrcTag);
    }

    private function findTimestampBounds(string $payload): ?array
    {
        $offset = 0;
        $length = strlen($payload);

        while ($offset + 4 <= $length) {
            $tag = substr($payload, $offset, 2);
            $valueLength = (int) substr($payload, $offset + 2, 2);
            $nextOffset = $offset + 4 + $valueLength;

            if ($nextOffset > $length) {
                throw new \RuntimeException('Generated KHQR payload has invalid TLV lengths.');
            }

            if ($tag === '99') {
                return [$offset, $nextOffset];
            }

            $offset = $nextOffset;
        }

        return null;
    }

    private function timestampSegment(CarbonInterface $createdAt, CarbonInterface $expiresAt): string
    {
        $value = '0013' . $this->timestampInMilliseconds($createdAt)
            . '0113' . $this->timestampInMilliseconds($expiresAt);

        return '99' . strlen($value) . $value;
    }

    private function timestampInMilliseconds(CarbonInterface $time): string
    {
        return (string) (((int) $time->format('U')) * 1000 + (int) floor(((int) $time->format('u')) / 1000));
    }

    private function secureUrl(string $url): string
    {
        return str_starts_with($url, 'http://')
            ? 'https://' . substr($url, 7)
            : $url;
    }
}
