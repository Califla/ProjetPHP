<?php
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
$rememberedEmail = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  extract($_POST);
  $err = [];
  if (!isset($email) || empty($email))
    $err["email"] = "Veuillez remplir le champ email.";
  if (!isset($password) || empty($password))
    $err["password"] = "Veuillez remplir le champ mot de passe.";
  if (empty($err)) {
    include('../../database/config.php');
    $stmt = $db->prepare("SELECT * FROM utilisateurs  WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['motdepasse'])) {
      session_start();
      if ($user['statut'] === 'suspendu') {
        header("Location: index.php?error=Votre compte a été suspendu. Veuillez contacter l'administrateur.");
        exit();
      } elseif ($user['statut'] === 'en_attente') {
        header("Location: index.php?error=Votre compte est en attente de validation. Veuillez patienter ou contacter l'administrateur.");
        exit();
      }
      if (!empty($remember))
        setcookie('remember_email', $email, time() + 86400 * 30, '/', '', false, true);
      else
        setcookie('remember_email', '', time() - 3600, '/', '', false, true);
      $_SESSION['id_user'] = $user['id_user'];
      $_SESSION['nom'] = $user['nom'];
      $_SESSION['prenom'] = $user['prenom'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['score'] = $user['score'];
      $_SESSION['photo'] = $user['photo'];
      $_SESSION['filiere'] = $user['filiere'];
      $_SESSION['statut'] = $user['statut'];
      $_SESSION['note_moyenne'] = $user['note_moyenne'];
      $_SESSION['date_inscription'] = $user['date_inscription'];
      $redirect = match ($user['role']) {
        'stagiaire','mentor' => '../../stagiaire/tableaubord/index.php',
        'admin' => '../../admin/tableaubord/table.php',
        'formateur' => '../../formateur/tableaubord/tableaubord.php'
      };
      header("Location: $redirect?msg=Connexion réussie !");
      exit();
    } else {
      header("Location: index.php?error=Email ou mot de passe incorrect.");
      exit();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Connexion</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="connexion.css" />
</head>

<body>

  <aside class="left">
    <div class="jsp">
      <div class="brand">
        <h1>ISMO-SkillSwap</h1>
        <p>Plateforme d'échange de compétences ISMO Tétouan</p>
      </div>
    </div>
    <div class="features">
      <h2>Pourquoi SkillSwap ?</h2>
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Valorisez vos compétences et progressez ensemble</span>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Obtenez de l'aide de vos pairs en temps réel</span>
      </div>

      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Gagnez des badges et des points de reconnaissance</span>
      </div>

      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Construisez votre passeport de compétences</span>
      </div>
    </div>

    <p class="footer-text">© 2026 ISMO Tétouan – Institut Spécialisé dans les métiers de l'offshoring</p>
  </aside>

  <main class="right">
    <div class="card">
      <?php if ($msg): ?>
        <div id="toastMsg" class="toast success"><?php echo htmlspecialchars($msg); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div id="toastMsg" class="toast error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <h2>Connexion</h2>
      <p class="subtitle">Accédez à votre espace personnel</p>

      <div class="field">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="votre.email@ismo.ma" autocomplete="email" value="<?= htmlspecialchars($rememberedEmail) ?>" />
            <?php if (isset($err['email']))
              echo '<div style="color: red;">' . htmlspecialchars($err['email']) . '</div>'; ?>
          </div>
          <label for="password">Mot de passe</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="••••••••"
              autocomplete="current-password" />
            <?php if (isset($err['password']))
              echo '<div style="color: red;">' . htmlspecialchars($err['password']) . '</div>'; ?>
            <button type="button" class="toggle-pw" onclick="togglePassword('password', this)"
              aria-label="Afficher le mot de passe">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
      </div>

      <div class="row-options">
        <label class="remember">
          <input type="checkbox" name="remember" id="remember" <?= $rememberedEmail ? 'checked' : '' ?> />
          <span>Se souvenir de moi</span>
        </label>
        <a href="#" class="forgot">Mot de passe oublié ?</a>
      </div>

      <button class="btn-primary">Se connecter</button>

      <p class="register">Pas encore de compte ? <a href="../inscription/inscription.php">S'inscrire</a></p>

    </div>
    </form>
  </main>
  <div class="help-btn">?</div>

  <script>
    function dismissToast() {
      var t = document.getElementById('toastMsg');
      if (t) {
        t.classList.add('toast-hide');
        setTimeout(function () {
          t.remove();
        }, 400);
      }
    }
    <?php if ($msg || $error): ?>
      setTimeout(dismissToast, 4500);
    <?php endif; ?>
    function togglePassword(inputId, btnEl) {
      var inp = document.getElementById(inputId);
      if (inp.type === 'password') {
        inp.type = 'text';
        btnEl.classList.add('visible');
      } else {
        inp.type = 'password';
        btnEl.classList.remove('visible');
      }
    }
  </script>

</body>

</html>