# LifeScend platform handbook

**Audit date:** 29 September 2026. **Basis:** repository source, deployment layout, and read-only production configuration. This is a description of implemented behavior, not an end-to-end payment certification. No live purchase was made for this audit. Do not treat inferred business practices as contracts or accounting policy.

## At a glance

LifeScend is a single-organization Laravel web application for promoting events and training, selling event tickets, managing private training requests, and checking attendees in. The public site is the storefront; authenticated customers have a dashboard; staff run ticket and check-in operations; managers and admins maintain content, users, sales, and reporting. Stripe handles online card payment. Email is used for verification, purchase confirmation, enquiries, and staff notifications.

The implemented revenue model is event ticket sales plus leads for custom training. There is no subscription billing, seller marketplace, commission or payout workflow in the code reviewed. Whether private trainings are priced or invoiced off-platform is a business question for LifeScend, not established by this application.

## Platform setup and ownership

| Layer | What is implemented |
| --- | --- |
| Application | PHP 8.3+ / Laravel 13, server-rendered Blade views, Composer dependencies and frontend assets. |
| Hosting | cPanel account `lifenhzd`; Laravel app at `/home/lifenhzd/lifescend`; public web root at `/home/lifenhzd/public_html`. The production entry point has server-specific paths. |
| Data | MySQL-backed Laravel models and migrations. Database sessions and cache are configured in production. |
| Payments | Stripe Payment Intents and a signed `payment_intent.succeeded` webhook. Stripe secret and webhook secret are configured on the server, not tracked in Git. |
| Mail | SMTP transport configured in production. Actual delivery and inbox receipt were not tested in this audit. |
| Files | Event and organization media, uploaded enquiry attachments, logs, dependencies, and `.env` live outside Git. |
| Version control | Private GitHub repository `johnkiunga01/lifescend`. The server is **not** a Git checkout; a GitHub push alone does not publish changes. |
| Deployment | Reviewed changes are copied over SSH after comparison and backup. The production `.env`, uploads, and server-specific web entry point must be preserved. See the repository `README.md`. |

There is an installation/setup route intended for first installation, guarded after setup. The application assumes one organization: the organization-scoping middleware and role selection use the first organization rather than a tenant selected by the user. There are no application-defined scheduled jobs in `routes/console.php`. Production uses the `database` queue connection, but continuous worker and cron operation were not verified.

## Modules and who uses them

| Area | Guest | Customer | Staff | Manager | Admin |
| --- | :---: | :---: | :---: | :---: | :---: |
| Browse events, speakers/partners, legal pages | Yes | Yes | Yes | Yes | Yes |
| Register, verify email, recover account | Yes | Yes | Yes | Yes | Yes |
| Buy tickets and use coupons | Register first | Yes | Yes | Yes | Yes |
| Tickets, billing history, saved events, calendar, account | No | Yes | Yes | Yes | Yes |
| Send event enquiry | Yes | Yes | Yes | Yes | Yes |
| Submit and view training request | No | Yes | Yes | Yes | Yes |
| Ticket management and attendee check-in | No | No | Yes | Yes | Yes |
| Manage events, prices, venues, speakers, sponsors, partners | No | No | No | Yes | Yes |
| Manage payments/reservations, coupons, reports, enquiries | No | No | No | Yes | Yes |
| Manage users and organization settings | No | No | No | Yes* | Yes |

*Managers have user-management access but are intended to be limited to customer and staff accounts; admins can assign any role. Check the access-control concern below before treating that boundary as fully enforced.

The role hierarchy in middleware is `customer < staff < manager < admin`. Registration creates a customer. Staff, manager and admin accounts are created or changed through user management. Dashboard views are role-specific, but many customer routes are shared by every logged-in role.

### Main features

