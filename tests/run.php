<?php

// Set error reporting
error_reporting(-1);
ini_set('display_errors', '1');

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Run PHPUnit
$command = 'vendor/bin/phpunit';
$output = [];
$returnVar = 0;

exec($command, $output, $returnVar);

// Display results
echo "\nTest Results:\n";
echo "============\n\n";

foreach ($output as $line) {
    echo $line . "\n";
}

echo "\nExit Code: " . $returnVar . "\n";

// Exit with appropriate status code
exit($returnVar); 