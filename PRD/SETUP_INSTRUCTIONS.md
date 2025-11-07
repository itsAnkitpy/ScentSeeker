# ScentSeeker Project Setup Instructions

This document provides detailed instructions for setting up the ScentSeeker project locally for development.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

*   **PHP:** Version 8.2 or higher (as specified in `composer.json`)
*   **Composer:** Latest version recommended (Dependency Manager for PHP)
*   **Node.js:** Latest LTS version recommended (includes npm or yarn for frontend dependencies)
*   **MySQL:** Or another database system compatible with Laravel (e.g., PostgreSQL, SQLite). MySQL was the planned database.
*   **Git:** For version control.

## Setup Steps

1.  **Clone the Repository:**
    If the project is hosted on a Git repository (e.g., GitHub, GitLab), clone it to your local machine:
    ```bash
    git clone <repository_url> scentseeker
    cd scentseeker
    ```
    If you are starting from a shared project folder, navigate into that folder.

2.  **Install PHP Dependencies:**
    Install all the required PHP packages using Composer:
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies:**
    Install Node.js packages for the frontend assets (Tailwind CSS, Alpine.js, Vite, etc.):
    ```bash
    npm install
    ```
    *Note: If you encounter issues with `npm install` later (e.g., for `tailwindcss` or `autoprefixer` binaries), ensure it completes successfully. We had to manually install `autoprefixer` and `@tailwindcss/postcss` during initial setup.*

4.  **Environment Configuration:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Generate an application key:
        ```bash
        php artisan key:generate
        ```
    *   **Configure your `.env` file:**
        Open the `.env` file and update the database connection details:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=scentseeker # Or your preferred database name
        DB_USERNAME=root      # Or your database username
        DB_PASSWORD=          # Or your database password
        ```
        Ensure you create the database (`scentseeker` or your chosen name) in your MySQL instance.
        Also, update `APP_URL` if necessary:
        ```env
        APP_URL=http://localhost:8000 # Or your local development URL
        ```

5.  **Run Database Migrations:**
    Create the necessary database tables by running the migrations:
    ```bash
    php artisan migrate
    ```
    This will create tables for users, personal access tokens (for Sanctum), perfumes, sellers, prices, and price history.

6.  **Seed the Database (Optional but Recommended for Development):**
    Populate the database with initial sample data using the seeders:
    ```bash
    php artisan db:seed
    ```
    This will run the `DatabaseSeeder`, which in turn calls `PerfumeSeeder`, `SellerSeeder`, and `PriceSeeder`.
    Alternatively, you can use the custom command created for initial data import (which includes a few more samples):
    ```bash
    php artisan import:initial-data
    ```

7.  **Frontend Asset Setup (Troubleshooting if needed):**
    During our initial setup, we encountered some issues with Tailwind CSS initialization. The following steps were taken and might be needed if the `npm install` didn't fully set up Tailwind:
    *   Ensure `alpinejs`, `autoprefixer`, and `@tailwindcss/postcss` are in `devDependencies` in `package.json`. If not, install them:
        ```bash
        npm install -D alpinejs autoprefixer @tailwindcss/postcss
        ```
    *   A `tailwind.config.js` file should exist with content similar to:
        ```javascript
        /** @type {import('tailwindcss').Config} */
        export default {
          content: [
            "./resources/**/*.blade.php",
            "./resources/**/*.js",
            "./resources/**/*.vue",
          ],
          theme: {
            extend: {},
          },
          plugins: [],
        }
        ```
    *   A `postcss.config.js` file should exist with content similar to:
        ```javascript
        export default {
          plugins: {
            '@tailwindcss/postcss': {},
            autoprefixer: {},
          },
        }
        ```
    *   Ensure `resources/js/app.js` initializes Alpine.js:
        ```javascript
        import './bootstrap';
        import Alpine from 'alpinejs';
        window.Alpine = Alpine;
        Alpine.start();
        ```
    *   Ensure `resources/css/app.css` imports Tailwind:
        ```css
        @import 'tailwindcss';
        /* Other @source or @theme directives might be present */
        ```

8.  **Run Development Servers:**
    To view the application, you need to run two servers concurrently:
    *   **PHP Development Server (for Laravel backend):**
        ```bash
        php artisan serve
        ```
        This usually starts the server at `http://127.0.0.1:8000`.
    *   **Vite Development Server (for frontend assets):**
        Open a new terminal window/tab in the project root and run:
        ```bash
        npm run dev
        ```
        This will compile frontend assets and enable hot module replacement.

    Alternatively, the project's `composer.json` includes a convenient script to run all necessary development processes:
    ```bash
    composer run dev
    ```
    This uses `concurrently` to manage `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`.

9.  **Access the Application:**
    Open your web browser and navigate to the URL provided by `php artisan serve` (usually `http://127.0.0.1:8000`).
    You should see the welcome page. You can navigate to:
    *   `/register` to create a new account.
    *   `/login` to sign in.
    *   `/perfumes` to see the list of perfumes.

## Key Project Structure Points

*   **API Routes:** Defined in `routes/api.php` (prefixed with `/api/v1/`).
*   **Web Routes:** Defined in `routes/web.php`.
*   **Eloquent Models:** Located in `app/Models/`.
*   **API Controllers:** Located in `app/Http/Controllers/Api/V1/`.
*   **Web Controllers:** Located in `app/Http/Controllers/` (and `app/Http/Controllers/Auth/`).
*   **API Resources:** Located in `app/Http/Resources/`.
*   **Form Requests (Validation):** Located in `app/Http/Requests/`.
*   **Database Migrations:** Located in `database/migrations/`.
*   **Database Seeders:** Located in `database/seeders/`.
*   **Blade Views:** Located in `resources/views/`.
    *   Main Layout: `resources/views/layouts/app.blade.php`
*   **Frontend Assets:**
    *   JavaScript: `resources/js/app.js`
    *   CSS: `resources/css/app.css`
*   **Console Commands:** Located in `app/Console/Commands/`.

## Troubleshooting

*   **"Cannot find module 'autoprefixer'" or similar PostCSS errors during `npm run dev`:**
    *   Ensure `autoprefixer` is installed: `npm install -D autoprefixer`.
    *   Ensure `postcss.config.js` is correctly configured.
*   **"Tailwind CSS plugin has moved" error during `npm run dev`:**
    *   Ensure `@tailwindcss/postcss` is installed: `npm install -D @tailwindcss/postcss`.
    *   Update `postcss.config.js` to use `'@tailwindcss/postcss': {}`.
*   **Class not found errors (PHP):**
    *   Run `composer dump-autoload`.
*   **Database connection issues:**
    *   Double-check your `.env` file settings (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST`, `DB_PORT`).
    *   Ensure your database server is running and the specified database exists.
*   **Frontend assets not loading/styling issues:**
    *   Make sure `npm run dev` is running without errors.
    *   Clear browser cache.
    *   Check the browser's developer console for errors.
    *   Verify paths in `@vite` directive in `resources/views/layouts/app.blade.php`.

This should cover the initial setup for the ScentSeeker project.