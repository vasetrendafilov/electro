<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Car Extends Model
{
	protected $table = "cars";

	protected $fillable = [
		'type',
		'rank',
    'titile',
    'energija',
    'price',
		'speed',
    "power",
    "seats",
    "reward",
		"time"
	];
  public function steal($user,$chance,$dmg)
  {
    $user->energy->update([ 'energija' => $user->energy->energija - $this->energija ]);
    $rewards = json_decode($this->reward,true);
    $car_chances = explode('_', $user->inventory->car_chance);
    //treba ubavo da se stavat idta za criminal za bava spredba
    switch (true) {
      case $chance <= $car_chances[$this->id-1]:
      //dodavanje na kolite vo sorage
      $cars = json_decode($user->inventory->cars,true);
      foreach ($cars as $key => $value) {
        if($key == $this->id){
         $values = explode('_', $value);
         $values[0]++;
         array_push($values,100-$dmg);
         $cars[$key] = (string)implode('_', $values);
				 $i=true;
       }
      }
			if($i != true){
				 $cars[$this->id] ='1_'.(100-$dmg);
			}
      $user->inventory->update(['cars' => json_encode($cars)]);
        $num = 0;
        break;
      case $chance > $car_chances[$this->id-1] && $chance <= $car_chances[$this->id-1] + 20 :
        $num = 2;
        break;
      default:
        $num = 1;
        break;
    }
    foreach ($rewards as $key => $value) {
        if($key == "car_chance"){
            $values = explode('_', $value);
            //dodava na sansata dobivkata
          if($car_chances[$this->id-1] + $values[$num] <100){
          $car_chances[$this->id-1] += $values[$num];
          $val = (string) implode("_", $car_chances);
          $user->inventory->update([ $key => $val ]);
          }
        }else{
          $user->prom->update([ $key => $user->prom->{$key} + $value]);
        }

    }
    return $num;
  }
	public function race($id,$user,$chance,$dmg)
	{
	  $user->energy->update([ 'energija' => $user->energy->energija - $this->energija ]);
		$cars = json_decode($user->inventory->cars,true);
		$cars_dmg = explode('_', $cars[$this->id]);
		if($cars_dmg[$id] >= 50){
				switch (true) {
					case $chance <= (25 + 3 * ($this->id - 1)):
				  	$cars_dmg[$id]-=$dmg+10;
            $user->prom->update(['pari'=> $user->prom->pari + $cars_dmg[$id]*$this->price ]);
            $num = 0;
						break;
					case $chance > (25 + 3 * ($this->id - 1)) && $chance <= (60 - 2 * ($this->id - 1)) :
						$cars_dmg[$id]-=$dmg +20;
					  $num = 1;
						break;
					default:
					  $user->inventory->removeCar($this->id,$id);
						return 2;
						break;
				}
				$cars[$this->id]= (string)implode('_', $cars_dmg);
				$user->inventory->update(['cars'=> json_encode($cars)]);
				return $num;
   	}
		return 3;
  }
	public function sell($user,$id)
	{
		$cars = json_decode($user->inventory->cars,true);
		$cars_dmg = explode('_', $cars[$this->id]);
		if($user->prom->update(['pari'=> $user->prom->pari + $cars_dmg[$id]*$this->price ])){
		  $user->inventory->removeCar($this->id,$id);
			return true;
		}
		return false;
	}
}
