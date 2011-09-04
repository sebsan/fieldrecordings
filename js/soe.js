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
	var e = jQuery("#" + id);
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
	var that = jQuery(this);
	var menu = jQuery('#menu_index');
	var id = that.attr('id');
	var callerType = id.split('_').pop();
	if(menu.hasClass('menu_closed'))
	{
		if(menu.hasClass('menu_' + callerType))
		{
			menu.show();
			menu.removeClass('menu_closed');
		}
		else
		{
			menu.removeClass();
			menu.load(rootUrl + callerType,
			function()
			{
				menu.show();
				menu.addClass('menu_' + callerType);
				
			});
		}
	}
	else
	{
		if(menu.hasClass('menu_' + callerType))
		{
			menu.hide();
			menu.addClass('menu_closed');
		}
		else
		{
			menu.removeClass();
			menu.load(rootUrl + callerType,
				  function()
				  {
					  menu.show();
					  menu.addClass('menu_' + callerType);
			
				  });
		}
	}
	return false;
}


function follow(e)
{
	var c = e.children();
	c.each(function()
	{
		follow(jQuery(this));
	});
}

function svgloadComplete(id, maxid)
{
	if(id == maxid)
		follow(jQuery(document));
}


function toggleSats()
{
	var visible = undefined;
	if(jQuery('.visible_loc_obj').length > 0)
		visible = jQuery('.visible_loc_obj').first();
	
	jQuery('.located_object').hide();
	
	var that = jQuery(this);
	var t = undefined;
	var types = new Array('artist','event','organization');
	for(var i = 0; i < types.length; i++)
	{
		var tt = types[i];
		if(that.hasClass('type_' + tt))
		{
			t = tt;
			break;
		}
	}
	if(t != undefined)
	{
		var lo = jQuery('#located_object_' + t);
		if(visible != undefined)
		{
			visible.hide();
			visible.removeClass('visible_loc_obj');
			if(visible.attr('id') != lo.attr('id'))
			{
				lo.addClass('visible_loc_obj');
				lo.show();
			}
		}
		else
		{
			lo.show();
			lo.addClass('visible_loc_obj');
		}
		
		
	}
}


function initSOE()
{
	var ww = jQuery(window).width();
	var wh = jQuery(window).height();
	svgWidth = ww;
	svgHeight = wh;
	raph = Raphael(document.getElementById("carte"), svgWidth, svgHeight );
	var minimap_stroke = new Color(0,0,254);
	var minimap_fill = new Color(0,0,254);
	var country_stroke = new Color(255,0,0);
	
	var bscale = 15;
	var btransx = ww / (2.5 * bscale) ;
	var btransy = 3200 / (3 * bscale) ;

	var scale = 4;
	var trh = ww * 0.85 * (1/scale);
	var trv = 330 * (1/scale);
	// draw circle
	var c = circle(raph, 110);
	c.stroke("red");
	c.translate((ww * 0.8) + 20 , 20);
	c.draw();
	var loc = window.location;

	
	for(var ci = 0; ci < locations.length ; ci++)
	{
		var cloc = locations[ci];
		var city = new circle(raph, 1 / bscale);
		city.scale(bscale).translate(btransx + cloc.lon, btransy + cloc.lat).draw();
		var bb = city.bbox();
		var CurCityClass = "";
		if(cloc.id == theCity)
		{
			// Draw current country (large)
			jQuery.get(templateUrl + "svg_path.php", { id: cloc.country },
					function(data)
					{
						var curCountryData = json_parse(data);
						var curCountryPath = new Path(raph, curCountryData.p);
						curCountryPath.scale(bscale)
						.translate(btransx, btransy)
						.attr("stroke-dasharray", "-")
						.stroke(country_stroke.toString())
						.draw();
						
					});
			CurCityClass = " city_current";
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
		
		var citylink = jQuery('<div class="city_label'
		+ CurCityClass
		+'" style="position:absolute;top:'
		+(bb.y + (bb.height * 2 ))
		+'px;left:'+bb.x+'px;"><a href="'
		+ loc.pathname +'?city='
		+ cloc.id 
		+'">'
		+ cloc.name
		+'</a></div>');
		jQuery('#carte').append(citylink);
		
		if(cloc.id == theCity)
		{
			jQuery.get(templateUrl + "svg_path.php", { id: cloc.country },
				   function(data)
				   {
					   var countryData = json_parse(data);
					   if(countryData.status == 0)
					   {
						   var countryPath = new Path(raph, countryData.p);
						   countryPath.scale(scale)
						   .translate(trh , trv)
						   .fill(minimap_fill.toString())
						   .attr("stroke-width", "0.2")
							.draw();
					   }
				   });
		}
		else
		{
			jQuery.get(templateUrl + "svg_path.php", { id: cloc.country },
					function(data)
					{
						var countryData = json_parse(data);
						if(countryData.status == 0)
						{
							var countryPath = new Path(raph, countryData.p);
							countryPath.scale(scale)
							.translate(trh , trv)
							.stroke(minimap_stroke.toString())
							.attr("stroke-width", "0.2")
							.draw();
						}
					});
		}
		
	}
	// satellites
	jQuery('.located_object').hide();
	jQuery('.located_type_item').click(toggleSats);
	
	/// Menu
	var menuIndex = jQuery('#menu_index');
	menuIndex.hide();
	jQuery('#menu_item span').click(toggleMenu);
	
	
}


jQuery(document).ready(initSOE);
