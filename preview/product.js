(function () {
  'use strict';

  function esc(s) {
    return String(s || '').replace(/[&<>"']/g, (c) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));
  }

  function formatCFA(n) {
    return Math.round(Number(n) || 0).toLocaleString('fr-FR') + ' CFA';
  }

  function t(key, fallback) {
    return window.gsT ? window.gsT(key) : (fallback || key);
  }

  function addToCart(product, qty) {
    var box = document.getElementById('pd-ct-items');
    if (!box) {
      // Cart drawer lives on the shop page; make sure the item is stored anyway.
      box = document.createElement('div');
      box.id = 'pd-ct-items';
      box.hidden = true;
      document.body.appendChild(box);
    }
    var exists = Array.from(box.children).some((el) => el.dataset.id === String(product.id));
    if (exists) return false;
    var pn = parseFloat(product.price) || 0;
    var img = product.img || '';
    var html = '<div class="gs-ct-item" data-id="' + esc(product.id) + '">'
      + (img
        ? '<img class="gs-ct-thumb" src="' + esc(img) + '" alt="">'
        : '<div class="gs-ct-thumb-ph"></div>')
      + '<div class="gs-ct-info"><div class="gs-ct-name">' + esc(product.name) + '</div><div class="gs-ct-sub">GamTech</div>'
      + '<div class="gs-ct-price" data-price="' + pn + '">' + (pn ? esc(formatCFA(pn)) : esc(t('ask', 'Prix sur demande'))) + '</div>'
      + '<div class="gs-qty"><span class="gs-qty-n">' + (qty || 1) + '</span></div></div></div>';
    box.insertAdjacentHTML('beforeend', html);
    return true;
  }

  function init() {
    var params = new URLSearchParams(location.search);
    var id = params.get('id') || params.get('i') || '';
    var p = (window.GAMTECH_PRODUCTS || []).find((x) => String(x.id) === String(id));
    var nf = document.getElementById('pd-nf');
    if (!p) {
      if (nf) {
        nf.hidden = false;
        document.getElementById('pd-nf-id').textContent = id ? ('Référence : ' + id) : '';
      }
      return;
    }

    document.title = 'GamTech – ' + p.name;
    document.getElementById('pd-title').textContent = p.name;
    document.getElementById('pd-brand').textContent = p.sub || p.cat || 'GamTech';
    document.getElementById('pd-sub').textContent = p.cat || '';
    document.getElementById('pd-crumb-cat').textContent = p.cat || 'Boutique';
    document.getElementById('pd-crumb-here').textContent = p.name;
    document.getElementById('pd-id').textContent = 'Ref. ' + p.id;

    var hasPrice = p.price != null && p.price !== '' && Number(p.price) > 0;
    var priceTxt = hasPrice ? formatCFA(p.price) : t('ask', 'Prix sur demande');
    document.getElementById('pd-price').textContent = priceTxt;
    document.getElementById('pd-ask').hidden = hasPrice;
    var wasEl = document.getElementById('pd-was');
    if (hasPrice && p.was != null && Number(p.was) > Number(p.price)) {
      wasEl.textContent = formatCFA(p.was);
      wasEl.hidden = false;
    } else {
      wasEl.hidden = true;
    }

    var imgWrap = document.getElementById('pd-img-wrap');
    if (p.img && !p.listOnly) {
      imgWrap.innerHTML = '<img src="' + esc(p.img) + '" alt="' + esc(p.name) + '">';
    } else {
      imgWrap.innerHTML = '<svg class="gs-ph-ico" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><circle cx="8" cy="9" r="2"/><path d="m21 15-5-5L5 20"/></svg>'
        + '<span class="gs-ph-txt">' + esc(t('photoSoon', 'Photo à venir')) + '</span>';
      imgWrap.classList.add('pd-img-empty');
    }

    var rows = [];
    if (p.sub) rows.push(['Marque / Sous-catégorie', p.sub]);
    if (p.cat) rows.push(['Catégorie', p.cat]);
    if (p.also) rows.push([' Aussi connu sous', p.also]);
    rows.push(['Prix', priceTxt]);
    document.getElementById('pd-specs').innerHTML = rows
      .map((r) => '<div><dt>' + esc(r[0]) + '</dt><dd>' + esc(r[1]) + '</dd></div>')
      .join('');

    var addBtn = document.getElementById('pd-add-btn');
    addBtn.textContent = t('add', 'Ajouter au panier');
    addBtn.addEventListener('click', function () {
      var added = addToCart(p, 1);
      addBtn.textContent = added ? t('added', 'Ajouté') : (window.gsT ? window.gsT('already_added') : 'Déjà ajouté');
      setTimeout(function () { addBtn.textContent = t('add', 'Ajouter au panier'); }, 1500);
    });

    var buyBtn = document.getElementById('pd-buy-btn');
    buyBtn.addEventListener('click', function () {
      addToCart(p, 1);
      // If the cart drawer is present (shop page), let its WhatsApp handler run.
      var waBtn = document.querySelector('.gs-whatsapp-btn');
      if (waBtn) {
        waBtn.click();
        return;
      }
      // Standalone product page: build the order message inline.
      var pn = parseFloat(p.price) || 0;
      var total = pn;
      var message = 'Nouvelle commande\n\n1. ' + p.name + '\n';
      message += pn > 0 ? ('Qty: 1 x ' + formatCFA(pn) + ' = ' + formatCFA(total) + '\n\nTotal: ' + formatCFA(total))
                         : 'Qty: 1 — Prix sur demande';
      window.location.href = 'https://wa.me/22890597003?text=' + encodeURIComponent(message);
    });

    document.getElementById('pd-page').hidden = false;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
