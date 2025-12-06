CREATE DATABASE IF NOT EXISTS online_computer_store;
USE online_computer_store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Placed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_orderitems_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_orderitems_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO products (name, category, price, stock, image, description) VALUES
('ApexBook Z17 Ultra', 'Laptops', 1599.99, 18, 'assets/img/laptop1.jpg', 'Premium ultrabook with 12-core processor and OLED display.'),
('NovaBook AirLite', 'Laptops', 849.99, 42, 'assets/img/laptop2.jpg', 'Slim, lightweight laptop designed for students and professionals.'),
('VisionView 32U HDR Monitor', 'Monitors', 499.99, 55, 'assets/img/monitor1.jpg', '32-inch 4K UHD monitor with HDR10 and thin-bezel design.'),
('QuantumType RGB Keyboard', 'Accessories', 149.99, 90, 'assets/img/keyboard1.jpg', 'Mechanical RGB keyboard with hot-swappable switches and macro support.'),
('SwiftPoint Pro Mouse', 'Accessories', 45.99, 60, 'assets/img/mouse1.jpg', 'Precision wireless mouse with silent-click and long battery life.'),
('HyperDock X7 USB-C Hub', 'Accessories', 79.99, 22, 'assets/img/dock1.jpg', '7-in-1 USB-C docking station with HDMI, SD card, and 100W PD charging.'),
('EchoWave Gaming Headset', 'Accessories', 89.99, 48, 'assets/img/headset1.jpg', '360° surround sound gaming headset with noise-isolating mic.'),
('CoreBox Mini Desktop G5', 'Desktops', 749.99, 30, 'assets/img/desktop1.jpg', 'Compact mini desktop with 16GB RAM, NVMe SSD, and WiFi 6 support.');
