<?php
$page_key   = 'about';
$page_title = 'About Us';
$page_desc  = 'Healing My-Marriage International Ministry is a nondenominational marriage and relationship outreach in Rivers State, Nigeria, upholding the whole truth of the Bible to teach, demonstrate and guide people to understand marriage and family matters.';
$page_path  = 'about.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'About Healing My-Marriage International';
$hero_sub   = 'A nondenominational Marriage/Relationship Outreach. We don\'t teach sectarian doctrine/dogma, rather we earnestly uphold the whole truth of the Bible.';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <p class="eyebrow">Welcome to HMI</p>
        <h2>Healing My-Marriage International Ministry, Inc.</h2>
        <div class="prose">
          <p>HMI is a nondenominational Marriage/Relationship Outreach. We don't teach or preach sectarian doctrine/dogma, rather we earnestly uphold the whole truth of the Bible.</p>
          <p>We are sent to teach, demonstrate and guide people to understand what exactly marriage is and for. How to prepare for a Godly fruitful and satisfying marital relation, and how to effectively manage marital crises.</p>
          <p>Interpreting what God expects and the place of marriage in Divine purpose and eternal plan. The marital union we espouse is that of one man and woman.</p>
          <p>We are mission oriented and open to all faith. We're poised to build families of God through our vertical approaches to marriage/family matters.</p>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/famlead.jpg')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/famlead.jpg')) ?>" alt="HMI Ministry Leadership" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php else: ?>
        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 300px;"><span class="text-muted">Leadership Image</span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="missionTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our Commitment</p>
      <h2 id="missionTitle">Nurturing faith-centered marriages and families</h2>
      <p class="lead">The ministry offers programs designed to support, educate and guide couples on their journey together.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
        <h3>Marriage Enrichment</h3><p>Workshops and resources focused on communication, trust and faith.</p>
      </div></article></div>
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip green"><i class="bi bi-house-heart-fill" aria-hidden="true"></i></div>
        <h3>Family Support</h3><p>Comfort and guidance for the entire family, fostering unity and spiritual growth.</p>
      </div></article></div>
      <div class="col-md-4 reveal"><article class="hmi-card"><div class="body">
        <div class="icon-chip red"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i></div>
        <h3>Faith-Based Counseling</h3><p>Spiritual teaching joined with practical advice to help you navigate challenges.</p>
      </div></article></div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="visionTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our Vision & Mission</p>
      <h2 id="visionTitle">To see Marriages/Families blossom, producing Godly, happy and fulfilled homes</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-6 reveal">
        <h3 class="h4">Our Vision</h3>
        <ul class="checklist mt-3">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To see Marriages/Families blossom, producing Godly, happy and fulfilled homes, secured and compassionate society, to the realization of the Kingdom Agenda.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To have effective leaders of the right kind emerge from functional families to be key players in global affairs.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To see the reproduction and perpetuation of well nurtured offspring as successors of the good parents.</span></li>
        </ul>
      </div>
      <div class="col-lg-6 reveal">
        <h3 class="h4">Our Mission</h3>
        <ul class="checklist mt-3">
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To provide clear understandable guidelines to initiate and maintain family through marriage.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To recover lost marriages, and serve as interventionist in Marriage/Family conflicts.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To restore Family values by providing framework benchmark.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To reconnect Families back to Christ and reinforce individual's commitment.</span></li>
          <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>To provide parenting framework geared towards raising healthy and productive Godly children for the Kingdom and society.</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="scopeTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our Scope</p>
      <h2 id="scopeTitle">What we do</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>
        <h3>Pre/Post Marriage Counseling</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip green"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
        <h3>Family Counseling</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip red"><i class="bi bi-building" aria-hidden="true"></i></div>
        <h3>Church/Group Seminars</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip stone"><i class="bi bi-gavel" aria-hidden="true"></i></div>
        <h3>Marriage/Family Disputes Mediation</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip"><i class="bi bi-calendar-heart" aria-hidden="true"></i></div>
        <h3>Wedding Planning/Officiating</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip green"><i class="bi bi-mortarboard" aria-hidden="true"></i></div>
        <h3>Training of Family Circle Teachers</h3>
      </div></div></div>
      <div class="col-md-4 col-sm-6 reveal"><div class="hmi-card"><div class="body">
        <div class="icon-chip red"><i class="bi bi-person-heart" aria-hidden="true"></i></div>
        <h3>Equipping Child Caregivers</h3>
      </div></div></div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="messageTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our Message</p>
      <h2 id="messageTitle">A happy and functional marriage is the foundation of a happy and functional church and society</h2>
    </div>
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <div class="prose">
          <p>We believe that a happy and functional marriage is the foundation of a happy and functional church and society.</p>
          <p>We encourage individuals to work on themselves and their marriages, as only they can bring about the change needed for a fulfilling life.</p>
          <p>No matter how long you've been married, there is always a higher marital life to strive for, just as there is always a higher faith experience to pursue.</p>
          <p>Our resources, including <strong>The Understanding of Marriage</strong> and <strong>Love Portion</strong>, are designed to provide you with the knowledge and insights needed to strengthen your marriage and family life.</p>
          <p>We pray that God opens your eyes, grants you a better perspective and understanding, and gives you the willingness to abide by Divine Principles. Mal.2:15; Gen.1:28.</p>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <?php if (file_exists(__DIR__ . '/assets/img/live/dfgdf.jpg')): ?>
        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
          <img src="<?= h(asset('img/live/dfgdf.jpg')) ?>" alt="Our Message" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="appealTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">Our Ernest Appeal</p>
      <h2 id="appealTitle">God wants to raise Kingdom Managers and Administrators</h2>
    </div>
    <div class="prose mx-auto" style="max-width: 800px;">
      <p>Marriage is not just for the individual's enjoyments. God has derivative interest in our marriage. Mal.2:15; Gen.1:28.</p>
      <p>As itinerant missionaries, we wish to appeal to you, ministers, leaders of communities and families: partner with us to build Godly homes that produce Kingdom leaders.</p>
    </div>
  </div>
</section>

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