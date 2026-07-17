(function(){
'use strict';
var qs=function(s,c){return(c||document).querySelector(s)};
var qa=function(s,c){return Array.from((c||document).querySelectorAll(s))};

document.addEventListener('DOMContentLoaded',function(){
  var sb=qs('#gs-sb'), ct=qs('#gs-ct'), ov=qs('#gs-ov');

  function openPanel(el){el.classList.add('open');ov&&ov.classList.add('on');}
  function closeAll(){sb&&sb.classList.remove('open');ct&&ct.classList.remove('open');ov&&ov.classList.remove('on');}

  var sbTog=qs('#gs-sb-tog'), ctClose=qs('#gs-ct-close');
  /* all cart toggle buttons (desktop header + mobile top bar) */
  qa('.gs-ct-tog-btn').forEach(function(btn){btn.addEventListener('click',function(){openPanel(ct);});});
  sbTog&&sbTog.addEventListener('click',function(){openPanel(sb);});
  ctClose&&ctClose.addEventListener('click',closeAll);
  ov&&ov.addEventListener('click',closeAll);

  /* categories toggle in sidebar */
  var catsTog=qs('#gs-cats-tog'), catsList=qs('#gs-cats-list');
  if(catsTog&&catsList){
    catsTog.addEventListener('click',function(){
      var open=catsTog.classList.toggle('open');
      catsList.style.display=open?'flex':'none';
    });
  }

  /* qty + delete */
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
    var s=qs('#gs-sub');if(s)s.textContent='$'+sub.toFixed(2);
    var d=qs('#gs-disc');if(d)d.textContent=disc>0?'-$'+disc.toFixed(2):'$0.00';
    var t=qs('#gs-tot');if(t)t.textContent='$'+tot.toFixed(2);
  }

  function updateBadge(){
    var n=qa('.gs-ct-item').length;
    var b1=qs('#gs-badge-hd');if(b1)b1.textContent=n;
    var b2=qs('#gs-badge-mob');if(b2)b2.textContent=n;
    var bc=qs('#gs-ct-cnt');if(bc)bc.textContent=n;
  }

  /* add to cart */
  document.addEventListener('click',function(e){
    var btn=e.target.closest('.gs-add-btn');
    if(!btn)return;
    e.preventDefault(); // Prevent page navigation
    var card=btn.closest('.gs-card');if(!card)return;
    var name=(card.querySelector('.gs-card-name')||{}).textContent||'Product';
    var pn=parseFloat(btn.dataset.price)||0;
    var img=btn.dataset.img||'';
    var productId=btn.dataset.id||'';
    var html='<div class="gs-ct-item" style="animation:gsfade .3s ease" data-id="'+productId+'">'
      +(img?'<img class="gs-ct-thumb" src="'+img+'" alt="">':'<div class="gs-ct-thumb-ph"><svg width="18" height="18" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>')
      +'<div class="gs-ct-info"><div class="gs-ct-name">'+name.replace(/</g,'&lt;')+'</div><div class="gs-ct-sub">GamTech</div>'
      +'<div class="gs-ct-price" data-price="'+pn.toFixed(2)+'">$'+pn.toFixed(2)+'</div>'
      +'<div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div></div>'
      +'<button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button></div>';
    ctItems&&ctItems.insertAdjacentHTML('beforeend',html);
    recalc();updateBadge();
    if(window.innerWidth>768)openPanel(ct);
    var orig=btn.innerHTML;
    btn.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Added!';
    btn.style.cssText='background:var(--gr);color:#fff';
    setTimeout(function(){btn.innerHTML=orig;btn.style.cssText='';},1200);
  });

  /* promo */
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

  /* category active state */
  qa('.gs-cat').forEach(function(c){
    c.addEventListener('click',function(){qa('.gs-cat').forEach(function(x){x.classList.remove('active')});c.classList.add('active');});
  });

  /* scroll-in fade */
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){
      entries.forEach(function(en){if(en.isIntersecting){en.target.classList.add('gs-fade');io.unobserve(en.target);}});
    },{threshold:.08});
    qa('.gs-card,.gs-promo,.gs-ft-card').forEach(function(el){io.observe(el);});
  }

  /* product tabs */
  qa('.gs-tab-btn').forEach(function(btn){
    btn.addEventListener('click',function(){
      qa('.gs-tab-btn').forEach(function(b){b.classList.remove('active');});
      qa('.gs-tab-panel').forEach(function(p){p.classList.remove('active');});
      btn.classList.add('active');
      var panel=qs('#gs-tab-'+btn.dataset.tab);
      if(panel)panel.classList.add('active');
    });
  });

  /* hero slider auto-slide */
  var slides=qa('.gs-hero-slide'), dots=qa('.gs-hero-dot'), cur=0;
  function showSlide(i){
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

  /* WhatsApp Checkout - Multiple Numbers */
  console.log('WhatsApp: Setting up checkout handlers');
  
  function formatCartMessage(cart){
    var lines=["🛒 New Order:", ""];
    var total=0;
    cart.forEach(function(item,i){
      var lineTotal=item.price*item.quantity;
      total+=lineTotal;
      lines.push((i+1)+". "+item.name+" x"+item.qty+" — $"+lineTotal.toFixed(2));
    });
    lines.push("");
    lines.push("Total: $"+total.toFixed(2));
    return lines.join("\n");
  }
  
  function sendCartToWhatsApp(cart,phone){
    if(!cart||cart.length===0){
      alert("Your cart is empty!");
      return;
    }
    var message=formatCartMessage(cart);
    var encodedMessage=encodeURIComponent(message);
    var url="https://wa.me/"+phone+"?text="+encodedMessage;
    console.log("WhatsApp: Opening", url);
    window.location.href=url;
  }
  
  function getCartItems(){
    var items=qa('.gs-ct-item');
    var cart=[];
    items.forEach(function(item){
      var name=(item.querySelector('.gs-ct-name')||{}).textContent||'Product';
      var priceEl=item.querySelector('.gs-ct-price');
      var qtyEl=item.querySelector('.gs-qty-n');
      var price=priceEl?(parseFloat(priceEl.dataset.price)||0):0;
      var qty=qtyEl?(parseInt(qtyEl.textContent)||1):1;
      cart.push({name:name,price:price,qty:qty});
    });
    return cart;
  }

  qa('.gs-whatsapp-btn').forEach(function(btn){
    btn.addEventListener('click',function(e){
      e.preventDefault();
      e.stopPropagation();
      var phone=btn.dataset.phone;
      var cart=getCartItems();
      sendCartToWhatsApp(cart,phone);
    });
  });
});
})();