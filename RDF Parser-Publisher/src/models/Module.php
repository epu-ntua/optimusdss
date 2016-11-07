<?php

use Illuminate\Database\Eloquent\Model as Model;

class Module extends Model {
    protected $fillable = ['site_id','name','type','username','password','source','target','searchfile','isactive'];
    public $timestamps = false;

    public function site()
    {
        return $this->belongsTo('Site');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('name', $type);
    }

    /*public function scopeOfTypeMappingops($query, $type)
    {
        return $query->where('name', $type);
    }*/

    public function mappingops()
    {
		return $this->hasMany('MappingOp');
    }

    public function lastreads()
    {
        return $this->hasMany('Lastread');
    }

}
