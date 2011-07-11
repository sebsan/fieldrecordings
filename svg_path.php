<?php

/*
	return the p attribute of an svg element in the file passed as argument; 
*/


class SVGPath
{
	public function __construct($file)
	{
		$this->svg = DOMDocument::load($file);
		if($this->svg === FALSE)
			$this->loadStatus = 1;
		else
			$this->loadStatus = 0;
	}
	
	public function getIDs()
	{
		$p = array();
		$status = $this->loadStatus;
		if($this->loadStatus == 0)
		{
			// 			$elem = $this->svg->getElementById ( $id );
			$xpath = new DOMXPath($this->svg);
			$res =  $xpath->query("//*[@d]");
			foreach($res as $elem)
			{
				$p[] = $elem->getAttribute("id");
			}
		}
		return json_encode(array("status" => $status, "p" => $p));
	}
	
	public function getPath($id)
	{
		$p = "";
		$status = $this->loadStatus;
		if($this->loadStatus == 0)
		{
// 			$elem = $this->svg->getElementById ( $id );
			$xpath = new DOMXPath($this->svg);
			$res =  $xpath->query("//*[@id='$id']");
			if($res->length >  0)
			{
				$node = $res->item(0);
				$elem = $node;
				$p = $elem->getAttribute("d");
			}
			else
				$status = 2;
		}
		return json_encode(array("status" => $status , "id"=> $id, "p" => $p));
	}
}

function get($p, $default = "")
{
	$ret = isset($_GET[$p]) ? $_GET[$p] : $default;
	return $ret;
}

$file = get("svg");
$id = get("id");
$p = get("get", NULL);

$sp = new SVGPath($file);
// header('Content-Type: text/javascript; charset=utf8');
header('Content-Type: text/plain; charset=utf8');
// header('Content-Type: application/json; charset=utf8');
// echo '('. $sp->getPath($id) .')';
if($p != NULL)
	echo $sp->getIDs();
else
	echo  $sp->getPath($id) ;

?>