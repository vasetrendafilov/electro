<?php

namespace App\Helpers;

class Hash
{
  protected $container;

  public function __construct($container){
    $this->container= $container;
  }
  public function hash($input){
    return hash('sha256', $input);
  }
  public function hashCheck($know, $user){
    return hash_equals($know, $user);
  }



}
