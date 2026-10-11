# Healing My-Marriage International: website rebuild

Stack: PHP 8+, Bootstrap 5.3 (bundled locally), vanilla JavaScript, custom CSS. No database. Content lives in JSON files; forms save to files.

## Run locally
    php -S localhost:8000
Open http://localhost:8000

## Deploy (cPanel / Apache)
1. Upload everything to `public_html` (or a subfolder).
2. Edit `includes/config.php`: set `base_url` to `https://healingmymarriage.org`, confirm phones, email, hours, and paste a GA4 ID in `ga_id`.
3. Make `data/submissions/` writable (chmod 750 or 755). It is blocked from web access by `.htaccess`.
4. When the server can send mail, set `mail_enabled` to `true` in `config.php`. Submissions are always saved to `data/submissions/*.jsonl` regardless.
5. In `.htaccess`, uncomment the HTTPS redirect once SSL is active. Update the Sitemap line in `robots.txt`.

## Where to edit content
| What | Where |
|---|---|
| Contact details, hours, social links, menu | `includes/config.php` |
| Articles / teaching | `data/posts.json` |
| Events (remove `"sample": true` on real ones) | `data/events.json` |
| Shop products (price in Naira, optional `image` path, `description`) | `data/products.json` |
| Vision, mission, faith statement, leaders, history | top of `about.php` (empty = section hidden) |
| Colours and fonts | `:root` block at the top of `assets/css/style.css` |

## Shop
The five e-books and prices come from the old site's shop (Foundation for Living N13,500; I Want To Marry N12,000; Love Portion N15,000; The Logics of Parenting N18,000; The Understanding of Marriage N15,000). `shop.php` has sorting, price filter, a cart, and an order-request form (`actions/order.php`). The server re-prices every order from `products.json`, saves it to `data/submissions/orders.jsonl`, and emails it when mail is enabled. Payment is not taken on the site yet: the ministry replies with payment options and sends the e-book. Add real cover images to `assets/img/` and set `"image"` in `products.json`; add a `"description"` to show a synopsis on each card.

## Form handling
`actions/counseling.php`, `actions/contact.php`, `actions/newsletter.php`, `actions/order.php`: CSRF token, honeypot field, 8-second resubmit guard, server-side validation, header-injection-safe cleaning, output escaping everywhere.

## The logo
`assets/img/logo.jpeg` is the supplied file, unaltered. The favicon files are a square crop of the emblem only. For best results supply a transparent PNG or SVG of the logo and replace the JPEG (same file name or update `header.php` and `footer.php`).

## Needs confirmation from the ministry
- Which phone and hours are correct. The old site shows Mon to Sat 8am to 5pm in one place and Mon to Fri 10am to 5pm in another. The old header also shows a template placeholder email (askme@consultio.com); this build uses info@healingmymarriage.org.
- Whether Reposition / Refix / Rekindle (read from the logo ring) is the ministry's own framework, and its order and wording.
- The founder's name, bio and photo (the homepage quote is from the old site's welcome text, unattributed).
- Vision, mission, statement of faith, leadership, history.
- Articles in `posts.json` are short draft copy written from the old post titles. Replace with the ministry's own teaching.
- Events in `events.json` are marked SAMPLE. Replace with real events.
- Counseling fees and the confidentiality wording in the FAQ and privacy page.
- The "5 years of experience" line on the old site was not used because it could not be verified.
- How e-books are paid for and delivered (the shop page says the ministry replies with payment options and emails the e-book). Also a synopsis for each e-book, which the old site did not show.
- Real photography: none was available. The cover tiles are made from the logo's own shapes and can be swapped for photos.

## Not built yet (recommended next)
Online card payment (Paystack/Flutterwave) and automatic e-book delivery, individual product pages, online giving, embedded YouTube sermon archive with speaker/scripture filters, admin panel for posts/events, photo and video gallery, Google Maps embed (a link-out card is used for now), and the old site's Community page.
