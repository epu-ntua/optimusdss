var percentColors = [
    { pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
    { pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
    { pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } } ];

function getSolutionColor(pct)
{
for (var i = 0; i < percentColors.length; i++)
    if (pct <= percentColors[i].pct)
		{
        var lower = percentColors[i - 1];
        var upper = percentColors[i];
		try
			{
			var range = upper.pct - lower.pct;
			var rangePct = (pct - lower.pct) / range;
			var pctLower = 1 - rangePct;
			var pctUpper = rangePct;
			var color = 
				{
				r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
				g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
				b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
				};
			
			return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
			}
		catch(err)
			{
			return 'rgb(255,0,0)';
			}
		}
}

function RandomColor(opacity)
{
var r = Math.floor(Math.random() * 255);
var g = Math.floor(Math.random() * 255);
var b = Math.floor(Math.random() * 255);

this.borderColor = 'rgb(' + r + ',' + g + ',' + b + ')'; 
this.fillColor = 'rgba(' + r + ',' + g + ',' + b + ', ' + opacity + ')';
}

function drawRotatedText(ctx, text, x, y, rad)
{
ctx.save();
ctx.translate(x, y);
ctx.rotate(rad);
ctx.fillText(text, 0, 0);
ctx.restore();
}

function drawArrow(ctx, fromx, fromy, tox, toy)
{
var headlen = 10;   // length of head in pixels
var angle = Math.atan2(toy-fromy,tox-fromx);

ctx.beginPath();
ctx.moveTo(fromx, fromy);
ctx.lineTo(tox, toy);
ctx.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
ctx.moveTo(tox, toy);
ctx.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));

ctx.strokeStyle = '#ff0000';
ctx.stroke();
}

