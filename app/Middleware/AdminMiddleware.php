<?php
namespace App\Middleware;

class AdminMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if(!$this->auth->check() || !$this->auth->user()->permissions->hasPermission('is_admin')){
			$this->flash->addmessage('error', 'Не сте администратор!');
			return $response->withRedirect($this->router->pathFor('home'));
    }
		$response = $next($request, $response);
		return $response;
	}
}
