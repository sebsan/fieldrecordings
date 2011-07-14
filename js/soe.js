/*
A bit of javascript for SOE

requires: jQuery, Raphael

*/

var SOE_debug = false;

var raf = undefined;
var p = undefined;

var map_id = new Array();
var map_path = new Array();


function Point(x, y)
{
	if(x instanceof Point)
	{
		this.x = x.x;
		this.y = x.y;
	}
	else
	{
		this.x = x;
		this.y = y;
	}
}

Point.prototype.scale = function(sx, sy)
{
	var gsy = sx;
	if(sy != undefined)
		gsy = sy;
	this.x = this.x * sx;
	this.y = this.y * gsy;
	return this;
}

Point.prototype.toString = function()
{
	return " [ " +this.x + " ; " +this.y + " ] ";
}

function Color(r, g, b, a)
{
	this.r = r;
	this.g = g;
	this.b = b;
	if(a == undefined)
		this.a = 255;
	else
		this.a = a;
}

Color.prototype.toString = function()
{
	var colS = "rgba("+ this.r +","+ this.g +","+ this.b +","+ this.a +")";
	return colS;
}

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
		path : this._pdata,
		stroke : this._stroke,
		fill : this._fill,
		rotation : this._rotation,
		translation : this._translation,
		scale : this._scale
	});
}

Path.prototype.bbox = function()
{
	return this._path.getBBox();
// 	return (new Point(bb.x, bb.y));
}

Path.prototype.attr = function(key, val)
{
	this._path.attr(key, val);
	return this;
}

Path.prototype.moveTo = function(x,y)
{
	this._pdata += "M "+ x + " " + y;
	return this;
}

Path.prototype.lineTo = function(x,y)
{
	this._pdata += "L "+ x + " " + y;
	return this;
}

Path.prototype.cubicTo = function(cx1,cy1 , cx2,cy2, x,y)
{
	this._pdata += "C " + cx1 + " " + cy1 + " " + cx2 + " " + cy2 + " " + x + " " + y;
	return this;
}

Path.prototype.close = function(x,y)
{
	this._pdata += " z";
	return this;
}

Path.prototype.stroke = function(color)
{
	if(color == undefined)
		return this._stroke;
	else
	{
		this._stroke = color;
	}
	return this;
}

Path.prototype.fill = function(color)
{
	if(color == undefined)
		return this._fill;
	else
	{
		this._fill = color;
	}
	return this;
}

Path.prototype.abs = function()
{
	var bb = this._path.getBBox();
	return this.translate(-bb.x, -bb.y); 
}

Path.prototype.scale = function(sx, sy)
{
	var gsx = sy;
	if(sy == undefined)
		gsx = sx
	this._scale = sx + " " + gsx + " 0 0";
	return this;
}

Path.prototype.translate = function(dx, dy)
{
	this._translation = dx + " " + dy;
	return this;
}

Path.prototype.rotate = function(r)
{
	this._rotation = r;
	return this;
}

Path.prototype.reset = function(p)
{
	if(p == undefined)
		this._pdata = "";
	else
		this._pdata = p;
	
	return this;
}

Path.prototype.draw = function()
{
	this._updateAttrs();
	this._path.show();
	return this;
}

Path.prototype.element = function()
{
	return this._path.node;
}

Path.prototype.contains = function(point)
{
	var bbox = this._path.getBBox();
	if(point.x > bbox.x
		&& point.x < bbox.x + bbox.width
		&& point.y > bbox.y
		&& point.y < bbox.y + bbox.height)
		return true;
	return false;
}


function circle(paper, r)
{
	var c = new Path(paper);
	var r_half = r * 0.5522847498;
	var r_double = r * 2;
	c.moveTo(r, 0);
	c.cubicTo(r + r_half, 0, r_double, r - r_half, r_double, r);
	c.cubicTo(r_double, r + r_half, r + r_half, r_double, r, r_double);
	c.cubicTo(r - r_half, r_double,0, r + r_half, 0, r);
	c.cubicTo(0, r - r_half, r - r_half, 0, r, 0);
	
	return c;
}


function lineConnect(paper, id, x, y)
{
	var e = $("#" + id);
	var o = e.offset();
	var sx = o.left + Math.floor(e.width() / 2);
	var sy = o.top + Math.floor(e.height() / 2);
// 	var l = new Path();
	var lc = new Path(paper).moveTo(sx,sy).lineTo(x,y).draw();
	return lc;
}

function line(paper, p0, p1)
{
	var l = new Path(paper);
	l.moveTo(p0.x,p0.y).lineTo(p1.x,p1.y).draw();
	return l;
}


var svgHeight = 1862;
var svgWidth = 2160;


function toggleMenu()
{
	if($('#menu_index').hasClass('menu_closed'))
	{
		$('#menu_index').show();
		$('#menu_index').removeClass('menu_closed');
	}
	else
	{
		$('#menu_index').hide();
		$('#menu_index').addClass('menu_closed');
	}
	return false;
}


