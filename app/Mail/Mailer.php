<?php

namespace App\Mail;

class Mailer
{
  protected $view;
  protected $mailer;
  public function __construct($view, $mailer)
  {
    $this->view = $view;
    $this->mailer = $mailer;
  }
  public function send($template, $data, $callback)
  {
    $message = new Message($this->mailer);
    $message->body($this->view->fetch($template,$data));//zima data bez response
    call_user_func($callback, $message);
    $this->mailer->send();
  }
}
