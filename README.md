# Libri - Library Management System

Libri is a PHP and MySQL web application for browsing, searching, and reserving books in a small library catalogue. Users can register an account, browse the full book collection, search by title/author/category, reserve or release books, and manage their profile.

## Features

- **User accounts** – registration with mobile number and password strength validation (minimum 6 characters, at least one number, one uppercase and one lowercase letter), login, and logout via PHP sessions.
- **Profile management** – users can update their name, address, and phone number; username and mobile stay read-only after registration.
- **Browse books** – a card-based grid view of every book in the catalogue, showing title, author, category, ISBN, year, and edition, with live availability status.
- **Advanced search** – filter the catalogue by title, author, and/or category using a dedicated search form.
- **Reservations** – reserve an available book directly from the browse or search views, view all of your active reservations in one place, and cancel a reservation when you no longer need it.
- **Dashboard stats** – the home page shows live counts of total books, categories, available books, and the current user's reservations.

## Tech Stack

- **Backend:** PHP (procedural, `mysqli` extension)
- **Database:** MySQL
- **Frontend:** HTML, CSS (no framework), vanilla JavaScript for client-side form checks
- **Environment:** Designed to run under XAMPP (Apache + MySQL + PHP)

## Project Structure

```
Libri_Web-Library/
├── index.php        # Home dashboard (requires login)
├── login.php         # Login form and authentication
├── register.php       # Account registration
├── logout.php         # Destroys session and redirects to login
├── books.php          # Browse all books
├── search.php          # Advanced search by title/author/category
├── reserved.php        # View and cancel your reservations
├── profile.php         # View/update account details
├── functions.php       # Database connection and all core logic (auth, reservations)
├── header.php / footer.php  # Shared layout partials
├── style.css           # Site-wide styling
└── db.sql              # Database schema and seed data
```

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any Apache + PHP + MySQL stack)

### Installation

1. Clone or download this repository into your XAMPP `htdocs` folder:
   ```
   git clone https://github.com/tyfta9/Libri_Web-Library.git
   ```
2. Start Apache and MySQL from the XAMPP control panel.
3. Open **phpMyAdmin**, create a database named `libri`, and import `db.sql` to create the tables and seed sample data.
4. If your MySQL setup uses different credentials than the XAMPP defaults (`root` with no password), update the connection details in `functions.php`'s `connectDb()` function.
5. Visit `http://localhost/Libri_Web-Library/register.php` in your browser to create an account, or use one of the seeded accounts in `db.sql` to log in.

## Usage

1. **Register** for an account, or log in with an existing one.
2. Fill in your **profile** details (address, phone) — this is required before you can reserve a book.
3. **Browse** the catalogue or use **Search** to find a specific title, author, or category.
4. **Reserve** any available book with a single click.
5. Check or cancel your active reservations under **My Reservations**.

## Known Issues & Future Improvements

This is a learning project, and a few things are intentionally left as-is for now, but are worth knowing about:

- **Schema/code mismatch:** `db.sql` defines a `resell` column on the `books` table, while `functions.php` and the views read/write a `reserved` column. A migration or schema update is needed to align the two.
- **Plain-text passwords:** passwords are currently stored and compared as plain text rather than hashed (e.g. with `password_hash()`/`password_verify()`). This should be fixed before any real deployment.
- **Hardcoded DB credentials:** database connection details are hardcoded in `functions.php` rather than pulled from an environment file.
- **SQL queries:** input is sanitized with `real_escape_string()`, but queries are built with string concatenation rather than prepared statements/parameter binding, which would be a more robust long-term approach.

## Author

Denys Lazorenko – [github.com/tyfta9](https://github.com/tyfta9)
