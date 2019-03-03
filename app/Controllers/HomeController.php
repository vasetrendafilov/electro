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

    //$this->kolo->addKolo("0_1_10_1_1_2_20_2_1_3_30_2_1_4_10_2_2_5_40_3_3_6_50_4_4_7_10_5_5_8_15_6_4_9_20_6_0_10_10_6");
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
