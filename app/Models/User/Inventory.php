<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;


class Inventory Extends Model
{
	protected $table = "users_inventories";

	protected $fillable = [
		'drinks',
		'drugs',
    'finished_missions',
		'weapons',
    'crime_chance',
		'cars',
		'car_chance',
		'asets'
	];
	public static $defaults=[
		'drinks' => "{\"9\":0,\"10\":0,\"11\":0,\"12\":0,\"13\":0,\"14\":0,\"15\":0,\"16\":0,\"17\":0,\"18\":0,\"19\":0,\"20\":0}",
		'drugs' => "{\"1\":0,\"2\":0,\"3\":0,\"4\":0,\"5\":0,\"6\":0,\"7\":0,\"8\":0}",
		'finished_missions' => "",
		'weapons' => "{\"1\":0,\"2\":0,\"3\":0,\"4\":0,\"5\":0,\"6\":0,\"7\":0,\"8\":0,\"9\":0,\"10\":0,\"11\":0,\"12\":0,\"13\":0,\"14\":0,\"15\":0,\"16\":0,\"17\":0,\"18\":0,\"19\":0,\"20\":0,\"21\":0,\"22\":0}",
		'crime_chance' => '0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0',
		'car_chance' => '0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0',
		'asets' => "{\"23\":0,\"24\":0,\"25\":0,\"26\":0,\"27\":0,\"28\":0,\"29\":0,\"30\":0,\"31\":0}"
	];
	public function removeCar($car_id,$id)
	{
		$cars = json_decode($this->cars,true);
		$car = explode('_', $cars[$car_id]);
		$car[0]--;
		unset($car[$id]);
		$cars[$car_id]= (string)implode('_', $car);
		if($this->update(['cars' => json_encode($cars)])){
			return true;
		}
		return false;
 }
 public function addCar($car_id,$dmg)
 {
	 $cars = json_decode($this->cars,true);
	 $car = explode('_', $cars[$car_id]);
	 $car[0]++;
	 array_push($car,$dmg);
	 $cars[$car_id]= (string)implode('_', $car);
	 if($this->update(['cars' => json_encode($cars)])){
		 return true;
	 }
	 return false;
  }
	public function addWeapons($val)
	{
		$weapons = json_decode($this->weapons,true);
		switch ($val) {
			case 5:
				 $weapons[14]++;
				break;
			case 4:
				$weapons[7]+=2;
				$weapons[11]++;
				$weapons[2]++;
				$weapons[9]++;
				break;
			case 2:
				$weapons[13]++;
				$weapons[2]++;
				$weapons[3]++;
				$weapons[8]++;
				break;
		}
		$this->update(['weapons' => json_encode($weapons) ]);
	}
	public function zastita()
	{
		$asets = json_decode($this->asets,true);
		$asets[31] -= 1;
		$this->update(['asets' => json_encode($asets)]);
	}
	public function have($id)
	{
		return (bool)json_decode($this->asets,true)[$id];
	}
	public function zaliha($type,$id)
	{
		return json_decode($this->{$type},true)[$id];
	}
	public function chance($type,$id)
	{
		return round(explode('_', $this->{$type.'_chance'})[$id-1]);
	}
}
