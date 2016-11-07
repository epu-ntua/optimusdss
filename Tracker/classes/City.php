<?php

/*
 * Giannis Tsapelas 2015
 */

class City {
    public $id;
    public $cityName;
    public $country;
    public $targets = array("consumption" => null, "emissions" => null, "cost" => null, "res" => null);
    
    public $baselineOptions = array();
    
    public function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function getCityName(){
        return $this->cityName;
    }
    
    public function setCityName($cityName){
        $this->cityName = $cityName;
    }
    
    public function getCountry(){
        return $this->country;
    }
    
    public function setCountry($country){
        $this->country = $country;
    }
    
    public function getTargets(){
        return $this->targets;
    }
    
    public function setTargets($targets){
        $this->targets = $targets;
    }
    
    
    
    public function getBaselineOptions(){
        return $this->baselineOptions;
    }
    
    public function setBaselineOptions($baselineOptions){
        $this->baselineOptions = $baselineOptions;
    }
}
