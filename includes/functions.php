<?php

// ── Mock data (used when DB is not connected) ──────────────────────────────
function getMockProperties() {
    return [
        ['id'=>1,'title'=>'Sunshine PG for Girls','type'=>'pg','gender'=>'female','city'=>'Bangalore','area'=>'Koramangala','price'=>8500,'rating'=>4.8,'reviews'=>124,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','AC','Meals','Laundry','CCTV'],'verified'=>true,'available'=>true,'description'=>'Cozy, well-maintained PG for working women. 24/7 security, homely meals, high-speed WiFi.'],
        ['id'=>2,'title'=>'Urban Nest 2BHK Flat','type'=>'flat','gender'=>'coed','city'=>'Bangalore','area'=>'Indiranagar','price'=>22000,'rating'=>4.6,'reviews'=>87,'beds'=>2,'baths'=>2,'furnished'=>'Semi Furnished','amenities'=>['WiFi','Parking','Gym','Power Backup'],'verified'=>true,'available'=>true,'description'=>'Modern 2BHK in prime Indiranagar location. Walking distance from metro station.'],
        ['id'=>3,'title'=>'Gents PG Near IT Park','type'=>'pg','gender'=>'male','city'=>'Pune','area'=>'Hinjewadi','price'=>7000,'rating'=>4.5,'reviews'=>203,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','Meals','CCTV','Laundry'],'verified'=>true,'available'=>true,'description'=>'Budget-friendly PG for IT professionals near Hinjewadi Phase 1.'],
        ['id'=>4,'title'=>'Luxury Studio Apartment','type'=>'studio','gender'=>'coed','city'=>'Mumbai','area'=>'Bandra West','price'=>18000,'rating'=>4.9,'reviews'=>56,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','AC','Housekeeping','Power Backup','Gym'],'verified'=>true,'available'=>false,'description'=>'Premium studio in the heart of Bandra. Ideal for young professionals.'],
        ['id'=>5,'title'=>'Cozy Girls Hostel','type'=>'pg','gender'=>'female','city'=>'Delhi','area'=>'Lajpat Nagar','price'=>9500,'rating'=>4.7,'reviews'=>178,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','Meals','AC','Laundry','CCTV'],'verified'=>true,'available'=>true,'description'=>'Safe and comfortable hostel for women in South Delhi. Strict security.'],
        ['id'=>6,'title'=>'3BHK Shared Apartment','type'=>'flat','gender'=>'male','city'=>'Hyderabad','area'=>'Gachibowli','price'=>12000,'rating'=>4.4,'reviews'=>92,'beds'=>3,'baths'=>2,'furnished'=>'Semi Furnished','amenities'=>['WiFi','Parking','Power Backup'],'verified'=>false,'available'=>true,'description'=> 'Spacious 3BHK flat for sharing. 3 single occupancy rooms with common areas.'],
        ['id'=>7,'title'=>'Premium Co-ed PG','type'=>'pg','gender'=>'coed','city'=>'Bangalore','area'=>'HSR Layout','price'=>11000,'rating'=>4.7,'reviews'=>145,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','Meals','AC','Gym','Laundry','CCTV'],'verified'=>true,'available'=>true,'description'=>'Modern co-living space with all amenities. Community events every weekend.'],
        ['id'=>8,'title'=>'Budget Studio Room','type'=>'studio','gender'=>'coed','city'=>'Chennai','area'=>'OMR','price'=>9000,'rating'=>4.3,'reviews'=>67,'beds'=>1,'baths'=>1,'furnished'=>'Fully Furnished','amenities'=>['WiFi','AC','Power Backup'],'verified'=>true,'available'=>true,'description'=>'Affordable studio near IT corridor in Chennai OMR.'],
    ];
}

// ── Public API ─────────────────────────────────────────────────────────────
function getProperties($filters = [], $limit = 100) {
    $db = getDB();
    if (!$db) {
        $data = getMockProperties();
        // Apply simple filters
        if (!empty($filters['city']))   $data = array_filter($data, fn($p) => strtolower($p['city']) === strtolower($filters['city']));
        if (!empty($filters['type']))   $data = array_filter($data, fn($p) => $p['type'] === $filters['type']);
        if (!empty($filters['gender'])) $data = array_filter($data, fn($p) => $p['gender'] === $filters['gender'] || $p['gender'] === 'coed');
        if (!empty($filters['budget'])) $data = array_filter($data, fn($p) => $p['price'] <= (int)$filters['budget']);
        if (!empty($filters['search'])) $data = array_filter($data, fn($p) => stripos($p['title'].$p['area'].$p['city'], $filters['search']) !== false);
        return array_values(array_slice($data, 0, $limit));
    }
    // Real DB query
    $sql = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    $types = '';
    if (!empty($filters['city']))   { $sql .= " AND city=?"; $params[] = $filters['city']; $types .= 's'; }
    if (!empty($filters['type']))   { $sql .= " AND type=?"; $params[] = $filters['type']; $types .= 's'; }
    if (!empty($filters['gender'])) { $sql .= " AND (gender=? OR gender='coed')"; $params[] = $filters['gender']; $types .= 's'; }
    if (!empty($filters['budget'])) { $sql .= " AND price<=?"; $params[] = (int)$filters['budget']; $types .= 'i'; }
    if (!empty($filters['search'])) { $sql .= " AND (title LIKE ? OR area LIKE ?)"; $s='%'.$filters['search'].'%'; $params[]=$s; $params[]=$s; $types.='ss'; }
    $sql .= " LIMIT ?"; $params[] = (int)$limit; $types .= 'i';
    $stmt = $db->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPropertyById($id) {
    $db = getDB();
    if (!$db) {
        foreach (getMockProperties() as $p) if ($p['id'] == $id) return $p;
        return null;
    }
    $stmt = $db->prepare("SELECT * FROM properties WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getCities() {
    return ['Bangalore','Mumbai','Delhi','Pune','Hyderabad','Chennai','Kolkata','Ahmedabad','Jaipur','Noida'];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    if (!$db) return $_SESSION['user'] ?? null;
    $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function saveBooking($data) {
    $db = getDB();
    if (!$db) {
        // Mock save
        $_SESSION['last_booking'] = $data;
        return rand(1000, 9999);
    }
    $stmt = $db->prepare("INSERT INTO bookings (user_id, property_id, checkin_date, checkout_date, name, email, phone, message, status, created_at) VALUES (?,?,?,?,?,?,?,?,'pending',NOW())");
    $stmt->bind_param('iissssss', $data['user_id'], $data['property_id'], $data['checkin'], $data['checkout'], $data['name'], $data['email'], $data['phone'], $data['message']);
    $stmt->execute();
    return $stmt->insert_id;
}

function amenityIcon($a) {
    $icons = ['WiFi'=>'📶','AC'=>'❄️','Meals'=>'🍽️','Laundry'=>'👕','CCTV'=>'📹','Parking'=>'🅿️','Gym'=>'💪','Power Backup'=>'⚡','Housekeeping'=>'🧹'];
    return $icons[$a] ?? '✓';
}
?>
