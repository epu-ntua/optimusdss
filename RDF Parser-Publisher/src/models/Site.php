<?php

use Illuminate\Database\Eloquent\Model as Model;

class Site extends Model {
    protected $fillable = ['city','site_name'];
    public $timestamps = false;


    public function index()
    {
        return $this->all()->toArray();
    }

    public function modules()
    {
        return $this->hasMany('Module');
        //return Module::where('id',$this->site_id)->first()->name;

    }
    /*public function getModules()
    {
    	echo "this id".$this->attributes->id."\n";
    	return Module::where('id',$this->site_id)->get();
    }*/

}
