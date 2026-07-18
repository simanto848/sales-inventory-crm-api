# Sales, Inventory & CRM System

A clean, maintainable, and scalable backend business application built with Laravel following the Repository-Service design pattern and SOLID principles.

## Completed Features

### 1. Sales & Inventory Management
*   **Product Catalog:** Complete product tracking with Name, SKU, and Price.
*   **Multi-Branch Support:** Manage multiple store locations and track branch-specific stock levels (stored in pivot `branch_products` table).
*   **Inventory Control:**
    *   Automatic stock deduction when a product is sold.
    *   Prevents purchases when available stock at the branch is insufficient (using database transactions to guarantee data integrity).

### 2. CRM Features
*   **Purchase History:** Tracks complete customer transactions, automatically incrementing purchase frequency and updating the last purchase date.
*   **Inactive Customer Detection:** Query to identify "lost" customers who have not made a purchase within a configurable window (e.g., 90 days).
*   **Customer Re-engagement:** Exposes endpoints to trigger re-engagement messages via email (sent using Laravel Mailables) or SMS.
*   **Employee Assignment:** Administrators can assign inactive customers to specific employees for follow-up.
*   **KPI Tracking:** Automatically increments the assigned employee's KPI score by +10 when their assigned inactive customer makes a new purchase.

### 3. Bonus Features
*   **Multi-Branch support:** Database structure supports multiple locations with custom branch stock levels.
*   **Automatic Email Invoices:** Automatically triggers and sends a styled HTML invoice email to the customer upon successful sale checkout.
*   **Secure E-Commerce Integration API:** A dedicated REST API route (`/api/integration/products`) secured by an `api.key` middleware to securely expose SKU, Name, Price, and total Available Stock for simulated third-party integrations.

---

## Technical Architecture & Design Patterns

The application follows the **Repository-Service Pattern** to achieve high separation of concerns:
*   **Controllers:** Extremely thin controllers located under the `App\Http\Controllers\Api` namespace. Controllers do not containing try-catch blocks or business logic; they only validate inputs using FormRequests, delegate execution to services, and return unified success JSON responses.
*   **Services:** Implement all core business rules (e.g., stock calculations, KPI increments, email triggers, transaction processing).
*   **Repositories:** Encapsulate database access details.
*   **Global Exception Handling:** Configured in `bootstrap/app.php` to catch system exceptions (like `InvalidArgumentException` or `NotFoundHttpException`) and transform them automatically into standard JSON errors.

---

## Setup & Local Installation

### Prerequisites
*   PHP >= 8.2
*   Composer
*   SQLite or MySQL database

### Step-by-Step Setup

1. **Clone the Repository:**
    ```bash
    git clone https://github.com/simanto848/sales-inventory-crm-api.git
    cd sales-inventory-crm-api/backend
    ```

2. **Install Dependencies:**
    ```bash
    composer install
    ```

3. **Configure Environment:**
    Copy the sample environment file:
    ```bash
    cp .env.example .env
    ```
    Generate application key:
    ```bash
    php artisan key:generate
    ```

4. **Setup Database:**
    By default, the project is configured to use **SQLite** (making it ready to run immediately). To use SQLite, create the empty database file:
    ```bash
    touch database/database.sqlite
    ```
    *If you prefer MySQL, update the `DB_*` variables in `.env` to point to your MySQL server.*

5. **Run Migrations & Seed Data:**
    Run migrations and seed realistic sample data (Users, Branches, Products, Customers, Sales, and Stocks) in one command:
    ```bash
    php artisan migrate:fresh --seed
    ```

6. **Mailtrap SMTP Configuration (Optional):**
    To test the email invoices and re-engagement emails using [Mailtrap](https://mailtrap.io/), update these settings in `.env`:
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    ```
    *(By default, emails are written to logs under `storage/logs/laravel.log` so you don't need SMTP credentials to test).*

7. **Run the Application:**
    Start the local development server:
    ```bash
    php artisan serve
    ```
    The API will be accessible at: `http://127.0.0.1:8000/api`

---

## Seed Data Details
Running the seeders creates:
*   **Admin/Employee accounts** (Default login email: `admin@example.com` / password: `password`).
*   **Branches:** Multiple locations (e.g., Dhaka, Chittagong, Sylhet).
*   **Products:** Configured with branch-specific initial stock inventory.
*   **Customers:** Sample customer CRM records with varying purchase histories and assignment logs.
*   **Sales:** Completed orders indicating branch-level transactions.
