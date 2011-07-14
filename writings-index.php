<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<title>SOE - PROJECT</title>
<style media="screen" type="text/css">
@import url("style.css");
@import url("menu_top.css");
@import url("map.css");
@import url("menu_map.css");
@import url("menu_nomap.css");
@import url("content.css");
@import url("writings.css");
</style>


<script type='text/javascript' src='JSON-js/json_parse.js'></script>
<script type='text/javascript' src='js/jquery.js'></script>
<script type='text/javascript' src='js/raphael.js'></script>
<script type='text/javascript' src='js/soe.js'></script>

<script type='text/javascript'>
var theCity = "<?php echo isset($_GET["city"]) ? $_GET["city"]: "berlin" ?>";
</script>

</head>
<body>

<div id="carte">

</div>



<div id="menu_index" class="menu_closed">
	<span>
		<div id="colonne_1" class="index_col">
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">General concept and objectives of the project</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Marcus Writer</a> <span class ="writings_day">July 8th, 2011</span>
				</div>
				<a class="writings_excerpt" href="#">
				In 1973, the Canadian composer and theorist R. Murray Schafer stated that the ʻblurring of the
				edges between music and environmental sounds is the most striking feature of twentieth
				century music.
				</a>
			</span>
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">Definition</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">John Bidule</a>
				</div>
				<a class="writings_excerpt" href="#">
				Certainly the definition of music had been undergoing radical change around
				that same time. In another contemporaneous definition, John Cage declared: “Music is sounds,
				sounds heard around us whether weʼre in or out of concert halls”.
				</a>
			</span>
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">Source and ties</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Wim Nelson</a>
				</div>
				<a class="writings_excerpt" href="#">
				From the technological point of view it was sound recording that had altered the very
				nature of music. With the invention of electroacoustic equipment, music could be detached from
				both its source and its ties. 
				</a>
			</span>
		</div>
	</span>

	<span>
		<div id="colonne_2" class="index_col">	
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">New sound-shaping and space-making devices</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Wallas Broutch</a> 
				</div>
				<a class="writings_excerpt" href="#">
				Since the early seventies,  were being made available, with which new sonic
				worlds could be created, working with the plastic, aesthetic and poetic qualities of sounds. 
				</a>
			</span>
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">Insertions</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Ludi Bird</a> 
				</div>
				<a class="writings_excerpt" href="#">
				Since it became possible to insert any sound from the environment into a musical composition,
				the microphone itself, for many artists, became a musical instrument.
				</a>
			</span>
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">Abstractions and patterns</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Seb Francisco</a> 
				</div>
				<a class="writings_excerpt" href="#">
				The practice of audio and sound recording in the outside world (e.g. in a forest, a pedestrian crossing, or an empty space etc.), as opposed to that which is done within the studio environment, is known as ʻfieldrecordingʼ.
				</a>
			</span>
		</div>
	</span>

	<span>
		<div id="colonne_3" class="index_col">
			<span class="menu_writings_wrapper">
				<div class="writings_titre">
				<a class="menu_writings" href="#">Et tout le reste</a>
				</div>
				<div class="writings_author">
				<a class="menu_writings" href="#">Peter Hinggs</a> 
				</div>
				<a class="writings_excerpt" href="#">
				Field recording has become a medium in a wide range of diverse artistic domains, and is now commonly encountered both as a distinct artistic practice and as a component of music and sound art. The artistic use of field recordings can be numerous
				</a>
			</span>
		</div>
	</span>


</div>

	<div id="menu_navigation">
	<a class="arrows" href="#">&#8592;</a> <a class="arrows" href="#">&#8594;</a>
	</div>
</div><!-- menu_index  -->


<div id="menu_item">
	<span><a href="#" target = "blank">Blog</a></span>
	<span><a href="#" target = "blank">Artists</a></span>
	<span><a href="#" target = "blank">Events</a></span>
	<span><a href="#" target = "blank">Institutions</a></span>
	<span><a href="#" target = "blank">Writings</a></span>
	<span><a href="#" target = "blank">Events</a></span>
	<span><a href="#" target = "blank">About us</a></span>

</div>


</body>
</html>


