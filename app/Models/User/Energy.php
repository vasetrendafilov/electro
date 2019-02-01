<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Energy Extends Model
{
	protected $table = "users_energy";

	protected $fillable = [
		'energija',
    'status',
		'atack',
		'pocit',
		'trkalo'
	];
	public static $defaults=[
		'energija' => 100,
		'status' => 0,
		'atack' => 5,
		'pocit' => 2,
		'trkalo' =>1
	];
	public function calculateEnergy(){
		if($this->energija <=100){
			 //razlika vo sekundi za vreminja
			 $difference_in_seconds = strtotime(Carbon::now()) - strtotime($this->updated_at);
			 $addEnergija = $difference_in_seconds / 10;//na kolku sekundi se polni eden poen
			 if($this->energija + $addEnergija < 100){
					$this->update(['energija' => $this->energija + $addEnergija ]);
			 }else {
					$this->update(['energija' => 100 ]);
			 }
		}
	}
	public function check($val){
		return ($this->energija >= $val ? true:false);
	}

}
