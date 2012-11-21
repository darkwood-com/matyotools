<?php

// For all the fetchin'
require 'basecamphp/basecamp.php';

class BasecampClass extends Basecamp {
	//logs
	const LOG_ITEMS = 3;
	private $logs = null;
	protected $config;
	
	public function __construct($config)
	{
		$this->config = $config;
		
		parent::__construct($config['user'], $config['pass'], $config['base']);
	}
	
	public function implode_array($glue, $marker, $a)
	{
		foreach($a as $key => $value)
		{
			$a[$key] = $key . $marker . $value;
		}
		return implode($glue, $a);
	}
	
	public function serializeArgs($args, $mode)
	{
		$output = '';
		
		if($mode == 'sh')
		{
			$output .= 'basecamp'.(count($args) > 0 ? ' ' : '');
		}
		else if($mode == 'http')
		{
			$output .= '/';
		}
		
		krsort($args);
		if(isset($args['mode']))
		{
			$output .= $args['mode'];
			unset($args['mode']);
		}
		
		foreach($args as $key => $value)
		{
			if(is_numeric($key))
			{
				$args[$key] = $value;
			}
			else
			{
				$args[$key] = $key.'='.$value;
			}
		}

		if($mode == 'sh')
		{
			$output .= (count($args) > 0 ? ' ' : '').implode($args, ' ');
		}
		else if($mode == 'http')
		{
			$output .= (count($args) > 0 ? '/' : '').implode($args, '/').'/';
		}
		
		return $output;
	}
	
	/**
	 * transform array into 
	 */
	public function unserializeArgs($input, $mode)
	{
		$args = array();
		if($mode == 'sh')
		{
			array_shift($input);
		}
		else if($mode == 'http')
		{
			$input = explode('/', substr($input, 1));
		}
		else
		{
			$input = array();
		}
		
		foreach ($input AS $v)
		{
			if (!$v)
			{
				continue;
			}
			// Get vars with foo=bar syntax
			if (preg_match('#^([a-zA-Z0-9_]+?)=(.*?)$#', $v, $m))
			{
				$args[$m[1]] = urldecode($m[2]);
			}
			else if(substr($v, 0, 1) != "?") // Ignore query string
			{
				$args[] = $v;
			}
		}
		
		if(isset($args[0]) && !isset($args['mode']))
		{
			//specific case of the mode input
			$args['mode'] = $args[0];
			unset($args[0]);
		}
		else if(!isset($args['mode']))
		{
			$args['mode'] = 'usage';
		}
		
		return $args;
	}
	
	public function projects()
	{
	  	return $this->hook("/projects.xml","project");
	}
	
	public function me()
	{
		return $this->hook('/me.xml', 'me');
	}
	
	public function logAdd($args)
	{
		$logs = $this->logList();
		
		array_unshift($logs, $args); //insert first
		array_splice($logs, 15); //log up to 10 elements
		
		$logFile = dirname(__FILE__).'/cache/logs.php';
		file_put_contents($logFile, '<?php return (' . var_export($logs, true) . ') ?>');
		@chmod($logFile, 0664);
	}
	
	public function logList()
	{
		$logFile = dirname(__FILE__).'/cache/logs.php';
		$logs = @include($logFile);
		return is_array($logs) ? $logs : array();
	}
	
	private function _cache($params)
	{
		$file = dirname(__FILE__).'/cache/'.md5(serialize($params)).'.php';
		if(file_exists($file)) return @include($file);
		
		$function = $params['function'];
		unset($params['function']);
		$value = call_user_func_array(array($this, $function), $params);
		//print_r($value);
		if (is_object($value))
		{
			$str = addslashes(serialize($value));
			$str = str_replace("\\", "\\\\", $str);
			$str = str_replace("'", "\'", $str);
			
			$content = '<?php return (unserialize(stripslashes(\'' . $str . '\'))) ?>';
		}
		else if (is_array($value))
		{
			$content = '<?php return (' . var_export(array($value, true)) . ') ?>';
		}

		file_put_contents($file, $content);
		@chmod($file, 0664);
		
		return $value;
	}
	
