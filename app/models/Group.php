<?php
use \Illuminate\Database\Eloquent\SoftDeletingTrait;

class Group extends Model {
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

	public function usergroup()
	{
		return $this->belongsTo('User_Group');
	}
}