/**
 * GamTech Store — Interactive UI
 * Handles: sidebar toggle, cart panel, qty controls,
 *          wishlist, promo code, category filter, search
 */
(function () {
  'use strict';

  /* =====================================================
     INIT — wait for DOM
     ===================================================== */
  document.addEventListener('DOMContentLoaded', function () {
    initMobileToggles();
    initOverlay();
    initQtyControls();
    initWishlist();
    initCartAdd();
    initPromoCode();
    initCategoryFilter();
    initSearchFocus();
    animateOnScroll();
  });

  /* =====================================================
     MOBILE SIDEBAR & CART TOGGLES
     ===================================================== */
  function initMobileToggles() {
    var sidebarToggle = qs('#gs-sidebar-toggle');
    var cartToggle    = qs('#gs-cart-toggle');
    var sidebar       = qs('.gs-sidebar');
    var cart          = qs('.gs-cart');
    var overlay       = qs('#gs-overlay');

    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
      });
    }

    if (cartToggle && cart) {
      cartToggle.addEventListener('click', function () {
        cart.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
      });
    }

    // Close btn inside cart header
    var cartClose = qs('#gs-cart-close');
    if (cartClose && cart) {
      cartClose.addEventListener('click', function () {
        cart.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
      });
    }
  }

  /* =====================================================
     OVERLAY DISMISS
     ===================================================== */
  function initOverlay() {
    var overlay = qs('#gs-overlay');
    if (!overlay) return;
    overlay.addEventListener('click', function () {
      overlay.classList.remove('active');
      var sidebar = qs('.gs-sidebar');
      var cart    = qs('.gs-cart');
      if (sidebar) sidebar.classList.remove('open');
      if (cart)    cart.classList.remove('open');
    });
  }

  /* =====================================================
     QUANTITY CONTROLS
     ===================================================== */
  function initQtyControls() {
    // Delegate on cart items container
    var cartItems = qs('.gs-cart-items');
    if (!cartItems) return;

    cartItems.addEventListener('click', function (e) {
      var btn = e.target.closest('.gs-qty-btn');
      if (!btn) return;

      var wrap  = btn.closest('.gs-qty');
      var numEl = wrap && wrap.querySelector('.gs-qty-num');
      if (!numEl) return;

      var val = parseInt(numEl.textContent, 10) || 1;
      if (btn.dataset.action === 'minus') {
        val = Math.max(1, val - 1);
      } else {
        val = val + 1;
      }
      numEl.textContent = val;
      updateCartTotals();
    });

    // Delete item
    cartItems.addEventListener('click', function (e) {
      var del = e.target.closest('.gs-cart-del');
      if (!del) return;
      var item = del.closest('.gs-cart-item');
      if (item) {
        item.style.transition = 'all 0.25s';
        item.style.opacity = '0';
        item.style.transform = 'translateX(20px)';
        setTimeout(function () {
          item.remove();
          updateCartTotals();
          updateCartCountBadge();
        }, 250);
      }
    });
  }

  /* =====================================================
     CART TOTAL RECALCULATION
     ===================================================== */
  function updateCartTotals() {
    var items     = qsa('.gs-cart-item');
    var subtotal  = 0;
    var discount  = 0;
    var promoActive = qs('#gs-promo-applied');

    items.forEach(function (item) {
      var priceEl = item.querySelector('.gs-cart-item-price');
      var qtyEl   = item.querySelector('.gs-qty-num');
      if (!priceEl || !qtyEl) return;
      var price = parseFloat(priceEl.dataset.price || priceEl.textContent.replace(/[^0-9.]/g, '')) || 0;
      var qty   = parseInt(qtyEl.textContent, 10) || 1;
      subtotal += price * qty;
    });

    if (promoActive) discount = subtotal * 0.1; // 10% promo

    var total = subtotal - discount;

    setElText('#gs-subtotal', '$' + subtotal.toFixed(2));
    setElText('#gs-discount', discount > 0 ? '-$' + discount.toFixed(2) : '$0.00');
    setElText('#gs-total',    '$' + total.toFixed(2));

    // Update checkout button count
    var checkoutBtn = qs('#gs-checkout-btn');
    if (checkoutBtn) {
      checkoutBtn.querySelector('.btn-count') &&
        (checkoutBtn.querySelector('.btn-count').textContent = '(' + items.length + ')');
    }
  }

  function updateCartCountBadge() {
    var items = qsa('.gs-cart-item');
    var badge = qs('.gs-cart-count-badge');
    if (badge) badge.textContent = items.length;
    var headerBadge = qs('#gs-cart-header-badge');
    if (headerBadge) headerBadge.textContent = items.length;
  }

  /* =====================================================
     WISHLIST TOGGLE
     ===================================================== */
  function initWishlist() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.gs-product-action-btn[data-action="wish"]');
      if (!btn) return;
      btn.classList.toggle('wishlisted');
      var icon = btn.querySelector('svg');
      if (icon) {
        icon.style.color = btn.classList.contains('wishlisted') ? '#ef4444' : '';
      }
      btn.style.transform = 'scale(1.3)';
      setTimeout(function () { btn.style.transform = ''; }, 200);
    });
  }

  /* =====================================================
     ADD TO CART (demo — adds a placeholder item)
     ===================================================== */
  function initCartAdd() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.gs-add-cart');
      if (!btn) return;
      var card   = btn.closest('.gs-product-card');
      if (!card) return;

      var name   = card.querySelector('.gs-product-name')  ? card.querySelector('.gs-product-name').textContent.trim()  : 'Product';
      var price  = card.querySelector('.gs-price-current') ? card.querySelector('.gs-price-current').textContent.trim() : '$0.00';
      var imgEl  = card.querySelector('.gs-product-img-wrap img');
      var imgSrc = imgEl ? imgEl.src : '';

      addToCartDOM(name, price, imgSrc);
      animateCartBounce();
      showAddedFeedback(btn);
    });
  }

  function addToCartDOM(name, price, imgSrc) {
    var cartItems = qs('.gs-cart-items');
    if (!cartItems) return;

    var priceNum = parseFloat(price.replace(/[^0-9.]/g, '')) || 0;
    var item = document.createElement('div');
    item.className = 'gs-cart-item gs-fade-in';
    item.innerHTML =
      '<img class="gs-cart-item-img" src="' + (imgSrc || '') + '" alt="">' +
      '<div class="gs-cart-item-info">' +
        '<div class="gs-cart-item-name">' + escHtml(name) + '</div>' +
        '<div class="gs-cart-item-sub">1 × ' + price + '</div>' +
        '<div class="gs-cart-item-price" data-price="' + priceNum + '">' + price + '</div>' +
        '<div class="gs-qty">' +
          '<button class="gs-qty-btn" data-action="minus">−</button>' +
          '<span class="gs-qty-num">1</span>' +
          '<button class="gs-qty-btn" data-action="plus">+</button>' +
        '</div>' +
      '</div>' +
      '<button class="gs-cart-del" title="Remove" aria-label="Remove item">' +
        '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
          '<polyline points="3 6 5 6 21 6"/>' +
          '<path d="M19 6l-1 14H6L5 6"/>' +
          '<path d="M10 11v6M14 11v6"/>' +
          '<path d="M9 6V4h6v2"/>' +
        '</svg>' +
      '</button>';

    cartItems.appendChild(item);
    updateCartTotals();
    updateCartCountBadge();

    // Open cart panel if on desktop
    var cart = qs('.gs-cart');
    if (cart && window.innerWidth > 1024) {
      cart.classList.add('open');
    }
  }

  function animateCartBounce() {
    var badge = qs('#gs-cart-header-badge');
    if (!badge) return;
    badge.style.transform = 'scale(1.5)';
    setTimeout(function () { badge.style.transform = ''; }, 250);
  }

  function showAddedFeedback(btn) {
    var orig = btn.innerHTML;
    btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Added!';
    btn.style.background = 'var(--gs-green)';
    btn.style.color = '#fff';
    setTimeout(function () {
      btn.innerHTML = orig;
      btn.style.background = '';
      btn.style.color = '';
    }, 1200);
  }

  /* =====================================================
     PROMO CODE
     ===================================================== */
  function initPromoCode() {
    var applyBtn = qs('#gs-promo-apply');
    if (!applyBtn) return;

    applyBtn.addEventListener('click', function () {
      var input = qs('#gs-promo-input');
      if (!input) return;
      var code  = input.value.trim().toUpperCase();
      var validCodes = ['TECH10', 'GAMTECH', 'SAVE10'];
      var msgEl = qs('#gs-promo-msg');

      if (validCodes.indexOf(code) !== -1) {
        if (msgEl) {
          msgEl.textContent = '✓ Promo applied — 10% off!';
          msgEl.style.color = 'var(--gs-green)';
        }
        // Mark as applied for total recalc
        var marker = document.createElement('span');
        marker.id  = 'gs-promo-applied';
        marker.style.display = 'none';
        document.body.appendChild(marker);
        updateCartTotals();
        applyBtn.textContent = 'Applied';
        applyBtn.style.background = 'var(--gs-green)';
        applyBtn.disabled = true;
      } else {
        if (msgEl) {
          msgEl.textContent = code ? 'Invalid code. Try TECH10' : 'Enter a promo code.';
          msgEl.style.color = 'var(--gs-red)';
        }
      }
    });

    // Allow enter key
    var input = qs('#gs-promo-input');
    if (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') applyBtn.click();
      });
    }
  }

  /* =====================================================
     CATEGORY FILTER
     ===================================================== */
  function initCategoryFilter() {
    var cats = qsa('.gs-cat-item');
    cats.forEach(function (cat) {
      cat.addEventListener('click', function () {
        cats.forEach(function (c) { c.classList.remove('active'); });
        cat.classList.add('active');
      });
    });
  }

  /* =====================================================
     SEARCH FOCUS EXPAND
     ===================================================== */
  function initSearchFocus() {
    var input = qs('.gs-search-wrap input');
    if (!input) return;
    input.addEventListener('focus', function () {
      input.parentElement.style.maxWidth = '680px';
    });
    input.addEventListener('blur', function () {
      input.parentElement.style.maxWidth = '';
    });
  }

  /* =====================================================
     SCROLL-IN ANIMATION
     ===================================================== */
  function animateOnScroll() {
    if (!('IntersectionObserver' in window)) return;
    var els = qsa('.gs-product-card, .gs-promo-card, .gs-feature-card');
    var io  = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('gs-fade-in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* =====================================================
     HELPERS
     ===================================================== */
  function qs(sel, ctx)  { return (ctx || document).querySelector(sel); }
  function qsa(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }
  function setElText(sel, val) { var el = qs(sel); if (el) el.textContent = val; }
  function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

})();
