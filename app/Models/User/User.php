<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clan;

class User Extends Model
{
	protected $table = "users";

	protected $fillable = [
		'username',
		'name',
		'email',
		'password',
		'drzava',
		'pol',
		'active',
    'active_hash',
    'recover_hash',
    'remember_identifier',
    'remember_token'
	];
	public function getClans()
	{
	  return Clan::where('user_id','<>',$this->id)->take(5)->get();
	}
	public function getName($id)
	{
		return User::find($id)->username;
	}
	public function getEmail($id)
	{
		return User::find($id)->email;
	}
	public function topPlyers($type,$num)
	{
		return Prom::orderBy($type,'DESC')->take($num)->get();
	}
	public function killAcc($username)
	{
		$this->email = '';
		$this->password = $username;
		$this->active = 0;
		$this->save();
	}
	public function updateRememberCredentials($identifier, $token)
	{
		$this->update([
			'remember_identifier' => $identifier,
			'remember_token'      => $token
		]);
	}
	public function removeRememberCredentials(){
		$this->updateRememberCredentials(null, null);
	}
	function get_gravatar( $email, $s = 92, $d = "http://www.theunderworld.mk/resources/img/profile.jpg", $r = 'g', $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=".urlencode($d)."&r=$r";
    return $url;
  }
	public function displayStatus($val)
	{
		switch ($val) {
			case 0:
				return "background:var(--yellow)";
			case 1:
		  	return "background:var(--green)";
			default:
				return "background:var(--gray-dark)";
		}
	}
	public function energy()
	{
		return $this->hasOne('App\Models\User\Energy');
	}
	public function task()
	{
		return $this->hasOne('App\Models\User\Task');
	}
	public function inventory()
	{
		return $this->hasOne('App\Models\User\Inventory');
	}
	public function contact()
	{
		return $this->hasOne('App\Models\User\Contact');
	}
	public function clan()
	{
		return $this->hasOne('App\Models\Clan');
	}
	public function crime()
	{
		return $this->hasOne('App\Models\User\Crime');
	}
	public function bank()
	{
		return $this->hasOne('App\Models\User\Bank');
	}
	public function prom()
	{
		return $this->hasOne('App\Models\User\Prom');
	}
	public function permissions()
	{
		return $this->hasOne('App\Models\User\UserPermission');
	}
	public function state($val)
	{
		switch ($val) {
			case 'SV':
				return 'Салвадорија';
			case 'YK':
				return 'Јакузистан';
			case 'AH':
				return 'Алкохолика';
			case 'VL':
				return 'Витолија';
			case 'BL':
				return 'Блудинтон';
			case 'CT':
				return 'Кастилион';
			case 'CP':
				return 'Капонија';
		}
	}
}
