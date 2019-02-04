<?php
namespace App\Auth;
use App\Models\User\User;
use App\Models\Clan;
use Carbon\Carbon;

class Auth
{
	protected $container;

	public function __construct($container){
		$this->container = $container;
	}
	public function __get($property){
		if($this->container->{$property}){
			return $this->container->{$property};
		}
	}
	public function user(){
		if(isset($_SESSION[$this->config['auth.session'] ])){
		  return User::find($_SESSION[$this->config['auth.session']]);
	  }
	}
	public function check(){
		return isset($_SESSION[$this->config['auth.session']]);
	}
	public function attempt($username, $password, $remember)
	{
		$user = User::where('username', $username)->where('active', true)->first();
		if(!$user){
			if(User::where('username', $username)->where('active', false)->first()){
				return 2;
			}
			return 1;
		}
		if(password_verify($password, $user->password)){
			if($remember === 'on'){
				$rememberIdentifier = $this->randomlib->generateString(128);
				$rememberToken = $this->randomlib->generateString(128);
				$user->updateRememberCredentials($rememberIdentifier, $this->hash->hash($rememberToken));
				setcookie($this->config['auth.remember'], "{$rememberIdentifier}___{$rememberToken}", Carbon::parse('+1 week ')->timestamp,'/');
      }
			$_SESSION[$this->config['auth.session']] = $user->id;
			return 3;
		}
		return 1;
	}
	public function logout()
	{
		if(isset($_COOKIE[$this->config['auth.remember']])){
      $this->user()->removeRememberCredentials();
			setcookie($this->config['auth.remember'], null, 1, "/", null);
			unset($_SESSION[$this->config['auth.session']]);
		}else{
			unset($_SESSION[$this->config['auth.session']]);
		}
	}

}
