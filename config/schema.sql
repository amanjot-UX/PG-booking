-- =====================================================
-- StayNest Database Schema
-- Run this in your MySQL/MariaDB client
-- =====================================================

CREATE DATABASE IF NOT EXISTS staynest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE staynest;

-- ── Users ─────────────────────────────────────────────────
CREATE TABLE users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL UNIQUE,
  phone       VARCHAR(20),
  password    VARCHAR(255) NOT NULL,
  role        ENUM('tenant','owner','admin') DEFAULT 'tenant',
  avatar      VARCHAR(255),
  city        VARCHAR(100),
  verified    TINYINT(1) DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Properties ────────────────────────────────────────────
CREATE TABLE properties (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  type        ENUM('pg','flat','studio') NOT NULL,
  gender      ENUM('male','female','coed') DEFAULT 'coed',
  city        VARCHAR(100) NOT NULL,
  area        VARCHAR(150) NOT NULL,
  address     TEXT,
  price       INT NOT NULL,
  description TEXT,
  beds        TINYINT DEFAULT 1,
  baths       TINYINT DEFAULT 1,
  furnished   ENUM('Fully Furnished','Semi Furnished','Unfurnished') DEFAULT 'Fully Furnished',
  amenities   JSON,
  verified    TINYINT(1) DEFAULT 0,
  available   TINYINT(1) DEFAULT 1,
  rating      DECIMAL(2,1) DEFAULT 0.0,
  reviews     INT DEFAULT 0,
  owner_id    INT,
  views       INT DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Property Images ───────────────────────────────────────
CREATE TABLE property_images (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  url         VARCHAR(500) NOT NULL,
  is_primary  TINYINT(1) DEFAULT 0,
  sort_order  INT DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Bookings / Enquiries ──────────────────────────────────
CREATE TABLE bookings (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT,
  property_id  INT NOT NULL,
  checkin_date DATE,
  checkout     VARCHAR(20),
  name         VARCHAR(100),
  email        VARCHAR(150),
  phone        VARCHAR(20),
  message      TEXT,
  status       ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  token_paid   TINYINT(1) DEFAULT 0,
  amount       INT DEFAULT 0,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Saved / Wishlist ──────────────────────────────────────
CREATE TABLE saved_properties (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  property_id INT NOT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_save (user_id, property_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Reviews ───────────────────────────────────────────────
CREATE TABLE reviews (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  user_id     INT,
  rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  title       VARCHAR(200),
  body        TEXT,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Sample Data ───────────────────────────────────────────
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin User', 'admin@staynest.in', '9999999999', '$2y$10$example_hash', 'admin'),
('Demo Owner', 'owner@staynest.in', '9876543210', '$2y$10$example_hash', 'owner'),
('Demo Tenant', 'tenant@staynest.in', '9123456789', '$2y$10$example_hash', 'tenant');

INSERT INTO properties (title,type,gender,city,area,price,description,beds,baths,furnished,amenities,verified,available,rating,reviews,owner_id) VALUES
('Sunshine PG for Girls','pg','female','Bangalore','Koramangala',8500,'Cozy well-maintained PG for working women. 24/7 security, homely meals, high-speed WiFi.',1,1,'Fully Furnished','["WiFi","AC","Meals","Laundry","CCTV"]',1,1,4.8,124,2),
('Urban Nest 2BHK Flat','flat','coed','Bangalore','Indiranagar',22000,'Modern 2BHK in prime Indiranagar location. Walking distance from metro station.',2,2,'Semi Furnished','["WiFi","Parking","Gym","Power Backup"]',1,1,4.6,87,2),
('Gents PG Near IT Park','pg','male','Pune','Hinjewadi',7000,'Budget-friendly PG for IT professionals near Hinjewadi Phase 1.',1,1,'Fully Furnished','["WiFi","Meals","CCTV","Laundry"]',1,1,4.5,203,2),
('Luxury Studio Apartment','studio','coed','Mumbai','Bandra West',18000,'Premium studio in the heart of Bandra. Ideal for young professionals.',1,1,'Fully Furnished','["WiFi","AC","Housekeeping","Power Backup","Gym"]',1,0,4.9,56,2),
('Cozy Girls Hostel','pg','female','Delhi','Lajpat Nagar',9500,'Safe and comfortable hostel for women in South Delhi. Strict security.',1,1,'Fully Furnished','["WiFi","Meals","AC","Laundry","CCTV"]',1,1,4.7,178,2);
