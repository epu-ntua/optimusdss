<?php

/*
 * Giannis Tsapelas 2015
 */

class CitySubmission {
    public $id;
    public $name;
    public $year;
    public $visibility;
    
    public $factors;
    
    public $factorsBaseline;
    public $baseline;
    public $targetYear;
    public $buildings = array();
    public $resultsInitial = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    public $resultsCurrent = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    public $resultsProjected = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    public $resultsProjectedCompare = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    
    public $resultsPercentCurrent = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    public $resultsPercentProjected = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    public $resultsPercentProjectedCompare = array("city"=> null, "administration"=> null, "hospitals"=> null, "education"=> null, "entertainment"=> null, "sport"=> null, "other"=> null);
    
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
    
    public function getYear(){
        return $this->year;
    }
    
    public function setYear($year){
        $this->year = $year;
    }
    
    public function getVisibility(){
        return $this->visibility;
    }
    
    public function setVisibility($visibility){
        $this->visibility = $visibility;
    }
    
    public function getFactors(){
        return $this->factors;
    }
    
    public function setFactors($factors){
        $this->factors = $factors;
    }
    
    public function getFactorsBaseline(){
        return $this->factorsBaseline;
    }
    
    public function setFactorsBaseline($factorsBaseline){
        $this->factorsBaseline = $factorsBaseline;
    }
    
    public function getBuildings(){
        return $this->buildings;
    }
    
    public function setBuildings($buildings){
        $this->buildings = $buildings;
    }
    
    public function addBuilding($building){
         array_push($this->buildings, $building);
    }
    
    public function getResultsPercentProjected(){
        return $this->resultsPercentProjected;
    }
    
    public function setResultsPercentProjected($resultsPercentProjected){
        $this->resultsPercentProjected = $resultsPercentProjected;
    }
    
    public function getResultsPercentProjectedCompare(){
        return $this->resultsPercentProjectedCompare;
    }
    
    public function setResultsPercentProjectedCompare($resultsPercentProjectedCompare){
        $this->resultsPercentProjectedCompare = $resultsPercentProjectedCompare;
    }
    
    public function getResultsPercentCurrent(){
        return $this->resultsPercentCurrent;
    }
    
    public function setResultsPercentCurrent($resultsPercentCurrent){
        $this->resultsPercentCurrent = $resultsPercentCurrent;
    }
    
    public function getResultsProjected(){
        return $this->resultsProjected;
    }
    
    public function setResultsProjected($resultsProjected){
        $this->resultsProjected = $resultsProjected;
    }
    
    public function getResultsProjectedCompare(){
        return $this->resultsProjectedCompare;
    }
    
    public function setResultsProjectedCompare($resultsProjectedCompare){
        $this->resultsProjectedCompare = $resultsProjectedCompare;
    }
    
    public function getResultsCurrent(){
        return $this->resultsCurrent;
    }
    
    public function setResultsCurrent($resultsCurrent){
        $this->resultsCurrent = $resultsCurrent;
    }
    
    public function getResultsInitial(){
        return $this->resultsInitial;
    }
    
    public function setResultsInitial($resultsInitial){
        $this->resultsInitial = $resultsInitial;
    }
    
    public function getBaseline(){
        return $this->baseline;
    }
    
    public function setBaseline($baseline){
        $this->baseline = $baseline;
    }
    
    public function getTargetYear(){
        return $this->targetYear;
    }
    
    public function setTargetYear($targetYear){
        $this->targetYear = $targetYear;
    }
}