function drawVariables(solution)
{
var variableCanvas = document.getElementById("variableCanvas_" + solution);

if (variableCanvas.getContext)
	{
    var ctx = variableCanvas.getContext("2d");
	var ctxW = 500;
	var ctxY = 50 + 150*NofVariables;
	
    ctx.fillStyle = "#F2F2F2";
    ctx.fillRect (0, 0, ctxW, ctxY);
	ctx.lineWidth = 1;
	ctx.strokeStyle = '#6697D0';
	ctx.strokeRect(0, 0, ctxW, ctxY);
	
	ctx.fillStyle = "black";
	ctx.font = "bold 16px Open Sans";
	ctx.fillText("Variables for " + Solutions[solution].name, ctxW/2-100, 15);
	
	var xStart = 15;
	var xEnd = ctxW-40;
	var yStart = top+30;
	var yEnd = bottom-41;
	var gWidth = xEnd-xStart;
	var gHeight = yEnd-yStart;
	var trWidth = gWidth*1/clSize;
	
	for (var i=1; i<=NofVariables; i++)
		{
		var bottom = 50 + 150*i;
		var top = 50 + 150*(i-1);
		
		ctx.fillStyle = "black";
		ctx.font = "12px bold Open Sans";
		ctx.fillText(Variables[i].name, ctxW/2-80, top);
	
		drawArrow(ctx, 10, bottom-40, ctxW-20, bottom-40);
		drawArrow(ctx, 10, bottom-40, 10, top+30);
		
		ctx.font = "10px Open Sans";
		var linlist = LinguisticValues;
		
		xStart = 15;
		xEnd = ctxW-40;
		yStart = top+30;
		yEnd = bottom-41;
		gWidth = xEnd-xStart;
		gHeight = yEnd-yStart;
		trWidth = gWidth*1/(clSize-1);
		
		for (lim=0; lim<clSize; lim++) //show background triangles
			{	
			if (lim %2 === 0)
				ctx.strokeStyle="rgb(102, 151, 208)";
			else
				ctx.strokeStyle="rgb(95, 135, 180)";
				
			ctx.beginPath();
			if (lim>0)
				{
				ctx.moveTo(xStart + (lim-1)*trWidth, yEnd);
				ctx.lineTo(xStart + lim*trWidth, yStart);
				}
			else
				ctx.moveTo(xStart + lim*trWidth, yStart);
			if (lim+1<clSize)
				ctx.lineTo(xStart + (lim+1)*trWidth, yEnd);
			ctx.stroke();
			//xp = xStart + gWidth*lim/clSize;
			//alert(lim+': '+xp);
			
			ctx.fillText(linlist[lim], xStart + lim*trWidth - 5, bottom-30);
			}
		
		//show value of the variable
		if (Variables[i].type == "Numerical")
			{
			xpos = xStart + gWidth*Solutions[solution].values[i];
			
			ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
			ctx.lineWidth = 2;
			ctx.beginPath();
			ctx.moveTo(xpos, yStart);
			ctx.lineTo(xpos, yEnd);
			ctx.stroke();
			ctx.lineWidth = 1;
			
			ctx.fillText(Solutions[solution].values[i], xpos, bottom-20);
			}
		else
		if (Variables[i].type == "Linguistic")
			{
			lin = parseLinguistic(Variables[i], Solutions[solution].values[i]);
			ltrWidth = gWidth*1/(Variables[i].LinguisticSize-1);

			//stroke the perimetre of the triangle
			ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
			ctx.beginPath();
			if (lin>0)
				{
				ctx.moveTo(xStart + (lin-1)*ltrWidth, yEnd);
				ctx.lineTo(xStart + lin*ltrWidth, yStart);
				}
			else
				ctx.moveTo(xStart + lin*ltrWidth, yStart);
			if (lin+1<Variables[i].LinguisticSize)
				ctx.lineTo(xStart + (lin+1)*ltrWidth, yEnd);
			ctx.stroke();
			
			//fill the inside
			ctx.fillStyle = 'rgba(200, 200, 0, 0.7)';
			ctx.beginPath();
			if (lin>0)
				ctx.moveTo(xStart + (lin-1)*ltrWidth, yEnd);
			else
				ctx.moveTo(xStart + lin*ltrWidth, yEnd);
			ctx.lineTo(xStart + lin*ltrWidth, yStart);
			if (lin+1<Variables[i].LinguisticSize)
				ctx.lineTo(xStart + (lin+1)*ltrWidth, yEnd);
			else
				ctx.lineTo(xStart + lin*ltrWidth, yEnd);
			ctx.closePath();
			ctx.fill();
			
			ctx.fillStyle = 'black';
			ctx.fillText(Solutions[solution].values[i], xStart + lin*ltrWidth - 5, bottom-10);
			}
		else
		if (Variables[i].type == "Interval")
			{
			var range = new RangeFromStr(Solutions[solution].values[i]);
			//draw low limit
			xpos1 = xStart + gWidth*range.low;
			
			ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
			ctx.beginPath();
			ctx.moveTo(xpos1, yStart);
			ctx.lineTo(xpos1, yEnd);
			ctx.stroke();
			
			ctx.strokeStyle = 'black';
			ctx.fillText(range.low, xpos1 - 20, bottom-20);
			
			//draw high limit
			xpos2 = xStart + gWidth*range.high;
			
			ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
			ctx.beginPath();
			ctx.moveTo(xpos2, yStart);
			ctx.lineTo(xpos2, yEnd);
			ctx.stroke();
			
			ctx.strokeStyle = 'black';
			ctx.fillText(range.high, xpos2, bottom-20);
			
			//fill the area
			ctx.fillStyle = 'rgba(200, 200, 0, 0.7)';
			ctx.fillRect(xpos1, yStart, xpos2-xpos1, yEnd-yStart);
			}
		}
	}
}

function getPercentage(solution, variable)
{
if (Variables[variable].type == "Numerical")
	return Solutions[solution].values[variable];
else
if (Variables[variable].type == "Linguistic")
	{
	var lin = parseLinguistic(Variables[variable], Solutions[solution].values[variable]);
	if (lin < 0)
		return 0;
	return lin/(Variables[variable].LinguisticSize-1);
	}
else
if (Variables[variable].type == "Interval")
	{
	var intv = new RangeFromStr(Solutions[solution].values[variable]);
	return intv.low/2+intv.high/2;
	}
else
	return 0;
}

