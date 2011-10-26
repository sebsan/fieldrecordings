<?php

/*
	return the p attribute of an svg element in the file passed as argument; 
*/

// display errors would break json output and no time for a custom errors handler yet 
// and WP produces some
@require_once('../../../wp-config.php');


class SVGPath
{
	private $SVGToken;
	private $lastToken;
	private $logs;
	public function __construct()
	{
		
		$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if (!$link) 
		{
			$this->loadStatus = 1;
		}
		
		$this->loadStatus = 0;
		mysql_select_db(DB_NAME);
		/// Temporary (to help deploy the new simplification function)
		$query = "CREATE TABLE IF NOT EXISTS `svgsimple` ( `id` int(11) NOT NULL AUTO_INCREMENT, `r` int(11) NOT NULL, `ccode` varchar(2) NOT NULL, `svgsimple` longtext NOT NULL, PRIMARY KEY (`id`) );";
		$res = mysql_query($query, $this->link);
		if($res === FALSE)
			error_log("ERROR creating svgsimple table");
	
		
		$this->SVGToken = array(
			'M' => 2 ,
			'm' => 2,
			'L' => 2,
			'l' => 2,
			'Z' => 0,
			'z' => 0,
			'H' => 1,
			'h' => 1,
			'v' => 1,
			'v' => 1,
			'C' => 6,
			'c' => 6,
			'S' => 4,
			's' => 4);
			
		$this->logs = array();
	}
	// TODO debug this
	private function fPrecision($d, $p = 2)
	{
		$p = min(9, $p);
		if(!is_array($p))
		{
			if(!is_float($d))
				$d = floatval($d);
			return sprintf('%F.'.$p, $d);
		}
		else
		{
			$ret = array();
			foreach($d as $val)
			{
				if(!is_float($val))
					$val = floatval($val);
				$ret[] = sprintf('%.'.$p.'F', $d);
			}
			return $ret;
		}
	}

	
	public function simplifyLinePath($lp , $rf = 50) // line string, percentage of removal
	{
		if($rf < 1)
			return $lp;
		
		$ts = explode(' ', $lp);
		$res = array();
		$lastCur = '';
		$curT = '';
		$toks = array_keys($this->SVGToken);
		
		$subpaths = array();
		$res2 = array();
		$c = count($ts);
		for($i = 0 ; $i < $c; $i++)
		{
			$t = $ts[$i];
// 			if($curT == '' && ($t != 'M' || $t != 'm'))
// 				continue;
			if(in_array($t, $toks))
			{
				$curT = $t;
				$i++;
			}
			if($curT == '')
				continue;
			if($this->SVGToken[$curT] > 0)
				$res2[] = array($curT, array_slice($ts, $i, $this->SVGToken[$curT]));
			else
				$res2[] = array($curT, array());
			$i += $this->SVGToken[$curT] - 1;
			
			if($curT == 'z' || $curT == 'Z')
			{
				$subpaths[] = $res2;
				$res2 = array();
			}
		}
		
		$first = true;
		$NoHarm = array('M','m','Z','z');
		foreach($subpaths as $subpath)
		{
			$kcount = 0;
			$r = $rf / 100 ;
			$rem = 1;
			$lim = $rem / $r;
			while((int)$lim != $lim)
			{
				if($rem >= $rf)
					break;
				$rem++;
				$lim = $rem / $r;
			}
			if(count($subpath) < $rem)
				$rem = 0;
			
			// first must be a M
			if($first === true)
			{
				$first = false;
				$ret = $subpath[0][0]. implode(' ' , $subpath[0][1]);
				$curT = $subpath[0][0];
				array_shift($subpath);
			}
			
			foreach($subpath as $r)
			{
				if($kcount < $rem 
					&& (!in_array($r[0], $NoHarm)))
				{
					$kcount++;
					continue;
				}
				elseif($kcount < $lim)
				{
					$values = $r[1];//$this->fPrecision($r[1], 6);
					if($curT == $r[0])
						$ret .= ' '. implode(',' , $values);
					else
					{
						if(($rt[0] == 'M' || $rt[0] == 'm') && ($rt[0] != 'Z' && $rt[0] != 'z'))
							$ret .= ' Z ';
						$curT = $r[0];
						$ret .= ' '.$r[0]. ' ' . implode(',' , $values);
					}
					$kcount++;
					continue;
				}
				$kcount = 0;
			}
		}
		return $ret;
	}
	
	/// $id is a 2 letters country code
	public function getPath($id, $simplification = 50)
	{
		$p = "";
		$status = $this->loadStatus;
		if($this->loadStatus == 0)
		{
			$testQuery = sprintf("SELECT id FROM svgsimple WHERE `ccode` = '%s' AND `r` = %d ;", mysql_real_escape_string($id), (int)$simplification);
			$testResult = mysql_query($testQuery, $this->link);
			if($testResult !== FALSE && mysql_num_rows($testResult) > 0)
			{
// 				error_log('Got svgsimple for '. $id);
				$query = sprintf("SELECT * FROM svgsimple WHERE `ccode` = '%s'  AND `r` = %d ;", mysql_real_escape_string($id), (int)$simplification);
				$result = mysql_query($query, $this->link);
				
				if($result !== FALSE)
				{
					if(mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_assoc($result);
						$p = $row['svgsimple'];
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
			else
			{
// 				error_log('NO svgsimple for '. $id);
				$query = sprintf("SELECT * FROM countries WHERE `ccode` = '%s' ;", mysql_real_escape_string($id));
				$result = mysql_query($query, $this->link);
				$TS = array();
				$array_dbg = array();
				if($result !== FALSE)
				{
					if(mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_assoc($result);
						
						$p = $this->simplifyLinePath($row['svg'], $simplification);
						$array_dbg[$id] = array(strlen($row['svg']), strlen($p));
						
						$TS[] = sprintf("INSERT DELAYED INTO svgsimple  (`r`, `ccode`, `svgsimple`) VALUES (%d, '%s', '%s'); ", (int)$simplification , mysql_real_escape_string($id), mysql_real_escape_string($p) );
					}
					else
					{
						$this->loadStatus = 3;
						$p = mysql_num_rows($result);
					}
				}
				else
					$this->loadStatus = 2;
				foreach($TS as $T)
				{
					if(mysql_query($T, $this->link) === FALSE)
						error_log('ERROR exec query: '.$T);
				}
				
// 				foreach($array_dbg as $c=>$v)
// 				{
// 					error_log($c.' :'. implode(' / ',$v));
// 				}
// 				foreach($this->logs as $l)
// 				{
// 					error_log($l);
// 				}
			}
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