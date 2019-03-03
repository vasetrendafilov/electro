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
  public function getCalc($request, $response)
   {
     $this->kolo->addKolo(trim($request->getParam('kolo')));

     //$this->kolo->addRes("0_10_1_1_20_2_1_30_2_1_10_2_2_40_3_3_50_4_4_10_5_5_15_6_4_20_6_0_10_6");
      if($this->kolo->calc()){
      foreach ($this->kolo->steps as $key => $step) {
       echo $step.'_';
      }
      }
      else foreach ($this->kolo->error as $err) {
          echo "0<br><br>".$err;
      }
   }
}
