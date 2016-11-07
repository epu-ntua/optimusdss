var vc = 0; //variable counter
var sc = 0; //solution counter

var types = ["Numerical" , "Linguistic", "Interval"];
var errorText = "";
var errorTag = null;

var LinguisticSize;
var LinguisticValues = new Array();
var NofVariables;
var Variables = [];
var NofSolutions;
var Solutions = [];

var all2tuples;

function showError()
{
if (errorTag != null)
	errorTag.innerHTML = errorText;
else
	{
	var mainForm = document.getElementById('mainForm');
	var varTitle = document.getElementById('varTitle');
	errorTag = document.createElement('p');
	errorTag.innerHTML = errorText;
	errorTag.className = "error";
	mainForm.insertBefore(errorTag, varTitle);
	}
}

function findId(name)
{
return name.substr(1, name.indexOf('_')-1);
}

function removeVariable(i)
{
var varDiv = document.getElementById('varDiv');
var remDiv = document.getElementById('C' + i);

varDiv.removeChild(remDiv);
}

function renameVariable(i, j)
{
var varDiv = document.getElementById('varDiv');
var remDiv = document.getElementById('C' + i);
remDiv.id = 'C' + j;
var nameBox = document.getElementById('C' + i + '_name');
nameBox.id = 'C' + j + '_name';
nameBox.name = nameBox.id;
var nameText = document.getElementById('C' + i + '_text');
nameText.id = 'C' + j + '_text';
nameText.innerHTML = "C" + j + " name:";
var typeText = document.getElementById('C' + i + '_typetext');
typeText.id = 'C' + j + '_typetext';
var typeSelect = document.getElementById('C' + i + '_select');
typeSelect.id = 'C' + j + '_select';
typeSelect.name = typeSelect.id;

/*Weight*/
/*
var weightBox = document.getElementById('C' + i + '_weight');
weightBox.id = 'C' + j + '_weight';
weightBox.name = weightBox.id;
var weightText = document.getElementById('C' + i + '_weighttext');
weightText.id = 'C' + j + '_weighttext';
*/

var removeButton = document.getElementById('C' + i + '_remove');
removeButton.id = 'C' + j + '_remove';
}

function onRemoveVariable()
{
if (vc <=1)
	return false;
else
	{
	var remid = findId(this.id);
	
	removeVariable(remid);
	for (var i= remid*1+1; i<=vc; i++)
		renameVariable(i, i-1);

	vc--;
	}
}

function addLinguisticInput(variableDiv)
{
var linId = variableDiv.id + "_linguistic";
if (document.getElementById(linId))
    return; //lignuistic input already exists

var linguisticBox = document.createElement('input');
var linguisticTextContainer = document.createElement('div');
linguisticTextContainer.innerHTML = "Linguistic scale: ";
linguisticTextContainer.className = 'textDiv';
linguisticTextContainer.id = linId + "Text";
linguisticBox.id = linId;
linguisticBox.name = linId;

var removeButton = document.getElementById(variableDiv.id + "_remove");
variableDiv.insertBefore(linguisticBox, removeButton);
variableDiv.insertBefore(linguisticTextContainer, linguisticBox);
}

function removeLinguisticInput(variableDiv)
{
var linId = variableDiv.id + "_linguistic";
var linguisticBox = document.getElementById(linId);
if (linguisticBox) //if the linguistic box exists, it must be removed
    {
    var linguisticTextContainer = document.getElementById(linId + "Text");
    
    variableDiv.removeChild(linguisticBox); 
    variableDiv.removeChild(linguisticTextContainer); 
    }
}

function onTypeChange()
{
if (this.options[this.selectedIndex].value === "Linguistic")
	addLinguisticInput(this.parentNode);
else
	removeLinguisticInput(this.parentNode);
}

