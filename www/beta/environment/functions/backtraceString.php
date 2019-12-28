<?php
function backtraceString($backtrace = NULL)
{
	if(! isset($backtrace) ) $backtrace = debug_backtrace();
	
	$output = "";
	foreach ($backtrace as $bt)
	{
		$args = '';
		foreach ($bt['args'] as $a)
		{
			if (!empty($args)) 
			{
				$args .= ', ';
			}
			switch (gettype($a))
			{
				case 'integer':
				case 'double':
					$args .= $a;
				break;
				case 'string':
					$a = substr($a, 0, 64) . (strlen($a) > 64) ? '...' : '';
					$args .= "\"$a\"";
				break;
				case 'array':
					$args .= 'Array('.count($a).')';
				break;
				case 'object':
					$args .= 'Object('.get_class($a).')';
				break;
				case 'resource':
					$args .= 'Resource('.strstr($a, '#').')';
				break;
				case 'boolean':
					$args .= $a ? 'True' : 'False';
				break;
				case 'NULL':
					$args .= 'Null';
				break;
				default:
					$args .= 'Unknown';
			}
		}
		$output .= "{$bt['class']}{$bt['type']}{$bt['function']}($args)\n";
		$output .= "{$bt['file']}:{$bt['line']}\n\n";
    }
	
    return $output;
}

?>