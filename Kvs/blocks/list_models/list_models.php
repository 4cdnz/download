<?php
function list_modelsShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsListDataSiteBlockModels($object_id, $block_config))->render($smarty);
		if ($result)
		{
			$storage[$object_id] = $result;
		}
	} catch (KvsSiteBlockStatusException $e)
	{
		if ($e->get_status_code() == 404)
		{
			return 'status_404';
		} elseif ($e->get_status_code() == 301 || $e->get_status_code() == 302)
		{
			return "status_{$e->get_status_code()}:{$e->get_redirect_url()}";
		}
	} catch (Exception $e)
	{
		KvsContext::log_exception($e);
	}
	return '';
}

function list_modelsGetHash($block_config)
{
	return (new KvsListDataSiteBlockModels('', $block_config))->to_hash();
}

function list_modelsCacheControl($block_config)
{
	return (new KvsListDataSiteBlockModels('', $block_config))->get_cache_mode();
}

function list_modelsMetaData()
{
	$result = [];

	$parameters = (new KvsListDataSiteBlockModels())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function list_modelsInstance(): KvsListDataSiteBlockModels
{
	return new KvsListDataSiteBlockModels();
}

class KvsListDataSiteBlockModels extends KvsListDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeModel::get_instance(), $block_uid, $block_config);
	}

	public function get_cache_mode(): string
	{
		return self::CACHE_MODE_STATUS_SPECIFIC;
	}

	protected function define_parameters(): array
	{
		$parameters = parent::define_parameters();

		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_country', KvsSiteBlockParameter::TYPE_STRING, false, 'country', [], false, ['var_country_id']);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_state', KvsSiteBlockParameter::TYPE_STRING, false, 'state', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_city', KvsSiteBlockParameter::TYPE_STRING, false, 'city', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_age_from', KvsSiteBlockParameter::TYPE_STRING, false, 'age_from', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_age_to', KvsSiteBlockParameter::TYPE_STRING, false, 'age_to', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_height_from', KvsSiteBlockParameter::TYPE_STRING, false, 'height_from', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_height_to', KvsSiteBlockParameter::TYPE_STRING, false, 'height_to', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_weight_from', KvsSiteBlockParameter::TYPE_STRING, false, 'weight_from', []);
		$parameters[] = new KvsSiteBlockParameter('dynamic_filters', 'var_weight_to', KvsSiteBlockParameter::TYPE_STRING, false, 'weight_to', []);
		for ($i = 1; $i <= 10; $i++)
		{
			$parameters[] = new KvsSiteBlockParameter('dynamic_filters', "var_custom$i", KvsSiteBlockParameter::TYPE_STRING, false, "custom$i", []);
		}
		return $parameters;
	}

	protected function apply_filters(KvsQueryExecutor $query_executor): array
	{
		$storage = parent::apply_filters($query_executor);

		$sort_by = strtolower($this->get_parameter_value('var_sort_by'));
		if ($sort_by === '')
		{
			$sort_by = strtolower($this->get_parameter_value('sort_by'));
		}
		$sort_by_clear = trim(str_ireplace(['asc', 'desc'], '', $sort_by));
		if ($sort_by_clear == 'age')
		{
			$query_executor->where('age', '>', 0);
		}

		$country = $this->get_parameter_value('var_country');
		if ($country !== '')
		{
			$country_object = null;
			try
			{
				$language_code = KvsContext::get_locale();
				if (!in_array($language_code, ['en', 'ru']))
				{
					$language_code = 'en';
				}
				$country_object = KvsObjectTypeCountry::get_instance()->prepare_internal_query()->where('language_code', '=', $language_code)->where_any(['country_code' => $country, 'country_id' => intval($country)])->object();
			} catch (Exception $e)
			{
				KvsException::logic_error("Failed to query country", $e);
			}
			if ($country_object)
			{
				$query_executor->where('country', '=', $country_object->string('country_code'));
				$storage['country'] = $country_object->string('title');
				$storage['country_id'] = $country_object->int('country_id');
				$storage['country_code'] = $country_object->string('country_code');
				$storage['cities'] = $this->data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST)->where('country', '=', $country_object->string('country_code'))->where('city', '!?')->grouped('city, count(*) AS total_models', 'city', 0, 0, 'city', KvsQueryExecutor::SORT_BY_ASC);
				$storage['states'] = $this->data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST)->where('country', '=', $country_object->string('country_code'))->where('state', '!?')->grouped('state, count(*) AS total_models', 'state', 0, 0, 'state', KvsQueryExecutor::SORT_BY_ASC);
			}
		}

		$state = $this->get_parameter_value('var_state');
		if ($state !== '')
		{
			$query_executor->where('state', '=', $state);
			$storage['state'] = $state;
			$storage['cities'] = $this->data_type->prepare_public_query(KvsAbstractPersistentDataType::PUBLIC_QUERY_TYPE_DIRECT_LIST)->where('state', '=', $state)->where('city', '!?')->grouped('city, count(*) AS total_models', 'city', 0, 0, 'city', KvsQueryExecutor::SORT_BY_ASC);
		}

		$city = $this->get_parameter_value('var_city');
		if ($city !== '')
		{
			$query_executor->where('city', '=', $city);
			$storage['city'] = $city;
		}

		$age_from = $this->get_parameter_value('var_age_from');
		if (intval($age_from) > 0)
		{
			$query_executor->where('age', '>=', $age_from);
			$storage['age_from'] = $age_from;
		}

		$age_to = $this->get_parameter_value('var_age_to');
		if (intval($age_to) > 0)
		{
			$query_executor->where('age', '>', 0)->where('age', '<=', $age_to);
			$storage['age_to'] = $age_to;
		}

		$height_from = $this->get_parameter_value('var_height_from');
		if (intval($height_from) > 0)
		{
			$query_executor->where('height', '>=', $height_from);
			$storage['height_from'] = $height_from;
		}

		$height_to = $this->get_parameter_value('var_height_to');
		if (intval($height_to) > 0)
		{
			$query_executor->where('height', '>', 0)->where('height', '<=', $height_to);
			$storage['height_to'] = $height_to;
		}

		$weight_from = $this->get_parameter_value('var_weight_from');
		if (intval($weight_from) > 0)
		{
			$query_executor->where('weight', '>=', $weight_from);
			$storage['weight_from'] = $weight_from;
		}

		$weight_to = $this->get_parameter_value('var_weight_to');
		if (intval($weight_to) > 0)
		{
			$query_executor->where('weight', '>', 0)->where('weight', '<=', $weight_to);
			$storage['weight_to'] = $weight_to;
		}

		for ($i = 1; $i <= 10; $i++)
		{
			$custom_value = $this->get_parameter_value("var_custom$i");
			if ($custom_value !== '')
			{
				$query_executor->where("custom$i", '=', $custom_value);
				$storage["custom$i"] = $custom_value;
			}
		}

		return $storage;
	}

	protected function get_default_sorting_direction(string $sort_by_name): string
	{
		if ($sort_by_name == 'age')
		{
			return KvsQueryExecutor::SORT_BY_ASC;
		}
		return parent::get_default_sorting_direction($sort_by_name);
	}

	protected function get_custom_related_mode_names(): array
	{
		return ['country', 'state', 'city', 'age', 'height', 'weight'];
	}

	protected function apply_custom_related_mode(string $related_mode, array $related_items, KvsQueryExecutor $query_executor): bool
	{
		// state and city related modes can be processed by the superclass related processing, no need for custom processing for them
		if ($related_mode == 'country')
		{
			$country_codes = [];
			foreach ($related_items as $related_item)
			{
				if ($related_item['country_code'] !== '')
				{
					$country_codes[] = $related_item['country_code'];
				}
			}
			if (count($country_codes) > 0)
			{
				$query_executor->where('country', '=', $country_codes);
			}
			return true;
		} elseif ($related_mode == 'age')
		{
			$age_min = 100000;
			$age_max = 0;
			foreach ($related_items as $related_item)
			{
				if (intval($related_item['age']) > 0)
				{
					$age_min = min($age_min, intval($related_item['age']));
					$age_max = max($age_max, intval($related_item['age']));
				}
			}
			if ($age_max > 0)
			{
				$query_executor->where('age', '>=', $age_min - 3)->where('age', '<=', $age_max + 3);
			}
			return true;
		} elseif ($related_mode == 'height')
		{
			$height_min = 100000;
			$height_max = 0;
			foreach ($related_items as $related_item)
			{
				if (intval($related_item['height']) > 0)
				{
					$height_min = min($height_min, intval($related_item['height']));
					$height_max = max($height_max, intval($related_item['height']));
				}
			}
			if ($height_max > 0)
			{
				$query_executor->where('height', '>=', $height_min - 5)->where('height', '<=', $height_max + 5);
			}
			return true;
		} elseif ($related_mode == 'weight')
		{
			$weight_min = 100000;
			$weight_max = 0;
			foreach ($related_items as $related_item)
			{
				if (intval($related_item['weight']) > 0)
				{
					$weight_min = min($weight_min, intval($related_item['weight']));
					$weight_max = max($weight_max, intval($related_item['weight']));
				}
			}
			if ($weight_max > 0)
			{
				$query_executor->where('weight', '>=', $weight_min - 5)->where('weight', '<=', $weight_max + 5);
			}
			return true;
		}
		return false;
	}

	protected function map_related_mode_to_obsolete(string $new_related_mode): array
	{
		switch ($new_related_mode)
		{
			case 'tags':
				return ['1'];
			case 'categories':
				return ['2'];
			case 'country':
				return ['3'];
			case 'city':
				return ['4'];
			case 'gender_id':
				return ['5'];
			case 'age':
				return ['6'];
			case 'height':
				return ['7'];
			case 'weight':
				return ['8'];
			case 'hair_id':
				return ['9'];
			case 'videos':
				return ['10'];
			case 'albums':
				return ['11'];
			case 'model_group':
				return ['12'];
			case 'state':
				return ['13'];
		}
		return [];
	}
}