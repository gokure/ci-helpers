<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 将参数以 URL 路径的形式拼接并返回字符串
 *
 * @param mixed     $args
 * @return string
 */
if (! function_exists('uri_join'))
{
	function uri_join() {
		$paths = array_flatten(func_get_args());
		$paths = array_map(function($p) { return str_replace('\\', '/', $p); }, $paths); // 转换 "\" 到 "/"
		$paths = array_values($paths);
		$first = current($paths);
		$last = end($paths);
		$prefix = (strlen($first) === 0 OR substr($first, 0, 1) === '/') ? '/' : ''; // 判断是否为绝对路径
		$subfix = (strlen($last) === 0 OR substr($last, -1, 1) === '/') ? '/' : ''; // 判断是否为绝对路径
		$paths = array_map(function($p) { return trim($p, '/'); }, $paths); // 去掉多余的 "/"
		$paths = array_filter($paths); // 去掉空值
		$paths = join('/', $paths);
		return $prefix . $paths . $subfix;
	}
}

/**
 * 用于通过给定的名称生成链接标记
 *
 * @param string    $name
 * @param mixed     $options
 * @param array     $html_options
 * @param object    $block
 * @return string
 */
if ( ! function_exists('link_to'))
{
	function link_to($name, $options = array(), $html_options = array(), $block = NULL)
	{
		$args = func_get_args();
		$block = end($args);
		if (is_callable($block))
		{
			array_pop($args);
			$options = isset($args[0]) ? $args[0] : array();
			$html_options = isset($args[1]) ? $args[1] : NULL;
			$name = call_user_func($block);
			return link_to($name, $options, $html_options);
		}
		else
		{
			$name = isset($args[0]) ? $args[0] : NULL;
			$options = isset($args[1]) ? $args[1] : array();
			$html_options = isset($args[2]) ? $args[2] : NULL;

			$html_options = convert_options_to_data_attributes($options, $html_options);
			$url = site_url($options);

			$tag_options = tag_options($html_options);
			$href = isset($html_options['href']) ? $html_options['href'] : NULL;

			if (! $href)
			{
				$href_attr = 'href="' . html_escape($url) . '"';
			}

			return '<a ' . $href_attr . $tag_options . '>' . (is_null($name) ? html_escape($url) : $name) . '</a>';
		}
	}
}

/**
 * 当条件不成立的前提下，通过给定的名称生成链接标记
 *
 * @param boolean   $condition
 * @param string    $name
 * @param mixed     $options
 * @param array     $html_options
 * @param object    $block
 * @return string
 */
if ( ! function_exists('link_to_unless'))
{
	function link_to_unless($condition, $name, $options = array(), $html_options = array(), $block = NULL)
	{
		$args = func_get_args();
		$block = end($args);
		if (is_callable($block))
		{
			array_pop($args);
			$options = isset($args[2]) ? $args[2] : array();
			$html_options = isset($args[3]) ? $args[3] : array();
		}
		if ($condition)
		{
			if (is_callable($block))
			{
				$func = new ReflectionFunction($block);
				return $func->getNumberOfParameters() <= 1 ? call_user_func($block, $name) : call_user_func_array($block, array($name, $options, $html_options));
			}
			else
			{
				return $name;
			}
		}
		else
		{
			return link_to($name, $options, $html_options);
		}
	}
}

/**
 * 当条件成立的前提下，通过给定的名称生成链接标记
 *
 * @param boolean   $condition
 * @param string    $name
 * @param mixed     $options
 * @param array     $html_options
 * @param object    $block
 * @return string
 */
if ( ! function_exists('link_to_if'))
{
	function link_to_if($condition, $name, $options = array(), $html_options = array(), $block = NULL)
	{
		$args = func_get_args();
		$block = end($args);
		if (is_callable($block))
		{
			array_pop($args);
			$options = isset($args[2]) ? $args[2] : array();
			$html_options = isset($args[3]) ? $args[3] : array();
		}
		return link_to_unless(! $condition, $name, $options, $html_options, $block);
	}
}

if ( ! function_exists('link_to_function'))
{
	function link_to_function($name, $function, $html_options = array())
	{
		$onclick = "{$function}; return false;";
		if (isset($html_options['onclick']))
		{
			$onclick = $html_options['onclick'];
		}
		$href = isset($html_options['href']) ? $html_options['href'] : "#";

		$defaults = array('href' => $href, 'onclick' => $onclick);
		return content_tag('a', $name, array_merge($html_options, $defaults));
	}
}

if ( ! function_exists('convert_options_to_data_attributes'))
{
	function convert_options_to_data_attributes($options, $html_options)
	{
		if ($html_options)
		{
			if (is_link_to_remote_options($options) OR is_link_to_remote_options($html_options))
			{
				$html_options['data-remote'] = 'true';
			}

			$disable_with = array_delete($html_options, 'disable_with');
			$confirm = array_delete($html_options, 'confirm');
			$method = array_delete($html_options, 'method');

			if ($disable_with) $html_options['data-disable-with'] = $disable_with;
			if ($confirm) $html_options['data-confirm'] = $confirm;
			if ($method) {
				if ($method AND strtolower($method) != 'get')
				{
					if (! isset($html_options['rel'])) $html_options['rel'] = '';
					if (! preg_match('/nofollow/', $html_options['rel']))
					{
						$html_options['rel'] = ltrim($html_options['rel'] .' nofollow');
					}
				}
				$html_options['data-method'] = $method;
			}
			return $html_options;
		}
		else
		{
			return is_link_to_remote_options($options) ? array('data-remote' => 'true') : array();
		}
	}
}

if ( ! function_exists('is_link_to_remote_options'))
{
	function is_link_to_remote_options(&$options)
	{
		return is_array($options) AND array_delete($options, 'remote');
	}
}

/* End of file url_helper.php */
