<?php
//
//if(isset($_GET['signout'])) {
//    unset($_COOKIE['user_id']);
//    unset($_COOKIE['user_role']);
//    setcookie('user_id', null, -1);
//    setcookie('user_role' ,null, -1);
//}
//
//if(isset($_GET['signup'])) {
//    header('Location: account/registration.php');
//    exit;
//}
//
//if(isset($_GET['login'])) {
    $template = 2;
//    include $_SERVER['DOCUMENT_ROOT']."home.php";
//    exit;
//}
//
//if(isset($_GET['profile'])) {
    $template = 4;
//    include $_SERVER['DOCUMENT_ROOT']."home.php";
//    exit;
//}
//
//$template = 1;
//include $_SERVER['DOCUMENT_ROOT']."/home.php";
//
//
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', []);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$app->get('/', function (Request $request, Response $response) {
    return $this->view->render($response, 'game_page.php');
});
$app->run();
?>