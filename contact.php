<?php
$page_key   = 'contact';
$page_title = 'Contact Us';
$page_desc  = 'Contact Healing My-Marriage International in Rivers State, Nigeria. Send a message, a prayer request, or call the office.';
$page_path  = 'contact.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Contact us';
$hero_sub   = 'Send a message, share a prayer request, or call the office. We would love to hear from you.';
require __DIR__ . '/includes/page-hero.php';

$f   = $flash ?? null;
$err = $f['errors'] ?? [];
$subject_pref = clean_line($_GET['subject'] ?? '', 120);
$maps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(SITE['maps_query']);
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5 reveal">
        <h2 class="h3 mb-4">How to reach us</h2>
        <ul class="contact-list">
          <li><div class="icon-chip"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></div><div><strong>Visit</strong><?= h(SITE['address']) ?></div></li>
          <li><div class="icon-chip green"><i class="bi bi-telephone-fill" aria-hidden="true"></i></div><div><strong>Call or WhatsApp</strong>
            <?php foreach (SITE['phones_raw'] as $i => $raw): ?><a href="<?= h(tel_link($raw)) ?>"><?= h(SITE['phones'][$i]) ?></a><br><?php endforeach; ?></div></li>
          <li><div class="icon-chip red"><i class="bi bi-envelope-fill" aria-hidden="true"></i></div><div><strong>Email</strong><a href="mailto:<?= h(SITE['email']) ?>"><?= h(SITE['email']) ?></a></div></li>
          <li><div class="icon-chip stone"><i class="bi bi-clock-fill" aria-hidden="true"></i></div><div><strong>Office hours</strong><?= h(SITE['hours']) ?></div></li>
        </ul>
        <a class="map-link mt-4" href="<?= h($maps) ?>" target="_blank" rel="noopener">
          <div><i class="bi bi-pin-map-fill" aria-hidden="true"></i><p class="h5 mt-2 mb-1 text-white">Open in Google Maps</p><p class="mb-0" style="color:#cfe3d9"><?= h(SITE['address']) ?></p></div>
        </a>
      </div>

      <div class="col-lg-7 reveal" id="message">
        <div class="form-card">
          <h2 class="h3">Send a message</h2>
          <p class="form-privacy">Use this form for questions, event registration or prayer requests. For marriage counseling, please use the <a href="<?= h(url('counseling.php#request')) ?>">counseling request form</a>.</p>
          <form action="<?= h(url('actions/contact.php')) ?>" method="post" novalidate>
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="m_name">Your name <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['name']) ? ' is-invalid' : '' ?>" id="m_name" name="name" type="text" required maxlength="100" autocomplete="name" value="<?= old($f, 'name') ?>">
                <?php if (isset($err['name'])): ?><div class="invalid-feedback d-block"><?= h($err['name']) ?></div><?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="m_email">Email <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['email']) ? ' is-invalid' : '' ?>" id="m_email" name="email" type="email" required maxlength="150" autocomplete="email" value="<?= old($f, 'email') ?>">
                <?php if (isset($err['email'])): ?><div class="invalid-feedback d-block"><?= h($err['email']) ?></div><?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="m_phone">Phone</label>
                <input class="form-control" id="m_phone" name="phone" type="tel" maxlength="30" autocomplete="tel" value="<?= old($f, 'phone') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="m_type">This is a</label>
                <select class="form-select" id="m_type" name="type">
                  <?php foreach (['General question', 'Prayer request', 'Event registration', 'Partnership or invitation', 'Books and resources'] as $o): ?>
                    <option<?= (($f['old']['type'] ?? '') === $o || ($subject_pref !== '' && strpos($subject_pref, 'Registration') === 0 && $o === 'Event registration')) ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="m_subject">Subject</label>
                <input class="form-control" id="m_subject" name="subject" type="text" maxlength="120" value="<?= old($f, 'subject', $subject_pref) ?>">
              </div>
              <div class="col-12">
                <label class="form-label" for="m_msg">Message <span class="text-danger">*</span></label>
                <textarea class="form-control<?= isset($err['message']) ? ' is-invalid' : '' ?>" id="m_msg" name="message" rows="6" required maxlength="2000"><?= old($f, 'message') ?></textarea>
                <?php if (isset($err['message'])): ?><div class="invalid-feedback d-block"><?= h($err['message']) ?></div><?php endif; ?>
              </div>
              <div class="col-12">
                <button class="btn btn-hmi-red btn-lg" type="submit">Send message</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
