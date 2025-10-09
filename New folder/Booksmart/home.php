<?php
session_start();

// Debug mode: visit home.php?debug=1 after attempting login to see session/cookie state
$debug = (isset($_GET['debug']) && $_GET['debug'] === '1');

if (!isset($_SESSION['user_id'])) {
    if ($debug) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "home.php debug\n";
        echo "=================\n";
        echo "Session status: " . session_status() . "\n";
        echo "Session ID: " . session_id() . "\n";
        echo "\n";
        echo "\$_SESSION dump:\n";
        var_export($_SESSION);
        echo "\n\n\$_COOKIE dump:\n";
        var_export($_COOKIE);
        echo "\n\nHeaders sent: " . (headers_sent() ? 'yes' : 'no') . "\n";
        exit;
    }

    header('Location: login.html');
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'booksmart';
$username = 'root';
$password = '';

$pdo = null;
$books = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch first 6 books from database
    $stmt = $pdo->prepare("SELECT * FROM books LIMIT 6");
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If database connection fails, use fallback data
    error_log("Database error: " . $e->getMessage());
    
    // Fallback books data
    $books = [
        [
            'title' => 'The Silent Patient',
            'author' => 'Alex Michaelides',
            'cover_image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.5,
            'available' => true
        ],
        [
            'title' => 'Where the Crawdads Sing',
            'author' => 'Delia Owens',
            'cover_image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.8,
            'available' => true
        ],
        [
            'title' => 'Educated',
            'author' => 'Tara Westover',
            'cover_image' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.7,
            'available' => false
        ],
        [
            'title' => 'The Midnight Library',
            'author' => 'Matt Haig',
            'cover_image' => 'https://images.unsplash.com/photo-1621351183012-e2f9972dd9bf?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.2,
            'available' => true
        ],
        [
            'title' => 'The Hobbit',
            'author' => 'J.R.R. Tolkien',
            'cover_image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.9,
            'available' => true
        ],
        [
            'title' => '1984',
            'author' => 'George Orwell',
            'cover_image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80',
            'rating' => 4.6,
            'available' => true
        ]
    ];
}

