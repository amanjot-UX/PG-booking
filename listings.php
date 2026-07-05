<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$filters = [
    'city'   => $_GET['city']   ?? '',
    'type'   => $_GET['type']   ?? '',
    'gender' => $_GET['gender'] ?? '',
    'budget' => $_GET['budget'] ?? '',
    'search' => $_GET['search'] ?? '',
];
$sort = $_GET['sort'] ?? 'popular';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

$allProperties = getProperties($filters, 100);

// Sort
if ($sort === 'price_asc')  usort($allProperties, fn($a,$b) => $a['price'] <=> $b['price']);
if ($sort === 'price_desc') usort($allProperties, fn($a,$b) => $b['price'] <=> $a['price']);
if ($sort === 'rating')     usort($allProperties, fn($a,$b) => $b['rating'] <=> $a['rating']);

$total = count($allProperties);
$totalPages = max(1, ceil($total / $perPage));
$properties = array_slice($allProperties, ($page-1)*$perPage, $perPage);
$cities = getCities();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse Properties — StayNest</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="listings-page">
  <!-- Sidebar Filters -->
  <aside class="filters-sidebar" id="filters-sidebar">
    <div class="filters-header">
      <h2>Filters</h2>
      <a href="listings.php" class="clear-filters">Clear All</a>
    </div>

    <form method="GET" action="listings.php" id="filter-form">
      <!-- Search -->
      <div class="filter-group">
        <label>Search</label>
        <input type="text" name="search" placeholder="Area, locality, landmark..." value="<?= htmlspecialchars($filters['search']) ?>">
      </div>

      <!-- City -->
      <div class="filter-group">
        <label>City</label>
        <select name="city">
          <option value="">All Cities</option>
          <?php foreach ($cities as $c): ?>
          <option value="<?= $c ?>" <?= $filters['city']===$c?'selected':'' ?>><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Type -->
      <div class="filter-group">
        <label>Property Type</label>
        <?php foreach (['pg'=>'PG / Hostel','flat'=>'Flat / Apartment','studio'=>'Studio Room'] as $val=>$lbl): ?>
        <label class="checkbox-label">
          <input type="radio" name="type" value="<?= $val ?>" <?= $filters['type']===$val?'checked':'' ?>>
          <?= $lbl ?>
        </label>
        <?php endforeach; ?>
        <label class="checkbox-label">
          <input type="radio" name="type" value="" <?= $filters['type']===''?'checked':'' ?>> All Types
        </label>
      </div>

      <!-- Gender -->
      <div class="filter-group">
        <label>Suitable For</label>
        <?php foreach (['male'=>'Boys Only','female'=>'Girls Only','coed'=>'Co-ed'] as $val=>$lbl): ?>
        <label class="checkbox-label">
          <input type="radio" name="gender" value="<?= $val ?>" <?= $filters['gender']===$val?'checked':'' ?>>
          <?= $lbl ?>
        </label>
        <?php endforeach; ?>
        <label class="checkbox-label">
          <input type="radio" name="gender" value="" <?= $filters['gender']===''?'checked':'' ?>> Anyone
        </label>
      </div>

      <!-- Budget -->
      <div class="filter-group">
        <label>Max Budget: <strong id="budget-display">₹<?= $filters['budget'] ? number_format($filters['budget']) : '50,000' ?></strong></label>
        <input type="range" name="budget" id="budget-range" min="3000" max="50000" step="500"
               value="<?= $filters['budget'] ?: 50000 ?>"
               oninput="document.getElementById('budget-display').textContent='₹'+parseInt(this.value).toLocaleString('en-IN')">
      </div>

      <!-- Sort -->
      <div class="filter-group">
        <label>Sort By</label>
        <select name="sort">
          <option value="popular" <?= $sort==='popular'?'selected':'' ?>>Most Popular</option>
          <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
          <option value="rating" <?= $sort==='rating'?'selected':'' ?>>Highest Rated</option>
        </select>
      </div>

      <button type="submit" class="btn-primary btn-full">Apply Filters</button>
    </form>
  </aside>

  <!-- Main Content -->
  <main class="listings-main">
    <div class="listings-topbar">
      <div class="results-count">
        <strong><?= $total ?></strong> properties found
        <?php if ($filters['city']): ?> in <strong><?= htmlspecialchars($filters['city']) ?></strong><?php endif; ?>
      </div>
      <button class="btn-ghost btn-filters-mobile" id="toggle-filters">⚙ Filters</button>
    </div>

    <?php if (empty($properties)): ?>
    <div class="no-results">
      <div class="no-results-icon">🔍</div>
      <h3>No properties found</h3>
      <p>Try adjusting your filters or search in a different area.</p>
      <a href="listings.php" class="btn-primary">Clear Filters</a>
    </div>
    <?php else: ?>

    <div class="properties-grid">
      <?php foreach ($properties as $p): ?>
      <?php include 'includes/property-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>" class="page-btn">← Prev</a>
      <?php endif; ?>
      <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>" class="page-btn">Next →</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </main>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
