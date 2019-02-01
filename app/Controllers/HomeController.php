<?php

namespace App\Controllers;
use App\Models\User\User;
use App\Models\User\Crime;
use App\Models\Missions;
use App\Models\Rabota;
use App\Models\Drzava;
use App\Models\Clan;
use App\Models\Shop;
use App\Models\Car;
use App\Models\DrinksDrugs;

class HomeController extends Controller
{
  public function getPeople($request, $response)
  {
    $user = $this->auth->user();
    if($user){
      Missions::checkMissions($user);
      $user->prom->updateRank();
    }
    return $this->view->render($response, 'home.twig',[
      'misii' => Missions::all()
    ]);
  }
  public function getPocit($request, $response)
  {
    $id = $request->getParam('id');
    if(isset($id) && $id !== ''){
      $user = User::findOrFail($id);
      $me = $this->auth->user()->energy;
      if($this->auth->user()->id != $user->id && $me->pocit > 0){
        $me->decrement('pocit');
        $user->prom->increment('pocit');
        $user->prom->pari += 100;
        $user->prom->mok += 50;
        $user->prom->iskustvo += ($user->prom->iskustvo*5)/100;
        $user->prom->save();
        $this->flash->addMessageNow('success','Успешно доделивте почит!');
         return $this->view->render($response, '/templates/partials/flash.twig');
       }
       $this->flash->addMessageNow('info','Ја преминавте денешната граница за почит, пробајте пак утре.');
       return $this->view->render($response, '/templates/partials/flash.twig');
     }
     $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
     return $this->view->render($response, '/templates/partials/flash.twig');
   }
  public function getAtack($request, $response)
  {
    $id = $request->getParam('id');
    if(isset($id) && $id !== ''){
      $user = $this->auth->user();
      $enemy = User::findOrFail($id);
      if($user->id != $enemy->id && $user->energy->atack > 0){
        $user->energy->decrement('atack');
        if($user->prom->points >= 5){
          if($user->prom->place == $enemy->prom->place){
            if($enemy->inventory->zaliha('asets',31) > 0){
              $enemy->inventory->zastita();
              $this->flash->addMessageNow('info','Противникот е заштитен од мафија, не можете да го нападнете');
              return $this->view->render($response, '/templates/partials/flash.twig');
            }
            if($enemy->inventory->have(28) && $enemy->prom->points >= 2 && $this->randomlib->generateInt(0, 3) <= 2){
              $enemy->prom->update(['points' => $enemy->prom->points - 2]);
              $user->prom->update(['points' => $user->prom->points - 4]);
              $this->flash->addMessageNow('error','Противникот има камуфлажа, не можете да го пронајдете.');
              return $this->view->render($response, '/templates/partials/flash.twig');
            }
            if($user->prom->atack($enemy)){
              if($user->prom->kiled($enemy)){
                $this->flash->addMessageNow('success','Го убивте и победивте!');
                return $this->view->render($response, '/templates/partials/flash.twig');
              }
              $this->flash->addMessageNow('success','Победивте, vi preostanvaat uste '.$user->energy->atack.' napadi');
              echo "<input type='hidden' value='".$enemy->prom->health."'>";
              return $this->view->render($response, '/templates/partials/flash.twig');
            }
            $this->flash->addMessageNow('error','Изгубивте.');
            return $this->view->render($response, '/templates/partials/flash.twig');
          }else if($user->inventory->have(27) && $user->prom->points >= 2){
            $user->prom->update(['points' => $user->prom->points - 2]);
            $this->flash->addMessageNow('info','Го пронајдовте, се наоѓа во '.$enemy->prom->place);
            return $this->view->render($response, '/templates/partials/flash.twig');
          }
          $this->flash->addMessageNow('error','Немате доволно поени за локаторот или воопшто го немате.');
          return $this->view->render($response, '/templates/partials/flash.twig');
        }
        $this->flash->addMessageNow('error','Немате доволно поени за напад.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
      $this->flash->addMessageNow('error','Доволно напаѓање за денес, пробајте пак утре.');
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getCrime($request, $response)
  {
    $id = $request->getParam('id');
    if(isset($id) && $id !== ''){
      $crime = Rabota::findOrFail($id);
      $user = $this->auth->user();
      if($user->task->add($crime->time) && $user->prom->rank >= $crime->rank){
        if($user->energy->check($crime->energija)){
          $num = $crime->crime($user,$this->randomlib->generateInt(0, 100));
          switch ($num){
            case 0:
              $this->flash->addMessageNow('success','Успешно го извршивте злосторството.');
              echo "<input type='hidden' value='$crime->time'>";
              break;
            case 1:
              $this->flash->addMessageNow('error','Неуспешно злосторство');
              echo "<input type='hidden' value='$crime->time'>";
              break;
            case 2:
              $user->task->add($crime->time + $crime->time);
              $this->flash->addMessageNow('error','Полицијата ве виде и фати, сега сте во затвор.');
              echo "<input type='hidden' value='$crime->time'><input type='hidden' value='".$crime->time."'>";
              break;
          }
          return $this->view->render($response, '/templates/partials/flash.twig');
        }else{
          $this->flash->addMessageNow('error','Немате доволно енергија');
          echo "<input type='hidden' value='1'>";
          return $this->view->render($response, '/templates/partials/flash.twig');
        }
      }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка.Ве молиме освежете ја страницата.');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getDrinksDrugs($request, $response)
  {
    $id = $request->getParam('id');
    $operation = $request->getParam('operation');
    $kolicina = $request->getParam('kolicina');
    if(isset($id) && $id !== '' && isset($operation) && $operation !== '' && isset($kolicina) && $kolicina !== ''){
      $user = $this->auth->user();
      $drink = DrinksDrugs::findOrFail($id);
      if($operation == 'add' && $kolicina > $user->inventory->zaliha($drink->type,$drink->id)){
        $kolicina -= $user->inventory->zaliha($drink->type,$drink->id);
        if($drink->add($user,$kolicina)){
          $this->flash->addMessageNow('success','Успешно купивте '.$drink->title);
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
         $this->flash->addMessageNow('error','Грешка! Немате доволно пари или го достигнавте лимитот');
         return $this->view->render($response, '/templates/partials/flash.twig');
       }else if ($operation == 'sell' && $kolicina <= $user->inventory->zaliha($drink->type,$drink->id)){
         if($drink->sell($user,$kolicina)){
           $this->flash->addMessageNow('success','Успешно продадовте '.$drink->title);
           return $this->view->render($response, '/templates/partials/flash.twig');
         }else{
           $this->flash->addMessageNow('error','Грешка! Немате доволно алкохол за продавање.');
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
       }
     }
     $this->flash->addMessageNow('error','Настана некаква грешка! Ве молиме освежете ја страницата.');
     return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getCar($request, $response)
  {
    $id = $request->getParam('id');
    if(isset($id) && $id !== ''){
      $car = Car::findOrFail($id);
      $user = $this->auth->user();
      if($user->task->add($car->time) && $user->prom->rank >= $car->rank){
        if($user->energy->check($car->energija)){
           $num = $car->steal($user,$this->randomlib->generateInt(0, 100),$this->randomlib->generateInt(0, 5));
           switch ($num){
           case 0:
             $this->flash->addMessageNow('success','Успешно украдовте '.$car->title);
             echo "<input type='hidden' value='$car->time'>";
             break;
           case 1:
             $this->flash->addMessageNow('info','Неуспешна кражба. Полицијата е зад вас!');
             echo "<input type='hidden' value='$car->time'>";
             break;
           case 2:
             $user->task->add($car->time + 20);
             $this->flash->addMessageNow('error','Полицијата ве виде и фати, сега сте во затвор.');
             echo "<input type='hidden' value='$car->time'><input type='hidden' value='20'>";
             break;
           case 3:
             $this->flash->addMessageNow('error','Твојот автомобил е уништен!');
             echo "<input type='hidden' value='1'>";
             break;
           }
           return $this->view->render($response, '/templates/partials/flash.twig');
         }else{
           $this->flash->addMessageNow('error','Немате доволно енергија!');
           echo "<input type='hidden' value='1'>";
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
      }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка.Ве молиме освежете ја страницата.');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getRace($request, $response)
  {
    $id = explode('_',$request->getParam('id'));
    if(isset($id) && sizeof($id) == 2){
      $user = $this->auth->user();
      $car = Car::findOrFail($id[0]);
      if($user->task->add(90) && $user->prom->rank >= $car->rank){
        if($user->energy->check($car->energija)){
          $num = $car->race($id[1],$user,$this->randomlib->generateInt(0, 100),$this->randomlib->generateInt(0, 5));
          switch ($num){
           case 0:
             $this->flash->addMessageNow('success','Победивте трка!'.$car->title);
             echo "<input type='hidden' value='90'>";
             break;
           case 1:
             $this->flash->addMessageNow('info','Неуспешна трка! Полицијата е зад вас!');
             echo "<input type='hidden' value='90'>";
             break;
           case 2:
             $user->task->add(310);
             $this->flash->addMessageNow('error','Полицијата ве виде и фати, сега сте во затвор!');
             echo "<input type='hidden' value='90'><input type='hidden' value='120'>";
             break;
           }
          return $this->view->render($response, '/templates/partials/flash.twig');
        }
      }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка.Ве молиме освежете ја страницата.');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getRabota($request, $response)
  {
    $id = $request->getParam('id');
    if(isset($id) && $id !== ''){
      $rabota = Rabota::findOrFail($id);
      $user = $this->auth->user();
      if($user->task->add($rabota->time) && $user->prom->rank >= $rabota->rank){
        if($user->energy->check($rabota->energija)){
          if($rabota->calculate($user,$this->randomlib->generateInt(0, 100))){
            $this->flash->addMessageNow('success','Успешна работа!');
            echo "<input type='hidden' value='$rabota->time'>";
            return $this->view->render($response, '/templates/partials/flash.twig');
          }else{
            $this->flash->addMessageNow('error','Неуспешна работа.');
            echo "<input type='hidden' value='$rabota->time'>";
            return $this->view->render($response, '/templates/partials/flash.twig');
          }
        }else{
        $this->flash->addMessageNow('error','Немате доволно енергија.');
        echo "<input type='hidden' value='1'>";
        return $this->view->render($response, '/templates/partials/flash.twig');
        }
      }
    }
    $this->flash->addMessage('error','Настана некаква грешка.Ве молиме освежете ја страницата.');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getSellCar($request, $response)
  {
    $id = explode('_',$request->getParam('id'));
    if(isset($id) && sizeof($id) == 2){
      $user = $this->auth->user();
      $car = Car::findOrFail($id[0]);
      if($car->sell($user,$id[1])){
        $this->flash->addMessageNow('success','Честитки! Успешно ja продадовте '.$car->title);
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getTravel($request, $response)
  {
    $grad = trim($request->getParam('place'));
    if(isset($grad) && $grad !== ''){
      $user = $this->auth->user();
      if($user->task->add(10)){
      $from = Drzava::where('name',$user->prom->place)->first();
      if($from->travel($user,$grad)){
        $this->flash->addMessageNow('success','Stasavte');
        echo "<input type='hidden' value='10'>";
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
      $this->flash->addMessageNow('error','ГРЕШКА! Немате доволно пари за карта или го испуштивте летот.');
      echo "<input type='hidden' value='1'>";
      return $this->view->render($response, '/templates/partials/flash.twig');
      }
    }
    $this->flash->addMessageNow('error','ГРЕШКА! Обиди се повторно!');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getHospital($request, $response)
  {
    $kolicina = $request->getParam('kolicina');
    if(isset($kolicina) && $kolicina !== ''){
      $user = $this->auth->user();
      if($user->prom->heal($kolicina)){
        $this->flash->addMessageNow('success','Успешно сe излечивте.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }else{
        $this->flash->addMessageNow('info','Немате доволно пари.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
    }
    $this->flash->addMessageNow('error','Puffffffff.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getBank($request, $response)
  {
    $name = trim($request->getParam('name'));
    $money =(int)$request->getParam('kolicina');
    if(isset($name) && $name !== '' && isset($money) && $money !== ''){
       $user = $this->auth->user();
       if($money >  $user->bank->stuff($name,'pari')){
         $money -= $user->bank->stuff($name,'pari');
         $btn = 'add';
       }else if($money <  $user->bank->stuff($name,'pari')){
         $btn = 'retrive';
         $money = $user->bank->stuff($name,'pari') - $money;
       }else{
         $this->flash->addMessageNow('info','Внесете повеќе пари.');
         return $this->view->render($response, '/templates/partials/flash.twig');
       }
       $num = $user->bank->transfer($user,$money,$name,$btn);
       switch ($num){
          case 1:
            $this->flash->addMessageNow('info','Немаш доволно пари или немаш дозвола!');
            break;
          case 2:
            $this->flash->addMessageNow('info','Твојот лимит е достигнат!');
            break;
          case 3:
            $this->flash->addMessageNow('info','Успешно направивте трансфер на пари!');
            break;
          case 4:
            $this->flash->addMessageNow('info','Немате доволно пари во банка!');
            break;
       }
       return $this->view->render($response, '/templates/partials/flash.twig');
     }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getShop($request, $response)
  {
    $id = $request->getParam('id');
    $kolicina = $request->getParam('kolicina');
    $user = $this->auth->user();
    $shop = Shop::findOrFail($id);
    if(((isset($id) && $id !== '') || (isset($kolicina) && $kolicina !== '')) && $user->prom->rank >= $shop->rank){
        switch ($shop->type) {
       case 'weapons':
         if($shop->add_wepons($user,$kolicina)){
           $this->flash->addMessageNow('success','Успешно купи оружје');
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
         $this->flash->addMessageNow('error','Немате доволно пари или достигнавте лимит!');
         return $this->view->render($response, '/templates/partials/flash.twig');
       case 'unlock':
         if($shop->unlock($user)){
           $this->flash->addMessageNow('success','Успешно отклучивте');
           echo "<input type='hidden' value='1'>";
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
         $this->flash->addMessageNow('error','Немате доволно пари или достигнавте лимит!');
         return $this->view->render($response, '/templates/partials/flash.twig');
       case 'fill':
         if($shop->fillUp($user,$kolicina)){
           $this->flash->addMessageNow('success','Успешно купивте '.$shop->title);
           return $this->view->render($response, '/templates/partials/flash.twig');
         }
         $this->flash->addMessageNow('error','Немате доволно пари или достигнавте лимит!');
         return $this->view->render($response, '/templates/partials/flash.twig');
     }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
 }
  public function getAddFriend($request, $response)
  {
   $username = $request->getParam('username');
   $me = $this->auth->user();
   $user = User::where('username',trim($username))->first();
   if(!$me->contact->isFriend($user->id) && !$me->contact->isBlocked($user->id)){
     if($user->contact->add($me->id)){
       $this->flash->addMessageNow('success','Испрати понуда за пријателство на '.$user->username);
       return $this->view->render($response, '/templates/partials/flash.twig');
     }
     $this->flash->addMessageNow('error','Во исчекување е понудата за пријателство');
     return $this->view->render($response, '/templates/partials/flash.twig');
   }else{
     $this->flash->addMessageNow('info','Вие веќе сте пријател со '.$user->username);
     return $this->view->render($response, '/templates/partials/flash.twig');
   }
   $this->flash->addMessageNow('error','Неуспешно праќање на порака. Освежете ја страницата');
   return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getConfirmFriend($request, $response)
  {
    $username = $request->getParam('username');
    $me = $this->auth->user();
    $user = User::where('username',trim($username))->first();
    if(!$me->contact->isFriend($user->id)){
      if($me->contact->confirm($user)){
        $this->flash->addMessageNow('success','Ја прифативте понудата за пријателство од '.$user->username);
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
    }else{
      $this->flash->addMessageNow('info','Веќе сте пријатели');
       return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','ГРЕШКА! Неуспешна понуда за пријателство');
     return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getDeleteFriend($request, $response)
  {
    $username = $request->getParam('username');
    $me = $this->auth->user();
    $user = User::where('username',trim($username))->first();
    if($me->contact->isFriend($user->id)){
      if($me->contact->remove($user)){
        $this->flash->addMessageNow('success','Го избришавте '.$user->username);
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
    }else{
      $this->flash->addMessageNow('info','Не сте пријатели!');
       return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','ГРЕШКА! Безуспешно бришење на пријател!');
     return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getBlockFriend($request, $response)
  {
    $id = $request->getParam('id');
    $me = $this->auth->user();
    $user = User::findOrFail($id);
    if($me->contact->block($user->id,$me->clan) && $me->id != $user->id){
      if(!$me->contact->remove($user) && $me->contact->isFriend($user->id)){
        $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
      $this->flash->addMessageNow('success','Го блокиравте '.$user->username);
      return $this->view->render($response, '/templates/partials/flash.twig');
    }else{
      $this->flash->addMessageNow('error','Го одбивте'.$user->username);
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getUnblockFriend($request, $response)
  {
    $id = $request->getParam('id');
    $me = $this->auth->user();
    $user = User::findOrFail($id);
    if($me->contact->unblock($user->id)){
      $this->flash->addMessageNow('success','Го одблокиравте '.$user->username);
      return $this->view->render($response, '/templates/partials/flash.twig');
    }else{
      $this->flash->addMessageNow('error','Играчот е вече одблокиран');
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getClan($request, $response)
  {
    $name = $request->getParam('name');
    $moto = $request->getParam('moto');
    $user = $this->auth->user();
    if(!Clan::where('user_id',$user->id)->first() && !(bool)Clan::where('name',$name)->count() && $user->prom->rank > 4){
      $user->clan()->create([
        'name' => $name,
        'email' => $user->email,
        'moto' => $moto,
        'members' => 1,
        'members_ids' => '_'.$user->id,
        'pocit' => $user->prom->pocit,
        'mok' => $user->prom->mok,
        'pari' => $user->prom->pari
      ]);
      $user->contact->update(['clan' => $name]);
          $this->flash->addMessageNow('success','Успешно направивте фамилија.');
          return $this->view->render($response, '/templates/partials/flash.twig');
        }
        $this->flash->addMessageNow('error','Името е завземено.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
  public function getMotoClan($request, $response)
  {
    $this->auth->user()->clan->update(['moto' => $request->getParam('moto')]);
  }
  public function getRemoveClan($request, $response)
  {
    $clan = $this->auth->user()->clan;
    if($clan){
      $clan->izbrisi();
      $this->flash->addMessageNow('success','Избришавте фамилија');
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','Неуспешно');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getJoinClan($request, $response)
  {
   $name = $request->getParam('name');
   $user = $this->auth->user();
   $clan = Clan::where('name',trim($name))->first();
   if(!$clan->isMember($user->id) && $user->contact->clan == ""){
       if($clan->join($user->id)){
         $this->flash->addMessageNow('success','Испрати понуда за придружба на '.$clan->name);
         return $this->view->render($response, '/templates/partials/flash.twig');
       }
       $this->flash->addMessageNow('error','Во исчекување е понудата за придружба');
       return $this->view->render($response, '/templates/partials/flash.twig');
     }else{
       $this->flash->addMessageNow('info','Вие веќе сте член на фамилија');
       return $this->view->render($response, '/templates/partials/flash.twig');
     }
     $this->flash->addMessageNow('error','Не успешно праќање на порака. Освежете ја страницата');
     return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getConfirmClan($request, $response)
  {
    $name = $request->getParam('name');
    $clan = $this->auth->user()->clan;
    $user = User::where('username',trim($name))->first();
    if(!$clan->isMember($user->id)){
     if($clan->confirm($user->id)){
       $user->contact->update(['clan' => $clan->name]);
       $clan->refreshStats();
       $this->flash->addMessageNow('success','Ја прифативте понудата за влез во фамилијата од '.$user->username);
       return $this->view->render($response, '/templates/partials/flash.twig');
     }
   }else{
     $this->flash->addMessageNow('info','Веќе сте во фамилија !');
     return $this->view->render($response, '/templates/partials/flash.twig');
   }
   $this->flash->addMessageNow('error','ГРЕШКА! Неуспешна понуда за влез во фамилијата!');
   return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getLeaveClan($request, $response)
  {
    $id = $request->getParam('id');
    $user = User::findOrFail($id);
    $clan = $this->auth->user()->clan;
    if(!$clan){ $clan = Clan::where('name', $user->contact->clan)->first();}
      if( $clan->isMember($user->id) && ($user->id == $this->auth->user()->id || $this->auth->user()->id == $clan->user_id)){
        if($clan->remove($user->id)){
          $user->contact->update(['clan' => ""]);
          $clan->substract(['pari' => $user->prom->pari,'mok' => $user->prom->mok,'pocit'=>$user->prom->pocit]);
          $this->flash->addMessageNow('success','Ја напушти фамилијата.  '.$user->username);
          return $this->view->render($response, '/templates/partials/flash.twig');
        }
      }
      $this->flash->addMessageNow('error','ГРЕШКА! Неуспешно бришење на пријател!');
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
  public function getPlanedCrime($request, $response)
  {
    $sofer = trim($request->getParam('sofer'));
    $hitmen = trim($request->getParam('hitmen'));
    $pari = $request->getParam('pari');
    if(isset($sofer) && $sofer !== '' && isset($hitmen) && $hitmen !== '' && isset($pari) && $pari !== ''){
      $user = $this->auth->user();
      if(!Crime::where('user_id',$user->id)->first()){
        $sofer = User::where('username',$sofer)->first();
        $hitmen = User::where('username',$hitmen)->first();
        if($sofer && $hitmen && $user->prom->hasMoney($pari)){
          if($user->contact->isFriend($sofer->id) && $user->contact->isFriend($hitmen->id)){
            $crimes = json_decode($sofer->contact->crime_pending,true);
            if($crimes[$user->id] == null){
             $crimes[$user->id] = 'sofer';
             $sofer->contact->update(['crime_pending' => json_encode($crimes)]);
            }else{
             $this->flash->addMessageNow('error','Veke go imate zadadeno na zadaca '.$sofer->username);
             return $this->view->render($response, '/templates/partials/flash.twig');
            }
            $crimes = json_decode($hitmen->contact->crime_pending,true);
            if($crimes[$user->id] == null){
             $crimes[$user->id] = 'hitmen';
             $hitmen->contact->update(['crime_pending' => json_encode($crimes)]);
            }else{
             $this->flash->addMessageNow('success','Веќе му имате зададено задача. '.$hitmen->username);
             return $this->view->render($response, '/templates/partials/flash.twig');
            }
            $user->crime()->create([
             'type'    => 'kill',
             'sofer'       =>'{"accept":0,"id":'.$sofer->id.',"car":""}',
             'hitmen'      => '{"accept":0,"id":'.$hitmen->id.',"guns":""}',
             'invest'         => $pari
            ]);
            $user->prom->update(['pari'=> $user->prom->pari - $pari]);
            $this->flash->addMessageNow('success','Криминалот е организиран, потребно е потврда од пријателите.');
            return $this->view->render($response, '/templates/partials/flash.twig');
          }else{
            $this->flash->addMessageNow('error','Не сте пријател со нив');
            return $this->view->render($response, '/templates/partials/flash.twig');
          }
        }
      }else{
        $this->flash->addMessageNow('error','Веќе имате организирано криминал.');
        return $this->view->render($response, '/templates/partials/flash.twig');
      }
      }
      $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
  public function getPlanedAcept($request, $response)
  {
    $id = $request->getParam('id');
    $pick = explode('_',$request->getParam('pick'));
    if(isset($id) && $id !== '' && isset($pick) && $pick !== ''){
      $user = $this->auth->user();
      $user1 = User::findOrFail($id);
      $contracts = json_decode($user->contact->crime_pending,true);
      if($contracts[$user1->id] != null){
        $vals = json_decode($user1->crime->{$contracts[$user1->id]},true);
        $vals['accept'] = 1;
        if($contracts[$user1->id] == "sofer"){
          $cars =  json_decode($user->inventory->cars,true);
          $car = explode('_', $cars[$pick[0]]);
          $vals['car'] = $pick[0].'_'.$car[$pick[1]];
          $user1->crime->update([$contracts[$user1->id] => json_encode($vals,true)]);
          $user->inventory->removeCar($pick[0],$pick[1]);
          unset($contracts[$user1->id]);
          $user->contact->update(['crime_pending'=>json_encode($contracts,true)]);
          $this->flash->addMessageNow('success','Ptvrdivte');
          return $this->view->render($response, '/templates/partials/flash.twig');
        }else if($contracts[$user1->id] == "hitmen"){
            $weapons =  json_decode($user->inventory->weapons,true);
            switch ($pick[0]) {
              case 1:
                if($weapons[13]>=1 && $weapons[2]>=1 && $weapons[3]>=1 && $weapons[8]>=1){
                  $weapons[13]--;
                  $weapons[2]--;
                  $weapons[3]--;
                  $weapons[8]--;
                  $vals['guns'] = 2;
                }
                break;
              case 2:
                if($weapons[7]>=2 && $weapons[4]>=1 && $weapons[11]>=1 && $weapons[9]>=1){
                  $weapons[7]-=2;
                  $weapons[11]--;
                  $weapons[2]--;
                  $weapons[9]--;
                  $vals['guns'] = 4;
                }
                break;
              case 3:
                if($weapons[14]>=1){
                  $weapons[14]--;
                  $vals['guns'] = 5;
                }
                break;
              default:
                $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
                return $this->view->render($response, '/templates/partials/flash.twig');
            }
            $user1->crime->update([$contracts[$user1->id] => json_encode($vals,true)]);
            $user->inventory->update(['weapons'=>json_encode($weapons,true)]);
            unset($contracts[$user1->id]);
            $user->contact->update(['crime_pending'=>json_encode($contracts,true)]);
            $this->flash->addMessageNow('success','Ptvrdivte');
            return $this->view->render($response, '/templates/partials/flash.twig');
        }
      }
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getPlanedStart($request, $response)
  {
    $user = $this->auth->user();
    if($user->crime->accepted('sofer')&&$user->crime->accepted('hitmen')){
      $sofer = User::findOrFail($user->crime->prom('sofer','id'));
      $hitmen = User::findOrFail($user->crime->prom('hitmen','id'));
      if($user->prom->points >=5 && $sofer->prom->points >=5 && $hitmen->prom->points >=5){
        if($user->task->add(30)){
        $chance = $this->randomlib->generateInt(0, 100);
        $car = explode("_",$user->crime->prom('sofer','car'));
        $first =(($car[0]-1) * 2) + 20 + $user->crime->prom('hitmen','guns');
          switch (true) {
            case $chance <= $first:
                $user->prom->update(['pari'=> $user->prom->pari + $user->crime->invest * 2 ]);
                $sofer->prom->update(['pari'=>$sofer->prom->pari  +$user->crime->invest / 2 ]);
                $sofer->inventory->addCar($car[0],$car[1]);
                $hitmen->prom->update(['pari'=> $hitmen->prom->pari +$user->crime->invest / 2 ]);
                $hitmen->inventory->addWeapons($user->crime->prom('hitmen','guns'));
                $user->crime->delete();
                $this->flash->addMessageNow('success','Uspeavte');
                echo "<input type='hidden' value='30'>";
                return $this->view->render($response, '/templates/partials/flash.twig');
            case $chance > $first   &&  $chance <= $first + 15 + $user->crime->prom('hitmen','guns') :
                $user->crime->delete();
                $this->flash->addMessageNow('error','Ve fanaaa');
                echo "<input type='hidden' value='30'><input type='hidden' value='10'>";
                return $this->view->render($response, '/templates/partials/flash.twig');
            default:
              $user->crime->delete();
              $sofer->inventory->addCar($car[0],$car[1]);
              $this->flash->addMessageNow('info','Ne uspeavte no se spasivte');
              echo "<input type='hidden' value='30'>";
              return $this->view->render($response, '/templates/partials/flash.twig');
          }
        }
      }
      $this->flash->addMessageNow('error','Nemate dovolno poeni');
      echo "<input type='hidden' value='1'>";
      return $this->view->render($response, '/templates/partials/flash.twig');
    }
    $this->flash->addMessageNow('error','Настана некаква грешка. Ве молиме освежете ја страницата.');
    echo "<input type='hidden' value='1'>";
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
  public function getTrkalo($request, $response)
  {
    $user = $this->auth->user();
    if($user->energy->trkalo > 0){
      $user->energy->decrement('trkalo');
      $num = $this->randomlib->generateInt(1, 12);
      switch ($num) {
        case 1:
          $user->prom->pari += 750;
        break;
        case 2:
          $user->prom->pari += 900;
        break;
        case 3:
          $user->prom->pari += 1050;
        break;
        case 4:
          $user->prom->pari += 1200;
        break;
        case 5:
          $user->prom->pari += 1350;
        break;
        case 6:
          $user->prom->pari += 1500;
        break;
        case 7:
          $user->prom->iskustvo += 50;
        break;
        case 8:
          $user->prom->mok += 20;
        break;
        case 9:
          $user->prom->pari += 50;
        break;
        case 10:
          $user->prom->pari += 200;
        break;
        case 11:
          $user->prom->pari += 350;
        break;
        case 12:
          $user->prom->pari += 500;
        break;
      }
      $user->prom->save();
      return (string)$num;
    }
    $this->flash->addMessageNow('info','Tolku od trkaloto za denes cekajte');
    return $this->view->render($response, '/templates/partials/flash.twig');
  }
}
