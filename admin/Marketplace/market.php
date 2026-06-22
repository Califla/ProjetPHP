<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
if (isset($_SESSION)) {
  extract($_SESSION);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  include("../../database/config.php");
  if (isset($_POST['pub'])) {
    extract($_POST);
    $err = [];
    if (!isset($titre) || empty($titre))
      $err['titre'] = "Le titre est requis";
    if (!isset($description) || empty($description))
      $err['description'] = "La description est requise";
    if (!isset($tags) || empty($tags))
      $err["tags"] = "Les tags sont requis";
    if (empty($err)) {
      $titre = htmlspecialchars(trim($titre));
      $tags = htmlspecialchars(trim($tags));
      $description = htmlspecialchars(trim($description));
      $date_pub = date('Y-m-d H:i:s');
      $status = 'ouvert';
      try {
        $stmt = $db->prepare("INSERT INTO aide (titre, description, status, date_pub, tags, id_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titre, $description, $status, $date_pub, $tags, $_SESSION['id_user']]);
        if ($stmt == true) {
          header("Location: market.php?msg=demande+publiée+avec+succès");
          exit();
        } else {
          header("Location: market.php?error=Erreur lors de la publication de la demande");
          exit();
        }
      } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Marketplace Admin</title>
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="stylesheet" href="../../1-css/marcketplace.css" />

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
        <div class="nav-item" onclick="location.href='../Badges/badges.php'">
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
        <div class="nav-item active" onclick="location.href='market.php'">
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

    <main class="main" data-role="admin">
      <div class="page-header">
        <div>
          <div class="page-title">Marketplace Admin</div>
          <div class="page-sub">Gérez les demandes d'aide des stagiaires et surveillez l'activité du marketplace.</div>
        </div>
        <?php if (false): ?>
        <button type="button" class="btn btn-primary" onclick="ouvrirModal('modalAjout')">+ publier une demande</button>
        <?php endif; ?>
      </div>

      <form method="GET" action="market.php" style="display:contents;">
        <section class="controls-row">
          <div class="control-group search-group">
            <svg class="control-icon" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="7" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input name="search" type="search" placeholder="Rechercher par mot-clé, compétence, technologie..."
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
          </div>
          <div class="control-group right-group">
            <select name="filiere">
              <option value="all">Toutes les filières</option>
              <option value="DEV" <?php echo (isset($_GET['filiere']) && $_GET['filiere'] == 'DEV') ? 'selected' : ''; ?>>
                DEV</option>
              <option value="CYBERSEC" <?php echo (isset($_GET['filiere']) && $_GET['filiere'] == 'CYBERSEC') ? 'selected' : ''; ?>>CYBERSEC</option>
              <option value="DATA" <?php echo (isset($_GET['filiere']) && $_GET['filiere'] == 'DATA') ? 'selected' : ''; ?>>DATA</option>
            </select>

            <button type="submit" class="filter-btn" id="filterBtn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14" />
                <line x1="4" y1="10" x2="4" y2="3" />
                <line x1="12" y1="21" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12" y2="3" />
                <line x1="20" y1="21" x2="20" y2="16" />
                <line x1="20" y1="12" x2="20" y2="3" />
                <line x1="1" y1="14" x2="7" y2="14" />
                <line x1="9" y1="8" x2="15" y2="8" />
                <line x1="17" y1="16" x2="23" y2="16" />
              </svg>
              Filtrer
            </button>
          </div>
        </section>
      </form>

      <div class="demandes-list">
        <?php
        include("../../database/config.php");
        try {
          $stmt = $db->query("SELECT a.*, u.nom, u.prenom, u.filiere FROM aide a JOIN utilisateurs u ON a.id_user = u.id_user ORDER BY a.date_pub DESC");
          $demandes = $stmt->fetchAll();
        } catch (PDOException $e) {
          echo "Erreur: " . $e->getMessage();
        }
        if (count($demandes) == 0) {
          echo '<div class="empty-state">Aucune demande d\'aide disponible pour le moment.</div>';
        }
        ?>
        <?php foreach ($demandes as $demand): ?>
          <article class="demande-card">
            <div class="card-header">
              <div class="header-info">
                <h2><?php echo htmlspecialchars($demand['titre']); ?></h2>
                <p><?php echo htmlspecialchars($demand['description']); ?></p>
                <p style="font-size:0.85rem;color:#999;margin-top:8px;">
                  Par : <span
                    style="color:var(--accent);font-weight:600;"><?php echo htmlspecialchars($demand['nom'] . ' ' . $demand['prenom']); ?></span>
                  <?php if (!empty($demand['filiere'])): ?>
                    &nbsp;<span
                      style="font-size:0.75rem;background:#eef2f7;padding:2px 8px;border-radius:4px;"><?php echo htmlspecialchars($demand['filiere']); ?></span>
                  <?php endif; ?>
                </p>
              </div>
              <div class="header-right">
                <span
                  class="badge-status <?php echo htmlspecialchars($demand['status']); ?>"><?php echo htmlspecialchars($demand['status']); ?></span>
              </div>
            </div>
            <div class="tag-row">
              <?php
              if ($demand['tags']) {
                $tagsArray = explode(',', $demand['tags']);
                foreach ($tagsArray as $tag) {
                  echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                }
              } else {
                echo "<span class='no-tags'>Aucun tag</span>";
              }
              ?>
            </div>
            <div class="card-footer">
              <div class="footer-left">
                <span>Publié le <?php echo htmlspecialchars($demand['date_pub']); ?></span>
              </div>
            </div>
            <div class="action-row">
              <div class="action-right">
                <a href="../../formateur/marketplace/signaler.php?id=<?php echo $demand['id_demande']; ?>"
                  class="ghost-btn report-btn">Signaler</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <div class="modal-window" id="modalAjout" onclick="if(event.target==this)fermerModal('modalAjout')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Publier une demande d'aide</h2>
        <button class="modal-close" onclick="fermerModal('modalAjout')">&times;</button>
      </div>
      <form action="market.php" method="POST">
        <div class="modal-body">
          <label class="modal-label">Titre</label>
          <?php if (isset($err['titre'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['titre']; ?></span>
          <?php endif; ?>
          <input class="modal-input" type="text" name="titre" placeholder="Ex: Aide sur les algorithmes..."
            value="<?php echo isset($titre) ? htmlspecialchars($titre) : ''; ?>">
          <label class="modal-label">Description</label>
          <?php if (isset($err['description'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['description']; ?></span>
          <?php endif; ?>
          <textarea class="modal-input" name="description" rows="4"
            placeholder="Décrivez votre demande..."><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
          <label class="modal-label">Tags (séparés par des virgules)</label>
          <?php if (isset($err['tags'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['tags']; ?></span>
          <?php endif; ?>
          <input class="modal-input" type="text" name="tags" placeholder="Ex: Python, Débutant"
            value="<?php echo isset($tags) ? htmlspecialchars($tags) : ''; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="fermerModal('modalAjout')">Annuler</button>
          <button type="submit" name="pub" class="btn-primary">Publier</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function ouvrirModal(id) {
      document.getElementById(id).classList.add('show');
    }
    function fermerModal(id) {
      document.getElementById(id).classList.remove('show');
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') fermerModal('modalAjout');
    });
    function dismissToast() {
      var t = document.getElementById('toastMsg');
      if (t) {
        t.classList.add('toast-hide');
        setTimeout(function () { t.remove(); }, 400);
      }
    }
    <?php if (isset($_GET["msg"]) || isset($_GET["error"])): ?>
      setTimeout(dismissToast, 4500);
    <?php endif; ?>
    <?php if (!empty($err) || isset($_GET['error'])): ?>
      ouvrirModal('modalAjout');
    <?php endif; ?>
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>