<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPermission Extends Model
{
	protected $table = "users_permissions";

	protected $fillable = [
	'is_admin'
	];
  public static $defaults=[
    'is_admin' => false
  ];
	public function hasPermission($permission)
	{
		return (bool)$this->{$permission};
	}
}
