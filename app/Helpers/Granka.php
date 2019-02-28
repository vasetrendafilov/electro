<?php

namespace App\Helpers;

class Granka
{
  public $left;
  public $right;
  public $val;

  public function __construct($next){
    $this->left = $next->left;
    $this->right = $next->right;
    $this->val = $next->val;
  }
  public function add($from,$to,$next)
  {
      $this->val += $next->val;
      $this->{$from} = $next->{$to};
      return true;
  }
  public function check($next,$jazli)
  {
    if ($jazli[$next->left] == 2) {
      if($this->right == $next->left) return $this->add('right','right',$next);
      else if($this->left == $next->left) return $this->add('left','right',$next);
    }
    if($jazli[$next->right] == 2){
      if($this->right == $next->right) return $this->add('right','left',$next);
      else if($this->left == $next->right) return $this->add('left','left',$next);
    }
    return false;
  }

}
