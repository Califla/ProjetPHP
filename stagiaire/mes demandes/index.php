<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
   if ($role !== 'stagiaire' && $role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
try{
  include('../../database/config.php');
  $aides= $db->prepare("SELECT * FROM aide WHERE id_user = ? AND `status` IN ('ouvert', 'en_coure') ORDER BY date_pub DESC");
  $aides->execute([$id_user]);
  $aides=$aides->fetchAll(PDO::FETCH_ASSOC);

  $pro= $db->prepare("SELECT p.id_proposition, p.status, p.date_prop, u.nom, u.prenom, a.id_demande FROM propositions_aide p JOIN aide a ON p.id_demande = a.id_demande JOIN utilisateurs u ON p.id_user = u.id_user WHERE a.id_user = ?");
  $pro->execute([$id_user]);
  $pro=$pro->fetchAll(PDO::FETCH_ASSOC);

  #aporter les demandes résolues insi que le nom et prenom du mentor qui a résolu la demande et la note donnée par le stagiaire et la date de résolution
  $demandes_resolues= $db->prepare("SELECT d.id_demande, d.titre, d.description, d.tags, d.date_pub, u.nom AS mentor_nom, u.prenom AS mentor_prenom, r.note_mentor, r.commentaire, r.date_intervention FROM aide d JOIN propositions_aide p ON d.id_demande = p.id_demande AND p.status = 'acceptee' JOIN aide_effectuee r ON p.id_proposition = r.id_proposition JOIN utilisateurs u ON r.id_mentor = u.id_user WHERE d.id_user = ? AND d.status = 'resolu' ORDER BY r.date_intervention DESC");
  $demandes_resolues->execute([$id_user]);
  $demandes_resolues=$demandes_resolues->fetchAll(PDO::FETCH_ASSOC);
  echo "<pre>";

  echo "</pre>";
}catch(PDOException $e){
  echo "Erreur :".$e->getMessage();
  exit();
}
}
$showModifier = false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Mes demandes</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="../../1-css/style.css">
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
        <div class="nav-item active" onclick="location.href='index.php'">
          <span class="nav-icon">📋</span>
          <span>Mes demandes</span>
        </div>
        <div class="nav-item" onclick="location.href='../competences/index.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <?php if ($role === 'mentor'):?>
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
      <div class="page-title">Mes demandes d'aide</div>
      <div class="page-sub">Gérez vos demandes et suivez leur progression</div>

      <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>

      <section class="controls-row">
        <div class="control-group search-group">
          <svg class="control-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input id="searchInput" type="search" placeholder="Rechercher dans mes demandes..." />
        </div>
        <div class="control-group right-group">
          <select id="statusSelect">
            <option value="all">Tous les statuts</option>
            <option value="ouvert">Ouvert</option>
            <option value="resolu">Résolu</option>
            <option value="ferme">Fermé</option>
          </select>
          <button class="filter-btn" id="filterBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
            Filtres
          </button>
        </div>
      </section>

      <div class="section-divider">
        <span class="section-divider-icon">📋</span>
        <h2 class="section-divider-title">Demandes actives</h2>
        <span class="section-divider-line"></span>
      </div>

      <div class="demandes-list" id="demandesList">
        <?php if (count($aides) === 0): ?>
          <p class="empty-state">Aucune demande d'aide trouvée</p>
        <?php endif; ?>
        <?php foreach ($aides as $d): ?>
        <article class="demande-card" data-id_demande="<?= $d['id_demande'] ?>" data-titre="<?= htmlspecialchars($d['titre']) ?>" data-description="<?= htmlspecialchars($d['description']) ?>" data-tags="<?= htmlspecialchars($d['tags']) ?>">
          <div class="card-header">
            <div>
              <h2><?php echo htmlspecialchars($d['titre']); ?></h2>
              <p><?php echo htmlspecialchars($d['description']); ?></p>
            </div>
            <span class="badge-status <?php echo strtolower(htmlspecialchars($d['status'])); ?>"><?php echo htmlspecialchars($d['status']); ?></span>
          </div>
          <div class="tag-row">
            <?php
              if ($d['tags']) {
                $tagsArray = explode(',', $d['tags']);
                foreach ($tagsArray as $tag) {
                  echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                }
              } else {
                echo "<span class='no-tags'>Aucun tag</span>";
              }
              ?>
          </div>
          <div class="card-footer">
            <?php
            $date = new DateTime($d['date_pub']);
            $date_formatee = $date->format('d/m/Y');
            ?>
            <div>Publié le <?php echo htmlspecialchars($date_formatee); ?></div>
          </div>
          <div class="action-row">
            <button type="button" class="ghost-btn" onclick="openModifier(this)">Modifier</button>
            <?php if ($d['status'] === 'en_coure'): ?>
              <button class="btn-resolu" onclick="openRating(<?= $d['id_demande'] ?>)">✓ Marquer comme résolu</button>
            <?php endif; ?>
          </div>
          <?php if ($d['status'] === 'ouvert'): ?>
            <?php $proposals = array_filter($pro, fn($p) => $p['id_demande'] == $d['id_demande'] && $p['status'] === 'en_attente'); ?>
            <?php if (!empty($proposals)): ?>
          <div class="proposals-section">
            <div class="proposals-title">Propositions reçues :</div>
            <?php foreach ($proposals as $p): ?>
            <div class="proposal-item">
              <span class="proposal-proposer"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></span>
              <span class="proposal-badge en_attente">En attente</span>
              <div class="proposal-actions">
                <button class="accept-btn" onclick="location.href='repondre_proposition.php?id_proposition=<?= $p['id_proposition'] ?>&action=accept&id_demande=<?= $d['id_demande'] ?>'">✓ Accepter</button>
                <button class="refuse-btn" onclick="location.href='repondre_proposition.php?id_proposition=<?= $p['id_proposition'] ?>&action=refuse&id_demande=<?= $d['id_demande'] ?>'">✗ Refuser</button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
            <?php endif; ?>
          <?php endif; ?>

        </article>
        <?php endforeach ?>
      </div>

      <div class="section-divider">
        <span class="section-divider-icon">✅</span>
        <h2 class="section-divider-title">Demandes résolues</h2>
        <span class="section-divider-line"></span>
      </div>

      <div class="demandes-resolues" id="demandesResolues">
        <?php if (count($demandes_resolues) > 0): ?>
          <?php foreach ($demandes_resolues as $resolue): ?>
            <?php
              $date_pub = new DateTime($resolue['date_pub']);
              $date_intervention = new DateTime($resolue['date_intervention']);
            ?>
            <article class="demande-card resolu">
              <div class="card-header">
                <div>
                  <h2><?= htmlspecialchars($resolue['titre']) ?></h2>
                  <p><?= htmlspecialchars($resolue['description']) ?></p>
                </div>
                <span class="badge-status resolu">Résolu</span>
              </div>
              <div class="tag-row">
                <?php
                  if ($resolue['tags']) {
                    $tagsArray = explode(',', $resolue['tags']);
                    foreach ($tagsArray as $tag) {
                      echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                    }
                  } else {
                    echo "<span class='no-tags'>Aucun tag</span>";
                  }
                ?>
              </div>
              <div class="card-footer">
                <span>Publié le <?= $date_pub->format('d/m/Y') ?></span>
                <span>Mentor: <?= htmlspecialchars($resolue['mentor_prenom'] . ' ' . $resolue['mentor_nom']) ?></span>
              </div>
              <div class="action-row">
                <span class="resolu-info">✅ Résolu le <?= $date_intervention->format('d/m/Y') ?></span>
                <span class="resolu-note">⭐ <?= htmlspecialchars($resolue['note_mentor']) ?></span>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="empty-state">Aucune demande résolue</p>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <form method="POST" action="resolu.php">
  <div class="modal-overlay" id="ratingModal">
    <div class="modal-content">
      <button class="modal-close" type="button" id="closeModal">&times;</button>
      <h2>Noter le mentor</h2>
      <input type="hidden" name="id_demande" id="ratingIdDemande" value="">
      <div class="star-rating" id="starRating">
        <span class="star" data-value="1">☆</span>
        <span class="star" data-value="2">☆</span>
        <span class="star" data-value="3">☆</span>
        <span class="star" data-value="4">☆</span>
        <span class="star" data-value="5">☆</span>
      </div>
      <input type="hidden" name="note_mentor" id="noteMentor" value="0">
      <p class="rating-label" id="ratingLabel">Sélectionnez une note</p>
      <textarea class="rating-comment" name="commentaire" placeholder="Laissez un commentaire (optionnel)" rows="3"></textarea>
      <button type="submit" name="resoudre" class="primary-btn submit-rating">Envoyer la note</button>
    </div>
  </div>
  </form>

  <div class="modal-overlay" id="modifierModal">
    <div class="modal-content">
      <button class="modal-close" onclick="document.getElementById('modifierModal').classList.remove('show')">&times;</button>
      <h2>Modifier la demande</h2>
      <form method="POST" action="modifier_demande.php">
        <input type="hidden" name="id_demande" value="">
        <div class="modal-body">
          <label class="modal-label">Titre</label>
          <input class="modal-input" type="text" name="titre" required>
          <label class="modal-label">Description</label>
          <textarea class="modal-input" name="description" rows="3" required></textarea>
          <label class="modal-label">Tags (séparés par des virgules)</label>
          <input class="modal-input" type="text" name="tags" placeholder="Ex: React, JavaScript">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="document.getElementById('modifierModal').classList.remove('show')">Annuler</button>
          <button type="submit" name="modifier" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModifier(btn) {
      const article = btn.closest('.demande-card');
      document.querySelector('#modifierModal [name="id_demande"]').value = article.dataset.id_demande;
      document.querySelector('#modifierModal [name="titre"]').value = article.dataset.titre;
      document.querySelector('#modifierModal [name="description"]').value = article.dataset.description;
      document.querySelector('#modifierModal [name="tags"]').value = article.dataset.tags;
      document.getElementById('modifierModal').classList.add('show');
    }

    let selectedRating = 0;
    const ratingModal = document.getElementById('ratingModal');
    const stars = document.querySelectorAll('#starRating .star');
    const ratingLabel = document.getElementById('ratingLabel');
    const noteInput = document.getElementById('noteMentor');

    function openRating(id) {
      document.getElementById('ratingIdDemande').value = id;
      selectedRating = 0;
      noteInput.value = 0;
      stars.forEach(s => s.textContent = '☆');
      ratingLabel.textContent = 'Sélectionnez une note';
      ratingModal.style.display = 'flex';
    }

    document.getElementById('closeModal').addEventListener('click', () => {
      ratingModal.style.display = 'none';
    });
    ratingModal.addEventListener('click', (e) => {
      if (e.target === ratingModal) ratingModal.style.display = 'none';
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') ratingModal.style.display = 'none';
    });

    stars.forEach(star => {
      star.addEventListener('mouseenter', () => {
        const val = parseInt(star.dataset.value);
        stars.forEach((s, i) => s.textContent = i < val ? '★' : '☆');
      });
      star.addEventListener('mouseleave', () => {
        stars.forEach((s, i) => s.textContent = i < selectedRating ? '★' : '☆');
      });
      star.addEventListener('click', () => {
        selectedRating = parseInt(star.dataset.value);
        noteInput.value = selectedRating;
        stars.forEach((s, i) => s.textContent = i < selectedRating ? '★' : '☆');
        const labels = ['', 'Très insuffisant', 'Insuffisant', 'Moyen', 'Bien', 'Excellent'];
        ratingLabel.textContent = labels[selectedRating];
      });
    });

    document.querySelector('#ratingModal .submit-rating').addEventListener('click', (e) => {
      if (selectedRating === 0) {
        e.preventDefault();
        ratingLabel.textContent = 'Veuillez sélectionner une note';
      }
    });
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>
</html>
