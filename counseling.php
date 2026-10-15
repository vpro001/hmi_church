<?php
$page_key   = 'counseling';
$page_title = 'Marriage Counseling';
$page_desc  = 'Confidential, faith-based marriage and family counseling in Rivers State, Nigeria. Personalized sessions, experienced counselors and biblical guidance.';
$page_path  = 'counseling.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Faith-based Marriage Counseling';
$hero_sub   = 'Personal, confidential sessions that bring biblical wisdom together with practical help for your relationship.';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-5" aria-labelledby="expectTitle">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">How Our Counseling Services Work</p>
        <h2 id="expectTitle">A process that starts with listening</h2>
        <p class="lead">We are a religious organization and espouse the entire tenets of the Christian faith. The Bible has the final say.</p>
        <div class="steps mt-4" style="grid-template-columns:1fr">
          <div class="step"><div class="chip"><i class="bi bi-send-fill" aria-hidden="true"></i></div><h3 class="h5">1. Initial Consultation</h3><p>Discuss your situation and goals with a counselor to create a tailored plan.</p></div>
          <div class="step"><div class="chip"><i class="bi bi-calendar-check-fill" aria-hidden="true"></i></div><h3 class="h5">2. Custom Sessions</h3><p>Engage in regular sessions designed to address your specific needs.</p></div>
          <div class="step"><div class="chip"><i class="bi bi-heart-pulse-fill" aria-hidden="true"></i></div><h3 class="h5">3. Follow-up/Support</h3><p>Receive ongoing support and resources to maintain your relationship's growth.</p></div>
        </div>
        
        <div class="mt-5 p-4 bg-light rounded">
          <h5>Our Approach</h5>
          <ul class="mb-0 small">
            <li>We counsel and proffer suggestive course of actions based on what the counselee tells us, the condition in which they came with, by our professional experience and the leading of the Holy Spirit.</li>
            <li>We are in no capacity to coerce information and cannot be held by Law privy to secrets, materials/properties or classified information.</li>
            <li>Engagement may be recorded electronically for HMI databank.</li>
            <li>Your privacy is protected as information received from you is deemed privileged and cannot be made public. Except where it poses proven danger to the public.</li>
            <li>For this service a fee may be charged and you are by our rule to purchase some of our recommended books and other teaching materials to help your situation.</li>
            <li>You received counsel to the best of our knowledge and confidence. You are by our rule prohibited to broadcast the contents of our counsels.</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-6 reveal" id="request">
        <div class="form-card">
          <h2 class="h3">Book a Session With Us</h2>
          <p class="form-privacy">Share only what you are comfortable with. We use this information to respond to you and for nothing else.</p>
          <p class="eyebrow">Consultation Fee</p>
          <p class="mb-3">This Appointment/Consultation Form fee is <strong>₦12,500</strong>, payable in favour of Healing My-Marriage Int'l <br>Account: 2305613200, United Bank for Africa (UBA).<br>This payment completes your registration before appointment will be secured.</p>
          <?php $f = $flash ?? null; $err = $f['errors'] ?? []; ?>
          <form action="<?= h(url('actions/counseling.php')) ?>" method="post" novalidate>
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="c_name">Your full name <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['name']) ? ' is-invalid' : '' ?>" id="c_name" name="name" type="text" required maxlength="100" autocomplete="name" value="<?= old($f, 'name') ?>">
                <?php if (isset($err['name'])): ?><div class="invalid-feedback d-block"><?= h($err['name']) ?></div><?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_partner">Spouse or partner's name</label>
                <input class="form-control" id="c_partner" name="partner" type="text" maxlength="100" value="<?= old($f, 'partner') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_email">Email <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['email']) ? ' is-invalid' : '' ?>" id="c_email" name="email" type="email" required maxlength="150" autocomplete="email" value="<?= old($f, 'email') ?>">
                <?php if (isset($err['email'])): ?><div class="invalid-feedback d-block"><?= h($err['email']) ?></div><?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_phone">Phone or WhatsApp <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($err['phone']) ? ' is-invalid' : '' ?>" id="c_phone" name="phone" type="tel" required maxlength="30" autocomplete="tel" value="<?= old($f, 'phone') ?>">
                <?php if (isset($err['phone'])): ?><div class="invalid-feedback d-block"><?= h($err['phone']) ?></div><?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_status">Your situation</label>
                <select class="form-select" id="c_status" name="status">
                  <option value="">Select...</option>
                  <?php foreach (['Married','Engaged or preparing for marriage','Separated','Seeking guidance for someone else','Minister or leader','Other'] as $o): ?>
                    <option<?= old($f, 'status') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="c_topic">Main area of concern</label>
                <select class="form-select" id="c_topic" name="topic">
                  <option value="">Select...</option>
                  <?php foreach (['Communication','Trust and forgiveness','Finances','In-laws and extended family','Parenting','Intimacy','Spiritual growth together','Pre-marital preparation','Not sure yet'] as $o): ?>
                    <option<?= old($f, 'topic') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="c_mode">How would you like to meet?</label>
                <select class="form-select" id="c_mode" name="mode">
                  <option value="">Select...</option>
                  <?php foreach (['In person','Phone call','Video call','No preference'] as $o): ?>
                    <option<?= old($f, 'mode') === $o ? ' selected' : '' ?>><?= h($o) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="c_msg">Briefly, what would you like help with?</label>
                <textarea class="form-control" id="c_msg" name="message" rows="5" maxlength="1500" placeholder="Describe your situation..."><?= old($f, 'message') ?></textarea>
                <div class="form-text">Optional. You can share more when we speak.</div>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input<?= isset($err['consent']) ? ' is-invalid' : '' ?>" type="checkbox" id="c_consent" name="consent" value="1" required>
                  <label class="form-check-label" for="c_consent">I agree that Healing My-Marriage International may contact me about this request. See the <a href="<?= h(url('privacy.php')) ?>">privacy policy</a>.</label>
                  <?php if (isset($err['consent'])): ?><div class="invalid-feedback d-block"><?= h($err['consent']) ?></div><?php endif; ?>
                </div>
              </div>
              <div class="col-12">
                <button class="btn btn-hmi-red btn-lg w-100" type="submit">Send my request</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="counselorsTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">The Counsellors</p>
      <h2 id="counselorsTitle">There's safety when you're surrounded by many counselors</h2>
      <p class="lead">A counselor is one called, trained and ordained to be a conduit and mouthpiece in a specific field.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 reveal">
        <div class="hmi-card h-100"><div class="body">
          <h3 class="h5">Our Team</h3>
          <p class="mb-2">We have a team of counselors, drawn from all walks of life with practical experience. Their integrity, moral and dependableness is vouched for.</p>
          <p class="mb-0">We apply the praxis of counseling and teaching eclectically, so as to deliver and guide our client to their desired destination in realtime. We are not judgmental, gender biased, sectarian, or socially prejudiced. We are empathic and conscientious and never back off until the desired result is achieved.</p>
        </div></div>
      </div>
      <div class="col-md-6 reveal">
        <div class="hmi-card h-100"><div class="body">
          <h3 class="h5">Confidentiality</h3>
          <p class="mb-0">We handle your matter confidential and vertically. God sent his word and they get healed!</p>
        </div></div>
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
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq0" aria-expanded="true" aria-controls="faq0">Is what I share kept private?</button></h3>
        <div id="faq0" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body">Counseling requests and conversations are treated as confidential. We only share details when you give permission or where safety requires it.</div></div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">Do we both need to attend?</button></h3>
        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body">Many couples come together, but you are welcome to start on your own. A counselor can advise on the best way forward once you describe your situation.</div></div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">What if my marriage is in serious trouble?</button></h3>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body">Please reach out. Counselors work with couples facing a wide range of difficulty. If you or anyone in your home is in danger, contact local emergency services first.</div></div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">Is the counseling Christian?</button></h3>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body">Yes. Our counseling is faith-based and integrates biblical teaching with practical advice. You are welcome to ask questions about our approach before you begin.</div></div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">What does it cost?</button></h3>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body">The consultation form fee is ₦12,500. Please call the office on +234 704 222 6633 to discuss. The ministry will confirm fees or free-will arrangements.</div></div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>