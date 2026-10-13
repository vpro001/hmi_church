<?php
require_once __DIR__ . '/functions.php';

$page_key   = $page_key   ?? '';
$page_title = $page_title ?? site_setting('site_name', 'Healing My-Marriage International Ministry, Inc.');
$page_desc  = $page_desc  ?? site_setting('site_description', 'A faith-based marriage and family ministry in Rivers State, Nigeria.');
$page_path  = $page_path  ?? basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$full_title = ($page_key === 'home') 
    ? site_setting('site_name') . ' | ' . site_setting('site_tagline') 
    : $page_title . ' | ' . site_setting('site_short_name');
$canonical  = base_url() . '/' . ($page_path === 'index.php' ? '' : $page_path);
$og_image   = base_url() . '/assets/img/icon-512.png';
$flash      = take_flash();

$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => site_setting('site_name'),
    'url'      => base_url() . '/',
    'logo'     => base_url() . '/assets/img/logo.jpeg',
    'description' => site_setting('site_description'),
    'email'    => site_email(),
    'telephone'=> site_phones_raw()[0] ?? '+2347042226633',
    'address'  => ['@type' => 'PostalAddress', 'streetAddress' => 'Mercyland Central / Diamond Estate', 'addressRegion' => 'Rivers State', 'addressCountry' => 'NG'],
    'sameAs'   => array_values(array_filter([facebook_url(), youtube_url()])),
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($full_title) ?></title>
<meta name="description" content="<?= h($page_desc) ?>">
<meta name="theme-color" content="#0f261b">
<link rel="canonical" href="<?= h($canonical) ?>">
<link rel="icon" type="image/png" href="<?= h(asset('img/favicon.png')) ?>">
<link rel="apple-touch-icon" href="<?= h(asset('img/apple-touch-icon.png')) ?>">

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= h(site_setting('site_name')) ?>">
<meta property="og:title" content="<?= h($full_title) ?>">
<meta property="og:description" content="<?= h($page_desc) ?>">
<meta property="og:url" content="<?= h($canonical) ?>">
<meta property="og:image" content="<?= h($og_image) ?>">
<meta name="twitter:card" content="summary">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('css/style.css')) ?>" rel="stylesheet">

<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if (ga_id() !== ''): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= h(ga_id()) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= h(ga_id()) ?>');</script>
<?php endif; ?>
</head>
<body class="page-<?= h($page_key) ?>"<?= !empty($_SESSION['clear_cart']) ? ' data-clear-cart="1"' : '' ?>>
<?php unset($_SESSION['clear_cart']); ?>
<a class="skip-link" href="#main">Skip to main content</a>

<div class="topbar d-none d-lg-block">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-4 flex-wrap">
      <?php foreach (site_phones_raw() as $i => $raw): ?>
        <a href="<?= h(tel_link($raw)) ?>"><i class="bi bi-telephone-fill" aria-hidden="true"></i> <?= h(site_phones()[$i] ?? $raw) ?></a>
      <?php endforeach; ?>
      <a href="mailto:<?= h(site_email()) ?>"><i class="bi bi-envelope-fill" aria-hidden="true"></i> <?= h(site_email()) ?></a>
      <span><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <?= h(site_address()) ?></span>
    </div>
    <div class="d-flex gap-3 align-items-center">
      <a href="<?= h(facebook_url()) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
      <a href="<?= h(youtube_url()) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
    </div>
  </div>
</div>