	function hookCache($url, $expected, $post = false, $get = false, $method = false)
	{
		return $this->_cache(array(
			'function' => 'hook',
			'url' => $url,
			'expected' => $expected,
			'post' => $post,
			'get' => $get,
			'method' => $method,
		));
	}
	
	function requestCache($url, $post = false, $get = false, $method = false)
	{
		return $this->_cache(array(
			'function' => 'request',
			'url' => $url,
			'post' => $post,
			'get' => $get,
			'method' => $method,
		));
	}
	
	public function exec($args, $mode)
	{
		$lines = array();
		
		switch($args['mode'])
		{
			case 'projects':
				$projects = $this->projects();
				foreach($projects as $project)
				{
					$lines[] = $project->id.' - '.$project->name;
				}
			break;
			
			case 'entries':
				if(isset($args[1]) && $args[1] == 'create')
				{
					$params = $paramsRequest = array(
						'person-id' => 'me',
						'date' => 'now',
						'hours' => '0',
						'description' => '',
					);
					
					if(isset($args[2], $this->config['project_entries'][$args[2]]))
					{
						$params = array_merge($params, $this->config['project_entries'][$args[2]]);
					}
					$params = array_merge($params, $args);
					
					
					if($params['person-id'] == 'me') $params['person-id'] = $this->me()->id;
					if($params['date'] == 'now') $params['date'] = date('Y-m-d');
					else if(is_numeric($params['date'])) $params['date'] = date('Y-m-d', time() + intval($params['date']) * 3600 * 24);
					
					if(isset($params['todo_item_id']))
					{
						$params['api'] = '/todo_items/'.$params['todo_item_id'].'/time_entries.xml';
					}
					else if(isset($params['project_id']))
					{
						$params['api'] = '/projects/'.$params['project_id'].'/time_entries.xml';
					}
					if(isset($params['api']))
					{
						$paramsRequest = array_intersect_key($params, $paramsRequest);
						$lines[] = 'time add request : ' . $this->implode_array(' ', '=', $paramsRequest);
						$lines[] = $this->request($params['api'], array(
							'time-entry' => $paramsRequest
						));
						$linesAdd = $this->exec(array('mode' => 'entries', 'day' => $params['date']), $mode);
						foreach($linesAdd as $line)
						{
							$lines[] = $line;
						}
					}
					else
					{
						$lines[] = 'new entry : none';
					}
				}
				else if(isset($args[1]) && $args[1] == 'update')
				{
					if(isset($args['id']))
					{
						$data = $this->hook('/time_entries/'.$args['id'].'/edit.xml', 'time-entry');
						$paramsRequest = array();
						foreach($data as $key => $value)
						{
							$paramsRequest[$key] = $value;
						}
						$params = $paramsRequest;
						$params = array_merge($params, $args);
						
						$paramsRequest = array_intersect_key($params, $paramsRequest);
						$lines[] = 'time update request : ' . $this->implode_array(' ', '=', array_merge($paramsRequest, array('id' => $args['id'])));
						$lines[] = $this->request('/time_entries/'.$args['id'].'.xml', array(
							'time-entry' => $paramsRequest
						), false, 'put');
						
						$linesAdd = $this->exec(array('mode' => 'entries', 'day' => $paramsRequest['date']), $mode);
						foreach($linesAdd as $line)
						{
							$lines[] = $line;
						}
					}
					else
					{
						$lines[] = 'update entry : none';
					}
				}
				else if(isset($args[1]) && $args[1] == 'delete')
				{
					if(isset($args['id']))
					{
						$lines[] = 'time delete request : ' . $this->implode_array(' ', '=', array('id' => $args['id']));
						$lines[] = $this->request('/time_entries/'.$args['id'].'.xml', false, false, 'delete');
						$linesAdd = $this->exec(array('mode' => 'entries', 'day' => 'today'), $mode);
						foreach($linesAdd as $line)
						{
							$lines[] = $line;
						}
					}
					else
					{
						$lines[] = 'delete entry : none';
					}
				}
				else if(isset($args['todo_item_id']))
				{
					$lines[] = $this->request('/todo_items/'.$args['todo_item_id'].'/time_entries.xml');
				}
				else if(isset($args['project_id']))
				{
					$lines[] = $this->request('/projects/'.$args['project_id'].'/time_entries.xml');
				}
				else if(isset($args['day']))
				{
					$params = $paramsRequest = array(
						'subject_id' => 'me',
						'from' => $args['day'],
						'to' => $args['day'],
					);
					
					$params = array_merge($params, $args);
					
					if($params['subject_id'] == 'me') $params['subject_id'] = $this->me()->id;
					if($params['day'] == 'today') { $params['from'] = $params['to'] = date('Y-m-d', time()); }
					else if(is_numeric($params['day'])) { $params['from'] = $params['to'] = date('Y-m-d', time() + intval($params['day']) * 3600 * 24); }
					$lines[] = 'time entries list : ' . $this->implode_array(' ', '=', $params);
					
					//list all marked tasks from a specific day
					$paramsRequest = array_intersect_key($params, $paramsRequest);
					$lines[] = $this->request('/time_entries/report.xml', false, $paramsRequest);
				}
				else if(isset($args[1]) && $args[1] == 'log')
				{
					$params = $paramsRequest = array(
						'subject_id' => 'me',
						'from' => -7,
						'to' => 0,
					);
					
					$params = array_merge($params, $args);
					
					if($params['subject_id'] == 'me') $params['subject_id'] = $this->me()->id;
					if(isset($args['from']) && is_numeric($args['from'])) { $params['from'] = intval($args['from']); }
					if(isset($args['to']) && is_numeric($args['to'])) { $params['to'] = intval($args['to']); }
					
					$times = array();
					for($i = $params['from']; $i <= $params['to']; $i++)
					{
						$times[date('Y-m-d', time() + $i * 3600 * 24)] = array('hours' => 0, 'projects' => array());
					}
					$params['from'] = date('Y-m-d', time() + $params['from'] * 3600 * 24);
					$params['to'] = date('Y-m-d', time() + $params['to'] * 3600 * 24);
					
					$paramsRequest = array_intersect_key($params, $paramsRequest);
					$timeEntries = $this->hook('/time_entries/report.xml', 'time-entries', false, $paramsRequest);
					foreach($timeEntries->{'time-entry'} as $timesEntry)
					{
						$times[(string)$timesEntry->{'date'}]['hours'] += floatval((string)$timesEntry->{'hours'});
						$times[(string)$timesEntry->{'date'}]['projects'][(string)$timesEntry->{'project-id'}] = true;
					}
					
					$lines[] = 'log entries list : ' . $this->implode_array(' ', '=', $params);
					foreach($times as $date => $time)
					{
						$lines[] = $date . ' - ' . $time['hours'] . ' : ' . implode(', ', array_keys($time['projects']));
					}
				}
				else 
				{
					$projects = $this->projects();
					foreach($projects as $project)
					{
						$lines[] = $this->request('/projects/'.$project->id.'/time_entries.xml');
					}
				}
			break;
			
			case 'people':
				if(isset($args['person_id']))
				{
					if($args['person_id'] == 'me')
					{
						$lines[] = $this->request('/me.xml');
					}
					else
					{
						$lines[] = $this->request('/people/'.$args['person_id'].'.xml');
					}
				}
				else 
				{
					$lines[] = $this->request('/people.xml');
				}
			break;
			
			default:
			case 'usage':
			case 'help':
				$lines[] = '* help';
				$lines[] = 'help : display this text';
				$lines[] = 'projects : print project list';
				$lines[] = 'entries : retrieve all entries';
				$lines[] = 'people : retrieve all visible people';
				
				$lines[] = '';
				$lines[] = '* Current usages :';
				foreach($this->config['project_entries'] as $project => $values)
				{
					$lines[] = 'basecamp entries create '. $project .' hours=X date=X';
				}
				
				//display cmd log
				$lines[] = '';
				$lines[] = '* logs :';
				foreach($this->logList() as $index => $log)
				{
					$lines[] = '- '.$index.': '.$this->serializeArgs($log, $mode);
				}
			break;
		}
		
		return $lines;
	}
}
?>