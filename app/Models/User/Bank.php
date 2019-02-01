<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;


class Bank Extends Model
{
	protected $table = "users_banks";

	protected $fillable = [
		'drzavna',
		'svetska',
    'small',
    'big'
	];
	public static $defaults=[
		'drzavna' => "{\"permission\":0,\"pari\":0,\"transakcii\":10}",
		'svetska' => "{\"permission\":0,\"pari\":0,\"transakcii\":10}",
		'small' => "{\"permission\":0,\"pari\":0,\"limit\":15000000}",
		'big' => "{\"permission\":0,\"pari\":0,\"limit\":50000000}"
	];
  public function hasPermission($bank)
  {
    return (bool)json_decode($this->{$bank},true)['permission'];
  }
	public function stuff($bank,$type)
	{
		return json_decode($this->{$bank},true)[$type];
	}
	public function unlock($bank)
	{
		$val = json_decode($this->{$bank},true);
		$val['permission'] = 1;
		$this->update([$bank => json_encode($val,true)]);
	}
	public function removeMoney($bank)
	{
		$val = json_decode($this->{$bank},true);
	  $val['pari'] -= ($val['pari'] * 50)/100 ;
	  $this->update([$bank => json_encode($val,true)]);
		return $val['pari'];
	}
  public function isLeft($bank,$val){
		return (json_decode($this->{$bank},true)['pari'] -$val >= 0 ? true:false);
	}
  public function transfer($user,$money,$type,$operation)
  {
    if($user->bank->hasPermission($type) && $user->prom->hasMoney($money)){
        $bank =  json_decode($user->bank->{$type},true);
				if($type == "big" || $type == "small"){
          if($bank['limit'] <= $bank['pari'] + $money && $operation =="add"){ return 2;}
        }else{
           $bank['transakcii']--;
        }
        if($operation == "add"){
          $user->prom->pari -= $money;
          $bank['pari']+=$money;
        }else{
					 if($this->isLeft($type,$money)){
          $user->prom->pari += $money;
          $bank['pari']-=$money;
					 }else{return 4;}
        }
        $user->bank->update([$type=> json_encode($bank,true)]);
        $user->prom->save();//za poubav kod
				 return 3;
    }else{
      return 1;
    }
    return 0;
  }
}
