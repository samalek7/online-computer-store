Online Computer Store
---------------------

This project is a simple online store that I created for my Web Programming course. 
It lets users look at computer-related products, add items to a cart, make an order, 
and leave a rating or review on products they bought. There is also an admin section 
where products can be added, edited, or deleted.

I used PHP, MySQL, HTML, CSS, and a little bit of JavaScript. Bootstrap is used for layout.

Main Features
-------------
- User registration and login
- Browsing all products
- Searching for products
- Viewing product details
- Adding items to the cart
- Updating or removing cart items
- Placing an order
- Seeing past orders
- Writing ratings and reviews

Admin Features
--------------
- Admin login
- Add new products
- Edit product details
- Delete products
- View all customer orders

Database
--------
The database is included in the db.sql file.  
Import it in phpMyAdmin after creating a database.

The main tables are:
- users
- products
- cart
- orders
- order_items
- reviews

How to Run It
-------------
1. Install XAMPP.
2. Put this project folder inside: C:/xampp/htdocs/
3. Start Apache and MySQL from XAMPP.
4. Open phpMyAdmin and import db.sql.
5. Go to this link in your browser:
   http://localhost/online-computer-store/

Admin Account Note
------------------
To make an admin user, open the users table in phpMyAdmin and set is_admin to 1 
for any user you want.

### Created by: Sameerkhan
