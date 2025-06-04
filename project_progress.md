# Project Progress: Perfume Comparison Website (Laravel Backend)

Based on `project.md`.
- `[x]` Completed
- `[ ]` Not Yet Completed / In Progress

## Phase 1: Foundation & Core Backend (MVP)

1.  **Project Setup & Planning:**
    *   **Tech Stack Finalization:**
        *   `[x]` **Frontend (Web): Laravel Blade with Alpine.js for interactivity, styled with Tailwind CSS, and assets compiled by Vite.** (Alpine.js and Tailwind CSS installed and configured; Blade views created; Vite is the default asset bundler)
        *   `[ ]` **Future Considerations for Interactivity:** For more complex interactive components, the project might later consider adopting Laravel Livewire or Inertia.js (potentially with Vue.js or React).
        *   `[x]` **Backend: Laravel (PHP framework)** (Project is a Laravel application)
        *   `[x]` **Database:  MySQL. Interactions will be managed via Laravel's Eloquent ORM.** (Eloquent models and migrations created; MySQL is assumed as per typical Laravel setup, user confirmed .env setup)
    *   `[ ]` **Version Control:** Git repository setup (e.g., GitHub, GitLab). (Assumed to be handled by user)
    *   `[x]` **Data Modeling (Eloquent & Migrations):** Define entities as **Eloquent Models** and their corresponding database structure using **Laravel Migrations**.
        *   `[x]` `Perfume` Model (corresponds to `perfumes` table)
        *   `[x]` `Seller` Model (corresponds to `sellers` table)
        *   `[x]` `Price` Model (corresponds to `prices` table)
        *   `[x]` `User` Model (corresponds to `users` table)
        *   `[x]` `PriceHistory` Model (corresponds to `price_history` table)
    *   `[x]` **API Endpoint Definition & Documentation (Laravel Routing & API Resources):**
        *   `[x]` Outline key RESTful API endpoints using **Laravel's routing system** (typically in `routes/api.php`), starting with versioning (e.g., `/api/v1/perfumes`, `/api/v1/perfumes/{perfume}/prices`, `/api/v1/register`, `/api/v1/login`).
        *   `[x]` Utilize **Laravel API Resources** for transforming Eloquent models and collections into standardized JSON responses. (PerfumeResource, PriceResource created)
        *   `[ ]` Plan for API documentation using tools like OpenAPI/Swagger, potentially generated with Laravel packages such as **Scribe**.
    *   `[ ]` **Image Management Strategy:** Define how images (`image_url`) will be uploaded, stored, optimized, and served.
    *   `[ ]` **Legal & Compliance:**
        *   `[ ]` Draft initial Terms of Service and Privacy Policy.
        *   `[ ]` Plan for cookie consent mechanisms.
        *   `[ ]` Outline approach for GDPR/CCPA compliance.
    *   `[ ]` **Accessibility (a11y) Standards:** Mandate adherence to WCAG guidelines for all frontend development from the outset.
    *   `[x]` **Security by Design:**
        *   `[x]` Adopt a holistic security approach: leverage **Laravel's built-in validation** (Form Requests or manual validation) for input, output encoding (e.g., **Blade's default XSS protection**), Eloquent ORM best practices. (Used Form Requests for Perfume CRUD, Blade is default)
        *   `[ ]` Plan for regular security review checkpoints. Utilize Laravel's security features (CSRF protection for web routes, etc.). (CSRF is default for web routes)

2.  **Database Design & Setup:**
    *   `[x]` Implement the defined schema using **Laravel Migrations**. (Migrations created and run)
    *   `[ ]` Plan for data backup and recovery procedures.
    *   `[x]` Seed initial data for development using **Laravel Seeders**. (Seeders for Perfume, Seller, Price created and run; ImportInitialDataCommand also created)

3.  **Backend API Development (Core - Laravel Controllers & Eloquent):**
    *   `[x]` Implement CRUD (Create, Read, Update, Delete) APIs for `Perfume` model (admin-facing initially) using **Laravel Controllers** and **Eloquent Models**.
    *   `[x]` Implement public APIs using Laravel Controllers:
        *   `[x]` `GET /api/v1/perfumes`: List perfumes.
        *   `[x]` `GET /api/v1/perfumes/{perfume}`: Get details for a specific perfume.
        *   `[x]` `GET /api/v1/perfumes/{perfume}/prices`: Get current prices for a specific perfume.
    *   `[x]` Develop initial data ingestion logic, potentially as **Laravel Artisan Commands**. (ImportInitialDataCommand created)
    *   `[x]` Establish an application-wide error handling and logging strategy using **Laravel's built-in logging (e.g., Monolog) and exception handling**. (Relied on Laravel defaults for Phase 1)

