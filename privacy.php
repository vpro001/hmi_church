<?php
$page_key   = 'privacy';
$page_title = 'Privacy Policy and Terms';
$page_desc  = 'How Healing My-Marriage International handles the information you send through this website.';
$page_path  = 'privacy.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Privacy policy and terms';
$hero_crumb = 'Privacy';
$hero_sub   = 'Plain-language notes on how we handle what you share with us.';
require __DIR__ . '/includes/page-hero.php';
?>
<section class="section pt-0">
  <div class="container" style="max-width:820px">
    <div class="prose">
      <p><strong>Draft for the ministry's review.</strong> Have a qualified adviser confirm this text before launch.</p>
      <h2>Information we collect</h2>
      <p>When you use the counseling request form, contact form or newsletter sign-up, we receive the details you type in, such as your name, email address, phone number and message.</p>
      <h2>How we use it</h2>
      <p>We use your details only to respond to you, to arrange counseling or events, and to send the newsletter if you subscribed. We do not sell your information.</p>
      <h2>Storage and access</h2>
      <p>Submissions are stored securely on the ministry's web server and are available only to authorized ministry staff. Counseling requests are treated as confidential.</p>
      <h2>Analytics and cookies</h2>
      <p>The site uses a session cookie to protect forms from misuse. If analytics is switched on, anonymous usage statistics are collected to help us improve the site.</p>
      <h2>Your choices</h2>
      <p>You can ask us to correct or delete your details, or to remove you from the newsletter, by writing to <a href="mailto:<?= h(SITE['email']) ?>"><?= h(SITE['email']) ?></a>.</p>
      <h2 id="terms">Terms of use</h2>
      <p>Content on this website is provided for encouragement and general guidance. It does not replace medical, legal or emergency help. If you or someone in your home is in danger, contact local emergency services immediately.</p>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
