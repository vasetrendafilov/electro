<?php

namespace App\Helpers;
use App\Helpers\Resistor;
use App\Helpers\Granka;
use App\Helpers\Otpornik;

class Kolo
{
  public $kolo;
  public $otpornik = [];
  public $jazli=[];
  public $granka=[];
  public $error=[];

  public function addKolo($kolo)
  {
      $this->kolo= $kolo;
      $temp = explode('_', $kolo);
      for ($i=0; $i < count($temp) ; $i+=3) {
          array_push($this->otpornik, new Otpornik((int)$temp[$i],(int)$temp[$i+1],(int)$temp[$i+2]));
          array_key_exists((int)$temp[$i], $this->jazli) ? $this->jazli[$temp[$i]]++ : $this->jazli[$temp[$i]]=1;
          array_key_exists((int)$temp[$i+2], $this->jazli) ? $this->jazli[$temp[$i+2]]++ : $this->jazli[$temp[$i+2]]=1;
        }
        $this->jazli[0]++;
        $this->jazli[100]++;
  }
  public function calc()
  {

    $cnt=0;
    while($this->otpornik != null){
      $this->granka[$cnt] = new Granka($this->otpornik[0]);
      array_splice($this->otpornik,0,1);
      for ($i=0; $i < count($this->otpornik); $i++)
        if($this->granka[$cnt]->check($this->otpornik[$i],$this->jazli)){
          array_splice($this->otpornik,$i,1);
          $i=-1;
        }
       $cnt++;
    }
    foreach ($this->granka as $key => $value)
      array_push($this->error,var_export($value, true));
    $err=0;
    while(count($this->granka) > 1){
      $err++;
       $this->paralel();
       $this->serial();
       if($err < 10){
       foreach ($this->granka as $key => $value)
        array_push($this->error,var_export($value, true));
       array_push($this->error,var_export($this->jazli, true));
     }else return false;
    }
    return true;
  }
  public function serial()
  {
    $temp = array_values($this->granka);
    $this->granka = [];
    $cnt=0;
    while($temp != null){
      $this->granka[$cnt] = new Granka($temp[0]);
      array_splice($temp,0,1);
      for ($i=0; $i < count($temp); $i++)
        if($this->granka[$cnt]->check($temp[$i],$this->jazli)){
          array_splice($temp,$i,1);
          $i=-1;
        }
       $cnt++;
    }
  }
  public function paralel()
  {
    foreach ($this->granka as $key => &$otpornik) {
        $vrski = $this->get($otpornik);
        if(count($vrski) > 1){
          $zbir = 0;
          foreach ($vrski as $i){
            $zbir += 1/$this->granka[$i]->val;
            if($i != $key )unset($this->granka[$i]);
          }
          $otpornik->val = 1/$zbir;
          $this->jazli[$otpornik->left]-=count($vrski)-1;
          $this->jazli[$otpornik->right]-=count($vrski)-1;
          break;
        }
    }
  }
  public function get($node)
  {
    $temp = [];
    foreach ($this->granka as $key => $otpornik)
      if($otpornik->left == $node->left && $otpornik->right == $node->right || $otpornik->left == $node->right && $otpornik->right == $node->left)
      array_push($temp,$key);
    return $temp;
  }
}
