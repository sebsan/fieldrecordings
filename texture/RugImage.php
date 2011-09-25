<?php

define("RUGIMAGEDIR", "images/");
define("RUGCACHEDIR", "cache/");
define("RUGMINDIV", 12);
define("RUGMAXDIV", 32);

class Color
{
	public $r;
	public $g;
	public $b;
	public $a;
	
	function __construct($r, $g, $b, $a = 255)
	{
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
		$this->a = $a;
	}
	
	public static function fromInt($rgb)
	{
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		return new Color($r, $g, $b);
	}
}

class Rect
{
	public $top;
	public $left;
	public $width;
	public $height;
	public $right;
	public $bottom;

	function __construct($t, $l, $w, $h)
	{
		$this->top = $t;
		$this->left = $l;
		$this->width = $w;
		$this->height = $h;
		$this->bottom = $t + $h;
		$this->right = $l + $w;
	}

	function Intersect($r)
	{
		return !($r->left > $this->right
			|| $r->right < $this->left
			|| $r->top > $this->bottom
			|| $r->bottom < $this->top);
	}

	function SubRect($r)
	{
		if($this->Intersect($r))
		{
			$st = max($this->top, $r->top);
			$sl = max($this->left, $r->left);
			$sw = ($r->right > $this->right) ? $this->right - $sl : $r->right - $sl;
			$sh = ($r->bottom > $this->bottom) ? $this->bottom - $st : $r->bottom - $st;
			$sr = new Rect($st,$sl,$sw,$sh);
			
			return $sr;
		}
		else
		{
			
			return false;
		}
	}

	function Dump()
	{
		echo '<div>'.$this->top.' '.$this->left.' '.$this->width.' '.$this->height.'</div>';
	}
}

class RugImage
{
	private $VS;	
	private $S;

	function __construct( $vsize)
	{
		$this->VS = $vsize;
	}

	function SplitImage($src)
	{
		echo "INFO: SplitImage: ".$src;
		$s =  getimagesize($src);
		$splitW = 0;
		$splitH = 0;
		for($hdiv = RUGMINDIV; $hdiv <= RUGMAXDIV; $hdiv++)
		{
			if(($s[0] % $hdiv) == 0)
			{
				$splitW = $s[0] / $hdiv;
				break;
			}
		}
		for($vdiv = RUGMINDIV; $vdiv <= RUGMAXDIV; $vdiv++)
		{
			if(($s[1] % $vdiv) == 0)
			{
				$splitH = $s[1] / $vdiv;
				break;
			}
		}
		if(($splitW == 0) || ($splitH == 0))
		{
			echo "<h2>ERROR: Unable to split ".$src."</h2>";
			echo "<h4>Hor. => ".$splitW."</h4>";
			echo "<h4>Ver. => ".$splitH."</h4>";
			return;
		}
		
		$imgsrc = imagecreatefrompng($src);
		if($imgsrc != false)
		{
			
			
				for($x = 0; $x < $s[0]; $x += $splitW)
				{
					for($y = 0; $y < $s[1]; $y += $splitH)
					{
						$dest = imagecreatetruecolor($splitW, $splitH);
						if($dest != false)
						{
							imagesavealpha($dest, true);
							$tcolor = imagecolorallocatealpha($dest,0x00,0x00,0x00,127); 
							imagefill($dest, 0, 0, $tcolor); 
							imagecopyresized($dest,$imgsrc,0,0,$x,$y,$splitW,$splitH,$splitW,$splitH);
							imagepng($dest, RUGIMAGEDIR .'/'.$x.'_'.$y.'_'.$splitW.'_'.$splitH.'.png');
							imagedestroy($dest);
						}
						else
							echo '<p>ERROR: Failed to create an image resource for :'.$x.'_'.$y.'_'.$splitW.'_'.$splitH.'</p>';
					}
				}
			
		}
		else
			echo 'ERROR: Failed to create an image resource from source';
	}

	function CenterOn($center)
	{
		$x = $center['x'];
		$y = $center['y'];
		$ret = RUGCACHEDIR . $this->VS['width'] . '-' . $this->VS['height'] . '_' .$x . '-' . $y . '.png';
		if(file_exists($ret))
		{
			return $ret;
		}
		
		$rx = $x - floor($this->VS['width'] / 2);
		$ry = $y - floor($this->VS['height'] / 2);
		$vr = new Rect($ry, $rx, $this->VS['width'], $this->VS['height']);
		
		$dest = imagecreatetruecolor($this->VS['width'], $this->VS['height']);
		imagesavealpha($dest, true);
		$tcolor = imagecolorallocatealpha($dest,0x00,0x00,0x00,127); 
		imagefill($dest, 0, 0, $tcolor); 
		
		$files = glob(RUGIMAGEDIR . "*.png");
		$ispace = array();
		foreach($files as $f)
		{
			$fn = basename($f, '.png');
			$format = explode('_',$fn);
			$r = new Rect($format[1], $format[0], $format[2], $format[3]);
			$sr = $vr->SubRect($r);
			if($sr != false)
			{
				$i = imagecreatefrompng($f);
			//	imagestring($i, 1, 10,10, $fn .' / '.$sr->left . '..'. $vr->left , imagecolorallocate($i,0,0,0));
				if($i != false)
				{
					imagecopy($dest,$i,
							$sr->left - $vr->left,
							$sr->top - $vr->top,
							abs($r->left - $sr->left),
							abs($r->top - $sr->top),
							$sr->width,
							$sr->height);
				}
				else
					echo '<h1>ERROR:Cannot create: '.$f.'</h1>'; 
			}

		}
		// insert color patches
// 		for($i = 0; $i < $this->VS['height']; $i++)
// 		{
// 			$r = 133 + rand(0,4);
// 			$c = $this->Color($dest, $r, $i);
// 			$ic = imagecolorallocate($dest, $c->r,$c->g,$c->b);
// 			imageline($dest,0 , $i - 6, $r , $i, $ic);
// 		}
		
		imagepng($dest, $ret);
		imagedestroy($dest);
		imagedestroy($ret);
		return $ret;
	}
	
	private function Color($img, $x, $y)
	{
		return Color::fromInt( imagecolorat($img, $x, $y) );

	}
}
