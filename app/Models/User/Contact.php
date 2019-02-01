<?php

namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;


class Contact Extends Model
{
	protected $table = "users_contacts";

	protected $fillable = [
		'friends',
		'friends_ids',
		'friends_pending',
		'clan',
		'crime_pending',
		'unread_msg',
		'blacklist'
	];
  public static $defaults=[
    'friends' => 0,
    'friends_ids' => '',
    'friends_pending' => '',
		'crime_pending' => '',
    'clan' => '',
		'unread_msg' => '',
    'blacklist' => ''
  ];
	public function checkMsg($id)
	{
		$ids = explode('_', $this->unread_msg);
		if(in_array((string)$id, $ids)){
			return 'Unread msg';
		}
		return '';
	}
	public function addMsg($id)
	{
		$ids = explode('_', $this->unread_msg);
		if(!in_array((string)$id, $ids)){
			$this->update(['unread_msg' => $this->unread_msg.'_'.$id]);
			return true;
		}
		return false;
	}
	public function removeMsg($id)
	{
		$ids = explode('_', $this->unread_msg);
		if(in_array((string)$id, $ids)){
			foreach ($ids as $key => $value) {
				if($value == $id){
					unset($ids[$key]);
					$this->update(['unread_msg' => implode("_", $ids)]);
				}
			}
		}
	}
	public function friendsLike($key)
	{
	  $friends = $this->getFriends();
		foreach ($friends as $friend) {
			$count = 0;
			for ($i=0; $i < strlen($key) ; $i++) {
				if($friend->username[$i] == $key[$i]){$count++;}
			}
			if($count == strlen($key)){
				return true;
			}
		}
		return false;
	}
	public function isFriend($id){
		if(in_array((string)$id, explode('_', $this->friends_ids))){
			return true;
		}
		return false;
	}
	public function isBlocked($id){
			$ids = explode('_', $this->blacklist);
			if(in_array((string)$id, $ids)){
				return true;
			}
			return false;
	}
	public function areFriends($users){
		$count = 0;
		foreach ($users as $user){
			if($this->isFriend($user->id)){$count++;}
		}
		if($users->count() == $count){return false;}
		return true;
	}
	public function block($id,$clan)
	{
		$ids = explode('_', $this->friends_pending);
		if(in_array((string)$id,$ids)){
			foreach ($ids as $key => $value) {
				if($value == $id){
					unset($ids[$key]);
				}
			}
			$this->update(['friends_pending' => implode("_", $ids)]);
			return false;
		}else if($clan && in_array((string)$id, explode('_', $clan->pending_members))){
			$ids =  explode('_', $clan->pending_members);
			foreach ($ids as $key => $value) {
				if($value == $id){
					unset($ids[$key]);
				}
			}
			$clan->update(['pending_members' => implode("_", $ids)]);
			return false;
		}else if(!in_array((string)$id, explode('_', $this->blacklist))){
			$this->update(['blacklist' => $this->blacklist.'_'.$id]);
			return true;
		}
	}
	public function unblock($id)
	{
		$ids = explode('_', $this->blacklist);
		if(in_array((string)$id, $ids)){
			foreach ($ids as $key => $value) {
				if($value == $id){
					unset($ids[$key]);
					$this->update(['blacklist' => implode("_", $ids)]);
					return true;
				}
			}
		}
		return false;
	}
	public function add($id)
	{
		$ids = explode('_', $this->friends_pending);
		if(!in_array((string)$id, $ids)){
			$this->update(['friends_pending' => $this->friends_pending.'_'.$id]);
			return true;
		}
		return false;
	}
	public function confirm($user)
	{
		$ids = explode('_', $this->friends_pending);
		if(in_array((string)$user->id, $ids)){
			foreach ($ids as $key => $value) {
				if($value == $user->id){
					 unset($ids[$key]);
				}
			}
			$this->update(['friends_pending' => implode("_", $ids)]);
			$this->update(['friends_ids' => $this->friends_ids.'_'.$user->id]);
			$this->increment('friends');
			$user->contact->update(['friends_ids' => $user->contact->friends_ids.'_'.$this->user_id]);
			$user->contact->increment('friends');
			return true;
		}
		return false;
	}
	public function remove($user)
	{
		$ids = explode('_', $this->friends_ids);
		if(in_array((string)$user->id, $ids)){
			foreach ($ids as $key => $value) {
				if($value == $user->id){
					 unset($ids[$key]);
				}
			}
			$this->update(['friends_ids' => implode("_", $ids)]);
			$this->update(['friends' =>  $this->friends - 1]);
			$ids = explode('_', $user->contact->friends_ids);
			if(in_array((string)$this->user_id, $ids)){
				foreach ($ids as $key => $value) {
					if($value == $this->user_id){
						 unset($ids[$key]);
					}
				}
				$user->contact->update(['friends_ids' => implode("_", $ids)]);
				$this->decrement('friends');
				return true;
			}
		}
		return false;
	}
	public function getFriends(){
		$users = array();
		$ids = explode('_', $this->friends_ids);
		unset($ids[0]);
		foreach ($ids as $num => $id) {
			if($num > 5){break;}
			array_push($users,User::find($id));
		}
		return $users;
	}
	public function getFriendsPending(){
		$users = array();
		$ids = explode('_', $this->friends_pending);
		unset($ids[0]);
		foreach ($ids as $num => $id) {
			if($num > 5){break;}
			array_push($users,User::find($id));
		}
		return $users;
	}
}
