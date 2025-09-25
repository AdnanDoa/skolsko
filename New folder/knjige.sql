-- Create Database
CREATE DATABASE IF NOT EXISTS audiobook_platform;
USE audiobook_platform;

-- Authors Table
CREATE TABLE Authors (
    author_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    email VARCHAR(255)
);

-- Narrators Table
CREATE TABLE Narrators (
    narrator_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    bio TEXT
);

-- Genres Table
CREATE TABLE Genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Books Table (missing in original but implied by relationships)
CREATE TABLE Books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(500) NOT NULL,
    author_id INT,
    narrator_id INT,
    genre_id INT,
    description TEXT,
    duration INT,
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES Authors(author_id),
    FOREIGN KEY (narrator_id) REFERENCES Narrators(narrator_id),
    FOREIGN KEY (genre_id) REFERENCES Genres(genre_id)
);

-- Users Table
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subscription_type ENUM('free', 'premium', 'family') DEFAULT 'free',
    subscription_expiry DATE
);

-- Book_Files Table
CREATE TABLE Book_Files (
    file_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    file_type ENUM('mp3', 'wav', 'm4a', 'flac') NOT NULL,
    file_url VARCHAR(500) NOT NULL,
    file_size BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- User_Library Table (Composite Primary Key)
CREATE TABLE User_Library (
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('reading', 'completed', 'wishlist', 'purchased') DEFAULT 'purchased',
    progress INT DEFAULT 0 COMMENT 'Percentage completed (0-100)',
    highlighted_passages JSON,
    last_opened TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- Transactions Table
CREATE TABLE Transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('credit_card', 'paypal', 'stripe', 'wallet') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (book_id) REFERENCES Books(book_id)
);

-- Reviews Table
CREATE TABLE Reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book_review (user_id, book_id)
);

-- Recommendations_Log Table
CREATE TABLE Recommendations_Log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    action ENUM('viewed', 'clicked', 'purchased') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- Subscriptions Table
CREATE TABLE Subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('monthly', 'annual', 'lifetime') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Indexes for better performance
CREATE INDEX idx_user_library_user ON User_Library(user_id);
CREATE INDEX idx_user_library_book ON User_Library(book_id);
CREATE INDEX idx_transactions_user ON Transactions(user_id);
CREATE INDEX idx_transactions_date ON Transactions(transaction_date);
CREATE INDEX idx_reviews_book ON Reviews(book_id);
CREATE INDEX idx_reviews_rating ON Reviews(rating);
CREATE INDEX idx_books_author ON Books(author_id);
CREATE INDEX idx_books_genre ON Books(genre_id);
CREATE INDEX idx_subscriptions_user ON Subscriptions(user_id);
CREATE INDEX idx_subscriptions_status ON Subscriptions(status);

-- Insert Sample Data
INSERT INTO Authors (name, bio, email) VALUES 
('J.K. Rowling', 'British author best known for Harry Potter series', 'jkrowling@email.com'),
('Stephen King', 'Master of horror fiction', 'sking@email.com');

INSERT INTO Narrators (name, bio) VALUES 
('Jim Dale', 'Award-winning narrator known for Harry Potter series'),
('Stephen Fry', 'English actor and narrator');

INSERT INTO Genres (name) VALUES 
('Fantasy'), ('Horror'), ('Mystery'), ('Science Fiction'), ('Romance');

INSERT INTO Books (title, author_id, narrator_id, genre_id, description, duration, price) VALUES 
('Harry Potter and the Philosopher''s Stone', 1, 1, 1, 'First book in the Harry Potter series', 480, 24.99),
('The Shining', 2, 2, 2, 'Classic horror novel', 420, 19.99);

INSERT INTO Users (name, email, password_hash, subscription_type, subscription_expiry) VALUES 
('John Doe', 'john@email.com', 'hashed_password123', 'premium', '2024-12-31'),
('Jane Smith', 'jane@email.com', 'hashed_password456', 'free', NULL);

-- Sample queries to test the database
SELECT 'Database created successfully!' as status;

-- Show all books with author and genre info
SELECT 
    b.book_id,
    b.title,
    a.name as author_name,
    n.name as narrator_name,
    g.name as genre_name,
    b.duration,
    b.price
FROM Books b
JOIN Authors a ON b.author_id = a.author_id
JOIN Narrators n ON b.narrator_id = n.narrator_id
JOIN Genres g ON b.genre_id = g.genre_id;