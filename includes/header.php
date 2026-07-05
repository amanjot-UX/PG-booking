<?php $user = getCurrentUser(); ?>
<header class="site-header" id="site-header">
  <div class="container header-inner">
    <a href="index.php" class="logo">
      <span class="logo-icon">🏠</span>
      <span class="logo-text">Stay<em>Nest</em></span>
    </a>

    <nav class="main-nav">
      <a href="listings.php">Browse</a>
      <a href="listings.php?type=pg">PG / Hostel</a>
      <a href="listings.php?type=flat">Flats</a>
      <a href="post-property.php">List Property</a>
    </nav>

    <div class="header-actions">
      <?php if ($user): ?>
        <a href="dashboard.php" class="btn-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></a>
        <a href="auth.php?action=logout" class="btn-ghost">Logout</a>
      <?php else: ?>
        <a href="auth.php?action=login" class="btn-ghost">Login</a>
        <a href="auth.php?action=register" class="btn-primary">Sign Up Free</a>
      <?php endif; ?>
    </div>

    <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- Mobile Nav -->
  <nav class="mobile-nav" id="mobile-nav">
    <a href="listings.php">Browse Properties</a>
    <a href="listings.php?type=pg">PG / Hostel</a>
    <a href="listings.php?type=flat">Flats &amp; Apartments</a>
    <a href="post-property.php">List Your Property</a>
    <?php if ($user): ?>
    <a href="dashboard.php">My Dashboard</a>
    <a href="auth.php?action=logout">Logout</a>
    <?php else: ?>
    <a href="auth.php?action=login">Login</a>
    <a href="auth.php?action=register">Sign Up Free</a>
    <?php endif; ?>
  </nav>
</header>
