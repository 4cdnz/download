<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all data providers to feed data into admin panel insight controller.
 */
abstract class KvsAdminInsightDataProvider
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns data type name.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		$data_type = $this->get_data_type();
		return $data_type ? $data_type->get_data_type_name_multiple() : '';
	}

	/**
	 * Lists all sorting values supported by this data provider.
	 *
	 * @return string[]
	 */
	public function get_sortings(): array
	{
		return [];
	}

	/**
	 * Returns data type if any within this data provider.
	 *
	 * @return KvsAbstractDataType
	 */
	public function get_data_type(): ?KvsAbstractDataType
	{
		return null;
	}

	/**
	 * Returns default grouping value for this data provider.
	 *
	 * @return string
	 */
	public function get_default_grouping(): string
	{
		return '';
	}

	/**
	 * Returns whether this insight data provider supports synonyms.
	 *
	 * @return bool
	 */
	public function supports_synonyms(): bool
	{
		return false;
	}

	/**
	 * Returns list of items that match the given search text with the given sorting and filtering.
	 *
	 * @param string $for
	 * @param string $sort_by
	 * @param string $status_filter
	 * @param bool $search_in_synonyms
	 *
	 * @return array
	 * @throws Exception
	 */
	abstract public function insights(string $for, string $sort_by, string $status_filter, bool $search_in_synonyms): array;

	/**
	 * Returns full data of the requested object or null if this data provider doesn't supply full data.
	 *
	 * @param string|null $id
	 * @param string|null $title
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	abstract public function details(?string $id, ?string $title): ?KvsPersistentData;

	/**
	 * Returns list of items for abstract search text, where items are comma-separated. Non existing items or
	 * synonyms are returned as 'new' IDs.
	 *
	 * @param array $items
	 *
	 * @return array
	 * @throws Exception
	 */
	abstract public function objects(array $items): array;

	/**
	 * Returns the total number of items in this data provider.
	 *
	 * @param string $status_filter
	 *
	 * @return int
	 * @throws Exception
	 */
	abstract public function total_count(string $status_filter): int;

	/**
	 * Returns all items from this data provider.
	 *
	 * @param string $sort_by
	 * @param string $status_filter
	 * @param string $group_by_field
	 *
	 * @return array
	 * @throws Exception
	 */
	abstract public function full_list(string $sort_by, string $status_filter, string $group_by_field): array;

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Convenience method to create insight item in the needed format.
	 *
	 * @param string|null $id
	 * @param string $title
	 * @param array|null $synonyms
	 * @param bool $is_inactive
	 * @param bool $is_error
	 *
	 * @return array
	 */
	protected function create_insight_item(?string $id, string $title, ?array $synonyms = null, bool $is_inactive = false, bool $is_error = false): array
	{
		if (!isset($id))
		{
			$id = 'new';
		}
		$result = ['id' => $id, 'title' => $title];
		if (isset($synonyms) && count($synonyms) > 0)
		{
			$result['synonyms'] = implode(', ', $synonyms);
		}
		$result['inactive'] = $is_inactive;
		$result['error'] = $is_error;
		return $result;
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}