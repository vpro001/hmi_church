</main>

<section class="newsletter-band" aria-labelledby="nlTitle">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <p class="eyebrow eyebrow-light">Stay connected</p>
        <h2 id="nlTitle" class="h3">Encouragement for your marriage, in your inbox</h2>
        <p class="mb-0">Teaching, event notices and prayer points from the ministry. No spam, and you can leave at any time.</p>
      </div>
      <div class="col-lg-6">
        <form class="newsletter-form" action="<?= h(url('actions/newsletter.php')) ?>" method="post" novalidate>
          <?= csrf_field() ?>
          <label class="visually-hidden" for="nlEmail">Email address</label>
          <input class="form-control" type="email" id="nlEmail" name="email" placeholder="Your email address" required autocomplete="email">
          <button class="btn btn-hmi-red" type="submit">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <a class="footer-logo" href="<?= h(url('index.php')) ?>" aria-label="Home">
          <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International Ministry, Inc." width="508" height="286" loading="lazy">
        </a>
        <p class="mt-3">A faith-centered ministry helping couples and families reposition, refix and rekindle what God has joined together.</p>
        <div class="d-flex gap-3 social">
          <a href="<?= h(SITE['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="<?= h(SITE['youtube']) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h2 class="footer-h">Explore</h2>
        <ul class="list-unstyled footer-links">
          <?php foreach (NAV as $item): ?>
            <li><a href="<?= h(url($item['href'])) ?>"><?= h($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h2 class="footer-h">What we offer</h2>
        <ul class="list-unstyled footer-links">
          <li><a href="<?= h(url('counseling.php')) ?>">Faith-based counseling</a></li>
          <li><a href="<?= h(url('programs.php#enrichment')) ?>">Marriage enrichment</a></li>
          <li><a href="<?= h(url('programs.php#family')) ?>">Family support</a></li>
          <li><a href="<?= h(url('programs.php#youth')) ?>">Supporting Youths Initiative</a></li>
          <li><a href="<?= h(url('programs.php#ministers')) ?>">Leadership for ministers</a></li>
          <li><a href="<?= h(url('shop.php')) ?>">Shop: Christian e-books</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h2 class="footer-h">Reach us</h2>
        <ul class="list-unstyled footer-contact">
          <li><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <span><?= h(SITE['address']) ?></span></li>
          <?php foreach (SITE['phones_raw'] as $i => $raw): ?>
            <li><i class="bi bi-telephone-fill" aria-hidden="true"></i> <a href="<?= h(tel_link($raw)) ?>"><?= h(SITE['phones'][$i]) ?></a></li>
          <?php endforeach; ?>
          <li><i class="bi bi-envelope-fill" aria-hidden="true"></i> <a href="mailto:<?= h(SITE['email']) ?>"><?= h(SITE['email']) ?></a></li>
          <li><i class="bi bi-clock-fill" aria-hidden="true"></i> <span><?= h(SITE['hours']) ?></span></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2">
      <span>&copy; <?= date('Y') ?> <?= h(SITE['name']) ?> All rights reserved.</span>
      <span><a href="<?= h(url('privacy.php')) ?>">Privacy policy</a> &middot; <a href="<?= h(url('privacy.php#terms')) ?>">Terms of use</a></span>
    </div>
  </div>
</footer>

<button class="to-top" id="toTop" type="button" aria-label="Back to top" hidden><i class="bi bi-arrow-up"></i></button>

<script src="<?= h(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= h(asset('js/main.js')) ?>"></script>
</body>
</html>
