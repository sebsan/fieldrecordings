/*
A bit of javascript for SOE

requires: jQuery, Raphael

*/

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
	this._fill = "#fff";
	this._path =  R.path(this._pdata).attr(
	{
		stroke : this._stroke,
		fill : this._fill,
		path : this._pdata
	});
	this._path.hide();
}

Path.prototype._updateAttrs = function()
{
	this._path.attr(
	{
		stroke : this._stroke,
		fill : this._fill,
		path : this._pdata
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
	var i = 200;
	for(var id = 4051; id < 5126; id += 2)
	{
		$.get("http://localhost/~pierre/fieldrecordings/svg_path.php", { svg : "europe.svg", id: "path" + id },
		      function(data)
		      {
			      try
			      {
// 			      var d = $.parseJSON(data);
		var d = json_parse(data);
			      var idx = map_path.push(new Path(raph, d.p));
			      map_path[idx - 1].stroke("rgb("+i+","+i+","+i+")");
			      i--;
			      if(i == 1)
				      i = 200;
			      map_path[idx - 1].draw();
			      }
			      catch(e)
			      {
				      alert("ouch: <" + data +">");
			      }
		      });
	}
	
}


$(document).ready(initSOE);
