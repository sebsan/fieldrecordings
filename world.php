<?php
header('Content-Type: image/svg+xml; charset=utf8');
/*
Draw the world
*/

require_once('../../../wp-config.php');

class SVGPath
{
	public function __construct()
	{
		
		$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if (!$link) 
		{
			$this->loadStatus = 1;
		}
		$this->loadStatus = 0;
		mysql_select_db(DB_NAME);
	}
	
	/// $id is a 2 letters country code
	public function getPath()
	{
		echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"  "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" width="2000" height="2000" version="1.1">
';
		$status = $this->loadStatus;
		if($this->loadStatus == 0)
		{
			$result = mysql_query("SELECT * FROM countries", $this->link);
			
			if($result !== FALSE)
			{
				if(mysql_num_rows($result) > 0)
				{
					while ($row = mysql_fetch_assoc($result)) 
					{
						echo '
						<path transform="matrix(4,0,0,4,800,500)" d="'.$row['svg'].'"/>
						';
					}
				}
			}
			else
				$this->loadStatus = 2;
		}
		echo '</svg>';
	}
}


$sp = new SVGPath($file);
$sp->getPath($id) ;

?>