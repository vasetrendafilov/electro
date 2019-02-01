<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Drzava Extends Model
{
	protected $table = "drzavi";

	protected $fillable = [
		'name',
		'relacii',
	];
  public function travel($user,$destination)
  {
    $price = json_decode($this->relacii,true)[$destination];
    if($user->prom->hasMoney($price * 5)){
      $user->prom->update([ 'pari' => $user->prom->pari - ($price * 5)]);
      $user->prom->update([ 'place' => $destination]);
      return true;
    }
   return false;
  }
	public function price()
	{
		$ceni = array();
		$relacii = json_decode($this->relacii,true);
		foreach ($relacii as $key => $value) {
			$ceni[$key] = $value * 5;
		}
		return $ceni;
	}

}
