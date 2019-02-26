<?php

namespace App\Helpers;

class Resistor
{
  public $left;
  public $right;
  public $val;
  public $type;
  public $child = null;

  public function __construct($left,$val,$right){
    $this->left= $left;
    $this->right= $right;
    $this->val= $val;
  }
  public function add($left,$val,$right)
  {
    if($this->child == null) $this->child = new Resistor($left,$val,$right);
    else $this->child->add($left,$val,$right);
  }
  public function serial($nodes)
  {
    $zbir = $this->get($nodes[0],$nodes[1])[0]->val;
    for ($i=2; $i < count($nodes); $i+=2){
      $temp = $this->get($nodes[$i],$nodes[$i+1])[0]->val;
      $zbir += $temp;
      $this->delete($nodes[$i],$temp,$nodes[$i+1]);
    }
    if($this->chage($nodes[0],$this->get($nodes[0],$nodes[1])[0]->val,$nodes[1],$nodes[0],$zbir,$nodes[$i-1])) return true;
    return false;
  }
  public function paralel($startNode,$endNode)
  {
    $otpornik = $this->get($startNode,$endNode);
    $zbir = 1/$otpornik[0]->val;
    for ($i=1; $i < count($otpornik); $i++){
      $zbir += 1/$otpornik[$i]->val;
      $this->delete($startNode,$otpornik[$i]->val,$endNode);
    }
    if($this->chage($startNode,$otpornik[0]->val,$endNode,$startNode,1/$zbir,$endNode)) return true;
    return false;
  }
  public function addType($jazli)
  {
    if($jazli[$this->left] == 2 || $jazli[$this->right] == 2) $this->type = true;
    else if ($jazli[$this->left] == $jazli[$this->right]) $this->type = false;
    else  $this->type = null;
    if($this->child == null) return true;
    return $this->child->addType($jazli);
  }
  public function calc(&$jazli)
  {
    if($this->type == true){
      $temp=[];
      array_push($temp,$this->left,$this->right);
      $next = $this->right;
      while($res = $this->find($next,$jazli)){
          array_push($temp,$res->left,$res->right);
          $next = $res->right;
      }
      if(count($temp)>2)
      if($this->serial($temp)){
        for ($i=1; $i < count($temp)-1; $i++)
          unset($jazli[$temp[$i]]);
        return true;
      }
    }
    if($this->type == false){
      $vrski = count($this->get($this->left,$this->right));
     if($vrski>1)
        if($this->paralel($this->left,$this->right)){
          $jazli[$this->left]-=$vrski-1;
          $jazli[$this->right]-=$vrski-1;
          return true;
        }
    }
    if($this->child == null) return true;
    return $this->child->calc($jazli);
  }
  public function get($left,$right,$temp=[])
  {
    if($this->left == $left && $this->right == $right) array_push($temp,(object) ['left' => $this->left,'val'=> $this->val, 'right'=> $this->right]);
    if ($this->child == null) return $temp;
    return $this->child->get($left,$right,$temp);
  }
  public function find($right,$jazli)
  {
    if($this->type == true  && $this->left == $right && $jazli[$this->left] == 2)
    return (object) ['left' => $this->left,'right'=> $this->right];
    if ($this->child == null) return false;
    return $this->child->find($right,$jazli);
  }
  public function chage($left,$val,$right,$leftNew,$valNew,$rightNew)
  {
    if($this->left == $left && $this->right == $right && $this->val == $val){
      $this->left= $leftNew;
      $this->right= $rightNew;
      $this->val= $valNew;
      return true;
    }
    if ($this->child == null) return false;
    return $this->child->chage($left,$val,$right,$leftNew,$valNew,$rightNew);
  }
  public function fix()
  {
    if($this->left > $this->right){
      $temp = $this->left;
      $this->left = $this->right;
      $this->right = $temp;
    }
    if ($this->child == null) return false;
    return $this->child->fix();
  }
  public function delete($left,$val,$right)
  {
    if ($this->child == null) return false;
    if($this->child->left == $left && $this->child->right == $right && $this->child->val == $val){
      $this->child = $this->child->child;
      return true;
    }
    return $this->child->delete($left,$val,$right);
  }
}
