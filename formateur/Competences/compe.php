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
    if (!isset($lacategorie) || empty($lacategorie))
      $err["categorie"] = "la categorie est obligatoire";
    if (empty($err)) {
      $lenom = htmlspecialchars(trim($lenom));
      $lacategorie = htmlspecialchars(trim($lacategorie));
      try {
        $req = $db->prepare("INSERT INTO competences(nom,categorie) VALUES (?,?)");
        $req->execute([$lenom, $lacategorie]);
        if ($req == false) {
          header("Location: compe.php?error=erreur lors de l'ajout");
          exit();
        } else {
          header("Location: compe.php?msg=compétence ajouter avec succes");
          exit();
        }
      } catch (PDOException $e) {
        die("Erreur lors de l'inscription : " . $e->getMessage());
      }
    }
  }

  if (isset($_POST['modif'])) {
    $id = (int) $_POST['id_competence'];
    $lenom = htmlspecialchars(trim($_POST['lenom']));
    $lacategorie = htmlspecialchars(trim($_POST['lacategorie']));
    $errModif = [];
    if (empty($lenom))
      $errModif['nom'] = "le nom est obligatoire";
    if (empty($lacategorie))
      $errModif['categorie'] = "la categorie est obligatoire";
    if (empty($errModif)) {
      try {
        $req = $db->prepare("UPDATE competences SET nom = ?, categorie = ? WHERE id_competence = ?");
        $req->execute([$lenom, $lacategorie, $id]);
        header("Location: compe.php?msg=compétence modifiée avec succès");
        exit();
      } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
      }
    }
  }
}

$editCompetence = null;
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $req = $db->prepare("SELECT id_competence, nom, categorie FROM competences WHERE id_competence = ?");
  $req->execute([$id]);
  $editCompetence = $req->fetch(PDO::FETCH_ASSOC);
}