function createNewVariable()
{
vc++; // increase variable counter
var varDiv = document.getElementById('varDiv');
var newDiv = document.createElement('div');
newDiv.id = "C" + vc;

var button = document.getElementById('moreVarButton');
var nameBox = document.createElement('input');
var nameTextContainer = document.createElement('div');
nameTextContainer.innerHTML = "C" + vc + " name:";
nameTextContainer.className = 'textDiv';
nameTextContainer.id = "C" + vc + "_text";
nameBox.id = "C" + vc + "_name";
nameBox.name = nameBox.id;

var typeTextContainer = document.createElement('div');
typeTextContainer.className = 'textDiv';
typeTextContainer.innerHTML = "Type:";
typeTextContainer.id = "C" + vc + "_typetext";

var typeSelect = document.createElement('select');
typeSelect.id = "C" + vc + "_select";
typeSelect.name = typeSelect.id;
for (i=0; i<3; i++)
	typeSelect.options[typeSelect.options.length] = new Option(types[i], types[i]);
typeSelect.onchange = onTypeChange;

/*WEIGHT*/
/*
var weightTextContainer = document.createElement('div');
weightTextContainer.className = 'textDiv';
weightTextContainer.innerHTML = "Weight:";
weightTextContainer.id = "C" + vc + "_weighttext";

var weightBox = document.createElement('input');
weightBox.id = "C" + vc + "_weight";
weightBox.name = weightBox.id;
weightBox.value = "1";
*/

var removeButton = document.createElement('input');
removeButton.type = 'button';
removeButton.className = 'removeButton';
removeButton.value = '-';
removeButton.id = "C" + vc + "_remove";
removeButton.name = removeButton.id;
removeButton.onclick = onRemoveVariable;

//putting new elements inside the div
varDiv.insertBefore(newDiv, button);

newDiv.appendChild(nameTextContainer);
newDiv.appendChild(nameBox);
newDiv.appendChild(typeTextContainer);
newDiv.appendChild(typeSelect);
/*WEIGHT*/
/*
newDiv.appendChild(weightTextContainer);
newDiv.appendChild(weightBox);
*/
newDiv.appendChild(removeButton);

nameBox.style.marginTop = "5px";
typeSelect.style.marginTop = "5px";
/*WEIGHT*/
/*
weightBox.style.marginTop = "5px";
weightBox.style.width = "30px";
*/
}

function getSelectionIndex(selection)
{
if (selection == "Numerical")
	return 0;
else
if (selection == "Linguistic")
	return 1;
else
if (selection == "Interval")
	return 2;
else
	return -1;
}

function completeVariable(varid, name, type, weight, linScale)
{
var nameBox = document.getElementById("C" + varid + "_name");
nameBox.value = name;

var typeSelect = document.getElementById("C" + varid + "_select");
typeSelect.selectedIndex = getSelectionIndex(type);
typeSelect.onchange(); 
/*WEIGHT*/
/*
var weightBox = document.getElementById("C" + varid + "_weight");
weightBox.value = weight;
*/
if (type === "Linguistic")
    {
    var linguisticBox = document.getElementById("C" + varid + "_linguistic");
    linguisticBox.value = linScale;  
    }
}

function fillVariableDiv()
{
var varDiv = document.getElementById('varDiv');
var button = document.createElement('input');
button.type = "button";
button.value = "+";
button.id = "moreVarButton";
button.className = "moreButton";
button.onclick = createNewVariable;
varDiv.appendChild(button);

createNewVariable();
}

function completeSolution(solid, name)
{
var nameBox = document.getElementById("A" + solid + "_name");
nameBox.value = name;
}

function createNewSolution()
{
sc++; // increase variable counter
var solDiv = document.getElementById('solDiv');
var button = document.getElementById('moreSolButton');
var newDiv = document.createElement('div');
newDiv.id = "A" + sc;

var nameBox = document.createElement('input');
var nameTextContainer = document.createElement('div');
nameTextContainer.innerHTML = "A" + sc + " name:";
nameTextContainer.id = "A" + sc + "_text";
nameTextContainer.className = 'textDiv';
nameBox.id = "A" + sc + "_name";
nameBox.name = nameBox.id;

var removeButton = document.createElement('input');
removeButton.type = 'button';
removeButton.className = 'removeButton';
removeButton.value = '-';
removeButton.id = "A" + sc + "_remove";
removeButton.name = removeButton.id;
removeButton.onclick = onRemoveSolution;

//putting new elements in the div
newDiv.appendChild(nameTextContainer);
newDiv.appendChild(nameBox);
newDiv.appendChild(removeButton);

solDiv.insertBefore(newDiv, button);

nameBox.style.marginTop = "5px";
}

function fillSolutionDiv()
{
var solDiv = document.getElementById('solDiv');
var button = document.createElement('input');
button.type = "button";
button.value = "+";
button.id = "moreSolButton";
button.className = "moreButton";
button.onclick = createNewSolution;
solDiv.appendChild(button);

createNewSolution();
}

