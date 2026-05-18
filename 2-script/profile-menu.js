const extraMenuItems = [
  { label: 'Mon Profil', icon: '👤', href: '#' },
  { label: 'Paramètres', icon: '⚙️', href: '#' },
  { label: 'Centre d\'aide', icon: '❓', href: '#' },
  { type: 'separator' },
  { label: 'Déconnexion', icon: '🚪', href: '#', danger: true },
];

document.addEventListener('DOMContentLoaded', () => {
  const userPill = document.querySelector('.user-pill');
  if (!userPill) return;

  const wrapper = document.createElement('div');
  wrapper.className = 'profile-wrapper';
  userPill.parentNode.insertBefore(wrapper, userPill);
  wrapper.appendChild(userPill);

  const menu = document.createElement('div');
  menu.className = 'profile-menu';
  menu.style.display = 'none';
  wrapper.appendChild(menu);

  const name = userPill.querySelector('.user-name')?.textContent || '';
  const role = userPill.querySelector('.user-role')?.textContent || '';
  const email = userPill.dataset.email || '';
  const avatar = userPill.querySelector('.user-avatar')?.textContent || '?';

  const headerHTML = `
    <div class="profile-menu-header">
      <span class="profile-menu-avatar">${avatar}</span>
      <div class="profile-menu-user">
        <div class="profile-menu-name">${name}</div>
        <div class="profile-menu-email">${email}</div>
        <div class="profile-menu-role">${role}</div>
      </div>
    </div>
  `;

  const itemsHTML = extraMenuItems.map(item => {
    if (item.type === 'separator') return '<div class="profile-menu-sep"></div>';
    const cls = item.danger ? 'profile-menu-item danger' : 'profile-menu-item';
    return `<a class="${cls}" href="${item.href}"><span class="profile-menu-icon">${item.icon}</span>${item.label}</a>`;
  }).join('');

  menu.innerHTML = headerHTML + itemsHTML;

  userPill.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = menu.style.display === 'block';
    closeAllMenus();
    if (!isOpen) {
      menu.style.display = 'block';
    }
  });

  document.addEventListener('click', closeAllMenus);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllMenus();
  });

  function closeAllMenus() {
    menu.style.display = 'none';
  }
});
