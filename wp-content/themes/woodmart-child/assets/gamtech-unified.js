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
  el.textContent='Cart '+cartId();
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

  function openPanel(el){el.classList.add('open');ov&&ov.classList.add('on');}
  function closeAll(){sb&&sb.classList.remove('open');ct&&ct.classList.remove('open');ov&&ov.classList.remove('on');}

  var sbTog=qs('#gs-sb-tog'), ctClose=qs('#gs-ct-close');
  qa('.gs-ct-tog-btn').forEach(function(btn){btn.addEventListener('click',function(){openPanel(ct);});});
  sbTog&&sbTog.addEventListener('click',function(){openPanel(sb);});
  ctClose&&ctClose.addEventListener('click',closeAll);
  ov&&ov.addEventListener('click',closeAll);

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
      +'<div class="gs-ct-price" data-price="'+pn+'">'+(pn?formatCFA(pn):'Ask for price')+'</div>'
      +'<div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div></div>'
      +'<button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button></div>';
    ctItems&&ctItems.insertAdjacentHTML('beforeend',html);
    showCartId();
    recalc();updateBadge();
    if(window.innerWidth>768)openPanel(ct);
    var orig=btn.innerHTML;
    btn.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Added!';
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
        if(msg){msg.textContent='✓ 10% discount applied!';msg.style.color='var(--gr)';}
        var m=document.createElement('span');m.id='gs-promo-applied';m.hidden=true;document.body.appendChild(m);
        recalc();applyBtn.textContent='Applied';applyBtn.style.background='var(--gr)';applyBtn.disabled=true;
      } else {
        if(msg){msg.textContent=code?'Invalid code. Try TECH10':'Enter a code.';msg.style.color='var(--re)';}
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
});
})();
