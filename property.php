<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$p = getPropertyById($id);
if (!$p) { header('Location: listings.php'); exit; }

$amenities = is_string($p['amenities']) ? json_decode($p['amenities'], true) : $p['amenities'];
$amenities = $amenities ?? [];
$typeLabels = ['pg'=>'PG / Hostel','flat'=>'Flat','studio'=>'Studio Room'];
$genderLabels = ['male'=>'Boys Only','female'=>'Girls Only','coed'=>'Co-ed'];

// Handle booking form submit
$bookingSuccess = false;
$bookingError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    if (!isLoggedIn() && empty($_POST['name'])) {
        $bookingError = 'Please fill in all required fields.';
    } else {
        $bData = [
            'user_id'     => $_SESSION['user_id'] ?? 0,
            'property_id' => $id,
            'checkin'     => $_POST['checkin'] ?? '',
            'checkout'    => $_POST['checkout'] ?? '',
            'name'        => $_POST['name'] ?? '',
            'email'       => $_POST['email'] ?? '',
            'phone'       => $_POST['phone'] ?? '',
            'message'     => $_POST['message'] ?? '',
        ];
        $bookingId = saveBooking($bData);
        if ($bookingId) $bookingSuccess = true;
        else $bookingError = 'Booking failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($p['title']) ?> — StayNest</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="property-page container">

  <!-- Breadcrumb -->
  <nav class="breadcrumb">
    <a href="index.php">Home</a> /
    <a href="listings.php">Properties</a> /
    <a href="listings.php?city=<?= urlencode($p['city']) ?>"><?= htmlspecialchars($p['city']) ?></a> /
    <span><?= htmlspecialchars($p['title']) ?></span>
  </nav>

  <!-- Gallery -->
  <div class="property-gallery">
    <div class="gallery-main <?= $p['type'] ?>-img gallery-hero">
      <div class="gallery-badge-group">
        <?php if ($p['verified']): ?><span class="badge-verified">✓ Verified</span><?php endif; ?>
        <span class="badge-type type-<?= $p['type'] ?>"><?= $typeLabels[$p['type']] ?? ucfirst($p['type']) ?></span>
        <?php if (!$p['available']): ?><span class="badge-unavailable">Not Available</span><?php endif; ?>
      </div>
    </div>
    <div class="gallery-thumbs">
      <?php for($i=1;$i<=4;$i++): ?>
      <div class="gallery-thumb <?= $p['type'] ?>-img thumb-<?= $i ?>"></div>
      <?php endfor; ?>
    </div>
  </div>

  <div class="property-content">
    <!-- Left: Details -->
    <div class="property-details">
      <div class="property-header">
        <div>
          <h1><?= htmlspecialchars($p['title']) ?></h1>
          <p class="property-location">📍 <?= htmlspecialchars($p['area']) ?>, <?= htmlspecialchars($p['city']) ?></p>
        </div>
        <div class="property-rating">
          <span class="big-star">★</span>
          <span class="big-rating"><?= number_format($p['rating'], 1) ?></span>
          <span class="rating-count"><?= $p['reviews'] ?> reviews</span>
        </div>
      </div>

      <!-- Quick Info -->
      <div class="quick-info-grid">
        <div class="qi-item">
          <span class="qi-icon">🏠</span>
          <div><strong><?= $typeLabels[$p['type']] ?? ucfirst($p['type']) ?></strong><span>Type</span></div>
        </div>
        <div class="qi-item">
          <span class="qi-icon">👥</span>
          <div><strong><?= $genderLabels[$p['gender']] ?? ucfirst($p['gender']) ?></strong><span>Suitable For</span></div>
        </div>
        <div class="qi-item">
          <span class="qi-icon">🛏</span>
          <div><strong><?= $p['beds'] ?> Bed<?= $p['beds']>1?'s':'' ?></strong><span>Rooms</span></div>
        </div>
        <div class="qi-item">
          <span class="qi-icon">🚿</span>
          <div><strong><?= $p['baths'] ?> Bath<?= $p['baths']>1?'s':'' ?></strong><span>Bathrooms</span></div>
        </div>
        <div class="qi-item">
          <span class="qi-icon">🪑</span>
          <div><strong><?= htmlspecialchars($p['furnished']) ?></strong><span>Furnishing</span></div>
        </div>
        <div class="qi-item">
          <span class="qi-icon"><?= $p['available'] ? '✅' : '❌' ?></span>
          <div><strong><?= $p['available'] ? 'Available' : 'Not Available' ?></strong><span>Status</span></div>
        </div>
      </div>

      <!-- Description -->
      <section class="detail-section">
        <h2>About This Property</h2>
        <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
      </section>

      <!-- Amenities -->
      <section class="detail-section">
        <h2>Amenities</h2>
        <div class="amenities-full-grid">
          <?php foreach ($amenities as $a): ?>
          <div class="amenity-item">
            <span class="amenity-icon"><?= amenityIcon($a) ?></span>
            <span><?= htmlspecialchars($a) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Rules -->
      <section class="detail-section">
        <h2>House Rules</h2>
        <ul class="rules-list">
          <li>✓ No smoking inside premises</li>
          <li>✓ Guests allowed till 9 PM</li>
          <li>✓ No loud music after 11 PM</li>
          <li>✓ ID proof mandatory</li>
          <li>✓ 1 month advance notice for vacating</li>
        </ul>
      </section>

      <!-- Map Placeholder -->
      <section class="detail-section">
        <h2>Location</h2>
        <div class="map-placeholder">
          <div class="map-mock">
            <span>📍 <?= htmlspecialchars($p['area']) ?>, <?= htmlspecialchars($p['city']) ?></span>
            <p style="margin-top:.5rem;font-size:.85rem;opacity:.7">Interactive map available on full deployment</p>
          </div>
        </div>
      </section>
    </div>

    <!-- Right: Booking Card -->
    <aside class="booking-sidebar">
      <div class="booking-card" id="booking-card">
        <div class="booking-price">
          <span class="price-big">₹<?= number_format($p['price']) ?></span>
          <span class="price-unit">/month</span>
        </div>

        <?php if ($bookingSuccess): ?>
        <div class="booking-success">
          <div class="success-icon">🎉</div>
          <h3>Enquiry Sent!</h3>
          <p>The owner will contact you within 24 hours. Check your email for confirmation.</p>
          <a href="listings.php" class="btn-primary btn-full">Browse More</a>
        </div>
        <?php else: ?>

        <?php if ($bookingError): ?>
        <div class="form-error"><?= htmlspecialchars($bookingError) ?></div>
        <?php endif; ?>

        <?php if (!$p['available']): ?>
        <div class="unavailable-notice">⚠️ This property is currently not available. You can still send an enquiry.</div>
        <?php endif; ?>

        <form method="POST" class="booking-form">
          <div class="form-row">
            <div class="form-group">
              <label>Move-in Date</label>
              <input type="date" name="checkin" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label>Duration</label>
              <select name="checkout">
                <option value="1_month">1 Month</option>
                <option value="3_months" selected>3 Months</option>
                <option value="6_months">6 Months</option>
                <option value="1_year">1 Year</option>
              </select>
            </div>
          </div>
          <?php if (!isLoggedIn()): ?>
          <div class="form-group">
            <label>Your Name *</label>
            <input type="text" name="name" placeholder="Full name" required>
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label>Phone *</label>
            <input type="tel" name="phone" placeholder="+91 99999 99999" required>
          </div>
          <?php endif; ?>
          <div class="form-group">
            <label>Message to Owner</label>
            <textarea name="message" rows="3" placeholder="Hi, I'm interested in this property..."></textarea>
          </div>
          <button type="submit" name="book" class="btn-primary btn-full btn-large">
            📩 Send Enquiry
          </button>
        </form>

        <div class="booking-meta">
          <span>🔒 Secure &amp; Free</span>
          <span>📞 Owner responds in &lt;24h</span>
        </div>

        <?php endif; ?>
      </div>

      <!-- Owner Card -->
      <div class="owner-card">
        <div class="owner-avatar">O</div>
        <div class="owner-info">
          <strong>Property Owner</strong>
          <span>Verified Owner</span>
          <span class="response-time">⚡ Usually responds in 2–4 hours</span>
        </div>
      </div>

      <!-- Share -->
      <div class="share-links">
        <span>Share:</span>
        <button onclick="navigator.share ? navigator.share({title:document.title,url:location.href}) : navigator.clipboard.writeText(location.href)" class="btn-share">🔗 Copy Link</button>
        <button class="btn-share">📤 WhatsApp</button>
      </div>
    </aside>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
