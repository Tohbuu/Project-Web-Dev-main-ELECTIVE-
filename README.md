# Pizza Ordering Web Application

## Project Overview

This is a web-based pizza ordering application built with Laravel, featuring a user-friendly interface for customers to browse and order pizzas online. The application includes user authentication, a product catalog with various pizza options, shopping cart functionality, and order processing capabilities.

## Features

- **User Authentication**: Register, login, and profile management using Laravel Breeze
- **Product Catalog**: Browse different pizza varieties with images, descriptions, and prices
- **Shopping Cart**: Add pizzas to cart, modify quantities, and proceed to checkout
- **Order Management**: Track order status and history
- **Responsive Design**: Mobile-friendly interface using TailwindCSS

## Technology Stack

- **Backend**: PHP 8.x, Laravel 10.x
- **Frontend**: HTML, CSS, JavaScript, AlpineJS
- **CSS Framework**: TailwindCSS
- **Build Tool**: Vite
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze
- **API Integration**: Laravel Socialite for social login options

## Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL database
- Git

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/Project-Web-Dev-main-ELECTIVE-.git
   cd Project-Web-Dev-main-ELECTIVE-
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

5. **Configure your database connection**  
   Edit the `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

6. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets:**
   ```bash
   npm run build
   ```

---

## Running the Application

### Development Mode

1. **Start the Laravel development server:**
   ```bash
   php artisan serve
   ```

2. **In a separate terminal, run the Vite development server:**
   ```bash
   npm run dev
   ```

3. **Access the application:**  
   Open [http://localhost:8000](http://localhost:8000) in your browser.

### Production Mode

1. **Build the assets for production:**
   ```bash
   npm run build
   ```

2. **Configure your web server (Apache/Nginx) to point to the `public` directory.**

---

## Project Structure

- `app/` - Core application code
  - `Http/Controllers/` - Controllers (including `FrontPageController`)
  - `Models/` - Database models
- `database/` - Migrations and seeders
- `public/` - Public files (images, entry point)
- `resources/` - Frontend assets
  - `views/` - Blade templates
  - `css/` - CSS files
  - `js/` - JavaScript files
- `routes/` - Application routes
- `config/` - Configuration files

---

## Available Pizza Menu

The application features a variety of pizzas including:

- Pepperoni Pizza
- Cheese Pizza
- Hawaiian Pizza
- Meat Pizza
- Overload Cheese Pizza

Each pizza comes with a detailed description and price information.

---

## Authentication

The application uses Laravel's built-in authentication system with Laravel Breeze. Users can:

- Register a new account
- Login with email and password
- Reset password
- Update profile information

---

## Order Process

1. Browse the pizza menu on the homepage
2. Add desired pizzas to the cart
3. Review cart items and adjust quantities if needed
4. Proceed to checkout
5. Complete the order by providing delivery information
6. Track order status

---

## Contributing

1. Fork the repository
2. Create a feature branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m 'Add some feature'
   ```
4. Push to the branch:
   ```bash
   git push origin feature-name
   ```
5. Submit a pull request

---

## License

This project is open-sourced software licensed under the MIT license.

This README.md provides a comprehensive overview of the pizza ordering web application, including its features, technology stack, installation instructions, and usage guidelines. The content is based on the code snippets and repository information provided, particularly focusing on the pizza menu items from the FrontPageController and the Laravel configuration details from the various files.
