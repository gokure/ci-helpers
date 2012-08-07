<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('image_tag'))
{
	function image_tag($source, $options = array())
	{
		$src = $options['src'] = path_to_image($source);

		if (! isset($options['alt']))
		{
			$options['alt'] = '';
		}

		$options['alt'] = html_escape($options['alt']);

		$size = array_delete($options, 'size');
		if ($size AND preg_match("/^\d+x\d+/", $size))
		{
			list($options['width'], $options['height']) = explode('x', $size);
		}

		if ($mouseover = array_delete($options, 'mouseover'))
		{
			$options['onmouseover'] = "this.src='" . path_to_image($mouseover) . "'";
			$options['onmouseout'] = "this.src='{$src}'";
		}

		return tag('img', $options);
	}
}

if ( ! function_exists('path_to_image'))
{
	function path_to_image($source)
	{
		return comput_public_path($source, 'images');
	}
}

if ( ! function_exists('path_to_css'))
{
	function path_to_css($source)
	{
		return comput_public_path($source, 'css');
	}
}

if ( ! function_exists('path_to_js'))
{
	function path_to_js($source)
	{
		return comput_public_path($source, 'js');
	}
}

if ( ! function_exists('comput_public_path'))
{
	function comput_public_path($source, $dir = '')
	{
		if (FALSE !== strpos($source, '://'))
		{
			return $source;
		}
		else
		{
			if ( ! empty($dir) AND strpos($source, '/') !== 0)
			{
				$source = uri_join($dir, $source);
			}

			return site_url($source);
		}
	}
}

/* End of file asset_tag_helper.php */
