document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.badge-item-row .btn-outline').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const row = btn.closest('.badge-item-row');
      const statNum = row.querySelector('.stat-num');
      statNum.textContent = (parseInt(statNum.textContent)||0) + 1;

      const historyContainer = document.querySelector('.history-panel');
      if(historyContainer){
        const badgeIcon = row.querySelector('.badge-icon-box')?.textContent || '';
        const badgeName = row.querySelector('h3')?.textContent || 'Badge';
        const newItem = document.createElement('div');
        newItem.className = 'history-item';
        newItem.innerHTML = `<div class="history-icon">${badgeIcon}</div><div class="history-info"><strong>Vous</strong><span>${badgeName} • maintenant</span></div>`;
        historyContainer.appendChild(newItem);
      }

      // tiny feedback
      btn.textContent = 'Attribué';
      btn.disabled = true;
    });
  });
});
