<?php
require_once __DIR__ . '/vendor/autoload.php';

// Define the template directory
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');

// Configure Twig environment
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/cache',
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());
