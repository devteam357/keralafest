<?php
session_start();
require_once 'config/database.php';
require_once 'config/settings.php';

// Test mode values
$test_values = array(
    'name' => AUTO_FILL_FORMS ? 'Test User' : '',
    'email' => AUTO_FILL_FORMS ? 'test@example.com' : '',
    'phone' => AUTO_FILL_FORMS ? '9876543210' : ''
);

// Display any error messages
$error_message = '';
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerala Food Fest 2025 - Prathishta | God's Own Country Unfolds</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Teachers&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Colors from banner */
            --primary-green: #0b7938;
            --kerala-green: #2e7d32;
            --warm-cream: #f5e6d3;
            --sunset-orange: #ff6b35;
            --deep-red: #c62828;
            --gold: #ffc107;
            --dark-text: #2c2c2c;
            --light-shadow: rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Teachers', sans-serif;
            color: var(--dark-text);
            overflow-x: hidden;
            background: #ffffff;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Marcellus', serif;
        }
        
        /* Header Navigation */
        .header {
            background: rgba(255, 255, 255, 0.98);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px var(--light-shadow);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-text {
            font-family: 'Marcellus', serif;
            font-size: 1.5rem;
            color: var(--primary-green);
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-links a {
            color: var(--dark-text);
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }
        
        .nav-links a:hover {
            color: var(--primary-green);
        }
        
        .mobile-menu {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary-green);
        }
        
        /* Hero Banner Section */
        .hero-banner {
            margin-top: 70px;
            background: linear-gradient(180deg, var(--warm-cream) 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
            min-height: 600px;
        }
        
        .banner-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
            position: relative;
        }
        
        .banner-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .banner-left {
            z-index: 2;
        }
        
        .edition-badge {
            display: inline-block;
            background: var(--deep-red);
            color: white;
            padding: 0.5rem 1.5rem;
            font-family: 'Marcellus', serif;
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .banner-title {
            font-size: 4rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
            line-height: 1.1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .banner-title .fest {
            color: var(--deep-red);
        }
        
        .banner-subtitle {
            font-size: 1.8rem;
            color: var(--kerala-green);
            margin-bottom: 1rem;
            font-style: italic;
        }
        
        .tagline {
            font-size: 1.2rem;
            color: var(--dark-text);
            margin-bottom: 2rem;
            font-weight: 500;
        }
        
        .event-dates {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .date-box {
            background: var(--deep-red);
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            font-family: 'Marcellus', serif;
            min-width: 60px;
        }
        
        .event-info {
            font-size: 1.1rem;
            color: var(--dark-text);
            margin-bottom: 1rem;
        }
        
        .event-info strong {
            color: var(--primary-green);
        }
        
        .days-highlight {
            background: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            display: inline-block;
            margin-top: 1rem;
            box-shadow: 0 5px 20px var(--light-shadow);
        }
        
        .days-highlight h3 {
            color: var(--deep-red);
            font-size: 1.5rem;
            margin: 0;
        }
        
        .days-highlight p {
            color: var(--primary-green);
            font-size: 1.1rem;
            margin: 0;
            font-weight: 600;
        }
        
        .banner-image {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .kerala-map {
            position: absolute;
            right: -50px;
            top: 50%;
            transform: translateY(-50%);
            width: 400px;
            height: 400px;
            background: var(--primary-green);
            opacity: 0.1;
            clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
        }
        
        /* Registration Section */
        .registration-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--gold);
        }
        
        .registration-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
        
        .form-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--light-shadow);
        }
        
        .form-card h3 {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Teachers', sans-serif;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(11, 121, 56, 0.1);
        }
        
        .btn-register {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green), var(--kerala-green));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Marcellus', serif;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(11, 121, 56, 0.3);
        }
        
        .event-details-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--light-shadow);
        }
        
        .event-details-card h3 {
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--gold);
        }
        
        /* Food Highlights Section */
        .food-section {
            padding: 4rem 0;
            background: white;
        }
        
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .food-card {
            background: linear-gradient(135deg, #fff, var(--warm-cream));
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .food-card:hover {
            transform: translateY(-10px);
            border-color: var(--gold);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.3);
        }
        
        .food-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .food-card h4 {
            color: var(--primary-green);
            margin-bottom: 1rem;
        }
        
        /* Sponsors Section */
        .sponsors-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, var(--warm-cream), white);
        }
        
        .sponsor-tier {
            margin-bottom: 3rem;
        }
        
        .sponsor-tier h3 {
            text-align: center;
            color: var(--gold);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .sponsor-grid {
            display: grid;
            gap: 2rem;
            justify-items: center;
        }
        
        .title-sponsor {
            grid-template-columns: 1fr;
        }
        
        .gold-sponsors {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        
        .silver-sponsors {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        
        .sponsor-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px var(--light-shadow);
            transition: all 0.3s;
            text-align: center;
            width: 100%;
        }
        
        .sponsor-card:hover {
            transform: scale(1.05);
        }
        
        /* Footer */
        footer {
            background: var(--dark-text);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h4 {
            color: var(--gold);
            margin-bottom: 1rem;
        }
        
        .footer-section a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: var(--gold);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #444;
            color: #888;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: var(--primary-green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .social-links a:hover {
            background: var(--gold);
            transform: translateY(-5px);
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--deep-red);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .banner-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .banner-title {
                font-size: 2.5rem;
            }
            
            .banner-subtitle {
                font-size: 1.3rem;
            }
            
            .event-dates {
                flex-wrap: wrap;
            }
            
            .date-box {
                font-size: 1.2rem;
                padding: 0.8rem;
            }
            
            .registration-grid {
                grid-template-columns: 1fr;
            }
            
            .kerala-map {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <span class="logo-text">Kerala Food Fest</span>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#register">Register</a></li>
                <li><a href="#highlights">Highlights</a></li>
                <li><a href="#sponsors">Sponsors</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <button class="mobile-menu">☰</button>
        </nav>
    </header>

    <!-- Hero Banner Section -->
    <section class="hero-banner" id="home">
        <div class="banner-container">
            <div class="banner-content">
                <div class="banner-left">
                    <div class="edition-badge">2ND EDITION</div>
                    <h1 class="banner-title">
                        Kerala <span class="fest">Fest</span><br>
                        Prathishta
                    </h1>
                    <p class="banner-subtitle">God's Own Country Unfolds</p>
                    <p class="tagline">Experience the authentic taste and culture of Kerala in the heart of Bhopal</p>
                    
                    <div class="event-dates">
                        <div class="date-box">13</div>
                        <div class="date-box">14</div>
                        <div class="date-box">15</div>
                        <div class="date-box">16</div>
                    </div>
                    
                    <div class="event-info">
                        <p><strong>November 2025</strong></p>
                        <p>Bittan Market Ground, Bhopal</p>
                        <p style="margin-top: 1rem;">Organized by: <strong>Ulledam Malayalee Association, Bhopal</strong></p>
                        <p>Radio Partner: <strong style="color: var(--deep-red);">RED FM 93.5</strong></p>
                    </div>
                    
                    <div class="days-highlight">
                        <h3>4 DAYS</h3>
                        <p>OF MYSTIC KERALA IN BHOPAL</p>
                    </div>
                </div>
                
                <div class="banner-image">
                    <div class="kerala-map"></div>
                    <!-- Placeholder for cultural imagery -->
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section class="registration-section" id="register">
        <div class="container">
            <h2 class="section-title">Register for the Festival</h2>
            
            <?php if($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="registration-grid">
                <!-- Registration Form -->
                <div class="form-card">
                    <h3>Book Your Tickets</h3>
                    <form action="api/register.php" method="POST">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $test_values['name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $test_values['email']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo $test_values['phone']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Additional Attendees</label>
                            <select name="additional_count" id="additional_count" class="form-control">
                                <option value="0">Just Me</option>
                                <option value="1">1 Additional Person</option>
                                <option value="2">2 Additional Persons</option>
                                <option value="3">3 Additional Persons</option>
                                <option value="4">4 Additional Persons</option>
                                <option value="5">5 Additional Persons</option>
                            </select>
                        </div>
                        
                        <div id="additional_names"></div>
                        
                        <div style="background: var(--warm-cream); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                            <strong>Total Amount: ₹<span id="total_amount"><?php echo TICKET_PRICE; ?></span></strong>
                            <input type="hidden" name="amount" id="amount" value="<?php echo TICKET_PRICE; ?>">
                        </div>
                        
                        <button type="submit" class="btn-register">Proceed to Payment</button>
                    </form>
                </div>
                
                <!-- Event Details Card -->
                <div class="event-details-card">
                    <h3>Festival Highlights</h3>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🎭</span>
                        <div>
                            <strong>Cultural Performances</strong>
                            <p>Kathakali, Mohiniyattam, Theyyam & more</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🍛</span>
                        <div>
                            <strong>Authentic Cuisine</strong>
                            <p>50+ food stalls with traditional Kerala dishes</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🥋</span>
                        <div>
                            <strong>Kalaripayattu</strong>
                            <p>Ancient martial art demonstrations</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🥁</span>
                        <div>
                            <strong>Chenda Melam</strong>
                            <p>Traditional percussion ensemble</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🎪</span>
                        <div>
                            <strong>Craft Bazaar</strong>
                            <p>Handloom & handicrafts from Kerala</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-icon">🎤</span>
                        <div>
                            <strong>Live Entertainment</strong>
                            <p>Malayalam playback singers & bands</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Food Highlights Section -->
    <section class="food-section" id="highlights">
        <div class="container">
            <h2 class="section-title">Culinary Journey</h2>
            <div class="food-grid">
                <div class="food-card">
                    <div class="food-icon">🍛</div>
                    <h4>Traditional Feast</h4>
                    <p>Authentic Sadya served on banana leaf</p>
                </div>
                <div class="food-card">
                    <div class="food-icon">🐟</div>
                    <h4>Seafood Specials</h4>
                    <p>Fresh Kerala coastal delicacies</p>
                </div>
                <div class="food-card">
                    <div class="food-icon">🥥</div>
                    <h4>Coconut Delights</h4>
                    <p>Traditional coconut-based dishes</p>
                </div>
                <div class="food-card">
                    <div class="food-icon">☕</div>
                    <h4>Tea & Snacks</h4>
                    <p>Kerala tea with banana chips & more</p>
                </div>
                <div class="food-card">
                    <div class="food-icon">🍡</div>
                    <h4>Sweet Treats</h4>
                    <p>Payasam, Ela Ada & traditional sweets</p>
                </div>
                <div class="food-card">
                    <div class="food-icon">🌶️</div>
                    <h4>Spice Market</h4>
                    <p>Authentic Kerala spices for sale</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsors Section -->
    <section class="sponsors-section" id="sponsors">
        <div class="container">
            <h2 class="section-title">Our Sponsors</h2>
            
            <div class="sponsor-tier">
                <h3>Title Sponsor</h3>
                <div class="sponsor-grid title-sponsor">
                    <div class="sponsor-card">
                        <h4>Premium Sponsor Space</h4>
                    </div>
                </div>
            </div>
            
            <div class="sponsor-tier">
                <h3>Gold Sponsors</h3>
                <div class="sponsor-grid gold-sponsors">
                    <div class="sponsor-card">Gold Sponsor 1</div>
                    <div class="sponsor-card">Gold Sponsor 2</div>
                    <div class="sponsor-card">Gold Sponsor 3</div>
                </div>
            </div>
            
            <div class="sponsor-tier">
                <h3>Silver Sponsors</h3>
                <div class="sponsor-grid silver-sponsors">
                    <div class="sponsor-card">Silver 1</div>
                    <div class="sponsor-card">Silver 2</div>
                    <div class="sponsor-card">Silver 3</div>
                    <div class="sponsor-card">Silver 4</div>
                </div>
            </div>
            
            <div class="sponsor-tier">
                <h3>Radio Partner</h3>
                <div class="sponsor-grid title-sponsor">
                    <div class="sponsor-card" style="background: linear-gradient(135deg, #ff0000, #cc0000); color: white;">
                        <h4>RED FM 93.5</h4>
                        <p>Bajaate Raho!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Kerala Food Fest</h4>
                <p>Experience the authentic flavors and rich culture of Kerala at Central India's premier cultural festival.</p>
                <div class="social-links">
                    <a href="#">f</a>
                    <a href="#">t</a>
                    <a href="#">i</a>
                    <a href="#">y</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="#register">Register Now</a>
                <a href="#highlights">Food Menu</a>
                <a href="#sponsors">Sponsors</a>
                <a href="#">Gallery</a>
            </div>
            
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="terms-and-conditions.php">Terms & Conditions</a>
                <a href="privacy-policy.php">Privacy Policy</a>
                <a href="refund-policy.php">Return & Refund Policy</a>
                <a href="#">Disclaimer</a>
            </div>
            
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>📍 Bittan Market Ground, Bhopal</p>
                <p>📧 info@keralafest.in</p>
                <p>📱 +91 98765 43210</p>
                <p style="margin-top: 1rem;">#keralafest2025</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Kerala Food Fest. All rights reserved. | Organized by Ulledam Malayalee Association, Bhopal</p>
        </div>
    </footer>

    <script>
        // Set ticket price from PHP
        var TICKET_PRICE = <?php echo TICKET_PRICE; ?>;
        
        // Update amount based on additional attendees
        document.getElementById('additional_count').addEventListener('change', function() {
            var count = parseInt(this.value);
            var total = (count + 1) * TICKET_PRICE;
            document.getElementById('total_amount').innerHTML = total;
            document.getElementById('amount').value = total;
            
            // Add name fields for additional attendees
            var namesDiv = document.getElementById('additional_names');
            namesDiv.innerHTML = '';
            
            for(var i = 1; i <= count; i++) {
                var div = document.createElement('div');
                div.className = 'form-group';
                div.innerHTML = '<label>Attendee ' + i + ' Name</label>' +
                               '<input type="text" name="attendee_names[]" class="form-control" placeholder="Enter name">';
                namesDiv.appendChild(div);
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                var target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Mobile menu toggle
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            var nav = document.querySelector('.nav-links');
            nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
            nav.style.position = 'absolute';
            nav.style.top = '100%';
            nav.style.left = '0';
            nav.style.right = '0';
            nav.style.background = 'white';
            nav.style.flexDirection = 'column';
            nav.style.padding = '1rem';
            nav.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
        });
    </script>
</body>
</html>