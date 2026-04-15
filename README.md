# Pocyun's Blog

A personal blog built with vanilla PHP and MySQL. Supports Markdown posts, image uploads, categories, tags, and an admin panel.

## Tech Stack

- **Backend**: PHP 8+, MySQLi
- **Frontend**: Vanilla JS, SASS (compiled to CSS)
- **Markdown**: [league/commonmark](https://commonmark.thephpleague.com/)
- **Server**: Apache (with `.htaccess`)

## Features

- Public blog with paginated posts and selected-post showcase
- Admin panel: create / edit / delete posts, manage categories & tags
- Markdown editor (EasyMDE) with image upload
- Cover image upload (jpg / png / webp / gif, max 2MB)
- CSRF protection on all state-changing forms
- Session-based admin authentication with bcrypt password hashing

## Project Structure

```
phpBased_blog/
├── admin/          # Admin panel pages
├── configs/        # config.php (gitignored) + config.example.php
├── css/
│   ├── *.sass      # Source files
│   └── dist/       # Compiled CSS (committed)
├── images/         # Static UI assets
├── js/             # Frontend scripts
├── template/       # Shared PHP partials
├── uploads/
│   ├── covers/     # Post cover images
│   └── content/    # In-post images
└── vendor/         # Composer packages (gitignored)
```

## Local Setup

### Requirements

- PHP 8.0+
- MySQL 8.0+
- Composer
- Apache with `mod_rewrite`

### Steps

```bash
# 1. Clone the repo
git clone <repo-url>
cd phpBased_blog

# 2. Install PHP dependencies
composer install

# 3. Create the database
mysql -u root -p -e "CREATE DATABASE phpbased_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Import the schema
mysql -u root -p phpbased_blog < schema.sql

# 5. Set up config
cp configs/config.example.php configs/config.php
# Edit configs/config.php and fill in your values

# 6. Edit conn.php with your DB credentials
```

Then visit `http://localhost/phpBased_blog/`.

### Seed Data (optional)

Visit `http://localhost/phpBased_blog/admin/seed.php` while logged in as admin to populate sample posts, categories, and tags.

> **Warning**: Seed will truncate all existing data first.

## AWS Deployment

```bash
# On the server
git clone <repo-url>
cd phpBased_blog

composer install --no-dev

cp configs/config.example.php configs/config.php
nano configs/config.php          # fill in production values
nano conn.php                    # fill in production DB credentials

# Set upload directory permissions
chmod 775 uploads/covers uploads/content
chown www-data:www-data uploads/covers uploads/content
```

### Updating

```bash
git pull
composer install --no-dev       # only needed if composer.json changed
```

## Configuration Files

| File | Description |
|------|-------------|
| `conn.php` | Database host / user / password / name |
| `configs/config.php` | App key, secret, and OAuth URLs (gitignored) |
| `utils.php` | `BASE_URL` and upload path constants |

> Update `BASE_URL`, `UPLOAD_COVERS_PATH`, `UPLOAD_CONTENT_PATH`, and `DOMAIN` in `utils.php` to match your production server path.

## Admin Account

Create the first admin user directly in the database:

```sql
INSERT INTO users (username, password_hash)
VALUES ('admin', '$2y$10$...');  -- generate with password_hash() in PHP
```

Or use PHP to generate the hash:

```bash
php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
```
