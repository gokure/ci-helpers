<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 返回指定长度小数位的数值，正数时返回值大于当前值，负数时返回值小于当前值
 * round_up(56.77001, 2); //=> 56.78
 * round_up(-0.453001, 4); //=> -0.4531
 */
if ( ! function_exists('round_up'))
{
	function round_up($value, $places = 0)
	{
		if ($places < 0)
		{
			$places = 0;
		}
		$mult = pow(10, $places);

		return ($value >= 0 ? ceil($value * $mult) : floor($value * $mult)) / $mult;
	}
}

/**
 * 返回指定长度小数位的数值，正数时返回值小于当前值，负数时返回值大于当前值
 * round_down(56.77001, 2); //=> 56.77
 * round_down(-0.453001, 4); //=> -0.453
 */
if ( ! function_exists('round_down'))
{
	function round_down($value, $places = 0)
	{
		if ($places < 0)
		{
			$places = 0;
		}
		$mult = pow(10, $places);

		return ($value < 0 ? ceil($value * $mult) : floor($value * $mult)) / $mult;
	}
}

/* End of file number_helper.php */
