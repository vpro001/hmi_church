<?php
$page_key   = 'programs';
$page_title = 'Programs';
$page_desc  = 'Marriage enrichment, family support, the Supporting Youths Initiative and leadership training for ministers from Healing My-Marriage International.';
$page_path  = 'programs.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Programs for marriages, families and leaders';
$hero_sub   = 'Four ways the ministry serves, each one rooted in faith and shaped for real life.';
require __DIR__ . '/includes/page-hero.php';

$programs = [
  ['id' => 'enrichment', 'icon' => 'bi-people-fill', 'chip' => '', 'title' => 'Marriage enrichment',
   'lead' => 'Strengthen your bond through workshops and resources focused on communication, trust and faith.',
   'points' => ['Communication and conflict skills', 'Trust, forgiveness and rebuilding', 'Praying and growing together', 'Teaching resources you can revisit at home'],
   'cta' => ['Ask about the next workshop', 'contact.php']],
  ['id' => 'family', 'icon' => 'bi-house-heart-fill', 'chip' => 'green', 'title' => 'Family support',
   'lead' => 'Find comfort and guidance with programs tailored for the entire family, fostering unity and spiritual growth.',
   'points' => ['Guidance for parents and children', 'Unity and spiritual growth at home', 'Support in seasons of difficulty'],
   'cta' => ['Talk to us about your family', 'counseling.php#request']],
  ['id' => 'youth', 'icon' => 'bi-stars', 'chip' => 'red', 'title' => 'Supporting Youths Initiative',
   'lead' => 'Building tomorrow\'s leaders. We invest in young people so the next generation starts with strong foundations.',
   'points' => ['Mentoring and encouragement', 'Preparation for relationships and marriage', 'Leadership with character'],
   'cta' => ['Partner or enquire', 'contact.php']],
  ['id' => 'ministers', 'icon' => 'bi-mortarboard-fill', 'chip' => 'stone', 'title' => 'Marriage and family leadership for ministers',
   'lead' => 'Leadership with love and biblical wisdom, equipping pastors and ministers to serve couples and families well.',
   'points' => ['Counseling skills for ministry', 'Caring for couples in your congregation', 'Sustaining your own marriage in ministry'],
   'cta' => ['Enquire for your church', 'contact.php']],
];
?>

<section class="section pt-0">
  <div class="container">
    <?php foreach ($programs as $i => $p): ?>
      <article class="row g-5 align-items-center py-4 py-lg-5 reveal" id="<?= h($p['id']) ?>">
        <div class="col-lg-5 order-lg-<?= $i % 2 ? '2' : '1' ?>">
          <div class="cover <?= ['', 'v2', 'v4', 'v3'][$i] ?>" style="border-radius:24px;aspect-ratio:4/3" aria-hidden="true">
            <i class="bi <?= h($p['icon']) ?>" style="font-size:5rem"></i>
          </div>
        </div>
        <div class="col-lg-7 order-lg-<?= $i % 2 ? '1' : '2' ?>">
          <p class="eyebrow"><?= $i + 1 ?> of <?= count($programs) ?> programs</p>
          <h2><?= h($p['title']) ?></h2>
          <p class="lead"><?= h($p['lead']) ?></p>
          <ul class="checklist my-4">
            <?php foreach ($p['points'] as $pt): ?><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><?= h($pt) ?></span></li><?php endforeach; ?>
          </ul>
          <a class="btn btn-hmi-green" href="<?= h(url($p['cta'][1])) ?>"><?= h($p['cta'][0]) ?></a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
