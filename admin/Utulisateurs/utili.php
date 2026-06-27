<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../../pagelogin/connexion/index.php');
  exit();
}
include('../../database/config.php');
$messageSucces = $_GET['msucc'] ?? '';
$messageErreur = $_GET['merr'] ?? '';
$rolesAutorises = ['stagiaire', 'formateur', 'mentor', 'admin'];
$roleFiltre = $_GET['role'] ?? '';
$statutFiltre = $_GET['statut'] ?? '';
$liste_des_statuts = ['actif', 'en_attente', 'suspendu'];
if(!in_array($statutFiltre,$liste_des_statuts)){
  $statutFiltre='';
}
if (!in_array($roleFiltre, $rolesAutorises)) {
  $roleFiltre = '';
}

if ($roleFiltre) {
  $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE role = ? ORDER BY date_inscription DESC");
  $stmt->execute([$roleFiltre]);
} 
elseif ($statutFiltre) {
  $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE statut = ? ORDER BY date_inscription DESC");
  $stmt->execute([$statutFiltre]);
}else {
  $stmt = $db->query("SELECT * FROM utilisateurs ORDER BY date_inscription DESC");
}

$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($utilisateurs);
$actifs = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE statut = 'actif'")->fetchColumn();
$attente = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE statut = 'en_attente'")->fetchColumn();
$suspendus = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE statut = 'suspendu'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="utili.css" />
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
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
        <div class="nav-item active" onclick="location.href='../Utulisateurs/utili.php'">
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
      <div class="user-pill" data-email="<?php echo $_SESSION['email']; ?>">
        <div class="user-info">
          <div class="user-name"><?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom']; ?></div>
          <div class="user-role"><?php echo $_SESSION['role']; ?></div>
        </div>
        <?php
        if ($_SESSION['photo']) {
          echo '<img class="user-avatar" src="../../pagelogin/photo/' . $_SESSION['photo'] . '" alt="Photo de profil">';
        } else {
          echo '<div class="user-avatar">' . substr($_SESSION['nom'], 0, 1) . substr($_SESSION['prenom'], 0, 1) . '</div>';
        }
        ?>
      </div>
    </header>

    <section class="content-area">
      <div class="page-header">
        <div>
          <p class="eyebrow">Gestion des utilisateurs</p>
          <h1>Gérez les comptes et permissions</h1>
        </div>
      </div>
      <?php if ($messageSucces): ?>
        <div class="message-alert message-success">
          <span class="message-icon">✓</span>
          <div>
            <strong>Succès</strong>
            <p><?= htmlspecialchars($messageSucces) ?></p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($messageErreur): ?>
        <div class="message-alert message-error">
          <span class="message-icon">!</span>
          <div>
            <strong>Erreur</strong>
            <p><?= htmlspecialchars($messageErreur) ?></p>
          </div>
        </div>
      <?php endif; ?>

      <div class="stats-grid">
        <article class="stat-card">
          <span class="stat-label">Total utilisateurs</span>
          <strong><?= $total ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-label">Actifs</span>
          <strong><?= $actifs ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-label">En attente</span>
          <strong><?= $attente ?></strong>
        </article>
        <article class="stat-card stat-card-alert">
          <span class="stat-label">Suspendus</span>
          <strong><?= $suspendus ?></strong>
        </article>
      </div>

      <section class="table-card">
        <div class="table-header">
          <div class="search-wrapper">
            <span class="search-icon">🔍</span>
            <input type="search" placeholder="Rechercher par nom, email..." aria-label="Recherche utilisateurs" />
          </div>
          <form class="filter-row" method="get" action="utili.php">
            <label>
              <span class="visually-hidden">Filtrer par rôle</span>
              <select name="role" onchange="this.form.submit()">
                <option value="">Tous les rôles</option>
                <option value="stagiaire" <?= $roleFiltre === 'stagiaire' ? 'selected' : '' ?>>Stagiaire</option>
                <option value="formateur" <?= $roleFiltre === 'formateur' ? 'selected' : '' ?>>Formateur</option>
                <option value="mentor" <?= $roleFiltre === 'mentor' ? 'selected' : '' ?>>Mentor</option>
                <option value="admin" <?= $roleFiltre === 'admin' ? 'selected' : '' ?>>Administrateur</option>
              </select>
            </label>
            <label>
              <span class="visually-hidden">Filtrer par statut</span>
              <select name="statut" onchange="this.form.submit()">
                <option value=''>Tous les statuts</option>
                <option value="actif" <?= $statutFiltre=== 'actif' ? 'selected' : '' ?>>Actif</option>
                <option value="en_attente" <?= $statutFiltre  === 'en_attente' ? 'selected' : '' ?>>En Attente</option>
                <option value="suspendu" <?= $statutFiltre  === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
              </select>
            </label>
          </form>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Filière</th>
                <th>Statut</th>
                <th>Inscription</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($utilisateurs as $user): ?>
                <tr>
                  <td class="user-cell">
                    <div class="user-cell-inner">
                      <span class="user-avatar"><?= htmlspecialchars(strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1))) ?></span>
                      <div>
                        <strong><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></strong>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($user['email']) ?></td>
                  <td><span class="badge badge-soft"><?= htmlspecialchars($user['role']) ?></span></td>
                  <td><?= htmlspecialchars($user['filiere'] ?? '-') ?></td>
                  <td><span class="status status-success"><?= htmlspecialchars($user['statut']) ?></span></td>
                  <td><?= htmlspecialchars($user['date_inscription']) ?></td>
                  <td class="actions-cell">
                    <div class="actions-cell-inner">
                      <button class="btn btn-secondary"><a href="changer_statut.php?id=<?= $user['id_user'] ?>&statut=actif">accepter</a></button>
                      <button class="btn btn-danger"><a href="changer_statut.php?id=<?= $user['id_user'] ?>&statut=suspendu">Suspendre</a></button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>
