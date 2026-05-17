const searchInput = document.getElementById('searchInput');
const statusSelect = document.getElementById('statusSelect');
const demandesList = document.getElementById('demandesList');
const filterBtn = document.getElementById('filterBtn');

function filterDemandes() {
  const query = searchInput.value.trim().toLowerCase();
  const status = statusSelect.value;
  const cards = demandesList.querySelectorAll('.demande-card');
  let visibleCount = 0;

  cards.forEach(card => {
    const title = card.querySelector('h2').textContent.toLowerCase();
    const description = card.querySelector('p').textContent.toLowerCase();
    const matchesSearch = !query || title.includes(query) || description.includes(query);
    const matchesStatus = status === 'all' || card.dataset.status === status;

    const isVisible = matchesSearch && matchesStatus;
    card.style.display = isVisible ? 'block' : 'none';
    if (isVisible) visibleCount++;
  });

  // Show "no results" message if needed
  if (visibleCount === 0) {
    if (!demandesList.querySelector('.no-results')) {
      const noResults = document.createElement('div');
      noResults.className = 'no-results';
      noResults.textContent = 'Aucune demande ne correspond à votre recherche.';
      demandesList.appendChild(noResults);
    }
  } else {
    const noResults = demandesList.querySelector('.no-results');
    if (noResults) noResults.remove();
  }
}

searchInput.addEventListener('input', filterDemandes);
statusSelect.addEventListener('change', filterDemandes);

// Filter button - toggles filter panel visibility
let filtersVisible = false;
filterBtn.addEventListener('click', () => {
  filtersVisible = !filtersVisible;
  filterBtn.classList.toggle('active');
  
  if (filtersVisible) {
    // Highlight active filters
    if (searchInput.value.trim() || statusSelect.value !== 'all') {
      filterBtn.classList.add('has-active-filters');
    }
    console.log('Filtres activés');
  } else {
    filterBtn.classList.remove('has-active-filters');
    console.log('Filtres fermés');
  }
});

// Reset filters button (optional)
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && filtersVisible) {
    filtersVisible = false;
    filterBtn.classList.remove('active');
  }
});
