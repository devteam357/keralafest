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
        
        /* Row 0: Header Navigation */
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
        
        /* Row 1: Slider Section */
        .slider-section {
            margin-top: 70px;
            background: linear-gradient(180deg, var(--warm-cream) 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }
        
        .slider-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            height: 500px;
        }
        
        .slider-slide {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .slider-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
        }
        
        .slider-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 2rem;
        }
        
        .slider-content h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .slider-content p {
            font-size: 1.3rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        /* Row 2: Form and Event Details */
        .registration-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .registration-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
        
        /* Form Column */
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
        
        /* Date Selection Checkboxes */
        .date-selection {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .date-checkbox {
            position: relative;
        }
        
        .date-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
        }
        
        .date-checkbox label {
            display: block;
            padding: 1rem;
            background: var(--warm-cream);
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .date-checkbox input[type="checkbox"]:checked + label {
            background: var(--primary-green);
            color: white;
            border-color: var(--primary-green);
            transform: scale(1.05);
        }
        
        .date-checkbox label:hover {
            border-color: var(--primary-green);
        }
        
        .date-label-number {
            display: block;
            font-size: 1.5rem;
            font-family: 'Marcellus', serif;
            margin-bottom: 0.3rem;
        }
        
        .date-label-month {
            display: block;
            font-size: 0.9rem;
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
        
        .amount-display {
            background: var(--warm-cream);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        /* Event Details Column - 2ND EDITION Content */
        .event-details-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--light-shadow);
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
        
        .event-title {
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .event-title .fest {
            color: var(--deep-red);
        }
        
        .event-subtitle {
            font-size: 1.3rem;
            color: var(--kerala-green);
            margin-bottom: 1rem;
            font-style: italic;
        }
        
        .event-dates-display {
            display: flex;
            gap: 0.5rem;
            margin: 1.5rem 0;
        }
        
        .date-box-display {
            background: var(--deep-red);
            color: white;
            padding: 0.8rem;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            font-family: 'Marcellus', serif;
            min-width: 50px;
        }
        
        .event-info-text {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .event-info-text strong {
            color: var(--primary-green);
        }
        
        .days-highlight {
            background: var(--warm-cream);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            display: inline-block;
            margin-top: 1rem;
        }
        
        .days-highlight h3 {
            color: var(--deep-red);
            font-size: 1.3rem;
            margin: 0;
        }
        
        .days-highlight p {
            color: var(--primary-green);
            font-size: 1rem;
            margin: 0;
            font-weight: 600;
        }
        
        /* Row 3: Sponsors Section */
        .sponsors-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, var(--warm-cream), white);
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
        
        .sponsor-tier {
            margin-bottom: 2.5rem;
        }
        
        .sponsor-tier h3 {
            text-align: center;
            color: var(--gold);
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }
        
        .sponsor-grid {
            display: grid;
            gap: 2rem;
            justify-items: center;
        }
        
        .title-sponsor {
            grid-template-columns: 1fr;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .powered-by-sponsors {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            max-width: 800px;
            margin: 0 auto;
        }
        
        .official-partners {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        
        .supported-sponsors, .association-sponsors {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        
        .partner-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 2rem auto 0;
        }
        
        .sponsor-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px var(--light-shadow);
            transition: all 0.3s;
            text-align: center;
            width: 100%;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sponsor-card:hover {
            transform: scale(1.05);
        }
        
        .partner-card {
            background: linear-gradient(135deg, var(--deep-red), #a00000);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px var(--light-shadow);
            transition: all 0.3s;
            text-align: center;
        }
        
        .partner-card h4 {
            margin-bottom: 0.5rem;
        }
        
        /* Row 4: Festival Highlights */
        .highlights-section {
            padding: 4rem 0;
            background: white;
        }
        
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .highlight-card {
            background: linear-gradient(135deg, #fff, var(--warm-cream));
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .highlight-card:hover {
            transform: translateY(-10px);
            border-color: var(--gold);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.3);
        }
        
        .highlight-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .highlight-card h4 {
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }
        
        /* Row 5: Culinary Journey */
        .culinary-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        
        .culinary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .culinary-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 20px var(--light-shadow);
        }
        
        .culinary-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(11, 121, 56, 0.2);
        }
        
        .culinary-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .culinary-card h4 {
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }
        
        /* Row 6: Footer */
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
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .registration-grid {
                grid-template-columns: 1fr;
            }
            
            .highlights-grid,
            .culinary-grid {
                grid-template-columns: 1fr;
            }
            
            .date-selection {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .slider-content h2 {
                font-size: 2rem;
            }
            
            .event-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Row 0: Header Navigation -->
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <span class="logo-text">Kerala Food Fest</span>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#register">Register</a></li>
                <li><a href="#highlights">Highlights</a></li>
                <li><a href="#culinary">Culinary</a></li>
                <li><a href="#sponsors">Sponsors</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <button class="mobile-menu">☰</button>
        </nav>
    </header>

    <!-- Row 1: Slider Section (Desktop & Mobile) -->
    <section class="slider-section" id="home">
        <div class="slider-container">
            <div class="slider-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1596040033229-a0b8d39c68f8?w=1400');">
                <div class="slider-overlay"></div>
                <div class="slider-content">
                    <h2>Experience God's Own Country</h2>
                    <p>4 Days of Mystic Kerala Culture & Cuisine in Bhopal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 2: Form & 2ND EDITION Details -->
    <section class="registration-section" id="register">
        <div class="container">
            <div class="registration-grid">
                <!-- Left Column: Registration Form -->
                <div class="form-card">
                    <h3>Book Your Tickets</h3>
                    <form action="api/register.php" method="POST">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Select Date(s) to Attend *</label>
                            <div class="date-selection">
                                <div class="date-checkbox">
                                    <input type="checkbox" name="dates[]" value="2025-11-13" id="date13">
                                    <label for="date13">
                                        <span class="date-label-number">13</span>
                                        <span class="date-label-month">November</span>
                                    </label>
                                </div>
                                <div class="date-checkbox">
                                    <input type="checkbox" name="dates[]" value="2025-11-14" id="date14">
                                    <label for="date14">
                                        <span class="date-label-number">14</span>
                                        <span class="date-label-month">November</span>
                                    </label>
                                </div>
                                <div class="date-checkbox">
                                    <input type="checkbox" name="dates[]" value="2025-11-15" id="date15">
                                    <label for="date15">
                                        <span class="date-label-number">15</span>
                                        <span class="date-label-month">November</span>
                                    </label>
                                </div>
                                <div class="date-checkbox">
                                    <input type="checkbox" name="dates[]" value="2025-11-16" id="date16">
                                    <label for="date16">
                                        <span class="date-label-number">16</span>
                                        <span class="date-label-month">November</span>
                                    </label>
                                </div>
                            </div>
                            <div class="alert alert-info" style="margin-top: 0.5rem; font-size: 0.9rem;">
                                Please select at least one date to attend
                            </div>
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
                        
                        <div class="amount-display">
                            <strong>Total Amount: ₹<span id="total_amount">200</span></strong>
                            <input type="hidden" name="amount" id="amount" value="200">
                        </div>
                        
                        <button type="submit" class="btn-register">Proceed to Payment</button>
                    </form>
                </div>
                
                <!-- Right Column: 2ND EDITION Content -->
                <div class="event-details-card">
                    <div class="edition-badge">2ND EDITION</div>
                    <h1 class="event-title">
                        Kerala <span class="fest">Fest</span><br>
                        Prathishta
                    </h1>
                    <p class="event-subtitle">God's Own Country Unfolds</p>
                    <p style="margin-bottom: 1rem;">Experience the authentic taste and culture of Kerala in the heart of Bhopal</p>
                    
                    <div class="event-dates-display">
                        <div class="date-box-display">13</div>
                        <div class="date-box-display">14</div>
                        <div class="date-box-display">15</div>
                        <div class="date-box-display">16</div>
                    </div>
                    
                    <div class="event-info-text">
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
            </div>
        </div>
    </section>

    <!-- Row 3: Our Sponsors -->
    <section class="sponsors-section" id="sponsors">
        <div class="container">
            <h2 class="section-title">Our Sponsors</h2>
            
            <!-- Title Sponsor (1 Logo) -->
            <div class="sponsor-tier">
                <h3>Title Sponsor</h3>
                <div class="sponsor-grid title-sponsor">
                    <div class="sponsor-card">
                        <h4>Title Sponsor Logo</h4>
                    </div>
                </div>
            </div>
            
            <!-- Powered By (2 Logos) -->
            <div class="sponsor-tier">
                <h3>Powered By</h3>
                <div class="sponsor-grid powered-by-sponsors">
                    <div class="sponsor-card">Powered By 1</div>
                    <div class="sponsor-card">Powered By 2</div>
                </div>
            </div>
            
            <!-- Official Partner (4 Logos) -->
            <div class="sponsor-tier">
                <h3>Official Partners</h3>
                <div class="sponsor-grid official-partners">
                    <div class="sponsor-card">Partner 1</div>
                    <div class="sponsor-card">Partner 2</div>
                    <div class="sponsor-card">Partner 3</div>
                    <div class="sponsor-card">Partner 4</div>
                </div>
            </div>
            
            <!-- Supported By (N Logos) -->
            <div class="sponsor-tier">
                <h3>Supported By</h3>
                <div class="sponsor-grid supported-sponsors">
                    <div class="sponsor-card">Supported 1</div>
                    <div class="sponsor-card">Supported 2</div>
                    <div class="sponsor-card">Supported 3</div>
                    <div class="sponsor-card">Supported 4</div>
                </div>
            </div>
            
            <!-- In Association With (N Logos) -->
            <div class="sponsor-tier">
                <h3>In Association With</h3>
                <div class="sponsor-grid association-sponsors">
                    <div class="sponsor-card">Association 1</div>
                    <div class="sponsor-card">Association 2</div>
                    <div class="sponsor-card">Association 3</div>
                </div>
            </div>
            
            <!-- Partners Section -->
            <div class="sponsor-tier" style="margin-top: 3rem;">
                <h3>Partners</h3>
                <div class="partner-section">
                    <div class="partner-card">
                        <h4>Radio Partner</h4>
                        <p style="font-size: 1.5rem; margin: 0.5rem 0;">RED FM 93.5</p>
                        <p style="margin: 0;">Bajaate Raho!</p>
                    </div>
                    <div class="partner-card" style="background: linear-gradient(135deg, #1a73e8, #0d47a1);">
                        <h4>Digital Partner</h4>
                        <p style="font-size: 1.3rem; margin: 0.5rem 0;">The Conversation</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 4: Festival Highlights (6 boxes) -->
    <section class="highlights-section" id="highlights">
        <div class="container">
            <h2 class="section-title">Festival Highlights</h2>
            <div class="highlights-grid">
                <div class="highlight-card">
                    <div class="highlight-icon">🎭</div>
                    <h4>Cultural Performances</h4>
                    <p>Kathakali, Mohiniyattam, Theyyam & traditional dances</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🥋</div>
                    <h4>Kalaripayattu</h4>
                    <p>Ancient martial art demonstrations by experts</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🥁</div>
                    <h4>Chenda Melam</h4>
                    <p>Traditional percussion ensemble performances</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🎪</div>
                    <h4>Craft Bazaar</h4>
                    <p>Handloom & handicrafts directly from Kerala</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🎤</div>
                    <h4>Live Entertainment</h4>
                    <p>Malayalam playback singers and bands</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🎨</div>
                    <h4>Art Exhibitions</h4>
                    <p>Traditional Kerala paintings and murals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 5: Culinary Journey (6 boxes) -->
    <section class="culinary-section" id="culinary">
        <div class="container">
            <h2 class="section-title">Culinary Journey</h2>
            <div class="culinary-grid">
                <div class="culinary-card">
                    <div class="culinary-icon">🍛</div>
                    <h4>Traditional Feast</h4>
                    <p>Authentic Sadya served on banana leaf</p>
                </div>
                <div class="culinary-card">
                    <div class="culinary-icon">🐟</div>
                    <h4>Seafood Specials</h4>
                    <p>Fresh Kerala coastal delicacies</p>
                </div>
                <div class="culinary-card">
                    <div class="culinary-icon">🥥</div>
                    <h4>Coconut Delights</h4>
                    <p>Traditional coconut-based dishes</p>
                </div>
                <div class="culinary-card">
                    <div class="culinary-icon">☕</div>
                    <h4>Tea & Snacks</h4>
                    <p>Kerala tea with banana chips & more</p>
                </div>
                <div class="culinary-card">
                    <div class="culinary-icon">🍡</div>
                    <h4>Sweet Treats</h4>
                    <p>Payasam, Ela Ada & traditional sweets</p>
                </div>
                <div class="culinary-card">
                    <div class="culinary-icon">🌶️</div>
                    <h4>Spice Market</h4>
                    <p>Authentic Kerala spices for sale</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 6: Footer (Same as Old) -->
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
                <a href="#highlights">Festival Highlights</a>
                <a href="#culinary">Culinary Journey</a>
                <a href="#sponsors">Sponsors</a>
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
        // Set ticket price
        var TICKET_PRICE = 200;
        
        // Date selection validation
        const dateCheckboxes = document.querySelectorAll('input[name="dates[]"]');
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            const checkedDates = document.querySelectorAll('input[name="dates[]"]:checked');
            if (checkedDates.length === 0) {
                e.preventDefault();
                alert('Please select at least one date to attend');
                return false;
            }
        });
        
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
                div.innerHTML = '<label>Attendee ' + i + ' Name *</label>' +
                               '<input type="text" name="attendee_names[]" class="form-control" placeholder="Enter name" required>';
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
            if(nav.style.display === 'flex') {
                nav.style.display = 'none';
            } else {
                nav.style.display = 'flex';
                nav.style.position = 'absolute';
                nav.style.top = '100%';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.background = 'white';
                nav.style.flexDirection = 'column';
                nav.style.padding = '1rem';
                nav.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>