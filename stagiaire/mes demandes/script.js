const searchInput = document.getElementById('searchInput');
const statusSelect = document.getElementById('statusSelect');
const demandesList = document.getElementById('demandesList');

function filterDemandes() {
  const query = searchInput.value.trim().toLowerCase();
  const status = statusSelect.value;
  const cards = demandesList.querySelectorAll('.demande-card');

  cards.forEach(card => {
    const title = card.querySelector('h2').textContent.toLowerCase();
    const description = card.querySelector('p').textContent.toLowerCase();
    const matchesSearch = title.includes(query) || description.includes(query);
    const matchesStatus = status === 'all' || card.dataset.status === status;

    card.style.display = matchesSearch && matchesStatus ? 'block' : 'none';
  });
}

searchInput.addEventListener('input', filterDemandes);
statusSelect.addEventListener('change', filterDemandes);

const filterBtn = document.getElementById('filterBtn');
filterBtn.addEventListener('click', () => {
  statusSelect.focus();
});
