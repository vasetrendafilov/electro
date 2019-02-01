<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Clan Extends Model
{
	protected $table = "clans";

	protected $fillable = [
		'moto',
    'name',
		'email',
    'members',
    'members_ids',
		'pending_members',
		'pocit',
    'mok',
    'pari'
	];
	public function substract($vals)
	{
		foreach ($vals as $key => $val) {
			$this->update([$key => $this->{$key} - $val]);
		}
	}
	public function izbrisi()
	{
		$ids = explode('_', $this->members_ids);
		unset($ids[0]);
		foreach ($ids as  $id) {
			$user = User::find($id);
		  $user->contact->update(['clan' => ""]);
		}
		$this->delete();
	}
	public function refreshStats()
	{
		$pocit = $pari = $mok = 0;
		$members = $this->members();
		foreach ($members as $member) {
			$pocit += $member->prom->pocit;
			$pari += $member->prom->pari;
			$mok += $member->prom->mok;
		}
		$this->update(['pocit' => $pocit]);
		$this->update(['pari' => $pari]);
		$this->update(['mok' => $mok]);
	}
	public function members()
	{
		$members = array();
		$ids = explode('_', $this->members_ids);
		unset($ids[0]);
		foreach ($ids as $id){
		  array_push($members , User::find($id));
		}
		return $members;
	}
	public function isMember($id){
		$ids = explode('_', $this->members_ids);
		if(in_array((string)$id, $ids)){
			return true;
		}
		return false;
	}
	public function join($id)
	{
		$ids = explode('_', $this->pending_members);
		if(!in_array((string)$id, $ids)){
			$this->update(['pending_members' => $this->pending_members.'_'.$id]);
			return true;
		}
		return false;
	}
	public function confirm($id)
	{
		$ids = explode('_', $this->pending_members);
		if(in_array((string)$id, $ids)){
			foreach ($ids as $key => $value){
				if($value == $id){ unset($ids[$key]); }
			}
			$this->update(['pending_members' => implode("_", $ids)]);
			$this->update(['members_ids' => $this->members_ids.'_'.$id]);
			$this->increment('members');
			return true;
		}
		return false;
	}
	public function remove($id)
	{
		$ids = explode('_', $this->members_ids);
		if(in_array((string)$id, $ids)){
			foreach ($ids as $key => $value){
				if($value == $id){ unset($ids[$key]);}
			}
			$this->update(['members_ids' => implode("_", $ids)]);
      $this->decrement('members');
			return true;
		}
		return false;
	}
	public function getMembersPending(){
		$users = array();
		$ids = explode('_', $this->pending_members);
		unset($ids[0]);
		foreach ($ids as $num => $id) {
			if($num > 5){break;}
		  array_push($users , User::find($id));
		}
		return $users;
	}
}
