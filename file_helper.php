<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 返回路径格式的字符串
 * file_join("usr", "mail", "gumby")   #=> "usr/mail/gumby"
 */
if ( ! function_exists('file_join'))
{
	function file_join()
	{
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
 * 判断是否为图片
 */
if ( ! function_exists('is_image'))
{
	function is_image($mime_type)
	{
		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($mime_type, $png_mimes))
		{
			$mime_type = 'image/png';
		}

		if (in_array($mime_type, $jpeg_mimes))
		{
			$mime_type = 'image/jpeg';
		}

		$img_mimes = array(
			'image/gif',
			'image/jpeg',
			'image/png',
		);

		return in_array($mime_type, $img_mimes, TRUE);
	}
}

/**
 * 发送数据流
 *
 * @param resource  $data
 * @param array     $options
 * @return string
 * @throws InvalidArgumentException
 */
if ( ! function_exists('send_data'))
{
	function send_data($data, $options = array())
	{
		_send_file_header($options);
		echo $data;
	}
}

/**
 * 复制文件，当目标文件存在时自动重命名，
 * 成功返回目标地址，目标地址为文件或复制失败时返回 FALSE
 *
 * @param string    $src
 * @param string    $dst
 * @return string|boolean
 */
if ( ! function_exists('copy_file'))
{
	function copy_file($src, $dst)
	{
		$result = FALSE;
		$path = @pathinfo($dst);
		if (! $path)
		{
			return $result;
		}
		$ext = isset($path['extension']) ? '.'.$path['extension'] : '';

		if (is_file($path['dirname']))
		{
			return $result;
		}
		else if ( ! file_exists($path['dirname']))
		{
			@mkdir($path['dirname'], 0777, TRUE);
		}

		$i = 1;
		while (TRUE)
		{
			if (file_exists($dst))
			{
				$dst = $path['dirname'] . '/' . $path['filename'] . $i . $ext;
				$i++;
				continue;
			}
			break;
		}

		$is_url_open = TRUE;
		if ( ! preg_match('#^(https?|ftp)://#u', $src))
		{
			$is_url_open = FALSE;
			if ( ! is_file($src))
			{
				return $result;
			}
		}

		if (@copy($src, $dst))
		{
			@chmod($dst, 0666);
			$result = TRUE;
		}
		else if (function_exists('curl_init') AND $is_url_open)
		{
			$fd = fopen($dst, 'wb');
			if ( ! $fd)
			{
				return FALSE;
			}
			$ch = curl_init();
			$options = array(
				CURLOPT_FILE => $fd,
				CURLOPT_HEADER => FALSE,
				CURLOPT_URL => $src,
				CURLOPT_MAXREDIRS => 5,
				CURLOPT_USERAGENT => isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : 'Mozilla/5.0 (Windows NT 6.1; rv:12.0) Gecko/20100101 Firefox/12.0',
				CURLOPT_FOLLOWLOCATION => true
			);
			curl_setopt_array($ch, $options);
			$result = curl_exec($ch);
			fclose($fd);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ( ! curl_errno($ch) AND ($code >= 200 AND $code < 300))
			{
				@chmod($dst, 0666);
				$result = TRUE;
			}
			else
			{
				@unlink($dst);
				$result = FALSE;
			}
			curl_close($ch);
		}
		return $result ? $dst : FALSE;
	}
}

// ------------------------------------------------------------------
// 私有方法

if ( ! function_exists('_send_file_header'))
{
	function _send_file_header($options)
	{
		$defaults = array(
			'type' => 'application/octet-stream',
			'disposition' => 'attachment'
		);
		$options = array_merge($defaults, $options);

		foreach (array('type', 'disposition') as $arg)
		{
			if (is_null($options[$arg]))
			{
				throw new InvalidArgumentException("{$arg} option required");
			}
		}

		$disposition = $options['disposition'];
		if (isset($options['filename']))
		{
			// IE浏览器编码问题
			if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"]))
			{
				$encoded_filename = str_replace("+", "%20", urlencode($options['filename']));
				$disposition .= "; filename=\"{$encoded_filename}\"";
			}
			else
			{
				$disposition .= "; filename=\"{$options['filename']}\"";
			}
		}

		if ( ! is_string($options['type']) AND isset($options['filename']))
		{
			load_class('MimeType', 'classes');
			$ext = pathinfo($options['filename'], PATHINFO_EXTENSION);
			$options['type'] = MimeType::lookup_by_extension($ext);
		}

		if ( ! headers_sent())
		{
			header("Content-Type: {$options['type']}");
			header("Content-Disposition: {$disposition}");
			header("Content-Transfer-Encoding: binary");
		}
	}
}

/* End of file file_helper.php */
