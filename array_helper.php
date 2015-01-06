<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 从数组中删除指定的 key 或 偏移量，然后返回删除的那个值，
 * 当要删除的 key 或 偏移量 不存在时，返回NULL
 *
 * 注意：与方法只使用于数组变量
 */
if ( ! function_exists('array_delete'))
{
	function array_delete(&$array, $offset)
	{
		$value = NULL;
		if (array_key_exists($offset, $array)) {
		{
			$value = $array[$offset];
			unset($array[$offset]);
		}

		return $value;
	}
}

/**
 * 将多维数组一维化
 */
if ( ! function_exists('array_flatten'))
{
	function array_flatten($array)
	{
		$return = array();
		array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });

		return $return;
	}
}

/**
 * 返回只有提供的 key 的数组，这个方法在数据过滤方面很有用
 *
 * $array = array('foo' => '1', 'bar' => '2', 'baz' => '3');
 * array_permit($array, array('foo', 'bar'));
 *  => array('foo' => '1', 'bar' => '2');
 */
if (! function_exists('array_permit'))
{
	function array_permit($array, $permit_keys)
	{
		foreach ($array as $key => $value)
		{
			if ( ! in_array($key, $permit_keys))
			{
				unset($array[$key]);
			}
		}

		return $array;
	}
}

/**
 * 给定的键名都在数组中存在则返回真，否则返回假
 * $array = array('foo' => '1', 'bar' => '2', 'baz' => 3);
 * array_present($array, array('foo', 'bar', 'baz'));
 *  => TRUE
 * array_present($array, array('foobar'));
 *  => FALSE
 */
if ( ! function_exists('array_present'))
{
	function array_present($array, $present_keys)
	{
		$result = TRUE;
		foreach ($present_keys as $key)
		{
			if ( ! array_key_exists($key, $array))
			{
				$result = FALSE;
				break;
			}
		}

		return $result;
	}
}

/**
 * 生成以逗号分割、最后一个以 $and 分割的字符串
 */
if ( ! function_exists('array_to_list'))
{
	function array_to_list($list, $and = 'and', $separator = ', ')
	{
		if (count($list) > 1)
		{
			return implode($separator, array_slice($list, null, -1)) . ' ' . $and . ' ' . array_pop($list);
		}
		else
		{
			return array_pop($list);
		}
	}
}

/**
 * 将对象转换成数组
 *
 * $obj = new stdClass;
 * $obj->id = 101;
 * $obj->name = 'Neo';
 *
 * object_to_array($obj)
 *  =>
 * array('id' => 101, 'name' => 'Neo')
 */
if (! function_exists('object_to_array'))
{
	function object_to_array($data)
	{
		if (is_array($data) OR is_object($data))
		{
			$result = array();
			foreach ($data as $key => $value)
			{
				$result[$key] = call_user_func(__FUNCTION__, $value);
			}
			return $result;
		}
		return $data;
	}
}

/* End of file array_helper.php */
