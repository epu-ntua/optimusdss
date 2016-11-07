<?php

require_once '../classes/CitySubmission.php';
require_once '../classes/City.php';
require_once '../classes/Building.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$City = $_SESSION['City'];
$CitySubmission = $_SESSION['CitySubmission'];

require_once("../includes/config.php");
require_once("../includes/database.php");

mysql_query("SET AUTOCOMMIT = 0");

try {
    mysql_query("START TRANSACTION");

    if($_SESSION['mode'] == 'insert'){   

        $i = 0;
        $sql = "SELECT * FROM city WHERE name = '".$City->getCityName()."' AND country = '".$City->getCountry()."'";
        $result = mysql_query($sql,$database->connection);
        if(!$result){
           throw new Exception(mysql_error());
        }
        while($row = mysql_fetch_array($result)){
            $i++;
            $City->setId($row['id']);
        }
        if($i == 0){
            $sql = "INSERT INTO city (name, country)
            VALUES ('".trim($City->getCityName())."', '".trim($City->getCountry())."')";    

            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }

            $City->setId(mysql_insert_id());
        }




        $sql = "INSERT INTO submission (user_id, city_id, year, date, visibility, baseline, name)
        VALUES (".$_SESSION['user'].", ".$City->getId().", '".$CitySubmission->getYear()."','".date('Y-m-d')."', '".trim($CitySubmission->getVisibility())."', ".$CitySubmission->getBaseline().", '".$CitySubmission->getName()."')";

        if(!mysql_query($sql,$database->connection)){
           throw new Exception(mysql_error());
            
        }

        $CitySubmission->setId(mysql_insert_id());




        if($i == 0){
            $sql = "INSERT INTO targets (city_id, year, consumption, emissions, cost, res)
            VALUES (".$City->getId().", ".$CitySubmission->getTargetYear().", ".$City->getTargets()['consumption'].", ".$City->getTargets()['emissions'].","
                     .$City->getTargets()['cost'].",".$City->getTargets()['res'].")";

            if(!mysql_query($sql,$database->connection)){
                //die('Ooops! We encountered a database error');
                throw new Exception(mysql_error());
            }
        }
        else{
            $sql = "UPDATE targets SET year = ".$CitySubmission->getTargetYear().", consumption = ".$City->getTargets()['consumption'].", 
                                       emissions = ".$City->getTargets()['emissions'].", cost = ".$City->getTargets()['cost'].", res = ".$City->getTargets()['res']." WHERE  city_id = ".$City->getId();

            if(!mysql_query($sql,$database->connection)){
                //die('Ooops! We encountered a database error');
                throw new Exception(mysql_error());
            }
        }


        $sql = "INSERT INTO emissionfactors (submission_id, electricity, naturalGas, fuel, other)
        VALUES (".$CitySubmission->getId().", '".$CitySubmission->getFactors()['electricity']."', '".$CitySubmission->getFactors()['naturalGas'].
                "', '".$CitySubmission->getFactors()['fuel']."', '".$CitySubmission->getFactors()['other']."')";

        if(!mysql_query($sql,$database->connection)){
           throw new Exception(mysql_error());
        }
		
		
		$sql = "SELECT id "
			 . "FROM submission "
			 . "WHERE city_id = '" . $_SESSION['City']->getId(). "' AND user_id = '" . $_SESSION['user'] . "' AND year = '" . $_SESSION['CitySubmission']->getBaseline() . "'";

		   if(!$sql = mysql_query($sql, $database->connection)){
			  throw new Exception(mysql_error());
			  //die('Ooops! We encountered a database error');
		   }

		   $baselineID = null;
		   while($row = mysql_fetch_array($sql)){
			  $baselineID = $row['id'];
		   }
		   if($baselineID == null){ 
			   $sql =  "INSERT INTO submission (user_id, city_id, year, date, visibility, baseline, name)
						VALUES (".$_SESSION['user'].", ".$City->getId().", '".$CitySubmission->getBaseline()."','".date('Y-m-d')."', '".trim($CitySubmission->getVisibility())."', ".$CitySubmission->getBaseline().", '".$CitySubmission->getName()."_Baseline')";

				if(!mysql_query($sql,$database->connection)){
				   throw new Exception(mysql_error());
					
				}

			   $baselineID = mysql_insert_id();
			   
			    $sql = "INSERT INTO emissionfactors (submission_id, electricity, naturalGas, fuel, other)
				VALUES (".$baselineID.", '".$CitySubmission->getFactorsBaseline()['electricity']."', '".$CitySubmission->getFactorsBaseline()['naturalGas'].
						"', '".$CitySubmission->getFactorsBaseline()['fuel']."', '".$CitySubmission->getFactorsBaseline()['other']."')";

				if(!mysql_query($sql,$database->connection)){
				   throw new Exception(mysql_error());
				}
		   }


        foreach($CitySubmission->getBuildings() as $Building){
            if($Building->getStatus()=="included"){
            $actions = $Building->getActions();

            //insert/update building
            if($Building->getId()< 0){
                $sql = "INSERT INTO building (name, type, city_id)
                VALUES ('".trim($Building->getName())."', '".trim($Building->getType())."', '".$City->getId()."')";

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }
                $Building->setId(mysql_insert_id());


            }
            else{
                $sql = "UPDATE building SET name = '".trim($Building->getName())."', type='".trim($Building->getType())."', city_id='".$City->getId()."' WHERE id = ".$Building->getId();

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }
            }


            foreach($actions as $action){
                $sql = "INSERT INTO building_actionplans (submission_id, building_id, actionplan_id, minmax)
                VALUES (".$CitySubmission->getId().", ".$Building->getId().", ".$action['id'].", '".$action['minmax']."')";

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }
            }

            $data = $Building->getSource();
            $sql = "INSERT INTO building_sources (submission_id, building_id, electricity_heating, electricity_cooling, electricity_other,
                                                                              naturalGas_heating,  naturalGas_cooling,  naturalGas_other,
                                                                              fuel_heating,        fuel_cooling,        fuel_other, 
                                                                              other_heating,     other_cooling,     other_other)
                    VALUES (".$CitySubmission->getId().",  ".$Building->getId()
                                                            .",".$data['electricity']['heating'].", ".$data['electricity']['cooling'].", ".$data['electricity']['other']
                                                            .",".$data['naturalGas']['heating'].", ".$data['naturalGas']['cooling'].", ".$data['naturalGas']['other']
                                                            .",".$data['fuel']['heating'].",".$data['fuel']['cooling'].",".$data['fuel']['other']
                                                            .",".$data['other']['heating'].",".$data['other']['cooling'].",".$data['other']['other'].")";
            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }

            $data = $Building->getConsumption();
            $sql = "INSERT INTO building_consumption (submission_id, building_id, heating, cooling, other, included)
                    VALUES (".$CitySubmission->getId().", ".$Building->getId().",".$data['heating'].", ".$data['cooling'].", ".$data['other'].", 1)";

            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }
            
            
            $sql = "INSERT INTO building_prices (submission_id, building_id, electricity, naturalGas, fuel, other)
            VALUES (".$CitySubmission->getId().", ".$Building->getId().", '".$Building->getPrices()['electricity']."', '".$Building->getPrices()['naturalGas'].
                    "', '".$Building->getPrices()['fuel']."', '".$Building->getPrices()['other']."')";

            if(!mysql_query($sql,$database->connection)){
               throw new Exception(mysql_error());
            }

            

            $sql = "INSERT INTO building_production (submission_id, building_id, production)
                    VALUES (".$CitySubmission->getId().", ".$Building->getId().",".$Building->getProduction().")";

            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }


            $sql = "SELECT id "
                 . "FROM submission "
                 . "WHERE city_id = '" . $_SESSION['City']->getId(). "' AND user_id = '" . $_SESSION['user'] . "' AND year = '" . $_SESSION['CitySubmission']->getBaseline() . "'";

               if(!$sql = mysql_query($sql, $database->connection)){
                  throw new Exception(mysql_error());
                  //die('Ooops! We encountered a database error');
               }

               $baselineID = null;
               while($row = mysql_fetch_array($sql)){
                  $baselineID = $row['id'];
               }

               $sql = "SELECT * "
                    . "FROM building_consumption "
                    . "WHERE building_id = '" . $Building->getId(). "' AND submission_id = '" . $baselineID . "'";

               if(!$sql = mysql_query($sql, $database->connection)){
                  throw new Exception(mysql_error());
                  //die('Ooops! We encountered a database error');
               }

               $baselineSubmited = null;
               while($row = mysql_fetch_array($sql)){
                  $baselineSubmited = $row['heating'];
               }

               if($baselineSubmited == null){ 
                   $data = $Building->getSourceBaseline();
                   $sql = "INSERT INTO building_sources (submission_id, building_id, electricity_heating, electricity_cooling, electricity_other,
                                                                                     naturalGas_heating,  naturalGas_cooling,  naturalGas_other,
                                                                                     fuel_heating,        fuel_cooling,        fuel_other, 
                                                                                     other_heating,     other_cooling,     other_other)
                           VALUES (".$baselineID.",  ".$Building->getId()
                                                                   .",".$data['electricity']['heating'].", ".$data['electricity']['cooling'].", ".$data['electricity']['other']
                                                                   .",".$data['naturalGas']['heating'].", ".$data['naturalGas']['cooling'].", ".$data['naturalGas']['other']
                                                                   .",".$data['fuel']['heating'].",".$data['fuel']['cooling'].",".$data['fuel']['other']
                                                                   .",".$data['other']['heating'].",".$data['other']['cooling'].",".$data['other']['other'].")";
                   echo $sql;
				   if(!mysql_query($sql,$database->connection)){
                       throw new Exception(mysql_error());
                   }

                   $data = $Building->getConsumptionBaseline();
                   $sql = "INSERT INTO building_consumption (submission_id, building_id, heating, cooling, other)
                           VALUES (".$baselineID.", ".$Building->getId().",".$data['heating'].", ".$data['cooling'].", ".$data['other'].")";

                   if(!mysql_query($sql,$database->connection)){
                       throw new Exception(mysql_error());
                   }
				   
				   $sql = "INSERT INTO building_prices (submission_id, building_id, electricity, naturalGas, fuel, other)
					VALUES (".$baselineID.", ".$Building->getId().", '".$Building->getPricesBaseline()['electricity']."', '".$Building->getPricesBaseline()['naturalGas'].
							"', '".$Building->getPricesBaseline()['fuel']."', '".$Building->getPricesBaseline()['other']."')";

					if(!mysql_query($sql,$database->connection)){
					   throw new Exception(mysql_error());
					}

                   $sql = "INSERT INTO building_production (submission_id, building_id, production)
                           VALUES (".$baselineID.", ".$Building->getId().",".$Building->getProductionBaseline().")";

                   if(!mysql_query($sql,$database->connection)){
                       throw new Exception(mysql_error());
                   }
               }

            }
        }



    }
    // ************** EDIT***********************
    else{
        $sql = "UPDATE submission SET visibility = '".trim($CitySubmission->getVisibility())."', baseline=".$CitySubmission->getBaseline().", name='".$CitySubmission->getName()."' WHERE id=".$CitySubmission->getId();

        if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
        }


        $sql = "UPDATE emissionfactors SET electricity = ".$CitySubmission->getFactors()['electricity'].", naturalGas = ".$CitySubmission->getFactors()['naturalGas'].", fuel = ".$CitySubmission->getFactors()['fuel'].", other = ".$CitySubmission->getFactors()['other']." WHERE submission_id=".$CitySubmission->getId();

        if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
        }


        $sql = "DELETE FROM building_actionplans WHERE submission_id=".$CitySubmission->getId();
        if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
        }


        foreach($CitySubmission->getBuildings() as $Building){
            if($Building->getStatus()=="included"){
                $sql = "DELETE FROM building_sources WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }

                $sql = "DELETE FROM building_consumption WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }
                
                $sql = "DELETE FROM building_prices WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }

                $sql = "DELETE FROM building_production WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }    
                
                
                
            $actions = $Building->getActions();

            //insert/update building
            if($Building->getId()< 0){
                $sql = "INSERT INTO building (name, type, city_id)
                VALUES ('".trim($Building->getName())."', '".trim($Building->getType())."', '".$City->getId()."')";

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }

                $Building->setId(mysql_insert_id());
            }
            else{
                $sql = "UPDATE building SET name = '".trim($Building->getName())."', type='".trim($Building->getType())."', city_id='".$City->getId()."' WHERE id = ".$Building->getId();

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }
            }


            foreach($actions as $action){
                $sql = "INSERT INTO building_actionplans (submission_id, building_id, actionplan_id, minmax)
                VALUES (".$CitySubmission->getId().", ".$Building->getId().", ".$action['id'].", '".$action['minmax']."')";

                if(!mysql_query($sql,$database->connection)){
                    throw new Exception(mysql_error());
                }
            }

            $data = $Building->getSource();
            $sql = "INSERT INTO building_sources (submission_id, building_id, electricity_heating, electricity_cooling, electricity_other,
                                                                              naturalGas_heating,  naturalGas_cooling,  naturalGas_other,
                                                                              fuel_heating,        fuel_cooling,        fuel_other, 
                                                                              other_heating,     other_cooling,     other_other)
                    VALUES (".$CitySubmission->getId().",  ".$Building->getId()
                                                            .",".$data['electricity']['heating'].", ".$data['electricity']['cooling'].", ".$data['electricity']['other']
                                                            .",".$data['naturalGas']['heating'].", ".$data['naturalGas']['cooling'].", ".$data['naturalGas']['other']
                                                            .",".$data['fuel']['heating'].",".$data['fuel']['cooling'].",".$data['fuel']['other']
                                                            .",".$data['other']['heating'].",".$data['other']['cooling'].",".$data['other']['other'].")";
            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }

            $data = $Building->getConsumption();
            $sql = "INSERT INTO building_consumption (submission_id, building_id, heating, cooling, other)
                    VALUES (".$CitySubmission->getId().", ".$Building->getId().",".$data['heating'].", ".$data['cooling'].", ".$data['other'].")";

            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }
			
			$sql = "INSERT INTO building_prices (submission_id, building_id, electricity, naturalGas, fuel, other)
            VALUES (".$CitySubmission->getId().", ".$Building->getId().", '".$Building->getPrices()['electricity']."', '".$Building->getPrices()['naturalGas'].
                    "', '".$Building->getPrices()['fuel']."', '".$Building->getPrices()['other']."')";

            if(!mysql_query($sql,$database->connection)){
               throw new Exception(mysql_error());
            }

            $sql = "INSERT INTO building_production (submission_id, building_id, production)
                    VALUES (".$CitySubmission->getId().", ".$Building->getId().",".$Building->getProduction().")";

            if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
            }


                $sql = "SELECT id "
                    . "FROM submission "
                    . "WHERE city_id = '" . $_SESSION['City']->getId(). "' AND user_id = '" . $_SESSION['user'] . "' AND year = '" . $_SESSION['CitySubmission']->getBaseline() . "'";

               if(!$sql = mysql_query($sql, $database->connection)){
                throw new Exception(mysql_error());
                  //die('Ooops! We encountered a database error');
               }

               $baselineID = null;
               while($row = mysql_fetch_array($sql)){
                  $baselineID = $row['id'];
               }

               $sql = "SELECT * "
                    . "FROM building_consumption "
                    . "WHERE building_id = '" . $Building->getId(). "' AND submission_id = '" . $baselineID . "'";

               if(!$sql = mysql_query($sql, $database->connection)){
                throw new Exception(mysql_error());
                  //die('Ooops! We encountered a database error');
               }

               $baselineSubmited = null;
               while($row = mysql_fetch_array($sql)){
                  $baselineSubmited = $row['heating'];
               }

               if($baselineSubmited == null){ 
                   $data = $Building->getSourceBaseline();
                   $sql = "INSERT INTO building_sources (submission_id, building_id, electricity_heating, electricity_cooling, electricity_other,
                                                                                     naturalGas_heating,  naturalGas_cooling,  naturalGas_other,
                                                                                     fuel_heating,        fuel_cooling,        fuel_other, 
                                                                                     other_heating,     other_cooling,     other_other,
                                                         included)
                           VALUES (".$baselineID.",  ".$Building->getId()
                                                                   .",".$data['electricity']['heating'].", ".$data['electricity']['cooling'].", ".$data['electricity']['other']
                                                                   .",".$data['naturalGas']['heating'].", ".$data['naturalGas']['cooling'].", ".$data['naturalGas']['other']
                                                                   .",".$data['fuel']['heating'].",".$data['fuel']['cooling'].",".$data['fuel']['other']
                                                                   .",".$data['other']['heating'].",".$data['other']['cooling'].",".$data['other']['other']
                                    .", 0)";
                   if(!mysql_query($sql,$database->connection)){
                     throw new Exception(mysql_error());
                   }

                   $data = $Building->getConsumptionBaseline();
                   $sql = "INSERT INTO building_consumption (submission_id, building_id, heating, cooling, other, included)
                           VALUES (".$baselineID.", ".$Building->getId().",".$data['heating'].", ".$data['cooling'].", ".$data['other'].", 0)";

                   if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                   }
                   
                   
                   $sql = "INSERT INTO building_prices (submission_id, building_id, electricity, naturalGas, fuel, other)
                    VALUES (".$CitySubmission->getId().", ".$Building->getId().", '".$Building->getPrices()['electricity']."', '".$Building->getPrices()['naturalGas'].
                            "', '".$Building->getPrices()['fuel']."', '".$Building->getPrices()['other']."')";

                    if(!mysql_query($sql,$database->connection)){
                       throw new Exception(mysql_error());
                    }
                    

                   $sql = "INSERT INTO building_production (submission_id, building_id, production, included)
                           VALUES (".$baselineID.", ".$Building->getId().",".$Building->getProductionBaseline().", 0)";

                   if(!mysql_query($sql,$database->connection)){
                         throw new Exception(mysql_error());
                   }
               }


            }
            else{
                $sql = "UPDATE  building_sources SET included=0 WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }

                $sql = "UPDATE  building_consumption SET included=0 WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }
                
                $sql = "UPDATE  building_prices SET included=0 WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }

                $sql = "UPDATE  building_production SET included=0 WHERE submission_id=".$CitySubmission->getId()." AND building_id=".$Building->getId();
                if(!mysql_query($sql,$database->connection)){
                        throw new Exception(mysql_error());
                }    
            }
        }


        $sql = "UPDATE targets SET consumption = '".$City->getTargets()['consumption']."', emissions = '".$City->getTargets()['emissions']."', "
                                 ."cost = '".$City->getTargets()['cost']."', res = '".$City->getTargets()['res']."' ".                      
               "WHERE city_id =".$City->getId();

        if(!mysql_query($sql,$database->connection)){
                throw new Exception(mysql_error());
        }
    }

    mysql_query("COMMIT");
    
    $_SESSION['City'] = null;
    $_SESSION['CitySubmission'] = null;
    $_SESSION['CityCompare'] = null;
    $_SESSION['CitySubmissionCompare'] = null;

    header('Location: insertComplete.php');
}
catch (Exception $e) {
    mysql_query("ROLLBACK");
    echo $e->getMessage();
    //header('Location: insertFailed.php');
}