function follow(e)
{
	var c = e.children();
	c.each(function()
	{
		follow($(this));
	});
}

function svgloadComplete(id, maxid)
{
	if(id == maxid)
		follow($(document));
}

function initSOE()
{
	$('#menu_index').hide();
	var ww = $(window).width();
	var wh = $(window).height();
	svgWidth = ww;
	svgHeight = wh;
	raph = Raphael(document.getElementById("carte"), svgWidth, svgHeight );
	var minimap_stroke = new Color(0,0,254);
	var minimap_fill = new Color(0,0,254);
	var country_stroke = new Color(255,0,0);
	
	var bscale = 0.4;
	var btrans = ww / (4 * bscale) ;
// 	var select = "path220";

	var scale = 0.1;
	var trh = ww * 0.8 * (1/scale);
	var trv = 20 * (1/scale);
	// draw circle
	var c = circle(raph, 110);
	c.stroke("red");
	c.translate((ww * 0.8) + 20 , 20);
	c.draw();
	
	var curPoint = undefined;
	if(theCity != undefined)
	{
		$.get("svg_path.php", { svg : "cities.svg", get: 1 },
		function(data)
		{
			var d = json_parse(data);
			for(var ci = 0; ci < d.p.length ; ci++)
			{
				$.get("svg_path.php", { svg : "cities.svg", id: d.p[ci] },
					function(cdata)
					{
						var cd = json_parse(cdata);
						var city = new Path(raph, cd.p);
						var bb0 = city.bbox();
						if(cd.id == theCity)
						{
							city.fill(new Color(0,0,255));
							curPoint = new Point(bb0.x + (bb0.width / 2),bb0.y + (bb0.height / 2));
	// 						    alert('curpoint: ' + curPoint.toString());
							///  We must wait for curPoint to be defined
							var maxID = 1113;
							for(var id = 38; id < maxID; id += 2)
							{
								$.get("svg_path.php", { svg : "europe_simple.svg", id: "path" + id },
									function(gdata)
									{
										var d = json_parse(gdata);
										if(d.status == 0)
										{
											var np = new Path(raph, d.p);
											if(np.contains(curPoint) == true)
											{
												np.close();
												np.fill(minimap_fill.toString());
												
												$.get("svg_path.php", { svg : "europe.svg", id: d.id },
													function(sdata)
													{
														var dd = json_parse(sdata);
														var sp = new Path(raph, dd.p);
														var bb = sp.bbox();
														// 						sp.translate((ww/2) - bb.x - bb.width , (wh/2) - bb.y - bb.height )
														sp.scale(bscale)
														.translate(btrans, 0)
														.attr("stroke-dasharray", "-")
														.stroke(country_stroke.toString())
														.draw();
														
													});
										
											}
											// 				var pp = np.bbox();
											np.scale(scale);
											np.translate(trh , trv);
											np.stroke(minimap_stroke.toString());
											np.attr("stroke-width", "0.2");
											np.draw();
// 											svgloadComplete(d.id, 'path' + (maxID - 1));
										}
									});
						}
						///   
							
						}
						city.scale(bscale).translate(btrans, 0).draw();
// 						var jcity = $(city.element());
// 						jcity.click(function()
						city.element().onclick = function()
						{
							$.get(document.location.href, {city : cd.id});
						};
						var bb = city.bbox();
						if(cd.id == theCity)
						{
							var slidX = bb.x + (bb.width / 2);
							line(raph, new Point(slidX, 0), new Point(slidX, bb.y));
							line(raph, new Point(slidX, svgHeight), new Point(slidX, bb.y));
							var tc = 20;
							var triangle = new Path(raph);
							triangle.moveTo(slidX + (tc /2), 0)
								.lineTo(slidX, tc)
								.lineTo(slidX - (tc /2), 0)
								.close()
								.fill(new Color(0,0,0).toString())
								.draw();
							var triangle1 = new Path(raph);
								triangle1.moveTo(slidX + (tc /2), svgHeight)
								.lineTo(slidX, svgHeight - tc)
								.lineTo(slidX - (tc /2), svgHeight)
								.close()
								.fill(new Color(0,0,0).toString())
								.draw();
						}
	// 					    line(raph, new Point(0 , bb.y), new Point(bb.x , bb.y));
						raph.text(bb.x  , bb.y + (bb.height * 2 ), cd.id);
						
	// 					    alert("Pos: " + d.id);
						
					});
			}
		});
	} 
// 	      return;

// 	var bpoint = new Point(curPoint);
// 	bpoint.scale(bscale);
// 	lineConnect(raph, 'content_outer', bpoint.x, bpoint.y);

	
	
	/// Menu
	$('#menu_item span').click(toggleMenu);
	
	
}


$(document).ready(initSOE);
