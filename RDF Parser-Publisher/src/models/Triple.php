<?php

use Illuminate\Database\Eloquent\Model as Model;

class Triple extends Model {
    protected $fillable = ['timestamp','triple','stream','city','site_name','sensor','target'];
    public $timestamps = false;
}
