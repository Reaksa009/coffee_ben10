<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ShopSettingController extends Controller
{
    public function edit()
    {
        $settings = ShopSetting::current();

        return view('shop-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = ShopSetting::current();
        $settings->update($request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:80'],
            'receipt_footer' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'max:10'],
            'receipt_width_mm' => ['required', 'integer', 'in:58,80'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'service_charge_rate' => ['required', 'numeric', 'min:0'],
        ]));

        ActivityLogger::log('settings.updated', 'Updated shop settings', $settings);

        return redirect()->route('shop-settings.edit')->with('success', 'Shop settings updated.');
    }
}
