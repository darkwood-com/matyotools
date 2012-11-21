<?php
$config = array(
	'user' => 'user',
	'pass' => 'pass',
	'base' => 'base',
);
require 'basecampClass.php';

$from = time() - 7 * 24 * 3600;
$to = time();
$exclude = array();
$mode = isset($_GET['mode']) && in_array($_GET['mode'], array('people', 'projects')) ? $_GET['mode'] : 'people'; //people or projects

$session = new basecampClass($config);

$times = array();
for($i = $from;$i <= $to; $i+= (24 * 3600))
{
	$times[date('Y-m-d', $i)] = 0;
}

$people = array();
foreach($session->hookCache('/people.xml', 'people')->person as $person)
{
	if($person->{'client-id'} == 0 && !in_array((string)$person->id, $exclude))
	{
		$people[(string)$person->id] = array('id' => (string)$person->id, 'name' => utf8_decode((string)$person->{'first-name'}) . ' ' . utf8_decode((string)$person->{'last-name'}), 'times' => $times);
	}
}
	
if($mode == 'people')
{
	foreach($session->hook('/time_entries/report.xml', 'report', false, array('from' => date('Y-m-d', $from), 'to' => date('Y-m-d', $to)))->{'time-entry'} as $time_entry)
	{
		$people[(string)$time_entry->{'person-id'}]['times'][(string)$time_entry->{'date'}] += floatval((string)$time_entry->{'hours'});
	}
	
	echo '<html><body><table cellspacing="5"><tr><th>Personnes</th>';
	foreach($times as $key => $time)
	{
		echo '<th>' . $key . '</th>';
	}
	echo '</tr>';
	foreach($people as $person)
	{
		echo '<tr><td>' . $person['name'] . '</td>';
		foreach($times as $key => $time)
		{
			$timestamp = strtotime($key);
			$day = date('w', $timestamp);
			if($day == '0' || $day == '6')
			{
				$bgcolor = "FFFFFF";
				$color = "000000";
			}
			else
			{
				if($person['times'][$key] < 4)
				{
					$bgcolor = "FF0000";
				}
				else if($person['times'][$key] > 8)
				{
					$bgcolor = "F88017";
				}
				else
				{
					$bgcolor = "57AB27";
				}
				$color = "FFFFFF";
			}
			echo '<td align="center"  style="background-color:#' . $bgcolor . ';color:#' . $color . '">' . $person['times'][$key] . '</td>';
		}
		echo '</tr>';
	}
	echo '</table></body></html>';
}
else
{
	$projects = array();
	foreach($session->hook('/time_entries/report.xml', 'report', false, array('from' => date('Y-m-d', $from), 'to' => date('Y-m-d', $to)))->{'time-entry'} as $time_entry)
	{
		$projectId = (string) $time_entry->{'project-id'};
		$todoItemId = (string) $time_entry->{'todo-item-id'};
		$date = (string) $time_entry->{'date'};
		$personId = (string) $time_entry->{'person-id'};
		$hours = floatval((string)$time_entry->{'hours'});
		
		if(!isset($projects[$projectId]))
		{
			$projects[$projectId] = array(
				'todo_items' => array(),
				'data' => $session->hookCache('/projects/'.$projectId.'.xml', 'project'),
			);
		}
		
		if(!isset($projects[$projectId]['todo_items'][$todoItemId]))
		{
			$projects[$projectId]['todo_items'][$todoItemId] = array(
				'times' => $times,
				'data' => $session->hookCache('/todo_items/'.$todoItemId.'.xml', 'project'),
			);
		}
		
		if(!isset($projects[$projectId]['todo_items'][$todoItemId]['times'][$date][$personId]))
		{
			$projects[$projectId]['todo_items'][$todoItemId]['times'][$date] = array($personId => 0);
		}
		
		$projects[$projectId]['todo_items'][$todoItemId]['times'][$date][$personId] += $hours;
	}
	
	echo '<html><head>
	<style type="text/css">
	table, td, th, tr {
		padding: 0px;
		margin: 0px;
	}
	tr.project td{
		border-top: 1px solid #000000;
	}
	td.hours {
		text-align:center;
	}
	</style>
	</head><body><table cellspacing="5"><tr><th>Projets</th><th>Tache</th>';
	foreach($times as $key => $time)
	{
		echo '<th colspan="2">' . $key . '</th>';
	}
	echo '</tr>';
	foreach($projects as $projectId => $project)
	{
		echo '<tr class="project"><td>' . utf8_decode($project['data']->{'name'}) . '</td><td></td>';
		foreach($times as $key => $time)
		{
			$hours = 0;
			foreach($project['todo_items'] as $todoItemId => $todoItem)
			{
				$peopleArray = is_array($todoItem['times'][$key]) ? $todoItem['times'][$key] : array();
				foreach($peopleArray as $personId => $hour)
				{
					$hours += $hour;
				}
			}
			echo '<td colspan="2" class="hours">'.$hours.'</td>';
		}
		echo '</tr>';
		foreach($project['todo_items'] as $todoItemId => $todoItem)
		{
			echo '<tr class="todo_item"><td></td><td>' . utf8_decode(is_object($todoItem['data']) ? $todoItem['data']->{'content'} : 'Undefined') . '</td>';
			foreach($times as $key => $time)
			{
				$labelPeople = array();
				$labelHours = array();
				$peopleArray = is_array($todoItem['times'][$key]) ? $todoItem['times'][$key] : array();
				foreach($peopleArray as $personId => $hour)
				{
					$labelPeople[] = $people[$personId]['name'];
					$labelHours[] = $hour;
				}
				
				echo '<td>'.implode('<br />', $labelPeople).'</td><td class="hours">'.implode('<br />', $labelHours).'</td>';
			}
			echo '</tr>';
		}
	}
	echo '</table></body></html>';
}
