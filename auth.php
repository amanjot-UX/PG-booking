<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$action = $_GET['action'] ?? 'login';

if ($action === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user && password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            // Mock login for demo
            if ($email && strlen($pass) >= 4) {
                $_SESSION['user_id'] = 1;
                $_SESSION['user'] = ['id'=>1,'name'=>'Demo User','email'=>$email,'role'=>'tenant'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Enter valid email and password (min 4 chars).';
            }
        }
    }

    if ($action === 'register') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $role  = $_POST['role'] ?? 'tenant';

        if (!$name || !$email || !$pass || strlen($pass) < 6) {
            $error = 'Please fill all fields. Password must be at least 6 characters.';
        } elseif ($db) {
            $check = $db->prepare("SELECT id FROM users WHERE email=?");
            $check->bind_param('s', $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = 'Email already registered.';
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name,email,phone,password,role,created_at) VALUES (?,?,?,?,?,NOW())");
                $stmt->bind_param('sssss', $name, $email, $phone, $hash, $role);
                $stmt->execute();
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user'] = ['id'=>$stmt->insert_id,'name'=>$name,'email'=>$email,'role'=>$role];
                header('Location: dashboard.php');
                exit;
            }
        } else {
            // Mock register
            $_SESSION['user_id'] = 1;
            $_SESSION['user'] = ['id'=>1,'name'=>$name,'email'=>$email,'role'=>$role];
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $action==='login'?'Login':'Create Account' ?> — StayNest</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<?php include 'includes/header.php'; ?>

<div class="auth-container">
  <div class="auth-left">
    <h2>Find Your <br><em>Perfect Home</em></h2>
    <p>Join thousands of happy tenants who found their ideal PG or flat on StayNest.</p>
    <ul class="auth-benefits">
      <li>✓ Zero brokerage fees</li>
      <li>✓ 12,000+ verified listings</li>
      <li>✓ Instant owner contact</li>
      <li>✓ Secure online booking</li>
    </ul>
  </div>

  <div class="auth-card">
    <div class="auth-tabs">
      <a href="?action=login" class="auth-tab <?= $action==='login'?'active':'' ?>">Login</a>
      <a href="?action=register" class="auth-tab <?= $action==='register'?'active':'' ?>">Sign Up</a>
    </div>

    <?php if ($error): ?><div class="form-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="form-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <?php if ($action === 'login'): ?>
    <form method="POST" class="auth-form">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>
      <button type="submit" class="btn-primary btn-full btn-large">Login to StayNest</button>
      <div class="auth-divider"><span>or continue with</span></div>
      <div class="social-auth">
        <button type="button" class="btn-social">G Google</button>
        <button type="button" class="btn-social">📱 OTP</button>
      </div>
    </form>

    <?php else: ?>
    <form method="POST" class="auth-form">
      <div class="form-group">
        <label>I am a</label>
        <div class="role-toggle">
          <label class="role-option">
            <input type="radio" name="role" value="tenant" checked>
            <span>🏠 Tenant</span>
          </label>
          <label class="role-option">
            <input type="radio" name="role" value="owner">
            <span>🔑 Owner</span>
          </label>
        </div>
      </div>
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" placeholder="Rahul Kumar" required>
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone" placeholder="+91 99999 99999">
      </div>
      <div class="form-group">
        <label>Password * (min 6 chars)</label>
        <input type="password" name="password" placeholder="••••••••" minlength="6" required>
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" required> I agree to the <a href="#">Terms of Service</a> &amp; <a href="#">Privacy Policy</a>
        </label>
      </div>
      <button type="submit" class="btn-primary btn-full btn-large">Create Free Account</button>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
