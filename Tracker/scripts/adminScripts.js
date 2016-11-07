function changeType(select){
    var newType = select.value;
    if(newType=="consumption"){
        document.getElementById("costRow").style.display = "none";
        document.getElementById("resRow").style.display = "none";
        document.getElementById("heatingRow").style.display = "table-row";
        document.getElementById("coolingRow").style.display = "table-row";
        document.getElementById("otherRow").style.display = "table-row";
    }
    else if(newType=="production"){
        document.getElementById("heatingRow").style.display = "none";
        document.getElementById("coolingRow").style.display = "none";
        document.getElementById("otherRow").style.display = "none";
        document.getElementById("costRow").style.display = "none";
        document.getElementById("resRow").style.display = "table-row";
    }
    else{
        document.getElementById("heatingRow").style.display = "none";
        document.getElementById("coolingRow").style.display = "none";
        document.getElementById("otherRow").style.display = "none";
        document.getElementById("resRow").style.display = "none";
        document.getElementById("costRow").style.display = "table-row";
    }
}


function editConsumptionAction(id){
    window.location.href = "editActionPlan.php?id="+id+"&type=consumption";
}

function editResAction(id){
    window.location.href = "editActionPlan.php?id="+id+"&type=production";
}
function editCostAction(id){
    window.location.href = "editActionPlan.php?id="+id+"&type=cost";
}




