<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;

class User Extends Model
{
	protected $table = "users";

	protected $fillable = [
		'username',
		'name',
		'email',
		'password',
		'active',
    'active_hash',
    'recover_hash',
    'remember_identifier',
    'remember_token'
	];
	public function permissions(){ return $this->hasOne('App\Models\User\UserPermission');}

	public function updateRememberCredentials($identifier, $token)
	{
		$this->update([
			'remember_identifier' => $identifier,
			'remember_token'      => $token
		]);
	}
	public function removeRememberCredentials(){$this->updateRememberCredentials(null, null);}

}
