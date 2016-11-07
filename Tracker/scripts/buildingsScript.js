/* 
 * Giannis Tsapelas 2015
 */

$(document).ready(function(){
    $("#nextBtn").on("click", function(){
        $foundNoData = false;
        $('.table tbody').find('tr').each(function() {
            //alert($(this).children('td').slice(1, 2).text());
            if($(this).children('td').slice(1, 2).text().indexOf("no data") >= 0){
                $foundNoData = true
            }
                
        });
        if($foundNoData == true){
            alert("You have some incuded buildings without data! Please insert data or exclude them from the submission");
            return false;
        }
    });
}); 
