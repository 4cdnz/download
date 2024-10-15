<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Describes site block parameter option structure.
 */
final class KvsSiteBlockParameterOption implements KvsDisplayableData
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
	private $name;

	/**
	 * @var string
	 */
	private $group;

	/**
	 * @var string[]
	 */
	private $obsolete_names;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param string $group
	 * @param string[] $obsolete_names
	 */
	public function __construct(string $name, string $group = '', array $obsolete_names = [])
	{
		$this->name = trim($name);
		$this->group = trim($group);
		$this->obsolete_names = $obsolete_names;

		if ($this->name === '')
		{
			throw new InvalidArgumentException('Empty parameter ID passed');
		}
		if ($this->name == 'comments_count')
		{
			$this->obsolete_names[] = 'most_commented';
		}
		if ($this->name == 'favourites_count')
		{
			$this->obsolete_names[] = 'most_favourited';
		}
		if ($this->name == 'purchases_count')
		{
			$this->obsolete_names[] = 'most_purchased';
		}
		if ($this->name == 'subscribers_count')
		{
			$this->obsolete_names[] = 'most_subscribed';
		}
	}

	/**
	 * Returns option identifier.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Returns option group identifier.
	 *
	 * @return string
	 */
	public function get_group(): string
	{
		return $this->group;
	}

	/**
	 * Returns option old / deprecated values for backward compatibility.
	 *
	 * @return string[]
	 */
	public function get_obsolete_names(): array
	{
		return $this->obsolete_names;
	}

	/**
	 * Converts this option to array in format supported by admin panel.
	 *
	 * @return array
	 */
	public function to_display_array(): array
	{
		$result = [];
		$result['name'] = $this->name;
		$result['group'] = $this->group;
		$result['obsolete_names'] = $this->obsolete_names;
		return $result;
	}

	/**
	 * Returns option identifier.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->name;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}