$showModal = isset($err) && !empty($err);
$showModalModif = isset($errModif) && !empty($errModif);
$showModalModif = $showModalModif || ($editCompetence && !isset($_POST['modif']));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Mes compétences</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="compe.css">
  <style>
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

    .err {
      color: red;
    }

    .empty-state {
      grid-column: 1 / -1;
      text-align: center;
      padding: 48px 20px;
      color: #8a9bb8;
      font-size: 1rem;
      font-weight: 500;
      background: #f4f7fc;
      border-radius: 16px;
      border: 2px dashed #d0d8e4;
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
      from {
        opacity: 0;
        transform: translateY(-16px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes toastOut {
      from {
        opacity: 1;
        transform: translateY(0);
      }

      to {
        opacity: 0;
        transform: translateY(-16px);
      }
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
        <div class="nav-item active" onclick="location.href='compe.php'">
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
        <div class="nav-item" onclick="location.href='../marketplace/marketplace.php'">
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
      <div class="user-pill" data-email="jouariya@ismo.ma">
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
      <section class="content-area">
        <?php if (isset($_GET["msg"])): ?>
          <div id="toastMsg" class="toast success">✅ <?php echo $_GET["msg"]; ?></div>
        <?php elseif (isset($_GET["error"])): ?>
          <div id="toastMsg" class="toast error">❌ <?php echo $_GET["error"]; ?></div>
        <?php endif; ?>
        <div class="page-header">
          <div>
            <h1>Gestion des compétences</h1>
          </div>
          <a href="#" class="btn btn-primary" onclick="ouvrirModal('modalAjout');return false;">+ Ajouter une
            compétence</a>
        </div>

        <div class="search-panel">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="search" placeholder="Rechercher une compétence..." aria-label="Recherche compétence" />
          </div>
          <div class="select-box">
            <select aria-label="Toutes les catégories">
              <option>Toutes les catégories</option>
              <option>Frontend</option>
              <option>Backend</option>
              <option>DevOps</option>
              <option>Cybersécurité</option>
              <option>Base de données</option>
            </select>
          </div>
        </div>

        <?php
        $catList = ['Frontend', 'Backend', 'DevOps', 'Cybersécurité', 'Base de données'];
        $catReq = $db->query("SELECT categorie, COUNT(*) AS nb FROM competences GROUP BY categorie");
        $catCounts = [];
        foreach ($catReq->fetchAll(PDO::FETCH_ASSOC) as $row) {
          $catCounts[str_replace('_', ' ', $row['categorie'])] = (int) $row['nb'];
        }
        ?>
        <div class="category-grid">
          <?php foreach ($catList as $cat): ?>
            <article class="overview-card">
              <strong><?= $catCounts[$cat] ?? 0 ?></strong>
              <span><?= $cat ?></span>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="cards-grid">
          <?php
          $aff = $db->prepare("SELECT id_competence, nom, categorie FROM competences");
          $aff->execute();
          $competences = $aff->fetchAll(PDO::FETCH_ASSOC);
          if (count($competences) > 0) {
            foreach ($competences as $c) {
              echo '<article class="skill-card">
                  <div class="skill-card-header">
                    <h2>' . htmlspecialchars($c["nom"]) . '</h2>
                    <div class="skill-card-actions">
                      <a href="compe.php?edit=' . $c['id_competence'] . '" class="icon-btn" aria-label="Modifier">✏️</a>
                      <a href="supprimer.php?id=' . $c['id_competence'] . '" class="icon-btn danger" aria-label="Supprimer" onclick="return confirm(\'Supprimer cette compétence ?\')">🗑️</a>
                    </div>
                  </div>
                  <span class="skill-badge">' . htmlspecialchars($c['categorie']) . '</span>
                  <div class="skill-row"><span>Déclarations</span><strong>0</strong></div>
                  <div class="skill-row"><span>Validations</span><strong>0</strong></div>
                  <div class="skill-row progress-row"><span>Taux validation</span><strong>0%</strong></div>
                  </article>';
            }
          } else
            echo '<div class="empty-state">📭 Aucune compétence disponible</div>';
          ?>

        </div>
      </section>
    </main>
  </div>
  <div class="modal-window<?php if ($showModal)
    echo ' show'; ?>" id="modalAjout" onclick="if(event.target==this)fermerModal('modalAjout')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Ajouter une compétence</h2>
        <button class="modal-close" onclick="fermerModal('modalAjout')">&times;</button>
      </div>
      <form method="POST" action="<?php $_SERVER["PHP_SELF"] ?>">
        <div class="modal-body">
          <label class="modal-label">Nom de la compétence</label>
          <?php if (isset($err["nom"]))
            echo "<div class='err'>" . $err["nom"] . "</div>" ?>
            <input class="modal-input"
              value="<?php if (isset($_POST['lenom']))
            echo htmlspecialchars($_POST['lenom']); ?>" type="text"
            name="lenom" placeholder="Ex: React, Python...">
          <?php if (isset($err["categorie"]))
            echo "<div class='err'>" . $err["categorie"] . "</div>" ?>
            <label class="modal-label">Catégorie</label>
            <select class="modal-input" name="lacategorie">
              <option value="">Sélectionner une catégorie</option>
              <option value="Frontend">Frontend</option>
              <option value="Backend">Backend</option>
              <option value="Cybersécurité">Cybersécurité</option>
              <option value="Base de données">Base de données</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-cancel" onclick="fermerModal('modalAjout')">Annuler</button>
            <button type="submit" name="enr" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal-window<?php if ($showModalModif)
            echo ' show'; ?>" id="modalModif" onclick="if(event.target==this)fermerModal('modalModif')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Modifier la compétence</h2>
        <button class="modal-close" onclick="fermerModal('modalModif')">&times;</button>
      </div>
      <form method="POST" action="<?php $_SERVER["PHP_SELF"] ?>">
        <input type="hidden" name="id_competence"
          value="<?= $editCompetence ? $editCompetence['id_competence'] : '' ?>">
        <div class="modal-body">
          <label class="modal-label">Nom de la compétence</label>
          <?php if (isset($errModif["nom"]))
            echo "<div class='err'>" . $errModif["nom"] . "</div>" ?>
            <input class="modal-input" type="text" name="lenom" placeholder="Ex: React, Python..."
              value="<?= htmlspecialchars($editCompetence['nom'] ?? ($_POST['lenom'] ?? '')) ?>">
          <?php if (isset($errModif["categorie"]))
            echo "<div class='err'>" . $errModif["categorie"] . "</div>" ?>
            <label class="modal-label">Catégorie</label>
            <select class="modal-input" name="lacategorie">
              <option value="">Sélectionner une catégorie</option>
              <option value="Frontend" <?= ($editCompetence['categorie'] ?? ($_POST['lacategorie'] ?? '')) == 'Frontend' ? 'selected' : '' ?>>Frontend</option>
            <option value="Backend" <?= ($editCompetence['categorie'] ?? ($_POST['lacategorie'] ?? '')) == 'Backend' ? 'selected' : '' ?>>Backend</option>
            <option value="DevOps" <?= ($editCompetence['categorie'] ?? ($_POST['lacategorie'] ?? '')) == 'DevOps' ? 'selected' : '' ?>>DevOps</option>
            <option value="Cybersécurité" <?= ($editCompetence['categorie'] ?? ($_POST['lacategorie'] ?? '')) == 'Cybersécurité' ? 'selected' : '' ?>>Cybersécurité</option>
            <option value="Base de données" <?= ($editCompetence['categorie'] ?? ($_POST['lacategorie'] ?? '')) == 'Base de données' ? 'selected' : '' ?>>Base de données</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" onclick="fermerModal('modalModif')">Annuler</button>
          <button type="submit" name="modif" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
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
    function ouvrirModal(id) {
      document.getElementById(id).classList.add('show');
    }
    function fermerModal(id) {
      document.getElementById(id).classList.remove('show');
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { fermerModal('modalAjout'); fermerModal('modalModif'); }
    });
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>