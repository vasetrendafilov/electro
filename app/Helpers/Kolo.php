<?php

namespace App\Helpers;
use App\Helpers\Otpornik;
class Kolo
{
  public $kolo;
  public $otpornik=[];
  public $jazli=[];
  public $start,$end;

  public function addKolo($kolo){
    $this->kolo= $kolo;
    $temp = explode('_', $kolo);
    $temp1=$temp2=$temp3=[];
    for ($i=0; $i < count($temp) ; $i+=3) {
      array_push($this->otpornik,new Otpornik((int)$temp[$i],(int)$temp[$i+2],(int)$temp[$i+1]));
      array_key_exists((int)$temp[$i], $this->jazli) ? $this->jazli[$temp[$i]]++ : array_push($this->jazli,1);
      array_key_exists((int)$temp[$i+2], $this->jazli) ? $this->jazli[$temp[$i+2]]++ : array_push($this->jazli,1);
    }
    $this->jazli[0]++;
    $this->jazli[count($this->jazli)-1]++;
    $this->start = $temp[0];
    $this->end = $temp[count($temp)-1];
  }
  public function calcType(){
      foreach ($this->otpornik as $key => $val)
          if($this->jazli[$val->left] == 2 || $this->jazli[$val->right] == 2)
            $this->otpornik[$key]->type = true;
          else if ($this->jazli[$val->left] == $this->jazli[$val->right])
            $this->otpornik[$key]->type = false;
          else
            $this->otpornik[$key]->type = null;
  }
  public function calc()
  {
      foreach ($this->otpornik as $key => $val){
        if($val->type == true){
            $temp=[];
            array_push($temp,$val->left,$val->right);
            $node = $val->right;
            foreach ($this->otpornik as $key1 => $val1)
              if($val1->left == $node && $val1->type == true && $this->jazli[$val1->left] == 2){
                array_push($temp,$val1->left,$val1->right);
                $node = $val1->right;
              }
          if(count($temp)>2) return $this->serial($temp);//dva elementi
        }
        if($val->type == false){
           if(count($this->getOtpornik($val->left,$val->right))>1)
              return $this->paralel($val->left,$val->right);
        }

      }
  }
  public function getOtpornik($left,$right)
  {
    $temp = [];
    foreach ($this->otpornik as $key => $val)
      if($val->left == $left && $val->right == $right)
          array_push($temp,$val);
    return $temp;
  }
  public function getIndex($left,$right)
  {
    $temp = [];
    foreach ($this->otpornik as $key => $val)
      if($val->left == $left && $val->right == $right)
         array_push($temp,$key);
    return $temp;
  }
  public function removeOtpornik($left,$right)
  {
    foreach ($this->otpornik as $key => $val)
      if($val->left == $left && $val->right == $right){
        unset($this->otpornik[$key]);
        unset($this->jazli[$val->left]);
        break;
      }
  }
  public function removeOtpornikParalel($left,$right)
  {
    $k=0;
     foreach ($this->otpornik as $key => $val)
       if($val->left == $left && $val->right == $right && $k++>0)
         unset($this->otpornik[$key]);
    $this->jazli[$left] = 2;
    $this->jazli[$right] = 2;
  }
  public function serial($nodes)
  {
    $zbir = $this->getOtpornik($nodes[0],$nodes[1])[0]->value;
    for ($i=2; $i < count($nodes); $i+=2){
      $zbir += $this->getOtpornik($nodes[$i],$nodes[$i+1])[0]->value;
      $this->removeOtpornik($nodes[$i],$nodes[$i+1]);
    }
    $this->otpornik[$this->getIndex($nodes[0],$nodes[1])[0]]->value = $zbir;
    $this->otpornik[$this->getIndex($nodes[0],$nodes[1])[0]]->right = $nodes[$i-1];
    return true;
  }
  public function paralel($startNode,$endNode)
  {
    $otpornik = $this->getOtpornik($startNode,$endNode);
    $zbir = 1/$otpornik[0]->value;
    for ($i=1; $i < count($otpornik); $i++){
      $zbir += 1/$otpornik[$i]->value;
    }
    $this->removeOtpornikParalel($startNode,$endNode);
    $this->otpornik[$this->getIndex($startNode,$endNode)[0]]->value = 1/$zbir;
    return true;
  }


}
