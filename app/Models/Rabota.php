<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Rabota Extends Model
{
	protected $table = "raboti";

	protected $fillable = [
		'type',
		'rank',
		'title',
		'chance',
		'time',
		'energija',
		'price'
	];
  public function getPrice()
  {
  return json_decode($this->price,true)['pari'];
  }
	public function calculate($user,$chance)
	{
		$user->energy->update([ 'energija' => $user->energy->energija - $this->energija ]);
		if($chance <= $this->chance){
       $prices = json_decode($this->price,true);
			 foreach ($prices as $key => $value) {
			 	$user->prom->update([ $key => $user->prom->{$key} + $value ]);
			 }
       return true;
		}
    return false;
	}
	public function crime($user,$chance)
	{
		$user->energy->update([ 'energija' => $user->energy->energija - $this->energija ]);
		$prices = json_decode($this->price,true);
		$crime_chances = explode('_', $user->inventory->crime_chance);
    switch (true) {
    	case $chance <= $crime_chances[$this->id-1]:
		  	$user->prom->update([ 'pari' => $user->prom->pari + $prices['pari'] ]);
    		$num = 0;
    		break;
			case $chance > $crime_chances[$this->id-1] && $chance <= $crime_chances[$this->id-1] + 20 :
				$num = 2;
				break;
    	default:
    		$num = 1;
    		break;
    }
		foreach ($prices as $key => $value) {
			if(!is_numeric($value)){
				if($key == "crime_chance"){
					$values = explode('_', $value);
					if($crime_chances[$this->id-1] + $values[$num] <=100){
						$crime_chances[$this->id-1] += $values[$num];
						$val = (string) implode("_", $crime_chances);
						$user->inventory->update([ $key => $val ]);
					}
				}else{
					$values = explode('_', $value);
					$user->prom->update([ $key => $user->prom->{$key} + $values[$num]]);
				}
			}
		}
		return $num;
	}

}
