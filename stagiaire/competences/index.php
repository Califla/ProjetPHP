<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  if ($role !== 'stagiaire' && $role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['declarer'])) {
      $err=[];
      if(!isset($_POST['competence']) || empty($_POST['competence'])) $err['competence']="Veuillez sélectionner une compétence";
      if(!isset($_POST['niveau']) || empty($_POST['niveau'])) $err['niveau']="Veuillez sélectionner un niveau";
      if(!isset($_POST['justification']) || empty($_POST['justification'])) $err['justification']="Veuillez saisir une justification";
      if(empty($err)) {
      include("../../database/config.php");
      try {
        #verifie que l'utilisateur n'a pas déjà fait une demande pour cette compétence
        $stmt = $db->prepare("SELECT * FROM validation_competence WHERE id_user = ? AND id_competence = ?");
        $stmt->execute([$_SESSION['id_user'], $_POST['competence']]);
        if ($stmt->rowCount() > 0) {
          header("Location: index.php?error=demande_existe_deja");
          exit();
        } else {
          $req = $db->prepare("INSERT INTO validation_competence (id_user, id_competence, niveau,`status`,justification,date_demande ) VALUES (?, ?, ?, ?, ?, ?)");
          $req->execute([$_SESSION['id_user'], $_POST['competence'], $_POST['niveau'],"en_attente", $_POST['justification'], date('Y-m-d')]);
          header("Location: index.php?msg=demande_declaree");
          exit();
        }
      } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
      }
    }
  }
  }
  include '../../database/config.php';
  try {
    #récupère toutes les compétences de l'utilisateur connecté et les validees en premier puis en attente et enfin refusées
    $competences = $db->prepare("SELECT * FROM validation_competence vc JOIN competences c ON vc.id_competence = c.id_competence WHERE vc.id_user = ? ORDER BY vc.status DESC");
    $competences->execute([$id_user]);
    $competences = $competences->fetchAll(PDO::FETCH_ASSOC);


    $total = count($competences);
    $validees = $db->prepare("SELECT COUNT(*) FROM validation_competence WHERE id_user = ? AND status = 'validee'");
    $validees->execute([$id_user]);
    $validees = $validees->fetchColumn();

    $en_attente = $db->prepare("SELECT COUNT(*) FROM validation_competence WHERE id_user = ? AND status = 'en_attente'");
    $en_attente->execute([$id_user]);
    $en_attente = $en_attente->fetchColumn();

    $refusees = $db->prepare("SELECT COUNT(*) FROM validation_competence WHERE id_user = ? AND status = 'refusee'");
    $refusees->execute([$id_user]);
    $refusees = $refusees->fetchColumn();
  } catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
  }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Mes compétences</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="style.css">
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
        <div class="nav-item active" onclick="location.href='index.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <?php if ($role === 'mentor'): ?>
          <div class="nav-item" onclick="location.href='../mes propositions/index.php'">
            <span class="nav-icon">🤝</span>
            <span>Mes propositions</span>
          </div>
        <?php endif; ?>
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
      <div class="page-top">
        <div>
          <h1 class="page-title">Mes compétences</h1>
          <p class="page-sub">Déclarez et faites valider vos compétences par vos formateurs</p>
        </div>
        <button class="btn-add" onclick="ouvrirModal('modalDeclarer');return false;">+ Déclarer une compétence</button>
      </div>

      <div class="stats-row">
        <div class="stat-box stat-valid">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <div>
            <div class="stat-num"><?= $validees ?></div>
            <div class="stat-lbl">Validées</div>
          </div>
        </div>
        <div class="stat-box stat-pending">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <div>
            <div class="stat-num"><?= $en_attente ?></div>
            <div class="stat-lbl">En attente</div>
          </div>
        </div>
        <div class="stat-box stat-refused">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </div>
          <div>
            <div class="stat-num"><?= $refusees ?></div>
            <div class="stat-lbl">Refusées</div>
          </div>
        </div>
        <div class="stat-box stat-total">
          <div class="stat-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
            </svg>
          </div>
          <div>
            <div class="stat-num"><?= $total ?></div>
            <div class="stat-lbl">Total</div>
          </div>
        </div>
      </div>

      <div class="progress-bar">
        <div class="progress-fill" style="width: <?= ($total > 0) ? ($validees / $total) * 100 : 0 ?>%"></div>
        <span class="progress-text"><?= $validees ?> / <?= $total ?> validées
          (<?= ($total > 0) ? round(($validees / $total) * 100) : 0 ?>%)</span>
      </div>


      <div class="cards-grid">
        <?php foreach ($competences as $competence): ?>
          <article class="skill-card <?= $competence['status'] ?>">
            <div class="skill-card-header">
              <h2>⚛️ <?= $competence['nom'] ?></h2>
              <span class="status-badge <?= $competence['status'] ?>"> <?= ucfirst($competence['status']) ?></span>
            </div>
            <span class="skill-cat"><?= $competence['categorie'] ?></span>
            <div class="skill-row"><span>Niveau</span><strong><?= $competence['niveau'] ?></strong></div>
            <?php if (!empty($competence['date_validation'])): ?>
              <?php
              $date = new DateTime($competence['date_validation']);
              $date_formatee = $date->format('d/m/Y');
              ?>
              <div class="skill-row"><span>Date de validation</span><strong><?= $date_formatee ?></strong></div>
            <?php else: ?>
              <div class="skill-row"><span>Date de validation</span><strong>Non validé</strong></div>
            <?php endif; ?>
            <?php if ($competence['status'] === 'refusee'): ?>
              <a href="supprimer_demande.php?id_competence=<?= $competence['id_competence'] ?>" class="btn-supprimer"
                onclick="return confirm('Supprimer cette demande ?')">🗑️ Supprimer la demande</a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
  <?php
  $compdisponibles = $db->prepare("SELECT * FROM competences WHERE id_competence NOT IN (SELECT id_competence FROM validation_competence WHERE id_user = ?)");
  $compdisponibles->execute([$id_user]);
  $compdisponibles = $compdisponibles->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <div class="modal-window" id="modalDeclarer" onclick="if(event.target==this)fermerModal('modalDeclarer')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Déclarer une compétence</h2>
        <button class="modal-close" onclick="fermerModal('modalDeclarer')">&times;</button>
      </div>
      <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="modal-body">
          <label class="modal-label">Compétence</label>
          <?php if (isset($err['competence'])): ?>
            <div class="error"><?= $err['competence'] ?></div>
          <?php endif; ?>
          <select class="modal-input" name="competence">
            <option value="">Sélectionner une compétence</option>
            <?php foreach ($compdisponibles as $competence): ?>
              <option value="<?= htmlspecialchars($competence['id_competence']); ?>">
                <?= htmlspecialchars($competence['nom']); ?></option>
            <?php endforeach; ?>
          </select>
          <label class="modal-label">Niveau</label>
          <?php if (isset($err['niveau'])): ?>
            <div class="error"><?= $err['niveau'] ?></div>
          <?php endif; ?>
          <select class="modal-input" name="niveau">
            <option value="">Sélectionner un niveau</option>
            <option value="debutant">Débutant</option>
            <option value="intermediaire">Intermédiaire</option>
            <option value="avance">Avancé</option>
          </select>
          <label class="modal-label">Justification (optionnelle)</label>
          <?php if (isset($err['justification'])): ?>
            <div class="error"><?= $err['justification'] ?></div>
          <?php endif; ?>
          <textarea class="modal-input" name="justification" placeholder="Pourquoi cette compétence ?"
            rows="3"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="fermerModal('modalDeclarer')">Annuler</button>
          <button type="submit" name="declarer" class="btn-primary">Déclarer</button>
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
      if (e.key === 'Escape') { fermerModal('modalDeclarer'); }
    });
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>