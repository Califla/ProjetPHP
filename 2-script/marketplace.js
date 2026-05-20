// Marketplace Data - Simulated database
const demandesData = [
  {
    id: 1,
    title: "Aide sur les algorithmes de tri en Python",
    description: "Je cherche quelqu'un pour m'expliquer QuickSort et MergeSort avec des exemples pratiques.",
    tags: ["Python", "Algorithmes", "Débutant"],
    filiere: "DEV",
    level: "Débutant",
    status: "ouvert",
    date: "26/04/2026",
    responses: 1,
    author: "Youssef Benali"
  },
  {
    id: 2,
    title: "Configuration d'un firewall pfSense",
    description: "Besoin d'aide pour configurer les règles de firewall et le NAT sur pfSense.",
    tags: ["Réseau", "Sécurité", "pfSense"],
    filiere: "CYBERSEC",
    level: "Intermédiaire",
    status: "ouvert",
    date: "26/04/2026",
    responses: 0,
    author: "Jean Martin"
  },
  {
    id: 3,
    title: "Debugging REST API avec Node.js",
    description: "Problèmes de performance sur mon API REST, besoin d'optimisation et debugging.",
    tags: ["Node.js", "REST API", "Debugging"],
    filiere: "DEV",
    level: "Intermédiaire",
    status: "ouvert",
    date: "25/04/2026",
    responses: 2,
    author: "Sophie Bernard"
  },
  {
    id: 4,
    title: "Analyse de données avec Pandas",
    description: "Comment optimiser le traitement de grandes données avec Pandas?",
    tags: ["Python", "Pandas", "Data"],
    filiere: "DATA",
    level: "Avancé",
    status: "ouvert",
    date: "24/04/2026",
    responses: 3,
    author: "Alex Chen"
  },
  {
    id: 5,
    title: "Pentest d'une application web",
    description: "Besoin de conseils pour effectuer un pentest complet d'une application web.",
    tags: ["Sécurité", "Pentest", "Web"],
    filiere: "CYBERSEC",
    level: "Avancé",
    status: "fermé",
    date: "23/04/2026",
    responses: 5,
    author: "Thomas Lefevre"
  }
];

const searchInput = document.getElementById('searchInput');
const filterSelects = document.querySelectorAll('.right-group select');
const demandesList = document.querySelector('.demandes-list');
const filterBtn = document.querySelector('.filter-btn');
const pageRole = document.querySelector('main')?.dataset.role || 'stagiaire';
const currentUserName = document.querySelector('.user-pill .user-name')?.textContent.trim() || '';

let currentData = [...demandesData];
let likedDemandes = new Set(JSON.parse(localStorage.getItem('likedDemandes') || '[]'));
let proposedDemandes = new Set(JSON.parse(localStorage.getItem('proposedDemandes') || '[]'));
let reportedDemandes = new Set(JSON.parse(localStorage.getItem('reportedDemandes') || '[]'));

document.addEventListener('DOMContentLoaded', () => {
  if (demandesList) {
    renderCards();
    setupEventListeners();
  }
});

function setupEventListeners() {
  if (searchInput) searchInput.addEventListener('input', filterAndRender);
  filterSelects.forEach(select => {
    select.addEventListener('change', filterAndRender);
  });
  if (filterBtn) {
    filterBtn.addEventListener('click', toggleFilterPanel);
  }
}

function filterAndRender() {
  const searchQuery = searchInput?.value.toLowerCase() || '';
  const filiere = filterSelects[0]?.value || 'all';
  const level = filterSelects[1]?.value || 'all';
  const status = filterSelects[2]?.value || 'all';

  currentData = demandesData.filter(demande => {
    const matchesSearch = !searchQuery || 
      demande.title.toLowerCase().includes(searchQuery) ||
      demande.description.toLowerCase().includes(searchQuery) ||
      demande.tags.some(tag => tag.toLowerCase().includes(searchQuery));

    const matchesFiliere = filiere === 'all' || demande.filiere === filiere;
    const matchesLevel = level === 'all' || demande.level === level;
    const matchesStatus = status === 'all' || demande.status === status;

    return matchesSearch && matchesFiliere && matchesLevel && matchesStatus;
  });

  renderCards();
  updateFilterButton();
}

function renderCards() {
  if (!demandesList) return;
  demandesList.innerHTML = '';

  if (currentData.length === 0) {
    demandesList.innerHTML = `
      <div class="no-results">
        <p>Aucune demande ne correspond à votre recherche.</p>
        <p>Essayez avec d'autres filtres.</p>
      </div>
    `;
    return;
  }

  currentData.forEach(demande => {
    const card = createCard(demande);
    demandesList.appendChild(card);
  });
}

