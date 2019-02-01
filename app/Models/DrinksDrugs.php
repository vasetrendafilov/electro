<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class DrinksDrugs Extends Model
{
	protected $table = "drinks_drugs";

	protected $fillable = [
		'type',
		'rank',
		'title',
		'description',
		'zaliha'
	];
	public function add($user,$kolicina)
	{
		$user_zaliha = json_decode($user->inventory->{$this->type},true);
		$price = $this->price->state($user->prom->place);
		if($user_zaliha[$this->id] + $kolicina <= $this->zaliha && $user_zaliha[$this->id] + $kolicina > 0 && $user->prom->hasMoney($price * $kolicina)){
			$user_zaliha[$this->id] += $kolicina;
			$user->prom->update(['pari' => $user->prom->pari - ($price * $kolicina)]);
			$user->inventory->update([$this->type => json_encode($user_zaliha)]);
			return true;
		}
		return false;
	}
	public function sell($user,$kolicina)
	{
		$user_zaliha = json_decode($user->inventory->{$this->type},true);
		if($user_zaliha[$this->id] - $kolicina >= 0){
			$user_zaliha[$this->id] -= $kolicina;
			$user->prom->update(['pari' => $user->prom->pari + ($this->price->state($user->prom->place) * $kolicina)]);
			$user->inventory->update([$this->type => json_encode($user_zaliha) ]);
			return true;
		}
		return false;
	}
	public function price()
	{
		return $this->hasOne('App\Models\Stocks','id');
	}
}
