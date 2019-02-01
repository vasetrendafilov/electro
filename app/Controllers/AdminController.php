<?php

namespace App\Controllers;
use App\Models\User\User;
use App\Models\DrinksDrugs;

class AdminController extends Controller
{
  public function getAdmin($request, $response)
  {
    $user = $this->auth->user();
    return $this->view->render($response, 'admin.twig',[
      'user'  => $user,
    ]);
  }
  public function postAdmin($request, $response)
  {
    $user = $this->auth->user();
    $activate = $this->randomlib->generateString(128);
    $this->Mail->send('email/auth/activate.twig',['user' => $user, 'activate' => $activate],function($message) use ($user){
      $message->to($user->email);
      $message->subject('Vi blagodarime za registracija');
    });
  }

}
