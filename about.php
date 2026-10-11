<?php
$page_key   = 'about';
$page_title = 'About Us';
$page_desc  = 'Healing My-Marriage International Ministry is a marriage and family hub in Rivers State, Nigeria, building nations out of marriages through biblical counseling and teaching.';
$page_path  = 'about.php';

/* ---- Content the ministry must supply. Empty values keep the section hidden. ---- */
$vision   = '';   // e.g. "Our vision is ..."
$mission  = '';
$faith    = [];   // list of statements of faith, e.g. ['We believe in ...']
$leaders  = [];   // each: ['name' => '', 'role' => '', 'bio' => '']
$history  = [];   // each: ['year' => '', 'text' => '']

require __DIR__ . '/includes/header.php';
$hero_title = 'About Healing My-Marriage';
$hero_sub   = 'A ministry for marriage and family matters, built on the belief that strong marriages build strong nations.';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">Our story</p>
        <h2>Why this ministry exists</h2>
        <div class="prose">
          <p>HMI is a marriage and family matters solutions hub. We are building nations out of marriages.</p>
          <p>The heart behind the work comes from pain that was lived, not studied. Cracked marriages, broken homes and unhappy couples with children in disarray leave wounds that can last a lifetime, and the same story can repeat in the next generation if no one steps in to help.</p>
          <p>Healing My-Marriage International Ministry, Inc. exists to step in, with truth, tenderness and practical help.</p>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <div class="story">
          <i class="bi bi-heart-fill heart-mark" aria-hidden="true"></i>
          <blockquote class="mt-3">Marriage is a journey filled with both beautiful moments and challenging times. We walk it with you.</blockquote>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="commitTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our commitment</p>
      <h2 id="commitTitle">Nurturing faith-centered marriages and families</h2>
      <p class="lead">The ministry offers programs designed to support, educate and guide couples on their journey together.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
        <h3>Marriage enrichment</h3><p>Workshops and resources focused on communication, trust and faith.</p>
      </div></article></div>
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip green"><i class="bi bi-house-heart-fill" aria-hidden="true"></i></div>
        <h3>Family support</h3><p>Comfort and guidance for the entire family, fostering unity and spiritual growth.</p>
      </div></article></div>
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip red"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i></div>
        <h3>Faith-based counseling</h3><p>Spiritual teaching joined with practical advice to help you navigate challenges.</p>
      </div></article></div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="valuesTitle">
  <div class="container">
    <div class="section-head reveal">
      <p class="eyebrow">What guides our work</p>
      <h2 id="valuesTitle">Four convictions behind everything we do</h2>
    </div>
    <div class="row g-4">
      <?php
      $values = [
        ['bi-book-fill',    'Biblical wisdom',     'Scripture is the foundation for every session, teaching and program.'],
        ['bi-heart-fill',   'Love in leadership',  'We lead with compassion, patience and respect for every person.'],
        ['bi-shield-check', 'Confidentiality',     'People share their hardest struggles with us. We guard that trust.'],
        ['bi-globe-europe-africa', 'Generational impact', 'Healing one marriage changes the story for children and grandchildren.'],
      ];
      foreach ($values as $v): ?>
        <div class="col-sm-6 col-lg-3 reveal">
          <div class="hmi-card"><div class="body">
            <div class="icon-chip stone"><i class="bi <?= h($v[0]) ?>" aria-hidden="true"></i></div>
            <h3 class="h5"><?= h($v[1]) ?></h3><p class="mb-0"><?= h($v[2]) ?></p>
          </div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($vision || $mission): ?>
<section class="section section-white"><div class="container"><div class="row g-4">
  <?php if ($vision): ?><div class="col-md-6"><h2 class="h3">Our vision</h2><p><?= h($vision) ?></p></div><?php endif; ?>
  <?php if ($mission): ?><div class="col-md-6"><h2 class="h3">Our mission</h2><p><?= h($mission) ?></p></div><?php endif; ?>
</div></div></section>
<?php endif; ?>

<?php if ($faith): ?>
<section class="section"><div class="container"><h2>What we believe</h2><ul class="checklist mt-4">
  <?php foreach ($faith as $f): ?><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><?= h($f) ?></span></li><?php endforeach; ?>
</ul></div></section>
<?php endif; ?>

<?php if ($leaders): ?>
<section class="section section-white"><div class="container"><h2 class="mb-4">Leadership</h2><div class="row g-4">
  <?php foreach ($leaders as $l): ?>
    <div class="col-md-6 col-lg-4"><div class="hmi-card"><div class="body"><h3><?= h($l['name']) ?></h3><p class="eyebrow"><?= h($l['role']) ?></p><p><?= h($l['bio']) ?></p></div></div></div>
  <?php endforeach; ?>
</div></div></section>
<?php endif; ?>

<section class="section section-forest text-center">
  <div class="container">
    <h2>Come as you are. Leave with hope.</h2>
    <p class="mx-auto lead" style="color:#cfe3d9">Whether your marriage needs a repair or a refresh, there is a place for you here.</p>
    <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
      <a class="btn btn-hmi-red btn-lg" href="<?= h(url('counseling.php#request')) ?>">Book for counseling</a>
      <a class="btn btn-hmi-ghost btn-lg" href="<?= h(url('contact.php')) ?>">Contact us</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
