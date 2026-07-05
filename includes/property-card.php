<?php
// Expects $p array with property data
if (!isset($p)) return;
$amenities = is_string($p['amenities']) ? json_decode($p['amenities'], true) : $p['amenities'];
$amenities = $amenities ?? [];
$typeColors = ['pg'=>'type-pg','flat'=>'type-flat','studio'=>'type-studio'];
$typeLabels = ['pg'=>'PG / Hostel','flat'=>'Flat','studio'=>'Studio'];
$genderIcons = ['male'=>'👦 Boys','female'=>'👧 Girls','coed'=>'👥 Co-ed'];
?>
<div class="property-card" data-type="<?= $p['type'] ?>">
  <div class="card-image">
    <div class="card-img-placeholder <?= $p['type'] ?>-img">
      <div class="img-overlay-content">
        <span><?= $typeLabels[$p['type']] ?? ucfirst($p['type']) ?></span>
      </div>
    </div>
    <?php if ($p['verified']): ?>
    <span class="badge-verified">✓ Verified</span>
    <?php endif; ?>
    <?php if (!$p['available']): ?>
    <div class="badge-unavailable">Not Available</div>
    <?php endif; ?>
    <span class="badge-type <?= $typeColors[$p['type']] ?? '' ?>"><?= $typeLabels[$p['type']] ?? ucfirst($p['type']) ?></span>
    <button class="btn-wishlist" data-id="<?= $p['id'] ?>" title="Save">♡</button>
  </div>

  <div class="card-body">
    <div class="card-meta">
      <span class="gender-tag"><?= $genderIcons[$p['gender']] ?? '' ?></span>
      <div class="rating">
        <span class="star">★</span>
        <span><?= number_format($p['rating'], 1) ?></span>
        <span class="review-count">(<?= $p['reviews'] ?>)</span>
      </div>
    </div>

    <h3 class="card-title"><a href="property.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
    <p class="card-location">📍 <?= htmlspecialchars($p['area']) ?>, <?= htmlspecialchars($p['city']) ?></p>

    <div class="card-amenities">
      <?php foreach (array_slice($amenities, 0, 4) as $a): ?>
      <span class="amenity-tag"><?= amenityIcon($a) ?> <?= htmlspecialchars($a) ?></span>
      <?php endforeach; ?>
      <?php if (count($amenities) > 4): ?>
      <span class="amenity-tag more">+<?= count($amenities)-4 ?> more</span>
      <?php endif; ?>
    </div>

    <div class="card-footer">
      <div class="card-price">
        <span class="price-amount">₹<?= number_format($p['price']) ?></span>
        <span class="price-period">/month</span>
      </div>
      <a href="property.php?id=<?= $p['id'] ?>" class="btn-view">View Details</a>
    </div>
  </div>
</div>
