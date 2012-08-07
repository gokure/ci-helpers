<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用于生成 <form> 的标签
 *
 * form('/path');
 *  => <form action="/path" method="post" accept-charset="UTF-8">
 *
 * form('/path', array('method' => 'put'), function() use(&$user) {
 *     return hidden_field_tag('user_id', $user->id)
 *          . submit_tag('提交');
 * })
 *
 * => <form action="/path" method="post" accept-charset="UTF-8">
 *    <div style="margin:0;padding:0;display:inline">
 *    <input type="hidden" name="_method" value="put" />
 *    <input type="hidden" name="utf8" value="&#x2713;" />
 *    </div>
 *    <input type="hidden" name="user_id" value="123" />
 *    <input type="submit" name="commit" value="提交" />
 *    </form>
 */
if ( ! function_exists('form_tag'))
{
	function form_tag($url_for_options = array(), $options = array(), $block = NULL)
	{
		$args = func_get_args();
		if (! is_array($options))
		{
			$options = array();
		}
		$html_options = array() + $options;
		$html_options['action'] = site_url($url_for_options);
		$html_options['accept-charset'] = 'UTF-8';
		if (array_delete($options, 'multipart')) $html_options['enctype'] = 'multipart/form-data';
		if (array_delete($options, 'remote')) $html_options['data-remote'] = TRUE;
		$block = end($args);
		if (is_callable($block))
		{
		  $output = form_tag_html($html_options);
		  $output .= call_user_func($block);
		  $output .= '</form>';
		  return $output;
		}
		else
		{
			return form_tag_html($html_options);
		}
	}
}

if ( ! function_exists('form_tag_html'))
{
	function form_tag_html($html_options)
	{
		$method = array_delete($html_options, 'method');
		if (! $method) $method = 'post';
		$method = strtolower($method);
		if ($method == 'get')
		{
			$html_options['method'] = 'get';
			$method_tag = '';
		}
		else
		{
			$html_options['method'] = 'post';
			$method_tag = tag('input', array('type' => 'hidden', 'name' => '_method', 'value' => $method));
		}
		$tags = tag('input', array('type' => 'hidden', 'name' => 'utf8', 'value' => '&#x2713;')) . $method_tag;
		$extra_tags = content_tag('div', $tags, array('style' => 'margin:0;padding:0;display:inline'));

		return tag('form', $html_options, TRUE) . $extra_tags;
	}
}

/**
 * 用于生成 <textarea> 的标签
 *
 * text_area_tag('about', 'This is about me');
 *  => <textarea name="about" id="about">This is about me</textarea>
 */
if ( ! function_exists('text_area_tag'))
{
	function text_area_tag($name, $content = null, $options = array())
	{
		$size = array_delete($options, 'size');
		if ($size AND is_string($size))
		{
			list($options['cols'], $options['rows']) = explode('x', $size);
		}

		if (isset($options['escape']))
			$escape = array_delete($options, 'escape');
		else
			$escape = TRUE;

		$options = array_merge(array('name' => $name, 'id' => sanitize_to_id($name)), $options);
		return content_tag('textarea', $content, $options);
	}
}

/**
 * 用于生成 <input type="text"> 的标签
 *
 * text_filed_tag('username', 'Bob');
 *  => <input type="text" value="Bob" name="username" id="username" />
 */
if ( ! function_exists('text_field_tag'))
{
	function text_field_tag($name, $value = NULL, $options = array()) {
		$options = array_merge(array('type' => 'text', 'name' => $name, 'id' => sanitize_to_id($name), 'value' => $value), $options);
		return tag('input', $options);
	}
}

/**
 * 用于生成 <input type="hidden"> 的标签
 *
 * text_filed_tag('token', 'secret');
 *  => <input type="hidden" value="secret" name="token" id="token" />
 */
if ( ! function_exists('hidden_field_tag'))
{
	function hidden_field_tag($name, $value = NULL, $options = array())
	{
		$options = array_merge($options, array('type' => 'hidden'));
		return text_field_tag($name, $value, $options);
	}
}

/**
 * 用于生成 <input type="password"> 的标签
 *
 * text_filed_tag('password', 'secret');
 *  => <input type="password" value="secret" name="password" id="password" />
 */
if ( ! function_exists('password_field_tag'))
{
	function password_field_tag($name, $value = NULL, $options = array())
	{
		$options = array_merge($options, array('type' => 'password'));
		return text_field_tag($name, $value, $options);
	}
}

