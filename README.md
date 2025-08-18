# PM Training App

PM Training App is a training management application built with Laravel. It provides API-based authentication using JSON Web Tokens (JWT) and is designed to streamline training management processes.

## Features

- API-based authentication with JWT.
- Modular and scalable architecture.
- Easy-to-use endpoints for managing training sessions, users, and roles.
- File upload support with public access via symbolic links.

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

6. Create the database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE training_app;"
   ```

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Create a symbolic link for file storage:
   ```bash
   php artisan storage:link
   ```

   This command creates a symbolic link from `public/storage` to `storage/app/public`, allowing public access to uploaded files.

9. (Optional) Seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

10. Start the development server:
    ```bash
    php artisan serve
    ```

### File Uploads

Uploaded files are stored in the `storage/app/public` directory. To make them publicly accessible, ensure you have run the following command:
```bash
php artisan storage:link
```

### File Validation Rules

- Allowed file types: `pdf`, `doc`, `docx`
- Maximum file size: `2MB`

### Folder Structure

- `app/` - Contains the core application code (models, controllers, etc.).
- `routes/` - Defines the API routes.
- `database/` - Contains migrations, seeders, and factories.
- `config/` - Configuration files for the application.
- `resources/` - Views, language files, and frontend assets.
- `tests/` - Unit and feature tests.

### Authentication

The application uses JWT for authentication. To log in, use the `/api/login` endpoint with valid credentials. The response will include a token to be used for subsequent API requests.

## Troubleshooting

- **File Not Found Errors**: Ensure the symbolic link for storage is created using `php artisan storage:link`.
- **Database Connection Issues**: Verify your `.env` file contains the correct database credentials.
- **Empty Request Data**: If `PUT` requests are not sending data, ensure the frontend uses `multipart/form-data` for file uploads and the backend handles `PUT` requests correctly.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).