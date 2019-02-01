<?php
namespace App\Middleware;

class TaskMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if(!$this->auth->check() || !$this->auth->user()->task->finished()){
      $this->flashres->addMessage('error', 'Почекајте! Веќе вршите некое дејство!');
			echo "<input type='hidden' value='1'>";
	  	return $this->view->render($response, '/templates/partials/flash.twig');
    }
		$response = $next($request, $response);
		return $response;
	}
}
