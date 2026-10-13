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
  <img class="hero-bg" src="<?= h(asset('img/live/prayer.jpg')) ?>" alt="" aria-hidden="true">
  <div class="hero-scrim" aria-hidden="true"></div>
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <p class="eyebrow eyebrow-light">Welcome to Healing My-Marriage International</p>
        <h1 id="heroTitle">Reposition. Refix. <em>Rekindle</em> your marriage.</h1>
        <p class="lead mt-3">Discover the power of biblical values in your marriage and family. We walk with couples through every season &mdash; counseling, teaching and a community that prays. Because strong marriages build strong nations.</p>
        <div class="cta-row">
          <a class="btn btn-hmi-red btn-lg" href="<?= h(url('counseling.php#request')) ?>">Get counseled</a>
          <a class="btn btn-hmi-ghost btn-lg" href="<?= h(url('about.php')) ?>">Who we are</a>
        </div>
        <p class="hero-note"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Every request is handled in confidence.</p>
      </div>
      <div class="col-lg-5 d-none d-md-block">
        <!-- Cycle illustration built from the logo's vocabulary: three steps around a heart -->
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
  <!-- Trust ribbon -->
  <div class="trust-ribbon" aria-hidden="true">
    <div class="container">
      <span><i class="bi bi-check2"></i> Nondenominational</span>
      <span><i class="bi bi-check2"></i> Fully confidential</span>
      <span><i class="bi bi-check2"></i> Biblically grounded</span>
      <span><i class="bi bi-check2"></i> Rivers State, Nigeria</span>
    </div>
  </div>
</section>

<!-- THREE ASSURANCES -->
<section class="section section-cream2" aria-labelledby="assureTitle">
  <div class="container">
    <h2 id="assureTitle" class="visually-hidden">Why couples trust us</h2>
    <div class="assurances">
      <article class="assure reveal">
        <div class="icon-chip"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i></div>
        <h3>Personalized Counselling</h3>
        <p>Tailored sessions to address your unique relationship challenges.</p>
      </article>
      <article class="assure reveal">
        <div class="icon-chip green"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
        <h3>Experienced Counsellors</h3>
        <p>A team of qualified professionals dedicated to your relationship&rsquo;s success.</p>
      </article>
      <article class="assure reveal">
        <div class="icon-chip red"><i class="bi bi-award-fill" aria-hidden="true"></i></div>
        <h3>Proven Methods</h3>
        <p>Effective techniques that foster understanding and lasting connection.</p>
      </article>
    </div>
  </div>
</section>

<!-- THE THREE STEPS -->
<section class="section" aria-labelledby="stepsTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">How restoration happens</p>
      <h2 id="stepsTitle">A clear path back to each other</h2>
      <p class="lead">Healing rarely happens by accident. Three steps, each one rooted in Scripture and made practical for ordinary days.</p>
    </div>
    <div class="steps">
      <article class="step reveal">
        <span class="step-num" aria-hidden="true">I</span>
        <div class="chip"><i class="bi bi-compass-fill" aria-hidden="true"></i></div>
        <h3>Reposition</h3>
        <p>Return to God's design for marriage and see your spouse, your home and your calling from the right place.</p>
      </article>
      <article class="step reveal">
        <span class="step-num" aria-hidden="true">II</span>
        <div class="chip"><i class="bi bi-tools" aria-hidden="true"></i></div>
        <h3>Refix</h3>
        <p>Name what is broken and repair it with honest conversation, forgiveness and tools you can use on ordinary days.</p>
      </article>
      <article class="step reveal">
        <span class="step-num" aria-hidden="true">III</span>
        <div class="chip"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>
        <h3>Rekindle</h3>
        <p>Rebuild warmth, trust and joy, so that your marriage is a living testimony to those around you.</p>
      </article>
    </div>
  </div>
</section>

<!-- FOUNDER -->
<section class="section section-cream2" aria-labelledby="founderTitle">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5 reveal">
        <figure class="founder-photo">
          <img src="<?= h(asset('img/live/founder.jpg')) ?>" alt="Rev. Ogboo Akujuobi, PhD, founder of Healing My-Marriage International" loading="lazy">
          <span class="corner tl" aria-hidden="true"></span>
          <span class="corner br" aria-hidden="true"></span>
          <figcaption class="years">
            <span class="n">5</span>
            <span class="l">Years of experience</span>
          </figcaption>
        </figure>
      </div>
      <div class="col-lg-7 reveal">
        <p class="eyebrow">Why we do what we do</p>
        <h2 id="founderTitle">Let&rsquo;s build stronger marriages, together</h2>
        <blockquote class="founder-quote">When I see a cracked marriage, a broken home, an unhappy couple with children in disarray, my heart bleeds. I know the devastation and frustration people suffer in a difficult marriage. I know the lifelong consequences on the products of that union &mdash; how they may lose happiness and fulfillment all through life, and how the same narrative may continue into the next generation if they were not rescued. I know this too well, because I was involved.</blockquote>
        <p class="founder-name">Rev. Ogboo Akujuobi, PhD</p>
        <p class="founder-role">Founder &amp; Serving Overseer</p>
        <div class="why-choose">
          <div><strong>Expert guidance</strong><span>Years of pastoral experience</span></div>
          <div><strong>Personal approach</strong><span>Shaped around your story</span></div>
          <div><strong>Faith-based</strong><span>Biblical, practical wisdom</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MINISTRY PILLARS -->
