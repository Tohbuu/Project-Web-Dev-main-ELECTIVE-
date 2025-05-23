<?php

// Get environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Get database connection details
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$database = getenv('DB_DATABASE') ?: 'laravel';
$username = getenv('DB_USERNAME') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '';

echo "Testing PostgreSQL Connection\n";
echo "============================\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? 'Not set' : '********') . "\n\n";

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;user=$username;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connection successful!\n\n";
    
    // Get PostgreSQL version
    $stmt = $pdo->query('SELECT version()');
    $version = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "PostgreSQL Version: " . $version['version'] . "\n\n";
    
    // Check if migrations table exists
    $stmt = $pdo->query("SELECT to_regclass('public.migrations')");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['to_regclass']) {
        echo "Migrations table exists.\n";
        
        // Get migration count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM migrations");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Number of migrations: " . $count['count'] . "\n\n";
        
        // List the last 5 migrations
        echo "Last 5 migrations:\n";
        $stmt = $pdo->query("SELECT migration, batch FROM migrations ORDER BY batch DESC, migration DESC LIMIT 5");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['migration'] . " (Batch: " . $row['batch'] . ")\n";
        }
    } else {
        echo "Migrations table does not exist. It will be created when you run migrations.\n";
    }
    
    // Check database size
    $stmt = $pdo->query("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
    $size = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nDatabase size: " . $size['size'] . "\n";
    
    // List tables
    echo "\nDatabase tables:\n";
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "- " . $table['table_name'] . "\n";
        }
    } else {
        echo "No tables found in the database.\n";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    
    // Additional diagnostics
    echo "\nDiagnostic Information:\n";
    echo "=====================\n";
    
    // Check if the host is reachable
    echo "Checking if host is reachable...\n";
    $socket = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($socket) {
        echo "Host is reachable on port $port.\n";
        fclose($socket);
    } else {
        echo "Cannot reach host on port $port: $errstr ($errno)\n";
    }
    
    // Check if the database exists
    try {
        $dsn = "pgsql:host=$host;port=$port;user=$username;password=$password";
        $pdo = new PDO($dsn);
        $stmt = $pdo->query("SELECT datname FROM pg_database WHERE datname = '$database'");
        $dbExists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbExists) {
            echo "Database '$database' exists.\n";
        } else {
            echo "Database '$database' does not exist!\n";
        }
    } catch (PDOException $e) {
        echo "Cannot check if database exists: " . $e->getMessage() . "\n";
    }
    
    exit(1);
}
