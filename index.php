<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require_once 'Logic/Db/Db.php';
require_once 'Logic/Controller.php';
require_once 'Logic/HomePageController.php';
require_once 'Logic/AuthController.php';
require_once 'Logic/GamePageController.php';
require_once 'Logic/SignUpController.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', [
        'debag' => true,
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new Twig_Extensions_Extension_I18n());
    return $view;
};

$container['db'] = function () {
    return Db::getConnection();
};

define('BASE_PATH', realpath(dirname(__FILE__)));
define('LANGUAGES_PATH', BASE_PATH . '/langs');

if (isset($_COOKIE['locale']))
    $locale = $_COOKIE['locale'];
else {
    $locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    setcookie('locale', $locale);
}
putenv("LC_ALL=" . $locale);
putenv(("LANG=" . $locale));
setlocale(LC_ALL, $locale, $locale . '.utf-8');
bind_textdomain_codeset($locale, 'utf-8');
bindtextdomain($locale, LANGUAGES_PATH);
textdomain($locale);

$app->get('/', Logic\HomePageController::class . ':getHomePage');
$app->get('/login/', Logic\AuthController::class . ':getLoginForm');
$app->get('/forget_password/', Logic\AuthController::class . ':getForgetPass');
$app->post('/forget_pass/', Logic\AuthController::class . ':handlerForgetPass');
$app->get('/request/{md5email}', Logic\AuthController::class . ':getNewPassForm');
$app->get('/logout/', Logic\AuthController::class . ':logout');
$app->post('/login/', Logic\AuthController::class . ':signIn');
$app->get('/signup/', Logic\SignUpController::class . ':getSignUpForm');
$app->post('/signup/', Logic\SignUpController::class . ':signUp');
$app->get('/game_page/{project_name}', Logic\GamePageController::class . ':getGamePage');
$app->get('/blog/', function (Request $request, Response $response, array $args) {
    require 'blog/index.php';
});
$app->get('/blog/wp-admin/', function (Request $request, Response $response, array $args) {
    require 'blog/wp-admin/index.php';
});
$app->get ('/cookies_rules/', function(Request $request, Response $response, array $args) {
    return $this->view->render($response, 'cookies_rules.html');
});
$app->run();
?>