// Fetch user data
$user = null;
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booksmart - Discover Your Next Favorite Book</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #4895ef;
            --secondary: #7209b7;
            --secondary-light: #b5179e;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --success: #4cc9f0;
            --warning: #ff9e00;
            --border-radius: 12px;
            --border-radius-lg: 20px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --box-shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf5 100%);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .logo {
            display: flex;
            align-items: center;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 28px;
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .logo i {
            margin-right: 10px;
            font-size: 32px;
            color: var(--secondary);
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--primary);
        }

        .nav-links a.active::after {
            width: 100%;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            padding: 12px 20px 12px 45px;
            border-radius: 30px;
            border: 1px solid var(--light-gray);
            background: var(--light);
            font-size: 1em;
            width: 300px;
            transition: var(--transition);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.15);
            width: 350px;
        }

        .search-bar i {
            position: absolute;
            left: 18px;
            color: var(--gray);
        }

        .profile {
            position: relative;
        }

        .profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .profile img:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .profile-dropdown {
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            min-width: 200px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-lg);
            padding: 15px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
            z-index: 100;
        }

        .profile:hover .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .profile-dropdown img {
            width: 70px;
            height: 70px;
            display: block;
            margin: 0 auto 15px;
        }

        .profile-dropdown h3 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .profile-dropdown a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: var(--dark);
            border-radius: 6px;
            transition: var(--transition);
        }

        .profile-dropdown a:hover {
            background: var(--light-gray);
            color: var(--primary);
            padding-left: 20px;
        }

        .profile-dropdown a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Hero Section */
        .hero {
            padding: 80px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #4361ee 0%, #7209b7 100%);
            color: white;
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.05"><path fill="white" d="M500,100 C700,50 900,150 900,350 C900,550 700,650 500,600 C300,650 100,550 100,350 C100,150 300,50 500,100 Z"/></svg>');
            background-size: cover;
        }

        .hero-content {
            max-width: 600px;
            z-index: 1;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        .hero-image {
            position: relative;
            z-index: 1;
        }

        .hero-image img {
            max-width: 500px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-lg);
            transform: perspective(1000px) rotateY(-10deg);
            transition: var(--transition);
        }

        .hero-image:hover img {
            transform: perspective(1000px) rotateY(-5deg) translateY(-10px);
        }

        /* Featured Books Section */
        .section {
            padding: 60px 5%;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .view-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .view-all:hover {
            gap: 12px;
        }

        /* Books Grid */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px;
        }

        .book-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--box-shadow-lg);
        }

        .book-cover {
            position: relative;
            overflow: hidden;
            height: 300px;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .book-card:hover .book-cover img {
            transform: scale(1.05);
        }

        .book-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            opacity: 0;
            transition: var(--transition);
        }

        .book-card:hover .book-overlay {
            opacity: 1;
        }

        .book-action {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .book-action:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .book-info {
            padding: 20px;
        }

        .book-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--dark);
            line-height: 1.3;
        }

        .book-author {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .book-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .book-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--warning);
        }

        .book-status {
            background: var(--light-gray);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-available {
            background: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }

        .no-books-message {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: var(--gray);
            font-size: 1.1rem;
        }

        /* Categories Section */
        .categories {
            background: var(--light);
            border-radius: var(--border-radius-lg);
            padding: 60px 5%;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px 20px;
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--primary);
            font-size: 24px;
        }

        .category-card h3 {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .category-card p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Testimonials */
        .testimonials {
            background: linear-gradient(135deg, #7209b7 0%, #4361ee 100%);
            color: white;
            padding: 80px 5%;
            border-radius: var(--border-radius-lg);
            margin: 60px 0;
        }

        .testimonials .section-title {
            color: white;
            text-align: center;
            margin-bottom: 50px;
        }

        .testimonials .section-title::after {
            left: 50%;
            transform: translateX(-50%);
            background: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 30px;
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .author-info h4 {
            margin-bottom: 5px;
        }

        .author-info p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Newsletter */
        .newsletter {
            background: var(--light);
            padding: 60px 5%;
            border-radius: var(--border-radius-lg);
            text-align: center;
            margin: 60px 0;
        }

        .newsletter h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .newsletter p {
            max-width: 600px;
            margin: 0 auto 30px;
            color: var(--gray);
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            gap: 10px;
        }

        .newsletter-form input {
            flex: 1;
            padding: 15px 20px;
            border-radius: 30px;
            border: 1px solid var(--light-gray);
            font-size: 1rem;
            transition: var(--transition);
        }

        .newsletter-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.15);
        }

        .newsletter-form button {
            padding: 15px 30px;
            border-radius: 30px;
            background: var(--primary);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-form button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 5% 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 30px;
            height: 2px;
            background: var(--primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 1100px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-content {
                margin-bottom: 40px;
            }
            
            .hero-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .search-bar input {
                width: 200px;
            }
            
            .search-bar input:focus {
                width: 250px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .section-title {
                font-size: 1.8rem;
            }
            
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 20px;
            }
            
            .book-cover {
                height: 240px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <a href="home.php" class="logo">
            <i class="fas fa-book-open"></i>
            Booksmart
        </a>
        
        <nav class="nav-links">
            <a href="home.php" class="active">Home</a>
            <a href="catalog.php">Catalog</a>
            <a href="#">My Books</a>
            <a href="#">Reviews</a>
            <a href="#">Community</a>
        </nav>
        
        <div class="header-actions">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search books, authors...">
            </div>
            
            <div class="profile">
                <img id="headerAvatar" src="<?php echo isset($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'https://i.pravatar.cc/150?img=32'; ?>" alt="Profile">
                <div class="profile-dropdown">
                    <img id="dropdownAvatar" src="<?php echo isset($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'https://i.pravatar.cc/150?img=32'; ?>" alt="Profile">
                    <h3 id="headerName"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                    <a href="profpage.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="#"><i class="fas fa-bookmark"></i> My Library</a>
                    <a href="#" id="openEditProfile"><i class="fas fa-edit"></i> Edit Profile</a>
                    <a href="#"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover Your Next Favorite Book</h1>
            <p>Explore thousands of books, track your reading, and connect with fellow book lovers in our vibrant community.</p>
            <div class="hero-buttons">
                <a href="catalog.php" class="btn btn-primary">Explore Catalog</a>
                <a href="#" class="btn btn-secondary">Join Community</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Books Collection">
        </div>
    </section>

    <!-- Featured Books -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Featured Books</h2>
            <a href="catalog.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="books-grid">
            <?php if(count($books) > 0): ?>
                <?php foreach($books as $book): ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="book-overlay">
                                <button class="book-action"><i class="fas fa-eye"></i></button>
                                <button class="book-action"><i class="fas fa-bookmark"></i></button>
                                <button class="book-action"><i class="fas fa-share-alt"></i></button>
                            </div>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
                            <div class="book-meta">
                                <div class="book-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo isset($book['rating']) ? number_format($book['rating'], 1) : '4.0'; ?></span>
                                </div>
                                <span class="book-status <?php echo (isset($book['available']) && $book['available']) ? 'status-available' : ''; ?>">
                                    <?php echo (isset($book['available']) && $book['available']) ? 'Available' : 'Borrowed'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-books-message">
                    <p>No books available at the moment. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories">
        <div class="section-header">
            <h2 class="section-title">Browse Categories</h2>
            <a href="catalog.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-magic"></i>
                </div>
                <h3>Fantasy</h3>
                <p>0 books</p>
            </div>
            
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-user-secret"></i>
                </div>
                <h3>Mystery</h3>
                <p>0 books</p>
            </div>
            
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>Romance</h3>
                <p>0 books</p>
            </div>
            
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>Sci-Fi</h3>
                <p>0 books</p>
            </div>
            
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Non-Fiction</h3>
                <p>0 books</p>
            </div>
            
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-theater-masks"></i>
                </div>
                <h3>Drama</h3>
                <p>0 books</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2 class="section-title">What Readers Say</h2>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"Booksmart has completely transformed my reading habits. The recommendations are spot on, and I've discovered so many amazing books I wouldn't have found otherwise."</p>
                <div class="testimonial-author">
                    <img src="https://www.bing.com/th/id/OSK.N1w4Pv9SwE61SuQ6XYAOwcsDeOnCgMPpyNtTZYC_Mmk?w=224&h=200&c=8&rs=1&qlt=90&o=6&pid=3.1&rm=2" alt="User">
                    <div class="author-info">
                        <h4>Boris Diaw</h4>
                        <p>Penzionisani košarkaš</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">"Stranica radi čvrsto,brzo se učitava knjige su uredno složene. Ako nešto i fula, to su male sitnice, al' ništa što ne može se popraviti."</p>
                <div class="testimonial-author">
                    <img src="https://th.bing.com/th/id/OIP.4yu0inWVd6hcux9Ge4KGDgHaE3?w=296&h=194&c=7&r=0&o=7&pid=1.7&rm=3" alt="User">
                    <div class="author-info">
                        <h4>Ramo Isak</h4>
                        <p>Ministar Odbrane</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">"I've tried many reading apps, but Booksmart stands out with its beautiful interface and powerful features. It's like having a personal librarian!"</p>
                <div class="testimonial-author">
                    <img src="https://th.bing.com/th?q=Darko+Lazic+Velika+Slika&w=120&h=120&c=1&rs=1&qlt=70&o=7&cb=1&pid=InlineBlock&rm=3&mkt=en-WW&cc=BA&setlang=en&adlt=moderate&t=1&mw=247" alt="User">
                    <div class="author-info">
                        <h4>Darko Lazić</h4>
                        <p>Pjevač</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <h2>Stay Updated</h2>
        <p>Subscribe to our newsletter to receive the latest book recommendations, news, and exclusive offers.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Your email address">
            <button type="submit">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Booksmart</h3>
                <p>Your personal library in the cloud. Discover, track, and share your reading journey with fellow book lovers.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>Explore</h3>
                <ul class="footer-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="catalog.php">Catalog</a></li>
                    <li><a href="#">My Books</a></li>
                    <li><a href="#">Reviews</a></li>
                    <li><a href="#">Community</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Account</h3>
                <ul class="footer-links">
                    <li><a href="profpage.php">Profile</a></li>
                    <li><a href="#">My Library</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="#">Help & Support</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Contact</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Book Street, City</li>
                    <li><i class="fas fa-phone"></i> +1 234 567 890</li>
                    <li><i class="fas fa-envelope"></i> info@booksmart.com</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 Booksmart. All rights reserved.</p>
        </div>
    </footer>

    <!-- Edit Profile Modal -->
    <div id="profileModal" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:2000;">
        <div style="background:white;padding:20px;border-radius:10px;max-width:480px;width:90%;">
            <h3>Edit Profile</h3>
            <form id="profileForm" action="profile_update.php" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:12px;">
                    <label>Avatar (jpg/png/gif)</label><br>
                    <input type="file" name="avatar" accept="image/*">
                </div>
                <div style="margin-bottom:12px;">
                    <label>Bio</label><br>
                    <textarea name="bio" id="bioField" rows="4" style="width:100%;"><?php echo isset($user['bio']) ? htmlspecialchars($user['bio']) : ''; ?></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" id="closeProfile">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Edit Profile Modal
    const openEdit = document.getElementById('openEditProfile');
    if (openEdit) {
        openEdit.addEventListener('click', function(e){
            e.preventDefault();
            const modal = document.getElementById('profileModal');
            if (modal) modal.style.display = 'flex';
        });
    }

    const closeBtn = document.getElementById('closeProfile');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(){
            const modal = document.getElementById('profileModal');
            if (modal) modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('profileModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    </script>
</body>
</html>