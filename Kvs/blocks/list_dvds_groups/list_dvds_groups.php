<?php
function list_dvds_groupsShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsListDataSiteBlockDvdGroups($object_id, $block_config))->render($smarty);
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

function list_dvds_groupsGetHash($block_config)
{
	return (new KvsListDataSiteBlockDvdGroups('', $block_config))->to_hash();
}

function list_dvds_groupsCacheControl($block_config)
{
	return (new KvsListDataSiteBlockDvdGroups('', $block_config))->get_cache_mode();
}

function list_dvds_groupsMetaData()
{
	$result = [];

	$parameters = (new KvsListDataSiteBlockDvdGroups())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function list_dvds_groupsInstance(): KvsListDataSiteBlockDvdGroups
{
	return new KvsListDataSiteBlockDvdGroups();
}

class KvsListDataSiteBlockDvdGroups extends KvsListDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeDvdGroup::get_instance(), $block_uid, $block_config);
	}

	protected function define_parameters(): array
	{
		$parameters = parent::define_parameters();
		$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_only_with_screenshot1', KvsSiteBlockParameter::TYPE_BOOL);
		$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_only_without_screenshot1', KvsSiteBlockParameter::TYPE_BOOL);
		$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_only_with_screenshot2', KvsSiteBlockParameter::TYPE_BOOL);
		$parameters[] = new KvsSiteBlockParameter('static_filters', 'show_only_without_screenshot2', KvsSiteBlockParameter::TYPE_BOOL);
		return $parameters;
	}

	protected function apply_filters(KvsQueryExecutor $query_executor): array
	{
		$result = parent::apply_filters($query_executor);

		if ($this->is_parameter_set('show_only_with_screenshot1'))
		{
			$query_executor->where('cover1', '!=', '');
		}
		if ($this->is_parameter_set('show_only_without_screenshot1'))
		{
			$query_executor->where('cover1', '=', '');
		}
		if ($this->is_parameter_set('show_only_with_screenshot2'))
		{
			$query_executor->where('cover2', '!=', '');
		}
		if ($this->is_parameter_set('show_only_without_screenshot2'))
		{
			$query_executor->where('cover2', '=', '');
		}

		return $result;
	}
}