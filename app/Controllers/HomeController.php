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
    //$this->kolo->addKolo("0_10_1_1_20_2_1_30_2_1_10_2_2_40_3_3_50_4_4_10_5_5_15_6_4_20_6");
    //$this->kolo->addKolo("0_10_1_1_10_2_0_10_2");
    var_dump($this->kolo->otpornik);
     $i=0;
     while (count($this->kolo->otpornik) != 1) {
       $this->kolo->calcType();
       $this->kolo->calc();
       if($i<20){
          echo "<br><br>";
         var_dump($this->kolo->otpornik);
         echo "<br>Jazli:";
         var_dump($this->kolo->jazli);
         echo "<br><br>";
     }else exit();
       $i++;
     }

    echo "<br>Finalu the result is ".$this->kolo->otpornik[0]->value;
    exit();


  }


}
