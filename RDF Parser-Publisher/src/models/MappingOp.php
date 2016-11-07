<?php

use Illuminate\Database\Eloquent\Model as Model;

class MappingOp extends Model{
	protected $fillable = ['site_id','module_id','measure','stream','sensor','units'];
	public $timestamps = false;

	public function index()
	{
    	return $this->all()->toArray();
	}

	 public function module()
    {
		return $this->belongsTo('Module');
    }

    public function getHeaders(){

    }


}