<header class="site-header" id="siteHeader">
  <nav class="navbar navbar-expand-lg" aria-label="Main navigation">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= h(url('index.php')) ?>" aria-label="<?= h(site_setting('site_name')) ?> home">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International Ministry, Inc. logo" width="508" height="286" class="brand-logo">
      </a>
      <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0 order-lg-3">
        <button class="cart-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartPanel" aria-controls="cartPanel" aria-label="Open cart">
          <i class="bi bi-bag-fill" aria-hidden="true"></i><span class="cart-count" id="cartCount" hidden>0</span>
        </button>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-label="Open menu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
        <div class="offcanvas-header">
          <span class="offcanvas-title" id="mainMenuLabel">Menu</span>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
        </div>
        <div class="offcanvas-body align-items-lg-center">
          <ul class="navbar-nav ms-lg-auto me-lg-3">
            <?php foreach (NAV as $item): ?>
              <li class="nav-item">
                <a class="nav-link<?= $page_key === $item['key'] ? ' active' : '' ?>" href="<?= h(url($item['href'])) ?>"<?= $page_key === $item['key'] ? ' aria-current="page"' : '' ?>><?= h($item['label']) ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
          <form class="nav-search" role="search" action="<?= h(url('resources.php')) ?>" method="get">
            <label class="visually-hidden" for="navSearch">Search articles and events</label>
            <input id="navSearch" class="form-control" type="search" name="q" placeholder="Search">
            <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
          </form>
          <?php if (is_customer()): ?>
            <a class="btn btn-hmi-outline ms-lg-3 mt-3 mt-lg-0" href="<?= h(url('account.php')) ?>"><i class="bi bi-person me-1"></i> My Account</a>
            <a class="btn btn-hmi-red ms-lg-3 mt-3 mt-lg-0" href="<?= h(url('logout.php')) ?>">Logout</a>
          <?php else: ?>
            <a class="btn btn-hmi-outline ms-lg-3 mt-3 mt-lg-0" href="<?= h(url('login.php')) ?>"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
            <a class="btn btn-hmi-red ms-lg-3 mt-3 mt-lg-0" href="<?= h(url('register.php')) ?>">Register</a>
          <?php endif; ?>
          <a class="btn btn-hmi-red ms-lg-3 mt-3 mt-lg-0" href="<?= h(url('counseling.php#request')) ?>">Book counseling</a>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- Cart panel -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartPanel" aria-labelledby="cartTitle">
  <div class="offcanvas-header">
    <h2 class="offcanvas-title h5 mb-0" id="cartTitle">Your cart</h2>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close cart"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div id="cartEmpty" class="empty-state my-auto">
      <i class="bi bi-bag" aria-hidden="true"></i>
      <p class="mt-2 mb-3">Your cart is empty.</p>
      <a class="btn btn-hmi-green" href="<?= h(url('shop.php')) ?>">Browse the shop</a>
    </div>
    <div id="cartFilled" hidden>
      <ul class="cart-lines list-unstyled" id="cartLines"></ul>
      <div class="cart-total d-flex justify-content-between"><span>Total</span><strong id="cartTotal"><?= currency_symbol() ?>0</strong></div>
      <form action="<?= h(url('actions/order.php')) ?>" method="post" novalidate id="orderForm" class="mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="cart" id="cartJson">
        <h3 class="h6">Place your order request</h3>
        <p class="form-privacy">The ministry will contact you with payment options and send your e-book.</p>
        <div class="mb-2"><label class="form-label" for="o_name">Full name</label><input class="form-control" id="o_name" name="name" type="text" required maxlength="100" autocomplete="name" value="<?= is_customer() ? h($_SESSION['customer_name']) : '' ?>"></div>
        <div class="mb-2"><label class="form-label" for="o_email">Email (your e-book is sent here)</label><input class="form-control" id="o_email" name="email" type="email" required maxlength="150" autocomplete="email" value="<?= is_customer() ? h($_SESSION['customer_email']) : '' ?>"></div>
        <div class="mb-3"><label class="form-label" for="o_phone">Phone or WhatsApp</label><input class="form-control" id="o_phone" name="phone" type="tel" required maxlength="30" autocomplete="tel" value="<?= is_customer() ? h($_SESSION['customer_phone'] ?? '') : '' ?>"></div>
        <button class="btn btn-hmi-red w-100" type="submit">Send order request</button>
        <button class="btn btn-link w-100 mt-1" type="button" id="cartClear">Empty cart</button>
      </form>
    </div>
  </div>
</div>

<?php if ($flash && empty($suppress_flash_display)): ?>
<div class="container flash-wrap">
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert" id="flash">
    <?= h($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
<?php endif; ?>

<main id="main">