/*
A bit of javascript for SOE

requires: jQuery, Raphael

*/

var SOE_debug = false;

var raf = undefined;
var p = undefined;

var map_id = new Array();
var map_path = new Array();

function Path(R, p)
{
	this._pdata = "";
	if(p != undefined)
		this._pdata = p;
	
	this._stroke = "#000";
	this._fill = "transparent";
	this._path =  R.path(this._pdata).attr(
	{
		stroke : this._stroke,
		fill : this._fill,
		path : this._pdata
	});
	this._path.hide();
	this._scale = "1 1";
	this._rotation = "0";
	this._translation = "0 0";
}

Path.prototype._updateAttrs = function()
{
	this._path.attr(
	{
		stroke : this._stroke,
		fill : this._fill,
		path : this._pdata,
		rotation : this._rotation,
		translation : this._translation,
		scale : this._scale
	});
}

Path.prototype.moveTo = function(x,y)
{
	this._pdata += "M "+ x + " " + y;
	this._updateAttrs();
	return this;
}

Path.prototype.lineTo = function(x,y)
{
	this._pdata += "L "+ x + " " + y;
	this._updateAttrs();
	return this;
}

Path.prototype.cubicTo = function(cx1,cy1 , cx2,cy2, x,y)
{
	this._pdata += "C " + cx1 + " " + cy1 + " " + cx2 + " " + cy2 + " " + x + " " + y;
	this._updateAttrs();
	return this;
}

Path.prototype.close = function(x,y)
{
	this._pdata += " z";
	this._updateAttrs();
	return this;
}

Path.prototype.stroke = function(color)
{
	if(color == undefined)
		return this._stroke;
	else
	{
		this._stroke = color;
		this._updateAttrs();
	}
}

Path.prototype.fill = function(color)
{
	if(color == undefined)
		return this._fill;
	else
	{
		this._fill = color;
		this._updateAttrs();
	}
}

Path.prototype.scale = function(sx, sy)
{
	var gsx = sy;
	if(sy == undefined)
		gsx = sx
	this._scale = sx + " " + gsx;
	this._updateAttrs();
}

Path.prototype.translate = function(dx, dy)
{
	this._translation = dx + " " + dy;
	this._updateAttrs();
}

Path.prototype.rotate = function(r)
{
	this._rotation = r;
	this._updateAttrs();
}

Path.prototype.reset = function(p)
{
	if(p == undefined)
		this._pdata = "";
	else
		this._pdata = p;
	
	this._updateAttrs();
}

Path.prototype.draw = function()
{
	this._path.show();
}

Path.prototype.element = function()
{
	return this._path.node;
}


function initSOE()
{
	raph = Raphael(document.getElementById("carte"), 1000,1000 );
// 	raph.safari();
	var line = new Path(raph);
	var content = $('#content_outer');
	if(curPoint.x < ($(window).width() / 2))
	{
		var leftVal = (($(window).width() / 2) + 28) + "px";
// 		alert(leftVal);
		content.css("left", leftVal);
		line.moveTo(content.offset().left, content.offset().top )
	}
	else
		line.moveTo(content.offset().left + content.width(), content.offset().top);
	
	line.lineTo(curPoint.x, curPoint.y);
	line.draw();
	
	
	var i = 1;
	for(var id = 4051; id < 5126; id += 2)
	{
		$.get("svg_path.php", { svg : "europe.svg", id: "path" + id },
		      function(data)
		      {
			      try
			      {
// 			      var d = $.parseJSON(data);
				var d = json_parse(data);
				var idx = map_path.push(new Path(raph, d.p));
// 				map_path[idx - 1].scale(i);
// 				i -= 0.01;
				map_path[idx - 1].draw();
			      }
			      catch(e)
			      {
				      if(SOE_debug == true)
					alert("ouch: Unable to parse JSON data");
			      }
		      });
	}
	
}


$(document).ready(initSOE);
