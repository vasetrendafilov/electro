<?php

use App\Middleware\BeforeMiddleware;
use Noodlehaus\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_cache_limiter(false);
session_start();

require __DIR__.'/../vendor/autoload.php';

$app = new \Slim\App([
  'settings'=>[
      'displayErrorDetails' => true
  ]
]);

$container = $app->getContainer();
$container['config'] = function () {
  return Config::load(__DIR__.'/Config/development.json');
};

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule){
    return $capsule;
};
$container['csrf'] = function ($c){
    return new Slim\Csrf\Guard;
};
$container['auth'] = function ($container){
    return new \App\Auth\Auth($container);
};
$container['flash'] = function ($container){
    return new \Slim\Flash\Messages;
};
$container['randomlib'] = function ($container){
    $randomlib = new RandomLib\Factory;
    return $randomlib->getMediumStrengthGenerator();
};
$container['device'] = function ($container){
    return  new Mobile_Detect;
};
$container['hash'] = function ($container){
    return new \App\Helpers\Hash($container);
};
$container['view'] = function ($container){
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new \App\Views\CsrfExtension($container['csrf']));

    $view->getEnvironment()->addGlobal('auth',[
            'check'=> $container->auth->check(),
            'user' =>$container->auth->user(),
        ]);
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    $view->getEnvironment()->addGlobal('device', $container->device);
    $view->getEnvironment()->addGlobal('baseUrl', $container->config['app.baseUrl']);
    return $view;
};

$container['Validator'] = function ($container){
    return new App\Validation\Validator($container);
};
$container['HomeController'] = function($container){
    return new \App\Controllers\HomeController($container);
};
$container['AjaxController'] = function($container){
    return new \App\Controllers\AjaxController($container);
};
$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};
$container['UserController'] = function($container){
    return new \App\Controllers\UserController($container);
};
$container['AdminController'] = function($container){
    return new \App\Controllers\AdminController($container);
};
$container['Mail'] = function($container){
  $mailer = new PHPMailer;
  $mailer->SMTPDebug =  $container->config['mail.smtp_debug'];
  //voa bilo problemot $mail->isSMTP();na stranata
  $mailer->isSMTP();
  $mailer->Host =       $container->config['mail.host'];
  $mailer->SMTPAuth =   $container->config['mail.smtp_auth'];
  $mailer->Username =   $container->config['mail.username'];
  $mailer->Password =   $container->config['mail.password'];
  $mailer->SMTPSecure = $container->config['mail.smtp_secure'];
  $mailer->Port =       $container->config['mail.port'];
  $mailer->setFrom($container->config['mail.username'],"The Underworld");
  $mailer->isHTML(true);
  $mailer->CharSet = 'UTF-8';
  return new App\Mail\Mailer($container['view'], $mailer);
};

$app->add($container->get('csrf'));
$app->add(new BeforeMiddleware($container));

require __DIR__.'/../app/routes.php';
