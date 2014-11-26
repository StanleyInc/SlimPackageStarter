<?php
use \Illuminate\Database\Eloquent\SoftDeletingTrait;

class User_Group extends Model {
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];	
	protected $table = 'users_groups';

	public function group()
	{
		return $this->hasOne('Group');
	}

	public function user()
	{
		return $this->hasOne('User');
	}
}