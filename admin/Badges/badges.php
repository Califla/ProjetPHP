<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
}
include("../../database/config.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['enr'])) {
    extract($_POST);
    $err = [];
    if (!isset($lenom) || empty($lenom))
      $err['nom'] = "le nom est obligatoire";
    if (!isset($lpoints) || empty($lpoints) || !is_numeric($lpoints))
      $err['points'] = "les points requis sont obligatoires";
    if (!isset($licone) || empty($licone))
      $err['icone'] = "l'icône est obligatoire";
    if (empty($err)) {
      $lenom = htmlspecialchars(trim($lenom));
      $lpoints = (int) $lpoints;
      $licone = htmlspecialchars(trim($licone));
      try {
        $req = $db->prepare("INSERT INTO badges(nom, points_requis, icone) VALUES (?,?,?)");
        $req->execute([$lenom, $lpoints, $licone]);
        if ($req == false) {
          header("Location: badges.php?error=erreur lors de l'ajout");
          exit();
        } else {
          header("Location: badges.php?msg=badge ajouté avec succès");
          exit();
        }
      } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
      }
    }
  }
}
$totalbadges= $db->query("SELECT COUNT(*) FROM badges")->fetchColumn();
$totalattributes= $db->query("SELECT COUNT(*) FROM obtention_badges ")->fetchColumn();
$showModal = isset($err) && !empty($err);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Badges</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="badges.css">
  <style>
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-bottom: 32px;
    }
    .stat-card {
      background: white;
      padding: 24px;
      border-radius: 16px;
      border: 1px solid #eef2f6;
      position: relative;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .stat-card h3 {
      font-size: 0.875rem;
      color: #64748b;
      margin-bottom: 12px;
    }
    .stat-card .val {
      font-size: 2rem;
      font-weight: 700;
      color: #0d2240;
    }
    .modal-window {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .modal-window.show { display: flex; }
    .modal-window .modal-content {
      background: #fff;
      border-radius: 20px;
      padding: 28px;
      width: 90%;
      max-width: 480px;
      box-shadow: 0 24px 64px rgba(0, 0, 0, 0.15);
    }
    .modal-window .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .modal-window .modal-header h2 { margin: 0; font-size: 1.2rem; }
    .modal-window .modal-close {
      background: none; border: none; font-size: 26px; color: #6b7a99;
      cursor: pointer; width: 36px; height: 36px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
    }
    .modal-window .modal-close:hover { background: #f4f7fb; }
    .modal-window .modal-body {
      display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;
    }
    .modal-window .modal-label { font-weight: 600; font-size: 0.85rem; color: #101d33; }
    .modal-window .modal-input {
      padding: 12px 14px; border: 1px solid #e8ecf1; border-radius: 12px;
      font-size: 0.9rem; font-family: 'Sora', sans-serif; outline: none; width: 100%;
    }
    .modal-window .modal-input:focus { border-color: #2e6fca; }
    .modal-window .modal-footer {
      display: flex; gap: 10px; justify-content: flex-end;
    }
    .modal-window .btn-cancel {
      background: #f4f7fb; color: #101d33; display: inline-flex; align-items: center;
      justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 8px;
      border: none; cursor: pointer; font-weight: 600; font-family: 'Sora', sans-serif;
    }
    .modal-window .btn-cancel:hover { background: #e2e8f0; }
    .modal-window .btn-primary {
      background: #0d2240; color: #fff; display: inline-flex; align-items: center;
      justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 8px;
      border: none; cursor: pointer; font-weight: 600; font-family: 'Sora', sans-serif;
    }
    .modal-window .btn-primary:hover { background: #163158; }
    .toast {
      padding: 16px 20px; border-radius: 12px; font-weight: 600; font-size: 0.9rem;
      margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
      animation: toastIn 0.4s ease; border: 1px solid; box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .toast.success { background: #e6f7e6; color: #1e7e34; border-color: #b7e4b7; }
    .toast.error { background: #fde8e8; color: #c0392b; border-color: #f5c6cb; }
    .toast-hide { animation: toastOut 0.4s ease forwards; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes toastOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-16px); } }
    .empty-state {
      grid-column: 1 / -1; text-align: center; padding: 48px 20px;
      color: #8a9bb8; font-size: 1rem; font-weight: 500;
      background: #f4f7fc; border-radius: 16px; border: 2px dashed #d0d8e4;
    }
    .err { color: red; }
    .icon-link {
      font-size: 0.75rem;
      color: #2e6fca;
      margin-bottom: 4px;
      display: inline-block;
    }
  </style>
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/table.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../Utulisateurs/utili.php'">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </div>
        <div class="nav-item active" onclick="location.href='badges.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item" onclick="location.href='../Moderation/mode.php'">
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
      <form class="header-search" action="#" onsubmit="return false;">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input id="globalSearch" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
      </form>
      <div class="user-pill" data-email="admin@ismo.ma">
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

    <main class="main">
      <section class="content-area">
        <?php if (isset($_GET["msg"])): ?>
          <div id="toastMsg" class="toast success">✅ <?php echo $_GET["msg"]; ?></div>
        <?php elseif (isset($_GET["error"])): ?>
          <div id="toastMsg" class="toast error">❌ <?php echo $_GET["error"]; ?></div>
        <?php endif; ?>
        <div class="page-header">
          <div>
            <h1>Gestion des badges</h1>
            <p class="eyebrow">créer et gérer les badges</p>
          </div>
          <a href="#" class="btn btn-primary" onclick="ouvrirModal('modalAjout');return false;">+ Ajouter un badge</a>
        </div>

        <?php
        include("../../database/config.php");
        $totalBadges = $db->query("SELECT COUNT(*) FROM badges")->fetchColumn();
        $totalAttrib = $db->query("SELECT COUNT(*) FROM obtention_badges")->fetchColumn();
        ?>
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Total badges</h3>
            <div class="val"><?php echo $totalBadges; ?></div>
          </div>
          <div class="stat-card">
            <h3>Total attributions</h3>
            <div class="val"><?php echo $totalAttrib; ?></div>
          </div>
        </div>

        <div class="badge-list">
          <?php
          include("../../database/config.php");
          $aff = $db->prepare("SELECT * FROM badges");
          $aff->execute();
          $badges = $aff->fetchAll(PDO::FETCH_ASSOC);
          if (count($badges) > 0) {
            foreach ($badges as $b) {
              echo '<article class="badge-card">
                  <div class="badge-icon">' . htmlspecialchars($b['icone']) . '</div>
                  <div class="badge-card-body">
                    <h3>' . htmlspecialchars($b['nom']) . '</h3>
                    <p>' . $b['points_requis'] . ' points requis</p>
                  </div>
                  <div class="badge-actions">
                    <a href="edit_badge.php?id=' . $b['id_badge'] . '" class="icon-btn" aria-label="Modifier">✏️</a>
                    <a href="delete_badge.php?id=' . $b['id_badge'] . '" class="icon-btn danger" aria-label="Supprimer" onclick="return confirm(\'Supprimer ce badge ?\')">🗑️</a>
                  </div>
                </article>';
            }
          } else
            echo '<div class="empty-state">📭 Aucun badge disponible</div>';
          ?>
        </div>
      </section>
    </main>
  </div>

  <div class="modal-window<?php if ($showModal) echo ' show'; ?>" id="modalAjout" onclick="if(event.target==this)fermerModal('modalAjout')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Ajouter un badge</h2>
        <button class="modal-close" onclick="fermerModal('modalAjout')">&times;</button>
      </div>
      <form method="POST" action="<?php $_SERVER["PHP_SELF"] ?>">
        <div class="modal-body">
          <label class="modal-label">Nom du badge</label>
          <?php if (isset($err["nom"])) echo "<div class='err'>" . $err["nom"] . "</div>" ?>
          <input class="modal-input" value="<?php if (isset($_POST['lenom'])) echo htmlspecialchars($_POST['lenom']); ?>" type="text" name="lenom" placeholder="Ex: Premier pas, Mentor actif...">

          <label class="modal-label">Points requis</label>
          <?php if (isset($err["points"])) echo "<div class='err'>" . $err["points"] . "</div>" ?>
          <input class="modal-input" value="<?php if (isset($_POST['lpoints'])) echo htmlspecialchars($_POST['lpoints']); ?>" type="number" name="lpoints" placeholder="Ex: 100">

          <label class="modal-label">Icône</label>
          <a href="https://emojidb.org/badge-emojis" target="_blank" class="icon-link">Trouver des icônes 🎯</a>
          <?php if (isset($err["icone"])) echo "<div class='err'>" . $err["icone"] . "</div>" ?>
          <input class="modal-input" value="<?php if (isset($_POST['licone'])) echo htmlspecialchars($_POST['licone']); ?>" type="text" name="licone" placeholder="Ex: 🎯">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" onclick="fermerModal('modalAjout')">Annuler</button>
          <button type="submit" name="enr" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function dismissToast() {
      var t = document.getElementById('toastMsg');
      if (t) { t.classList.add('toast-hide'); setTimeout(function () { t.remove(); }, 400); }
    }
    <?php if (isset($_GET["msg"]) || isset($_GET["error"])): ?>
      setTimeout(dismissToast, 4500);
    <?php endif; ?>
    function ouvrirModal(id) { document.getElementById(id).classList.add('show'); }
    function fermerModal(id) { document.getElementById(id).classList.remove('show'); }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') fermerModal('modalAjout');
    });
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>
