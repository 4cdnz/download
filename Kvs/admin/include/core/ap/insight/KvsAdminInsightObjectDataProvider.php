<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Insight data provider for abstract object types.
 */
class KvsAdminInsightObjectDataProvider extends KvsAdminInsightDataProvider
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	private const SORT_BY_ALPHABET = 'alphabet';
	private const SORT_BY_MOST_USED = 'most_used';
	private const SORT_BY_LEAST_USED = 'least_used';

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	/**
	 * @var KvsAbstractPersistentObjectType
	 */
	private $object_type;

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Constructor.
	 *
	 * @param KvsAbstractPersistentObjectType $object_type
	 */
	public function __construct(KvsAbstractPersistentObjectType $object_type)
	{
		$this->object_type = $object_type;
	}

	/**
	 * Supports several sortings for categorization object types.
	 *
	 * @return string[]
	 */
	public function get_sortings(): array
	{
		if ($this->object_type instanceof KvsAbstractCategorizationType)
		{
			return [self::SORT_BY_ALPHABET, self::SORT_BY_MOST_USED, self::SORT_BY_LEAST_USED];
		}
		return [];
	}

	/**
	 * Returns object type of this data provider.
	 *
	 * @return KvsAbstractDataType
	 */
	public function get_data_type(): ?KvsAbstractDataType
	{
		return $this->object_type;
	}

	/**
	 * Returns default grouping value for this data provider.
	 *
	 * @return string
	 */
	public function get_default_grouping(): string
	{
		foreach ($this->object_type->get_relationships() as $relationship)
		{
			if ($relationship->is_group())
			{
				return $relationship->get_name_single();
			}
		}
		return '';
	}

	/**
	 * Returns if object type supports synonyms.
	 *
	 * @return bool
	 */
	public function supports_synonyms(): bool
	{
		return $this->object_type->get_object_synonyms_identifier() !== '';
	}

	/**
	 * Queries object table to return data in the needed format, sorting and filtering.
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

		$query_executor = $this->object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);

		if (strlen($for) > 0)
		{
			$query_executor->group()->where($this->object_type->get_object_title_identifier(), '~', $for);
			if ($this->object_type->get_object_synonyms_identifier() !== '' && $search_in_synonyms)
			{
				$query_executor->alt($this->object_type->get_object_synonyms_identifier(), '~', $for);
			}
			$query_executor->group();
		}

		if ($this->object_type->get_object_status_enumeration() && $status_filter !== '')
		{
			$query_executor->where('status_id', '=', $status_filter);
		}

		$sort_by_field = $this->object_type->get_object_title_identifier();
		$sort_by_direction = KvsQueryExecutor::SORT_BY_ASC;
		switch ($sort_by)
		{
			case self::SORT_BY_MOST_USED:
			case self::SORT_BY_LEAST_USED:
				$sort_by_field = 'total_content';
				if ($sort_by == self::SORT_BY_MOST_USED)
				{
					$sort_by_direction = KvsQueryExecutor::SORT_BY_DESC;
				}
				break;
		}

		$status_enum = $this->object_type->get_object_status_enumeration();

		$data = $query_executor->all($sort_by_field, $sort_by_direction);

		$synonyms_match = [];
		foreach ($data as $item)
		{
			$synonyms = null;
			if ($search_in_synonyms && $this->object_type->get_object_synonyms_identifier() !== '' && strlen($for) > 0)
			{
				$matched_synonyms = KvsUtilities::str_to_array(trim($item[$this->object_type->get_object_synonyms_identifier()]));
				foreach ($matched_synonyms as $key => $synonym)
				{
					if (!KvsUtilities::str_contains($synonym, $for))
					{
						unset($matched_synonyms[$key]);
					}
				}
				$synonyms = $matched_synonyms;
			}
			$insight_item = $this->create_insight_item(trim($item[$this->object_type->get_identifier()]), trim($item[$this->object_type->get_object_title_identifier()]),
					$synonyms, $status_enum ? $status_enum->is_inactive($item['status_id']) : false, $status_enum ? $status_enum->is_error($item['status_id']) : false
			);
			if (KvsUtilities::str_contains($item[$this->object_type->get_object_title_identifier()], $for))
			{
				// first result should contain items that match with title, later only with synonyms match
				$result[] = $insight_item;
			} else
			{
				$synonyms_match[] = $insight_item;
			}
		}
		return array_merge($result, $synonyms_match);
	}

	/**
	 * Returns full data of the requested object.
	 *
	 * @param string|null $id
	 * @param string|null $title
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	public function details(?string $id, ?string $title): ?KvsPersistentData
	{
		if (!$this->object_type->can_view())
		{
			return null;
		}

		$data = null;
		$query_executor = $this->object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_DETAILS);
		if (intval($id) > 0)
		{
			return $query_executor->where($this->object_type->get_identifier(), '=', $id)->object();
		} elseif ($title !== '')
		{
			return $query_executor->where($this->object_type->get_object_title_identifier(), '=', $title)->object();
		}

		KvsException::logic_error("No valid ID or title passed to render insight details for object type: $this->object_type");
		return null;
	}

	/**
	 * Queries object table to return data in the needed format.
	 *
	 * @param array $items
	 *
	 * @return array
	 * @throws Exception
	 */
	public function objects(array $items): array
	{
		$result = [];

		$query_executor = $this->object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
		$query_executor->group();

		$has_items = false;
		foreach ($items as $item)
		{
			if ($item != '')
			{
				$has_items = true;
				$query_executor->alt($this->object_type->get_object_title_identifier(), '=', $item);
				if ($this->object_type->get_object_synonyms_identifier() !== '')
				{
					$query_executor->alt($this->object_type->get_object_synonyms_identifier(), '~', $item);
				}
			}
		}
		if (!$has_items)
		{
			return $result;
		}

		$data = $query_executor->group()->all();

		$items_mapped = [];
		$items_syn_mapped = [];
		$items_titles = [];
		foreach ($data as $item)
		{
			$items_mapped[KvsUtilities::str_lowercase(trim($item[$this->object_type->get_object_title_identifier()]))] = trim($item[$this->object_type->get_identifier()]);
			if ($this->object_type->get_object_synonyms_identifier() !== '')
			{
				$synonyms = KvsUtilities::str_to_array(trim($item[$this->object_type->get_object_synonyms_identifier()]));
				foreach ($synonyms as $synonym)
				{
					$items_syn_mapped[KvsUtilities::str_lowercase($synonym)] = trim($item[$this->object_type->get_identifier()]);
				}
			}
			$items_titles[trim($item[$this->object_type->get_identifier()])] = trim($item[$this->object_type->get_object_title_identifier()]);
		}

		foreach ($items as $item)
		{
			$id = $items_mapped[KvsUtilities::str_lowercase($item)];
			if (isset($id))
			{
				$result[] = $this->create_insight_item($id, $items_titles[$id]);
			} else
			{
				$id = $items_syn_mapped[KvsUtilities::str_lowercase($item)];
				$result[] = $this->create_insight_item($id, $items_titles[$id] ?? $item);
			}
		}

		return $result;
	}

	/**
	 * Returns the total number of objects in their table.
	 *
	 * @param string $status_filter
	 *
	 * @return int
	 * @throws Exception
	 */
	public function total_count(string $status_filter): int
	{
		$query_executor = $this->object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
		if ($this->object_type->get_object_status_enumeration() && $status_filter !== '')
		{
			$query_executor->where('status_id', '=', $status_filter);
		}

		return $query_executor->count();
	}

	/**
	 * Returns all objects.
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
		$query_executor = $this->object_type->prepare_protected_query(KvsQueryExecutor::PROTECTED_QUERY_TYPE_METADATA);
		if ($this->object_type->get_object_status_enumeration() && $status_filter !== '')
		{
			$query_executor->where('status_id', '=', $status_filter);
		}

		$sort_by_field = $this->object_type->get_object_title_identifier();
		$sort_by_direction = KvsQueryExecutor::SORT_BY_ASC;
		switch ($sort_by)
		{
			case self::SORT_BY_MOST_USED:
			case self::SORT_BY_LEAST_USED:
				$sort_by_field = 'total_content';
				if ($sort_by == self::SORT_BY_MOST_USED)
				{
					$sort_by_direction = KvsQueryExecutor::SORT_BY_DESC;
				}
				break;
		}

		$status_enum = $this->object_type->get_object_status_enumeration();

		$items = $query_executor->all($sort_by_field, $sort_by_direction);
		$result = [];
		foreach($items as $item)
		{
			$title = trim($item[$this->object_type->get_object_title_identifier()]);
			if ($title === '')
			{
				$title = KvsAdminPanel::get_data_type_with_id($this->object_type, trim($item[$this->object_type->get_identifier()]));
			}
			$result_item = $this->create_insight_item(
					trim($item[$this->object_type->get_identifier()]), $title, null, $status_enum ? $status_enum->is_inactive($item['status_id']) : false, $status_enum ? $status_enum->is_error($item['status_id']) : false
			);
			if ($group_by_field !== '')
			{
				$grouping_field = $this->object_type->get_field($group_by_field);
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