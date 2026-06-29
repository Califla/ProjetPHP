<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
if (isset($_SESSION)){
    extract($_SESSION);
}
try{
    include('../../database/config.php');
    # Récupérer les publications signalées
    $req = $db->prepare("SELECT p.*, u.nom, u.prenom FROM aide p JOIN utilisateurs u ON p.id_user = u.id_user WHERE p.signal > 0 ORDER BY p.date_pub DESC");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modération</title>
  <link rel="stylesheet" href="../../1-css/style.css?v=2" />
  <link rel="stylesheet" href="mode.css?v=2" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../Tableaubord/table.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../Utulisateurs/utili.php'">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </div>
        <div class="nav-item" onclick="location.href='../Badges/badges.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item active" onclick="location.href='mode.php'">
          <span class="nav-icon">🛡️</span>
          <span>Modération</span>
        </div>
        <div class="nav-item" onclick="location.href='../Statistiques/stat.php'">
          <span class="nav-icon">📊</span>
          <span>Statistiques</span>
        </div>
        <div class="nav-item" onclick="location.href='../Marketplace/market.php'">
          <span class="nav-icon">🛒</span>
          <span>Marketplace</span>
        </div>
      </nav>
    </aside>

    <header class="header">
      <form class="header-search" action="../../search.php" method="GET">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input name="q" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
      </form>
      <div class="user-pill" data-email="<?php echo $email; ?>">
        <div class="user-info">
          <div class="user-name"><?php echo $nom . " " . $prenom; ?></div>
          <div class="user-role"><?php echo $role ?></div>
        </div>
        <?php
        if ($photo) {
          echo '<img class="user-avatar" src="../../pagelogin/photo/' . $photo . '" alt="Photo de profil">';
        } else {
          echo '<div class="user-avatar">' . substr($nom, 0, 1) . substr($prenom, 0, 1) . '</div>';
        }
        ?>
    </header>

    <main class="content-area">
      <div class="page-header">
        <div>
          <h1>Modération</h1>
          <p class="page-subtitle">Examinez les publications signalées par la communauté</p>
        </div>
      </div>

      <div class="stats-grid">
        <article class="stat-card">
          <span class="stat-label">Publications signalées</span>
          <strong><?php echo count($result); ?></strong>
        </article>
      </div>
      <div class="moderation-items">
        <?php if (count($result) == 0): ?>
          <div style="text-align:center;padding:24px;color:#94a3b8;">Aucune publication signalée</div>
        <?php else: ?>
        <?php foreach ($result as $row): ?>
          <article class="moderation-card">
            <div class="card-header">
              <div class="warning-icon">⚠️</div>
              <div class="publication-info">
                <h2><?php echo $row['titre']; ?></h2>
              </div>
              <div class="user-meta">
                <?php
                  $initial = strtoupper(substr($row['nom'] ?? '?', 0, 1) . substr($row['prenom'] ?? '?', 0, 1));
                ?>
                <div class="user-avatar"><?= $initial ?></div>
                <div class="user-details">
                  <span class="user-name card-user-name"><?= htmlspecialchars(($row['nom'] ?? 'Inconnu') . ' ' . ($row['prenom'] ?? '')) ?></span>
                  <span class="date"><?= $row['date_pub'] ?></span>
                </div>
              </div>
              <div class="report-count">
                <div class="count"><?php echo $row['signal']; ?></div>
                <div class="label">signalements</div>
              </div>
            </div>
          <div class="content-body">
            <p><?php echo htmlspecialchars($row['description']); ?></p>
          </div>

          <div class="card-actions">
            <a href="garder.php?id=<?php echo $row['id_demande']; ?>" class="btn btn-keep">✓ Garder</a>
            <a href="supprimer.php?id=<?php echo $row['id_demande']; ?>" class="btn btn-delete">🗑️ Supprimer</a>
          </div>
        </article>
        <?php endforeach; ?>
        <?php endif; ?>
        
      </div>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>