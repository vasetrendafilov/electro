<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Shop Extends Model
{
	protected $table = "shop";

	protected $fillable = [
		'type',
		'rank',
		'title',
		'description',
		'price',
    'reward'
	];
	public function add_wepons($user,$kolicina)
	{
		$user_zaliha = json_decode($user->inventory->{$this->type},true);
		$kolicina -= $user_zaliha[$this->id];
		if($user->prom->hasMoney($this->price * $kolicina) && $kolicina > 0){
			$user_zaliha[$this->id] += $kolicina;
			$user->prom->update(['pari' => $user->prom->pari - ($this->price * $kolicina)]);
			$rewards = json_decode($this->reward,true);
			foreach ($rewards as $key => $value) {
			 $user->prom->update([ $key => $user->prom->{$key} + $value ]);
			}
			$user->inventory->update([$this->type => json_encode($user_zaliha) ]);
			return true;
		}
		return false;
	}
	public function unlock($user)
	{
		if($user->prom->place == $this->reward('1') && $user->prom->hasMoney($this->price) && !$user->bank->hasPermission($this->reward('name'))){
			$user->prom->update(['pari' => $user->prom->pari - $this->price]);
			if($this->id <= 26 && $this->id > 22){$user->bank->unlock($this->reward('name'));}
			$asets = json_decode($user->inventory->asets,true);
			$asets[$this->id] = 1;
			if($user->inventory->update(['asets' => json_encode($asets)])){
				return true;
			}
		}
		return false;
	}
	public function fillUp($user,$kolicina)
	{
    switch ($this->id) {
    	case 30:
				$kolicina -= $user->prom->points;
				if($user->prom->hasMoney($this->price * $kolicina) && $user->prom->points + $kolicina <= $this->reward && $kolicina > 0){
					$user->prom->update(['pari' => $user->prom->pari - $this->price * $kolicina]);
					$user->prom->update(['points'=> $user->prom->points + $kolicina]);
					return true;
				}break;
			case 31:
				$kolicina -= $user->inventory->zaliha('asets',31);
				if($user->prom->hasMoney($this->price * $kolicina) && $user->inventory->zaliha('asets',31) + $kolicina <= $this->reward &&  $kolicina > 0){
				$asets = json_decode($user->inventory->asets,true);
				$asets[31] += $kolicina ;
				$user->prom->update(['pari' => $user->prom->pari - $this->price * $kolicina]);
				$user->inventory->update(['asets' => json_encode($asets)]);
				return true;
			}break;
			case 32:
				$kolicina -= $user->energy->energija;
				if($user->prom->hasMoney($this->price * $kolicina) && $user->energy->energija + $kolicina <= $this->reward && $user->energy->energija + $kolicina > 0){
				$user->prom->update(['pari' => $user->prom->pari - $this->price * $kolicina]);
				$user->energy->update(['energija'=> $user->energy->energija + $kolicina]);
				return true;
			}break;
    }
		return false;
	}
	public function reward($val)
	{
		return json_decode($this->reward,true)[$val];
	}
}
