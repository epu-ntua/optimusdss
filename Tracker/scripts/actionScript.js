/* 
 * Giannis Tsapelas 2015
 */

$(document).ready(function(){
    $(".table-hover tbody tr").on("click", function(){
        if($(this).attr('class') != "selected"){
            $(this).addClass("selected");
            $(this).find("input").prop('checked', true);
        }
        else{
            $(this).removeClass("selected");
            $(this).find("input").prop('checked', false);
        }
    });
}); 
