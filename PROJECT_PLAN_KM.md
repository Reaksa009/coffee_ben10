# ផែនការគម្រោង Coffee Ben10 POS

ឯកសារនេះជាឯកសារធ្វើការសម្រាប់រៀបចំគម្រោង POS ហាងកាហ្វេ Coffee Ben10។ អាចកែបន្ថែមបានតាមការអភិវឌ្ឍន៍ពិត។

## 0. Project Input Fields

ផ្នែកនេះសម្រាប់បំពេញព័ត៌មានគម្រោង។ អាចកែតម្លៃក្នុងចន្លោះទទេបានតាមការងារពិត។

### ព័ត៌មានគម្រោង

| Field | Value |
| --- | --- |
| Project name | Coffee Ben10 POS |
| Shop name |  |
| Owner name |  |
| Phone number |  |
| Address |  |
| Start date |  |
| Target opening date |  |
| Current project status | Planning / Development / Testing / Deployment |

### Team និង Roles

| Role | Name | Responsibility |
| --- | --- | --- |
| Owner |  | Final decision, budget, shop operation |
| Admin |  | System setup, users, settings |
| Manager |  | Products, inventory, purchases, reports |
| Cashier |  | POS sales, payments, shift closing |
| Developer |  | Build, fix bugs, deploy |

### Budget និង Cost

| Item | Estimated Cost | Actual Cost | Notes |
| --- | ---: | ---: | --- |
| Computer / POS device |  |  |  |
| Receipt printer |  |  | 58mm or 80mm |
| Cash drawer |  |  |  |
| Internet |  |  |  |
| Hosting / domain |  |  |  |
| Development cost |  |  |  |
| Other |  |  |  |

### Feature Priority

| Feature | Priority | Status | Notes |
| --- | --- | --- | --- |
| POS sales | High | Done |  |
| KHQR payment | High | Done |  |
| Cashier shift closing | High | Done |  |
| Product/category CRUD | High | Done |  |
| Inventory | High | Done |  |
| Purchase management | High | Done |  |
| Receipt printer | High | Need printer test |  |
| Daily close report | High | Done |  |
| Staff activity log | Medium | Done |  |
| Backup/export | Medium | Done |  |
| Shop settings | Medium | Done |  |

### Deployment Input

| Field | Value |
| --- | --- |
| GitHub repository |  |
| Production URL |  |
| Hosting provider | Vercel / VPS / Shared hosting |
| Database type | MySQL / MongoDB |
| Database name |  |
| Admin email |  |
| KHQR account name |  |
| KHQR merchant city | PHNOM PENH |
| Receipt printer size | 58mm / 80mm |

### Daily Operation Input

| Field | Value |
| --- | --- |
| Opening time |  |
| Closing time |  |
| Default currency | USD |
| Tax rate | 0% |
| Service charge | 0% |
| Low stock check time |  |
| Daily close responsible person |  |

## 1. គោលបំណងគម្រោង

បង្កើតប្រព័ន្ធ POS សម្រាប់ហាងកាហ្វេដែលអាចគ្រប់គ្រងការលក់ ប្រភេទផលិតផល ស្តុកវត្ថុធាតុដើម ការទូទាត់ បុគ្គលិក របាយការណ៍ និងការបិទវេន cashier បានច្បាស់លាស់។

## 2. អ្នកប្រើប្រាស់សំខាន់

- Admin: គ្រប់គ្រងអ្នកប្រើប្រាស់ ការកំណត់ហាង លុប/កែទិន្នន័យសំខាន់ៗ និងមើលរបាយការណ៍។
- Manager: គ្រប់គ្រងផលិតផល ប្រភេទផលិតផល ស្តុក supplier purchase និងរបាយការណ៍។
- Cashier: បើកវេន បង្កើត order ទទួលការទូទាត់ បោះពុម្ពវិក្កយបត្រ និងបិទវេន។

## 3. Scope សម្រាប់ MVP

### POS និងការលក់

- បង្កើត order ពី POS
- ជ្រើសរើស size និង sugar level
- ជ្រើសរើស order type: dine-in, takeaway, delivery
- បញ្ចូល table number ឬ pickup/delivery name
- ទូទាត់តាម cash, card, wallet, KHQR
- បោះពុម្ព receipt និង reprint ពី order history

### Product និង Category

- CRUD category
- CRUD product
- តម្លៃតាម size: small, medium, large
- Upload រូបភាពផលិតផល
- Recipe/product costing
- Profit margin តាម product

### Inventory

- គ្រប់គ្រង coffee beans, milk, cups, lids, sugar, syrup
- Low-stock alerts
- កាត់ stock តាម recipe នៅពេលលក់
- បង្ហាញ quantity និង unit cost

### Supplier និង Purchase

- Supplier info
- Purchase records
- Restock inventory ពី purchase
- Purchase history/report

### Shift / Cashier Closing

- Opening cash
- Cash sales
- KHQR sales
- Cash in/out
- Expected cash
- Closing cash difference

### Reports

- Sales report
- Product performance
- Daily close report
- Sales by payment method
- Best-selling drinks
- Discounts used
- Low-stock summary

### Admin Tools

- Staff activity log
- Backup/export CSV
- Shop settings: shop name, address, phone, receipt footer, currency, receipt width

## 4. ស្ថានភាពបច្ចុប្បន្ន

### បានធ្វើរួច

