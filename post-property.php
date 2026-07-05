<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $amenities = $_POST['amenities'] ?? [];
    $amenitiesJson = json_encode($amenities);

    if ($db) {
        $stmt = $db->prepare("INSERT INTO properties (title,type,gender,city,area,price,description,beds,baths,furnished,amenities,verified,available,rating,reviews,owner_id,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,0,1,0,0,?,NOW())");
        $ownerId = $_SESSION['user_id'] ?? 0;
        $stmt->bind_param('sssssssiiisi', $_POST['title'],$_POST['type'],$_POST['gender'],$_POST['city'],$_POST['area'],$_POST['price'],$_POST['description'],$_POST['beds'],$_POST['baths'],$_POST['furnished'],$amenitiesJson,$ownerId);
        if ($stmt->execute()) $success = true;
        else $error = 'Failed to save property.';
    } else {
        // Mock success
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Post Property Free — StayNest</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="post-property-page">
  <div class="post-hero">
    <h1>List Your Property <em>Free</em></h1>
    <p>Reach lakhs of genuine tenants. No hidden charges.</p>
    <div class="post-stats">
      <span>📈 500+ enquiries/day</span>
      <span>✅ Free listing</span>
      <span>⚡ Go live in 24hrs</span>
    </div>
  </div>

  <div class="container">
    <?php if ($success): ?>
    <div class="post-success-card">
      <div class="success-big-icon">🎉</div>
      <h2>Property Listed Successfully!</h2>
      <p>Your listing is under review and will be live within 24 hours. We'll notify you by email.</p>
      <div class="success-actions">
        <a href="post-property.php" class="btn-outline">List Another Property</a>
        <a href="dashboard.php" class="btn-primary">View My Listings</a>
      </div>
    </div>
    <?php else: ?>

    <?php if ($error): ?><div class="form-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="post-form">
      <!-- Step 1: Basic Info -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">1</span>
          <h2>Basic Information</h2>
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label>Property Title *</label>
            <input type="text" name="title" placeholder="e.g. Sunshine Girls PG in Koramangala" required maxlength="100">
          </div>
          <div class="form-group">
            <label>Property Type *</label>
            <select name="type" required>
              <option value="">Select Type</option>
              <option value="pg">PG / Hostel</option>
              <option value="flat">Flat / Apartment</option>
              <option value="studio">Studio Room</option>
            </select>
          </div>
          <div class="form-group">
            <label>City *</label>
            <select name="city" required>
              <option value="">Select City</option>
              <?php foreach (getCities() as $c): ?>
              <option value="<?= $c ?>"><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Locality / Area *</label>
            <input type="text" name="area" placeholder="e.g. Koramangala, BTM Layout" required>
          </div>
          <div class="form-group">
            <label>Monthly Rent (₹) *</label>
            <input type="number" name="price" placeholder="8500" min="1000" max="200000" required>
          </div>
          <div class="form-group">
            <label>Suitable For *</label>
            <select name="gender" required>
              <option value="coed">Co-ed (Anyone)</option>
              <option value="male">Boys Only</option>
              <option value="female">Girls Only</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Step 2: Room Details -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">2</span>
          <h2>Room Details</h2>
        </div>
        <div class="form-grid-3">
          <div class="form-group">
            <label>Bedrooms *</label>
            <select name="beds" required>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4+</option>
            </select>
          </div>
          <div class="form-group">
            <label>Bathrooms *</label>
            <select name="baths" required>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3+</option>
            </select>
          </div>
          <div class="form-group">
            <label>Furnishing *</label>
            <select name="furnished" required>
              <option value="Fully Furnished">Fully Furnished</option>
              <option value="Semi Furnished">Semi Furnished</option>
              <option value="Unfurnished">Unfurnished</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Step 3: Amenities -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">3</span>
          <h2>Amenities Provided</h2>
        </div>
        <div class="amenities-picker">
          <?php
          $allAmenities = ['WiFi','AC','Meals','Laundry','CCTV','Parking','Gym','Power Backup','Housekeeping','TV','Refrigerator','Geyser','Water Purifier','Study Table','Wardrobe'];
          foreach ($allAmenities as $a): ?>
          <label class="amenity-checkbox">
            <input type="checkbox" name="amenities[]" value="<?= $a ?>">
            <span><?= amenityIcon($a) ?> <?= $a ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Step 4: Description -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">4</span>
          <h2>Property Description</h2>
        </div>
        <div class="form-group">
          <label>Describe Your Property *</label>
          <textarea name="description" rows="5" placeholder="Describe the property, nearby landmarks, transport, special features..." required minlength="50"></textarea>
          <small>Minimum 50 characters. More detail = more enquiries!</small>
        </div>
      </div>

      <!-- Step 5: Photos -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">5</span>
          <h2>Property Photos</h2>
        </div>
        <div class="upload-area" id="upload-area">
          <input type="file" name="photos[]" id="photo-input" multiple accept="image/*" style="display:none">
          <div class="upload-placeholder" onclick="document.getElementById('photo-input').click()">
            <span class="upload-icon">📷</span>
            <p>Click to upload photos</p>
            <small>Upload up to 10 photos (JPG, PNG). Max 5MB each.</small>
          </div>
          <div id="photo-preview" class="photo-preview-grid"></div>
        </div>
      </div>

      <!-- Step 6: Contact -->
      <div class="form-section">
        <div class="form-section-title">
          <span class="step-badge">6</span>
          <h2>Your Contact Details</h2>
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label>Your Name *</label>
            <input type="text" name="owner_name" value="<?= htmlspecialchars(getCurrentUser()['name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Phone Number *</label>
            <input type="tel" name="owner_phone" placeholder="+91 99999 99999" required>
          </div>
          <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="owner_email" value="<?= htmlspecialchars(getCurrentUser()['email'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>WhatsApp (optional)</label>
            <input type="tel" name="owner_whatsapp" placeholder="+91 99999 99999">
          </div>
        </div>
      </div>

      <div class="form-submit-area">
        <div class="submit-note">
          <strong>🔒 Your listing is safe.</strong> We review every listing before it goes live to ensure quality and authenticity.
        </div>
        <button type="submit" class="btn-primary btn-large">🚀 Submit Property Listing</button>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
