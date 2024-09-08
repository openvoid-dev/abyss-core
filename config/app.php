<?php
// * Start the application
use Abyss\Core\Application;
use Abyss\Outsider\Outsider;

// * Configure app
Application::configure(dirname(__DIR__));

// * Connect to the database
$config = require_once __DIR__ . '/../config/database.php';
Outsider::connect($config);

// * Start the router
Application::handle_request();