/**
 * 用于生成select标签，可与options_for_select合用，
 * $options 选项
 *  'include_blank' => 生成一个空option标签
 *  'prompt' => 生成一个有提示信息的空option标签
 *
 * select_tag("gender", $gender, array("class" => "foobar"));
 *  => <select class="foobar">
 *      <option value="0">男</option><option selected="selected" value="1">女</option>
 *     </select>
 */
if ( ! function_exists('select_tag'))
{
	function select_tag($name, $option_tags = NULL, $options = array())
	{
		$html_name = (isset($options['multiple']) AND substr($name, -2) != '[]') ? "{$name}[]" : $name;

		if (array_delete($options, 'include_blank'))
			$option_tags = "<option value=\"\"></option>" . $option_tags;

		if ($prompt = array_delete($options, 'prompt'))
			$option_tags = "<option value=\"\">{$prompt}</option>" . $option_tags;

		$options = array_merge(array("name" => $html_name, "id" => sanitize_to_id($name)), $options);

		return content_tag('select', $option_tags, $options);
	}
}

/**
 * 用于生成 <input type="radio"> 的标签
 *
 * radio_button_tag("gender", 0, true, array("id" => null));
 *  => <input type="radio" value="0" name="gender" />
 */
if ( ! function_exists('radio_button_tag'))
{
	function radio_button_tag($name, $value, $checked = FALSE, $options = array())
	{
		$html_options = array('type' => 'radio', 'name' => $name, 'id' => sanitize_to_id($name) . '_' . sanitize_to_id($value), 'value' => $value);
		if ($checked)
			$html_options['checked'] = "checked";
		$html_options = array_merge($html_options, $options);
		return tag('input', $html_options);
	}
}

/**
 * 用于生成 <input type="checkbox"> 标签
 *
 * check_box_tag("gender", 0, true, array("id" => null));
 *  => <input type="checkbox" value="0" name="gender" />
 */
if ( ! function_exists('check_box_tag'))
{
	function check_box_tag($name, $value = "1", $checked = FALSE, $options = array())
	{
		$html_options = array('type' => 'checkbox', 'name' => $name, 'id' => sanitize_to_id($name), 'value' => $value);
		if ($checked)
			$html_options['checked'] = "checked";
		$html_options = array_merge($html_options, $options);
		return tag('input', $html_options);
	}
}

/**
 * 用于生成label标签
 *
 * label_tag("gender", "男");
 *  => <label for="gender">男</label>
 */
if ( ! function_exists('label_tag'))
{
	function label_tag($name = NULL, $content_or_options = NULL, $options = array())
	{
		if (strlen($name) OR isset($options['for']))
			$options['for'] = sanitize_to_id($name);
		$content = $content_or_options ? $content_or_options : humanize($name);
		return content_tag('label', $content, $options);
	}
}

/**
 * 用于生成<input type="submit">的标签
 *
 * submit_tag("提交")
 *  => <input name="commit" type="submit" value="提交" />
 */
if ( ! function_exists('submit_tag'))
{
	function submit_tag($value = "提交", $options = array())
	{
		if ($disable_with = array_delete($options, 'disable_with'))
			$options['data-disable-with'] = $disable_with;

		if ($confirm = array_delete($options, 'confirm'))
			$options['data-confirm'] = $confirm;

		return tag('input', array_merge(array('type' => 'submit', 'name' => 'commit', 'value' => $value), $options));
	}
}

/**
 * 用于生成 <button> 标签
 * button_tag "Checkout", :disable_with => "Please wait..."
 * =>
 * <button data-disable-with="Please wait..." name="button" type="submit">Checkout</button>
 */
if ( ! function_exists('button_tag'))
{
	function button_tag($content_or_options = NULL, $options = array())
	{
		if ($disable_with = array_delete($options, 'disable_with'))
			$options['data-disable-with'] = $disable_with;

		if ($confirm = array_delete($options, 'confirm'))
			$options['data-confirm'] = $confirm;

		$options += array('name' => 'button', 'type' => 'submit');

		$text = $content_or_options ? $content_or_options : 'Button';
		return content_tag('button', $text, $options);
	}
}

/**
 * 清理字符串，转化非法字符为"_"
 *
 * sanitize_to_id("hello world");
 *  => "hello_world"
 */
if ( ! function_exists('sanitize_to_id'))
{
	function sanitize_to_id($name)
	{
		return preg_replace("/[^-a-zA-Z0-9:.]/", "_", str_replace(']', '', $name));
	}
}

/* End of file form_tag_helper.php */
