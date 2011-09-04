<?php

/*
	return the p attribute of an svg element in the file passed as argument; 
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
	public function getPath($id)
	{
		$p = "";
		$status = $this->loadStatus;
		if($this->loadStatus == 0)
		{
			$query = sprintf("SELECT * FROM countries WHERE ccode = '%s' ", mysql_real_escape_string($id));
			$result = mysql_query($query, $this->link);
			
			if($result !== FALSE)
			{
				if(mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					$p = $row['svg'];
				}
				else
				{
					$this->loadStatus = 3;
					$p = mysql_num_rows($result);
				}
			}
			else
				$this->loadStatus = 2;
		}
		return json_encode(array("status" =>  $this->loadStatus , "id"=> $id, "p" => $p));
	}
}

function get($p, $default = "")
{
	$ret = isset($_GET[$p]) ? $_GET[$p] : $default;
	return $ret;
}

// $file = get("svg");
$id = get("id");
// $p = get("get", NULL);

$sp = new SVGPath($file);
// header('Content-Type: text/javascript; charset=utf8');
header('Content-Type: text/plain; charset=utf8');
// header('Content-Type: application/json; charset=utf8');
// echo '('. $sp->getPath($id) .')';
// if($p != NULL)
// 	echo $sp->getIDs();
// else
	echo  $sp->getPath($id) ;

?>