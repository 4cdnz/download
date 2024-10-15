<?php
function list_content_sourcesShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsListDataSiteBlockContentSources($object_id, $block_config))->render($smarty);
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

function list_content_sourcesGetHash($block_config)
{
	return (new KvsListDataSiteBlockContentSources('', $block_config))->to_hash();
}

function list_content_sourcesCacheControl($block_config)
{
	return (new KvsListDataSiteBlockContentSources('', $block_config))->get_cache_mode();
}

function list_content_sourcesMetaData()
{
	$result = [];

	$parameters = (new KvsListDataSiteBlockContentSources())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function list_content_sourcesInstance(): KvsListDataSiteBlockContentSources
{
	return new KvsListDataSiteBlockContentSources();
}

class KvsListDataSiteBlockContentSources extends KvsListDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeContentSource::get_instance(), $block_uid, $block_config);
	}
}