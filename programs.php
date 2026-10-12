<?php
$page_key   = 'programs';
$page_title = 'Programs';
$page_desc  = 'Marriage enrichment, family support, the Supporting Youths Initiative and leadership training for ministers from Healing My-Marriage International in Rivers State, Nigeria.';
$page_path  = 'programs.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Programs for Marriages, Families and Leaders';
$hero_sub   = 'Four ways the ministry serves, each one rooted in faith and shaped for real life.';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">Marriage Enrichment</p>
        <h2>Strengthen your bond through workshops and resources</h2>
        <p class="lead">Focused on communication, trust and faith. For couples at every stage of their journey together.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Communication and conflict skills</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Trust, forgiveness and rebuilding</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Praying and growing together</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Teaching resources you can revisit at home</span></li>
        </ul>
        <a class="btn btn-hmi-green mt-3" href="<?= h(url('contact.php')) ?>">Ask about the next workshop</a>
      </div>
      <div class="col-lg-6 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/marriage-counselling.jpg')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/marriage-counselling.jpg')) ?>" alt="Marriage Enrichment Workshop" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php else: ?>
        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 300px;"><span class="text-muted">Marriage Enrichment</span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section-white">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 order-lg-2 reveal">
        <p class="eyebrow">Family Support</p>
        <h2>Find comfort and guidance for the entire family</h2>
        <p class="lead">Programs tailored for the whole family, fostering unity and spiritual growth.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Guidance for parents and children</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Unity and spiritual growth at home</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Support in seasons of difficulty</span></li>
        </ul>
        <a class="btn btn-hmi-green mt-3" href="<?= h(url('counseling.php#request')) ?>">Talk to us about your family</a>
      </div>
      <div class="col-lg-6 order-lg-1 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/famlead.jpg')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/famlead.jpg')) ?>" alt="Family Support" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">Supporting Youths Initiative</p>
        <h2>Building tomorrow's leaders</h2>
        <p class="lead">We invest in young people so the next generation starts with strong foundations.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Mentoring and encouragement</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Preparation for relationships and marriage</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Leadership with character</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Youth programs fostering growth, learning, and deep connection to faith</span></li>
        </ul>
        <a class="btn btn-hmi-green mt-3" href="<?= h(url('contact.php')) ?>">Partner or enquire</a>
      </div>
      <div class="col-lg-6 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/jhj.jpg')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/jhj.jpg')) ?>" alt="Youth Programs" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section-white">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 order-lg-2 reveal">
        <p class="eyebrow">Leadership for Ministers</p>
        <h2>Marriage and family leadership for ministers</h2>
        <p class="lead">Leadership with love and biblical wisdom, equipping pastors and ministers to serve couples and families well.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Counseling skills for ministry</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Caring for couples in your congregation</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Sustaining your own marriage in ministry</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Training of Family Circle Teachers, Marriage Committees</span></li>
        </ul>
        <a class="btn btn-hmi-green mt-3" href="<?= h(url('contact.php')) ?>">Enquire for your church</a>
      </div>
      <div class="col-lg-6 order-lg-1 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/heart.webp')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/heart.webp')) ?>" alt="Ministers Leadership" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>