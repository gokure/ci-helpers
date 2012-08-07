<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用于生成 select 标签的 options 内容
 *
 * $gender = array("0" => "男", "1" => "女");
 * options_for_select($gender, 1);
 *  =>
 * <option value="0">男</option>
 * <option selected="selected" value="1">女</option>
 * or
 * options_for_select($gender, array('selected' => '1', 'disabled' => '0'));
 *  =>
 * <option value="0" disabled="disabled">男</option>
 * <option selected="selected" value="1">女</option>
 */
if ( ! function_exists('options_for_select'))
{
	function options_for_select($container, $selected = null)
	{
		if (is_string($container))
			return $container;

		$tags = array();
		$selected = (array)$selected;

		if (! isset($selected['selected']))
			$selected['selected'] = $selected;

		if (! isset($selected['disabled']))
			$selected['disabled'] = null;

		foreach ($container as $key => $text) {
			$options = array('value' => $key);
			$key = strval($key);

			if (strlen($key))
			{
				if (in_array($key, array_map('strval', (array)$selected['selected'])))
					$options['selected'] = 'selected';

				if ($selected['disabled'] AND in_array($key, array_map('strval', (array)$selected['disabled'])))
					$options['disabled'] = 'disabled';
			}

			$tags[] = content_tag('option', $text, $options);
		}

		return implode("\n", $tags);;
	}
}

/**
 * 用于生成带有 <optgroup> 标签的 option 标签
 *
 * $grouped_options = array(
 *     'North America' => array('US' => 'United States', 'Canada' => 'Canada'),
 *     'Europe' => array('Denmark' => 'Denmark','Germany' => 'Germany','France' => 'France')
 * );
 * grouped_options_for_select($grouped_options)
 * =>
 * <optgroup label="Europe">
 *     <option value="Denmark">Denmark</option>
 *     <option value="Germany">Germany</option>
 *     <option value="France">France</option>
 * </optgroup>
 *     <optgroup label="North America">
 *     <option value="US">United States</option>
 *     <option value="Canada">Canada</option>
 * </optgroup>
 */
if ( ! function_exists('grouped_options_for_select'))
{
	function grouped_options_for_select($grouped_options, $selected_key = null, $prompt = null)
	{
		$body = '';
		if ($prompt)
			$body .= content_tag('option', $prompt, array('value' => ''), true);

		foreach ($grouped_options as $label => $container)
		{
			$body .= content_tag('optgroup', options_for_select($container, $selected_key), array('label' => $label));
		}

		return $body;
	}
}

/* End of file form_options_helper.php */
