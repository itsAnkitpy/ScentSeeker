# Project Plan: Perfume Comparison Website (Laravel Backend)

## Phase 1: Foundation & Core Backend (MVP)

1.  **Project Setup & Planning:**
    *   **Tech Stack Finalization:**
        *   **Frontend (Web): Laravel Blade with Alpine.js for interactivity, styled with Tailwind CSS, and assets compiled by Vite.**
        *   **Future Considerations for Interactivity:** For more complex interactive components, the project might later consider adopting Laravel Livewire or Inertia.js (potentially with Vue.js or React).
        *   **Backend: Laravel (PHP framework)**
        *   **Database:  MySQL. Interactions will be managed via Laravel's Eloquent ORM.**
    *   **Version Control:** Git repository setup (e.g., GitHub, GitLab).
    *   **Data Modeling (Eloquent & Migrations):** Define entities as **Eloquent Models** and their corresponding database structure using **Laravel Migrations**. Laravel automatically handles `created_at` and `updated_at` timestamps for models.
        *   `Perfume` Model (corresponds to `perfumes` table): attributes like `name`, `brand`, `description`, `notes` (potentially JSON or related models for structured notes like top, middle, base), `image_url`, `concentration`, `gender_affinity`, `launch_year`, etc.
        *   `Seller` Model (corresponds to `sellers` table): attributes like `name`, `logo_url`, `website_url`, `rating`, `contact_info`, `type` (e.g., 'official_retailer', 'reddit_seller').
        *   `Price` Model (corresponds to `prices` table): attributes like `perfume_id` (foreign key), `seller_id` (foreign key), `price`, `currency`, `stock_status`, `product_url`, `last_updated`, `offer_details`, `size_ml`, `item_type` (e.g., 'full_bottle', 'decant').
        *   `User` Model (corresponds to `users` table): attributes like `username`, `email`, `password` (hashed by Laravel), `email_verified_at`. Standard Laravel user model can be extended.
        *   `PriceHistory` Model (corresponds to `price_history` table): attributes like `price_id` (foreign key), `date`, `price`.
    *   **API Endpoint Definition & Documentation (Laravel Routing & API Resources):**
        *   Outline key RESTful API endpoints using **Laravel's routing system** (typically in `routes/api.php`), starting with versioning (e.g., `/api/v1/perfumes`, `/api/v1/perfumes/{perfume}/prices`, `/api/v1/register`).
        *   Utilize **Laravel API Resources** for transforming Eloquent models and collections into standardized JSON responses.
        *   Plan for API documentation using tools like OpenAPI/Swagger, potentially generated with Laravel packages such as **Scribe**.
    *   **Image Management Strategy:** Define how images (`image_url`) will be uploaded (e.g., Laravel's file storage system), stored (e.g., local disk, S3, Cloudinary via Laravel Filesystem), optimized, and served (e.g., via CDN).
    *   **Legal & Compliance:**
        *   Draft initial Terms of Service and Privacy Policy.
        *   Plan for cookie consent mechanisms (frontend and backend considerations).
        *   Outline approach for GDPR/CCPA compliance (data handling within Laravel).
    *   **Accessibility (a11y) Standards:** Mandate adherence to WCAG guidelines for all frontend development from the outset.
    *   **Security by Design:**
        *   Adopt a holistic security approach: leverage **Laravel's built-in validation** (Form Requests or manual validation) for input, output encoding (e.g., **Blade's default XSS protection** as frontend will be Blade-rendered), Eloquent ORM best practices to prevent SQL injection.
        *   Plan for regular security review checkpoints. Utilize Laravel's security features (CSRF protection for web routes, etc.).

2.  **Database Design & Setup:**
    *   Implement the defined schema using **Laravel Migrations**.
    *   Plan for data backup and recovery procedures (database-level and application-level considerations).
    *   Seed initial data for development using **Laravel Seeders**.

3.  **Backend API Development (Core - Laravel Controllers & Eloquent):**
    *   Implement CRUD (Create, Read, Update, Delete) APIs for `Perfume` model (admin-facing initially) using **Laravel Controllers** and **Eloquent Models**.
    *   Implement public APIs using Laravel Controllers:
        *   `GET /api/v1/perfumes`: List perfumes with basic filtering (e.g., by name, brand), leveraging Eloquent query builder and potentially API Resources for transformation.
        *   `GET /api/v1/perfumes/{perfume}`: Get details for a specific perfume (route model binding).
        *   `GET /api/v1/perfumes/{perfume}/prices`: Get current prices from various sellers for a specific perfume.
    *   Develop initial data ingestion logic, potentially as **Laravel Artisan Commands** or dedicated controllers/services.
    *   Establish an application-wide error handling and logging strategy using **Laravel's built-in logging (e.g., Monolog) and exception handling**.

