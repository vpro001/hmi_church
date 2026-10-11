<?php
$page_key   = 'home';
$page_title = 'Home';
$page_desc  = 'Biblical marriage counseling, enrichment and family support in Rivers State, Nigeria. Reposition, refix and rekindle your marriage with Healing My-Marriage International.';
$page_path  = 'index.php';
require __DIR__ . '/includes/header.php';

$posts  = array_slice(get_posts(), 0, 3);
$events = array_slice(get_events(true), 0, 3);
?>

<!-- HERO -->
<section class="hero" aria-labelledby="heroTitle">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <p class="eyebrow eyebrow-light">Faith-based marriage and family ministry</p>
        <h1 id="heroTitle">Reposition. Refix. <em>Rekindle</em> your marriage.</h1>
        <p class="lead mt-3">We walk with couples and families through every season, with biblical counseling, practical teaching and a community that prays. Because strong marriages build strong nations.</p>
        <div class="cta-row">
          <a class="btn btn-hmi-red btn-lg" href="<?= h(url('counseling.php#request')) ?>">Get counseled</a>
          <a class="btn btn-hmi-ghost btn-lg" href="<?= h(url('about.php')) ?>">Who we are</a>
        </div>
        <p class="hero-note"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Every request is handled in confidence.</p>
      </div>
      <div class="col-lg-5 d-none d-md-block">
        <!-- Cycle illustration built from the logo's vocabulary: three arrows around a heart -->
        <svg class="cycle" viewBox="0 0 440 440" role="img" aria-label="A ring of three steps, Reposition, Refix and Rekindle, around a heart">
          <defs>
            <path id="arcA" d="M246 72.3 A150 150 0 0 1 361 271.3"/>
            <path id="arcB" d="M105.1 316.4 A150 150 0 0 0 334.9 316.4"/>
            <path id="arcC" d="M79 271.3 A150 150 0 0 1 194 72.3"/>
          </defs>
          <circle class="ring-bg" cx="220" cy="220" r="205"/>
          <path class="arc arc-a" d="M246 72.3 A150 150 0 0 1 361 271.3"/>
          <path class="arc arc-b" d="M105.1 316.4 A150 150 0 0 0 334.9 316.4"/>
          <path class="arc arc-c" d="M79 271.3 A150 150 0 0 1 194 72.3"/>
          <text><textPath href="#arcA" startOffset="50%" text-anchor="middle">REPOSITION</textPath></text>
          <text><textPath href="#arcB" startOffset="50%" text-anchor="middle" dy="5">REFIX</textPath></text>
          <text><textPath href="#arcC" startOffset="50%" text-anchor="middle">REKINDLE</textPath></text>
          <circle class="core" cx="220" cy="220" r="78"/>
          <g transform="translate(220 224) scale(6.4) translate(-8 -8)"><path class="heart" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/></g>
        </svg>
      </div>
    </div>
  </div>
  <div class="swoosh-edge" aria-hidden="true">
    <svg viewBox="0 0 1440 140" preserveAspectRatio="none">
      <path d="M0 18 C 380 150 1060 150 1440 18 V140 H0 Z" fill="#1c9eb4"/>
      <path d="M0 52 C 380 184 1060 184 1440 52 V140 H0 Z" fill="#faf6ea"/>
    </svg>
  </div>
</section>

<!-- THE THREE STEPS -->
<section class="section pt-lg-4" aria-labelledby="stepsTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">How restoration happens</p>
      <h2 id="stepsTitle">A clear path back to each other</h2>
      <p class="lead">Healing rarely happens by accident. We walk couples through three steps, each one rooted in Scripture and made practical for daily life.</p>
    </div>
    <div class="steps">
      <article class="step reveal">
        <div class="chip"><i class="bi bi-compass-fill" aria-hidden="true"></i></div>
        <h3>Reposition</h3>
        <p>Return to God's design for marriage and see your spouse, your home and your calling from the right place.</p>
      </article>
      <article class="step reveal">
        <div class="chip"><i class="bi bi-tools" aria-hidden="true"></i></div>
        <h3>Refix</h3>
        <p>Name what is broken and repair it with honest conversation, forgiveness and tools you can use on ordinary days.</p>
      </article>
      <article class="step reveal">
        <div class="chip"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>
        <h3>Rekindle</h3>
        <p>Rebuild warmth, trust and joy, so that your marriage is a living testimony to those around you.</p>
      </article>
    </div>
  </div>
