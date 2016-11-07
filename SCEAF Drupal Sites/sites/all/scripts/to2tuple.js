var clSize, linSize;
var linLimits = [];
var wid;
var cur;
var resultsDiv = null;

/*initialization method - to be called before any other operation*/
function initialize()
{
//Linguistic initialization
clSize = LinguisticSize;

//create widths and limits for the BLTS
var wid = 1/(clSize-1);
var cur = 0.0;
str = "";
for (var i=0; i<clSize; i++)
	{
	linLimits[i] = cur;
	cur += wid;
	}
linLimits[clSize-1] = 1;

//initialize widths and limits foreach linguistic variable
for (var j=1; j<=NofVariables; j++)    
    if (Variables[j].type == "Linguistic")
        {
        var str = Variables[j].name + ": ";
        var wid2 = 1/(Variables[j].LinguisticSize-1);
        var cur2 = 0.0;
        Variables[j].oldLimits = new Array();
        for (var i=0; i<Variables[j].LinguisticSize-1; i++)
                {
                Variables[j].oldLimits[i] = cur2;
                cur2 += wid2;
                }
        Variables[j].oldLimits[Variables[j].LinguisticSize-1] = 1;
        }
}

/*reurns the ordinal value of the linguistic term*/
function parseLinguistic(variable, lin)
{
for (var i=0; i<variable.LinguisticSize; i++)
	if (lin == variable.LinguisticValues[i])
		return i;
		
return -1;
}

/*creates Point element from coordinates*/
function Point(x,y) {
	this.x = x;
	this.y = y;
}
	
/*creates Range element from limits(high and low)*/
function Range(lo, hi) {
	this.low  = lo;
	this.high = hi;
	
	function lowwer()
	{
	return this.low;
	}
	
	function upper()
	{
	return this.upper;
	}
}

/*creates a Range element parsing limits(high and low) from a string*/
function RangeFromStr(str) {
	vals = str.split('-');
	
	this.low = vals[0];
	this.high = vals[1];
}
	
/*returns the point of intersection between a line and a value*/
function valPoint(x1,y1,x2,y2, val)
{
//calculate line (1) a and b
var a = (y2-y1)/(x2-x1);
var b = y1 - a*x1;
var y = a*val + b;

if ((y>=0)&&(y<=1))
	return new Point(val, y);
else
	return null;
}

/*returns the point of intersection between two lines*/
function cutPoint(x11,y11,x12,y12, x21,y21,x22,y22)
{
//handle special case -- same line
var x, res = -1;

if ((x11==x21)&&(y11==y21))
	{
	x = x11;
	res = y11;
	}
if ((x12==x22)&&(y12==y22)&&(y22>y11))
	{
	x = x12;
	res = y22;
	}
if (res >=0)
	return new Point(x, res);
	
//calculate line (1) a and b
var a1 = (y12-y11)/(x12-x11);
var b1 = y11 - a1*x11;

//calculate line (2) a and b
var a2 = (y22-y21)/(x22-x21);
var b2 = y21 - a2*x21;

//find meet point of the two lines
var x = (b2-b1)/(a1-a2);
var y = a1*x+b1;

if ((x>=0)&&(x<=1)&&(y>=0)&&(y<=1))
	return new Point(x,y);
else
	return null;
}
	
/*creates Vector for a numeric variable*/
function NumericVector(num) {
this.vars = new Array(clSize);
	
this.vars[0] = 0;
for (var i=1; i<clSize; i++) {/*foreach element in the new linguistic scale*/
	if ((linLimits[i]>=num)&&(linLimits[i-1]<=num)) /*if it is between the i-th and the (i-1)-th elements*/
		{
		var val = (num - linLimits[i-1])/(1/(clSize-1)); /*find how close is to the i-th (1 means num == scale[i], 0 means num == scale[i-1])*/
		/*num can be written in this way: num = val*scale[i] + (1-val)*scale[i-1]*/
		
		this.vars[i] = Math.round(val * 100) / 100; /*round to 2 decimal digits*/
		this.vars[i-1] = Math.round((1 - val)*100)/100; /*round again to overcome javascript float limitation*/
		} 
	else
		this.vars[i] = 0;
	}
}

