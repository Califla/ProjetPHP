<?php
session_start();
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$users = [];
if ($q !== '') {
  try {
    include 'database/config.php';
    $stmt = $db->prepare("SELECT id_user, nom, prenom, email, role, filiere, photo FROM utilisateurs WHERE role != 'admin' and `statut` !='suspendu' AND (CONCAT(nom, ' ', prenom) LIKE ? OR role LIKE ? OR filiere LIKE ?) LIMIT 30");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Recherche</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="1-css/style.css" />
  <style>
    .search-page { padding: 24px 32px; max-width: 800px; margin: 0 auto; width: 100%; }
    .search-page h1 { font-family: 'Sora', sans-serif; font-size: 22px; margin-bottom: 24px; }
    .search-page .search-count { color: #94a3b8; font-size: 14px; margin-bottom: 16px; }
    .search-user-card { display: flex; align-items: center; gap: 16px; padding: 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px; text-decoration: none; color: inherit; transition: box-shadow .15s; }
    .search-user-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .search-user-card .ava { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; flex-shrink: 0; overflow: hidden; }
    .search-user-card .ava img { width: 100%; height: 100%; object-fit: cover; }
    .search-user-card .info .name { font-weight: 600; font-size: 15px; }
    .search-user-card .info .detail { font-size: 13px; color: #64748b; margin-top: 2px; }
    .search-page .empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .search-page .empty-icon { font-size: 48px; margin-bottom: 12px; }
    .search-page .form-inline { display: flex; gap: 12px; margin-bottom: 24px; }
    .search-page .form-inline input { flex: 1; padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 15px; }
    .search-page .form-inline button { padding: 10px 24px; background: #6366f1; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .search-page .form-inline button:hover { background: #4f46e5; }
  </style>
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <?php if (isset($_SESSION['role'])): ?>
          <div class="nav-item" onclick="location.href='<?= ($_SESSION['role'] === 'formateur' ? 'formateur/tableaubord/tableaubord.php' : 'stagiaire/tableaubord/index.php') ?>'">
            <span class="nav-icon">🏠</span><span>Tableau de bord</span>
          </div>
        <?php endif; ?>
        <div class="nav-item" onclick="location.href='public_profile/public_profile.php'">
          <span class="nav-icon">👤</span><span>Profils publics</span>
        </div>
      </nav>
    </aside>

    <header class="header">
      <form class="header-search" action="search.php" method="GET">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input class="header-search-input" type="search" name="q" placeholder="Rechercher un stagiaire..." value="<?= htmlspecialchars($q) ?>">
      </form>
      <div class="user-pill">
        <div class="user-info">
          <div class="user-name">Recherche</div>
          <div class="user-role"><?= isset($_SESSION['role']) ? $_SESSION['role'] : 'Visiteur' ?></div>
        </div>
        <div class="user-avatar">?</div>
      </div>
    </header>

    <main class="main">
      <section class="search-page">
        <form class="form-inline" action="search.php" method="GET">
          <input type="search" name="q" placeholder="Rechercher un utilisateur..." value="<?= htmlspecialchars($q) ?>">
          <button type="submit">Rechercher</button>
        </form>

        <?php if ($q === ''): ?>
          <div class="empty">
            <div class="empty-icon">🔍</div>
            <p>Tapez un nom, un rôle ou une filière pour commencer la recherche.</p>
          </div>
        <?php elseif (empty($users)): ?>
          <div class="empty">
            <div class="empty-icon">📭</div>
            <p>Aucun résultat pour "<strong><?= htmlspecialchars($q) ?></strong>"</p>
          </div>
        <?php else: ?>
          <div class="search-count"><?= count($users) ?> résultat<?= count($users) > 1 ? 's' : '' ?> pour "<strong><?= htmlspecialchars($q) ?></strong>"</div>
          <div class="search-results">
            <?php foreach ($users as $u): ?>
              <a href="public_profile/public_profile.php?id=<?= $u['id_user'] ?>" class="search-user-card">
                <div class="ava">
                  <?php if ($u['photo']): ?>
                    <img src="pagelogin/photo/<?= htmlspecialchars($u['photo']) ?>" alt="">
                  <?php else: ?>
                    <?= strtoupper(substr($u['nom'], 0, 1) . substr($u['prenom'], 0, 1)) ?>
                  <?php endif; ?>
                </div>
                <div class="info">
                  <div class="name"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></div>
                  <div class="detail"><?= htmlspecialchars(ucfirst($u['role'])) . ($u['filiere'] ? ' — ' . htmlspecialchars($u['filiere']) : '') ?></div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
  <script src="2-script/profile-menu.js"></script>
</body>
</html>
