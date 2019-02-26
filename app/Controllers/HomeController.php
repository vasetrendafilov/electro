<?php

namespace App\Controllers;

class HomeController extends Controller
{
  public function getHome($request, $response)
  {
    return $this->view->render($response, 'home.twig');
  }
  public function postHome($request, $response)
  {
    $this->kolo->addKolo(trim($request->getParam('kolo')));

    //$this->kolo->addRes("0_10_1_1_20_2_1_30_2_1_10_2_2_40_3_3_50_4_4_10_5_5_15_6_4_20_6_0_10_6");
     $i=0;
     while ($this->kolo->otpornik->child != null) {
       $this->kolo->otpornik->addType($this->kolo->jazli);
       echo "<br><br>";
      var_dump($this->kolo->otpornik);
       $this->kolo->otpornik->calc($this->kolo->jazli);
       if($i<20){
          echo "<br><br>";
         var_dump($this->kolo->otpornik);
         echo "<br>Jazli:";
         var_dump($this->kolo->jazli);
         echo "<br><br>";
     }else exit();
       $i++;
     }

    echo "<br>Finalu the result is ".$this->kolo->otpornik->val;


  }


}
