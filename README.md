# 🏠 StayNest — PG & Flat Booking Website

A full-featured PG and flat booking platform built with PHP, HTML, CSS, and JavaScript.

## Features

### For Tenants
- 🔍 Advanced search with city, type, gender, and budget filters
- 📋 Detailed property listings with photos, amenities, and location
- ❤️ Save/wishlist properties
- 📩 Send enquiries directly to owners
- 🔐 Secure login / registration system
- 📊 Personal dashboard with booking history

### For Owners
- 🏠 Post properties for free (multi-step form)
- 📸 Upload multiple photos
- 📈 View enquiries and listing analytics
- ✅ Listing verification system

### General
- 📱 Fully responsive (mobile-first design)
- 🌆 6 major Indian cities
- 🔒 Session-based authentication
- 💾 MySQL database with fallback mock data
- ⭐ Rating and review system

## Tech Stack
- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0 / MariaDB 10.6+
- **Frontend**: HTML5, CSS3 (custom design system), Vanilla JavaScript
- **Fonts**: Playfair Display + DM Sans (Google Fonts)
- **Architecture**: MVC-like with PHP includes

## Project Structure
```
pg-booking/
├── index.php              # Homepage
├── listings.php           # Browse properties with filters
├── property.php           # Property detail + booking form
├── auth.php               # Login / Register
├── post-property.php      # Owner: list a property
├── dashboard.php          # User dashboard
├── config/
│   ├── database.php       # DB connection
│   └── schema.sql         # MySQL schema + sample data
├── includes/
│   ├── functions.php      # Core functions + mock data
│   ├── header.php         # Site header
│   ├── footer.php         # Site footer
│   └── property-card.php  # Property card partial
└── assets/
    ├── css/style.css      # Complete stylesheet
    └── js/main.js         # JavaScript
```

## Installation

### Requirements
- PHP 8.0 or higher
- MySQL 8.0 / MariaDB 10.6 (optional — works with mock data)
- Apache / Nginx with mod_rewrite
- XAMPP / WAMP / MAMP for local development

### Steps

1. **Clone / copy files** to your web server root:
   ```
   htdocs/pg-booking/   (XAMPP)
   www/pg-booking/      (WAMP)
   ```

2. **Create the database** (optional):
   ```bash
   mysql -u root -p < config/schema.sql
   ```

3. **Configure database** in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'staynest');
   ```
   > **Note**: The site works without a database using built-in mock data!

4. **Visit** `http://localhost/pg-booking/`

### Without Database (Demo Mode)
The site runs fully without a database. All 8 sample properties are loaded from mock data in `includes/functions.php`. Login with any email + password (4+ chars).

## Pages
| URL | Description |
|-----|-------------|
| `/index.php` | Homepage with hero search, cities, featured listings |
| `/listings.php` | Browse with sidebar filters + pagination |
| `/property.php?id=1` | Property detail with booking form |
| `/auth.php?action=login` | Login page |
| `/auth.php?action=register` | Registration page |
| `/post-property.php` | Multi-step property listing form |
| `/dashboard.php` | User dashboard (bookings, saved, listings) |

## Customization
- **Colors**: Edit CSS variables in `assets/css/style.css` (`:root` block)
- **Cities**: Edit `getCities()` in `includes/functions.php`
- **Mock data**: Edit `getMockProperties()` in `includes/functions.php`
- **Amenities**: Edit the `$allAmenities` array in `post-property.php`

## License
MIT License — free for personal and commercial use.
