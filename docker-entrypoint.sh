#!/bin/bash
set -e

# Debug: Print all environment variables
echo "Environment variables:"
env | sort

# Debug: Check database connection variables
echo "DB_HOST value: ${DB_HOST}"
echo "DB_PORT value: ${DB_PORT}"
echo "DB_DATABASE value: ${DB_DATABASE}"
echo "DB_USERNAME value: ${DB_USERNAME}"
echo "DB_PASSWORD value: ${DB_PASSWORD:0:3}*** (first 3 chars only for security)"

# Create a new .env file with actual environment variables
cat > .env << EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-base64:JHz8z/RktRjihYfF5Pe6w0nc1/EzU0Dx1FgTey5inB4=}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# PostgreSQL Database Configuration
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-dpg-d0nsl43uibrs73c4mp9g-a}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-pizza_db_q04x}
DB_USERNAME=${DB_USERNAME:-pizza_db_q04x_user}
DB_PASSWORD=${DB_PASSWORD:-V8ujHuGwGmmfOvTT1TfCWbfBAh2kpFWu}
DB_SSLMODE=require
DB_SCHEMA=public
DB_CHARSET=utf8
DB_TIMEZONE=UTC

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=${CACHE_STORE:-database}

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME:-Laravel}"

VITE_APP_NAME="${APP_NAME:-Laravel}"

GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID:-}
GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET:-}
GOOGLE_REDIRECT_URI=${GOOGLE_REDIRECT_URI:-https://pizza-app.onrender.com/auth/google/callback}
EOF

# Display the generated .env file for debugging (remove in production)
echo "Generated .env file:"
cat .env

# Create PostgreSQL connection configuration file
echo "Creating PostgreSQL connection configuration..."
mkdir -p ~/.postgresql
cat > ~/.postgresql/pgpass << EOF
dpg-d0nsl43uibrs73c4mp9g-a:5432:pizza_db_q04x:pizza_db_q04x_user:V8ujHuGwGmmfOvTT1TfCWbfBAh2kpFWu
EOF
chmod 600 ~/.postgresql/pgpass

# Set PostgreSQL environment variables
export PGHOST=dpg-d0nsl43uibrs73c4mp9g-a
export PGPORT=5432
export PGDATABASE=pizza_db_q04x
export PGUSER=pizza_db_q04x_user
export PGPASSWORD=V8ujHuGwGmmfOvTT1TfCWbfBAh2kpFWu
export PGSSLMODE=require

# Test PostgreSQL connection with psql
echo "Testing PostgreSQL connection with psql..."
if psql -c '\l' > /dev/null 2>&1; then
    echo "PostgreSQL connection successful with psql!"
else
    echo "PostgreSQL connection failed with psql. Error code: $?"
    echo "Trying to connect with more details..."
    PGSSLMODE=require psql -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" -d "${DB_DATABASE}" -c '\l' || true
fi

# Test PostgreSQL connection with PHP PDO
echo "Testing PostgreSQL connection with PHP PDO..."
php -r "
try {
    echo "Attempting to connect to PostgreSQL database...\n";
    echo "Host: ${DB_HOST}\n";
    echo "Port: ${DB_PORT}\n";
    echo "Database: ${DB_DATABASE}\n";
    echo "Username: ${DB_USERNAME}\n";
    echo "SSL Mode: require\n";
    
    \$dsn = "pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE};user=${DB_USERNAME};password=${DB_PASSWORD};sslmode=require";
    \$pdo = new PDO(\$dsn);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "PDO connection successful!\n";
    
    \$stmt = \$pdo->query('SELECT version()');
    \$version = \$stmt->fetch();
    echo "PostgreSQL version: " . \$version[0] . "\n";
    
    // Check if migrations table exists
    \$stmt = \$pdo->query("SELECT to_regclass('public.migrations')");
    \$result = \$stmt->fetch();
    if (\$result[0]) {
        echo "Migrations table exists.\n";
        \$stmt = \$pdo->query("SELECT COUNT(*) FROM migrations");
        \$count = \$stmt->fetch();
        echo "Number of migrations: " . \$count[0] . "\n";
        
        // List the last 5 migrations
        echo "Last 5 migrations:\n";
        \$stmt = \$pdo->query("SELECT migration, batch FROM migrations ORDER BY batch DESC, migration DESC LIMIT 5");
        while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . \$row['migration'] . " (Batch: " . \$row['batch'] . ")\n";
        }
    } else {
        echo "Migrations table does not exist. Will be created during migration.\n";
    }
    
    // Check database size
    \$stmt = \$pdo->query("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
    \$size = \$stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nDatabase size: " . \$size['size'] . "\n";
    
    // List tables
    echo "\nDatabase tables:\n";
    \$stmt = \$pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    \$tables = \$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count(\$tables) > 0) {
        foreach (\$tables as \$table) {
            echo "- " . \$table['table_name'] . "\n";
        }
    } else {
        echo "No tables found in the database.\n";
    }
    
    \$pdo = null;
} catch(PDOException \$e) {
    echo "PDO Error: " . \$e->getMessage() . "\n";
}
"

# Check if the database is reachable with ping
echo "Testing network connectivity to database host..."
ping -c 4 "${DB_HOST}" || echo "Ping failed, but this might be expected in some environments."

# Check DNS resolution
echo "Testing DNS resolution for database host..."
nslookup "${DB_HOST}" || echo "DNS lookup failed, but this might be expected in some environments."

# Create a database configuration file for Laravel
echo "Creating database configuration file for Laravel..."
cat > config/database.php.new << EOF
<?php

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('DB_SCHEMA', 'public'),
            'sslmode' => env('DB_SSLMODE', 'require'),
        ],
    ],
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', 'laravel_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
EOF

# Only replace the database.php file if the new one was created successfully
if [ -f config/database.php.new ]; then
    mv config/database.php.new config/database.php
    echo "Database configuration file updated successfully."
else
    echo "Failed to create database configuration file."
fi

# Clear caches
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations with verbose output
echo "Running database migrations..."
php artisan migrate --force --verbose

# Seed the database if needed (uncomment if you have seeders)
# echo "Seeding the database..."
# php artisan db:seed --force

# Cache configuration for production
echo "Caching Laravel configuration for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check storage directory permissions
echo "Checking storage directory permissions..."
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
ls -la /var/www/html/storage
ls -la /var/www/html/bootstrap/cache

# Create a simple health check file
echo "Creating health check file..."
echo "<?php echo 'OK - ' . date('Y-m-d H:i:s'); ?>" > /var/www/html/public/health.php
chmod 644 /var/www/html/public/health.php

# Final check - verify Laravel is working
echo "Verifying Laravel installation..."
php artisan --version

echo "Starting Apache web server..."
# Start Apache
apache2-foreground