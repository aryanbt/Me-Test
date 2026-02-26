# Photo Gallery CMS (PHP 8.3 + MySQL)

A modern, secure and responsive photo/video gallery CMS with:

- Public front page gallery and full-page infinite-scroll gallery.
- Login-only auth (no signup).
- Role-based dashboard:
  - **Admin**: full control, user management, gallery management.
  - **Manager**: upload, rename, delete, bulk delete media.
  - **User**: dashboard access.
- Upload form includes title, description, and tags.
- CSRF protection, password hashing, prepared statements, MIME validation.

## Quick setup

1. Create DB and import schema:
   ```bash
   mysql -u root -p -e "CREATE DATABASE gallery_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p gallery_cms < sql/schema.sql
   ```
2. Configure environment variables (or use defaults in `app/config/config.php`):
   - `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - Optional: `APP_BASE_PATH` when app is deployed in a subdirectory (example: `gallery` for `/gallery`).
3. Serve `public/` with nginx or quickly test with built-in server:
   ```bash
   php -S 0.0.0.0:8080 -t public
   ```
4. Login:
   - Email: `admin@example.com`
   - Password: `Admin@123`

## Routes

- `/` home gallery
- `/gallery.php` infinite grid gallery
- `/login.php`, `/logout.php`
- `/dashboard.php`
- `/admin_users.php` (admin only)
- `/manage_media.php` (admin/manager)
- `/api/media.php` infinite-scroll data source


## Subdirectory deployments

This CMS is subdirectory-safe. All internal links, redirects, assets, API calls, and uploaded media URLs are generated using runtime base-path detection (or `APP_BASE_PATH` override).
