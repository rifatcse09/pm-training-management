# PM Training Management Backend API

A robust backend API system for the Bangladesh Planning Ministry's training management platform, built with Laravel 12, JWT authentication, and comprehensive role-based access control. This API provides secure endpoints for managing employee training programs, assignments, and generating detailed reports.

![PM Training Management API](./banner.png)

## Project Overview

The PM Training Management Backend API serves as the core system for managing training operations within the Bangladesh Planning Ministry. It provides secure RESTful endpoints for employee management, training program administration, assignment tracking, and comprehensive reporting with role-based permissions.

### Key Features

🔐 **Authentication & Authorization**
- JWT-based secure authentication
- Role-based access control (Admin, Officer, Operator)
- User activation and management system
- Password reset functionality

👥 **Employee Management**
- Complete employee lifecycle management
- Designation-based categorization (Grade 1-29)
- Employee profile management with photo support
- Training history tracking

📚 **Training Program Administration**
- Local and foreign training program management
- Training organizer and institution management
- Group training session coordination
- Training assignment workflows

📊 **Advanced Reporting System**
- Grade-wise training reports with PDF export
- Individual employee training summaries
- Monthly analytics and statistics
- Fiscal year-based reporting

📈 **Dashboard Analytics**
- Real-time training statistics
- Monthly chart data for visualization
- Performance metrics and KPIs
- Data aggregation for frontend consumption

## Technology Stack

### Backend Framework
- **Laravel 12** - Modern PHP framework with latest features
- **PHP 8.2+** - Latest PHP version with performance improvements
- **MySQL** - Primary database for data persistence
- **JWT Authentication** - Secure token-based authentication
- **Laravel Sanctum** - API authentication system

### Key Packages
- **tymon/jwt-auth** - JWT authentication for Laravel
- **carlos-meneses/laravel-mpdf** - PDF generation for reports
- **fruitcake/laravel-cors** - Cross-origin resource sharing
- **Laravel Tinker** - Interactive shell for debugging

### Development Tools
- **Laravel Pint** - Code style fixer
- **Laravel Sail** - Docker development environment
- **PHPUnit** - Unit testing framework
- **Faker** - Test data generation

## API Endpoints

### Authentication Routes
```
POST   /api/v1/register           # User registration
POST   /api/v1/login              # User login
POST   /api/v1/logout             # User logout
GET    /api/v1/me                 # Get authenticated user
PUT    /api/v1/profile            # Update user profile
POST   /api/v1/forgot-password    # Password reset request
POST   /api/v1/reset-password     # Password reset confirmation
```

### Employee Management
```
GET    /api/v1/employees                    # List all employees
GET    /api/v1/employees/{id}               # Get specific employee
POST   /api/v1/employees                    # Create new employee
PUT    /api/v1/employees/{id}               # Update employee
DELETE /api/v1/employees/{id}               # Delete employee
GET    /api/v1/employees/{id}/trainings     # Get employee trainings
```

### Training Management
```
GET    /api/v1/trainings                    # List all trainings
GET    /api/v1/trainings/{id}               # Get specific training
POST   /api/v1/trainings                    # Create new training
PUT    /api/v1/trainings/{id}               # Update training
DELETE /api/v1/trainings/{id}               # Delete training
GET    /api/v1/trainings/assignments        # Get training assignments
POST   /api/v1/trainings/assign             # Assign training to employees
```

### Organizer Management
```
GET    /api/v1/organizers                   # List all organizers
GET    /api/v1/organizers/{id}              # Get specific organizer
POST   /api/v1/organizers                   # Create new organizer
PUT    /api/v1/organizers/{id}              # Update organizer
DELETE /api/v1/organizers/{id}              # Delete organizer
GET    /api/v1/project-organizers          # Get project organizers
```

### Dashboard & Analytics
```
GET    /api/v1/dashboard                    # Dashboard statistics
GET    /api/v1/dashboard/training-stats     # Training statistics
GET    /api/v1/dashboard/monthly-chart      # Monthly chart data
```

### Admin Management
```
GET    /api/v1/admin/pending-users          # List pending users
PUT    /api/v1/admin/activate-user/{id}     # Activate user account
GET    /api/v1/admin/users                  # List all users
PUT    /api/v1/admin/users/{id}             # Update user
POST   /api/v1/admin/assign-role/{id}       # Assign user role
DELETE /api/v1/admin/users/{id}             # Delete user
```

