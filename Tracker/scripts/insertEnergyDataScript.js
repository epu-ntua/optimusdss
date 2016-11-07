/* 
 * Giannis Tsapelas 2015
 */

$(document).ready(function(){
    $("#consumptionHeating, #consumptionCooling, #consumptionOther").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if($(this).val() < 0) {
            alert("Consumption must be greater than 0");
            $(this).val($prev);
        }   
    });
});

$(document).ready(function(){
    $("#production").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if($(this).val() < 0) {
            alert("Production must be greater than 0");
            $(this).val($prev);
        }   
    });
});


$(document).ready(function(){
    $("#electricityHeating, #electricityCooling, #electricityOther, #fuelHeating, #fuelCooling, #fuelOther, #naturalGasHeating, #naturalGasCooling, #naturalGasOther, #otherHeating, #otherCooling, #otherOther").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if(($(this).val() < 0) || ($(this).val() > 100)){
            alert("Source usage must be between 0 and 100 ");
            $(this).val($prev);
        }   
    });
});

$(document).ready(function(){
    $('#form').submit(function() {
        $sumHeating = 0;
        $sumCooling = 0;
        $sumOther = 0;
        $sumHeating = parseInt($('#electricityHeating').val()) + parseInt($('#naturalGasHeating').val()) + parseInt($('#fuelHeating').val()) + parseInt($('#otherHeating').val());
        $sumCooling = parseInt($('#electricityCooling').val()) + parseInt($('#naturalGasCooling').val()) + parseInt($('#fuelCooling').val()) + parseInt($('#otherCooling').val());
        $sumOther   = parseInt($('#electricityOther').val()) + parseInt($('#naturalGasOther').val()) + parseInt($('#fuelOther').val()) + parseInt($('#otherOther').val());
        if($sumHeating != 100){
            alert("Total source usage for heating must sum to 100%");
            return false;
        }
        else if ($sumCooling != 100){
            alert("Total source usage for cooling must sum to 100%");
            return false;
        }
        else if ($sumOther != 100){
            alert("Total source usage for other must sum to 100%");
            return false;
        }
        else return true;
    });
});

