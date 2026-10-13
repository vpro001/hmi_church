<?php
/**
 * Admin panel - Login page
 */

require_once __DIR__ . '/../includes/auth.php';

if (admin_check()) {
    redirect('admin/index.php');
}

$flash = take_flash();
$page_title = 'Admin Login';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0f261b">
<title><?= h($page_title) ?> | Healing My-Marriage Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>" rel="stylesheet">
<style>
  :root {
    --forest-950:#0f261b; --forest-900:#143024; --forest-800:#1b3f2e; --forest-700:#1f4733;
    --gold:#c9922f; --gold-lt:#e8c56d; --gold-100:#f6ecd4;
    --cream:#faf6ea; --cream-200:#f3ead0;
    --ink:#243027; --muted:#54635a; --red:#d8352f; --red-700:#b32a25;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh; font-family: 'Source Sans 3', sans-serif; color: var(--ink);
    background: var(--forest-950);
    display: grid; grid-template-columns: 1fr;
  }
  a { text-underline-offset: 3px; }
  :focus-visible { outline: 3px solid var(--gold); outline-offset: 3px; border-radius: 6px; }

  /* Visual side */
  .av { position: relative; overflow: hidden; display: flex; padding: 2.25rem 1.5rem; min-height: 240px; color: #e4efe9; }
  .av img.bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center 30%; filter: saturate(.65); z-index: 0; }
  .av .scrim { position: absolute; inset: 0; z-index: 1; background: linear-gradient(115deg, rgba(15,38,27,.97) 0%, rgba(15,38,27,.9) 46%, rgba(15,38,27,.66) 100%); }
  .av::after { content:""; position:absolute; inset:0; z-index:2; pointer-events:none; background-image: repeating-linear-gradient(90deg, rgba(255,255,255,.05) 0 16px, transparent 16px 40px); -webkit-mask-image: linear-gradient(90deg, transparent 30%, #000 100%); mask-image: linear-gradient(90deg, transparent 30%, #000 100%); }
  .av .glow { position:absolute; top:-20%; right:-10%; width:520px; height:520px; z-index:1; background: radial-gradient(circle, rgba(201,146,47,.28) 0%, transparent 62%); pointer-events:none; }
  .av-inner { position: relative; z-index: 3; display: flex; flex-direction: column; gap: 1.75rem; width: 100%; max-width: 480px; margin-inline: auto; }
  .brand { align-self: flex-start; background:#fff; border-radius: 14px; padding: .5rem .85rem; display:inline-block; line-height:0; box-shadow: 0 8px 24px rgba(0,0,0,.28); }
  .brand img { height: 50px; width:auto; display:block; }
  .av-copy { margin-block: auto; }
  .eyebrow { display:inline-flex; align-items:center; gap:.85rem; font-weight:600; font-size:.78rem; letter-spacing:.2em; text-transform:uppercase; color: var(--gold-lt); margin-bottom: 1.1rem; }
  .eyebrow::before { content:""; width:44px; height:4px; background: currentColor; border-radius:2px; flex:none; }
  .av-copy h2 { font-family:'Fraunces',Georgia,serif; font-weight:600; color:#fff; font-size: clamp(1.7rem, 2.8vw, 2.4rem); line-height:1.12; margin:0 0 1rem; }
  .av-copy h2 em { font-style:italic; color: var(--gold-lt); font-weight:500; }
  .av-copy p { color:#cfe3d9; font-size:1.06rem; line-height:1.7; margin:0; }
  .av-trust { list-style:none; padding:0; margin:auto 0 0; display:grid; gap:.65rem; }
  .av-trust li { display:flex; gap:.65rem; align-items:center; color:#a9c4b6; font-size:.92rem; }
  .av-trust i { color: var(--gold-lt); }

  /* Form side */
  .ap { display:flex; align-items:center; padding: clamp(2.5rem,6vw,4rem) 1.5rem; background: var(--cream); }
  .ap-inner { width:100%; max-width: 420px; margin-inline:auto; }
  .ap-inner .eyebrow { color: var(--gold); }
  .ap-inner h1 { font-family:'Fraunces',Georgia,serif; font-weight:600; color: var(--forest-950); font-size: clamp(1.9rem,3.4vw,2.4rem); margin:0 0 .5rem; }
  .ap-sub { color: var(--muted); margin: 0 0 2rem; }

  /* Honeypot spam-trap (output by csrf_field) — keep hidden from real users */
  .hp-field { position: absolute; left: -9999px; height: 0; overflow: hidden; }

  .form-label { font-weight:600; font-size:.93rem; color: var(--forest-800); margin-bottom:.4rem; }
  .form-control { border:1.5px solid var(--cream-200); border-radius:12px; padding:.8rem 1rem; background:#fff; }
  .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 .22rem rgba(201,146,47,.16); background:#fff; }
  .btn-signin { width:100%; margin-top:1.25rem; border:0; border-radius:999px; background: var(--forest-900); color:#fff; font-weight:600; font-size:1.02rem; padding:.95rem; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; cursor:pointer; transition: background .18s ease, transform .18s ease, box-shadow .18s ease; box-shadow: 0 10px 26px rgba(15,38,27,.24); }
  .btn-signin:hover { background: var(--forest-700); transform: translateY(-2px); }
  .btn-signin:active { transform: none; }

  .alert { border:0; border-radius:14px; padding:.9rem 1.1rem; margin-bottom:1.5rem; display:flex; gap:.6rem; align-items:flex-start; }
  .alert-danger { background:#fbe0de; color:#8f2420; }
  .alert-success { background:#dcf3e6; color:#17784a; }
  .alert .btn-close { filter: grayscale(1) opacity(.5); }

  .back { display:block; text-align:center; margin-top:1.75rem; padding-top:1.5rem; border-top:1px solid var(--cream-200); }
  .back a { color: var(--muted); font-weight:600; text-decoration:none; font-size:.95rem; }
  .back a:hover { color: var(--forest-700); text-decoration:underline; }

  @media (min-width: 992px) {
    body { grid-template-columns: 1fr 1fr; }
    .av { padding: 3rem; }
    .ap { padding: 3rem; }
  }
</style>
</head>
<body>
  <div class="av">
    <img class="bg" src="<?= h(asset('img/live/prayer.jpg')) ?>" alt="" aria-hidden="true">
    <div class="scrim" aria-hidden="true"></div>
    <div class="glow" aria-hidden="true"></div>
    <div class="av-inner">
      <a class="brand" href="<?= h(url('index.php')) ?>" aria-label="Healing My-Marriage International home">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International">
      </a>
      <div class="av-copy">
        <p class="eyebrow">Admin access</p>
        <h2>Care for the ministry, <em>page by page.</em></h2>
        <p>Manage teaching, events, orders and the families who reach out &mdash; all in one place.</p>
      </div>
      <ul class="av-trust">
        <li><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Staff access only</li>
        <li><i class="bi bi-lock-fill" aria-hidden="true"></i> Sessions are secured</li>
      </ul>
    </div>
  </div>

  <div class="ap">
    <div class="ap-inner">
      <p class="eyebrow">Sign in</p>
      <h1>Admin Panel</h1>
      <p class="ap-sub">Sign in to manage the ministry website.</p>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>" aria-hidden="true" style="margin-top:.15rem"></i>
        <span><?= h($flash['message']) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <form method="post" action="<?= h(url('admin/actions/login.php')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="username">Username or Email</label>
          <input class="form-control" type="text" id="username" name="username" required autocomplete="username" value="<?= old($flash, 'username') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button class="btn-signin" type="submit"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Sign In</button>
      </form>

      <div class="back">
        <a href="<?= h(url('index.php')) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to website</a>
      </div>
    </div>
  </div>
<script src="<?= h(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
