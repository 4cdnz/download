<?php
function list_tagsShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsListDataSiteBlockTags($object_id, $block_config))->render($smarty);
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

function list_tagsGetHash($block_config)
{
	return (new KvsListDataSiteBlockTags('', $block_config))->to_hash();
}

function list_tagsCacheControl($block_config)
{
	return (new KvsListDataSiteBlockTags('', $block_config))->get_cache_mode();
}

function list_tagsMetaData()
{
	$result = [];

	$parameters = (new KvsListDataSiteBlockTags())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function list_tagsInstance(): KvsListDataSiteBlockTags
{
	return new KvsListDataSiteBlockTags();
}

class KvsListDataSiteBlockTags extends KvsListDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeTag::get_instance(), $block_uid, $block_config);
	}
}