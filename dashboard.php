<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: auth.php?action=login');
    exit;
}

$user = getCurrentUser();
$tab  = $_GET['tab'] ?? 'overview';

// Mock data for dashboard
$myBookings = [
    ['id'=>1,'property'=>'Sunshine PG for Girls','location'=>'Koramangala, Bangalore','checkin'=>'2024-02-01','status'=>'confirmed','price'=>8500],
    ['id'=>2,'property'=>'Urban Nest 2BHK Flat','location'=>'Indiranagar, Bangalore','checkin'=>'2024-03-15','status'=>'pending','price'=>22000],
];
$savedProperties = array_slice(getMockProperties(), 0, 4);
$myListings = ($user['role'] ?? 'tenant') === 'owner' ? array_slice(getMockProperties(), 0, 3) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — StayNest</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="dashboard-page">
  <!-- Sidebar -->
  <aside class="dashboard-sidebar">
    <div class="user-profile-card">
      <div class="user-avatar-big"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
      <h3><?= htmlspecialchars($user['name'] ?? 'User') ?></h3>
      <span class="user-role"><?= ucfirst($user['role'] ?? 'tenant') ?></span>
      <p class="user-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>

    <nav class="dash-nav">
      <a href="?tab=overview" class="dash-nav-item <?= $tab==='overview'?'active':'' ?>">📊 Overview</a>
      <a href="?tab=bookings" class="dash-nav-item <?= $tab==='bookings'?'active':'' ?>">📋 My Bookings</a>
      <a href="?tab=saved" class="dash-nav-item <?= $tab==='saved'?'active':'' ?>">❤️ Saved Properties</a>
      <?php if (($user['role'] ?? '') === 'owner'): ?>
      <a href="?tab=listings" class="dash-nav-item <?= $tab==='listings'?'active':'' ?>">🏠 My Listings</a>
      <?php endif; ?>
      <a href="?tab=profile" class="dash-nav-item <?= $tab==='profile'?'active':'' ?>">👤 Edit Profile</a>
      <a href="auth.php?action=logout" class="dash-nav-item logout">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="dashboard-main">

    <?php if ($tab === 'overview'): ?>
    <h1>Welcome back, <?= htmlspecialchars(explode(' ', $user['name'] ?? 'User')[0]) ?>! 👋</h1>
    <div class="dash-stats">
      <div class="dash-stat-card">
        <span class="stat-icon">📋</span>
        <div><strong><?= count($myBookings) ?></strong><span>Bookings</span></div>
      </div>
      <div class="dash-stat-card">
        <span class="stat-icon">❤️</span>
        <div><strong><?= count($savedProperties) ?></strong><span>Saved</span></div>
      </div>
      <?php if (($user['role'] ?? '') === 'owner'): ?>
      <div class="dash-stat-card">
        <span class="stat-icon">🏠</span>
        <div><strong><?= count($myListings) ?></strong><span>Listings</span></div>
      </div>
      <div class="dash-stat-card">
        <span class="stat-icon">👁</span>
        <div><strong>248</strong><span>Views This Week</span></div>
      </div>
      <?php endif; ?>
    </div>

    <div class="recent-activity">
      <h2>Recent Bookings</h2>
      <?php foreach (array_slice($myBookings, 0, 3) as $b): ?>
      <div class="activity-item">
        <div class="activity-icon">🏠</div>
        <div class="activity-info">
          <strong><?= htmlspecialchars($b['property']) ?></strong>
          <span><?= htmlspecialchars($b['location']) ?> · Check-in: <?= $b['checkin'] ?></span>
        </div>
        <span class="status-badge status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <?php elseif ($tab === 'bookings'): ?>
    <h1>My Bookings</h1>
    <div class="bookings-table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Property</th><th>Location</th><th>Check-in</th><th>Rent</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php foreach ($myBookings as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['property']) ?></td>
            <td><?= htmlspecialchars($b['location']) ?></td>
            <td><?= $b['checkin'] ?></td>
            <td>₹<?= number_format($b['price']) ?>/mo</td>
            <td><span class="status-badge status-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
            <td><a href="#" class="btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'saved'): ?>
    <h1>Saved Properties</h1>
    <div class="properties-grid">
      <?php foreach ($savedProperties as $p): ?>
      <?php include 'includes/property-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <?php elseif ($tab === 'listings' && ($user['role']??'') === 'owner'): ?>
    <div class="listings-header-row">
      <h1>My Listings</h1>
      <a href="post-property.php" class="btn-primary">+ Add New Listing</a>
    </div>
    <div class="properties-grid">
      <?php foreach ($myListings as $p): ?>
      <?php include 'includes/property-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <?php elseif ($tab === 'profile'): ?>
    <h1>Edit Profile</h1>
    <div class="profile-form-wrap">
      <form method="POST" class="post-form">
        <div class="form-grid-2">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>City</label>
            <select name="city">
              <?php foreach (getCities() as $c): ?>
              <option value="<?= $c ?>"><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>New Password (leave blank to keep)</label>
            <input type="password" name="password" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="••••••••">
          </div>
        </div>
        <button type="submit" class="btn-primary">Save Changes</button>
      </form>
    </div>
    <?php endif; ?>

  </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
