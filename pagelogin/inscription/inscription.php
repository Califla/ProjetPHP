<?php
require_once __DIR__ . '/../../database/config.php';

$err = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = trim($_POST['nom'] ?? '');
  $prenom = trim($_POST['prenom'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';
  $filiere = $_POST['filiere'] ?? '';
  $role = $_POST['role'] ?? '';
  $terms = isset($_POST['terms']);

  $old = compact('nom', 'prenom', 'email', 'filiere', 'role');

  if (empty($nom)) $err['nom'] = 'Le nom est obligatoire';
  if (empty($prenom)) $err['prenom'] = 'Le prénom est obligatoire';
  if (empty($email)) $err['email'] = "L'email est obligatoire";
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err['email'] = "L'email n'est pas valide";
  if (empty($password)) $err['password'] = 'Le mot de passe est obligatoire';
  elseif (strlen($password) < 6) $err['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
  if (empty($confirm_password)) $err['confirm_password'] = 'La confirmation est obligatoire';
  elseif ($password !== $confirm_password) $err['confirm_password'] = 'Les mots de passe ne correspondent pas';
  if (empty($role)) $err['role'] = 'Veuillez sélectionner un rôle';
  if (!$terms) $err['terms'] = 'Vous devez accepter les conditions d\'utilisation';

  if (empty($err)) {
    $stmt = $conn->prepare("SELECT id_user FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $err['email'] = 'Cet email est déjà utilisé';
    }
    $stmt->close();
  }

  if (empty($err)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $statut = 'actif';

    $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, motdepasse, role, filiere, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nom, $prenom, $email, $hashed_password, $role, $filiere, $statut);

    if ($stmt->execute()) {
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['role'] = $role;
      $_SESSION['nom'] = $nom;
      $_SESSION['prenom'] = $prenom;
      header("Location: ../connexion/index.php");
      exit;
    } else {
      $err['general'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Inscription</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="inscription.css">

</head>

<body>


  <div class="left">
    <div class="brand-wrapper">
      <div class="jsp">
        <div class="brand">
          <h1>ISMO-SkillSwap</h1>
          <p>Plateforme d'échange de compétences ISMO Tétouan</p>
        </div>
      </div>
    </div>
    <div class="features">
      <h2>Rejoignez la communauté</h2>
      <div class="feature-item">
        <div class="feature-icon"><svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg></div>
        <span class="feature-text">Créez votre profil de compétences</span>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg></div>
        <span class="feature-text">Partagez vos connaissances avec vos pairs</span>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg></div>
        <span class="feature-text">Développez votre expertise reconnue</span>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg></div>
        <span class="feature-text">Accédez à l'entraide collaborative</span>
      </div>
    </div>
    <p class="footer-text">© 2026 ISMO Tétouan – Institut Spécialisé dans les métiers de l'offshoring</p>
  </div>

  <main class="right">

    <div class="step <?= empty($err) ? 'active' : '' ?>" id="step-role">
      <div class="role-step">
        <h2>Inscription</h2>
        <p class="subtitle">Choisissez votre rôle pour commencer</p>

        <div class="role-cards">
          <div class="role-card" data-role="stagiaire" onclick="selectRole(this)">
            <div class="role-icon stagiaire">🎓</div>
            <div class="role-info">
              <h3>Stagiaire</h3>
              <p>Apprenez, échangez des compétences et progressez avec vos pairs</p>
            </div>
            <div class="role-check"></div>
          </div>

          <div class="role-card" data-role="formateur" onclick="selectRole(this)">
            <div class="role-icon formateur">👨‍🏫</div>
            <div class="role-info">
              <h3>Formateur</h3>
              <p>Encadrez les stagiaires et partagez votre expertise professionnelle</p>
            </div>
            <div class="role-check"></div>
          </div>

          <div class="role-card" data-role="admin" onclick="selectRole(this)">
            <div class="role-icon admin">🛡️</div>
            <div class="role-info">
              <h3>Administrateur</h3>
              <p>Gérez la plateforme, les filières et les utilisateurs ISMO</p>
            </div>
            <div class="role-check"></div>
          </div>
        </div>

        <button class="btn-continue" id="btn-continue" onclick="goToForm()">Continuer →</button>
        <p class="login-link" style="margin-top:20px">Déjà un compte ? <a href="../connexion/index.php">Se connecter</a></p>
      </div>
    </div>

    <div class="step <?= !empty($err) ? 'active' : '' ?>" id="step-form">
        <div class="card">
        <form method="POST" action="">
        <div class="card-header">
          <h2>Inscription</h2>
          <p class="subtitle">Créez votre compte SkillSwap</p>
        </div>

        <div class="role-badge" onclick="goBack()" title="Changer de rôle">
          <span id="badge-icon">🎓</span>
          <span id="badge-label">Stagiaire</span>
          <span style="margin-left:4px;opacity:0.6">✕</span>
        </div>

        <?php if (!empty($err['general'])): ?>
          <div class="error-msg" style="color:#e74c3c;margin-bottom:16px;padding:10px;background:#fde8e8;border-radius:8px;text-align:center"><?= htmlspecialchars($err['general']) ?></div>
        <?php endif; ?>

        <div class="row-2">
          <div class="field">
            <label>Nom</label>
            <input type="text" name="nom" placeholder="Nom de famille" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" />
            <?php if (!empty($err['nom'])): ?><span class="field-error"><?= htmlspecialchars($err['nom']) ?></span><?php endif; ?>
          </div>
          <div class="field">
            <label>Prénom</label>
            <input type="text" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" />
            <?php if (!empty($err['prenom'])): ?><span class="field-error"><?= htmlspecialchars($err['prenom']) ?></span><?php endif; ?>
          </div>
        </div>

        <div class="field">
          <label>Email</label>
          <input type="email" name="email" placeholder="votre.email@ismo.ma" value="<?= htmlspecialchars($old['email'] ?? '') ?>" />
          <?php if (!empty($err['email'])): ?><span class="field-error"><?= htmlspecialchars($err['email']) ?></span><?php endif; ?>
        </div>

        <div class="field filiere-field" id="filiere-field">
          <label>Filière</label>
          <div class="select-wrap">
            <select name="filiere">
              <option>DEV 101 – Développement Digital</option>
              <option>INF 201 – Infrastructure & Cloud</option>
              <option>CRM 301 – Relation Client & CRM</option>
              <option>DATA 401 – Data & Business Intelligence</option>
              <option>SEC 501 – Cybersécurité</option>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Mot de passe</label>
          <input type="password" name="password" placeholder="••••••••" />
          <?php if (!empty($err['password'])): ?><span class="field-error"><?= htmlspecialchars($err['password']) ?></span><?php endif; ?>
        </div>

        <div class="field">
          <label>Confirmer le mot de passe</label>
          <input type="password" name="confirm_password" placeholder="••••••••" />
          <?php if (!empty($err['confirm_password'])): ?><span class="field-error"><?= htmlspecialchars($err['confirm_password']) ?></span><?php endif; ?>
        </div>

        <div class="terms">
          <input type="checkbox" name="terms" id="terms-check" />
          <span>J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de
              confidentialité</a></span>
          <?php if (!empty($err['terms'])): ?><span class="field-error"><?= htmlspecialchars($err['terms']) ?></span><?php endif; ?>
        </div>

        <input type="hidden" name="role" id="role-input" value="" />
        <button type="submit" class="btn-primary" id="btn-create-account">Créer mon compte</button>
        <p class="login-link">Déjà un compte ? <a href="../connexion/index.php">Se connecter</a></p>
      </form>
      </div>
    </div>

  </main>

  <div class="help-btn">?</div>

  <script>
    let selectedRole = null;

    const roleLabels = {
      stagiaire: { label: 'Stagiaire', icon: '🎓' },
      formateur: { label: 'Formateur', icon: '👨‍🏫' },
      admin: { label: 'Administrateur', icon: '🛡️' }
    };

    function selectRole(card) {
      document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      selectedRole = card.dataset.role;
      const btn = document.getElementById('btn-continue');
      btn.classList.add('enabled');
    }
 
    function goToForm() {
      if (!selectedRole) return;

      document.getElementById('badge-icon').textContent = roleLabels[selectedRole].icon;
      document.getElementById('badge-label').textContent = roleLabels[selectedRole].label;
      document.getElementById('role-input').value = selectedRole;

      const filiereField = document.getElementById('filiere-field');
      filiereField.classList.toggle('hidden', selectedRole !== 'stagiaire');


      document.getElementById('step-role').classList.remove('active');
      document.getElementById('step-form').classList.add('active');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goBack() {
      document.getElementById('step-form').classList.remove('active');
      document.getElementById('step-role').classList.add('active');
    }
  </script>
</body>

</html>
