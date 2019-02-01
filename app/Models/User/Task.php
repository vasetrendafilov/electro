<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Task Extends Model
{
	protected $table = "users_tasks";

	protected $fillable = [
		'time'
	];
  public function add($time)
  {
      return (bool)$this->update(['time'=> $time ]);
  }
  public function finished()
  {
    if($this->time != 0){
      $now = Carbon::now();
      if($now->gte($this->updated_at->addSeconds($this->time))){
        if($this->update(['time'=> 0 ])){
          return true;
        }
      }
      return false;
    }
    return true;
  }
}
