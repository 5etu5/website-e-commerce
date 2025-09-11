-- MySQL Schema for Vintage Threads E-commerce Platform
-- For use with XAMPP
-- 
-- Run this in phpMyAdmin or MySQL command line:
-- 1. Create database: CREATE DATABASE vintage_threads;
-- 2. Use database: USE vintage_threads;
-- 3. Run this script

DROP DATABASE IF EXISTS vintage_threads;
CREATE DATABASE vintage_threads CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vintage_threads;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Regions table
CREATE TABLE regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
    shipping_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    category_id INT,
    image_url VARCHAR(500),
    sizes VARCHAR(255),
    condition_notes TEXT,
    measurements TEXT,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_address TEXT NOT NULL,
    region_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    order_token VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id),
    INDEX idx_token (order_token),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- Blog posts table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    image_url VARCHAR(500),
    published TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_published (published),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('T-Shirts', 't-shirts', 'Vintage and retro t-shirts'),
('Jeans', 'jeans', 'Classic denim and vintage jeans'),
('Hoodies', 'hoodies', 'Vintage hoodies and sweatshirts'),
('Jackets', 'jackets', 'Vintage jackets and outerwear'),
('Accessories', 'accessories', 'Vintage accessories and collectibles'),
('Shoes', 'shoes', 'Vintage and retro footwear'),
('New Arrivals', 'new-arrivals', 'Recently added vintage items'),
('Outlet', 'outlet', 'Discounted vintage clothing');

-- Insert default regions
INSERT INTO regions (name, currency, shipping_rate) VALUES
('United States', 'USD', 9.99),
('Canada', 'CAD', 12.99),
('United Kingdom', 'GBP', 8.99),
('European Union', 'EUR', 11.99),
('Australia', 'AUD', 15.99);

-- Insert default admin user (username: admin, password: password)
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, published) VALUES
('The Art of Vintage Fashion', 'the-art-of-vintage-fashion', 
'<p>Vintage fashion is more than just clothing - it''s a window into the past, a statement of style, and a commitment to sustainable fashion...</p>', 
'Discover the timeless appeal of vintage fashion and why it continues to captivate fashion enthusiasts worldwide.',
1),
('How to Style Vintage Denim', 'how-to-style-vintage-denim',
'<p>Vintage denim is a cornerstone of any well-curated wardrobe. From high-waisted jeans to classic denim jackets...</p>',
'Learn the secrets to incorporating vintage denim pieces into your modern wardrobe with style and confidence.',
1);

-- Insert sample products
INSERT INTO products (name, slug, description, price, stock_quantity, category_id, image_url, sizes, condition_notes, is_featured) VALUES
('Vintage Band T-Shirt', 'vintage-band-t-shirt', 'Authentic 80s band tour t-shirt in excellent condition', 45.00, 5, 1, '/assets/images/vintage-tshirt.jpg', 'S, M, L', 'Excellent vintage condition with minimal wear', 1),
('Classic 501 Jeans', 'classic-501-jeans', 'Vintage Levi''s 501 jeans with perfect fading', 85.00, 3, 2, '/assets/images/vintage-jeans.jpg', '30, 32, 34', 'Great vintage condition with authentic wear patterns', 1),
('Retro Leather Jacket', 'retro-leather-jacket', 'Genuine leather jacket from the 70s', 150.00, 2, 4, '/assets/images/leather-jacket.jpg', 'M, L', 'Very good condition with beautiful patina', 1);