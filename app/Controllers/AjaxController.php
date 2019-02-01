<?php

namespace App\Controllers;
use App\Models\Missions;
use App\Models\Rabota;
use App\Models\Drzava;
use App\Models\Stocks;
use App\Models\Clan;
use App\Models\Shop;
use App\Models\Car;
use App\Models\DrinksDrugs;
use App\Models\User\User;
use App\Models\User\Crime;
use App\Models\User\Prom;

class AjaxController extends Controller
{
  public function getAddMsg($request, $response)
  {
    $username = trim($request->getParam('username'));
    if(isset($username) && !empty($username)){
      $user = User::where('username',$username)->first();
      $user->contact->addMsg($this->auth->user()->id);
      return true;
    }
    return false;
  }
  public function getChatRoom($request, $response)
  {
    $username = trim($request->getParam('username'));
    if(isset($username) && !empty($username)){
      $user = $this->auth->user();
      $id1 = $user->id;
      $id2 = User::where('username',$username)->first()->id;
      $user->contact->removeMsg($id2);
      if($id1 < $id2){
        return $id2.'_'.$id1;
      }else if($id1 > $id2){
        return $id1.'_'.$id2;
      }
    }
    return "";
  }
  public function getSoon($request, $response)
  {
    return" <div class='card'>
      <svg class='w-100 d-block rounded' viewBox='0 0 840 740' version='1.1' xmlns='http://www.w3.org/2000/svg'>
        <g transform='matrix(1,0,0,1,700,240)' >
        <path fill='rgb(86,101,115)' fill-opacity='1' d=' M-350.0669860839844,12.932999610900879 C-350.0669860839844,12.932999610900879 -325.72698974609375,-25.673999786376953 -325.72698974609375,-25.673999786376953 C-324.34100341796875,-27.87299919128418 -321.25799560546875,-28.18600082397461 -319.4580078125,-26.31100082397461 C-319.4580078125,-26.31100082397461 -299,-5 -299,-5 C-299,-5 -258.7080078125,-67.26899719238281 -258.7080078125,-67.26899719238281 C-257.0480041503906,-69.83499908447266 -253.24000549316406,-69.66600036621094 -251.81300354003906,-66.96399688720703 C-251.81300354003906,-66.96399688720703 -209.5489959716797,13.065999984741211 -209.5489959716797,13.065999984741211 C-208.8459930419922,14.39799976348877 -209.81199645996094,
        16 -211.3179931640625,16 C-211.3179931640625,16 -348.375,16 -348.375,16 C-349.9490051269531,16 -350.9070129394531,14.265000343322754 -350.0669860839844,12.932999610900879z'></path><path fill='rgb(86,101,115)' fill-opacity='1' d=' M-330,-75 C-330,-83.28399658203125 -323.28399658203125,-90 -315,-90 C-306.71600341796875,-90 -300,-83.28399658203125 -300,-75 C-300,-66.71600341796875 -306.71600341796875,-60 -315,-60 C-323.28399658203125,-60 -330,-66.71600341796875 -330,-75z'></path></g>
      		<rect id='Rectangle-6-Copy' fill='#ECF0F1' x='0' y='420' width='100%' height='100%'></rect>
      		<rect id='Rectangle-5' fill='#D7DCDD' x='80' y='476' width='266' height='50' rx='2'></rect>
      		<rect id='Rectangle-5-Copy' fill='#D7DCDD' x='500' y='476' width='266' height='50' rx='2'></rect>
      		<rect id='Rectangle-3' fill='#D7DCDD' x='80' y='560' width='685' height='50' rx='2'></rect>
      		<rect id='Rectangle-3-Copy' fill='rgb(86,101,115)' x='300' y='555' width='60' height='60' rx='2'></rect>
          <rect id='Rectangle-btn' fill='rgb(86,101,115)' x='0' y='660' width='100%' height='80' rx='2'></rect>
          <rect id='Rectangle-btn-text' fill='#D7DCDD' x='295' y='680' width='270' height='40' rx='2'></rect>
          <text transform='rotate(-35)' x='-200' y='580' fill='var(--dark)' style='font-size: 80px;font-weight:600;''>НАСКОРО!</text>
      </svg>
    </div>";
  }
  public function getSearch($request, $response)
  {
    $key = $request->getParam('key');
    if (isset($key) && !empty($key)){
      $user = $this->auth->user();
      $friends = $user->contact->getFriends();
      if($user->contact->friendsLike($key)){
        echo"  <label for='friends'>ПРИЈАТЕЛИ</label>
        <ul name='friends' class='friends'> ";
        foreach ($friends as $val){
          $count = 0;
    			for ($i=0; $i < strlen($key) ; $i++) {
    				if($val->username[$i] == $key[$i]){$count++;}
    			}
    			if($count == strlen($key)){
            echo "<li><img src='".$user->get_gravatar($val->email,40)."'width='40' height='40'><span>".$val->username."</span> ".$user->contact->checkMsg($val->id)."<span style='".$user->displayStatus($val->energy->status)."'></span> </li>";
    			}
        }
        echo"</ul>";
      }
      $users = User::where('username','LIKE', $key.'%')->where('id','<>',$user->id)->take(5)->get();
      if($users->count() > 0 && $user->contact->areFriends($users)){
        echo"  <label for='people'>МАФИЈАШИ</label>
        <ul name='people' class='people'> ";
        foreach ($users as $val){
          if(!$user->contact->isFriend($val->id) && !$user->contact->isBlocked($val->id))
          echo "<li><img src='".$user->get_gravatar($val->email,40)."'width='40' height='40'><span>".$val->username."</span><span style='".$user->displayStatus($val->energy->status)."'></span> </li>";
        }
        echo"</ul>";
      }
      $clans = Clan::where('name','LIKE', $key.'%')->where('user_id','<>',$user->id)->take(5)->get();
        if($clans->count() > 0){
        echo"  <label for='clans'>ФАМИЛИИ</label>
        <ul name='clans' class='clans'> ";
        foreach ($clans as $id => $clan) {
        echo "<li><img src='".$user->get_gravatar($clan->email,40)."'width='40' height='40'><span>".$clan->name."</span><span style='background:var(--gray-dark)'></span> </li>";
        }
        echo"</ul>";
      }
    }
  }
  public function getTrkalo($request, $response)
  {
    return $this->view->render($response, '/templates/cards/kazino.twig');
  }
  public function getChatTiles($request, $response)
  {
    return $this->view->render($response, '/templates/partials/chatTiles.twig');
  }
  public function getStatistika($request, $response)
  {
    return $this->view->render($response, '/templates/cards/statistika.twig',[
      'rows' =>Prom::all(),
      'clans' => Clan::all()
    ]);
  }
  public function getProfile($request, $response)
  {
    $user = $this->auth->user();
    $weapons = array(array(),array());
    if($weaponsIds = json_decode($user->inventory->weapons,true)){
      foreach ($weaponsIds as $id=>$val) {
        if($val > 0){
          array_push($weapons[0],Shop::findOrFail($id));
          array_push($weapons[1],$val);
        }
      }
    }
    return $this->view->render($response, '/templates/cards/profile.twig',[
      'person'  => $user,
      'weapons' => $weapons,
      'x' => false
    ]);
  }
  public function getProfilePeople($request, $response)
  {
    $username = $request->getParam('type');
    if(isset($username) && !empty($username)){
    $user = User::where('username',$username)->first();
    $row2 = $weapons = array(array(),array());
    if($carsIds = json_decode($user->inventory->cars,true)){
      foreach ($carsIds as $id=>$val) {
        $car = Car::findOrFail($id);
        $dmg = explode('_', $val);
        for ($i=1; $i <= $dmg[0] ; $i++) {
            array_push($row2[0],$car);
            array_push($row2[1],$dmg[$i]);
        }
      }
    }
    if($weaponsIds = json_decode($user->inventory->weapons,true)){
      foreach ($weaponsIds as $id=>$val) {
        if($val > 0){
          array_push($weapons[0],Shop::findOrFail($id));
          array_push($weapons[1],$val);
        }
      }
    }
    $misii = Missions::all();
    return $this->view->render($response, '/templates/cards/profile.twig',[
      'person'  => $user,
      'row2' => $row2,
      'weapons' => $weapons,
      'misii' => $misii,
      'x' => true
    ]);
    }
  }
  public function getBlacklist($request, $response)
  {
    $ids = explode('_',$this->auth->user()->contact->blacklist);
    $users = array();
    unset($ids[0]);
     foreach ($ids as $id) {
        array_push($users, User::findOrFail($id));
     }
   return $this->view->render($response, '/templates/cards/blacklist.twig',[
     'users'  => $users
   ]);
  }
  public function getClan($request, $response)
  {
    $user = $this->auth->user();
    if($user->clan){
      $options = array('1' => 2);
      $clan = $user->clan;
    }else if(!empty($user->contact->clan)){
      $options = array('1' => 1);
      $clan = Clan::where('name', $user->contact->clan)->first();
    }else{
      $options = array('0' => true);
      return $this->view->render($response, '/templates/cards/clan.twig',['x' => $options ]);
    }
    return $this->view->render($response, '/templates/cards/clan.twig',[
      'clan' => $clan,
      'members' => $clan->members(),
      'x' => $options
    ]);
  }
  public function getProfileClan($request, $response)
  {
    $name = $request->getParam('type');
    $clan = Clan::where('name',$name)->first();
    $options = array('1' => false);
    return $this->view->render($response, '/templates/cards/clan.twig',[
      'clan' => $clan,
      'members' => $clan->members(),
      'x' => $options
    ]);
  }
  public function getStats($request, $response)
  {
    $user = $this->auth->user();
    $vals = array('0' => $user->prom->mok,'1' => $user->prom->pari,'2' => $user->state($user->prom->place),'3' => $user->energy->energija,'4' => $user->prom->health );
    echo implode('_', $vals);
  }
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
  public function getRabota($request, $response)
  {
    $type = $request->getParam('type');
    $raboti = Rabota::where('type',$type)->get();
    if ($type == 'rabota') {
      $options = array(1=>'НИСКИ', 2=>'ПРОСЕЧНИ', 3=>'ВИСОКИ',5=>'РАБОТИ');
    }else{
      $options = array(1=>'ПОЧЕТНИК', 2=>'ЗАВИСНИК', 3=>'ГУРУ',5=>'ИЗВРШИ');
    }
    $row1 = $row2 = $row3 = array();
    foreach ($raboti as $rabota) {
      switch (true) {
        case $rabota->rank >= 1 && $rabota->rank <=3:
          array_push($row1,$rabota);
          break;
        case $rabota->rank >= 4 && $rabota->rank <=7:
          array_push($row2,$rabota);
          break;
        case $rabota->rank >= 8 && $rabota->rank <=10:
          array_push($row3,$rabota);
          break;
      }
    }
    return $this->view->render($response, '/templates/cards/rabota.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'x' => $options
    ]);
  }
  public function getDrinks($request, $response)
  {
    $type = $request->getParam('type');
    $drinks_drugs = DrinksDrugs::where('type',$type)->get();
    $row1 = $row2 = $row3 = $name = array();
    foreach ($drinks_drugs as $drink_drug) {
      array_push($row1,$drink_drug);
      array_push($name,$drink_drug->title);
    }
    if($ids = json_decode($this->auth->user()->inventory->{$type},true)){
      foreach ($ids as $id => $val) {
        if($val > 0){
          array_push($row2, DrinksDrugs::findOrFail($id));
        }
      }
    }
    if ($type == 'drugs') {
      $row3 = Stocks::where('id','<',9)->get();
    }else{
      $row3 = Stocks::where('id','>=',9)->get();
    }
    return $this->view->render($response, '/templates/cards/drinks_drugs.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'name' => $name
    ]);
  }
  public function getCars($request, $response)
  {
    $cars = Car::all();
    $row1 = $row2 = $row3 = array(array(),array(),array());
    $options = array(1=>'НИСКА', 2=>'СРЕДНА', 3=>'ВИСОКА',4=>'car',5=>"УКРАДИ");
    foreach ($cars as $car) {
      switch ($car->type) {
        case "middle":
          array_push($row1[0],$car);
          break;
        case "fast":
          array_push($row2[0],$car);
          break;
        case "top":
          array_push($row3[0],$car);
          break;
      }
    }
    return $this->view->render($response, '/templates/cards/cars.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'x' => $options
    ]);
  }
  public function getCrime($request, $response)
  {
    $row1 = $row2  = $hire = $options = array();
    $cars = array(array(),array(),array());
    $user = $this->auth->user();
    $crimes = Crime::where('user_id',$user->id)->get();
    foreach ($crimes as $key => $value) {
      array_push($row1,$value);
    }
    if($contracts = json_decode($user->contact->crime_pending,true)){
      foreach ($contracts as $id => $val){
        array_push($row2,User::findOrFail($id));
        array_push($hire,$val);
        $carsIds = json_decode($user->inventory->cars,true);
        foreach ($carsIds as $id=>$val) {
            $car = Car::findOrFail($id);
            $dmg = explode('_', $val);
            for ($i=1; $i <= $dmg[0] ; $i++) {
              if($dmg[$i]>50){
                array_push($cars[0],$car);
                array_push($cars[1],"$id"."_"."$i");
                array_push($cars[2],$dmg[$i]);
            }
          }
        }
      }
    }
    return $this->view->render($response, '/templates/cards/crime.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'hire'  => $hire,
      'cars'  => $cars
    ]);
  }
  public function getTrki($request, $response)
  {
    $row1 = $row2 = $row3  = array(array(),array(),array());
    $options = array(1=>'НИСКА', 2=>'СРЕДНА', 3=>'ВИСОКА',4=>'race',5=>"ТРКАЈ СЕ",6=>true);
    $user = $this->auth->user();
    $carsIds = json_decode($user->inventory->cars,true);
    foreach ($carsIds as $id=>$val) {
        $car = Car::findOrFail($id);
        $dmg = explode('_', $val);
        for ($i=1; $i <= $dmg[0] ; $i++) {
          if($dmg[$i]>50){
            switch ($car->type) {
              case "middle":
                array_push($row1[0],$car);
                array_push($row1[1],"$id"."_"."$i");
                array_push($row1[2],$dmg[$i]);
                break;
              case "fast":
                array_push($row2[0],$car);
                array_push($row2[1],"$id"."_"."$i");
                array_push($row2[2],$dmg[$i]);
                break;
              case "top":
                array_push($row3[0],$car);
                array_push($row3[1],"$id"."_"."$i");
                array_push($row3[2],$dmg[$i]);
                break;
            }
        }
      }
    }
    return $this->view->render($response, '/templates/cards/cars.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'x' => $options
    ]);
  }
  public function getTravel($request, $response)
  {
    $states = Drzava::where('name',$this->auth->user()->prom->place)->first();
    return $this->view->render($response, '/templates/cards/travel.twig',[
      'val'  => $states->price()
    ]);

  }
  public function getGaraza($request, $response)
  {
    $row1 = $row2 = $row3 = array(array(),array(),array());
    $options = array(1=>'НИСКА', 2=>'СРЕДНА', 3=>'ВИСОКА',4=>'sellcar',5=>"ПРОДАЈ",6=>true,7=>true);
    $user = $this->auth->user();
    $carsIds = json_decode($user->inventory->cars,true);
    foreach ($carsIds as $id=>$val) {
      $car = Car::findOrFail($id);
      $dmg = explode('_', $val);
      for ($i=1; $i <= $dmg[0] ; $i++) {
        if($dmg[$i]>50){
          switch ($car->type) {
            case "middle":
              array_push($row1[0],$car);
              array_push($row1[1],"$id"."_"."$i");
              array_push($row1[2],$dmg[$i]);
              break;
            case "fast":
              array_push($row2[0],$car);
              array_push($row2[1],"$id"."_"."$i");
              array_push($row2[2],$dmg[$i]);
              break;
            case "top":
              array_push($row3[0],$car);
              array_push($row3[1],"$id"."_"."$i");
              array_push($row3[2],$dmg[$i]);
              break;
          }
        }
      }
    }
    return $this->view->render($response, '/templates/cards/cars.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'x' => $options
    ]);
  }
  public function getHospital($request, $response)
  {
    $prom = $this->auth->user()->prom;
    return "<style>.container-fluid .card-columns{column-count: 1!important;}</style>
    <div class='container p-0'></div>
    <div class='card-columns'>
    <div class='container p-0'></div>
        <div class='card'>
        <img class='card-img-top' src='".$this->config['app']['baseUrl']."/resources/views/templates/svg/bolnica.svg' width='100%'>
        <div class='card-body'>
          <div class='row' style='margin-left:-15px!important'>
            <input type='hidden' value='10'>
            <div class='col'>
                <input type='range' min='$prom->health' max='100' value='$prom->health' class='slider'>
               <input type='number' class='result' value='$prom->health'>
            </div>
          </div>
          <button class='btn btn-danger hospital'>HEAL <span></span></button>
        </div>
        </div>
        </div>";
  }
  public function getBank($request, $response)
  {
    $user = $this->auth->user();
    $name = array('drzavna','svetska','small','big');
    $title = array('ДРЖАВНА БАНКА','СВЕТСКА БАНКА','МАЛ СЕФ','ГОЛЕМ СЕФ');
    return $this->view->render($response, '/templates/cards/bank.twig',[
      'name'  => $name,
      'title' => $title
    ]);
  }
  public function getShop($request, $response)
  {
    $weapons = Shop::where('type','weapons')->get();
    $options = array(1=>'so da napravi');
    $row1 = $row2 = $row3 = array();
    foreach ($weapons as $weapon) {
          array_push($row1,$weapon);
    }
    $unlocks = Shop::where('type','unlock')->get();
    foreach ($unlocks as $unlock) {
      array_push($row2,$unlock);
    }
    $fills = Shop::where('type','fill')->get();
    foreach ($fills as $fill) {
      array_push($row3,$fill);
    }
    return $this->view->render($response, '/templates/cards/shop.twig',[
      'row1'  => $row1,
      'row2'  => $row2,
      'row3'  => $row3,
      'x' => $options
    ]);
  }


}
