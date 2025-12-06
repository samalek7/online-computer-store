# Online Computer Store – Web Term Project  
A full-stack e-commerce web application built using PHP, MySQL, HTML5, CSS (Bootstrap 5), and JavaScript.

---

## 📌 Project Overview
This project replicates a functional online store where users can browse computer products, add them to a cart, place orders, and leave reviews.  
An administrator has full control over inventory and can view all customer orders.

This application demonstrates:
- Backend development (PHP + MySQL)
- Frontend UI (Bootstrap)
- Authentication & Authorization
- CRUD Operations
- Session handling
- Database relationships
- Bonus features such as reviews and ratings

---

## 🛠️ Tech Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript  
- **Backend:** PHP 8+  
- **Database:** MySQL (phpMyAdmin)  
- **Server:** XAMPP / Apache  

---

## 📂 Project Directory Structure

```
online-computer-store/
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── products.php
│   ├── orders.php
│   ├── admin_header.php
│   └── admin_footer.php
│
├── assets/
│   ├── css/style.css
│   ├── js/scripts.js
│   └── img/           # add your product images here
│
├── cart.php
├── checkout.php
├── config.php
├── footer.php
├── header.php
├── index.php
├── login.php
├── logout.php
├── orders.php
├── product.php
├── products.php
├── register.php
├── db.sql
└── PRESENTATION_SCRIPT.md
```

---

## 🧑‍💻 User Features

### ✔️ Registration & Login  
Secure authentication using `password_hash()` and `password_verify()`.

### ✔️ Browse + Search + Filter Products  
Users can:
- View all products  
- Search by name  
- Filter by category  
- View detailed product pages  

### ✔️ Shopping Cart  
- Add / update / remove items  
- Cart stored per-user  
- Total calculation included  

### ✔️ Checkout  
- Creates an order  
- Creates order_items  
- Automatically reduces stock  
- Clears cart  

### ✔️ Order History  
Users can view:
- Past orders  
- Items inside each order  
- Amounts and timestamps  

---

## ⭐ Bonus Feature: Reviews & Ratings  
Each product includes:
- Average rating (1–5 stars)
- Number of reviews
- Logged-in users can write or edit reviews
- Dynamic star rendering
- Display of all customer reviews (name, rating, comment)

Tables used:
- `reviews (id, user_id, product_id, rating, comment, created_at)`

---

## 🛠️ Admin Features

### ✔️ Secure Admin Login  
Admins only—restricted access with sessions.

### ✔️ Product Management  
CRUD Operations:
- Add new products  
- Edit existing products  
- Delete products  
- Update price, stock, category, image, and description  

### ✔️ Order Management  
Admins can:
- View all orders
- View items in each order
- View customer name & email

---

## 🛡️ Security Features  
- Prepared SQL statements  
- Password hashing  
- Session-based authentication  

---

## 📥 Installation Guide

1. Copy project folder into `XAMPP/htdocs/`.
2. Import `db.sql` into phpMyAdmin.
3. Update database credentials in `config.php` if needed.
4. Start **Apache + MySQL** in XAMPP.
5. Visit the website:

```
http://localhost/online-computer-store/
```

---

## 👨‍💼 Admin Login Setup

Create an admin manually:

```sql
INSERT INTO users (name, email, password, is_admin)
VALUES (
  'Admin',
  'admin@example.com',
  '<PASTE PASSWORD HASH HERE>',
  1
);
```

Generate hash:

```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
```

---

## ⭐ Optional Features Implemented  

| Feature                     | Status |
|----------------------------|--------|
| Product search & filter    | ✔️     |
| Responsive admin dashboard | ✔️     |
| Product reviews & ratings  | ✔️     |
| Inventory auto-update      | ✔️     |
| Sessions for login/cart    | ✔️     |

---

## 📌 Credits  
Project developed by: **Your Name**  
Course: **Web Programming Term Project**  
University: **Algoma University**