function removeSolution(i)
{
var solDiv = document.getElementById('solDiv');
var remDiv = document.getElementById('A' + i);

solDiv.removeChild(remDiv);
}

function renameSolution(i, j)
{
var solDiv = document.getElementById('solDiv');
var remDiv = document.getElementById('A' + i);
remDiv.id = 'A' + j;
var nameBox = document.getElementById('A' + i + '_name');
nameBox.id = 'A' + j + '_name';
nameBox.name = nameBox.id;
var nameText = document.getElementById('A' + i + '_text');
nameText.id = 'A' + j + '_text';
nameText.innerHTML = "A" + j + " name:";
var removeButton = document.getElementById('A' + i + '_remove');
removeButton.id = 'A' + j + '_remove';
}

function onRemoveSolution()
{
if (sc <=1)
	return false;
else
	{
	var remid = findId(this.id);
	
	removeSolution(remid);
	for (var i= remid*1+1; i<=sc; i++)
		renameSolution(i, i-1);

	sc--;
	}
}

function validateDescription()
{
errorText = "";

for (i=sc; i>=1; i--)
	{
	var nameBox = document.getElementById('A' + i + '_name');
	
	if ((nameBox.value == ""))
		errorText = 'No solution name given for A' + i + '.';
	}
	
for (i=vc; i>=1; i--)
	{
	var nameBox = document.getElementById('C' + i + '_name');
	var typeSelect = document.getElementById('C' + i + '_select');
	/*WEIGHT*/
	var weightBox = document.getElementById('C' + i + '_weight');
	
	var weight = parseInt(weightBox.value);
	
	if (!(weight == weightBox.value))
		errorText = 'Weight for C' + i + ' must be an integer.';
	else
	if (weight <= 0)
		errorText = 'Weight for C' + i + ' must be a positive integer.';
	
	var selOption = typeSelect.options[ typeSelect.selectedIndex ].value;
	if ((selOption != "Numerical")&&(selOption != "Linguistic")&&(selOption != "Interval"))
		errorText = 'Invalid type given for C' + i + '.';
		
	if ((nameBox.value == ""))
		errorText = 'No variable name given for C' + i + '.';
	}

var lingvals = document.getElementById('lingvals').value;
var lingvalsarr = lingvals.split(',');
if (lingvalsarr.length < 2)
		errorText = "At least two linguistic values must be given.";
		
var pname = document.getElementById('pname').value;
if (pname == "")
	errorText = "Give a name for this problem.";
	
if (errorText==="")
	{
	errorTag = null;
	return true;
	}
else
	{
	showError();
	return false;
	}
}

function isValidNumerical(tag, val)
{
var fval = parseFloat(val);

if (!(fval==val))
	return tag + " is not a valid number.";
else
if (fval<0 || fval>1)
	return tag + " must be between 0 and 1.";
else
	return "";
}

function isValidLinguistic(tag, val)
{var found = false;

for (i=0; i<LinguisticSize; i++)
	if (val == LinguisticValues[i])
		{
		found = true;
		break;
		}
		
if (found)
	return "";
else
	return tag + " is not a valid term";
}

function isValidInterval(tag, val)
{
var arr = val.split('-');
if (arr.length != 2)
	return tag + " has invalid format (must be two numbers seperated by a -)";
var a1 = isValidNumerical('', arr[0]);
if (a1 != "")
	return tag + ", low limit: " + a1;
	
var a2 = isValidNumerical('', arr[1]);
if (a2 != "")
	return tag + ", high limit: " + a2;

if (arr[1] < arr[0])
	return tag + ": high limit must be greater than low limit";
	
return "";
}

function validateInputValues()
{
var val;
errorText = "";

for (var j=NofSolutions; j>=1; j--)
	for (var i=NofVariables; i>=1; i--)
		{
		tag = "Variable " + Variables[i].name + " in solution " + Solutions[j].name;
		val = document.getElementById('val_A' + j + '_C' + i).value;
		
		if (Variables[i].type === "Numerical")
			myerr = isValidNumerical(tag, val);
		else
		if (Variables[i].type === "Linguistic")
			myerr = isValidLinguistic(tag, val);
		else
		if (Variables[i].type === "Interval")
			myerr = isValidInterval(tag, val);

		if (myerr != "")
			errorText = myerr;
		}

if (errorText==="")
	{
	errorTag = null;
	return true;
	}
else
	{
	showError();
	return false;
	}
}

