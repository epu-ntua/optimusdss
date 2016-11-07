<?php

for($y=date("Y")-15; $y <= date("Y"); $y++){
    if($_SESSION['mode'] == "insert"){
       /*  if(!in_array($y, $_SESSION['City']->getBaselineOptions())){ */
            echo '<option ';
            if($baseline == $y){
                echo "selected ";  
            }  
            echo 'value="';
            echo $y;
            echo '">';
            echo $y;
            echo '</option>';
      /*   }  */
      /*  else{
            echo '<option ';
            if($year == $y){
                echo "selected ";  
            }  
            echo 'value="';
            echo $y;
            echo '" disabled>';
            echo $y;
            echo ' - Submited</option>';
          
        }*/
    }
    else{
        echo '<option ';
        if($baseline == $y){
            echo "selected ";  
        }  
        echo 'value="';
        echo $y;
        echo '">';
        echo $y;
        echo '</option>';        
    }
}


