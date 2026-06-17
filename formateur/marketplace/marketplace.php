<?php
session_start();
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
          header("Location: marketplace.php?msg=demande+publiée+avec+succès");
          exit();
        } else {
          header("Location: marketplace.php?error=Erreur lors de la publication de la demande");
          exit();
        }
      } catch (PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
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
  <title>ISMO-SkillSwap – Marketplace Formateur</title>
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .empty-state {
      text-align: center;
      padding: 48px 20px;
      color: #8a9bb8;
      font-size: 1rem;
      font-weight: 500;
      background: #f4f7fc;
      border-radius: 16px;
      border: 2px dashed #d0d8e4;
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

    .modal-window.show {
      display: flex;
    }

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

    .modal-window .modal-header h2 {
      margin: 0;
      font-size: 1.2rem;
    }

    .modal-window .modal-close {
      background: none;
      border: none;
      font-size: 26px;
      color: #6b7a99;
      cursor: pointer;
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-window .modal-close:hover {
      background: #f4f7fb;
    }

    .modal-window .modal-body {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-bottom: 24px;
    }

    .modal-window .modal-label {
      font-weight: 600;
      font-size: 0.85rem;
      color: #101d33;
    }

    .modal-window .modal-input {
      padding: 12px 14px;
      border: 1px solid #e8ecf1;
      border-radius: 12px;
      font-size: 0.9rem;
      font-family: 'Sora', sans-serif;
      outline: none;
      width: 100%;
    }

    .modal-window .modal-input:focus {
      border-color: #2e6fca;
    }

    .modal-window .modal-footer {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .modal-window .btn-cancel {
      background: #f4f7fb;
      color: #101d33;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-family: 'Sora', sans-serif;
    }

    .modal-window .btn-cancel:hover {
      background: #e2e8f0;
    }

    .modal-window .btn-primary {
      background: #0d2240;
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-family: 'Sora', sans-serif;
    }

    .modal-window .btn-primary:hover {
      background: #163158;
    }

    .toast {
      padding: 16px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      animation: toastIn 0.4s ease;
      border: 1px solid;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .toast.success {
      background: #e6f7e6;
      color: #1e7e34;
      border-color: #b7e4b7;
    }

    .toast.error {
      background: #fde8e8;
      color: #c0392b;
      border-color: #f5c6cb;
    }

    .toast-hide {
      animation: toastOut 0.4s ease forwards;
    }

    @keyframes toastIn {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes toastOut {
      from { opacity: 1; transform: translateY(0); }
      to   { opacity: 0; transform: translateY(-20px); }
    }
  </style>
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/tableaubord.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../validecompetence/validecompetence.php'">
          <span class="nav-icon">📋</span>
          <span>Valider compétences</span>
        </div>
        <div class="nav-item" onclick="location.href='../Competences/compe.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <div class="nav-item" onclick="location.href='../utilisateur/index.php'">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </div>
        <div class="nav-item" onclick="location.href='../badges/badge.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item active" onclick="location.href='marketplace.php'">
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
      <div class="user-pill" data-email="">
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
    </header>

    <main class="main" data-role="formateur">
      <?php if (isset($_GET["msg"])): ?>
        <div id="toastMsg" class="toast success">✅ <?php echo $_GET["msg"]; ?></div>
      <?php elseif (isset($_GET["error"])): ?>
        <div id="toastMsg" class="toast error">❌ <?php echo $_GET["error"]; ?></div>
      <?php endif; ?>
      <div class="page-header">
        <div>
          <div class="page-title">Marketplace Formateur</div>
          <div class="page-sub">Gérez les demandes d'aide et proposez votre soutien aux stagiaires.</div>
        </div>
        <button type="button" class="btn btn-primary" onclick="ouvrirModal('modalAjout')">+ publier une demande</button>
      </div>
      <section class="controls-row">
        <div class="control-group search-group">
          <svg class="control-icon" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input id="searchInput" type="search" placeholder="Rechercher par mot-clé, compétence, technologie..." />
        </div>
        <div class="control-group right-group">
          <select>
            <option value="all">Toutes les filières</option>
            <option value="DEV">DEV</option>
            <option value="CYBERSEC">CYBERSEC</option>
            <option value="DATA">DATA</option>
          </select>
          <select>
            <option value="all">Tous les niveaux</option>
            <option value="Débutant">Débutant</option>
            <option value="Intermédiaire">Intermédiaire</option>
            <option value="Avancé">Avancé</option>
          </select>
          <select>
            <option value="all">Tous les statuts</option>
            <option value="ouvert">Ouvert</option>
            <option value="fermé">Fermé</option>
          </select>
          <button class="filter-btn" id="filterBtn">
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
            Filtres
          </button>
        </div>
      </section>
      <div class="demandes-list">
        <?php
        include("../../database/config.php");
        $stmt = $db->query("SELECT aide.*, utilisateurs.nom, utilisateurs.prenom FROM aide JOIN utilisateurs ON aide.id_user = utilisateurs.id_user WHERE aide.status = 'ouvert'");
        $stmt->execute();
        $demandes = $stmt->fetchAll();
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
                </p>
              </div>
              <div class="header-right">
                <span class="badge-status ouvert"><?php echo htmlspecialchars($demand['status']); ?></span>
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
              <?php if ($_SESSION['role'] === 'mentor'): ?>
                <button class="primary-btn">Proposer mon aide</button>
              <?php endif; ?>
              <div class="action-right">
                <a href="signaler.php?id=<?php echo $demand['id_demande']; ?>" class="ghost-btn report-btn">Signaler</a>
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
      <form action="marketplace.php" method="POST">
        <div class="modal-body">
          <label class="modal-label">Titre</label>
          <?php if (isset($err['titre'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['titre']; ?></span>
          <?php endif; ?>
          <input class="modal-input" type="text" name="titre" placeholder="Ex: Aide sur les algorithmes..." value="<?php echo isset($titre) ? htmlspecialchars($titre) : ''; ?>">
          <label class="modal-label">Description</label>
          <?php if (isset($err['description'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['description']; ?></span>
          <?php endif; ?>
          <textarea class="modal-input" name="description" rows="4" placeholder="Décrivez votre demande..."><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
          <label class="modal-label">Tags (séparés par des virgules)</label>
          <?php if (isset($err['tags'])): ?>
            <span style="color:red;font-size:0.8rem;"><?php echo $err['tags']; ?></span>
          <?php endif; ?>
          <input class="modal-input" type="text" name="tags" placeholder="Ex: Python, Débutant" value="<?php echo isset($tags) ? htmlspecialchars($tags) : ''; ?>">
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
  <script src="../2-script/profile-menu.js"></script>
  <script src="../2-script/search.js"></script>
</body>

</html>