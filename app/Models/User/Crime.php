<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Crime Extends Model
{
	protected $table = "users_crimes";

	protected $fillable = [
		'type',
		'sofer',
		'hitmen',
		'invest'
	];
  public function accepted($person){
      return (bool)json_decode($this->{$person},true)['accept'];
  }
  public function prom($person,$type){
      return json_decode($this->{$person},true)[$type];
  }
}