</section>

<!-- WHY WE EXIST -->
<section class="section section-white" aria-labelledby="whyTitle">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">Why we do what we do</p>
        <h2 id="whyTitle">We are building nations out of marriages</h2>
        <p class="lead">HMI is a hub for marriage and family matters, serving couples, parents, young people and ministers.</p>
        <ul class="checklist mt-4">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Faith-based principles.</strong> Biblical teaching woven into practical advice.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>A personal approach.</strong> Sessions shaped around your own story, not a script.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><strong>Experienced guidance.</strong> Counselors dedicated to the success of your relationship.</span></li>
        </ul>
        <a class="btn btn-hmi-green mt-4" href="<?= h(url('about.php')) ?>">Read our story</a>
      </div>
      <div class="col-lg-6 reveal">
        <figure class="story mb-0">
          <i class="bi bi-quote heart-mark" aria-hidden="true"></i>
          <blockquote>When I see a cracked marriage, a broken home, an unhappy couple and children in disarray, my heart bleeds. I know this too well because I was involved.</blockquote>
          <figcaption class="mt-4 small text-uppercase" style="letter-spacing:.12em;color:#9fe3ef">From the heart of the ministry</figcaption>
        </figure>
      </div>
    </div>
  </div>
</section>

<!-- WHAT WE OFFER -->
<section class="section" aria-labelledby="offerTitle">
  <div class="container">
    <div class="section-head reveal">
      <p class="eyebrow">What we offer</p>
      <h2 id="offerTitle">Support for every part of the family</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-xl-4 reveal">
        <article class="hmi-card"><div class="body">
          <div class="icon-chip"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i></div>
          <h3>Faith-based counseling</h3>
          <p>Confidential sessions that bring spiritual teaching together with practical advice for the challenges you face.</p>
          <a class="link-arrow mt-auto" href="<?= h(url('counseling.php')) ?>">Learn about counseling</a>
        </div></article>
      </div>
      <div class="col-md-6 col-xl-4 reveal">
        <article class="hmi-card"><div class="body">
          <div class="icon-chip green"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
          <h3>Marriage enrichment</h3>
          <p>Workshops and resources that strengthen communication, trust and faith, for couples at every stage.</p>
          <a class="link-arrow mt-auto" href="<?= h(url('programs.php#enrichment')) ?>">See enrichment</a>
        </div></article>
      </div>
      <div class="col-md-6 col-xl-4 reveal">
        <article class="hmi-card"><div class="body">
          <div class="icon-chip red"><i class="bi bi-house-heart-fill" aria-hidden="true"></i></div>
          <h3>Family support</h3>
          <p>Programs for the whole household that build unity, comfort and spiritual growth.</p>
          <a class="link-arrow mt-auto" href="<?= h(url('programs.php#family')) ?>">See family support</a>
        </div></article>
      </div>
      <div class="col-md-6 col-xl-6 reveal">
        <article class="hmi-card"><div class="body">
          <div class="icon-chip stone"><i class="bi bi-stars" aria-hidden="true"></i></div>
          <h3>Supporting Youths Initiative</h3>
          <p>Building tomorrow's leaders by investing in young people today.</p>
          <a class="link-arrow mt-auto" href="<?= h(url('programs.php#youth')) ?>">See the initiative</a>
        </div></article>
      </div>
      <div class="col-md-6 col-xl-6 reveal">
        <article class="hmi-card"><div class="body">
          <div class="icon-chip"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></div>
          <h3>Marriage and family leadership for ministers</h3>
          <p>Leadership with love and biblical wisdom, for pastors and ministers who serve couples and families.</p>
          <a class="link-arrow mt-auto" href="<?= h(url('programs.php#ministers')) ?>">See ministers' track</a>
        </div></article>
      </div>
    </div>
  </div>
</section>

