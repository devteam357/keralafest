event-management/
├── config/
│   ├── database.php         # Database connection
│   ├── settings.php         # Central configuration (URLs, timezone, etc.)
│   └── email_config.php    # Email SMTP settings
├── admin/
│   ├── index.php           # Dashboard with metrics
│   ├── attendees.php       # Attendee management
│   ├── scan.php            # QR code scanner page
│   ├── transactions.php    # Payment transactions
│   ├── export.php          # CSV export
│   ├── print_list.php     # Printable attendee list
│   └── login.php           # Admin authentication
├── assets/
│   ├── css/
│   │   └── style.css       # Centralized styling
│   ├── js/
│   │   └── main.js        # JavaScript functions
│   └── images/             # Logos, sliders
├── includes/
│   ├── functions.php       # Reusable PHP functions
│   ├── qr_generator.php    # QR code generation
│   └── email_templates.php # Email template functions
├── api/
│   ├── register.php        # Registration endpoint
│   ├── payment_webhook.php # Razorpay webhook
│   └── scan_qr.php        # QR scanning endpoint
├── index.php              # Main event page
├── thank-you.php          # Post-registration page
└── .htaccess             # URL rewriting & security