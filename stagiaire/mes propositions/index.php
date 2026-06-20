<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  if ($role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Mes propositions</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/index.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../mes demandes/index.php'">
          <span class="nav-icon">📋</span>
          <span>Mes demandes</span>
        </div>
        <div class="nav-item" onclick="location.href='../competences/index.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <div class="nav-item active" onclick="location.href='index.php'">
          <span class="nav-icon">🤝</span>
          <span>Mes propositions</span>
        </div>
        <div class="nav-item" onclick="location.href='../badges/index.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item" onclick="location.href='../passeport/index.php'">
          <span class="nav-icon">👤</span>
          <span>Passeport</span>
        </div>
        <div class="nav-item" onclick="location.href='../marketplace/index.php'">
          <span class="nav-icon">🛒</span>
          <span>Marketplace</span>
        </div>
      </nav>
    </aside>

    <header class="header">
      <form class="header-search" action="#" onsubmit="return false;">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input id="globalSearch" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
      </form>
      <div class="user-pill" data-email="<?php echo $email; ?>">
        <div class="user-info">

          <div class="user-name"><?php echo $nom . " " . $prenom; ?></div>
          <div class="user-role"><?php echo $role; ?></div>
        </div>
        <?php
        if ($photo) {
          echo '<img class="user-avatar" src="../../pagelogin/photo/' . $photo . '" alt="Photo de profil">';
        } else {
          echo '<div class="user-avatar">' . substr($nom, 0, 1) . substr($prenom, 0, 1) . '</div>';
        }
        ?>
      </div>
    </header>

    <main class="main">
      <div class="page-title">Mes propositions</div>
      <div class="page-sub">Suivez l'état de vos propositions d'aide</div>

      <section class="stats-row" id="statsRow">
        <div class="mini-stat">
          <span class="mini-stat-value" id="statTotal">0</span>
          <span class="mini-stat-label">Total</span>
        </div>
        <div class="mini-stat">
          <span class="mini-stat-value" id="statEnAttente">0</span>
          <span class="mini-stat-label">En attente</span>
        </div>
        <div class="mini-stat">
          <span class="mini-stat-value" id="statAcceptee">0</span>
          <span class="mini-stat-label">Acceptée</span>
        </div>
        <div class="mini-stat">
          <span class="mini-stat-value" id="statRefusee">0</span>
          <span class="mini-stat-label">Refusée</span>
        </div>
      </section>

      <section class="filters-row">
        <div class="search-group">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input id="searchInput" type="search" placeholder="Rechercher une proposition..." />
        </div>
        <select id="statusFilter">
          <option value="all">Tous les statuts</option>
          <option value="en_attente">En attente</option>
          <option value="acceptee">Acceptée</option>
          <option value="refusee">Refusée</option>
        </select>
      </section>

      <div class="proposals-list" id="proposalsList"></div>
    </main>
  </div>

  <script>
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const proposalsList = document.getElementById('proposalsList');

    function getProposals() {
      const details = JSON.parse(localStorage.getItem('proposalsDetails') || '[]');
      const proposed = new Set(JSON.parse(localStorage.getItem('proposedDemandes') || '[]'));
      return details.filter(p => proposed.has(p.id));
    }

    function render() {
      const query = searchInput.value.trim().toLowerCase();
      const status = statusFilter.value;

      let proposals = getProposals();

      if (query) {
        proposals = proposals.filter(p => p.title.toLowerCase().includes(query));
      }
      if (status !== 'all') {
        proposals = proposals.filter(p => p.status === status);
      }

      // Stats
      const all = getProposals();
      document.getElementById('statTotal').textContent = all.length;
      document.getElementById('statEnAttente').textContent = all.filter(p => p.status === 'en_attente').length;
      document.getElementById('statAcceptee').textContent = all.filter(p => p.status === 'acceptee').length;
      document.getElementById('statRefusee').textContent = all.filter(p => p.status === 'refusee').length;

      // List
      if (proposals.length === 0) {
        proposalsList.innerHTML = '<div class="empty-state">Aucune proposition trouvée.</div>';
        return;
      }

      const labels = {
        en_attente: { text: 'En attente', cls: 'en_attente' },
        acceptee: { text: 'Acceptée', cls: 'acceptee' },
        refusee: { text: 'Refusée', cls: 'refusee' }
      };

      proposalsList.innerHTML = proposals.map(p => `
        <article class="proposal-card">
          <div class="proposal-info">
            <h3>${p.title}</h3>
            <span class="proposal-date">Proposé le ${p.date}</span>
          </div>
          <span class="proposal-badge ${labels[p.status].cls}">${labels[p.status].text}</span>
        </article>
      `).join('');
    }

    searchInput.addEventListener('input', render);
    statusFilter.addEventListener('change', render);
    render();
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>