### Reports
```
GET    /api/v1/training-reports             # Generate training reports
GET    /api/v1/training-assignments/pdf     # Export assignments as PDF
```

## Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer 2.0+
- MySQL 8.0+
- Redis (optional, for caching)
- Node.js & npm (for asset compilation)

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/rifatcse09/pm-training-management.git
   cd pm-training-management-backend
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

4. **Configure database in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=training_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seeders:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create storage symlink:**
   ```bash
   php artisan storage:link
   ```

7. **Start the development server:**
   ```bash
   php artisan serve
   ```

### Docker Setup (Alternative)

1. **Start with Laravel Sail:**
   ```bash
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

## Database Structure

### Core Tables

- **users** - System users with role-based access
- **employees** - Employee records with designation information
- **designations** - Job designations with grade classifications
- **trainings** - Training program catalog
- **organizers** - Training organizers and institutions
- **countries** - Country information for foreign trainings
- **group_trainings** - Group training session management
- **employee_training** - Training assignment pivot table

### Key Relationships

- Employee → Designation (Many-to-One)
- Employee → Training (Many-to-Many through employee_training)
- Training → Organizer (Many-to-One)
- Training → Country (Many-to-Many)
- GroupTraining → Employees (One-to-Many through employee_training)

## API Authentication

### JWT Token Structure

```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "admin"
  }
}
```

### Request Headers

```
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
```

## Role-Based Permissions

### Admin Role
- Full system access
- User management capabilities
- System configuration
- All CRUD operations across modules

### Officer Role
- Read access to training data
- Report generation capabilities
- Training assignment viewing
- Limited modification rights

### Operator Role
- Training record management
- Employee assignment to trainings
- Training session coordination
- Operational data entry

## Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error messages"]
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 10,
    "total_items": 100,
    "per_page": 10
  }
}
```

## Testing

### Run Unit Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Generate Test Coverage Report
```bash
php artisan test --coverage
```

## Security Features

### Data Protection
- Input validation and sanitization
- SQL injection prevention via Eloquent ORM
- XSS protection through Laravel's built-in features
- CSRF protection for web routes

### Authentication Security
- JWT token expiration management
- Password hashing using bcrypt
- Rate limiting on authentication endpoints
- Secure password reset functionality

### Access Control
- Role-based middleware protection
- Route-level permission checks
- Resource-based authorization
- API rate limiting

## Performance Optimization

### Caching Strategy
- Database query caching
- Redis for session storage
- API response caching
- Static file caching

### Database Optimization
- Proper indexing on frequently queried columns
- Optimized query relationships
- Database connection pooling
- Query optimization monitoring

## Monitoring & Logging

### Application Logging
- Daily log rotation
- Error tracking and alerting
- Performance monitoring
- API request logging

### Health Checks
- Database connectivity monitoring
- API endpoint health checks
- System resource monitoring
- Automated alert system

## Deployment

### Production Environment
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.pm-training.gov.bd

# Database
DB_CONNECTION=mysql
DB_HOST=production-db-host
DB_DATABASE=training_production

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis-host

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gov.bd
```

## API Versioning

Current API version: **v1**

Version strategy:
- Semantic versioning for releases
- Backward compatibility maintenance
- Deprecation notices for old endpoints
- Clear migration guides for version upgrades

## Contributing

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Follow Laravel coding standards
4. Write comprehensive tests
5. Update documentation
6. Submit a pull request

### Code Standards

- Follow PSR-12 coding standards
- Use Laravel best practices
- Write meaningful commit messages
- Include unit and feature tests
- Update API documentation

## Support & Documentation

### Technical Support
- **API Documentation**: Available at `/api/documentation`
- **Development Team**: hello@mdrifatul.info
- **System Administrator**: hello@mdrifatul.info

### Resources
- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Documentation](https://jwt-auth.readthedocs.io/)
- [API Testing with Postman](./docs/api-testing.md)

## License

This project is proprietary software developed for the Bangladesh Planning Ministry. All rights reserved.

---

**© 2024 Bangladesh Planning Ministry - Training Management System Backend API**

**Version:** 1.0.0  
**Last Updated:** January 2025  
**Maintained by:** Planning Ministry Development Team
