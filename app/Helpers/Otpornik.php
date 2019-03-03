<?php

namespace App\Helpers;

class Otpornik
{
  public $left;
  public $right;
  public $val;
  public $id;

  public function __construct($left,$id,$val,$right){
    $this->left = $left;
    $this->right = $right;
    $this->val = $val;
    $this->id = $id;
  }
}
