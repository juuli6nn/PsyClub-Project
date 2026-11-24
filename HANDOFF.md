# Project Handoff: PsyClub Membership Management & Transaction Processing System

Paste this file into another AI to bring it up to speed. Secrets are intentionally NOT included; they live in `.env`.

## 1. What this is
A school project for Prof. John Benedic R. Enriquez. It is a Laravel 12 / PHP 8.2 / MySQL admin web app (server-rendered Blade, no API) for managing members, membership categories and levels, and membership transactions (applications, renewals, payments, receipts).

- Project path: `/Applications/XAMPP/xamppfiles/htdocs/membership-system`
- Stack: Laravel 12, Pest 3, Tailwind CSS v4 + Vite 7, MySQL (db `membership_system`, user `root`, no password), Brevo SMTP, OpenWeather, Leaflet/OpenStreetMap.
- Brand: "PsyClub" with a Psyduck logo at `public/images/logo.png` (transparent PNG).
- UI rules: minimalist Vercel-style dark theme (`bg-black`, `zinc-950` cards, `zinc-800` borders, white primary buttons, Geist font, emerald accents). Left sidebar layout in `resources/views/layouts/app.blade.php`. Row actions and back links are styled as small bordered buttons, not plain hyperlinks. The auth pages use plain text links ("Sign up" / "Sign in").
- User preferences: keep UI simple and basic. At the end of each chapter, give: Changes done/added, Where to see it, How to verify.

## 2. Rubric (original requirements)
- Member CRUD, search by name/category/level, deactivate.
- Categories: Student, Regular, Senior Citizen, Corporate. Levels: Basic, Silver, Gold, Platinum, each with fee, validity and benefits.
- Transaction Processing: application, renewal, payments, official reference numbers, statuses Pending/Paid/Approved/Expired/Cancelled, history per member.
- Tables: users, members, membership_categories, membership_levels, membership_transactions, payments.
- Validation: required fields, email format, unique membership number, payment amount.
- Business rules: payments must link to a valid transaction; only Paid transactions can be Approved.
- Auth via Laravel's built-in system. Pages: login, dashboard, members, categories, levels, transactions, payments, member history, printable receipt.
- Decision made: follow the rubric as an admin-only portal (no customer-facing side), except the public application portal from the prof's tutorial.

## 3. What is DONE

### Core system (Chapters 1-8)
- Migrations and models: `Member`, `MembershipCategory`, `MembershipLevel`, `MembershipTransaction`, `Payment` with relationships.
- Controllers: `MemberController` (CRUD, search, toggleStatus, print, auto-creates a Pending application transaction on register), `MembershipCategoryController`, `MembershipLevelController`, `TransactionController` (index with status filter and search, create, store, show, renew, receipt, updateStatus enforcing "only Paid -> Approved"), `PaymentController` (index, create, store; blocks overpayment and Cancelled/Expired transactions; auto Pending -> Paid when fully paid), `DashboardController`.
- Refs: members `MEM-XXXXXX`, transactions `TXN-YYYYMMDD-XXXX`, payments `PAY-YYYYMMDD-XXXX`.
- Dashboard: 4 KPI cards, recent activity, level ranking; "Hello, {username}!" plus live weather greeting.
- Printable receipt (`transactions/receipt`) and member record (`members/print`) with print CSS.
- Seeder: admin user (`admin` / `admin@example.com` / `password`, verified, profile completed), 4 categories (with level and fee), 4 levels, 4 sample members (Nagi Seishiro, Reo Mikage, Isagi Yoichi, Bachira Meguru) with transactions and payments.
- Reset to a clean slate: `php artisan migrate:fresh --seed`.