4.  **Frontend Integration (Core - Blade, Alpine.js, Tailwind CSS):**
    *   Develop core frontend views using **Laravel Blade templates** for structure and server-side rendering.
    *   Implement client-side interactivity and dynamic updates using **Alpine.js**, fetching data from Laravel backend APIs where necessary.
    *   Style the frontend using **Tailwind CSS**.
    *   Ensure frontend components (Blade partials with Alpine.js) effectively consume data from Laravel API endpoints.
    *   Implement basic search functionality on the frontend (Blade views with Alpine.js components) that interacts with the Laravel backend API.

5.  **User Authentication (Basic - Laravel Sanctum):**
    *   Backend: Implement user registration (`POST /api/v1/register`) and login (`POST /api/v1/login`) endpoints using Laravel.
        *   Utilize **Laravel Sanctum** for API token-based authentication, suitable for SPAs.
        *   Leverage Laravel's built-in strong password hashing (Bcrypt by default).
    *   Frontend: Create registration and login pages/modals using **Laravel Blade and Alpine.js**. Alpine.js will handle client-side aspects of managing auth state (e.g., storing and sending Sanctum tokens, updating UI based on auth status).

6.  **Deployment (MVP - Laravel Focused):**
    *   Set up basic CI/CD (e.g., GitHub Actions) with steps for PHP/Laravel (Composer install, running tests, environment configuration).
    *   Deploy backend using Laravel-friendly platforms:
        *   Managed services: **Laravel Forge** (server provisioning & deployment), **Laravel Vapor** (serverless).
        *   Traditional VPS/Server: Setup with **Nginx/Apache**, PHP-FPM, Composer, and tools like **Supervisor** for managing queue workers.
    *   Deploy database (e.g., managed service like RDS, Atlas, Cloud SQL, or on the same server for smaller setups).
    *   Frontend will be served directly by the Laravel application, built with Blade, Alpine.js, and Vite.

## Phase 2: Enhancing Comparison & User Features

7.  **Advanced Seller Integration & Data Ingestion (Laravel Task Scheduling & Queues):**
    *   **Overall Strategy:**
        *   Implement a data staging area (potentially separate tables or flags in main tables) for validation, cleaning, transformation, and de-duplication before import into main Eloquent models.
        *   Develop modular, source-specific parsers/adaptors as PHP classes/services within the Laravel application.
        *   Ensure ingestion jobs (e.g., Laravel Jobs processed by Queues) are idempotent.
        *   Implement robust error handling and detailed logging for the entire ingestion pipeline using Laravel's logging facilities.
        *   Admin interface (see Phase 3) for manual review, matching, and correction of staged data.
    *   **Source 1: Verified Seller Websites:**
        *   Prioritize official APIs if available (manage keys, rate limits, schema mapping using PHP HTTP clients like Guzzle).
        *   Consider structured data feeds (XML, CSV) if offered, parsed with PHP libraries.
        *   For web scraping (use ethically and strategically if no APIs/feeds):
            *   Respect `robots.txt`, ToS.
            *   Use appropriate PHP libraries (e.g., Goutte for static, Panther for dynamic/JS-heavy sites if necessary).
            *   Design maintainable scrapers with polite request rates.
    *   **Source 2: Excel Sheets from Subreddits (e.g., r/desifragranceaddicts):**
        *   Use Reddit API (PHP wrappers like PRAW-PHP or direct API calls) for automated discovery.
        *   Map Reddit usernames to internal `Seller` models (potentially with admin approval).
        *   Download and parse Excel files (`.xlsx`, `.xls`, `.csv`) using PHP libraries (e.g., PhpSpreadsheet).
        *   Implement flexible parsing logic (configurable mappings per seller, or heuristic parsing) to handle varied sheet formats.
        *   Extract perfume details, decant sizes, and prices, mapping them to the staging schema.
    *   Develop robust, scheduled jobs using **Laravel's Task Scheduling** (configured in `app/Console/Kernel.php`) to dispatch **Laravel Queued Jobs** for automatically updating prices and stock information.
    *   Implement data normalization logic within PHP services to handle varying data formats.
    *   Research and integrate with 2-3 initial seller APIs/websites and set up Reddit Excel sheet ingestion.

8.  **Advanced Search & Filtering:**
    *   Backend: Enhance `/api/v1/perfumes` Laravel controller method to support more filters (concentration, notes, price range, gender, etc.) using Eloquent query scopes and request parameters.
    *   Frontend: Update UI (Blade views with Alpine.js components) to include these advanced search and filter controls.

