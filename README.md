# Page Customizer

A Laravel-based web application that allows clients to customize their web pages and view analytics through a modern dashboard interface. Built with Laravel 12 backend and Vue.js 3 frontend.

## Features

- **Page Customization**: Create and customize web pages with editable content, themes, and media uploads
- **Theme Management**: Multiple pre-built themes with customizable color schemes
- **Analytics Dashboard**: Track page views, unique visitors, and returning visitors with interactive charts
- **Media Management**: Upload and manage logos and background images
- **Public Pages**: Publish pages with unique URLs for public viewing
- **User Authentication**: Secure registration and login system with Laravel Sanctum
- **Responsive Design**: Modern UI built with Tailwind CSS

## Technology Stack

### Backend
- **Laravel 12** - PHP framework
- **Laravel Sanctum** - API authentication
- **SQLite** - Database (configurable)
- **Laravel Storage** - File management

### Frontend
- **Vue.js 3** - Frontend framework
- **TypeScript** - Type safety
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Tailwind CSS** - Styling framework
- **Chart.js** - Analytics visualization
- **Axios** - HTTP client

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 20.19.0 or higher
- npm or yarn

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd page-customizer
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with database settings:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 4. Database Setup

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed themes (optional)
php artisan db:seed --class=PageThemeSeeder
```

### 5. Install Frontend Dependencies

```bash
cd frontend/page-customizer-frontend
npm install
```

### 6. Build Assets

```bash
# From project root
npm install
npm run build
```

## Development

### Start Development Servers

The project includes a convenient development script that starts all necessary services:

```bash
composer run dev
```

This command starts:
- Laravel development server (port 8000)
- Queue worker
- Log viewer
- Vite development server

### Manual Development Setup

If you prefer to run services manually:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:listen

# Terminal 3: Frontend development
cd frontend/page-customizer-frontend
npm run dev
```

### Frontend Development

```bash
cd frontend/page-customizer-frontend
npm run dev
```

The frontend will be available at `http://localhost:5173`

## API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout (authenticated)
- `GET /api/user` - Get current user (authenticated)

### Page Management
- `GET /api/my-page` - Get user's page (authenticated)
- `PUT /api/my-page` - Update page content (authenticated)
- `POST /api/my-page/logo` - Upload logo (authenticated)
- `POST /api/my-page/background` - Upload background image (authenticated)

### Themes
- `GET /api/themes` - Get available themes (authenticated)

### Analytics
- `GET /api/analytics` - Get analytics data (authenticated)
- `GET /api/analytics/export` - Export analytics as CSV (authenticated)

### Public Pages
- `GET /pages/{slug}` - View public page

## Project Structure

```
page-customizer/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/           # API controllers
│   │   └── PublicPageController.php
│   └── Models/            # Eloquent models
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── frontend/
│   └── page-customizer-frontend/
│       ├── src/
│       │   ├── components/   # Vue components
│       │   ├── views/         # Vue views
│       │   ├── stores/        # Pinia stores
│       │   └── router/        # Vue Router config
│       └── package.json
├── public/               # Laravel public directory
├── resources/
│   └── views/            # Blade templates
└── routes/
    ├── api.php          # API routes
    └── web.php          # Web routes
```

## Configuration

### Storage Configuration

The application uses Laravel's storage system for file uploads. Configure storage settings in `config/filesystems.php`:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### CORS Configuration

API endpoints are configured for CORS. Modify `config/cors.php` if needed.

## Testing

```bash
# Run PHP tests
php artisan test

# Run frontend tests (if configured)
cd frontend/page-customizer-frontend
npm test
```

## Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed production deployment instructions.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please refer to the documentation files:
- [ARCHITECTURE.md](ARCHITECTURE.md) - System architecture overview
- [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Database structure
- [USER_GUIDE.md](USER_GUIDE.md) - End-user instructions
- [DEPLOYMENT.md](DEPLOYMENT.md) - Production deployment guide