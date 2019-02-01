<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Prom Extends Model
{
	protected $table = "users_prom";

	protected $fillable = [
		'place',
		'rank',
		'mok',
		'pocit',
		'pari',
		'iskustvo',
		'ubistva',
		'health',
		'lives',
		'points',
		'atack_wins',
    'atack_loses'
	];
	public static $defaults=[
		'place' => 'SV',
		'rank' => 1,
    'mok' => 10,
		'pocit' => 0,
		'pari' => 100,
		'iskustvo' => 100,
		'ubistva' => 0,
		'health' => 100,
		'lives' => 7,
		'points' => 0,
		'atack_wins' => 0,
		'atack_loses' => 0
  ];
	public function hasMoney($val){
		return ($this->pari >= $val ? true:false);
	}
	public function heal($kolicina)
	{
		if($this->hasMoney(10 * $kolicina) && $kolicina > 0 && $kolicina <=100 && $kolicina > $this->health){
			$kolicina -= $this->health;
			$this->update(['pari' => $this->pari - (10 * $kolicina)]);
			$this->update(['health' => $this->health + $kolicina]);
			return true;
		}
		return false;
	}
	public function atack($enemy)
	{
		$this->update(['points' => $this->points - 5]);
		if( $this->mok + $this->atack_wins + $this->ubistva * 10 > $enemy->prom->mok + $enemy->prom->atack_wins + $enemy->prom->ubistva * 10 ){
			$this->update(['iskustvo' => $this->iskustvo + $enemy->prom->rank * 7]);
			$this->increment('atack_wins');
			$enemy->prom->update(['iskustvo' => $enemy->prom->iskustvo + $this->rank * 2]);
			$enemy->prom->update(['health' => $enemy->prom->health - 10]);
			$enemy->prom->increment('atack_loses');
			return true;
		}
		$this->update(['iskustvo' => $this->iskustvo + $enemy->prom->rank * 2]);
		$this->increment('atack_loses');
		$enemy->prom->update(['iskustvo' => $enemy->prom->iskustvo + $this->rank * 7]);
		$enemy->prom->increment('atack_wins');
		return false;
	}
	public function kiled($enemy)
	{
		if($enemy->prom->health <= 0){
			$drzavna = $enemy->bank->removeMoney('drzavna');
			$svetska = $enemy->bank->removeMoney('svetska');
			$enemy->prom->update(['pari'=> $enemy->prom->pari - ($enemy->prom->pari * 50)/100 ]);
			$enemy->prom->update(['health'=> 100]);
			$enemy->prom->decrement('lives');
			$this->update(['pari'=> $this->pari + $drzavna + $svetska]);
			$this->increment('ubistva');
			if($enemy->prom->lives <= 0){
				$enemy->prom->update(['health'=> 0]);
				$enemy->killAcc($this->username);
			}
			return true;
		}
		return false;
	}
	public function nameRank($rank = 1)
	{
	  if($rank == 1){
			$rank = $this->rank;
		}
		switch($rank){
		case 1: return "Почетник";
		case 2: return "Граѓанин";
		case 3: return "Помошник";
		case 4: return "Шпион";
		case 5: return "Советник";
		case 6: return "Криминалец";
		case 7: return "Гангстер";
		case 8: return "Лидер";
		case 9: return "Шеф";
		case 10: return "Мафиозо";
		}
	}
	public function updateRank()
	{
		$i = $this->iskustvo;
		switch (true) {
			case $i >= 0 && $i <= 2200:
				$this->update(['rank'=>1]);
				break;
			case $i > 2200 && $i <= 5000:
				$this->update(['rank'=>2]);
				break;
			case $i > 5000 && $i <= 9000:
				$this->update(['rank'=>3]);
				break;
			case $i > 9000 && $i <= 14500:
				$this->update(['rank'=>4]);
				break;
			case $i > 14500 && $i <= 21500:
				$this->update(['rank'=>5]);
				break;
			case $i > 21500 && $i <= 30000:
				$this->update(['rank'=>6]);
				break;
			case $i > 30000 && $i <= 40000:
				$this->update(['rank'=>7]);
				break;
			case $i > 40000 && $i <= 54500:
				$this->update(['rank'=>8]);
				break;
			case $i > 54500 && $i <= 72000:
				$this->update(['rank'=>9]);
				break;
			case $i > 72000:
				$this->update(['rank'=>10]);
				break;
		}
	}
	public function procent()
	{
		switch ($this->rank) {
			case 1:
				return round(($this->iskustvo / 2200)*100);
			case 2:
				return round((($this->iskustvo - 2200) / 2800)*100);
			case 3:
				return round((($this->iskustvo - 5000) / 4000)*100);
			case 4:
				return round((($this->iskustvo - 9000) / 5500)*100);
			case 5:
				return round((($this->iskustvo - 14500) / 7000)*100);
			case 6:
				return round((($this->iskustvo - 21500) / 8500)*100);
			case 7:
				return round((($this->iskustvo - 30000) / 10000)*100);
			case 8:
				return round((($this->iskustv - 40000) / 14500)*100);
			case 9:
				return round((($this->iskustvo - 54500) / 17500)*100);
			case 10:
				return 99;
		}
	}
}
