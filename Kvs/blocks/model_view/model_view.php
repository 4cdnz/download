<?php
function model_viewShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsViewDataSiteBlockModel($object_id, $block_config))->render($smarty);
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

function model_viewGetHash($block_config)
{
	return (new KvsViewDataSiteBlockModel('', $block_config))->to_hash();
}

function model_viewCacheControl($block_config)
{
	return (new KvsViewDataSiteBlockModel('', $block_config))->get_cache_mode();
}

function model_viewMetaData()
{
	$result = [];

	$parameters = (new KvsViewDataSiteBlockModel())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function model_viewAsync($block_config)
{
	(new KvsViewDataSiteBlockModel('', $block_config))->async();
}

function model_viewPreProcess($block_config, $object_id)
{
	(new KvsViewDataSiteBlockModel($object_id, $block_config))->pre_process();
}

function model_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingModelView.js?v={$config['project_version']}";
}

function model_viewInstance(): KvsViewDataSiteBlockModel
{
	return new KvsViewDataSiteBlockModel();
}

class KvsViewDataSiteBlockModel extends KvsViewDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeModel::get_instance(), $block_uid, $block_config);
	}

	protected function map_show_next_and_previous_info_to_obsolete(string $new_show_next_and_previous_info): array
	{
		switch ($new_show_next_and_previous_info)
		{
			case 'model_id':
				return ['0'];
			case 'model_group':
				return ['1'];
		}
		return [];
	}
}