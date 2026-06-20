document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.badge-item-row .btn-outline').forEach(btn => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.badge-item-row');
      const form = document.querySelector('.action-panel');
      const badgeSelect = document.querySelector('select[name="id_badge"]');
      if (badgeSelect) badgeSelect.value = row.dataset.idBadge;
      if (form) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });

  const stagiaireInput = document.getElementById('stagiaire-input');
  const idUserHidden = document.getElementById('id_user_hidden');
  if (!stagiaireInput || !idUserHidden) return;

  function resolveStagiaire() {
    const val = stagiaireInput.value.trim();
    let found = '';
    document.querySelectorAll('#stagiaires-list option').forEach(o => {
      if (o.value === val) found = o.getAttribute('data-id');
    });
    idUserHidden.value = found;
  }

  stagiaireInput.addEventListener('input', resolveStagiaire);
  stagiaireInput.addEventListener('change', resolveStagiaire);

  document.querySelector('.action-panel')?.addEventListener('submit', e => {
    resolveStagiaire();
    if (!idUserHidden.value) { e.preventDefault(); stagiaireInput.focus(); }
  });
});
