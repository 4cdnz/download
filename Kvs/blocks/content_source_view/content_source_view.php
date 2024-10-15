<?php
function content_source_viewShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsViewDataSiteBlockContentSource($object_id, $block_config))->render($smarty);
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

function content_source_viewGetHash($block_config)
{
	return (new KvsViewDataSiteBlockContentSource('', $block_config))->to_hash();
}

function content_source_viewCacheControl($block_config)
{
	return (new KvsViewDataSiteBlockContentSource('', $block_config))->get_cache_mode();
}

function content_source_viewMetaData()
{
	$result = [];

	$parameters = (new KvsViewDataSiteBlockContentSource())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function content_source_viewAsync($block_config)
{
	(new KvsViewDataSiteBlockContentSource('', $block_config))->async();
}

function content_source_viewPreProcess($block_config, $object_id)
{
	(new KvsViewDataSiteBlockContentSource($object_id, $block_config))->pre_process();
}

function content_source_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingCSView.js?v={$config['project_version']}";
}

function content_source_viewInstance(): KvsViewDataSiteBlockContentSource
{
	return new KvsViewDataSiteBlockContentSource();
}

class KvsViewDataSiteBlockContentSource extends KvsViewDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeContentSource::get_instance(), $block_uid, $block_config);
	}

	protected function map_async_parameter_name_to_obsolete(string $new_name): array
	{
		switch ($new_name)
		{
			case 'content_source_id':
				return ['cs_id'];
		}
		return [];
	}

	protected function map_show_next_and_previous_info_to_obsolete(string $new_show_next_and_previous_info): array
	{
		switch ($new_show_next_and_previous_info)
		{
			case 'content_source_id':
				return ['0'];
			case 'content_source_group':
				return ['1'];
		}
		return [];
	}
}