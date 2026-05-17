document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.btn-action').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      btn.classList.toggle('active');
      btn.style.opacity = btn.classList.contains('active')? '0.9' : '1';
    });
  });

  document.querySelectorAll('.panel-h a').forEach(a=>{
    a.addEventListener('click', (e)=>{ e.preventDefault(); alert('Voir tout — fonctionnalité en cours.'); });
  });

  // Simple bar animation (if any .bar-fill exists)
  document.querySelectorAll('.bar-fill').forEach(bar=>{
    const w = bar.style.width || '0%';
    bar.style.width = '0%';
    setTimeout(()=> bar.style.width = w, 50);
  });
});
