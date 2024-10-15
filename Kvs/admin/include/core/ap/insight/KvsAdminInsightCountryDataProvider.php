<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Insight data provider for countries.
 */
class KvsAdminInsightCountryDataProvider extends KvsAdminInsightDataProvider
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentDataType
	 */
	private $data_type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->data_type = KvsObjectTypeCountry::get_instance();
	}

	/**
	 * Status is not supported for countries.
	 *
	 * @return null
	 */
	public function get_data_type(): ?KvsAbstractDataType
	{
		return $this->data_type;
	}

	/**
	 * Returns default grouping value for this data provider.
	 *
	 * @return string
	 */
	public function get_default_grouping(): string
	{
		return 'continent_code';
	}

	/**
	 * Queries country table to return data in the needed format, sorting and filtering.
	 *
	 * @param string $for
	 * @param string $sort_by
	 * @param string $status_filter
	 * @param bool $search_in_synonyms
	 *
	 * @return array
	 * @throws Exception
	 */
	public function insights(string $for, string $sort_by, string $status_filter, bool $search_in_synonyms): array
	{
		$result = [];

		$query_executor = $this->data_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);

		if (strlen($for) > 0)
		{
			$query_executor->where('title', '~', $for);
		}

		$data = $query_executor->all('title', KvsQueryExecutor::SORT_BY_ASC);
		foreach ($data as $item)
		{
			$result[] = $this->create_insight_item($item['country_code'], $item['title']);
		}
		return $result;
	}

	/**
	 * No details are supported for countries.
	 *
	 * @param string|null $id
	 * @param string|null $title
	 *
	 * @return null
	 */
	public function details(?string $id, ?string $title): ?KvsPersistentData
	{
		return null;
	}

	/**
	 * Queries country table to return data in the needed format.
	 *
	 * @param array $items
	 *
	 * @return array
	 * @throws Exception
	 */
	public function objects(array $items): array
	{
		$result = [];

		$has_items = false;
		foreach ($items as $item)
		{
			if ($item != '')
			{
				$has_items = true;
				$query_executor = $this->data_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
				$query_executor->group()->where('language_code', '=', KvsContext::get_locale() == 'russian' ? 'ru' : 'en')->where('country_code', '=', $item)->group()->alt('title', '=', $item);
				$item = $query_executor->single();
				if ($item)
				{
					$result[] = $this->create_insight_item($item['country_code'], $item['title']);
				}
			}
		}
		if (!$has_items)
		{
			return $result;
		}

		return $result;
	}

	/**
	 * Returns the total number of countries.
	 *
	 * @param string $status_filter
	 *
	 * @return int
	 * @throws Exception
	 */
	public function total_count(string $status_filter): int
	{
		$query_executor = $this->data_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
		$query_executor->where('language_code', '=', KvsContext::get_locale() == 'russian' ? 'ru' : 'en');
		return $query_executor->count();
	}

	/**
	 * Returns all countries.
	 *
	 * @param string $sort_by
	 * @param string $status_filter
	 * @param string $group_by_field
	 *
	 * @return array
	 * @throws Exception
	 */
	public function full_list(string $sort_by, string $status_filter, string $group_by_field): array
	{
		$query_executor = $this->data_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
		$query_executor->where('language_code', '=', KvsContext::get_locale() == 'russian' ? 'ru' : 'en');

		$items = $query_executor->all();
		$result = [];
		foreach($items as $item)
		{
			$result_item = $this->create_insight_item($item['country_code'], $item['title']);
			if ($group_by_field !== '')
			{
				$grouping_field = $this->data_type->get_field($group_by_field);
				if ($grouping_field instanceof KvsReferenceField)
				{
					$result_item[$grouping_field->get_name()] = intval($item[$grouping_field->get_name()]);
				} elseif ($grouping_field instanceof KvsPersistentField)
				{
					$result_item[$grouping_field->get_name()] = trim($item[$grouping_field->get_name()]);
				}
			}
			$result[] = $result_item;
		}
		return $result;
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}