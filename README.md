# PM Training App

PM Training App is a training management application built with Laravel. It provides API-based authentication using JSON Web Tokens (JWT) and is designed to streamline training management processes.

## Features

- API-based authentication with JWT.
- Modular and scalable architecture.
- Easy-to-use endpoints for managing training sessions, users, and roles.

## Getting Started

Follow these steps to set up and run the application:

### Prerequisites

Ensure you have the following installed on your system:

- PHP >= 8.0
- Composer
- MySQL or any other supported database
- Node.js and npm (optional, for frontend assets)

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd pm-training-app
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the `.env.example` file to `.env` and configure your environment variables:
   ```bash
   cp .env.example .env
   ```

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Generate the JWT secret key:
   ```bash
   php artisan jwt:secret
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. (Optional) Seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

### Folder Structure

- `app/` - Contains the core application code (models, controllers, etc.).
- `routes/` - Defines the API routes.
- `database/` - Contains migrations, seeders, and factories.
- `config/` - Configuration files for the application.
- `resources/` - Views, language files, and frontend assets.
- `tests/` - Unit and feature tests.

### Authentication

The application uses JWT for authentication. To log in, use the `/api/login` endpoint with valid credentials. The response will include a token to be used for subsequent API requests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