9.  **Price History Implementation:**
    *   Backend:
        *   Modify data ingestion jobs (Laravel Queued Jobs) to store price changes in the `PriceHistory` Eloquent model.
        *   Create `GET /api/v1/perfumes/{perfume}/price-history` endpoint in a Laravel controller.
    *   Frontend: Integrate a JavaScript charting library (e.g., Chart.js, or an Alpine.js-friendly wrapper) within Blade views to display price history.

10. **Wishlists & Price Alerts:**
    *   Backend:
        *   APIs for users to add/remove perfumes from their wishlist (e.g., `POST /api/v1/users/me/wishlist`, `DELETE /api/v1/users/me/wishlist/{perfume}`) using Laravel controllers and Eloquent relationships.
        *   APIs to set price alerts (e.g., `POST /api/v1/users/me/alerts`).
        *   Develop a notification service using **Laravel's built-in notification system** (e.g., for email alerts via Mailables, potentially integrating with SMS/Push services) triggered by price update jobs if an alert condition is met.
    *   Frontend: Develop UI for wishlist and alert management using Blade and Alpine.js.

11. **User Reviews & Ratings:**
    *   Backend: APIs for submitting (e.g., `POST /api/v1/perfumes/{perfume}/reviews`) and retrieving reviews using Laravel controllers. Calculate average ratings using Eloquent accessors or query builder aggregations.
    *   Frontend: Develop UI for displaying and submitting reviews/ratings using Blade and Alpine.js.

## Phase 3: Content, Personalization & Admin

12. **Admin Dashboard (Laravel Nova or Custom):**
    *   Develop an admin panel:
        *   Option 1: **Laravel Nova** for a rapid, powerful admin interface.
        *   Option 2: A custom solution, either as a protected section of the main Laravel app (using Blade templates and web routes) or a separate frontend application (e.g., Vue/React SPA) communicating with dedicated admin API endpoints in Laravel.
    *   Features: Manage `Perfume` models, `Seller` models (including Reddit seller mappings & Excel format configurations), data sources, view `User` models, moderate reviews, manage staged data.

13. **Content Features (Blog/Articles - Optional):**
    *   Integrate a headless CMS (e.g., Strapi, Contentful) or build a simple blogging engine within Laravel (e.g., custom models for posts, categories, served via controllers and Blade or API).
    *   APIs (if headless CMS or Laravel API) to fetch content. Frontend (Blade views, potentially with Alpine.js for interactivity) to display articles.

14. **Personalized Recommendations (Basic):**
    *   Implement "similar perfumes" based on shared notes or brand using Eloquent queries.
    *   Track user browsing history (with consent, stored in database) to suggest relevant items, logic implemented in Laravel services/controllers.

## Phase 4: Optimization, Scaling & Monetization

15. **Performance Optimization:**
    *   Database indexing (via Laravel Migrations) and query optimization (reviewing Eloquent queries, using `EXPLAIN`).
    *   Implement caching strategies (e.g., **Laravel's Cache facade** with drivers like Redis for API responses, database query caching, CDN for static assets).
    *   Frontend performance (leveraging Vite's capabilities for bundling, code splitting, lazy loading with Alpine.js, image optimization) will be a key consideration.

16. **SEO Optimization:**
    *   Dynamic sitemap generation (e.g., using a Laravel package or custom Artisan command).
    *   Structured data (Schema.org) for perfumes and offers, potentially generated via API responses or Blade views if applicable.
    *   **Server-Side Rendering (SSR) will be handled by Laravel Blade templates.** While Alpine.js will manage client-side interactivity, the initial page load and core content structure will be server-rendered. The Laravel backend will serve both Blade views and API data.

17. **Testing & QA (PHPUnit & Laravel Dusk):**
    *   Continuously implement and expand:
        *   Unit tests for PHP classes/services using **PHPUnit**.
        *   Feature tests for API endpoints and application logic using **Laravel's PHPUnit integration**.
        *   Browser/End-to-end tests using **Laravel Dusk** (if testing any web views served by Laravel, or for full E2E flows involving the frontend).
        *   Frontend tests (e.g., Vitest for JavaScript logic within Alpine.js components, Cypress or Playwright for E2E testing of Blade views) will be implemented.

18. **Affiliate Marketing Integration (If applicable):**
    *   Modify "Go to Shop" links (data provided by Laravel API) to include affiliate tags.
    *   Track clicks/conversions if possible (backend logic in Laravel to handle callbacks or process tracking data).

19. **Monitoring & Analytics:**
    *   Integrate error tracking (Sentry, Flare for Laravel-specific errors) and performance monitoring (e.g., New Relic, Tideways for PHP/Laravel).
    *   Set up web analytics (Google Analytics, Plausible) on the frontend.