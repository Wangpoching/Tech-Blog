<?php
  require('../conn.php');
  require('../utils.php');
  session_start();

  function handleLogin($conn) {
    $username = getValue($_POST, 'username');
    $password = getValue($_POST, 'password');
    if ($username === '') return '使用者名稱不可為空';
    if ($password === '') return '密碼不可為空';
    $sql = "SELECT * FROM `users` WHERE username = ?";
    $userResult = executeQuery($conn, $sql, 's', $username);
    if ($userResult->num_rows === 0) return '帳號密碼錯誤';
    $row = $userResult->fetch_assoc();
    if (!password_verify($password, $row['password_hash'])) return '帳號密碼錯誤';
    $_SESSION['admin_id'] = $row['id'];
    header('Location: index.php');
    exit();
  }

  $error = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = handleLogin($conn);
  }
?>
<!DOCTYPE html>
<html lang="en">
<?php
  $title = '後台登入';
  $localCSS = [
    'css/dist/admin-login.css',
  ];
  $extraCSS = ['https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Noto+Sans+TC:wght@400;500&display=swap'];
  require_once('../template/head.php');
?>
<body>
  <header>
      <div class="header__wrapper">
        <div class="site-name">Pocyun Wang</div>
        <div class="back-to-landing"><a href="../index.php">← View blog</a></div>
      </div>
    </header>
    <main>
      <div class="card">
        <div class="area-name">ADMIN AREA</div>
        <div class="welcome-text">Welcome Back</div>
        <div class="description">Sign in to manage your blog.</div>
        <?php if ($error): ?>
          <div class="error-box invisible">⚠ <span><?= escape($error) ?></span></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="login-form">
          <label>
            <div>Username</div>
            <input type="text" name="username" />
          </label>
          <label>
            <div>Password</div>
            <input type="password" name="password" />
          </label>
          <input type="submit" value="Sign in" />
        </form>
      </div>
    </main>
    <footer class="footer">Handcrafted by <span class="accent">Pocyun</span> for <span class="accent" id="years"></span> years · <div class="footer-right">
        <a href="https://github.com/yourname" target="_blank">GitHub</a>
        <a href="https://linkedin.com/in/yourname" target="_blank">LinkedIn</a>
      </div>
    </footer>
  <script src="../js/footer.js"></script>
  <script src="../js/error.js"></script>
</body>
</html>
