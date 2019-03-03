<?php

namespace App\Helpers;

class Granka
{
  public $left;
  public $right;
  public $val;
  public $ids=[];
  public $id;

  public function __construct($next){
    $this->left = $next->left;
    $this->right = $next->right;
    $this->val = $next->val;
    array_push($this->ids,$next->id);
    $this->id = $next->id;
  }
  public function add($from,$to,$next)
  {
      $this->val += $next->val;
      $this->{$from} = $next->{$to};
      array_push($this->ids,$next->id);
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
  public function formula()
  {
    $temp1='R';
    $temp2='';
    foreach ($this->ids as $id) {
      $temp1 .= (string)$id;
      $temp2 .= " R$id +";
    }
    return $temp1.' = '.substr($temp2,0, -2)." = $this->val".'Ω';
  }
  public function makeId()
  {
    $temp ='';
    foreach ($this->ids as $id)
      $temp .= (string)$id;
  }
}