/*creates Vector for a linguistic variable*/
function LinguisticVector(variable, lin) {
this.vars = new Array(clSize);

for (var i=0; i<clSize; i++) /*foreach element in the BLTS*/
	{
	var cp = new Array(4);
	for (var j=0; j<4; j++)	
		cp[j] = null;
	this.vars[i] = 0;
	
	/*find the intersection of the two triangles*/
	/*two triangles intersect - the triangle for the old linguistic value with the triangle for the new one*/
	if (i>0) /*up-line of the new triangle*/
		{
		if (lin>0) /*up-line of the old triangle*/
			cp[0] = cutPoint(linLimits[i-1],0,linLimits[i],1, variable.oldLimits[lin-1],0,variable.oldLimits[lin],1);
		if (lin<variable.LinguisticSize-1) /*down-line line of the old triangle*/
			{
			var linnex = lin*1+1;
			cp[1] = cutPoint(linLimits[i-1],0,linLimits[i],1, variable.oldLimits[lin],1,variable.oldLimits[linnex],0);
			}
		}
	
	if (i<clSize-1) /*down-line line of the new triangle*/
		{
		if (lin>0) /*up-line of the old triangle*/
			cp[2] = cutPoint(linLimits[i],1,linLimits[i+1],0, variable.oldLimits[lin-1],0,variable.oldLimits[lin],1);
		if (lin<variable.LinguisticSize -1) /*down-line line of the old triangle*/
			{
			var linnex = lin*1+1;
			cp[3] = cutPoint(linLimits[i],1,linLimits[i+1],0, variable.oldLimits[lin],1,variable.oldLimits[linnex],0);
			}
		}
	
	/*find the point from those intersections with the greatest value*/
	for (var j=0; j<4; j++)
		if (cp[j])
			if (cp[j].y>this.vars[i])
				this.vars[i] = cp[j].y;
	
	this.vars[i] = Math.round(this.vars[i]*100)/100; /*round to 2 decimal digits*/
	}
}

/*creates Vector for a range variable*/
function RangeVector(p1, p2) {
this.vars = new Array(clSize);

for (var i=0; i<clSize; i++) /*foreach element in the new linguistic scale*/
	{
	if ((linLimits[i]>=p1)&&(linLimits[i]<=p2)) /*range limits contain the whole scale element*/
		this.vars[i] = 1;
	else /*range limits intersect with the scale element's triangle*/
		{
		var cp = new Array(4); /*cut points*/
		this.vars[i] = 0;
		
		/*find all the intersection*/
		if (i>0) /*up-line line of the triangle*/
			{
			cp[0] = valPoint(linLimits[i-1],0,linLimits[i],1, p1); /*with the low  range limit*/
			cp[1] = valPoint(linLimits[i-1],0,linLimits[i],1, p2); /*with the high range limit*/
			}
		if (i<clSize-1) /*down-line line of the triangle*/
			{
			cp[2] = valPoint(linLimits[i],1,linLimits[i+1],0, p1); /*with the low  range limit*/
			cp[3] = valPoint(linLimits[i],1,linLimits[i+1],0, p2); /*with the high range limit*/
			}
			
		/*find the point from those intersections with the greatest value*/
		for (var j=0; j<4; j++)
			if (cp[j])
				if (cp[j].y>this.vars[i])
					this.vars[i] = cp[j].y;
					
		this.vars[i] = Math.round(this.vars[i]*100)/100; /*round to 2 decimal digits*/
		}
	}
	
}

/*create the Interval Vector for all the variable vectors that are given as input*/
function IntervalVector(allVars) {
this.vars = new Array(clSize);
for (i=0; i<clSize; i++) /*foreach element in the new linguistic scale*/
	{
	this.vars[i] = 0;
	var sweight = 0;
	
	for (j=1; j<=NofVariables; j++) /*foreach variable*/
		{
		sweight += Variables[j].weight;
		this.vars[i] += allVars[j].vars[i] * Variables[j].weight; /*add the variable's value for the scale[i] according to its weight*/
		}

	this.vars[i] /= sweight; /*normalize using the total weight of all the variables*/
	this.vars[i] = Math.round(this.vars[i]*100)/100; /*round to 2 decimal digits*/
	}
}

