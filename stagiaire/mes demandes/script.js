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

// ── Status change ──────────────────────────────────────────────
function getDemandeStatuses() {
  return JSON.parse(localStorage.getItem('demandeStatuses') || '{}');
}

function toggleRatingBtn(card) {
  const ratingBtn = card.querySelector('.rating-btn');
  if (!ratingBtn) return;
  ratingBtn.style.display = card.dataset.status === 'resolu' ? '' : 'none';
}

function initStatusSelects() {
  const saved = getDemandeStatuses();
  document.querySelectorAll('.status-select').forEach(select => {
    const card = select.closest('.demande-card');
    const title = card.querySelector('h2').textContent;

    if (saved[title]) {
      card.dataset.status = saved[title];
    }
    select.value = card.dataset.status;
    updateBadge(card);
    toggleRatingBtn(card);

    select.addEventListener('change', () => {
      const newStatus = select.value;
      card.dataset.status = newStatus;
      const saved2 = getDemandeStatuses();
      saved2[title] = newStatus;
      localStorage.setItem('demandeStatuses', JSON.stringify(saved2));
      updateBadge(card);
      toggleRatingBtn(card);
    });
  });
}

function updateBadge(card) {
  const badge = card.querySelector('.badge-status');
  if (!badge) return;
  const status = card.dataset.status;
  const labels = { ouvert: 'Ouvert', resolu: 'Résolu', ferme: 'Fermé' };
  badge.className = 'badge-status ' + status;
  badge.textContent = labels[status] || status;
}

initStatusSelects();

// Seed demo proposals
(function seedProposals() {
  const existing = JSON.parse(localStorage.getItem('proposalsDetails') || '[]');
  const proposed = new Set(JSON.parse(localStorage.getItem('proposedDemandes') || '[]'));
  const demo = [
    { id: 99, title: 'Aide sur React Hooks useEffect', date: '24/04/2026', status: 'en_attente', proposer: 'Youssef Benali' },
    { id: 98, title: 'Aide sur React Hooks useEffect', date: '24/04/2026', status: 'en_attente', proposer: 'Karim Benali' },
    { id: 97, title: 'Problème avec Git merge conflicts', date: '26/04/2026', status: 'en_attente', proposer: 'Sara El Fassi' },
  ];
  let changed = false;
  demo.forEach(d => {
    if (!existing.find(e => e.title === d.title && e.proposer === d.proposer)) {
      existing.push(d);
      proposed.add(d.id);
      changed = true;
    }
  });
  if (changed) {
    localStorage.setItem('proposalsDetails', JSON.stringify(existing));
    localStorage.setItem('proposedDemandes', JSON.stringify(Array.from(proposed)));
  }
})();

renderProposals();

// ── Proposals (accept/refuse) ──────────────────────────────────
function renderProposals() {
  const proposals = JSON.parse(localStorage.getItem('proposalsDetails') || '[]');

  document.querySelectorAll('.proposals-section').forEach(section => {
    const card = section.closest('.demande-card');
    const title = card.querySelector('h2').textContent;
    const cardProposals = proposals.filter(p => p.title === title);

    if (cardProposals.length === 0) {
      section.innerHTML = '';
      return;
    }

    section.innerHTML = '<div class="proposals-title">Propositions reçues :</div>' +
      cardProposals.map(p => {
        const labels = { en_attente: 'En attente', acceptee: 'Acceptée', refusee: 'Refusée' };
        const btns = p.status === 'en_attente'
          ? `<button class="accept-btn" data-proposer="${p.proposer}">✓ Accepter</button>
             <button class="refuse-btn" data-proposer="${p.proposer}">✗ Refuser</button>`
          : '';
        return `
          <div class="proposal-item" data-proposer="${p.proposer}">
            <a href="../../public_profile.html?name=${encodeURIComponent(p.proposer)}" class="proposal-proposer">${p.proposer}</a>
            <span class="proposal-badge ${p.status}">${labels[p.status]}</span>
            <div class="proposal-actions">${btns}</div>
          </div>
        `;
      }).join('');
  });

  document.querySelectorAll('.accept-btn').forEach(btn => {
    btn.addEventListener('click', (e) => updateProposalStatus(e, 'acceptee'));
  });
  document.querySelectorAll('.refuse-btn').forEach(btn => {
    btn.addEventListener('click', (e) => updateProposalStatus(e, 'refusee'));
  });
}

function updateProposalStatus(e, newStatus) {
  const btn = e.currentTarget;
  const card = btn.closest('.demande-card');
  if (!card) return;
  const title = card.querySelector('h2').textContent;
  const proposer = btn.dataset.proposer;
  const proposals = JSON.parse(localStorage.getItem('proposalsDetails') || '[]');
  const p = proposals.find(p => p.title === title && p.proposer === proposer);
  if (p) {
    p.status = newStatus;
    localStorage.setItem('proposalsDetails', JSON.stringify(proposals));
    renderProposals();
  }
}
