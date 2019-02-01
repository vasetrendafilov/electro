<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stocks Extends Model
{
	protected $table = "stocks";
  public function state($val)
  {
    return $this->{$val};
  }

}