function createCard(demande) {
  const article = document.createElement('article');
  article.className = 'demande-card';
  article.dataset.id = demande.id;

  const isLiked = likedDemandes.has(demande.id);
  const isProposed = proposedDemandes.has(demande.id);
  const canModify = currentUserName && currentUserName === demande.author;

  const shouldShowPropose = pageRole === 'stagiaire' && !canModify;
  const proposeButtonHtml = shouldShowPropose
    ? `<button class="primary-btn propose-btn ${isProposed ? 'proposed' : ''}" data-id="${demande.id}">
        ${isProposed ? '✓ Proposé' : 'Proposer mon aide'}
      </button>`
    : '';
  const modifyButtonHtml = canModify
    ? `<button class="ghost-btn modify-btn" data-id="${demande.id}">Modifier</button>`
    : '';
  const reportButtonHtml = `<button class="ghost-btn report-btn" data-id="${demande.id}">${reportedDemandes.has(demande.id) ? 'Signalé' : 'Signaler'}</button>`;

  // header-right contains only the open/closed badge
  const rightBlockHtml = `
    <div class="header-right">
      <span class="badge-status ${demande.status}">${demande.status === 'ouvert' ? 'Ouvert' : 'Fermé'}</span>
    </div>`;

  article.innerHTML = `
    <div class="card-header">
      <div class="header-info">
        <h2>${demande.title}</h2>
        <p>${demande.description}</p>
        <p style="font-size: 0.85rem; color: #999; margin-top: 8px;">Par : <strong>${demande.author}</strong></p>
      </div>
      ${rightBlockHtml}
    </div>
    <div class="tag-row">
      ${demande.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
    </div>
    <div class="card-footer">
      <div class="footer-left">
        <span>Publié le ${demande.date}</span>
        <span>${demande.responses} réponse${demande.responses !== 1 ? 's' : ''}</span>
      </div>
    </div>
    <div class="action-row">
      ${modifyButtonHtml}
      ${proposeButtonHtml}
      <div class="action-right">${reportButtonHtml}</div>
    </div>
  `;

  if (canModify) article.querySelector('.modify-btn')?.addEventListener('click', () => modifyDemande(demande.id));
  if (shouldShowPropose) {
    article.querySelector('.propose-btn')?.addEventListener('click', () => proposeDemande(demande.id));
  }
  article.querySelector('.report-btn')?.addEventListener('click', () => reportDemande(demande.id));

  return article;
}

function toggleLike(id) {
  if (likedDemandes.has(id)) {
    likedDemandes.delete(id);
  } else {
    likedDemandes.add(id);
  }
  localStorage.setItem('likedDemandes', JSON.stringify(Array.from(likedDemandes)));
  renderCards();
}

function proposeDemande(id) {
  if (pageRole === 'admin') return;
  if (proposedDemandes.has(id)) {
    proposedDemandes.delete(id);
  } else {
    proposedDemandes.add(id);
  }
  localStorage.setItem('proposedDemandes', JSON.stringify(Array.from(proposedDemandes)));

  const demande = demandesData.find(d => d.id === id);
  if (proposedDemandes.has(id) && demande) {
    alert(`✅ Votre aide a été proposée pour "${demande.title}"`);
  }
  renderCards();
}

function reportDemande(id) {
  const demande = demandesData.find(d => d.id === id);
  if (!demande) return;
  if (reportedDemandes.has(id)) {
    alert(`Cette demande a déjà été signalée.`);
    return;
  }

  reportedDemandes.add(id);
  localStorage.setItem('reportedDemandes', JSON.stringify(Array.from(reportedDemandes)));
  alert(`✅ La demande "${demande.title}" a été signalée.`);
  renderCards();
}

function modifyDemande(id) {
  const demande = demandesData.find(d => d.id === id);
  if (!demande) return;
  if (currentUserName !== demande.author) {
    alert('Vous ne pouvez modifier que vos propres demandes.');
    return;
  }

  const newTitle = prompt('Nouveau titre :', demande.title);
  if (newTitle === null) return;
  const newDesc = prompt('Nouvelle description :', demande.description);
  if (newDesc === null) return;
  const newTags = prompt('Tags (séparés par des virgules) :', demande.tags.join(', '));
  if (newTags === null) return;

  demande.title = newTitle.trim();
  demande.description = newDesc.trim();
  demande.tags = newTags.split(',').map(t => t.trim()).filter(t => t);

  renderCards();
}

function toggleFilterPanel() {
  if (filterBtn) filterBtn.classList.toggle('active');
}

function updateFilterButton() {
  const filiere = filterSelects[0]?.value;
  const level = filterSelects[1]?.value;
  const status = filterSelects[2]?.value;
  const searchQuery = searchInput?.value.trim();

  const hasActiveFilters = searchQuery || filiere !== 'all' || level !== 'all' || status !== 'all';
  if (filterBtn) {
    if (hasActiveFilters) {
      filterBtn.classList.add('has-active-filters');
    } else {
      filterBtn.classList.remove('has-active-filters');
    }
  }
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    if (filterBtn?.classList.contains('active')) {
      filterBtn.classList.remove('active');
    }
  }
  if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
    e.preventDefault();
    searchInput?.focus();
  }
});
