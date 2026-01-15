# Picast Laravel - Domain & YouTube Management System

Picast Laravel is a comprehensive web application built with Laravel framework, designed for managing domains with WHOIS tracking, YouTube video processing, and role-based user access control. The application follows Domain-Driven Design (DDD) principles with a clean separation between business logic and technical implementation.

## 🚀 Features

### Core Functionality
- **🌐 Domain Management**: Complete CRUD operations for domain tracking with automated WHOIS updates
- **👥 User Role & Permission System**: Flexible role-based access control using Spatie Laravel Permission
- **📧 User Invitation System**: Secure user onboarding via invite codes
- **📅 Domain Expiration Monitoring**: Automated reminders for domain renewal deadlines
- **🎥 YouTube Video Processing**: Download and manage YouTube videos with queue processing
- **📱 Telegram Notifications**: Custom notification channel for important alerts
- **🔗 REST API**: Comprehensive API for external integrations and automation

### Development & Quality Assurance
- **🐳 Containerized Development**: Full Docker environment for consistent development
- **🔍 Static Analysis**: PHPStan at maximum level with Larastan for Laravel-specific analysis
- **✨ Code Quality**: Laravel Pint for PSR-12 code formatting
- **🧪 Comprehensive Testing**: PHPUnit tests with coverage reports
- **🚀 CI/CD Pipeline**: GitHub Actions for automated testing and deployment
- **📚 API Documentation**: Auto-generated OpenAPI documentation with Scribe

## 🛠️ Technology Stack

### Backend Technologies
- **Framework**: Laravel 12.x
- **PHP**: 8.4+ (Latest stable version)
- **Database**: MySQL 8.3
- **Cache**: Memcached
- **Queue**: Laravel Queue with Supervisor
- **Authentication**: Laravel Sanctum for API tokens

### Frontend Technologies
- **Build Tool**: Vite 7.0+
- **CSS Framework**: Tailwind CSS 4.0
- **JavaScript**: Axios for HTTP requests
- **UI**: Laravel Blade templates

### Key Dependencies

#### PHP Packages
- `spatie/laravel-permission` - Role and permission management
- `io-developer/php-whois` - WHOIS data retrieval
- `norkunas/youtube-dl-php` - YouTube video downloading
- `alaouy/youtube` - YouTube API integration
- `irazasyed/telegram-bot-sdk` - Telegram bot functionality
- `intervention/image` - Image processing
- `guzzlehttp/guzzle` - HTTP client
- `knuckleswtf/scribe` - API documentation generator

#### Development Tools
- `larastan/larastan` - PHPStan for Laravel
- `laravel/pint` - Code style fixer
- `barryvdh/laravel-debugbar` - Debug toolbar
- `fakerphp/faker` - Test data generation

### Infrastructure & DevOps
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx 1.17
- **Process Manager**: Supervisor for queue workers
- **Email Testing**: MailHog for development
- **Database Admin**: Adminer
- **Debugging**: XDebug with IDE integration

## 🏗️ Architecture Overview

The application implements Domain-Driven Design (DDD) with the following structure:

```
app/Context/
├── Domains/          # Domain management context
│   ├── Domain/       # Business models and entities
│   ├── Application/  # Services, policies, and contracts
│   └── Infrastructure/ # Controllers, jobs, and external integrations
├── User/             # User management context
│   ├── Domain/       # User-related models
│   └── Infrastructure/ # User controllers and services
└── Youtube/          # YouTube processing context
    ├── Domain/       # Video models and entities
    ├── Application/  # Video processing services
    └── Infrastructure/ # Video controllers and jobs
```

## 🐳 Local Development Setup

### Prerequisites
- Docker & Docker Compose
- Make (for convenience commands)
- Git

### Quick Start

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Simtel/picast-laravel.git
   cd picast-laravel
   ```

2. **Build Docker containers**:
   ```bash
   make build
   ```

3. **Start the development environment**:
   ```bash
   make up
   ```

4. **Install PHP dependencies**:
   ```bash
   make composer-install
   ```

5. **Create environment configuration**:
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file with your configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=picast
   DB_USERNAME=root
   DB_PASSWORD=example
   
   YOUTUBE_API_KEY=your_youtube_api_key
   TELEGRAM_BOT_TOKEN=your_telegram_bot_token
   ```

