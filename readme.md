# Home Project on Laravel

This project is a web application developed on Laravel, featuring a set of capabilities designed for managing domains and users.

## Features

- **User Role System**: Each user can have one or more roles, allowing for customized access to different parts of the system.
- **User Invitation System**: Convenient functionality for inviting users to the system.
- **Domain Notebook Functionality**: Allows adding domains, checking whois information, and receiving reminders about registration deadlines.
- **API for Domain Management**: Ability to interact with the system via API (documentation available [here](https://armisimtel.ru/openapi)).
- **CI/CD via GitHub Actions**: Automation of the processes for testing and deploying the application.
- **PHPStan at Maximum Level**: Static code analysis to improve its quality and reliability.

## Local Development with Docker

For convenient development and testing of the application, Docker containers are used:

- **PHP**: 8.3 FPM
- **MySQL** + **Adminer**
- **Nginx**: 1.13
- **XDebug**: Debugging tool.
- **Memcache**: For caching.
- **MailHog**: For capturing and testing email messages.

### Setup Instructions

1. **Clone the repository**:

   ```bash
   git clone https://github.com/Simtel/picast-laravel
   ```

2. **Build the containers**:

   ```bash
   make build
   ```

3. **Run the containers**:

   ```bash
   make up
   ```

4. **Create the databases** `picast` and `picast_test` through Adminer at [http://localhost:8080](http://localhost:8080).

5. **Install the dependencies**:

   ```bash
   make composer-install
   ```

6. **Create the .env file from the example**:

   ```bash
   cp .env.example .env
   ```

7. **Run migrations**:

   ```bash
   make migrate
   ```

8. **Seed the tables with base data**:

   ```bash
   make seed
   ```