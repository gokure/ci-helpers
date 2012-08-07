<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 生成指定长度的随机字符串
 */
if ( ! function_exists('friendly_token'))
{
	function friendly_token()
	{
		$args = func_get_args();
		$options = array(
			'length' => 8,
			'chars' => array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9))
		);
		if (is_array(end($args)))
		{
			$options = array_merge($options, end($args));
		}
		$length = count($options['chars']);
		$tokens = array();
		for ($i = 0; $i < $options['length']; $i++)
		{
			$tokens[] = $options['chars'][mt_rand(0, $length-1)];
		}
		return implode('', $tokens);
	}
}
/**
 * 返回 HTML 转义后的值，支持数组类型
 */
if ( ! function_exists('html_escape'))
{
	function html_escape($var) {
		if (is_array($var))
		{
			return array_map('html_escape', $var);
		}
		else
		{
			return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
		}
	}
}

/**
 * html_escape 的别名
 */
if ( ! function_exists('h'))
{
	function h($var)
	{
		return html_escape($var);
	}
}

/**
 * 简单的文本格式化，可用于多行文本<textarea>内换行的显示
 */
if ( ! function_exists('simple_format'))
{
	function simple_format($text, $html_options = array(), $options = array())
	{
		$start_tag = tag('p', $html_options, TRUE);
		$text = html_escape($text);
		$text = preg_replace('/\t/', '&nbsp;&nbsp;&nbsp;&nbsp;', $text); // 格式化制表符
		$text = str_replace(' ', '&nbsp;', $text); // 格式化空格
		return $start_tag . nl2br($text) . '</p>';
	}
}

/**
 * 可用于安全输出，当输入的文本为空时，输出第二个参数的内容
 */
if ( ! function_exists('output'))
{
	function output($text, $default = '', $options = array())
	{
		if (is_bool($options))
		{
			$options = array('escape' => $options);
		}
		$options = $options + array('escape' => TRUE, 'before' => '', 'after' => '');
		if ($options['escape'])
		{
			$text = html_escape($text);
			$default = html_escape($default);
		}
		echo (isset($text) AND ! empty($text)) ? $options['before'] . $text . $options['after'] : $default;
	}
}

/**
 * 截取给定长度的文字
 */
if ( ! function_exists('mb_truncate'))
{
	function mb_truncate($text, $options = array())
	{
		if (! is_array($options)) $options = array('length' => $options, 'encode' => 'UTF-8');

		$options = $options + array('length' => 30, 'omission' => '...');
		if (mb_strlen($text, $options['encode']) > $options['length'])
		{
			$text = mb_substr($text, 0, $options['length'], $options['encode']) . $options['omission'];
		}
		return $text;
	}
}

/* End of file text_helper.php */