6. **Create databases**:
   - Access Adminer at [http://localhost:8080](http://localhost:8080)
   - Login with: Server: `db`, Username: `root`, Password: `example`
   - Create databases: `picast` and `picast_test`

7. **Run database migrations**:
   ```bash
   make migrate
   ```

8. **Seed the database with initial data**:
   ```bash
   make seed
   ```

9. **Install and build frontend assets**:
   ```bash
   npm install
   npm run build
   ```

### Development Services

Once started, the following services will be available:

| Service | URL | Description |
|---------|-----|-------------|
| Application | [http://localhost](http://localhost) | Main application |
| Adminer | [http://localhost:8080](http://localhost:8080) | Database administration |
| MailHog | [http://localhost:8025](http://localhost:8025) | Email testing interface |
| API Documentation | [http://localhost/api/documentation](http://localhost/docs) | Interactive API docs |

### Available Make Commands

```bash
make help              # Show all available commands
make up                # Start all containers
make down              # Stop all containers
make restart           # Restart containers
make cli               # Access PHP container shell
make mysql-console     # Access MySQL console
make migrate           # Run database migrations
make seed              # Seed database with test data
make test              # Run PHPUnit tests
make test-coverage     # Generate test coverage report
make phpstan           # Run static analysis
make pint              # Fix code style
make worker            # Start queue worker
```

## 📡 API Documentation

The application provides a comprehensive REST API for automation and external integrations.

### Base URL
```
https://your-domain.com/api/v1
```

### Authentication
API uses token-based authentication. Include the token in the Authorization header:
```
Authorization: Bearer YOUR_API_TOKEN
```

### Core Endpoints

#### User Management
```http
GET    /api/v1/user/current    # Get current authenticated user
```

#### Domain Management
```http
GET    /api/v1/domains         # List all user domains
POST   /api/v1/domains         # Create new domain
GET    /api/v1/domains/{id}    # Get domain details with WHOIS
PUT    /api/v1/domains/{id}    # Update domain
DELETE /api/v1/domains/{id}    # Delete domain
```

#### YouTube Video Management
```http
GET    /api/v1/video           # List user videos
POST   /api/v1/video           # Queue video for download
GET    /api/v1/video/{id}      # Get video details
PUT    /api/v1/video/{id}      # Update video
DELETE /api/v1/video/{id}      # Delete video
```

### Request Examples

#### Create Domain
```bash
curl -X POST https://your-domain.com/api/v1/domains \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "example.com"}'
```

#### Queue YouTube Video
```bash
curl -X POST https://your-domain.com/api/v1/video \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://youtube.com/watch?v=VIDEO_ID"}'
```

### Response Format
All API responses follow a consistent JSON format:

```json
{
  "data": {
    "id": 1,
    "name": "example.com",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  },
  "meta": {
    "pagination": {
      "current_page": 1,
      "total": 50
    }
  }
}
```

### Error Handling
API errors return appropriate HTTP status codes with detailed error messages:

```json
{
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

### Interactive Documentation
For detailed API documentation with interactive examples, visit:
- **Local**: [http://localhost/docs](http://localhost/docs)
- **OpenAPI Spec**: [http://localhost/docs/openapi.yaml](http://localhost/docs/openapi.yaml)

## 🧪 Testing

### Running Tests
```bash
# Run all tests
make test

# Run tests with coverage
make test-coverage

# Run specific test
docker exec -it picast_php php artisan test --filter DomainTest
```

### Test Structure
- **Feature Tests**: `/tests/Feature/` - Integration tests for full workflows
- **Unit Tests**: `/tests/Unit/` - Isolated unit tests for specific components
- **API Tests**: `/tests/Feature/Api/` - API endpoint tests

## 🔧 Code Quality

### Static Analysis
```bash
# Run PHPStan analysis
make phpstan

# Fix code style
make pint
```

### Pre-commit Hooks
Set up Git hooks for automatic code quality checks:
```bash
make set-githooks
```

## 📦 Deployment

### Production Requirements
- PHP 8.3+
- MySQL 8.0+
- Nginx or Apache
- Supervisor for queue workers
- SSL certificate for HTTPS

### Environment Configuration
Key environment variables for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=picast
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

# External APIs
YOUTUBE_API_KEY=your-youtube-api-key
TELEGRAM_BOT_TOKEN=your-telegram-bot-token

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_DRIVER=memcached
MEMCACHED_HOST=127.0.0.1
```

### Deployment Steps
1. Clone repository to production server
2. Install PHP dependencies: `composer install --no-dev --optimize-autoloader`
3. Configure environment variables
4. Run migrations: `php artisan migrate --force`
5. Build assets: `npm run build`
6. Set up queue workers with Supervisor
7. Configure web server (Nginx/Apache)
8. Set up SSL certificate

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes following PSR-12 standards
4. Run tests: `make test`
5. Run static analysis: `make phpstan`
6. Fix code style: `make pint`
7. Commit changes: `git commit -am 'Add new feature'`
8. Push to branch: `git push origin feature/new-feature`
9. Create Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For issues and questions:
- Create an issue on GitHub
- Check the [API documentation](http://localhost/docs) for API-related questions
- Review test files for usage examples