<section class="section" aria-labelledby="pillarsTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our commitment</p>
      <h2 id="pillarsTitle">Support for every part of the family</h2>
      <p class="lead">Counseling, young people and the leaders who serve them &mdash; three works, one heart for the family.</p>
    </div>
    <div class="pillars">
      <a class="pillar reveal" href="<?= h(url('counseling.php')) ?>">
        <img src="<?= h(asset('img/live/marriage-counselling.jpg')) ?>" alt="Marriage and family counseling" loading="lazy">
        <div class="pillar-scrim" aria-hidden="true"></div>
        <div class="pillar-body">
          <span class="k">Counseling</span>
          <h3>Marriage &amp; Family Counseling</h3>
          <p>Confidential, personalized sessions that bring biblical wisdom together with practical help for the challenges you face.</p>
          <span class="link-arrow">Learn more</span>
        </div>
      </a>
      <a class="pillar reveal" href="<?= h(url('programs.php#youth')) ?>">
        <img src="<?= h(asset('img/live/banner1.jpg')) ?>" alt="Supporting Youths Initiative" loading="lazy">
        <div class="pillar-scrim" aria-hidden="true"></div>
        <div class="pillar-body">
          <span class="k">Youths</span>
          <h3>Supporting Youths Initiative</h3>
          <p>Building tomorrow&rsquo;s leaders by investing in young people today &mdash; character, faith and preparation for relationships.</p>
          <span class="link-arrow">See the initiative</span>
        </div>
      </a>
      <a class="pillar reveal" href="<?= h(url('programs.php#ministers')) ?>">
        <img src="<?= h(asset('img/live/famlead.jpg')) ?>" alt="Marriage and family leadership for ministers" loading="lazy">
        <div class="pillar-scrim" aria-hidden="true"></div>
        <div class="pillar-body">
          <span class="k">Ministers</span>
          <h3>Leadership for Ministers</h3>
          <p>Leadership with love and biblical wisdom, for pastors and ministers who serve couples and families.</p>
          <span class="link-arrow">See ministers&rsquo; track</span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- COMMUNITY BAND -->
<section class="community-band" aria-labelledby="commTitle">
  <img class="community-bg" src="<?= h(asset('img/live/dfgdf.jpg')) ?>" alt="" aria-hidden="true" loading="lazy">
  <div class="community-scrim" aria-hidden="true"></div>
  <div class="container">
    <div class="community-inner reveal">
      <p class="eyebrow eyebrow-light">We are building nations out of marriages</p>
      <h2 id="commTitle">A happy marriage is the foundation of a happy church and society</h2>
      <p>Every home restored becomes a foundation for the next generation. No matter how long you&rsquo;ve been married, there is always a higher marital life to strive for.</p>
      <div class="d-flex flex-wrap gap-3 mt-4 justify-content-center">
        <a class="btn btn-hmi-green btn-lg" href="<?= h(url('about.php')) ?>">Read our story</a>
        <a class="btn btn-hmi-ghost btn-lg" href="<?= h(url('programs.php')) ?>">See our programs</a>
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

<!-- CTA -->
<section class="section section-forest cta-band" aria-labelledby="ctaTitle">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-7">
        <p class="eyebrow eyebrow-light">Are you in need of counseling?</p>
        <h2 id="ctaTitle">Take the first step toward a healthier marriage</h2>
        <p class="lead" style="color:#cfe3d9">Tell us a little about your situation. A counselor from the ministry will respond to you personally.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a class="btn btn-hmi-red btn-lg" href="<?= h(url('counseling.php#request')) ?>">Book for counseling</a>
          <a class="btn btn-hmi-ghost btn-lg" href="<?= h(SITE['youtube']) ?>" target="_blank" rel="noopener"><i class="bi bi-youtube" aria-hidden="true"></i> Watch on YouTube</a>
        </div>
      </div>
      <div class="col-lg-5">
        <ul class="list-unstyled d-grid gap-3 mb-0">
          <li class="d-flex gap-3"><i class="bi bi-telephone-fill fs-4" style="color:var(--gold-lt)" aria-hidden="true"></i><span><a class="text-white fw-semibold text-decoration-none" href="<?= h(tel_link(SITE['phones_raw'][0])) ?>"><?= h(SITE['phones'][0]) ?></a><br><span style="color:#a9c4b6"><?= h(SITE['hours']) ?></span></span></li>
          <li class="d-flex gap-3"><i class="bi bi-geo-alt-fill fs-4" style="color:var(--gold-lt)" aria-hidden="true"></i><span><?= h(SITE['address']) ?></span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
