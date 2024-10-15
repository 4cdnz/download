<?php
function list_categoriesShow($block_config, $object_id)
{
	global $smarty, $storage;

	try
	{
		$result = (new KvsListDataSiteBlockCategories($object_id, $block_config))->render($smarty);
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

function list_categoriesGetHash($block_config)
{
	return (new KvsListDataSiteBlockCategories('', $block_config))->to_hash();
}

function list_categoriesCacheControl($block_config)
{
	return (new KvsListDataSiteBlockCategories('', $block_config))->get_cache_mode();
}

function list_categoriesMetaData()
{
	$result = [];

	$parameters = (new KvsListDataSiteBlockCategories())->get_parameters();
	foreach ($parameters as $parameter)
	{
		$result[] = $parameter->to_display_array();
	}
	return $result;
}

function list_categoriesInstance(): KvsListDataSiteBlockCategories
{
	return new KvsListDataSiteBlockCategories();
}

class KvsListDataSiteBlockCategories extends KvsListDataSiteBlock
{
	public function __construct(string $block_uid = '', array $block_config = [])
	{
		parent::__construct(KvsObjectTypeCategory::get_instance(), $block_uid, $block_config);
	}

	protected function map_related_mode_to_obsolete(string $new_related_mode): array
	{
		switch ($new_related_mode)
		{
			case 'category_group':
				return ['1'];
			case 'videos':
				return ['2'];
			case 'albums':
				return ['3'];
		}
		return [];
	}
}