function drawResults(all2tuples, cnv_id, ommitGraph)
{
var resultsCanvas = document.getElementById(cnv_id);

if (resultsCanvas.getContext)
	{
    var ctx = resultsCanvas.getContext("2d");
	var ctxW = 400;
	var ctxY = 500;
	
    ctx.fillStyle = "#F2F2F2";
    ctx.fillRect (0, 0, ctxW, ctxY);
	ctx.lineWidth = 1;
	ctx.strokeStyle = '#6697D0';
	ctx.strokeRect(0, 0, ctxW, ctxY);
		
	ctx.fillStyle = "black";
	ctx.font = "bold 16px Open Sans";
	ctx.fillText("Solutions", ctxW/2-50, 15);
	ctx.font = "12px Open Sans";
	ctx.fillText("sorted according to their performance", 60, 30);
	ctx.fillText("in " + ProblemName, 60, 48);
	
	drawArrow(ctx, 10, 100, ctxW-30, 100);
	
	ctx.font = "10px Open Sans";
	
	var yOffset = 6;
	var angle = 1;
	
	var maxx = -1, maxsol = -1;
	
	for (var i=1; i<=NofSolutions; i++) //show all the solutions on the axis
		{
		var x = 10 + all2tuples[i].totalValue*(ctxW-60)/clSize;
		
		if (x > maxx)
			{
			maxx = x;
			maxsol = all2tuples[i].solution;
			}
			
		yOffset = -1*yOffset;
		angle *= -1;
		
		ctx.beginPath();
		ctx.arc(x, 100, 3, 0, 2 * Math.PI, false);
		ctx.fillStyle = getSolutionColor(all2tuples[i].totalValue/clSize);
		ctx.fill();
		ctx.lineWidth = 1;
		ctx.strokeStyle = 'black';
		ctx.stroke();
		
		ctx.fillStyle = "black";
		drawRotatedText(ctx, Solutions[all2tuples[i].solution].name, x, 100 + yOffset, angle*Math.PI/6);
		}
		
	if (maxsol >= 0) //print the best solution
		{
		ctx.font = "14px Open Sans";
		ctx.fillText("Best case: " + Solutions[maxsol].name, 60, 170);
		}
		
	if (ommitGraph) {
		return;
	}
	
	var polX, polY; 
	polX = 0;	
	polY = 200;
	var ctrX = polX + 150;
	var ctrY = polY + 150;
	var polR = 100;
	var txtR = 110;
	var f = 0;
	
	//Draw the basic polygon (border)
	ctx.font = "8px Open Sans";
	ctx.strokeStyle = 'rgb(0, 0, 0)';
	ctx.lineWidth = 1;
	ctx.beginPath();
			
	for (var variable=1; variable<=NofVariables; variable++)
		{
		f = ((variable-1)/NofVariables)*2*Math.PI;
		var pvarX = ctrX + polR*Math.sin(f);
		var pvarY = ctrY - polR*Math.cos(f);
			
		ctx.lineTo(pvarX, pvarY);
		//also print variable name
		var ptxtX = ctrX + txtR*Math.sin(f);
		var ptxtY = ctrY - txtR*Math.cos(f);
		if (f>Math.PI)
			{
			ptxtX -= 20;
			ptxtY += 10;
			}
			
		ctx.fillStyle = 'rgba(0, 0, 0)';
		ctx.fillText(Variables[variable].name, ptxtX, ptxtY);
		}
	ctx.closePath();
	ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
	ctx.stroke();
	ctx.fill();
	
	/*Draw the polygon foreach solutiom*/
	for (var solution=1; solution<=NofSolutions; solution++)
		{
		//Pick a random color
		var clr = Solutions[solution].color;
		
		//Output the solution name
		ctx.font = "12px Open Sans";
		ctx.fillStyle = 'rgb(0, 0, 0)';
		ctx.fillText(Solutions[solution].name, polX + 240, polY + 20*solution - 25);
		
		ctx.strokeStyle = clr.borderColor;
		ctx.fillStyle = clr.fillColor;
		ctx.lineWidth = 1;
		
		//Add the index
		ctx.fillRect(polX + 200, polY + 20*solution - 40, 30,15);
		ctx.strokeRect(polX + 200, polY + 20*solution - 40, 30,15);
		//ctx.fill();
		
		//Draw the polygon
		ctx.beginPath();
			
		for (var variable=1; variable<=NofVariables; variable++)
			{
			f = ((variable-1)/NofVariables)*2*Math.PI;

			var valR = 1 + (polR-1)*getPercentage(solution, variable);
			var pvarX = ctrX + valR*Math.sin(f);
			var pvarY = ctrY - valR*Math.cos(f);
			
			ctx.lineTo(pvarX, pvarY);
			}
			
		ctx.closePath();
		ctx.stroke();
		ctx.fill();
		}
    }
}
