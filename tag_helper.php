<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用生成单标签
 * @param string    $name
 * @param array     $options
 * @param boolean   $open
 * @param boolean   $escape
 * @param object    $block
 * @return string
 *
 * tag('br', array('class' => 'newline'));
 *  => <br class="newline" />
 */
if (! function_exists('tag'))
{
	function tag($name, $options = NULL, $open = FALSE, $escape = TRUE)
	{
		$options = tag_options($options, $escape);
		return '<' . $name . $options . ($open ? '>' : ' />');
	}
}

/**
 * 用于生成带有内容的标签，与 :tag 类似
 *
 * @param string    $name
 * @param mixed     $content_or_options_with_block
 * @param array     $options
 * @param boolean   $escape
 * @param object    $block
 * @return string
 *
 * content_tag("span", "显示", array("class" => "foobar"));
 *  => <span class="foobar">显示</span>
 */
if ( ! function_exists('content_tag'))
{
	function content_tag($name, $content_or_options_with_block = NULL, $options = NULL, $escape = TRUE, $block = NULL)
	{
		$args = func_get_args();
		$block = end($args);
		if (is_callable($block))
		{
			if (is_array($content_or_options_with_block))
			{
				$options = $content_or_options_with_block;
			}

			return content_tag($name, call_user_func($block), $options, $escape);
		}
		if ($escape)
		{
			// FIXME implement escaping
			//$content_or_options_with_block = html_escape($content_or_options_with_block);
		}
		$options = tag_options($options);
		return "<{$name}{$options}>{$content_or_options_with_block}</{$name}>";
	}
}

/**
 * 标签选项，用于输出标签属性，见 @tag，@content_tag
 */
if ( ! function_exists('tag_options'))
{
	function tag_options($options, $escape = TRUE)
	{
		if (empty($options)) return '';

		$attrs = array();
		$bool_attributes = array('disabled', 'readonly', 'multiple', 'checked', 'autobuffer',
						   'autoplay', 'controls', 'loop', 'selected', 'hidden', 'scoped', 'async',
						   'defer', 'reversed', 'ismap', 'seemless', 'muted', 'required',
						   'autofocus', 'novalidate', 'formnovalidate', 'open', 'pubdate');
		foreach ($options as $key => $value)
		{
			if ($key == 'data' AND is_array($value))
			{
				foreach($value as $k => $v)
				{
					$k = "data-" . dasherize($k);
					if (! is_string($v))
						$v = json_encode($v);
					$attrs[] = tag_option($k, $v, $escape);
				}
			}
			else if (in_array($key, $bool_attributes))
			{
				if ($value)
					$attrs[] = "{$key}=\"{$key}\"";
			}
			else if ( ! is_null($value))
			{
				$attrs[] = tag_option($key, $value, $escape);
			}
		}

		return empty($attrs) ? NULL : ' ' . implode(' ', $attrs);
	}
}

if ( ! function_exists('tag_option'))
{
	function tag_option($key, $value, $escape)
	{
		if (is_array($value))
		{
			$value = implode(' ', $value);
		}
		if ($escape)
		{
			// TODO implement escaping
			//$value = html_escape($value);
		}
		return "{$key}=\"{$value}\"";
	}
}

/* End of file tag_helper.php */
