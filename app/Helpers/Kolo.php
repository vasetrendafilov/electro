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
  public $steps=[];

  public function addKolo($kolo)
  {
      $this->kolo= $kolo;
      $temp = explode('_', $kolo);
      for ($i=0; $i < count($temp) ; $i+=4) {
          array_push($this->otpornik, new Otpornik((int)$temp[$i],(int)$temp[$i+1],(int)$temp[$i+2],(int)$temp[$i+3]));
          array_key_exists((int)$temp[$i], $this->jazli) ? $this->jazli[$temp[$i]]++ : $this->jazli[$temp[$i]]=1;
          array_key_exists((int)$temp[$i+3], $this->jazli) ? $this->jazli[$temp[$i+3]]++ : $this->jazli[$temp[$i+3]]=1;
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
      foreach ($this->granka as $key => $granka)
      if(count($granka->ids)>1){
        array_push($this->steps,$granka->formula());
        $granka->makeId();
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
    foreach ($this->granka as $key => $granka)
    if(count($granka->ids)>1){
      array_push($this->steps,$granka->formula());
      $granka->makeId();
    }
  }
  public function paralel()
  {
    foreach ($this->granka as $key => &$otpornik) {
        $vrski = $this->get($otpornik);
        if(count($vrski) > 1){
          $zbir = 0;
          $temp = [];
          foreach ($vrski as $i){
            $zbir += 1/$this->granka[$i]->val;
            array_push($temp,$this->granka[$i]->id);
            if($i != $key ){
              $otpornik->id .= $this->granka[$i]->id;
              unset($this->granka[$i]);
            }
          }
          array_push($this->steps,($this->paralelFormula($temp)).' = '.(1/$zbir).'Ω');
          $otpornik->val = 1/$zbir;
          $this->jazli[$otpornik->left]-=count($vrski)-1;
          $this->jazli[$otpornik->right]-=count($vrski)-1;
          break;
        }
    }
  }
  public function paralelFormula($ids)
  {
    $temp1='1/R';
    $temp2='';
    foreach ($ids as $id) {
      $temp1 .= (string)$id;
      $temp2 .= " 1/R$id +";
    }
    return $temp1.' = '.substr($temp2,0, -2);
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
