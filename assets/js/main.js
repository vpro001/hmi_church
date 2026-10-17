(function () {
  'use strict';

  var root = document.documentElement;
  root.classList.add('js');

  /* ---------- Header shadow + back to top ---------- */
  var header = document.getElementById('siteHeader');
  var toTop = document.getElementById('toTop');
  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (header) header.classList.toggle('is-scrolled', y > 8);
    if (toTop) toTop.hidden = y < 600;
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (toTop) toTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ---------- Reveal on scroll, with a safety net ---------- */
  var items = [].slice.call(document.querySelectorAll('.reveal'));
  if ('IntersectionObserver' in window && items.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    items.forEach(function (el) { io.observe(el); });
    setTimeout(function () { items.forEach(function (el) { el.classList.add('in'); }); }, 2500);
  } else {
    items.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Form validation (server validates again) ---------- */
  [].forEach.call(document.querySelectorAll('form[novalidate]'), function (form) {
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        var firstBad = form.querySelector(':invalid');
        if (firstBad) firstBad.focus();
      }
      form.classList.add('was-validated');
    });
  });

  /* ---------- Live filter: resources and events ---------- */
  function setupFilter(listSel, searchId, chipsSel) {
    var list = document.querySelector(listSel);
    if (!list) return;
    var cards = [].slice.call(list.querySelectorAll('[data-filter-item]'));
    var search = document.getElementById(searchId);
    var chips = [].slice.call(document.querySelectorAll(chipsSel + ' button'));
    var empty = document.querySelector(list.getAttribute('data-empty') || '#noResults');
    var cat = 'all';

    function apply() {
      var q = (search ? search.value : '').trim().toLowerCase();
      var shown = 0;
      cards.forEach(function (c) {
        var okCat = cat === 'all' || c.getAttribute('data-cat') === cat;
        var okQ = !q || (c.getAttribute('data-text') || '').indexOf(q) !== -1;
        var show = okCat && okQ;
        c.hidden = !show;
        if (show) shown++;
      });
      if (empty) empty.hidden = shown !== 0;
    }
    if (search) {
      var params = new URLSearchParams(window.location.search);
      if (params.get('q')) search.value = params.get('q');
      search.addEventListener('input', apply);
    }
    chips.forEach(function (b) {
      b.addEventListener('click', function () {
        chips.forEach(function (x) { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
        b.classList.add('active'); b.setAttribute('aria-pressed', 'true');
        cat = b.getAttribute('data-cat');
        apply();
      });
    });
    apply();
  }
  setupFilter('#resourceList', 'resourceSearch', '.pill-filters');

  /* ---------- Event countdown ---------- */
  [].forEach.call(document.querySelectorAll('[data-countdown]'), function (el) {
    var target = new Date(el.getAttribute('data-countdown')).getTime();
    if (isNaN(target)) return;
    function tick() {
      var d = target - Date.now();
      if (d <= 0) { el.textContent = 'Happening now'; return; }
      var days = Math.floor(d / 864e5);
      var hrs = Math.floor((d % 864e5) / 36e5);
      el.textContent = days + ' days ' + hrs + ' hrs to go';
    }
    tick(); setInterval(tick, 60000);
  });

  /* ---------- Auto-dismiss flash ---------- */
  var flash = document.getElementById('flash');
  if (flash && window.bootstrap) {
    flash.scrollIntoView({ block: 'nearest' });
    setTimeout(function () {
      try { bootstrap.Alert.getOrCreateInstance(flash).close(); } catch (e) {}
    }, 9000);
  }

  /* ---------- Shop: sort + filter ---------- */
  (function () {
    var list = document.getElementById('shopList');
    if (!list) return;
    var items = [].slice.call(list.querySelectorAll('[data-product]'));
    var sort = document.getElementById('shopSort');
    var min = document.getElementById('priceMin');
    var max = document.getElementById('priceMax');
    var chips = [].slice.call(document.querySelectorAll('#shopCats button'));
    var empty = document.getElementById('shopEmpty');
    var count = document.getElementById('shopCount');
    var reset = document.getElementById('shopReset');
    var cat = 'all';

    function apply() {
      var lo = parseInt(min.value, 10); var hi = parseInt(max.value, 10);
      var shown = items.filter(function (el) {
        var price = parseInt(el.getAttribute('data-price'), 10);
        var ok = (cat === 'all' || el.getAttribute('data-cat') === cat) &&
                 (isNaN(lo) || price >= lo) && (isNaN(hi) || price <= hi);
        el.hidden = !ok;
        return ok;
      });
      var mode = sort.value;
      shown.sort(function (a, b) {
        var pa = +a.getAttribute('data-price'), pb = +b.getAttribute('data-price');
        if (mode === 'price-asc') return pa - pb;
        if (mode === 'price-desc') return pb - pa;
        if (mode === 'name') return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
        return +a.getAttribute('data-order') - +b.getAttribute('data-order');
      }).forEach(function (el) { list.appendChild(el); });
      if (empty) empty.hidden = shown.length !== 0;
      if (count) count.textContent = shown.length + (shown.length === 1 ? ' e-book' : ' e-books');
    }
    [sort, min, max].forEach(function (el) { el.addEventListener('input', apply); });
    chips.forEach(function (b) {
      b.addEventListener('click', function () {
        chips.forEach(function (x) { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
        b.classList.add('active'); b.setAttribute('aria-pressed', 'true');
        cat = b.getAttribute('data-cat'); apply();
      });
    });
    if (reset) reset.addEventListener('click', function () {
      min.value = ''; max.value = ''; sort.value = 'default';
      chips[0] && chips[0].click(); apply();
    });
  })();

  /* ---------- Cart (kept in this browser; the server re-prices every order) ---------- */
  (function () {
    var KEY = 'hmi_cart_v1';
    var isCustomerLoggedIn = document.body.getAttribute('data-logged-in') === '1';
    var loginUrl = document.body.getAttribute('data-login-url') || 'login.php';

    function load() { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } }
    function save(c) { try { localStorage.setItem(KEY, JSON.stringify(c)); } catch (e) {} }

    // If customer is not logged in or clear cart is requested, wipe local cart storage
    var cart = isCustomerLoggedIn ? load() : [];
    if (!isCustomerLoggedIn || document.body.hasAttribute('data-clear-cart')) {
      cart = [];
      save(cart);
    }

    var elCount = document.getElementById('cartCount');
    var elEmpty = document.getElementById('cartEmpty');
    var elFilled = document.getElementById('cartFilled');
    var elLines = document.getElementById('cartLines');
    var elTotal = document.getElementById('cartTotal');
    var elJson = document.getElementById('cartJson');
    var form = document.getElementById('orderForm');
    var checkoutBtn = document.getElementById('checkoutBtn');

    if (!elCount) return;

    function money(n) { return '₦' + n.toLocaleString('en-NG'); }
    function esc(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

    function render() {
      if (!isCustomerLoggedIn) {
        if (elCount) { elCount.hidden = true; elCount.textContent = '0'; }
        if (elFilled) elFilled.hidden = true;
        if (elEmpty) elEmpty.hidden = false;
        return;
      }
      var qty = cart.reduce(function (a, l) { return a + l.qty; }, 0);
      var total = cart.reduce(function (a, l) { return a + l.qty * l.price; }, 0);
      if (elCount) { elCount.textContent = qty; elCount.hidden = qty === 0; }
      if (elEmpty) elEmpty.hidden = qty !== 0;
      if (elFilled) elFilled.hidden = qty === 0;
      if (elLines) {
        elLines.innerHTML = cart.map(function (l, i) {
          return '<li class="cart-line"><span class="t">' + esc(l.title) + '</span><span class="p">' + money(l.qty * l.price) + '</span>' +
            '<span class="qty" role="group" aria-label="Quantity for ' + esc(l.title) + '">' +
            '<button type="button" data-act="dec" data-i="' + i + '" aria-label="Decrease quantity">&minus;</button>' +
            '<span aria-live="polite">' + l.qty + '</span>' +
            '<button type="button" data-act="inc" data-i="' + i + '" aria-label="Increase quantity">+</button></span>' +
            '<button class="rm" type="button" data-act="rm" data-i="' + i + '">Remove</button></li>';
        }).join('');
      }
      if (elTotal) elTotal.textContent = money(total);
      if (elJson) elJson.value = JSON.stringify(cart.map(function (l) { return { id: l.id, qty: l.qty }; }));
    }

    document.addEventListener('click', function (e) {
      var add = e.target.closest('.add-to-cart');
      if (add) {
        // Enforce authentication: cannot add to cart without being logged in
        if (!isCustomerLoggedIn) {
          e.preventDefault();
          var redirectTarget = encodeURIComponent(window.location.pathname + window.location.search);
          window.location.href = loginUrl + '?redirect=' + redirectTarget + '&notice=login_required';
          return;
        }

        var id = add.getAttribute('data-id');
        var line = cart.filter(function (l) { return l.id === id; })[0];
        if (line) { line.qty = Math.min(20, line.qty + 1); }
        else { cart.push({ id: id, title: add.getAttribute('data-title'), price: parseInt(add.getAttribute('data-price'), 10), qty: 1 }); }
        save(cart); render();
        var label = add.innerHTML; add.innerHTML = '<i class="bi bi-check2" aria-hidden="true"></i> Added';
        setTimeout(function () { add.innerHTML = label; }, 1200);
        return;
      }
      var b = e.target.closest('#cartLines [data-act]');
      if (b) {
        var i = +b.getAttribute('data-i'), act = b.getAttribute('data-act');
        if (act === 'inc') cart[i].qty = Math.min(20, cart[i].qty + 1);
        if (act === 'dec') cart[i].qty -= 1;
        if (act === 'rm' || cart[i].qty < 1) cart.splice(i, 1);
        save(cart); render();
      }
      if (e.target.id === 'cartClear') { cart = []; save(cart); render(); }
    });

    if (form) {
      form.addEventListener('submit', function (e) {
        if (!isCustomerLoggedIn) {
          e.preventDefault();
          window.location.href = loginUrl + '?redirect=' + encodeURIComponent('shop.php') + '&notice=login_required';
          return;
        }
        if (!cart.length) {
          e.preventDefault();
          return;
        }
        if (checkoutBtn) {
          checkoutBtn.disabled = true;
          checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Connecting to Paystack...';
        }
      });
    }

    render();
  })();

})();
