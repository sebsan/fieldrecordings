
<html>
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
</style>


<script type='text/javascript' src='JSON-js/json_parse.js'></script>
<script type='text/javascript' src='js/jquery.js'></script>
<script type='text/javascript' src='js/raphael.js'></script>
<script type='text/javascript' src='js/soe.js'></script>

<script type='text/javascript'>
var theCity = "<?php echo $_GET["city"] ?>";
</script>

</head>
<body>

<div id="carte">

</div>

<div id="content_outer">
	<div id="content">
		<div class="content_category">ARTISTS</div>
		<div class="title">Jan Vandam</div>
		<div class="location">Brussels - Belgium</div>
		<div class="picture"><img src="img/BeritGreinke_SHHH_11.jpg" width="80%"></div>
		<div class="section">
			<div class="section_title">Biography</div>
			<div class="section_par">
			Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.
			</div>
			<div class="section_title">Use of fieldrecordings</div>
			<div class="section_par">
			Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
			</div>
		</div>
		<div class="general_url">
			<a href="#">www.janvandam.com</a>
		</div>
		<div class="tags_box">
			<div class="tags">
				<a href="#">tag 1</a>, <a href="#">tag 2</a>, <a href="#">tag 3</a>
			</div>
		</div>

		<div class="artist_track"> Listen to <img src="img/arrow_sound.png"/> Title of Sound </div>
	</div> <!-- content -->
</div> <!-- content_outer  -->

<div id="menu_index" class="menu_closed">
	<span>
		<div id="colonne_1" class="index_col">
			<div class="menu_category">
			Brussels
			</div>
			<a href="#">Berlusconi Claims He Won't Seek Re-Election</a>
			<a href="#">British Gas Announces Energy Price Hike</a>
			<a href="#">Humanitarian Emergency In East Africa</a>
			<a href="#">DEC Launches Appeal As 10m Face Starvation</a>
			<a href="#">Clegg: No Triumphalist Euro Schadenfreude</a>
			<a href="#">Kate Makes Girl's Princess Dream Come True</a>
			<a href="#">From 6am, New Olympic Tickets On Sale</a>
			<a href="#">They've Come To Say Goodbye To Their Childhoods</a>
		</div>
	</span>
	<span>
		<div id="colonne_2" class="index_col">
			<a href="#">Seven People Killed In Michigan Shooting Spree</a>
			<a href="#">Oxbridge: The Entry Gap Highlighted</a>
			<a href="#">Decision On BSkyB Takeover Could Take Weeks After Surge In Online Campaigning</a>
			<a href="#">Police Prepare To Rally Against Cuts As Pay Freeze Looms</a>
			<a href="#">Six-Figure Sum Paid To Met, Claims Evening Standard</a>
			<a href="#">Alarm Over Aircraft Carrier Programme</a>
			<a href="#">Arianna's Big Day In London</a>
			<a href="#">David Laws' Swift Return To Parliament</a>
		</div>
	</span>
	<span>
		<div id="colonne_3" class="index_col">
			<a href="#">News Of The World Editor Arrested As Phone-Hacking Scandal Deepens</a>
			<a href="#">The Newspaper's Closing In Pictures</a>
			<a href="#">Boehner Backs Obama's Call For Far-Reaching Debt Ceiling Plan</a>
			<a href="#">Space Shuttle Prepares For Final Flight</a>
			<a href="#">White House Rules Out Constitutional Option On Debt Ceiling</a>
		</div>
	</span>
	<span>
		<div id="colonne_4" class="index_col">
			<div class="menu_category">
			Madrid
			</div>
			<a href="#">A Complaint From Palin In Newly-Released Email</a>
			<a href="#">New Twist In Casey Anthony Release Date</a>
			<a href="#">Guess Which Bank May Soon Become The Biggest In The U.S.</a>
			<a href="#">House Rejects Effort To Prohibit Funds For U.S. Involvement In Libya</a>
			<a href="#">Michael Bloomberg To Officiate Gay Wedding</a>
			<a href="#">Obama To Unveil Gun Control Reforms</a>
			<a href="#">Federal Prosecutors Take Softer Approach To Corporate Crime</a>
			<a href="#">Montana Risks Losing Federal Education Funding</a>
		</div>
	</span>
	<span>
		<div id="colonne_5" class="index_col">
			<a href="#">Conservative Kingmaker Urges GOP Candidates To Sign Marriage Vow</a>
			<a href="#">House Dems Scramble After Obama Insists On Grand Bargain</a>
			<a href="#">Congressional Black Caucus Members Criticize Obama On Unemployment</a>
			<a href="#">New Twist In Casey Anthony Release Date</a>
			<a href="#">Syria Accuses U.S. Of Inciting Unrest</a>
			<a href="#">Kate Makes Girl's Princess Dream Come True</a>
			<a href="#">David Laws' Swift Return To Parliament</a>
			<a href="#">They've Come To Say Goodbye To Their Childhoods</a>
		</div>
	</span>
	
	<div id="menu_navigation">
		<a href="#">&#8592;</a><a href="#">&#8594;</a>
	</div>
</div><!-- menu_index  -->

<div id="menu_item">
	<span><a href="#" target = "blank">Blog</a></span>
	<span><a href="#" target = "blank">Artists</a></span>
	<span><a href="#" target = "blank">Events</a></span>
	<span><a href="#" target = "blank">Institutions</a></span>
	<span><a href="#" target = "blank">Writings</a></span>
	<span><a href="#" target = "blank">Events</a></span>
</div>








</body>
</html>