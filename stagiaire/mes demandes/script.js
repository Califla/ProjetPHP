const searchInput = document.getElementById('searchInput');
const statusSelect = document.getElementById('statusSelect');
const demandesList = document.getElementById('demandesList');
const filterBtn = document.getElementById('filterBtn');
const modal = document.getElementById('ratingModal');
const closeModal = document.getElementById('closeModal');
const stars = document.querySelectorAll('.star');
const ratingLabel = document.getElementById('ratingLabel');
const submitBtn = document.getElementById('submitRating');
const modalMentor = document.getElementById('modalMentorName');
const commentInput = document.getElementById('ratingComment');

let selectedRating = 0;
let currentMentor = '';

function getRatings() {
  return JSON.parse(localStorage.getItem('mentorRatings') || '{}');
}

function saveRating(mentor, rating, comment) {
  const ratings = getRatings();
  if (!ratings[mentor]) ratings[mentor] = [];
  ratings[mentor].push({ rating, comment, date: new Date().toLocaleDateString() });
  localStorage.setItem('mentorRatings', JSON.stringify(ratings));
}

function getAverageRating(mentor) {
  const ratings = getRatings();
  const list = ratings[mentor];
  if (!list || list.length === 0) return null;
  const sum = list.reduce((acc, r) => acc + r.rating, 0);
  return (sum / list.length).toFixed(1);
}

function getRatingCount(mentor) {
  const ratings = getRatings();
  return ratings[mentor] ? ratings[mentor].length : 0;
}

document.querySelectorAll('.rating-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    currentMentor = btn.dataset.mentor;
    modalMentor.textContent = currentMentor;
    selectedRating = 0;
    stars.forEach(s => s.textContent = '☆');
    ratingLabel.textContent = 'Sélectionnez une note';
    commentInput.value = '';
    modal.style.display = 'flex';
  });
});

closeModal.addEventListener('click', () => {
  modal.style.display = 'none';
});

modal.addEventListener('click', (e) => {
  if (e.target === modal) modal.style.display = 'none';
});

stars.forEach(star => {
  star.addEventListener('mouseenter', () => {
    const val = parseInt(star.dataset.value);
    stars.forEach((s, i) => {
      s.textContent = i < val ? '★' : '☆';
    });
  });

  star.addEventListener('mouseleave', () => {
    stars.forEach((s, i) => {
      s.textContent = i < selectedRating ? '★' : '☆';
    });
  });

  star.addEventListener('click', () => {
    selectedRating = parseInt(star.dataset.value);
    stars.forEach((s, i) => {
      s.textContent = i < selectedRating ? '★' : '☆';
    });
    const labels = ['', 'Très insuffisant', 'Insuffisant', 'Moyen', 'Bien', 'Excellent'];
    ratingLabel.textContent = labels[selectedRating];
  });
});

submitBtn.addEventListener('click', () => {
  if (selectedRating === 0) {
    ratingLabel.textContent = 'Veuillez sélectionner une note';
    return;
  }
  saveRating(currentMentor, selectedRating, commentInput.value.trim());
  modal.style.display = 'none';
  const btn = document.querySelector(`.rating-btn[data-mentor="${currentMentor}"]`);
  if (btn) {
    btn.textContent = '✅ Noté';
    btn.disabled = true;
    btn.style.opacity = '0.6';
  }
});

document.querySelectorAll('.rating-btn').forEach(btn => {
  const mentor = btn.dataset.mentor;
  const ratings = getRatings();
  if (ratings[mentor] && ratings[mentor].length > 0) {
    btn.textContent = '✅ Noté';
    btn.disabled = true;
    btn.style.opacity = '0.6';
  }
});

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

let filtersVisible = false;
filterBtn.addEventListener('click', () => {
  filtersVisible = !filtersVisible;
  filterBtn.classList.toggle('active');

  if (filtersVisible) {
    if (searchInput.value.trim() || statusSelect.value !== 'all') {
      filterBtn.classList.add('has-active-filters');
    }
  } else {
    filterBtn.classList.remove('has-active-filters');
  }
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && filtersVisible) {
    filtersVisible = false;
    filterBtn.classList.remove('active');
  }
  if (e.key === 'Escape' && modal.style.display === 'flex') {
    modal.style.display = 'none';
  }
});
