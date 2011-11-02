<?php
/**

Sounds of Europe

%FILE%		soe_event.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

// echo '<h1>SOE_EVENT.PHP</h1>';
// $tlfn = get_stylesheet_directory() . '/timeline2.html';
// $tl = file_get_contents($tlfn);
// if($tl === FALSE)
// 	echo '<p>Failed to read the content of: '.$tlfn.'<p>';
// echo $tl;

global $wpdb;
$query = "
SELECT * 
FROM ".$wpdb->posts." AS p
WHERE (p.post_type = 'soe_event');";

$events = array();
$result = $wpdb->get_results($query, OBJECT);
foreach($result as $r)
{
	$custom = get_post_custom($r->ID);
	foreach($custom as $key => $val)
	{
		$r->{$key} = $val;
	}
	$events[] = $r;
}


function sortEvent($a, $b)
{
	
	$da = new DateTime($a->event_date_start[0]);
	$db = new DateTime($b->event_date_start[0]);
	if($da == $db)
		return 0;
	return ($da < $db) ? -1 : 1 ;
}

// function eventLength($e)
// {
// 	$da = new DateTime($e->event_date_start[0]);
// 	try
// 	{
// 		$db = new DateTime($e->event_date_end[0]);
// 		//error_log('END_DATE ['.$e->post_title.']: '. $db->format('d.m.y'));
// 		$diff = $da->diff($db);
// 		return $diff;
// 	}
// 	catch (Exception $ex) 
// 	{
// 		//error_log('No end date for: '.$e->post_title);
// 		return new DateInterval();
// 	}
// }


usort($events, 'sortEvent');


$all_start = new DateTime($events[0]->event_date_start[0]);
//error_log('START0: '.$all_start->format('d.m.y'));
$all_last = $all_start;

foreach($events as $event)
{
	$ed = new DateTime($event->event_date_start[0]);
	if($event->event_date_end[0] != '')
		$ed = new DateTime($event->event_date_end[0]);
// 	$intv = eventLength($event);
// 	//error_log('Event ['.$event->post_title.'] lasts: '. $intv->format('%Y %M months'));
// 	//error_log('Event ['.$event->post_title.'] ends: '. $ed->format('d.m.y'));
	if($ed > $all_last)
		$all_last = $ed;
}

$all_interval = $all_start->diff($all_last, true);
$colsCount = (12 * $all_interval->y) + $all_interval->m;

//error_log('START1: '.$all_start->format('d.m.y'));

echo '
<div class="menu_page_nav_box">
<span id="timeline-nav-prev" class="menu_page_nav inactive_nav">← previous</span>
<span id="timeline-nav-next" class="menu_page_nav">next →</span>
</div>
<div id="timeline1">
<table id="timeline_table">
';


$n = new DateTime($all_start->format('r'));
$y = new DateTime($all_start->format('r'));
$mi = new DateInterval('P1M');

echo '<tr>';
$pYear = 0;
$yElems = array();
for($i = 0; $i <= $colsCount; $i++)
{
	$cyear = intval($y->format('Y'));
	if($cyear > $pYear)
	{
		$pYear = $cyear;
		$yElems[$pYear] = 1;
	}
	else
		$yElems[$pYear] += 1;
	$y->add($mi);
}
foreach($yElems as $ty=>$cs)
{
	echo '<td colspan="'.$cs.'" class="year_row">'.$ty.'</td>';
}
echo '</tr>
<tr>
<td class="pattern_row"></td>
</tr>
';

echo '<tr>';
for($i = 0; $i <= $colsCount; $i++)
{
	echo '<td class="month_row">'.$n->format('F').'</td>';
	$n->add($mi);
}
echo '</tr>
<tr>
<td class="pattern_row"></td>
</tr>
';

//error_log('START2: '.$all_start->format('d.m.y'));

$patterns = explode(' ', 'krol wave sinuz curl losange chevron');

foreach($events as $event)
{
	$ed = new DateTime($event->event_date_start[0]);
	$pat = $patterns[mt_rand(0, count($patterns) -1)];
	//error_log($event->post_title .' => '.$all_start->format('d.m.y').s' / '.$ed->format('d.m.y') . ' # '.  $all_start->diff($ed, true)->format('%Y + %M'));
	
	$i0 =  (12 * $all_start->diff($ed, true)->y) + $all_start->diff($ed, true)->m;
	$i1 = (12 * $ed->diff($all_last, true)->y) + $ed->diff($all_last, true)->m;
	
	$p1 = 1;
	if($event->event_date_end[0] != '')
	{
		$de =  new DateTime($event->event_date_end[0]);
		$p1 = (12 * $ed->diff($de, true)->y) + $ed->diff($de, true)->m + 1;
	}
	echo '
	<tr>
	'. ($i0 > 0 ? '<td  class="pattern_row" colspan="'.$i0.'"></td>' : '').'
	<td class="pattern_row '.$pat.'" colspan="'.$p1.'"></td>
	</tr>
	<tr>
	'. ($i0 > 0 ? '<td  class="presentation_row" colspan="'.$i0.'"></td>' : '').'
	<td class="presentation_row" colspan="'.$i1.'"><a href="'.get_permalink($event->ID).'">'.$event->post_title.'</a></td>
	</tr>
	';
	
	
}
echo '</table></div>';


?>