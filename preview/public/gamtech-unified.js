(function(){
'use strict';
var qs=function(s,c){return(c||document).querySelector(s)};
var qa=function(s,c){return Array.from((c||document).querySelectorAll(s))};
function formatCFA(n){return Math.round(Number(n)||0).toLocaleString('fr-FR')+' CFA';}

function cartId(){
  var id='';
  try{id=localStorage.getItem('gs-cart-id')||'';}catch(e){}
  if(!id){
    id='GT-'+Math.random().toString(36).slice(2,8).toUpperCase();
    try{localStorage.setItem('gs-cart-id',id);}catch(e){}
  }
  return id;
}

function showCartId(){
  var el=qs('#gs-cart-id');
  if(!el)return;
  el.hidden=false;
  el.textContent=(window.gsT?window.gsT('cart.id'):'Panier')+' '+cartId();
}

function encodeOrder(o){
  var compact={i:o.id,n:o.name,t:o.total,l:(o.items||[]).map(function(x){return[x.name,x.qty,x.price];})};
  var json=JSON.stringify(compact);
  return btoa(unescape(encodeURIComponent(json))).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');
}

function cookieGet(name){
  var parts=('; '+document.cookie).split('; '+name+'=');
  if(parts.length<2)return '';
  return parts.pop().split(';').shift();
}

function cookieSet(name,value,days){
  var max=days?'; max-age='+(days*86400):'';
  document.cookie=name+'='+value+'; path=/'+max+'; SameSite=Lax';
}

function deviceId(){
  var id=cookieGet('gs_did');
  if(id)return id;
  id='d'+Math.random().toString(36).slice(2)+Date.now().toString(36);
  cookieSet('gs_did',id,365);
  return id;
}

function paintHero(data){
  var wrap=qs('#gs-hero-slides');
  var dots=qs('#gs-hero-dots');
  if(!wrap||!data||!data.slides||!data.slides.length)return false;
  wrap.innerHTML=data.slides.map(function(s,i){
    var img=s.img||'';
    return '<div class="gs-hero-slide'+(i===0?' gs-hero-store active':'')+'" style="background-image:url(\''+String(img).replace(/'/g,'')+'\')">'
      +'<div class="gs-hero-slide-content">'
      +'<div class="gs-hero-tag"><span class="dot"></span>'+(s.tag||'')+'</div>'
      +'<h1>'+(s.title||'')+(s.highlight?'<br><span>'+s.highlight+'</span>':'')+'</h1>'
      +'<p>'+(s.text||'')+'</p>'
      +'<a href="#shop" class="gs-hero-cta">'+(s.cta||'Shop Now')+'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>'
      +'</div></div>';
  }).join('');
  dots.innerHTML=data.slides.map(function(_s,i){
    return '<span class="gs-hero-dot'+(i===0?' active':'')+'" data-slide="'+i+'"></span>';
  }).join('');
  return true;
}

function initHeroSlider(){
  var slides=qa('.gs-hero-slide'), dots=qa('.gs-hero-dot'), cur=0;
  function showSlide(i){
    slides=qa('.gs-hero-slide');
    dots=qa('.gs-hero-dot');
    if(!slides.length)return;
    slides.forEach(function(s){s.classList.remove('active')});
    dots.forEach(function(d){d.classList.remove('active')});
    cur=i;if(cur>=slides.length)cur=0;if(cur<0)cur=slides.length-1;
    slides[cur]&&slides[cur].classList.add('active');
    dots[cur]&&dots[cur].classList.add('active');
  }
  if(slides.length>1){
    dots.forEach(function(d){d.addEventListener('click',function(){showSlide(parseInt(d.dataset.slide)||0);});});
    setInterval(function(){showSlide(cur+1);},5000);
  }
}

document.addEventListener('DOMContentLoaded',function(){
  var sb=qs('#gs-sb'), ct=qs('#gs-ct'), ov=qs('#gs-ov');

  qa('a.gs-nav-a, a.gs-hd-action').forEach(function(a){
    var href=(a.getAttribute('href')||'').toLowerCase();
    var text=(a.textContent||'').replace(/\s+/g,' ').trim().toLowerCase();
    if(href.indexOf('admin')!==-1||href==='#account'||text.indexOf('my account')!==-1||text.indexOf('stock desk')!==-1){
      a.remove();
    }
  });

  function openPanel(el){
    el.classList.add('open');
    if(window.innerWidth<=1024) ov&&ov.classList.add('on');
  }
  function closeAll(){
    if(sb)sb.classList.remove('open');
    if(ct)ct.classList.remove('open');
    if(ov)ov.classList.remove('on');
  }

  var sbTog=qs('#gs-sb-tog'), ctClose=qs('#gs-ct-close');
  qa('.gs-ct-tog-btn').forEach(function(btn){btn.addEventListener('click',function(){openPanel(ct);});});
  sbTog&&sbTog.addEventListener('click',function(){openPanel(sb);});
  ctClose&&ctClose.addEventListener('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    closeAll();
  });
  ov&&ov.addEventListener('click',function(e){
    e.preventDefault();
    closeAll();
  });

  var catsTog=qs('#gs-cats-tog'), catsList=qs('#gs-cats-list');
  if(catsTog&&catsList){
    catsTog.addEventListener('click',function(){
      var open=catsTog.classList.toggle('open');
      catsList.style.display=open?'flex':'none';
    });
  }

  var ctItems=qs('#gs-ct-items');
  ctItems&&ctItems.addEventListener('click',function(e){
    var btn=e.target.closest('.gs-qty-btn');
    if(btn){var n=btn.closest('.gs-qty').querySelector('.gs-qty-n');var v=parseInt(n.textContent)||1;
      n.textContent=btn.dataset.action==='minus'?Math.max(1,v-1):v+1;recalc();return;}
    var del=e.target.closest('.gs-ct-del');
    if(del){var item=del.closest('.gs-ct-item');
      item.style.cssText='opacity:0;transform:translateX(16px);transition:all .2s';
      setTimeout(function(){item.remove();recalc();updateBadge();},220);}
  });

  function recalc(){
    var sub=0,hasPromo=document.getElementById('gs-promo-applied');
    qa('.gs-ct-item').forEach(function(item){
      var prEl=item.querySelector('.gs-ct-price');var qEl=item.querySelector('.gs-qty-n');
      if(!prEl||!qEl)return;
      sub+=(parseFloat(prEl.dataset.price)||0)*(parseInt(qEl.textContent)||1);
    });
    var disc=hasPromo?sub*0.1:0, tot=sub-disc;
    var s=qs('#gs-sub');if(s)s.textContent=formatCFA(sub);
    var d=qs('#gs-disc');if(d)d.textContent=disc>0?'-'+formatCFA(disc):formatCFA(0);
    var t=qs('#gs-tot');if(t)t.textContent=formatCFA(tot);
  }

  function updateBadge(){
    var n=qa('.gs-ct-item').length;
    var b1=qs('#gs-badge-hd');if(b1)b1.textContent=n;
    var b2=qs('#gs-badge-mob');if(b2)b2.textContent=n;
    var bc=qs('#gs-ct-cnt');if(bc)bc.textContent=n;
  }

  var nameIn=qs('#gs-cart-name');
  if(nameIn){
    try{nameIn.value=localStorage.getItem('gs-cart-name')||'';}catch(e){}
    nameIn.addEventListener('input',function(){
      try{localStorage.setItem('gs-cart-name',nameIn.value);}catch(e){}
    });
  }

  document.addEventListener('click',function(e){
    var btn=e.target.closest('.gs-add-btn');
    if(!btn)return;
    e.preventDefault();
    var card=btn.closest('.gs-card');
    var name=btn.dataset.name||(card&&card.querySelector('.gs-card-name')||{}).textContent||'Product';
    var pn=parseFloat(btn.dataset.price)||0;
    var img=btn.dataset.img||'';
    var productId=btn.dataset.id||'';
    var html='<div class="gs-ct-item" style="animation:gsfade .3s ease" data-id="'+productId+'">'
      +(img?'<img class="gs-ct-thumb" src="'+img+'" alt="">':'<div class="gs-ct-thumb-ph"><svg width="18" height="18" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>')
      +'<div class="gs-ct-info"><div class="gs-ct-name">'+name.replace(/</g,'&lt;')+'</div><div class="gs-ct-sub">GamTech</div>'
      +'<div class="gs-ct-price" data-price="'+pn+'">'+(pn?formatCFA(pn):(window.gsT?window.gsT('ask'):'Prix sur demande'))+'</div>'
      +'<div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div></div>'
      +'<button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button></div>';
    ctItems&&ctItems.insertAdjacentHTML('beforeend',html);
    showCartId();
    recalc();updateBadge();
    if(window.innerWidth>768)openPanel(ct);
    var orig=btn.innerHTML;
    btn.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> '+(window.gsT?window.gsT('added'):'Ajouté');
    btn.style.cssText='background:var(--gr);color:#fff';
    setTimeout(function(){btn.innerHTML=orig;btn.style.cssText='';},1200);
  });

  var applyBtn=qs('#gs-promo-btn');
  if(applyBtn){
    applyBtn.addEventListener('click',function(){
      var code=(qs('#gs-promo-in')||{}).value||'';
      code=code.trim().toUpperCase();
      var msg=qs('#gs-promo-msg');
      if(['TECH10','GAMTECH','SAVE10'].indexOf(code)!==-1){
        if(msg){msg.textContent=window.gsT?window.gsT('promo.ok'):'✓ Remise de 10 % appliquée';msg.style.color='var(--gr)';}
        var m=document.createElement('span');m.id='gs-promo-applied';m.hidden=true;document.body.appendChild(m);
        recalc();applyBtn.textContent=window.gsT?window.gsT('promo.applied'):'OK';applyBtn.style.background='var(--gr)';applyBtn.disabled=true;
      } else {
        if(msg){msg.textContent=code?(window.gsT?window.gsT('promo.bad'):'Code invalide. Essayez TECH10'):(window.gsT?window.gsT('promo.need'):'Entrez un code.');msg.style.color='var(--re)';}
      }
    });
    var pi=qs('#gs-promo-in');pi&&pi.addEventListener('keydown',function(e){if(e.key==='Enter')applyBtn.click();});
  }

  qa('.gs-cat').forEach(function(c){
    c.addEventListener('click',function(){qa('.gs-cat').forEach(function(x){x.classList.remove('active')});c.classList.add('active');});
  });

  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){
      entries.forEach(function(en){if(en.isIntersecting){en.target.classList.add('gs-fade');io.unobserve(en.target);}});
    },{threshold:.08});
    qa('.gs-card,.gs-promo,.gs-ft-card').forEach(function(el){io.observe(el);});
  }

  // Handle hash navigation for menu buttons - show/hide sections
  function handleHashNavigation(hash) {
    if (!hash) return;
    var targetId = hash.replace('#', '');
    
    // Special handling for home/shop
    if (hash === '/' || targetId === 'shop') {
      // Show hero section, hide all others
      showSection('shop');
      updateActiveNav('/');
      closeAll();
      return;
    }
    
    var targetEl = document.getElementById(targetId);
    if (targetEl) {
      // Show only this section, hide all others
      showSection(targetId);
      updateActiveNav(hash);
      closeAll();
    }
  }

  // Show only the target section, hide all others
  function showSection(targetId) {
    var mainEl = qs('.gs-mn');
    if (!mainEl) return;
    
    if (targetId === 'shop') {
      // For home, show all home-related sections
      var hero = qs('.gs-hero-slider');
      var cats = qs('#gs-cats-row');
      var find = qs('#find-model');
      var deals = qs('#deals');
      var arrivals = qs('#arrivals');
      var shopAll = qs('#shop-all');
      
      // Show home sections
      if (hero) hero.style.display = '';
      if (cats) cats.style.display = 'flex';
      if (find) find.style.display = 'flex';
      if (deals) deals.style.display = 'block';
      if (arrivals) arrivals.style.display = 'block';
      if (shopAll) shopAll.style.display = 'none'; // Hide shop-all on home
      
      mainEl.scrollTop = 0;
    } else {
      // Hide home sections
      var hero = qs('.gs-hero-slider');
      var cats = qs('#gs-cats-row');
      var find = qs('#find-model');
      var deals = qs('#deals');
      var arrivals = qs('#arrivals');
      var shopAll = qs('#shop-all');
      
      if (hero) hero.style.display = 'none';
      if (cats) cats.style.display = 'none';
      if (find) find.style.display = 'none';
      if (deals) deals.style.display = 'none';
      if (arrivals) arrivals.style.display = 'none';
      
      // Show the target section
      var targetEl = document.getElementById(targetId);
      if (targetEl) {
        targetEl.style.display = 'flex';
      }
      
      mainEl.scrollTop = 0;
    }
  }

  // Update active state for navigation menu
  function updateActiveNav(hash) {
    qa('.gs-nav-a').forEach(function(link) {
      link.classList.remove('active');
    });
    var activeLink = qs('.gs-nav-a[href="' + hash + '"]');
    if (activeLink) {
      activeLink.classList.add('active');
    } else if (hash === '#shop-all' || hash === '#find-model') {
      // If it's shop-all or find-model, activate the "Toute la boutique" link
      var shopLink = qs('.gs-nav-a[href="#shop-all"]');
      if (shopLink) shopLink.classList.add('active');
    }
  }

  // Listen for hash changes
  window.addEventListener('hashchange', function() {
    handleHashNavigation(window.location.hash);
  });

  // Handle clicks on navigation links
  document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href^="#"]');
    if (link && link.getAttribute('href').length > 1) {
      var hash = link.getAttribute('href');
      handleHashNavigation(hash);
    }
  });

  // Initialize on page load
  if (window.location.hash) {
    setTimeout(function() {
      handleHashNavigation(window.location.hash);
    }, 100);
  } else {
    var homeLink = qs('.gs-nav-a[href="/"]');
    if (homeLink) homeLink.classList.add('active');
    showSection('shop'); // Show home by default
  }

  fetch('/api/hero').then(function(r){return r.ok?r.json():Promise.reject();})
    .catch(function(){return fetch('/hero.json').then(function(r){return r.ok?r.json():null;});})
    .then(function(data){if(data)paintHero(data);initHeroSlider();})
    .catch(function(){initHeroSlider();});

  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.gs-whatsapp-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    var items = qa('.gs-ct-item');
    if (items.length === 0) {
      alert('Your cart is empty!');
      return;
    }
    var name = ((qs('#gs-cart-name')||{}).value||'').trim();
    var err = qs('#gs-cart-name-err');
    if (!name) {
      if (err) err.hidden = false;
      qs('#gs-cart-name')&&qs('#gs-cart-name').focus();
      openPanel(ct);
      return;
    }
    if (err) err.hidden = true;
    try{localStorage.setItem('gs-cart-name',name);}catch(ex){}
    var id = cartId();
    showCartId();
    var orderData = [];
    var total = 0;
    items.forEach(function(item) {
      var itemName = (item.querySelector('.gs-ct-name') || {}).textContent || 'Product';
      var priceEl = item.querySelector('.gs-ct-price');
      var qtyEl = item.querySelector('.gs-qty-n');
      var price = priceEl ? (parseFloat(priceEl.dataset.price) || 0) : 0;
      var qty = qtyEl ? (parseInt(qtyEl.textContent) || 1) : 1;
      total += price * qty;
      orderData.push({ name: itemName, price: price, qty: qty });
    });
    var order = { id: id, name: name, items: orderData, total: total };
    fetch('/api/order', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(order)
    }).catch(function(){});
    var orderPageUrl = window.location.origin + '/order.html?c=' + encodeOrder(order);
    var message = 'New order from ' + name + '\nCart ' + id + '\n\n';
    orderData.forEach(function(item, idx) {
      message += (idx + 1) + '. ' + item.name + '\n';
      message += 'Qty: ' + item.qty + ' x ' + formatCFA(item.price) + ' = ' + formatCFA(item.price * item.qty) + '\n\n';
    });
    message += 'Total: ' + formatCFA(total) + '\n\n';
    message += 'Open cart: ' + orderPageUrl;
    var whatsappNumber = btn.dataset.phone || '22890597003';
    window.location.href = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(message);
  });

  var banner=qs('#gs-cookie');
  var consent='';
  try{consent=localStorage.getItem('gs_cookie')||'';}catch(e){}
  function pingVisit(){
    fetch('/api/visit',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({deviceId:deviceId(),day:new Date().toISOString().slice(0,10)})
    }).catch(function(){});
  }
  if(consent==='yes'){
    if(banner)banner.hidden=true;
    pingVisit();
  } else if(banner){
    banner.hidden=false;
  }
  var acc=qs('#gs-cookie-accept');
  acc&&acc.addEventListener('click',function(){
    try{localStorage.setItem('gs_cookie','yes');}catch(e){}
    deviceId();
    if(banner)banner.hidden=true;
    pingVisit();
  });

  // ═══ THEME TOGGLE (DARK/LIGHT) ═══════════════════════
  var themeTog=qs('#gs-theme-tog');
  var themeLabel=qs('#gs-theme-label');
  var htmlEl=document.documentElement;
  
  function initTheme(){
    var saved=localStorage.getItem('gs-theme')||'dark';
    if(saved==='light'){
      htmlEl.classList.add('gs-light-mode');
      if(themeLabel)themeLabel.textContent='Dark';
    } else {
      htmlEl.classList.remove('gs-light-mode');
      if(themeLabel)themeLabel.textContent='Light';
    }
  }
  
  if(themeTog){
    initTheme();
    themeTog.addEventListener('click',function(){
      var isDark=htmlEl.classList.contains('gs-light-mode');
      if(isDark){
        htmlEl.classList.remove('gs-light-mode');
        localStorage.setItem('gs-theme','dark');
        if(themeLabel)themeLabel.textContent='Light';
      } else {
        htmlEl.classList.add('gs-light-mode');
        localStorage.setItem('gs-theme','light');
        if(themeLabel)themeLabel.textContent='Dark';
      }
    });
  }

  // ═══ LANGUAGE SWITCHER ═══════════════════════════════
  var langTog=qs('#gs-lang-tog');
  var langLabel=qs('#gs-lang-label');
  var langStrings=window.gtLang||{};
  
  function getCurrentLang(){
    var cookie=cookieGet('gt_lang');
    return cookie==='fr'?'fr':'en';
  }
  
  function setLanguage(lang){
    cookieSet('gt_lang',lang,365);
    langLabel.textContent=lang==='fr'?'FR':'EN';
    updatePageText(lang);
  }
  
  function updatePageText(lang){
    var strings=langStrings[lang]||{};
    
    // Update navigation
    qa('a.gs-nav-a').forEach(function(a){
      var text=(a.textContent||'').trim().toLowerCase();
      if(text.indexOf('home')!==-1&&strings.home)a.textContent=strings.home;
      if(text.indexOf('shop all')!==-1&&strings.shop_all)a.textContent=strings.shop_all;
      if(text.indexOf('new')!==-1&&strings.new)a.textContent=strings.new;
      if(text.indexOf('best')!==-1&&strings.popular)a.textContent=strings.popular;
      if(text.indexOf('deal')!==-1&&strings.deals)a.textContent=strings.deals;
    });
    
    // Update categories label
    var catTog=qs('#gs-cats-tog');
    if(catTog&&strings.categories){
      catTog.innerHTML='<span>'+strings.categories+'</span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>';
    }
    
    // Update search placeholder
    var searchInput=qs('.gs-hd-search-input');
    if(searchInput&&strings.search){
      searchInput.placeholder=strings.search;
    }
    
    // Update cart label
    qa('.gs-ct-tog-btn').forEach(function(btn){
      var span=btn.querySelector('.gs-hd-action-main');
      if(span&&strings.cart)span.textContent=strings.cart;
    });
    
    // Update sidebar labels
    var navLbl=qs('.gs-nav-lbl');
    if(navLbl&&strings.menu)navLbl.textContent=strings.menu;
    
    // Update page lang attribute
    document.documentElement.lang=lang==='fr'?'fr':'en';
  }
  
  function initLanguage(){
    var lang=getCurrentLang();
    setLanguage(lang);
  }
  
  if(langTog){
    initLanguage();
    langTog.addEventListener('click',function(e){
      e.stopPropagation();
      var dropdownId='gs-lang-dropdown';
      var existing=qs('#'+dropdownId);
      if(existing){existing.remove();return;}
      
      var currentLang=getCurrentLang();
      var html='<div class="gs-lang-dropdown" id="'+dropdownId+'">'
        +'<button class="gs-lang-option'+(currentLang==='en'?' active':'')+'" data-lang="en">English (EN)</button>'
        +'<button class="gs-lang-option'+(currentLang==='fr'?' active':'')+'" data-lang="fr">Français (FR)</button>'
        +'</div>';
      langTog.insertAdjacentHTML('afterend',html);
      
      var dropdown=qs('#'+dropdownId);
      qa('.gs-lang-option').forEach(function(opt){
        opt.addEventListener('click',function(){
          var lang=opt.dataset.lang;
          qa('.gs-lang-option').forEach(function(o){o.classList.remove('active')});
          opt.classList.add('active');
          setLanguage(lang);
          dropdown.remove();
        });
      });
      
      document.addEventListener('click',function close(e){
        if(!e.target.closest('#'+dropdownId)&&!e.target.closest('#gs-lang-tog')){
          dropdown.remove();
          document.removeEventListener('click',close);
        }
      });
    });
  }

  // ═══ END THEME & LANGUAGE ════════════════════════════
});
})();