4.  **Frontend Integration (Core - Blade, Alpine.js, Tailwind CSS):**
    *   `[x]` Develop core frontend views using **Laravel Blade templates** for structure and server-side rendering. (Created `layouts.app`, `welcome.blade.php`, `auth/register.blade.php`, `auth/login.blade.php`)
    *   `[x]` Implement client-side interactivity and dynamic updates using **Alpine.js**, fetching data from Laravel backend APIs where necessary. (Demonstrated in welcome page for perfume listing, and in auth forms)
    *   `[x]` Style the frontend using **Tailwind CSS**. (Setup and basic styling applied)
    *   `[x]` Ensure frontend components (Blade partials with Alpine.js) effectively consume data from Laravel API endpoints. (Demonstrated)
    *   `[ ]` Implement basic search functionality on the frontend (Blade views with Alpine.js components) that interacts with the Laravel backend API.

5.  **User Authentication (Basic - Laravel Sanctum):**
    *   `[x]` Backend: Implement user registration (`POST /api/v1/register`) and login (`POST /api/v1/login`) endpoints using Laravel.
        *   `[x]` Utilize **Laravel Sanctum** for API token-based authentication.
        *   `[x]` Leverage Laravel's built-in strong password hashing (Bcrypt by default).
    *   `[x]` Frontend: Create registration and login pages/modals using **Laravel Blade and Alpine.js**. Alpine.js will handle client-side aspects of managing auth state.

6.  **Deployment (MVP - Laravel Focused):**
    *   `[ ]` Set up basic CI/CD (e.g., GitHub Actions).
    *   `[ ]` Deploy backend using Laravel-friendly platforms.
    *   `[ ]` Deploy database.
    *   `[ ]` Frontend will be served directly by the Laravel application.

## Phase 2: Enhancing Comparison & User Features

7.  `[ ]` **Advanced Seller Integration & Data Ingestion (Laravel Task Scheduling & Queues):**
    *   `[ ]` **Overall Strategy:**
        *   `[ ]` Implement a data staging area.
        *   `[ ]` Develop modular, source-specific parsers/adaptors.
        *   `[ ]` Ensure ingestion jobs are idempotent.
        *   `[ ]` Implement robust error handling and detailed logging.
        *   `[ ]` Admin interface for manual review.
    *   `[ ]` **Source 1: Verified Seller Websites:**
    *   `[ ]` **Source 2: Excel Sheets from Subreddits:**
    *   `[ ]` Develop robust, scheduled jobs using **Laravel's Task Scheduling**.
    *   `[ ]` Implement data normalization logic.
    *   `[ ]` Research and integrate with 2-3 initial seller APIs/websites.

8.  `[ ]` **Advanced Search & Filtering:**
    *   `[ ]` Backend: Enhance `/api/v1/perfumes` for more filters.
    *   `[ ]` Frontend: Update UI for advanced search.

9.  `[ ]` **Price History Implementation:**
    *   `[ ]` Backend: Store price changes, create `GET /api/v1/perfumes/{perfume}/price-history` endpoint.
    *   `[ ]` Frontend: Integrate charting library.

10. `[ ]` **Wishlists & Price Alerts:**
    *   `[ ]` Backend: APIs for wishlists and alerts, notification service.
    *   `[ ]` Frontend: UI for wishlist and alert management.

11. `[ ]` **User Reviews & Ratings:**
    *   `[ ]` Backend: APIs for submitting and retrieving reviews.
    *   `[ ]` Frontend: UI for displaying and submitting reviews.

## Phase 3: Content, Personalization & Admin

12. `[ ]` **Admin Dashboard (Laravel Nova or Custom):**
    *   `[ ]` Develop an admin panel.
    *   `[ ]` Features: Manage models, data sources, users, reviews.

13. `[ ]` **Content Features (Blog/Articles - Optional):**
    *   `[ ]` Integrate CMS or build blogging engine.
    *   `[ ]` APIs and frontend display.

14. `[ ]` **Personalized Recommendations (Basic):**
    *   `[ ]` Implement "similar perfumes".
    *   `[ ]` Track user browsing history.

## Phase 4: Optimization, Scaling & Monetization

15. `[ ]` **Performance Optimization:**
    *   `[ ]` Database indexing and query optimization.
    *   `[ ]` Implement caching strategies.
    *   `[ ]` Frontend performance.

16. `[ ]` **SEO Optimization:**
    *   `[ ]` Dynamic sitemap generation.
    *   `[ ]` Structured data (Schema.org).
    *   `[ ]` **Server-Side Rendering (SSR) will be handled by Laravel Blade templates.** (This is an architectural note, largely covered by current approach)

17. `[ ]` **Testing & QA (PHPUnit & Laravel Dusk):**
    *   `[ ]` Unit tests.
    *   `[ ]` Feature tests.
    *   `[ ]` Browser/End-to-end tests.
    *   `[ ]` Frontend tests.

18. `[ ]` **Affiliate Marketing Integration (If applicable):**
    *   `[ ]` Modify "Go to Shop" links.
    *   `[ ]` Track clicks/conversions.

19. `[ ]` **Monitoring & Analytics:**
    *   `[ ]` Integrate error tracking and performance monitoring.
    *   `[ ]` Set up web analytics.