<!-- EVENTS -->
<section class="section section-white" aria-labelledby="evTitle">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 section-head mb-4 reveal" style="max-width:none">
      <div>
        <p class="eyebrow">Gather with us</p>
        <h2 id="evTitle" class="mb-0">Upcoming events</h2>
      </div>
      <a class="link-arrow" href="<?= h(url('events.php')) ?>">All events</a>
    </div>
    <?php if ($events): ?>
      <div class="d-grid gap-3">
        <?php foreach ($events as $e): ?>
          <article class="event-row reveal">
            <div class="event-date" aria-hidden="true"><span class="d"><?= h(fmt_date($e['start_datetime'], 'j')) ?></span><span class="m"><?= h(fmt_date($e['start_datetime'], 'M')) ?></span></div>
            <div>
              <h3><?= h($e['title']) ?></h3>
              <div class="meta">
                <span><i class="bi bi-calendar-event" aria-hidden="true"></i> <?= h(fmt_date($e['start_datetime'], 'l, j F Y')) ?></span>
                <span><i class="bi bi-clock" aria-hidden="true"></i> <?= h(fmt_date($e['start_datetime'], 'g:i a')) ?></span>
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= h($e['location'] ?? 'Mercyland Central / Diamond Estate, Rivers State') ?></span>
              </div>
            </div>
            <a class="btn btn-hmi-outline" href="<?= h(url('events.php#' . $e['id'])) ?>">Details</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state reveal">
        <i class="bi bi-calendar-heart" aria-hidden="true"></i>
        <h3 class="mt-3">New dates are on the way</h3>
        <p class="mx-auto mb-3">Join our newsletter or follow us on Facebook to hear first.</p>
        <a class="btn btn-hmi-green" href="<?= h(url('events.php')) ?>">Events page</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- RESOURCES -->
<section class="section" aria-labelledby="blogTitle">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 section-head mb-4 reveal" style="max-width:none">
      <div>
        <p class="eyebrow">Teaching and updates</p>
        <h2 id="blogTitle" class="mb-0">Latest from the ministry</h2>
      </div>
      <a class="link-arrow" href="<?= h(url('resources.php')) ?>">All resources</a>
    </div>
    <div class="row g-4">
      <?php foreach ($posts as $p): ?>
        <div class="col-md-6 col-lg-4 reveal">
          <article class="hmi-card">
            <div class="cover v1" aria-hidden="true"><span class="tag"><?= h($p['category'] ?? 'Article') ?></span><i class="bi bi-journal-text"></i></div>
            <div class="body">
              <div class="meta"><span><?= h(fmt_date($p['published_at'] ?? $p['created_at'])) ?></span></div>
              <h3><a class="text-decoration-none" style="color:inherit" href="<?= h(url('post.php?slug=' . $p['slug'])) ?>"><?= h($p['title']) ?></a></h3>
              <p><?= h($p['excerpt']) ?></p>
              <a class="link-arrow mt-auto" href="<?= h(url('post.php?slug=' . $p['slug'])) ?>">Read more</a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- WATCH + CTA -->
<section class="section section-forest" aria-labelledby="ctaTitle">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-7">
        <p class="eyebrow eyebrow-light">You do not have to carry this alone</p>
        <h2 id="ctaTitle">Take the first step toward a healthier marriage</h2>
        <p class="lead" style="color:#cfe3d9">Tell us a little about your situation. A counselor from the ministry will respond to you personally.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a class="btn btn-hmi-red btn-lg" href="<?= h(url('counseling.php#request')) ?>">Book for counseling</a>
          <a class="btn btn-hmi-ghost btn-lg" href="<?= h(SITE['youtube']) ?>" target="_blank" rel="noopener"><i class="bi bi-youtube" aria-hidden="true"></i> Watch on YouTube</a>
        </div>
      </div>
      <div class="col-lg-5">
        <ul class="list-unstyled d-grid gap-3 mb-0">
          <li class="d-flex gap-3"><i class="bi bi-telephone-fill fs-4" style="color:var(--green)" aria-hidden="true"></i><span><a class="text-white fw-semibold text-decoration-none" href="<?= h(tel_link(SITE['phones_raw'][0])) ?>"><?= h(SITE['phones'][0]) ?></a><br><span style="color:#a9c4b6"><?= h(SITE['hours']) ?></span></span></li>
          <li class="d-flex gap-3"><i class="bi bi-geo-alt-fill fs-4" style="color:var(--green)" aria-hidden="true"></i><span><?= h(SITE['address']) ?></span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