- Login/Register/Profile
- Role permission: admin, manager, cashier
- Dashboard
- POS order flow
- KHQR payment flow
- Cash/card/wallet payment simulation
- Product CRUD
- Category CRUD
- Promo/discount
- Customer loyalty
- Order history
- Payment history
- Sales report និង product report
- Shift closing
- Ingredient inventory
- Product recipe costing
- Supplier info
- Purchase management
- Receipt print support
- Daily close report
- Staff activity log
- Backup/export
- Shop settings

### ត្រូវពិនិត្យបន្ថែម

- UI polishing សម្រាប់ mobile/tablet
- Receipt print test ជាមួយ printer 58mm/80mm ពិត
- KHQR production verification
- Backup schedule ប្រចាំថ្ងៃ
- Permission detail សម្រាប់ action សំខាន់ៗ

## 5. Development Phases

### Phase 1: Stabilize MVP

គោលដៅ: ឱ្យ workflow សំខាន់ៗដំណើរការល្អសម្រាប់ហាងបើកប្រើប្រាស់។

ការងារ:

- Test full sale flow: add product -> POS -> payment -> receipt
- Test shift open/close
- Test purchase restock
- Test recipe stock deduction
- Test daily close report
- Fix UI/validation errors

Acceptance criteria:

- Cashier អាចលក់ និងបោះពុម្ព receipt បាន
- Manager អាចបន្ថែម product/category/inventory/purchase បាន
- Report បង្ហាញទិន្នន័យត្រឹមត្រូវ
- Test suite pass

### Phase 2: Coffee Shop Operations

គោលដៅ: ធ្វើឱ្យប្រព័ន្ធសមស្របនឹងប្រតិបត្តិការហាងកាហ្វេពិត។

ការងារ:

- Recipe template សម្រាប់ drink ទូទៅ
- Low-stock dashboard card
- Purchase report តាម supplier/date
- Staff action filtering
- Export report តាម date range

Acceptance criteria:

- Manager មើលដឹងថា stock ណាខ្វះ
- Product cost/margin អាចជួយសម្រេចតម្លៃលក់
- Purchase history អាច trace តាម supplier

### Phase 3: Production Readiness

គោលដៅ: រៀបចំ app សម្រាប់ deploy និងប្រើប្រាស់ជាក់ស្តែង។

ការងារ:

- Configure production environment
- Set APP_KEY, APP_URL, DB credentials
- Verify KHQR credentials
- Run migrations on production database
- Smoke test login/POS/payment/report
- Prepare rollback plan

Acceptance criteria:

- App deploy ដោយគ្មាន error
- Production DB មាន table គ្រប់គ្រាន់
- Admin account អាច login
- Payment and receipt flow ដំណើរការ

## 6. Backlog

### High Priority

- Improve product create/edit validation message
- Add search/filter in inventory
- Add purchase date range report
- Add receipt layout preview in shop settings
- Add cashier shift summary export

### Medium Priority

- Customer order history detail
- Product ingredient usage report
- Supplier payment status
- Staff performance report
- Daily backup reminder

### Low Priority

- Dark mode
- Barcode support
- Multi-branch support
- Kitchen display screen
- Telegram alerts for low stock

## 7. Risk និងការកាត់បន្ថយហានិភ័យ

- KHQR provider changes: រក្សា fallback manual verification ឬ simulated flow សម្រាប់ test។
- Inventory inaccurate: ត្រូវបណ្តុះបណ្តាល staff ឱ្យ record purchase និង recipe ត្រឹមត្រូវ។
- Printer compatibility: ត្រូវ test ជាមួយ thermal printer ពិត មុនដាក់ប្រើប្រាស់។
- Data loss: ត្រូវមាន CSV export និង backup schedule។
- Permission mistake: ត្រូវ test role admin/manager/cashier មុន deploy។

## 8. Testing Checklist

- Login as admin
- Login as manager
- Login as cashier
- Create category
- Create product
- Add recipe ingredients
- Create inventory item
- Create supplier
- Create purchase and restock inventory
- Open cashier shift
- Place dine-in order
- Place takeaway order
- Place delivery order
- Process cash payment
- Process KHQR payment
- Print receipt
- Reprint receipt from order history
- Close cashier shift
- View daily close report
- Export CSV backup

## 9. Deployment Checklist

- Pull latest code
- Run `composer install --no-dev --optimize-autoloader`
- Set `.env` production variables
- Run `php artisan key:generate` if needed
- Run `php artisan migrate --force`
- Run `php artisan config:cache`
- Run `php artisan route:cache`
- Run `php artisan view:cache`
- Test `/login`
- Test `/dashboard`
- Test `/pos`
- Test one full order

## 10. Weekly Working Plan

### សប្តាហ៍ទី 1

- Stabilize product/category/POS flow
- Fix known errors
- Test add product and checkout
- Prepare default inventory items

### សប្តាហ៍ទី 2

- Finish inventory and purchase workflow
- Improve reports
- Test receipt printer
- Prepare training notes for cashier

### សប្តាហ៍ទី 3

- Production deployment
- Smoke testing
- Staff feedback
- Bug fixes

## 11. Decision Log

| Date | Decision | Reason |
| --- | --- | --- |
| 2026-06-03 | Use Laravel + Blade | Existing project already uses Laravel and Blade. |
| 2026-06-03 | Use role-based access | Admin, manager, cashier need different permissions. |
| 2026-06-03 | Track ingredient inventory by recipe | Coffee shop needs product cost and stock usage. |
| 2026-06-03 | Support 58mm/80mm receipt | Common thermal printer sizes for POS. |

## 12. Next Actions

- Review this plan with the shop owner/manager
- Confirm which features are required before opening day
- Test all cashier workflows on the actual computer
- Test receipt printer hardware
- Push code to GitHub
- Deploy production version