- **Public content:** home, event listings/details, about, partners, testimonials, contact, terms and privacy. Events can have ticket types, prices, dates, venue/online details, speakers, sponsors, rich description, and media.
- **Checkout:** individual or company booking details, attendee information now or later, ticket quantity, coupon validation, and Stripe card payment. EUR is the payable currency for a ticket type with an explicit EUR fee; otherwise legacy USD remains payable. USD is a display alternative when both values exist. Prices are manually set, not currency-converted.
- **Tickets:** unique ticket records and codes, QR display, attendee details, status, and staff check-in. Customer can edit attendee information before the ticket is canceled or checked in.
- **Training:** customers request public/private, online/in-house training; staff or managers can accept and schedule a linked event. Current scheduling code creates USD-only ticket types; an event can subsequently be edited to add EUR.
- **Operations:** event, venue, type, speaker, sponsor, partner, coupon, user, testimonial, support, contact and enquiry management; manual reservation/payment handling; attendance and sales reports.
- **Notifications:** verification and transactional email plus in-app notification/activity views. Some notifications use the database queue; delivery depends on a working queue runner and SMTP.

## Customer journey

### 1. Discover and register

A visitor browses published events and can send an event enquiry without an account. Checkout requires login. Registration creates a customer account, requires terms acceptance, and sends a six-digit email verification code. Login is gated by verification and account status. Users may be asked to renew consent when terms/privacy versions change. Password reset and blocked-account support request flows exist.

### 2. Select and pay

The customer selects ticket type and quantity, can apply a coupon, and enters attendee or company details. The server checks availability, per-user limits, ticket sales eligibility, and coupon validity. A Stripe Payment Intent is created in the selected ticket's payable currency. With an exact EUR fee present, Stripe charges EUR; the USD amount is informational. Existing USD-only ticket types still charge USD until a manager adds an exact EUR fee. The checkout does not calculate exchange rates.

### 3. After successful payment

The browser calls the server's confirmation endpoint; Stripe's signed webhook is a second fulfillment path if the browser does not finish. Once the application processes a succeeded payment, it creates a completed order, order items, one ticket and attendee record per seat, increments sold counts, records coupon use, creates an invoice *record*, and logs the purchase. The customer is redirected to My Tickets. A confirmation email is attempted, and managers/notification email may receive purchase alerts. Duplicate fulfillment of the same Payment Intent is intended to be prevented by its unique order identifier.

The customer can then open each ticket's QR/code view, update attendee information until check-in/cancellation, review orders under Billing, and see purchased events on the dashboard/calendar. At the event, staff select the event, scan or enter a ticket code, verify it, and confirm check-in; the system marks the ticket used and records the check-in.

**Important distinction:** the invoice model stores a number and a future-looking PDF path, but the reviewed code does not generate an invoice PDF or offer a download endpoint. Billing is an order-history screen, not a verified tax-invoice system. The purchase email is not itself a ticket attachment; it links back to My Tickets.

### Other purchase paths

A fully free/100%-discounted purchase can create an order and tickets without Stripe. A legacy `reserve` endpoint still accepts pay-later orders even though the pay-later buttons were removed from the event page. Reserved orders do not issue tickets until staff/manager mark payment and fulfill them. This route should not be assumed disabled merely because the button is gone.

## Customer dashboard

The customer home view shows purchased passes, private trainings tied to their requests, and training-request activity. Navigation also exposes My Tickets, Calendar, Saved Events, Training Requests, Billing, Enquiries, Notifications, Testimonials, Settings, and Help. The calendar combines purchased-ticket events with preferred training dates. Billing lists the customer's orders and amounts/currencies. Enquiries are matched by the account email. Settings allows name, email, and password changes; changing email requires re-verification. In-app notifications are drawn from activity/notification records; they are not evidence that an email was delivered.

## Manager and staff workflows

1. A manager/admin creates a venue and event, adds description/media, speakers/sponsors and ticket types, enters exact EUR and USD prices where relevant, checks dates/capacity, then publishes it. The public listing and event page update from these records.
2. Staff watch ticket/reservation lists, complete manual payments when applicable, and manage attendee entry. They select an event for check-in and verify ticket codes before confirmation.
3. Managers/admins review payments, coupons, enquiries, contact messages, account-support requests and testimonials; they manage users and organization content/settings. Updating legal text can trigger renewed customer consent.
4. A training request starts as pending. Staff or a manager accepts it, then schedules a public or private linked event. Further commercial arrangements for a private training are not fully represented by this request workflow.
5. Reports expose sales, event performance, attendance, customer activity, coupons and revenue breakdowns. Their monetary totals currently need correction for mixed currencies before being used for accounting.

## Key records and relationships

