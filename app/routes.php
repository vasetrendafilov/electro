<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\TaskMiddleware;

$app->get('/', 'HomeController:getPeople')->setName('home');

$app->group('',function() use ($app){

  $app->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
  $app->post('/auth/signup', 'AuthController:postSignUp');

  $app->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
  $app->post('/auth/signin', 'AuthController:postSignIn');

  $app->get('/recover-password', 'UserController:getRecoverPassword')->setName('password.recover');
  $app->post('/recover-password', 'UserController:postRecoverPassword');

  $app->get('/reset-password', 'UserController:getResetPassword')->setName('password.reset');
  $app->post('/reset-password', 'UserController:postResetPassword');

  $app->get('/ajax/validate', 'AjaxController:getValidation')->setName('validate');
  $app->get('/activate', 'AuthController:getActivate')->setName('activate');
  $app->get('/send/activate', 'AuthController:getSendActivate')->setName('send.active');

})->add(new GuestMiddleware($container));

$app->group('',function() use ($app){

  $app->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

  $app->get('/update/profile', 'UserController:getUpdate')->setName('update');
  $app->post('/update/profile', 'UserController:postUpdate');

  $app->get('/ajax/soon', 'AjaxController:getSoon')->setName('soon');
  //get kriminal
  $app->get('/ajax/rabota', 'AjaxController:getRabota')->setName('rabotas');
  $app->get('/ajax/drinks', 'AjaxController:getDrinks')->setName('drinks');
  $app->get('/ajax/cars', 'AjaxController:getCars')->setName('cars');
  $app->get('/ajax/trki', 'AjaxController:getTrki')->setName('trki');
  $app->get('/ajax/planed_crime', 'AjaxController:getCrime')->setName('ajax.crime');
  //get lokacii
  $app->get('/ajax/hospital', 'AjaxController:getHospital')->setName('hospi');
  $app->get('/ajax/travel', 'AjaxController:getTravel')->setName('ajax.travel');
  $app->get('/ajax/garaza', 'AjaxController:getGaraza')->setName('garaza');
  $app->get('/ajax/shop', 'AjaxController:getShop')->setName('shops');
  $app->get('/ajax/bank', 'AjaxController:getBank')->setName('banks');

  $app->get('/ajax/search', 'AjaxController:getSearch')->setName('search');
  $app->get('/ajax/trkalo', 'AjaxController:getTrkalo')->setName('trkalo');
  $app->get('/post/trkalo', 'HomeController:getTrkalo')->setName('post.trkalo');

  $app->get('/ajax/blacklist', 'AjaxController:getBlacklist')->setName('ajax.blacklist');
  $app->get('/ajax/clan', 'AjaxController:getClan')->setName('ajax.clan');

  $app->get('/profile/people', 'AjaxController:getProfilePeople')->setName('profilePeople');
  $app->get('/chat/room', 'AjaxController:getChatRoom')->setName('chat.room');
  $app->get('/chat/add', 'AjaxController:getAddMsg')->setName('chat.add');

  $app->get('/profile', 'AjaxController:getProfile')->setName('profile');
  $app->get('/stats', 'AjaxController:getStats')->setName('stats');
  $app->get('/statistika', 'AjaxController:getStatistika')->setName('statistika');
  $app->get('/chatTiles', 'AjaxController:getChatTiles')->setName('chat.tiles');

  $app->get('/add/friend', 'HomeController:getAddFriend')->setName('add.friend');
  $app->get('/confirm/friend', 'HomeController:getConfirmFriend')->setName('confirm.friend');
  $app->get('/delete/friend', 'HomeController:getDeleteFriend')->setName('delete.friend');
  $app->get('/block/friend', 'HomeController:getBlockFriend')->setName('block.friend');
  $app->get('/unblock/friend', 'HomeController:getUnblockFriend')->setName('unblock.friend');
  $app->get('/clan', 'HomeController:getClan')->setName('clan');
  $app->get('/join/clan', 'HomeController:getJoinClan')->setName('join.clan');
  $app->get('/leave/clan', 'HomeController:getLeaveClan')->setName('leave.clan');
  $app->get('/remove/clan', 'HomeController:getRemoveClan')->setName('remove.clan');
  $app->get('/confirm/clan', 'HomeController:getConfirmClan')->setName('confirm.clan');
  $app->get('/moto/clan', 'HomeController:getMotoClan')->setName('moto.clan');
  $app->get('/profile/clan', 'AjaxController:getProfileClan')->setName('profile.clan');

})->add(new AuthMiddleware($container));

$app->group('',function() use ($app){

  //post kriminal
  $app->get('/post/rabota', 'HomeController:getRabota')->setName('rabota');
  $app->get('/post/crime', 'HomeController:getCrime')->setName('crime');
  $app->get('/post/drinks-drugs', 'HomeController:getDrinksDrugs')->setName('drinks-drugs');
  $app->get('/post/car', 'HomeController:getCar')->setName('car');
  $app->get('/post/race', 'HomeController:getRace')->setName('race');
  $app->get('/post/planed', 'HomeController:getPlanedCrime')->setName('planed_crime');
  $app->get('/post/planed/acept', 'HomeController:getPlanedAcept')->setName('planed_acept');
  $app->get('/post/planed/start', 'HomeController:getPlanedStart')->setName('planed_start');
  //post lokacii
  $app->get('/post/travel', 'HomeController:getTravel')->setName('travel');
  $app->get('/post/hospital', 'HomeController:getHospital')->setName('hospital');
  $app->get('/post/bank', 'HomeController:getBank')->setName('bank');
  $app->get('/post/shop', 'HomeController:getShop')->setName('shop');
  $app->get('/post/sell/car', 'HomeController:getSellCar')->setName('sellcar');
  //post special
  $app->get('/post/atack', 'HomeController:getAtack')->setName('atack');
  $app->get('/post/pocit', 'HomeController:getPocit')->setName('pocit');

})->add(new TaskMiddleware($container));

$app->group('',function() use ($app){

  $app->get('/admin', 'AdminController:getAdmin')->setName('admin');
  $app->post('/admin', 'AdminController:postAdmin');

})->add(new AdminMiddleware($container));
