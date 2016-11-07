<?php

/*
 * Giannis Tsapelas 2015
 */

class Building {
    public $id;
    public $name;
    public $type;
    public $consumption = array("heating" => null, "cooling" => null, "other" => null);
    public $source = array("electricity" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "fuel" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "naturalGas" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "other" => 
                                array("heating" => null, "cooling" => null, "other" => null) );
    public $production;
    public $prices;
    public $pricesBaseline;
    public $consumptionBaseline = array("heating" => null, "cooling" => null, "other" => null);
    public $sourceBaseline = array("electricity" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "fuel" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "naturalGas" => 
                                array("heating" => null, "cooling" => null, "other" => null),
                           "other" => 
                                array("heating" => null, "cooling" => null, "other" => null) );
    public $productionBaseline;
    public $actions;
    public $status = "nodata"; // or included or excluded_nodata or excluded_data
    

                           
    
    
    public function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function getType(){
        return $this->type;
    }
    
    public function setType($type){
        $this->type = $type;
    }
    
    public function getConsumption(){
        return $this->consumption;
    }
    
    public function setConsumption($consumption){
        $this->consumption = $consumption;
    }
    
    public function getSource(){
        return $this->source;
    }
    
    public function setSource($source){
        $this->source = $source;
    }
    
    public function getProduction(){
        return $this->production;
    }
    
    public function setProduction($production){
        $this->production = $production;
    }
    
    
    public function getPrices(){
        return $this->prices;
    }
    
    public function setPrices($prices){
        $this->prices = $prices;
    }
    
    public function getPricesBaseline(){
        return $this->pricesBaseline;
    }
    
    public function setPricesBaseline($pricesBaseline){
        $this->pricesBaseline = $pricesBaseline;
    }
    
    public function getConsumptionBaseline(){
        return $this->consumptionBaseline;
    }
    
    public function setConsumptionBaseline($consumptionBaseline){
        $this->consumptionBaseline = $consumptionBaseline;
    }
    
    public function getSourceBaseline(){
        return $this->sourceBaseline;
    }
    
    public function setSourceBaseline($sourceBaseline){
        $this->sourceBaseline = $sourceBaseline;
    }
    
    public function getProductionBaseline(){
        return $this->productionBaseline;
    }
    
    public function setProductionBaseline($productionBaseline){
        $this->productionBaseline = $productionBaseline;
    }
    
    public function getActions(){
        return $this->actions;
    }
    
    public function setActions($actions){
        $this->actions = $actions;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function setStatus($status){
        $this->status = $status;
    }
    

}
