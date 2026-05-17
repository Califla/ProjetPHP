const searchUsers = [
  { nom: "Ahmed Idrissi", role: "stagiaire", filiere: "DEV", avatar: "AI" },
  { nom: "Marie Dupont", role: "stagiaire", filiere: "DEV", avatar: "MD" },
  { nom: "Jean Martin", role: "stagiaire", filiere: "CYBERSEC", avatar: "JM" },
  { nom: "Sophie Bernard", role: "stagiaire", filiere: "DEV", avatar: "SB" },
  { nom: "Alex Chen", role: "stagiaire", filiere: "DATA", avatar: "AC" },
  { nom: "Thomas Lefevre", role: "stagiaire", filiere: "CYBERSEC", avatar: "TL" },
  { nom: "Lafhal Jouariya", role: "formateur", filiere: null, avatar: "LJ" },
];

document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('globalSearch');
  if (!searchInput) return;

  const searchContainer = document.createElement('div');
  searchContainer.style.position = 'relative';
  searchContainer.style.display = 'contents';
  searchInput.parentNode.insertBefore(searchContainer, searchInput);
  searchContainer.appendChild(searchInput);

  const dropdown = document.createElement('div');
  dropdown.className = 'search-dropdown';
  dropdown.style.display = 'none';
  searchContainer.appendChild(dropdown);

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim().toLowerCase();
    if (!query) {
      dropdown.style.display = 'none';
      return;
    }

    const results = searchUsers.filter(u =>
      u.nom.toLowerCase().includes(query) ||
      u.role.toLowerCase().includes(query) ||
      (u.filiere && u.filiere.toLowerCase().includes(query))
    );

    if (results.length === 0) {
      dropdown.innerHTML = '<div class="search-dropdown-item" style="color:#94a3b8;cursor:default">Aucun résultat</div>';
      dropdown.style.display = 'block';
      return;
    }

    dropdown.innerHTML = results.map(u => `
      <div class="search-dropdown-item" data-user="${u.nom}">
        <span class="search-dropdown-avatar">${u.avatar}</span>
        <span class="search-dropdown-info">
          <strong>${u.nom}</strong>
          <small>${u.role}${u.filiere ? ' — ' + u.filiere : ''}</small>
        </span>
      </div>
    `).join('');

    dropdown.style.display = 'block';

    dropdown.querySelectorAll('.search-dropdown-item').forEach(item => {
      item.addEventListener('click', () => {
        searchInput.value = item.dataset.user;
        dropdown.style.display = 'none';
      });
    });
  });

  document.addEventListener('click', (e) => {
    if (!searchContainer.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') dropdown.style.display = 'none';
  });
});
