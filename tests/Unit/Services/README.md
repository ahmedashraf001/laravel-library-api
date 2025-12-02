# Unit Tests with Mocked Repositories
**Mocking repository dependencies** - No database calls in unit tests
**Testing business logic in isolation** - Services tested independently
 
 

### Run specific test file:
```bash
php artisan test tests/Unit/Services/AuthorServiceTest.php
```

### Run specific test method:
```bash
php artisan test --filter=test_index_returns_authors_from_repository
```

### Run all tests 
```bash
php artisan test
```
### Run only unit tests (fast)
```bash
php artisan test tests/Unit/
```
### Run only feature tests
```bash
php artisan test tests/Feature/
```