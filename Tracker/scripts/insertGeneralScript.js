/* 
 * Giannis Tsapelas 2015
 */

$(document).ready(function(){
    $("#year").on("change", function(){
        $year = $(this).val();
        //alert($year);
        $found = false;
        $('#baseline').find('option').each(function() {
            //alert($(this).val());
            if($(this).val() == $year) $found = true;
        });
        if($found == true){
            $("#newBaseline").hide();
        }
        else{
            $("#newBaseline").show();
        }
        $("#newBaseline").val($year);
        $("#newBaseline").text($year);
        
    });
}); 




$(document).ready(function(){
    $("#country, #cityName").on("change", function(){
        $country = $("#country").val();
        $city = $("#cityName").val();
        
        var xmlhttp;
        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else{// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
                if(xmlhttp.status == 200){
                    location.reload();
                }
                else if(xmlhttp.status == 400) {
                    alert('There was an error 400')
                }
                else {
                    alert('something else other than 200 was returned')
                }
            }
        }

        xmlhttp.open("GET","doFindGeneral.php?country="+$country+"&city="+$city,true);
        xmlhttp.send();
    });
}); 





$(document).ready(function(){
    $("#targetConsumption, #targetEmissions, #targetCost, #targetRes").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if(($(this).val() < 0) || ($(this).val() > 100)){
            alert("Targets must be between 0 and 100 ");
            $(this).val($prev);
        }   
    });
});



$(document).ready(function(){
    $("#priceElectricity, #priceFuel, #priceNaturalGas, #priceOther").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if($(this).val() < 0){
            alert("Prices must be greater than 0");
            $(this).val($prev);
        }   
    });
});


$(document).ready(function(){
    $("#factorElectricity, #factorFuel, #factorNaturalGas, #factorOther").on("focus", function(){
        $prev = $(this).val();
    }).change(function() {
        if($(this).val() < 0){
            alert("Emission factors must be greater than 0");
            $(this).val($prev);
        }   
    });
});