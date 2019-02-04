<?php

namespace App\Controllers;

class AjaxController extends Controller
{
 public function getValidation($request, $response)
  {
    $type = $request->getParam('type');
    $val = $request->getParam('val');
    $password = $request->getParam('password');
    switch ($type) {
      case 'username':
      $v = $this->Validator->validate(['username' => [$val,'required|alnumDash|max(50)|min(4)|uniqueUsername'] ]);
      break;
      case 'email':
      $v = $this->Validator->validate(['email' => [$val,'required|max(100)|email|uniqueEmail'] ]);
      break;
      case 'password':
      $v = $this->Validator->validate(['password' => [$val,'required|min(8)|alnumDash'] ]);
      break;
      case 'password_confirm':
      $v = $this->Validator->validate([
        'password' => [$password,'required'],
        'password_confirm' => [$val,'required|matches(password)'] ]);
      break;
    }
    return ($v->passes() ? 'true':$v->errors()->first());
  }
}
