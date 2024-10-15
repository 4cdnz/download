<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Base class for all KVS site "view" blocks.
 */
class KvsViewDataSiteBlock extends KvsAbstractDataSiteBlock
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
	 * Returns block type ID.
	 *
	 * @return string
	 */
	public function get_block_type_id(): string
	{
		return $this->data_type->get_data_type_name() . '_view';
	}

	/**
	 * Renders "view" block and returns block storage.
	 *
	 * @param Smarty $smarty
	 *
	 * @return array|null
	 * @throws KvsSiteBlockStatusException with error code = 404 for missing object, or 301 / 302 for redirect
	 */
	public function render(Smarty $smarty): ?array
	{
		$storage = [];

		$select_id = intval($this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_id"));
		$select_dir = $this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_dir");

		$query_executor = $this->prepare_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_OBJECT);
		try
		{
			if ($select_id > 0)
			{
				$data = $query_executor->where($this->data_type->get_identifier(), '=', $select_id)->single();
				if (empty($data))
				{
					throw new KvsSiteBlockStatusException(404);
				}
				if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_directory_identifier() !== '' && $select_dir !== '')
				{
					$object_page_url_pattern = $this->data_type->get_object_page_url_pattern();
					if ($select_dir !== $data[$this->data_type->get_object_directory_identifier()] && $object_page_url_pattern !== '')
					{
						$directory_parameter_http_name = $this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_dir", false);
						if (substr_count($_SERVER['QUERY_STRING'], "&$directory_parameter_http_name=") <= 1)
						{
							// return redirect only if URL doesn't contain additional values of the same parameter that could be QSA-ed at the end
							// example: http://domain.com/videos/123/wrong-directory/?dir=another_value
							throw new KvsSiteBlockStatusException(301, str_ireplace(['%id%', '%dir%'], [intval($data[$this->data_type->get_identifier()]), trim($data[$this->data_type->get_object_directory_identifier()])], $object_page_url_pattern));
						}
					}
				}
			} elseif ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_directory_identifier() !== '' && $select_dir !== '')
			{
				$data = $query_executor->where($this->data_type->get_object_directory_identifier(), '=', $select_dir)->single();
				if (empty($data))
				{
					throw new KvsSiteBlockStatusException(404);
				}
			} else
			{
				return $storage;
			}
		} catch (Exception $e)
		{
			KvsException::logic_error("Failed to generate view for object type ({$this->data_type})", $e);
			return $storage;
		}

		if (intval($data[$this->data_type->get_identifier()]) === 0)
		{
			KvsException::coding_error("View block ($this->data_type) loaded data with empty ID");
			throw new KvsSiteBlockStatusException(404);
		}

		$this->process_object_info($data, $this->data_type);
		if ($data['view_page_url'])
		{
			$data['canonical_url'] = $data['view_page_url'];
		}

		$object = $this->data_type::find_by_id(intval($data[$this->data_type->get_identifier()]));

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->supports_subscriptions())
		{
			$data['is_subscribed'] = 0;
			if (($user = KvsContext::get_user()) && $object instanceof KvsPersistentObject)
			{
				try
				{
					if ($user->is_subscribed_to($object))
					{
						$data['is_subscribed'] = 1;
					}
				} catch (Exception $e)
				{
					KvsContext::log_exception($e);
				}
			}
		}

		$relationships = $this->data_type->get_relationships();
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($target)
			{
				if ($relationship->is_group())
				{
					try
					{
						$temp = [&$data];
						$this->pull_grouping_info($relationship, $temp);
						if (is_array($data[$relationship->get_name_single()] ?? null) && $target->get_object_title_identifier() !== '')
						{
							$data["{$relationship->get_name_single()}_as_string"] = $data[$relationship->get_name_single()][$target->get_object_title_identifier()];
						}
						unset($temp);
					} catch (Exception $e)
					{
						KvsException::logic_error("Failed to load grouping object ($target) for parent object ($this->data_type)", $e);
					}
				} elseif ($relationship->is_property())
				{
					try
					{
						$temp = [&$data];
						$this->pull_property_info($relationship, $temp);
						unset($temp);
					} catch (Exception $e)
					{
						KvsException::logic_error("Failed to load property objects ($target) for parent object ($this->data_type)", $e);
					}

					if ($relationship->is_multiple() && is_array($data[$relationship->get_name_multiple()]))
					{
						if ($target->get_object_title_identifier() !== '')
						{
							$list_titles = [];
							foreach ($data[$relationship->get_name_multiple()] as $item)
							{
								$title = trim($item[$target->get_object_title_identifier()]);
								if ($title !== '')
								{
									$list_titles[] = $title;
								}
							}
							$data["{$relationship->get_name_multiple()}_as_string"] = implode(', ', $list_titles);
						}
					}
				}
			}
		}

		foreach ($data as $k => $v)
		{
			$storage[$k] = $v;
		}

		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($relationship->is_grouped() && $target)
			{
				try
				{
					if ($this->is_parameter_set("pull_{$relationship->get_name_multiple()}"))
					{
						$this->pull_child_info($relationship, $data, $this->get_parameter_value("pull_{$relationship->get_name_multiple()}_sort_by"), intval($this->get_parameter_value("pull_{$relationship->get_name_multiple()}_count")));
					}
				} catch (Exception $e)
				{
					KvsException::logic_error("Failed to load grouped object ($target) for parent object ($this->data_type)", $e);
				}
			}
		}

		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			if ($this->is_parameter_set('show_next_and_previous_info'))
			{
				$next_query = $this->data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST)->where($this->data_type->get_identifier(), '>', intval($data[$this->data_type->get_identifier()]));
				$prev_query = $this->data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST)->where($this->data_type->get_identifier(), '<', intval($data[$this->data_type->get_identifier()]));
				foreach ($relationships as $relationship)
				{
					$target = $relationship->get_target();
					if ($relationship->is_group() && $target)
					{
						if ($this->get_parameter_value('show_next_and_previous_info') == $relationship->get_name_single())
						{
							if (is_array($data[$relationship->get_name_single()]))
							{
								$next_query->where($relationship, '=', $data["{$relationship->get_name_single()}_id"]);
								$prev_query->where($relationship, '=', $data["{$relationship->get_name_single()}_id"]);
							} else
							{
								$next_query->where('0', '=', '1');
								$prev_query->where('0', '=', '1');
							}
						}
					}
				}
				try
				{
					$next = $next_query->single($this->data_type->get_identifier(), KvsQueryExecutor::SORT_BY_ASC);
					if ($next)
					{
						$this->process_object_info($next, $this->data_type);
						$smarty->assign("next_{$this->data_type->get_data_type_name()}", $next);
					}
				} catch (Exception $e)
				{
					KvsException::logic_error("Failed to load next object info ({$this->data_type})", $e);
				}
				try
				{
					$prev = $prev_query->single($this->data_type->get_identifier(), KvsQueryExecutor::SORT_BY_DESC);
					if ($prev)
					{
						$this->process_object_info($prev, $this->data_type);
						$smarty->assign("previous_{$this->data_type->get_data_type_name()}", $prev);
					}
				} catch (Exception $e)
				{
					KvsException::logic_error("Failed to load previous object info ({$this->data_type})", $e);
				}
			}
		}

		$smarty->assign('data', $data);

		return $storage;
	}

	/**
	 * Returns block caching mode.
	 *
	 * @return string
	 */
	public function get_cache_mode(): string
	{
		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->supports_subscriptions())
		{
			return self::CACHE_MODE_USER_NO_CACHE;
		}
		return self::CACHE_MODE_DEFAULT;
	}

	/**
	 * Converts block configuration into hash to form caching key.
	 *
	 * @return string
	 */
	public function to_hash(): string
	{
		$hash = parent::to_hash();
		if ($hash == self::BLOCK_HASH_NOCACHE || $hash == self::BLOCK_HASH_RUNTIME_NOCACHE)
		{
			return $hash;
		}

		$id = intval($this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_id"));
		$dir = '';
		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_directory_identifier() !== '')
		{
			$dir = $this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_dir");
		}
		$hash .= ' || ' . $this->get_data_instance_version($id, $dir);

		return $hash;
	}

	/**
	 * Process async actions for a "view" block.
	 */
	public function async(): void
	{
		if ($this->get_async_parameter_value('action') == 'rate' && intval($this->get_async_parameter_value($this->data_type->get_identifier())) > 0)
		{
			if ($this->is_async_parameter_set('vote'))
			{
				//todo: should use public executor instead
				$object = $this->data_type::find_by_id(intval($this->get_async_parameter_value($this->data_type->get_identifier())));
				if ($object instanceof KvsPersistentObject && $object->get_object_type()->get_object_rating_identifier() !== '')
				{
					$vote = intval($this->get_async_parameter_value('vote'));
					if ($vote >= 0 && $vote <= 5)
					{
						try
						{
							if (!$object->rate($vote))
							{
								$this->send_async_failure((new KvsSiteErrors($this->get_block_type_id()))->add_error('ip_already_voted'));
							}
							$result_data = [
									'rating' => $object->int($object->get_object_type()->get_object_rating_identifier()),
									'rating_amount' => $object->int("{$object->get_object_type()->get_object_rating_identifier()}_amount"),
							];
							if ($result_data['rating_amount'] > 0)
							{
								$result_data['rating'] = $result_data['rating'] / $result_data['rating_amount'];
							} else
							{
								$result_data['rating'] = 0;
							}

							$this->inc_data_instance_version($object->int('version_control'), $object->get_id(), $object->get_directory());
							$this->send_async_success($result_data);
						} catch (Exception $e)
						{
							KvsContext::log_exception($e);
						}
					}
				}
			}
			$this->send_async_response_invalid();
		}
	}

	/**
	 * Processes cache-independent request for a "view" block.
	 */
	public function pre_process(): void
	{
		global $config;

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_views_identifier() !== '')
		{
			// increment view stats
			$object_id = intval($this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_id"));
			$object_dir = $this->get_parameter_value("var_{$this->data_type->get_data_type_name()}_dir");
			if ($object_id > 0)
			{
				KvsFilesystem::maybe_append_log("$config[project_path]/admin/data/stats/{$this->data_type->get_data_type_name_multiple()}_id.dat", "$object_id||" . date('Y-m-d H:i:s'));
			} elseif ($object_dir !== '')
			{
				KvsFilesystem::maybe_append_log("$config[project_path]/admin/data/stats/{$this->data_type->get_data_type_name_multiple()}_dir.dat", "$object_dir||" . date('Y-m-d H:i:s'));
			}
		}
	}

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	/**
	 * Defines parameters for this block.
	 *
	 * @return KvsSiteBlockParameter[]
	 */
	protected function define_parameters(): array
	{
		if ($this->data_type->get_identifier() === '')
		{
			throw new RuntimeException("Attempt to use 'view' block with data type that has no ID");
		}

		$parameters = parent::define_parameters();

		if ($this->data_type instanceof KvsAbstractPersistentObjectType && $this->data_type->get_object_directory_identifier() !== '')
		{
			$parameters[] = new KvsSiteBlockParameter('context_object', "var_{$this->data_type->get_data_type_name()}_dir", KvsSiteBlockParameter::TYPE_STRING, true, 'dir');
		}
		$parameters[] = new KvsSiteBlockParameter('context_object', "var_{$this->data_type->get_data_type_name()}_id", KvsSiteBlockParameter::TYPE_INT, false, 'id');

		$relationships = $this->data_type->get_relationships();
		foreach ($relationships as $relationship)
		{
			$target = $relationship->get_target();
			if ($relationship->is_grouped() && $target)
			{
				$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}", KvsSiteBlockParameter::TYPE_BOOL);
				$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}_count", KvsSiteBlockParameter::TYPE_INT, false, '3');
				$parameters[] = new KvsSiteBlockParameter("pull_{$relationship->get_name_multiple()}", "pull_{$relationship->get_name_multiple()}_sort_by", KvsSiteBlockParameter::TYPE_SORTING, false, $target->get_identifier(), $this->data_type_to_sorting_options($target));
			}
		}

		if ($this->data_type instanceof KvsAbstractCategorizationType)
		{
			$options = [];
			$options[] = new KvsSiteBlockParameterOption($this->data_type->get_identifier(), '', $this->map_show_next_and_previous_info_to_obsolete($this->data_type->get_identifier()));
			$relationships = $this->data_type->get_relationships();
			foreach ($relationships as $relationship)
			{
				$target = $relationship->get_target();
				if ($relationship->is_group() && $target)
				{
					$options[] = new KvsSiteBlockParameterOption($relationship->get_name_single(), '', $this->map_show_next_and_previous_info_to_obsolete($relationship->get_name_single()));
				}
			}
			$parameters[] = new KvsSiteBlockParameter('additional_data', 'show_next_and_previous_info', KvsSiteBlockParameter::TYPE_CHOICE, false, $this->data_type->get_identifier(), $options);
		}

		return $parameters;
	}

	/**
	 * Returns old value of show_next_and_previous_info option for the new value for blocks that supported
	 * show_next_and_previous_info before nextgen.
	 *
	 * @param string $new_show_next_and_previous_info
	 *
	 * @return string[]
	 * @noinspection PhpUnusedParameterInspection
	 */
	protected function map_show_next_and_previous_info_to_obsolete(string $new_show_next_and_previous_info): array
	{
		return [];
	}

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}