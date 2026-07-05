<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$properties = getProperties([], 6);
$cities = getCities();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StayNest — Find Your Perfect PG & Flat</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <span class="hero-tag">Trusted by 50,000+ tenants</span>
    <h1>Find Your Perfect <br><em>PG &amp; Flat</em> in India</h1>
    <p>Verified listings. Zero brokerage. Move in today.</p>

    <div class="search-box">
      <form action="listings.php" method="GET" class="search-form">
        <div class="search-field">
          <label>City</label>
          <select name="city">
            <option value="">All Cities</option>
            <?php foreach ($cities as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="search-field">
          <label>Property Type</label>
          <select name="type">
            <option value="">All Types</option>
            <option value="pg">PG / Hostel</option>
            <option value="flat">Flat / Apartment</option>
            <option value="studio">Studio Room</option>
          </select>
        </div>
        <div class="search-field">
          <label>Budget (₹/month)</label>
          <select name="budget">
            <option value="">Any Budget</option>
            <option value="5000">Under ₹5,000</option>
            <option value="10000">Under ₹10,000</option>
            <option value="15000">Under ₹15,000</option>
            <option value="25000">Under ₹25,000</option>
          </select>
        </div>
        <div class="search-field">
          <label>For</label>
          <select name="gender">
            <option value="">Anyone</option>
            <option value="male">Boys</option>
            <option value="female">Girls</option>
            <option value="coed">Co-ed</option>
          </select>
        </div>
        <button type="submit" class="btn-search">Search <span>→</span></button>
      </form>
    </div>

    <div class="hero-stats">
      <div class="stat"><strong>12,400+</strong><span>Properties</span></div>
      <div class="stat"><strong>48</strong><span>Cities</span></div>
      <div class="stat"><strong>4.8★</strong><span>Avg Rating</span></div>
    </div>
  </div>
</section>

<!-- CITIES -->
<section class="section cities-section">
  <div class="container">
    <div class="section-header">
      <h2>Explore by City</h2>
      <a href="listings.php" class="link-all">View all cities →</a>
    </div>
    <div class="cities-grid">
      <?php
      $cityData = [
        ['name'=>'Bangalore','img'=>'bg-bangalore','count'=>3200,'tag'=>'Tech Hub'],
        ['name'=>'Mumbai','img'=>'bg-mumbai','count'=>2800,'tag'=>'Financial Capital'],
        ['name'=>'Delhi','img'=>'bg-delhi','count'=>2100,'tag'=>'Capital City'],
        ['name'=>'Pune','img'=>'bg-pune','count'=>1900,'tag'=>'Education Hub'],
        ['name'=>'Hyderabad','img'=>'bg-hyd','count'=>1600,'tag'=>'Cyberabad'],
        ['name'=>'Chennai','img'=>'bg-chennai','count'=>1200,'tag'=>'South Gateway'],
      ];
      foreach($cityData as $city): ?>
      <a href="listings.php?city=<?= urlencode($city['name']) ?>" class="city-card <?= $city['img'] ?>">
        <div class="city-overlay">
          <span class="city-tag"><?= $city['tag'] ?></span>
          <h3><?= $city['name'] ?></h3>
          <p><?= number_format($city['count']) ?>+ properties</p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED LISTINGS -->
<section class="section listings-section">
  <div class="container">
    <div class="section-header">
      <h2>Featured Properties</h2>
      <div class="filter-tabs">
        <button class="tab active" data-filter="all">All</button>
        <button class="tab" data-filter="pg">PG</button>
        <button class="tab" data-filter="flat">Flat</button>
        <button class="tab" data-filter="studio">Studio</button>
      </div>
    </div>
    <div class="properties-grid" id="properties-grid">
      <?php foreach ($properties as $p): ?>
      <?php include 'includes/property-card.php'; ?>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:2.5rem">
      <a href="listings.php" class="btn-outline">Browse All Properties</a>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section how-section">
  <div class="container">
    <h2 class="text-center">How StayNest Works</h2>
    <div class="steps-grid">
      <div class="step">
        <div class="step-num">01</div>
        <h3>Search & Filter</h3>
        <p>Enter your city, budget and preferences to find hundreds of verified properties.</p>
      </div>
      <div class="step-line"></div>
      <div class="step">
        <div class="step-num">02</div>
        <h3>Shortlist & Visit</h3>
        <p>Save your favourites, schedule visits online or call the owner directly.</p>
      </div>
      <div class="step-line"></div>
      <div class="step">
        <div class="step-num">03</div>
        <h3>Book & Move In</h3>
        <p>Pay the token amount securely online, sign the agreement and move in!</p>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section testimonials-section">
  <div class="container">
    <h2 class="text-center">What Tenants Say</h2>
    <div class="testimonials-grid">
      <?php
      $reviews = [
        ['name'=>'Priya S.','city'=>'Bangalore','text'=>'Found my PG within 2 days of searching. The filters are amazing and the owner was super responsive. Zero broker fees!','rating'=>5,'avatar'=>'P'],
        ['name'=>'Rahul M.','city'=>'Pune','text'=>'StayNest saved me from scammers. All properties are verified. Moved into a beautiful flat near my office.','rating'=>5,'avatar'=>'R'],
        ['name'=>'Anjali K.','city'=>'Delhi','text'=>'As a girl, safety was my priority. The ladies-only PG listings were exactly what I needed. Highly recommend!','rating'=>4,'avatar'=>'A'],
      ];
      foreach ($reviews as $r): ?>
      <div class="testimonial-card">
        <div class="stars"><?= str_repeat('★', $r['rating']) ?></div>
        <p>"<?= $r['text'] ?>"</p>
        <div class="reviewer">
          <div class="avatar"><?= $r['avatar'] ?></div>
          <div>
            <strong><?= $r['name'] ?></strong>
            <span><?= $r['city'] ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-grid">
      <div class="cta-card owner">
        <h3>List Your Property</h3>
        <p>Reach lakhs of tenants. Post for free. Get inquiries instantly.</p>
        <a href="post-property.php" class="btn-white">Post for Free →</a>
      </div>
      <div class="cta-card tenant">
        <h3>Looking for a Room?</h3>
        <p>Browse 12,000+ verified PGs and flats. No brokerage, no hidden fees.</p>
        <a href="listings.php" class="btn-white">Start Searching →</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
