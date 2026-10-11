<?php /* Expects: $hero_title, $hero_sub (optional), $hero_crumb (optional) */ ?>
<section class="page-hero" aria-labelledby="pageTitle">
  <div class="container position-relative">
    <nav class="breadcrumb-lite" aria-label="Breadcrumb">
      <a href="<?= h(url('index.php')) ?>">Home</a> <span aria-hidden="true">/</span> <span><?= h($hero_crumb ?? $hero_title) ?></span>
    </nav>
    <h1 id="pageTitle"><?= h($hero_title) ?></h1>
    <?php if (!empty($hero_sub)): ?><p class="mt-3 mb-0" style="max-width:40rem"><?= h($hero_sub) ?></p><?php endif; ?>
  </div>
  <div class="swoosh-edge" aria-hidden="true">
    <svg viewBox="0 0 1440 140" preserveAspectRatio="none">
      <path d="M0 18 C 380 150 1060 150 1440 18 V140 H0 Z" fill="#1c9eb4"/>
      <path d="M0 52 C 380 184 1060 184 1440 52 V140 H0 Z" fill="#faf6ea"/>
    </svg>
  </div>
</section>
