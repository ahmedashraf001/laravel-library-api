# Library Management API (Laravel 12)

A clean, well-structured REST API for managing a small library:

### **Business Problem**
Libraries need a simple digital system to track **authors, books, and borrowing operations**, while enforcing business rules to keep data consistent.
### **Solution**
 - **Main use cases**:
  - Manage authors (create, list, update, delete).
  - Manage books linked to authors (can't delete authors that have books).
  - Borrow and return books with business rules (no double-borrowing, cannot return twice).
  - View full borrowing history (who borrowed what and when).

### This is a public API for easy testing and demonstration.
---

## API Documentation

📘 **OpenAPI Specification (Swagger UI)**
- [View Interactive API Docs](https://app.swaggerhub.com/apis-docs/ahmed-dd0/library-api001/1.0)
- Complete schema definitions with all request/response examples

🔧 **Postman Collection**
- [Test APIs in Postman](https://documenter.getpostman.com/view/31504317/2sB3WwrdVa)
- Pre-configured requests ready to run
---
## Tech Stack & Architecture

- **Framework**: Laravel **12**, PHP **8.2+**.
- **Data layer**: Eloquent models & migrations (e.g. `books` table with `author_id` FK).
- **API design**:
  - **Versioned routes** under `api.php` → `/api/v1/...`
  - Resource-style controllers: `AuthorController`, `BookController`, `BorrowController`.
  - **Form Requests** (e.g. `StoreAuthorRequest`, `BorrowRequest`) for validation.
  - **API Resources** (e.g. `AuthorResource`, `BookResource`, `BorrowRecordResource`) for consistent JSON.
- **Domain logic**:
  - `BorrowingService` encapsulates borrowing/return rules.
  - Custom domain exceptions: `BookAlreadyBorrowedException`, `BookAlreadyReturnedException`.
- **Quality & DX**:
  - OpenAPI 3 spec in `openAPI.yml`.
  - Testing via `php artisan test` .
- **Technologies**
  - **Service Layer** for Borrowing System(Business logic separation for maintainability) , **PostgreSQL** , **Eloquent ORM** ,**Pest PHP**	
  
## Design Patterns & Principles
This project demonstrates clean code principles and software engineering best practices:
- **RESTful Design** - Standard HTTP methods and meaningful status codes
- **Request Validation** - Dedicated Form Request classes with custom error handling
- **Resource Layer** - Consistent API responses with `AuthorResource`, `BookResource`
- **Service Pattern** - `BorrowingService` encapsulates complex business logic
- **Repository Pattern** - Eloquent models with clear relationships
- **Custom Exceptions** - Business rule violations (`BookAlreadyBorrowedException`)
- **Soft Deletes** - Non-destructive data removal for audit trails
- **Feature Testing** - Comprehensive test coverage with Pest PHP

---
## Project Setup

#### 1. Requirements

- **PHP**: 8.2+
- **Composer**
- **Database**:PostgreSQL 12 or higher

 ### Installation

1. **Clone the repository**
```bash
git clone <your-repository-url>
cd laravel-library-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Setup database**
```bash
# Create PostgreSQL database first
# Using psql: CREATE DATABASE library_api;

# Update .env with your PostgreSQL credentials
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=library_api
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

5. **Start the development server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`

 

---

 
 
 
## Testing

This project uses **Pest PHP** for clean, readable tests.

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Test authors API
php artisan test --filter=AuthorApiTest

# Test books API
php artisan test --filter=BookApiTest

# Test borrowing system
php artisan test --filter=BorrowTest
```

### Test Coverage

The test suite covers:
- **Author CRUD** , **Book CRUD** , **Borrowing Workflow** , **Business Rules** , **Validation**  , **Error Handling**  
---