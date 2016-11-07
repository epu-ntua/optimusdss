<?php
use Illuminate\Database\Eloquent\Model as Model;

class Lastread extends Model{
	protected $fillable = ['module_id','name','value'];
	public $timestamps = false;

	public function index()
	{
    	return $this->all()->toArray();
	}

	 public function module()
    {
		return $this->belongsTo('Module');
    }



}