### Prof's PDF tutorial (Chapters 9-13)
- Chapter 9: `/register` and `/login` with `MustVerifyEmail`, Brevo SMTP, verify-email flow (`verification.notice`, `verification.verify`, `verification.send`).
- Chapter 10: migration `2026_11_24_000003` adds `membership_applications` table, location fields, and category `level`, `fee`, `is_active`. Model `MembershipApplication` and `StoreMembershipApplicationRequest`.
- Chapter 11: `App\Notifications\MembershipApplicationSubmitted` (ShouldQueue, `afterCommit()`), `MembershipApplicationController` (`DB::transaction`, syncs applicant into Members + Pending Transaction, queues email). Routes `/membership/apply` (public GET/POST).
- Chapter 12: `WeatherController` at `GET /weather?lat&lon` (server-side OpenWeather call with Open-Meteo fallback). `/membership/apply` has a Leaflet/OSM map (Google Maps is only used if `GOOGLE_MAPS_KEY` is set; it is empty because Google needs billing).
- Chapter 13: `tests/Feature/MembershipApplicationTest.php` plus `tests/Feature/ExampleTest.php`. 7 tests / 29 assertions pass.

### Documentation-PDF alignment (the "KaOrg" sample), Chapter A (done)
- Username (no spaces) at sign-up; login accepts username or email.
- After verifying email, new users go to the "Complete your profile" page (`/profile`).
- `ProfileController` + `profile/edit.blade.php`: first/last name, phone, address fields (street, city, province, country code, postal code), Leaflet pin map with Nominatim reverse geocoding that auto-fills the address, "Updating your address" overlay, remove pin, weather at address.
- Users migration `2026_11_24_000004` adds username, first_name, last_name, phone, address fields, latitude, longitude, profile_completed_at.
- Sidebar bottom-left profile badge links to `/profile`, with a Sign out link. The top-right header was cleaned up (only a "Verify Email" badge shows, for unverified users).

## 4. What is LEFT (in order)
1. **Chapter B (was just starting, no code written yet):** Members page (`/members`) needs Category, Level and Status filter dropdowns next to the search box. Edit `MemberController@index` to filter by `membership_category_id`, `membership_level_id` and `is_active` (all = no filter), and `resources/views/members/index.blade.php` to add a GET form with the selects. Keep the existing search behavior and button styling.
2. **Chapter C:** New sidebar page "Membership status" (`/membership-status`): a table of members, each row with a Payment dropdown (Paid/Unpaid), a Membership status dropdown (Active/Inactive) and a Save button, plus search. Needs a route, controller, view and a sidebar link. Map Payment Paid/Unpaid onto the member's latest transaction (e.g. Paid/Approved = Paid) and Active/Inactive onto `members.is_active`.
3. Optional: a "Verify my application" clickable link in the application email (the user chose NOT to do this for now).
4. Final pass: run Pint, Pest, `npm run build`, and reseed.

## 5. Environment quirks
- `php` is not on PATH. Use `/Applications/XAMPP/xamppfiles/bin/php`. The sandbox blocks it, so commands that run PHP need `BypassSandbox=true`.
- Pint: `vendor/bin/pint --format agent` (no `--dirty`; the project is not a git repo).
- Tests: `php artisan test --compact`. Test DB is in-memory SQLite.
- After editing Blade/Tailwind classes, run `npm run build`. If rollup/oxide native modules are missing, run `npm install` (needed once, with sandbox bypass).
- Queue is database-driven. To deliver queued emails run `php artisan queue:work`.
- Routes live in `routes/web.php`; all admin routes are inside the `auth` middleware group, `/membership/apply` and `/weather` are outside it.
- Project rules (from `AGENTS.md`): follow existing conventions, use explicit return types where practical, run Pint after PHP changes, do not add dependencies without approval, only create documentation when asked.

## 6. Secrets / config (see `.env`, not repeated here)
`MAIL_*` is configured for Brevo (`smtp-relay.brevo.com:587`, from address `julianthegreat725@gmail.com`, from name "PsyClub"). `OPENWEATHER_KEY` is set (new keys can take 10-15 minutes to activate; the controller falls back to Open-Meteo). `GOOGLE_MAPS_KEY` is intentionally blank. The Brevo SMTP key was shared in chat, so consider rotating it before submitting or publishing.
