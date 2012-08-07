<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用替换短线替换下划线
 *  => "puni_puni" // => "puni-puni"
 */
if (! function_exists('dasherize'))
{
	function dasherize($str)
	{
		return str_replace('_', '-', $str);
	}
}

/* End of file inflector_helper.php */
