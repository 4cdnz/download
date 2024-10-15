<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Convenience class to report errors back to user.
 */
final class KvsSiteErrors
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var string
	 */
	private $block_id;

	/**
	 * @var array
	 */
	private $json_output = [];

	/**
	 * @var string
	 */
	private $xml_output = '';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $block_id
	 */
	public function __construct(string $block_id)
	{
		$block_id = trim($block_id);
		if ($block_id === '')
		{
			throw new InvalidArgumentException('Empty block ID passed');
		}
		$this->block_id = $block_id;
	}

	/**
	 * Returns JSON error format.
	 *
	 * @return array
	 */
	public function get_json_output(): array
	{
		return $this->json_output;
	}

	/**
	 * Returns XML error format.
	 *
	 * @return string
	 */
	public function get_xml_output(): string
	{
		return $this->xml_output;
	}

	/**
	 * Adds error into the list of errors.
	 *
	 * @param string $error_code
	 * @param string $error_field_name
	 * @param array $error_details
	 *
	 * @return $this
	 */
	public function add_error(string $error_code, string $error_field_name = '', array $error_details = []): KvsSiteErrors
	{
		global $lang;

		$error_code = trim($error_code);
		$error_field_name = trim($error_field_name);
		if ($error_code === '')
		{
			throw new InvalidArgumentException('Empty error code passed');
		}

		$json_error = ['code' => $error_code, 'block' => $this->block_id];
		$this->xml_output .= "<error type=\"$error_code\" block=\"$this->block_id\"";
		if ($error_field_name !== '')
		{
			$json_error['field'] = $error_field_name;
			$this->xml_output .= " field=\"$error_field_name\"";
		}
		if (count($error_details) > 0)
		{
			$json_error['details'] = $error_details;
		}

		if (isset($lang))
		{
			$field_error_code = $error_code;
			if ($error_field_name !== '')
			{
				$field_error_code = $error_field_name . '_' . $error_code;
			}
			$error_text = '';
			if (isset($error['message']))
			{
				$error_text = $error['message'];
			}
			if ($error_text === '')
			{
				$error_text = $lang['validation'][$this->block_id][$field_error_code] ?? '';
			}
			if ($error_text === '')
			{
				$error_text = $lang['validation']['common'][$field_error_code] ?? '';
			}
			if ($error_text === '')
			{
				$error_text = $lang['validation']['common'][$error_code] ?? '';
			}
			if ($error_text === '')
			{
				$error_text = str_replace('%1%', $error_code, $lang['validation']['common']['unknown_error'] ?? '');
			}
			if ($error_text !== '')
			{
				if (count($error_details) > 0)
				{
					for ($i = 1; $i <= count($error_details); $i++)
					{
						$error_text = str_replace("%$i%", $error_details[$i - 1], $error_text);
					}
				}
				$json_error['message'] = $error_text;
			}
		}

		$old_error_field_code = '';
		if ($error_code == 'ip_already_voted')
		{
			$old_error_field_code = 'error_1';
		}
		if ($old_error_field_code !== '')
		{
			$this->xml_output .= ">$old_error_field_code</error>";
		} else
		{
			$this->xml_output .= "/>";
		}
		$this->json_output[] = $json_error;

		return $this;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}