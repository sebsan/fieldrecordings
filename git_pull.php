<?php
if($_GET['qui'] === "millle")
{
	$out = array();
	exec("git pull", $out);

	foreach($out as $line)
	{
		echo '<p>' . $line . '</p>';
	}
}
else
{
	echo '<h1>Bâtard</h1>';
}

?>