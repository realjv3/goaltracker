<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//db connection
$container['db'] = function($c) {

    $settings = $c->get('settings')['db'];
    extract($settings);
    try {
        return new PDO("mysql:host=$host;dbname=$db_name", $id, $password);
    } catch(PDOException $e) {
        die("<span style='color: red'>Error connecting to database: </span>" . $e->getMessage() . "<br/>");
    }
};
