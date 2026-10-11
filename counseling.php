<?php
$page_key   = 'counseling';
$page_title = 'Marriage Counseling';
$page_desc  = 'Confidential, faith-based marriage and family counseling in Rivers State, Nigeria. Personalized sessions, experienced counselors and biblical guidance.';
$page_path  = 'counseling.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Faith-based marriage counseling';
$hero_sub   = 'Personal, confidential sessions that bring biblical wisdom together with practical help for your relationship.';
require __DIR__ . '/includes/page-hero.php';

$f = $flash ?? null;
$err = $f['errors'] ?? [];
function fe(array $err, string $k): string { return isset($err[$k]) ? '<div class="invalid-feedback d-block">' . h($err[$k]) . '</div>' : ''; }
?>

<section class="section pt-0" aria-labelledby="expectTitle">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5 reveal">
        <p class="eyebrow">What to expect</p>
        <h2 id="expectTitle">Help that starts with listening</h2>
        <p class="lead">Marriage is a journey with beautiful moments and hard ones. You do not need to have the right words to begin.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Personalized counseling.</strong> Sessions shaped around your own relationship and challenges.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Experienced counselors.</strong> A team dedicated to your relationship's success.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Proven methods.</strong> Effective techniques that foster understanding and connection.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Rooted in Scripture.</strong> Biblical teaching applied to everyday decisions.</span></li>
        </ul>
        <div class="steps mt-5" style="grid-template-columns:1fr">
          <div class="step"><div class="chip"><i class="bi bi-send-fill" aria-hidden="true"></i></div><h3 class="h5">1. You send a request</h3><p>Use the form. It takes about two minutes.</p></div>
          <div class="step"><div class="chip"><i class="bi bi-telephone-inbound-fill" aria-hidden="true"></i></div><h3 class="h5">2. We reach out</h3><p>A member of the team contacts you to agree a suitable time.</p></div>
          <div class="step"><div class="chip"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i></div><h3 class="h5">3. We begin together</h3><p>Your first session is a safe space to be heard.</p></div>
        </div>
      </div>

      <div class="col-lg-7 reveal" id="request">
        <div class="form-card">
          <h2 class="h3">Book for counseling</h2>
          <p class="form-privacy">Share only what you are comfortable with. We use this information to respond to you and for nothing else.</p>
          <form action="<?= h(url('actions/counseling.php')) ?>" method="post" novalidate>
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="c_name">Your full name <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['name']) ? ' is-invalid' : '' ?>" id="c_name" name="name" type="text" required maxlength="100" autocomplete="name" value="<?= old($f, 'name') ?>">
                <?= fe($err, 'name') ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_partner">Spouse or partner's name</label>
                <input class="form-control" id="c_partner" name="partner" type="text" maxlength="100" value="<?= old($f, 'partner') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_email">Email <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['email']) ? ' is-invalid' : '' ?>" id="c_email" name="email" type="email" required maxlength="150" autocomplete="email" value="<?= old($f, 'email') ?>">
                <?= fe($err, 'email') ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_phone">Phone or WhatsApp <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['phone']) ? ' is-invalid' : '' ?>" id="c_phone" name="phone" type="tel" required maxlength="30" autocomplete="tel" value="<?= old($f, 'phone') ?>">
                <?= fe($err, 'phone') ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_status">Your situation</label>
                <select class="form-select" id="c_status" name="status">
                  <?php foreach (['Married', 'Engaged or preparing for marriage', 'Separated', 'Seeking guidance for someone else', 'Minister or leader', 'Other'] as $o): ?>
                    <option<?= ($f['old']['status'] ?? '') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_topic">Main area of concern</label>
                <select class="form-select" id="c_topic" name="topic">
                  <?php foreach (['Communication', 'Trust and forgiveness', 'Finances', 'In-laws and extended family', 'Parenting', 'Intimacy', 'Spiritual growth together', 'Pre-marital preparation', 'Not sure yet'] as $o): ?>
                    <option<?= ($f['old']['topic'] ?? '') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="c_mode">How would you like to meet?</label>
                <select class="form-select" id="c_mode" name="mode">
                  <?php foreach (['In person', 'Phone call', 'Video call', 'No preference'] as $o): ?>
                    <option<?= ($f['old']['mode'] ?? '') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="c_msg">Briefly, what would you like help with?</label>
                <textarea class="form-control" id="c_msg" name="message" rows="5" maxlength="1500"><?= old($f, 'message') ?></textarea>
                <div class="form-text">Optional. You can share more when we speak.</div>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input<?= isset($err['consent']) ? ' is-invalid' : '' ?>" type="checkbox" id="c_consent" name="consent" value="1" required>
                  <label class="form-check-label" for="c_consent">I agree that Healing My-Marriage International may contact me about this request. See the <a href="<?= h(url('privacy.php')) ?>">privacy policy</a>.</label>
                  <?= fe($err, 'consent') ?>
                </div>
              </div>
              <div class="col-12">
                <button class="btn btn-hmi-red btn-lg" type="submit">Send my request</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="faqTitle">
  <div class="container" style="max-width:860px">
    <div class="section-head center reveal">
      <p class="eyebrow">Questions</p>
      <h2 id="faqTitle">Before you reach out</h2>
    </div>
    <div class="accordion reveal" id="faq">
      <?php
      $faqs = [
        ['Is what I share kept private?', 'Counseling requests and conversations are treated as confidential. We only share details when you give permission or where safety requires it.'],
        ['Do we both need to attend?', 'Many couples come together, but you are welcome to start on your own. A counselor can advise on the best way forward once you describe your situation.'],
        ['What if my marriage is in serious trouble?', 'Please reach out. Counselors work with couples facing a wide range of difficulty. If you or anyone in your home is in danger, contact local emergency services first.'],
        ['Is the counseling Christian?', 'Yes. Our counseling is faith-based and integrates biblical teaching with practical advice. You are welcome to ask questions about our approach before you begin.'],
        ['What does it cost?', 'Please call the office on ' . SITE['phones'][0] . ' to discuss. The ministry will confirm fees or free-will arrangements.'],
      ];
      foreach ($faqs as $i => $q): ?>
        <div class="accordion-item">
          <h3 class="accordion-header"><button class="accordion-button<?= $i ? ' collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>" aria-expanded="<?= $i ? 'false' : 'true' ?>" aria-controls="faq<?= $i ?>"><?= h($q[0]) ?></button></h3>
          <div id="faq<?= $i ?>" class="accordion-collapse collapse<?= $i ? '' : ' show' ?>" data-bs-parent="#faq"><div class="accordion-body"><?= h($q[1]) ?></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
