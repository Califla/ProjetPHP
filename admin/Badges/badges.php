<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  require_once("../../database/config.php");

  $sql = "
  SELECT b.*,
  COUNT(ob.id_user) as total_attributions
  FROM badges b
  LEFT JOIN obtention_badges ob
  ON b.id_badge = ob.id_badge
  GROUP BY b.id_badge
  ";

  $stmt = $db->query($sql);
  $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $recentStmt = $db->query("
    SELECT 
        u.nom,
        u.prenom,
        b.nom AS badge_nom,
        ob.date_obtention
    FROM obtention_badges ob
    JOIN utilisateurs u ON u.id_user = ob.id_user
    JOIN badges b ON b.id_badge = ob.id_badge
    ORDER BY ob.date_obtention DESC
    LIMIT 5
");

  $recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php if (isset($_GET['success'])): ?>
  <div style="color: green;">
    Badge créé avec succès ✔
  </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion des badges</title>
  <link rel="stylesheet" href="badges.css" />
  <link rel="stylesheet" href="../../1-css/style.css" />
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

    <main class="content-area">
      <div class="page-header">
        <div>
          <h1 class="page-title">Gestion des badges</h1>
          <p classtableaubord="eyebrow">Gestion des badges</p>
          <p class="page-subtitle">Créez et gérez les badges de reconnaissance</p>
        </div>
        <button type="button" class="btn btn-primary"
          onclick="document.getElementById('badgeModal').style.display='flex'">
          + Créer un badge
        </button>
      </div>

      <div class="badges-container">
        <section class="badges-available">
          <h2>Badges disponibles</h2>
          <div class="badge-list">

            <?php foreach ($badges as $badge) { ?>

              <article class="badge-card">

                <div class="badge-icon">
                  🏆
                </div>

                <h3><?= htmlspecialchars($badge['nom']) ?></h3>

                <p>
                  <?= $badge['points_requis'] ?> points requis
                </p>

                <div class="badge-meta">
                  <span class="meta-label">attribués</span>
                  <span class="meta-value">
                    <?= $badge['total_attributions'] ?>
                  </span>
                </div>

                <div class="badge-actions">

                  <a
                    href="edit_badge.php?id=<?= $badge['id_badge'] ?>"
                    class="icon-btn"
                    style="text-decoration:none;">
                    ✏️
                  </a>

                  <a
                    href="delete_badge.php?id=<?= $badge['id_badge'] ?>"
                    class="icon-btn danger"
                    style="text-decoration:none;"
                    onclick="return confirm('Supprimer ce badge ?')">
                    🗑️
                  </a>

                </div>

              </article>

            <?php } ?>

          </div>
        </section>

        <section class="attributions-recent">
          <h2>Attributions récentes</h2>
          <div class="attribution-list">
            <article class="attribution-item">
              <div class="attribution-badge">🎯</div>
              <div class="attribution-info">
                <div class="attribution-name">Ahmed Idrissi</div>
                <div class="attribution-badge-name">Premier pas</div>
                <div class="attribution-details">Attribué par Prof. Kamal Benjelloun • 26/04/2026</div>
              </div>
            </article>

            <article class="attribution-item">
              <div class="attribution-badge">⭐</div>
              <div class="attribution-info">
                <div class="attribution-name">Sara El Amrani</div>
                <div class="attribution-badge-name">Mentor actif</div>
                <div class="attribution-details">Attribué par Prof. Kamal Benjelloun • 25/04/2026</div>
              </div>
            </article>

            <article class="attribution-item">
              <div class="attribution-badge">🔒</div>
              <div class="attribution-info">
                <div class="attribution-name">Youssef Benali</div>
                <div class="attribution-badge-name">Sécurité Pro</div>
                <div class="attribution-details">Attribué par Prof. Amina Tazi • 24/04/2026</div>
              </div>
            </article>

            <article class="attribution-item">
              <div class="attribution-badge">💜</div>
              <div class="attribution-info">
                <div class="attribution-name">Fatima Zahrae</div>
                <div class="attribution-badge-name">Expert React</div>
                <div class="attribution-details">Attribué par Prof. Kamal Benjelloun • 23/04/2026</div>
              </div>
            </article>

            <article class="attribution-item">
              <div class="attribution-badge">💛</div>
              <div class="attribution-info">
                <div class="attribution-name">Karim Alaoui</div>
                <div class="attribution-badge-name">Collaborateur</div>
                <div class="attribution-details">Attribué par Prof. Hassan Idrissi • 22/04/2026</div>
              </div>
            </article>

            <div class="view-all">
              <a href="#">Voir toutes les attributions</a>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
  <div id="badgeModal" class="modal">

    <div class="modal-content">

      <span class="close"
        onclick="document.getElementById('badgeModal').style.display='none'">
        &times;
      </span>

      <h2>Créer un badge</h2>

      <form method="POST" action="create_badge.php" enctype="multipart/form-data">

        <input type="text" name="nom" placeholder="Nom du badge" required>

        <input type="number" name="points_requis" placeholder="Points requis" required>

        <input type="file" name="image" required>

        <button type="submit">
          Créer le badge
        </button>

      </form>

    </div>

  </div>

  </div>
  <script>
    function openModal() {
      document.getElementById("badgeModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("badgeModal").style.display = "none";
    }

    window.onclick = function(e) {
      let modal = document.getElementById("badgeModal");
      if (e.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>