/*returns the numeric value of an interval solution*/
function getTotalValue(interv)
{
var total = 0;
var result = 0;

for (var i=0; i<clSize; i++) /*foreach element in the new linguistic scale*/
	{
	total  += interv.vars[i];
	result += interv.vars[i]*i; /*add i*intervalVector[i] to the total value of the solution*/
	}

result /= total;
return result;
}

/*create the 2-Tuple element for a specific solution*/
function create2Tuple(solution) {
this.solution = solution;
this.allVars = new Array();

for (var i=1; i<=NofVariables; i++) /*foreach variable in the solution*/
	{
        
	/*create its value vector according to its type*/
	if (Variables[i].type === "Numerical")
		this.allVars[i] = new NumericVector(Solutions[solution].values[i]);
	else
	if (Variables[i].type === "Linguistic")
		this.allVars[i] = new LinguisticVector(Variables[i], parseLinguistic(Variables[i], Solutions[solution].values[i]) );
        else
	if (Variables[i].type === "Interval")
		{
		var range = new RangeFromStr(Solutions[solution].values[i]); /*first parse the range limits*/ 
		this.allVars[i] = new RangeVector(range.low, range.high);
		}
	}
	
/*find the interval vector of all the variables*/
this.interval = new IntervalVector(this.allVars);

/*get the interval vector's total value*/
this.totalValue = getTotalValue(this.interval);

/*get the total value's linguistic part*/
this.linguisticValue = Math.round(this.totalValue);

/*get the total value's decimal part*/
this.diffValue = this.totalValue - this.linguisticValue;
this.diffValue = Math.round(this.diffValue*100)/100; /*round to 2 decimal digits*/

/*convert linguistc part from ordinal to string*/
this.linguisticTerm = LinguisticValues[ this.linguisticValue ];
}

function showTables(my2ple)
{
var body = document.getElementById('aggregateDiv');
	
resultsDiv = document.createElement('div');

var title = document.createElement('h3');
title.innerHTML = 'Solution ' + my2ple.solution + ' - ' + Solutions[ my2ple.solution ].name;
resultsDiv.appendChild(title);

var resTableNum = document.createElement('table');
var numtb = '<table class="tftable">';

for (var j=1; j<=NofVariables; j++)
	{
	
	numtb += '<tr><th>' + Variables[j].name + '</th>';
	for (var i=0; i<clSize; i++)
		numtb += '<td>' + my2ple.allVars[j].vars[i] + '</td>';
	numtb += '</tr>';
	}
numtb += '<tr><th class="thfinal">Aggregate</th>';
for (var i=0; i<clSize; i++)
	numtb += '<td>' + my2ple.interval.vars[i] + '</td>';
numtb += '</tr>';
	
numtb += "</table>";
	
resTableNum.innerHTML = numtb;
resTableNum.className = 'tftable';
resultsDiv.appendChild(resTableNum);

var variableCanvas = document.createElement('canvas');
variableCanvas.id = 'variableCanvas_' + my2ple.solution;
variableCanvas.style.maxWidth = "500px";
variableCanvas.style.width = "100%";
var height = 50 + 150*NofVariables;
variableCanvas.style.height = height + "px";
variableCanvas.width = 500;
variableCanvas.height = height;
variableCanvas.style.display = "none";
variableCanvas.style.margin = "0 auto";

resultsDiv.appendChild(variableCanvas);

var graphButton = document.createElement('button');
graphButton.id = 'graphButton_' + my2ple.solution;
graphButton.innerHTML = 'Show graphs';
graphButton.className = 'button showGraph';
graphButton.style.marginLeft = "30px";
graphButton.onclick = clickGraph;

resultsDiv.appendChild(graphButton);

body.appendChild(resultsDiv);

drawVariables(my2ple.solution);
}