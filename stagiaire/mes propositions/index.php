<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  if ($role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
try{
  include('../../database/config.php');

  $total = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE id_user = ?");
  $total->execute([$id_user]);
  $totalCount = $total->fetchColumn();

  $enattente = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE id_user = ? AND `status` = 'en_attente'");
  $enattente->execute([$id_user]);
  $enattenteCount = $enattente->fetchColumn();

  $acceptee = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE id_user = ? AND `status` = 'acceptee'");
  $acceptee->execute([$id_user]);
  $accepteeCount = $acceptee->fetchColumn();

  $refusee = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE id_user = ? AND `status` = 'refusee'");
  $refusee->execute([$id_user]);
  $refuseeCount = $refusee->fetchColumn();

  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

  $sql = "SELECT p.id_proposition, p.status, p.date_prop, d.titre FROM propositions_aide p JOIN aide d ON p.id_demande = d.id_demande WHERE p.id_user = ?";
  $params = [$id_user];

  if ($statusFilter !== 'all') {
    $sql .= " AND p.status = ?";
    $params[] = $statusFilter;
  }

  if (!empty($search)) {
    $sql .= " AND d.titre LIKE ?";
    $params[] = '%' . $search . '%';
  }

  $sql .= " ORDER BY p.date_prop DESC";

  $stmt = $db->prepare($sql);
  $stmt->execute($params);
  $propositions = $stmt->fetchAll(PDO::FETCH_ASSOC);

}catch(PDOException $e){
  echo "Erreur :".$e->getMessage();
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
          <div class="stat-icon-row">
            <svg class="stat-icon" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            <span class="mini-stat-value" id="statTotal"><?php echo $totalCount; ?></span>
          </div>
          <span class="mini-stat-label">Total</span>
        </div>
        <div class="mini-stat">
          <div class="stat-icon-row">
            <svg class="stat-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="mini-stat-value" id="statEnAttente"><?php echo $enattenteCount; ?></span>
          </div>
          <span class="mini-stat-label">En attente</span>
        </div>
        <div class="mini-stat">
          <div class="stat-icon-row">
            <svg class="stat-icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="mini-stat-value" id="statAcceptee"><?php echo $accepteeCount; ?></span>
          </div>
          <span class="mini-stat-label">Acceptée</span>
        </div>
        <div class="mini-stat">
          <div class="stat-icon-row">
            <svg class="stat-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <span class="mini-stat-value" id="statRefusee"><?php echo $refuseeCount; ?></span>
          </div>
          <span class="mini-stat-label">Refusée</span>
        </div>
      </section>

      <form method="GET" action="index.php" style="display:contents;">
      <section class="filters-row">
        <div class="search-group">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input name="search" type="search" placeholder="Rechercher une proposition..." value="<?php echo htmlspecialchars($search); ?>" />
        </div>
        <select name="status">
          <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tous les statuts</option>
          <option value="en_attente" <?php echo $statusFilter === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
          <option value="acceptee" <?php echo $statusFilter === 'acceptee' ? 'selected' : ''; ?>>Acceptée</option>
          <option value="refusee" <?php echo $statusFilter === 'refusee' ? 'selected' : ''; ?>>Refusée</option>
        </select>
        <button type="submit" class="filter-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
          Filtrer
        </button>
      </section>
      </form>
      <div class="proposals-list" id="proposalsList">
        <?php if (count($propositions) === 0): ?>
          <div class="empty-state">
            <?php if (!empty($search) || $statusFilter !== 'all'): ?>
              Aucune proposition ne correspond à votre recherche.
            <?php else: ?>
              Vous n'avez encore fait aucune proposition d'aide.
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php foreach ($propositions as $prop): ?>
          <article class="proposal-card">
            <div class="proposal-info">
              <h3><?php echo htmlspecialchars($prop['titre']); ?></h3>
              <div class="proposal-meta">
                <span class="proposal-date">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  <?php echo htmlspecialchars($prop['date_prop']); ?>
                </span>
              </div>
            </div>
            <span class="proposal-badge <?php echo htmlspecialchars($prop['status']); ?>"><?php echo htmlspecialchars($prop['status']); ?></span>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>