function clickGraph()
{
var sol = this.id.substr( this.id.indexOf('_') + 1);

var solCanvas = document.getElementById('variableCanvas_' + sol);
if (solCanvas.style.display == "none")
	{
	this.innerHTML = "Hide graphs";
	this.className = "button hideGraph";
	solCanvas.style.display = "block";
	}
else
	{
	this.innerHTML = "Show graphs";
	this.className = "button showGraph";
	solCanvas.style.display = "none";
	}
}

function create2Tuples()
{
all2tuples = new Array(); /*array with all the 2Tupples for the solutions*/

for (var i=1; i<=NofSolutions; i++) /*foreach solution*/
    {
    all2tuples[i] = new create2Tuple(i); /*call 2Tupple constructor*/
    showTables(all2tuples[i]); /*show 2Tupple fuzzy sets and aggregate*/
    }
}

function sort2Tupples()
{
var swapped;

do
	{
    swapped = false;
    for (var i=1; i<NofSolutions; i++)
        if (all2tuples[i].totalValue < all2tuples[i+1].totalValue)
			{
            var temp = all2tuples[i];
            all2tuples[i] = all2tuples[i+1];
            all2tuples[i+1] = temp;
            swapped = true;
            }
    }
while (swapped);

}

function showAll2Tuples(cnv_id, ommitGraph)
{
var mainForm = document.getElementById('mainForm');

if (ommitGraph) {//total case
	var resTable = document.createElement('table');
	var numtb = '<table class="tftable">';

	for (var i=1; i<=NofSolutions; i++)
		{
		numtb += '<tr><th>' + Solutions[all2tuples[i].solution].name + '</th>';
		numtb += '<td>' + all2tuples[i].linguisticTerm; + '</td>';
		numtb += '<td>' + all2tuples[i].diffValue; + '</td>';
		numtb += '<td><em>' + Math.round(all2tuples[i].totalValue*100)/100; + '</em></td>';
		numtb += '</tr>';
		}

	numtb += '</table>';

	resTable.innerHTML = numtb;
	resTable.className = 'tftable';
}

var prevButton = document.getElementById('prevButton');
	
var resultsCanvas = document.createElement('canvas');
resultsCanvas.id = cnv_id;
resultsCanvas.width  = 400;
if (!ommitGraph)
	resultsCanvas.height = 500; 
else	
	resultsCanvas.height = 200; 
resultsCanvas.style.width  = resultsCanvas.width + 'px';
resultsCanvas.style.height = resultsCanvas.height + 'px';
resultsCanvas.style.display = "block";
resultsCanvas.style.margin = "0 auto";

mainForm.insertBefore(resultsCanvas, prevButton);
if (ommitGraph) {
	mainForm.insertBefore(resTable, resultsCanvas);
}

drawResults(all2tuples, cnv_id, ommitGraph);
}

function printValuesTable(cnv_id) {
var mainForm = document.getElementById('mainForm');

var resTable = document.createElement('table');
var numtb = '<table class="tftable">';

//create header
numtb += '<tr><th>Indicator</th>';
for (var j=1; j<=NofSolutions; j++) {
	numtb += '<th>' + Solutions[j].name + '</th>';
}
numtb += '</tr>';

//add table content
for (var i=1; i<=NofVariables; i++) {
	numtb += '<tr><td style="background: #eee; width: 100px;">' + Variables[i].name + '</td>';
	for (var j=1; j<=NofSolutions; j++) {
		var str = '';
		if (Variables[i].type=="Numerical") {
			str = Math.round(Solutions[j].values[i]*100)/100;
			clr = getSolutionColor(Solutions[j].values[i]);
			clr_txt = Math.round((1-Solutions[j].values[i])*255);
		}
		else {
			str = Solutions[j].values[i];
			lin = parseLinguistic(Variables[i], str);
			clr = getSolutionColor((lin+0.5)/Variables[i].LinguisticSize);
			clr_txt = Math.round((1-((lin+0.5)/Variables[i].LinguisticSize))*255);
		}
		numtb += '<td style="color: rgb(' + clr_txt + ',' + clr_txt + ',' + clr_txt + '); background: ' + clr + ';">' + str + '</td>';
	}
	numtb += '</tr>';
}

numtb += '</table>';

resTable.innerHTML = numtb;
resTable.className = 'tftable';

var cnv = document.getElementById(cnv_id);
mainForm.insertBefore(resTable, cnv);
}

