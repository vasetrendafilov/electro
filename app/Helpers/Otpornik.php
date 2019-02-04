<?php

namespace App\Helpers;

class Otpornik
{
  public $left=null;
  public $right=null;
  public $value;
  public $type;

  public function __construct($left,$right,$value){
    $this->left= $left;
    $this->right= $right;
    $this->value= $value;
  }

}
