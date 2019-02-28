<?php

namespace App\Helpers;

class Otpornik
{
  public $left;
  public $right;
  public $val;

  public function __construct($left,$val,$right){
    $this->left = $left;
    $this->right = $right;
    $this->val = $val;
  }
}
