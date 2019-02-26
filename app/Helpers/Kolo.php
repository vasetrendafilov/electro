<?php

namespace App\Helpers;
use App\Helpers\Resistor;
class Kolo
{
  public $kolo;
  public $otpornik;
  public $jazli=[];
  public $start,$end;

  public function addKolo($kolo)
  {
      $this->kolo= $kolo;
      $temp = explode('_', $kolo);
      $this->otpornik = new Resistor((int)$temp[0],(int)$temp[1],(int)$temp[2]);
      $this->jazli[$temp[0]]=1;
      $this->jazli[$temp[2]]=1;
      for ($i=3; $i < count($temp) ; $i+=3) {
          $this->otpornik->add((int)$temp[$i],(int)$temp[$i+1],(int)$temp[$i+2]);
          array_key_exists((int)$temp[$i], $this->jazli) ? $this->jazli[$temp[$i]]++ : $this->jazli[$temp[$i]]=1;
          array_key_exists((int)$temp[$i+2], $this->jazli) ? $this->jazli[$temp[$i+2]]++ : $this->jazli[$temp[$i+2]]=1;
        }
      $this->jazli[$temp[0]]++;
      $this->jazli[$temp[count($temp)-1]]++;
      $this->otpornik->fix();
  }
}
