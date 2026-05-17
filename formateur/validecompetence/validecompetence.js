document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.validation-container');
  const stats = document.querySelectorAll('.stat-v-num');
  function toNum(el){ return parseInt(el.textContent)||0; }
  function updateStats(pendingDelta=0, validatedDelta=0, refusedDelta=0){
    if(!stats || stats.length<3) return;
    stats[0].textContent = Math.max(0, toNum(stats[0]) + pendingDelta);
    stats[1].textContent = Math.max(0, toNum(stats[1]) + validatedDelta);
    stats[2].textContent = Math.max(0, toNum(stats[2]) + refusedDelta);
  }

  if(!container) return;
  container.addEventListener('click', (e)=>{
    const btn = e.target.closest('button');
    if(!btn) return;
    const card = btn.closest('.v-card-item');
    if(btn.classList.contains('v-btn-validate')){
      if(!card) return;
      updateStats(-1, 1, 0);
      card.remove();
    } else if(btn.classList.contains('v-btn-refuse')){
      if(!card) return;
      updateStats(-1, 0, 1);
      card.remove();
    }
  });
});