`Organization` has users via `organization_user` memberships with roles. `Event` has a venue, ticket types, orders, tickets, attendees and check-ins. `TicketType` carries USD price (`price`) plus optional exact EUR price (`price_eur`) and sale prices. `Order` belongs to a user and event, has order items, tickets, an invoice record, optional coupon, currency and Stripe Payment Intent ID. Each `Ticket` belongs to an event, order, user and ticket type and has an attendee and QR/code. `TrainingRequest` belongs to a user and may link to a scheduled event. `EventEnquiry`, `ContactMessage`, `ActivityLog`, `CouponUsage`, `Refund`, and `AccountSupportRequest` support the surrounding workflows. Presence of a `Refund` model does not establish an automated refund workflow; that was not verified.

## Important gaps and risks

These are source-code findings to validate and prioritize, not claims that abuse has occurred.

1. **Payment confirmation integrity (urgent).** The authenticated `/stripe/confirm` handler checks that a supplied Stripe Payment Intent succeeded, but does not visibly bind its metadata to the current user/event or compare Stripe's paid amount/currency with the order it creates. It can also take ticket selection from the request rather than the intent metadata. This should be reviewed and fixed before relying on it for unattended paid fulfillment. The webhook path uses signed Stripe events but also recomputes ticket prices at fulfillment time, so price edits between intent creation and processing may cause discrepancies.
2. **Currency accuracy (high).** The purchase email hardcodes dollar symbols for EUR orders. Staff dashboards and reports sum USD and EUR amounts into a single dollar-labeled total. Such totals are not meaningful financial figures; report by currency or convert using an explicit recorded rate and policy. The training-request scheduling and manual reservation-completion flows still use USD-centric pricing.
3. **Access control (high).** Role middleware reads the first organization membership's role but does not check its membership status, while login checks `users.status`. A suspended organization membership may therefore retain role-based access if the user account remains active. Manager user-edit permissions also need a target-role review.
4. **Legacy reservation endpoint (medium).** Hiding the pay-later UI did not remove `POST /events/{slug}/reserve`. Decide whether it should be disabled entirely or kept as a controlled staff workflow.
5. **Fulfillment and email reliability (medium).** A successful Stripe charge can precede an application-side fulfillment failure; review Stripe events against orders/tickets and define a manual recovery process. Purchase-email exceptions in browser confirmation are swallowed, so an order can succeed without an email. Verify webhook delivery, SMTP and the queue runner in production.
6. **Invoice expectations (medium).** Invoice records have placeholder PDF paths; there is no verified PDF generation/download. Confirm legal invoice requirements before promising customers downloadable invoices.
7. **Operational coverage.** No end-to-end test payment, refund, duplicate webhook, email receipt, check-in or restoration drill was performed for this documentation. These need a Stripe test-mode environment with a separate database and mail sink, not live customer transactions.

## How to change the site safely

Use the private repository as the source of reviewed changes. Develop against a separate local database and Stripe test keys; do not use production credentials or customer data in Git. Review the change, run focused tests, compare the intended release to current server files, back up affected production files and data, then deploy only approved changes over SSH. Check migrations before running them. Preserve production `.env`, uploads, and the server-specific web entry point. Verify the affected public/customer/manager workflow after release and keep a rollback copy. GitHub is a backup and collaboration remote, not an automatic production deployment pipeline.

## Questions for LifeScend's owner

- Are private trainings quoted, paid and invoiced outside the app? Who owns that process?
- Is pay-later still a legitimate sales channel, or should the route be removed?
- What are the refund, cancellation, attendee-transfer, and tax-invoice policies?
- Should every published ticket type require an exact EUR fee, or may legacy USD checkout remain?
- Who monitors Stripe failures, support inboxes, the queue, backups, and post-event attendance?

## Code map

Start with `routes/web.php` for feature entry points; `app/Http/Controllers/DashboardController.php` and `CustomerController.php` for account views; `EventManagementController.php` for event editing; `StripePaymentController.php`, `EventController.php`, and `OrderFulfillmentService.php` for purchase paths; `TicketController.php` and `CheckInController.php` for admission; `TrainingRequestController.php` for custom training; `AdminUserController.php` and `EnsureUserHasRole.php` for roles; `ReportController.php` for reporting; `resources/views/layouts/dashboard.blade.php` for navigation; `database/migrations/` and `app/Models/` for the data model; and `README.md` for deployment cautions.
