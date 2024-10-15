<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Interface for all query executors.
 */
interface KvsQueryExecutor
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const PROTECTED_QUERY_TYPE_GENERAL = 'general';
	public const PROTECTED_QUERY_TYPE_METADATA = 'metadata';
	public const PROTECTED_QUERY_TYPE_DETAILS = 'details';
	public const PROTECTED_QUERY_TYPE_LIST = 'list';

	public const OP_EQ = '=';
	public const OP_NEQ = '!=';
	public const OP_LT = '<';
	public const OP_LE = '<=';
	public const OP_GT = '>';
	public const OP_GE = '>=';
	public const OP_LK = '~';
	public const OP_NLK = '!~';
	public const OP_EM = '?';
	public const OP_NEM = '!?';

	public const LIKE_STARTS = '%_';
	public const LIKE_ENDS = '_%';
	public const LIKE_FIND = '%_%';

	public const SORT_BY_ASC = 'ASC';
	public const SORT_BY_DESC = 'DESC';

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	/**
	 * Returns query executor data type.
	 *
	 * @return KvsAbstractDataType
	 */
	public function get_type(): KvsAbstractDataType;

	/**
	 * Returns last executed query if any.
	 *
	 * @return string
	 */
	public function get_last_query(): string;

	/**
	 * Emulate failure for testing needs.
	 *
	 * @return KvsQueryExecutor
	 */
	public function emulate_failure(): KvsQueryExecutor;

	/**
	 * Inserts data into data source.
	 *
	 * @param array $values
	 *
	 * @return int
	 * @throws Exception
	 */
	public function insert(array $values): int;

	/**
	 * Updates data in data source.
	 *
	 * @param array $values
	 *
	 * @return int
	 * @throws Exception
	 */
	public function update(array $values): int;

	/**
	 * Deletes data in data source.
	 *
	 * @return int
	 * @throws Exception
	 */
	public function delete(): int;

	/**
	 * Calculates the number of data records in the data source.
	 *
	 * @return int
	 * @throws Exception
	 */
	public function count(): int;

	/**
	 * Checks if result is not empty.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function has(): bool;

	/**
	 * Selects IDs of data records from the data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return int[]
	 * @throws Exception
	 */
	public function ids(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array;

	/**
	 * Selects all data records from the data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function all(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array;

	/**
	 * Selects paginated list from the data source.
	 *
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function paginated(int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array;

	/**
	 * Selects paginated list from the data source.
	 *
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData[]
	 * @throws Exception
	 */
	public function objects(int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array;

	/**
	 * Selects grouped data using the provided selector and group by clause.
	 *
	 * @param string $selector
	 * @param string $group_by
	 * @param int $limit
	 * @param int $from
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array
	 * @throws Exception
	 */
	public function grouped(string $selector, string $group_by, int $limit = 0, int $from = 0, string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): array;

	/**
	 * Selects single data record from data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return array|null
	 * @throws Exception
	 */
	public function single(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): ?array;

	/**
	 * Selects single data record from data source.
	 *
	 * @param string $sort_by_field_name
	 * @param string $sort_by_direction
	 *
	 * @return KvsPersistentData|null
	 * @throws Exception
	 */
	public function object(string $sort_by_field_name = '', string $sort_by_direction = self::SORT_BY_DESC): ?KvsPersistentData;

	/**
	 * Starts / ends filter grouping.
	 *
	 * @return KvsQueryExecutor
	 */
	public function group(): KvsQueryExecutor;

	/**
	 * Adds 'AND' filtering clause.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 *
	 * @return KvsQueryExecutor
	 */
	public function where($field_or_relationship_or_name, string $operation, $value = '', string $like_mode = self::LIKE_FIND): KvsQueryExecutor;

	/**
	 * Adds 'OR' filtering clause.
	 *
	 * @param $field_or_relationship_or_name
	 * @param string $operation
	 * @param mixed $value
	 * @param string $like_mode
	 *
	 * @return KvsQueryExecutor
	 */
	public function alt($field_or_relationship_or_name, string $operation, $value = '', string $like_mode = self::LIKE_FIND): KvsQueryExecutor;

	/**
	 * Adds 'AND' field localized filtering clause.
	 *
	 * @param string $field_id
	 * @param string $locale
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_localized(string $field_id, string $locale): KvsQueryExecutor;

	/**
	 * Adds 'OR' field localized filtering clause.
	 *
	 * @param string $field_id
	 * @param string $locale
	 *
	 * @return KvsQueryExecutor
	 */
	public function alt_localized(string $field_id, string $locale): KvsQueryExecutor;

	/**
	 * Adds list of 'AND' filtering clauses "field = value".
	 *
	 * @param array $fields
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_all(array $fields): KvsQueryExecutor;

	/**
	 * Adds list of 'OR' filtering clauses "field = value".
	 *
	 * @param array $fields
	 *
	 * @return KvsQueryExecutor
	 */
	public function where_any(array $fields): KvsQueryExecutor;

	/**
	 * Converts the current query executor state into ID subquery.
	 *
	 * @return mixed
	 */
	public function get_as_subquery();
}