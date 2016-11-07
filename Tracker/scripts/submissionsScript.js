/* 
 * Giannis Tsapelas 2015
 */

$(document).ready(function(){
    $(".table-hover tbody tr").on("click", function(){
        $id = $(this).children('#submissionID').text();
        
        window.location.href = "doGetSubmission.php?sid="+$id+"&city=City&citySubmission=CitySubmission";

    });
}); 


$(document).ready(function(){
    $(".otherSubmissions").on("click", function(){
        $id = $(this).children('#submissionID').text();

        window.location.href = "doGetSubmission.php?sid="+$id+"&city=CityCompare&citySubmission=CitySubmissionCompare";

    });
});