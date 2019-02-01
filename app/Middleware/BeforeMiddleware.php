<?php

namespace App\Middleware;
use App\Models\User;

class BeforeMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$this->checkRememberMe();
		$response = $next($request, $response);
		return $response;
	}
	protected function checkRememberMe()
	{
    $user = $this->auth->user();
		if(isset($_COOKIE[$this->config['auth.remember']]) && !$user)
		{
			$data = $_COOKIE[$this->config['auth.remember']];
			$credentials = explode('___', $data);

			if(empty(trim($data)) || count($credentials) !== 2) {
			  setcookie($this->config['auth.remember'], null, 1, "/", null);
			  return $response->withRedirect($this->router->pathFor('auth.signin'));
		  }else{
				$identifier = $credentials[0];
				$token = $this->hash->hash($credentials[1]);
        $user = User::where('remember_identifier', $identifier)->first();
				if($user){
					if($this->hash->hashCheck($token, $user->remember_token)){
						$_SESSION[$this->config['auth.session']] = $user->id;
					}else{
						$user->removeRememberCredentials();
						setcookie($this->config['auth.remember'], null, 1, "/", null);
					}
			  }
			}
    }
  }
}
