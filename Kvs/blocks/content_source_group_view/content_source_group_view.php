<?php
function content_source_group_viewShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsViewDataSiteBlockContentSourceGroup($object_id, $block_config))->render($smarty);
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

function content_source_group_viewGetHash($block_config)
{
	return (new KvsViewDataSiteBlockContentSourceGroup('', $block_config))->to_hash();
}

function content_source_group_viewCacheControl($block_config)
{
	return (new KvsViewDataSiteBlockContentSourceGroup('', $block_config))->get_cache_mode();
}

function content_source_group_viewMetaData()
{
	$result = [];

	$parameters = (new KvsViewDataSiteBlockContentSourceGroup())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function content_source_group_viewInstance(): KvsViewDataSiteBlockContentSourceGroup
{
	return new KvsViewDataSiteBlockContentSourceGroup();
}

class KvsViewDataSiteBlockContentSourceGroup extends KvsViewDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeContentSourceGroup::get_instance(), $block_uid, $block_config);
	}
}