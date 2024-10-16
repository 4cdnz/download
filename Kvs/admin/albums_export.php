<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

$table_name="$config[tables_prefix]albums";
$table_key_name="album_id";

$errors = null;

$options=get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");
set_time_limit(0);

if ($options['ALBUM_FIELD_1_NAME']=='') {$options['ALBUM_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['ALBUM_FIELD_2_NAME']=='') {$options['ALBUM_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['ALBUM_FIELD_3_NAME']=='') {$options['ALBUM_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
$languages=mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));

$ALBUMS_EXPORT_PRESETS=array();
if ($options['ALBUMS_EXPORT_PRESETS']<>'')
{
	$ALBUMS_EXPORT_PRESETS=@unserialize($options['ALBUMS_EXPORT_PRESETS']);
}
if ($_POST['preset_id']<>'' && $_POST['preset_name']=='' && $_POST['action']=='start_export')
{
	$_POST['preset_name']=$_POST['preset_id'];
}
if ($_POST['preset_name']<>'')
{
	$name=$_POST['preset_name'];

	$temp_data=$_POST;
	unset($temp_data['action'], $temp_data['data'], $temp_data['file'], $temp_data['file_hash'], $temp_data['se_file_ids']);
	$ALBUMS_EXPORT_PRESETS[$name]=$temp_data;

	if ($temp_data['is_default_preset']==1)
	{
		foreach ($ALBUMS_EXPORT_PRESETS as $k=>$preset)
		{
			if ($k<>$name && $preset['is_default_preset']==1)
			{
				$ALBUMS_EXPORT_PRESETS[$k]['is_default_preset']=0;
			}
		}
	}

	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_EXPORT_PRESETS'",serialize($ALBUMS_EXPORT_PRESETS));
}
if (!isset($_GET['preset_id']) && array_cnt($_POST)==0)
{
	foreach ($ALBUMS_EXPORT_PRESETS as $k=>$preset)
	{
		if ($preset['is_default_preset']==1)
		{
			$_GET['preset_id']=$k;
			break;
		}
	}
}
if (isset($_POST['delete_preset'], $_POST['preset_id']))
{
	unset($ALBUMS_EXPORT_PRESETS[$_POST['preset_id']]);
	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_EXPORT_PRESETS'", serialize($ALBUMS_EXPORT_PRESETS));

	$_SESSION['messages'][] = $lang['albums']['success_message_import_export_preset_removed'];

	$return_to = $page_name;
	if ($_POST['se_file_ids'])
	{
		$return_to = "$page_name?se_file_ids=" . urlencode($_POST['se_file_ids']);
	}
	return_ajax_success($return_to);
} elseif (isset($_GET['preset_id']))
{
	$_POST=$ALBUMS_EXPORT_PRESETS[$_GET['preset_id']];

	if (array_cnt($_POST['se_admin_ids'])>0)
	{
		$se_admin_ids=implode(",",array_map("intval",$_POST['se_admin_ids']));
		$_POST['admins']=mr2array(sql_pr("select user_id, login from $config[tables_prefix]admin_users where user_id in ($se_admin_ids) order by login asc"));
	}
	if (array_cnt($_POST['se_user_ids'])>0)
	{
		$se_user_ids=implode(",",array_map("intval",$_POST['se_user_ids']));
		$_POST['users']=mr2array(sql_pr("select user_id, username from $config[tables_prefix]users where user_id in ($se_user_ids) order by username asc"));
	}
	if (array_cnt($_POST['se_category_ids'])>0)
	{
		$se_category_ids=implode(",",array_map("intval",$_POST['se_category_ids']));
		$_POST['categories']=mr2array(sql_pr("select category_id, title from $config[tables_prefix]categories where category_id in ($se_category_ids) order by title asc"));
	}
	if (array_cnt($_POST['se_model_ids'])>0)
	{
		$se_model_ids=implode(",",array_map("intval",$_POST['se_model_ids']));
		$_POST['models']=mr2array(sql_pr("select model_id, title from $config[tables_prefix]models where model_id in ($se_model_ids) order by title asc"));
	}
	if (array_cnt($_POST['se_cs_ids'])>0)
	{
		$se_cs_ids=implode(",",array_map("intval",$_POST['se_cs_ids']));
		$_POST['content_sources']=mr2array(sql_pr("select content_source_id, title from $config[tables_prefix]content_sources where content_source_id in ($se_cs_ids) order by title asc"));
	}
}

if ($_GET['se_file_ids'])
{
	$_POST['se_file_ids'] = intval($_GET['se_file_ids']);
	$_POST['se_file_ids_count'] = 0;

	$select_data = @unserialize(file_get_contents("$config[temporary_path]/mass-export-" . intval($_GET['se_file_ids']) . '.dat'));
	if (is_array($select_data) && $select_data['ids'])
	{
		$_POST['se_file_ids_count'] = mr2number(sql_pr("select count(*) from $table_name where $table_key_name in (" . implode(',', array_map('intval', $select_data['ids'])) . ')'));
	}
}

$list_formats_images_main=mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=1 order by title"));
$list_formats_images_preview=mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=2 order by title"));

$list_satellites=mr2array(sql("select * from $config[tables_prefix]admin_satellites order by multi_prefix"));
foreach ($list_satellites as $k=>$satellite)
{
	$list_satellites[$k]['website_ui_data']=@unserialize($satellite['website_ui_data']);
}

if ($_POST['action']=='start_export')
{
	$is_error = 1;
	settype($_POST['fields'], 'array');
	foreach ($_POST['fields'] as $field)
	{
		if ($field != '')
		{
			$is_error = 0;
			break;
		}
	}
	if ($is_error)
	{
		$errors[] = get_aa_error('export_fields_required', $lang['albums']['export_divider_fields']);
	}

	validate_field('empty',$_POST['separator'],$lang['albums']['import_export_field_separator_fields']);
	validate_field('empty',$_POST['line_separator'],$lang['albums']['import_export_field_separator_lines']);

	if ($_POST['limit']<>'')
	{
		validate_field('empty_int',$_POST['limit'],$lang['albums']['export_field_limit']);
	}

	if ($_POST['is_post_date_range_enabled'] == 1)
	{
		validate_field('calendar_range', $_POST, $lang['albums']['export_field_post_date_range'], ['is_required' => 1, 'same_allowed' => 1, 'range_start' => 'post_date_from', 'range_end' => 'post_date_to']);
	}
	if ($_POST['is_added_date_range_enabled'] == 1)
	{
		validate_field('calendar_range', $_POST, $lang['albums']['export_field_added_date_range'], ['is_required' => 1, 'same_allowed' => 1, 'range_start' => 'added_date_from', 'range_end' => 'added_date_to']);
	}
	if ($_POST['is_id_range_enabled'] == 1)
	{
		validate_field('int_range', $_POST, $lang['albums']['export_field_id_range'], ['is_required' => 1, 'same_allowed' => 1, 'range_start' => 'id_range_from', 'range_end' => 'id_range_to']);
	}

	$post_date_selector='post_date';
	if ($config['relative_post_dates']=="true")
	{
		$now_date=date("Y-m-d H:i:s");
		$post_date_selector="(case when relative_post_date!=0 then date_add('$now_date', interval relative_post_date-1 day) else post_date end)";
	}
	$title_selector='title';
	$desc_selector='description';
	$tag_selector='tag';
	$locale_field_title='title';
	$locale_field_desc='description';
	if ($_POST['language']<>'')
	{
		foreach ($languages as $language)
		{
			if ($_POST['language']==$language['code'])
			{
				$title_selector="(case when title_$language[code]<>'' then title_$language[code] else title end)";
				$desc_selector="(case when description_$language[code]<>'' then description_$language[code] else description end)";
				$tag_selector="(case when tag_$language[code]<>'' then tag_$language[code] else tag end)";
				$locale_field_title="title_$language[code]";
				$locale_field_desc="description_$language[code]";
				break;
			}
		}
	}

	if (!is_array($errors))
	{
		$separator = str_replace(array("\\r", "\\n", "\\t"), array("\r", "\n", "\t"), $_POST['separator']);
		$line_separator= str_replace(array("\\r", "\\n", "\\t"), array("\r", "\n", "\t"), $_POST['line_separator']);

		switch ($_POST['order_by'])
		{
			case 'album_id':$order_by="album_id";break;
			case 'title':$order_by="$title_selector";break;
			case 'description':$order_by="$desc_selector";break;
			case 'content_source':$order_by="content_source_title";break;
			case 'rating':$order_by="rating";break;
			case 'album_viewed':$order_by="album_viewed";break;
			case 'user':$order_by="user_title";break;
			case 'custom_1':$order_by="custom1";break;
			case 'custom_2':$order_by="custom2";break;
			case 'custom_3':$order_by="custom3";break;
			case 'rand':$order_by="rand()";break;
			default:$order_by="post_date";break;
		}
		if ($order_by<>"rand()")
		{
			$order_direction=$_POST['order_direction'];
			if ($order_direction<>'asc') {$order_direction="desc";}

			if ($order_by=='post_date')
			{
				$order_by="$post_date_selector $order_direction, album_id $order_direction";
			} elseif ($order_by=='rating')
			{
				$order_by="$table_name.rating/$table_name.rating_amount $order_direction, $table_name.rating_amount $order_direction";
			} else {
				$order_by.=" $order_direction";
			}
		}
		if (intval($_POST['limit'])>0)
		{
			$limit="limit ".intval($_POST['limit']);
		}

		foreach ($_POST['fields'] as $field)
		{
			if ($field == 'categories')
			{
				$is_categories_selected = 1;
			}
			if ($field == 'models')
			{
				$is_models_selected = 1;
			}
			if ($field == 'tags')
			{
				$is_tags_selected = 1;
			}
		}

		$where='';

		if ($_POST['se_file_ids'])
		{
			$select_data = @unserialize(file_get_contents("$config[temporary_path]/mass-export-" . intval($_POST['se_file_ids']) . '.dat'));
			if (is_array($select_data) && $select_data['ids'])
			{
				$where .= " and $table_key_name in (" . implode(',', array_map('intval', $select_data['ids'])) . ')';
			}
		}

		if ($_POST['se_text']!='')
		{
			$q=sql_escape($_POST['se_text']);
			$where.=" and ($locale_field_title like '%$q%' or $locale_field_desc like '%$q%') ";
		}

		if (array_cnt($_POST['se_admin_ids'])>0)
		{
			$se_admin_ids=implode(",",array_map("intval",$_POST['se_admin_ids']));
			$where.=" and admin_user_id in ($se_admin_ids)";
		}
		if (array_cnt($_POST['se_user_ids'])>0)
		{
			$se_user_ids=implode(",",array_map("intval",$_POST['se_user_ids']));
			$where.=" and user_id in ($se_user_ids)";
		}
		if (array_cnt($_POST['se_category_ids'])>0)
		{
			$se_category_ids=implode(",",array_map("intval",$_POST['se_category_ids']));
			$where.=" and exists (select category_id from $config[tables_prefix]categories_albums where album_id=$table_name.album_id and category_id in ($se_category_ids))";
		}
		if (array_cnt($_POST['se_model_ids'])>0)
		{
			$se_model_ids=implode(",",array_map("intval",$_POST['se_model_ids']));
			$where.=" and exists (select model_id from $config[tables_prefix]models_albums where album_id=$table_name.album_id and model_id in ($se_model_ids))";
		}
		if ($_POST['se_tags']!='')
		{
			$tag_ids=array('0');
			$temp=explode(",",$_POST['se_tags']);

			foreach ($temp as $temp_tag)
			{
				$temp_tag=trim($temp_tag);
				if ($temp_tag=='') {continue;}

				$tag_id=mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?",$temp_tag));
				if ($tag_id>0)
				{
					$tag_ids[]=$tag_id;
				}
			}
			$tag_ids=implode(",",$tag_ids);
			$where.=" and exists (select tag_id from $config[tables_prefix]tags_albums where album_id=$table_name.album_id and tag_id in ($tag_ids))";
		}
		if (array_cnt($_POST['se_cs_ids'])>0)
		{
			$se_cs_ids=implode(",",array_map("intval",$_POST['se_cs_ids']));
			$where.=" and content_source_id in ($se_cs_ids)";
		}

		if ($_POST['se_status_id']=='0') {$where.=" and status_id=0";} else
		if ($_POST['se_status_id']=='1') {$where.=" and status_id=1";} else
		if ($_POST['se_status_id']=='2') {$where.=" and status_id=2";} else
		if ($_POST['se_status_id']=='3') {$where.=" and status_id=3";}
		if ($_POST['se_review_flag']=='1') {$where.=" and is_review_needed=1";} else
		if ($_POST['se_review_flag']=='2') {$where.=" and is_review_needed=0";}
		if ($_POST['se_is_private']=='0') {$where.=" and is_private=0";} else
		if ($_POST['se_is_private']=='1') {$where.=" and is_private=1";} else
		if ($_POST['se_is_private']=='2') {$where.=" and is_private=2";}
		if (intval($_POST['se_admin_flag_id'])>0) {$where.=" and admin_flag_id=".intval($_POST['se_admin_flag_id']);}
		if ($_POST['is_post_date_range_enabled'] == 1)
		{
			if ($_POST['post_date_from'] != '' && $_POST['post_date_from'] != '0000-00-00' && $_POST['post_date_from'] != '0000-00-00 00:00:00')
			{
				$where .= " and $post_date_selector>='" . date('Y-m-d 00:00:00', strtotime($_POST['post_date_from'])) . "'";
			}
			if ($_POST['post_date_to'] != '' && $_POST['post_date_to'] != '0000-00-00' && $_POST['post_date_to'] != '0000-00-00 00:00:00')
			{
				$where .= " and $post_date_selector<='" . date('Y-m-d 23:59:59', strtotime($_POST['post_date_to'])) . "'";
			}
		}
		if ($_POST['is_id_range_enabled'] == 1)
		{
			$id_from = intval($_POST["id_range_from"]);
			$id_to = intval($_POST["id_range_to"]);
			if ($id_from > 0)
			{
				$where .= " and album_id>='$id_from' ";
			}
			if ($id_to > 0)
			{
				$where .= " and album_id<='$id_to' ";
			}
		}
		if ($_POST['is_added_date_range_enabled'] == 1)
		{
			if ($_POST['added_date_from'] != '' && $_POST['added_date_from'] != '0000-00-00' && $_POST['added_date_from'] != '0000-00-00 00:00:00')
			{
				$where .= " and added_date>='" . date('Y-m-d 00:00:00', strtotime($_POST['added_date_from'])) . "'";
			}
			if ($_POST['added_date_to'] != '' && $_POST['added_date_to'] != '0000-00-00' && $_POST['added_date_to'] != '0000-00-00 00:00:00')
			{
				$where .= " and added_date<='" . date('Y-m-d 23:59:59', strtotime($_POST['added_date_to'])) . "'";
			}
		}
		if ($_POST['se_status_id']=='') {$where.=" and status_id in(0,1) ";}
		if ($_POST['is_post_time_considered']==1) {$now_date=date("Y-m-d H:i:s");$where.=" and $post_date_selector<='$now_date' ";}

		if ($_SESSION['userdata']['is_access_to_own_content']==1)
		{
			$admin_id=intval($_SESSION['userdata']['user_id']);
			$where.=" and admin_user_id=$admin_id ";
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content']==1)
		{
			$where.=" and status_id=0 ";
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
		}
		if ($where!='') {$where=" where ".substr($where,4);}

		$export_id=mt_rand(10000000,99999999);
		$export_filename="$config[temporary_path]/export-$export_id.dat";

		$categorization_status_ids = '0,1';
		if (intval($_POST['se_categorization_status_id']) == 1)
		{
			$categorization_status_ids = '1';
		}

		$data=sql("select *, $title_selector as title, $desc_selector as description, $post_date_selector as post_date,
			(select $title_selector from $config[tables_prefix]content_sources where content_source_id=$table_name.content_source_id and status_id in ($categorization_status_ids)) as content_source_title,
			(select url from $config[tables_prefix]content_sources where content_source_id=$table_name.content_source_id and status_id in ($categorization_status_ids)) as content_source_url,
			(select username from $config[tables_prefix]users where user_id=$table_name.user_id) as user_title,
			(select title from $config[tables_prefix]flags where flag_id=$table_name.admin_flag_id) as flag_title,
			coalesce(format(rating/rating_amount,1),0) as rating,
			coalesce(ceil(rating/rating_amount*20),0) as rating_percent
		from $table_name $where order by $order_by $limit");

		if (intval($_POST['is_header_row'])==1)
		{
			$export_result_str='';
			foreach ($_POST['fields'] as $field)
			{
				switch ($field)
				{
					case 'album_id':$export_result_str.="{$separator}{$lang['albums']['import_export_field_id']}";break;
					case 'title':$export_result_str.="{$separator}{$lang['albums']['import_export_field_title']}";break;
					case 'directory':$export_result_str.="{$separator}{$lang['albums']['import_export_field_directory']}";break;
					case 'description':$export_result_str.="{$separator}{$lang['albums']['import_export_field_description']}";break;
					case 'categories':$export_result_str.="{$separator}{$lang['albums']['import_export_field_categories']}";break;
					case 'models':$export_result_str.="{$separator}{$lang['albums']['import_export_field_models']}";break;
					case 'tags':$export_result_str.="{$separator}{$lang['albums']['import_export_field_tags']}";break;
					case 'content_source':$export_result_str.="{$separator}{$lang['albums']['import_export_field_content_source']}";break;
					case 'content_source/url':$export_result_str.="{$separator}{$lang['albums']['import_export_field_content_source_url']}";break;
					case 'website_link':$export_result_str.="{$separator}{$lang['albums']['import_export_field_website_link']}";break;
					case 'post_date':$export_result_str.="{$separator}{$lang['albums']['import_export_field_post_date']}";break;
					case 'added_date':$export_result_str.="{$separator}{$lang['albums']['import_export_field_added_date']}";break;
					case 'rating':$export_result_str.="{$separator}{$lang['albums']['import_export_field_rating']}";break;
					case 'rating_percent':$export_result_str.="{$separator}{$lang['albums']['import_export_field_rating_percent']}";break;
					case 'rating_amount':$export_result_str.="{$separator}{$lang['albums']['import_export_field_rating_amount']}";break;
					case 'album_viewed':$export_result_str.="{$separator}{$lang['albums']['import_export_field_visits']}";break;
					case 'user':$export_result_str.="{$separator}{$lang['albums']['import_export_field_user']}";break;
					case 'status':$export_result_str.="{$separator}{$lang['albums']['import_export_field_status']}";break;
					case 'type':$export_result_str.="{$separator}{$lang['albums']['import_export_field_type']}";break;
					case 'access_level':$export_result_str.="{$separator}{$lang['albums']['import_export_field_access_level']}";break;
					case 'tokens':$export_result_str.="{$separator}{$lang['albums']['import_export_field_tokens_cost']}";break;
					case 'admin_flag':$export_result_str.="{$separator}{$lang['albums']['import_export_field_admin_flag']}";break;
					case 'custom_1':$export_result_str.="{$separator}{$options['ALBUM_FIELD_1_NAME']}";break;
					case 'custom_2':$export_result_str.="{$separator}{$options['ALBUM_FIELD_2_NAME']}";break;
					case 'custom_3':$export_result_str.="{$separator}{$options['ALBUM_FIELD_3_NAME']}";break;
					case 'gallery_url':$export_result_str.="{$separator}{$lang['albums']['import_export_field_gallery_url']}";break;
					case 'image_preview_source':$export_result_str.="{$separator}{$lang['albums']['import_export_field_image_preview_source']}";break;
					case 'main_images_sources':$export_result_str.="{$separator}{$lang['albums']['import_export_field_images_main_sources']}";break;
				}
				foreach ($list_satellites as $satellite)
				{
					if ($field=="website_link/{$satellite['multi_prefix']}")
					{
						$export_result_str.="{$separator}{$lang['albums']['import_export_field_website_link']} ($satellite[project_url])";
						break;
					}
				}
				foreach ($languages as $language)
				{
					if ($field=="title_{$language['code']}")
					{
						$export_result_str.="{$separator}{$lang['albums']['import_export_field_title']} ($language[title])";
						break;
					} elseif ($field=="description_{$language['code']}")
					{
						$export_result_str.="{$separator}{$lang['albums']['import_export_field_description']} ($language[title])";
						break;
					} elseif ($field=="directory_{$language['code']}")
					{
						$export_result_str.="{$separator}{$lang['albums']['import_export_field_directory']} ($language[title])";
						break;
					}
				}
				foreach ($list_formats_images_preview as $format_image)
				{
					if ($field=="image_preview_{$format_image['format_album_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_image['title'],$lang['albums']['import_export_field_image_preview_format']);
						break;
					}
				}
				foreach ($list_formats_images_main as $format_image)
				{
					if ($field=="main_images_{$format_image['format_album_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_image['title'],$lang['albums']['import_export_field_images_main_format']);
						break;
					}
					if ($field=="main_images_zip_{$format_image['format_album_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_image['title'],$lang['albums']['import_export_field_images_main_format_zip']);
						break;
					}
				}
			}
			$export_result_str='#'.substr($export_result_str,strlen($separator));
			file_put_contents($export_filename,"{$export_result_str}$line_separator",FILE_APPEND);
		}
		while ($res = mr2array_single($data))
		{
			$album_id=$res['album_id'];
			$dir_path=get_dir_by_id($album_id);
			$export_result_str="";

			if ($is_categories_selected==1)
			{
				$category_titles=mr2array_list(sql_pr("select $title_selector from $config[tables_prefix]categories_albums inner join $config[tables_prefix]categories using (category_id) where album_id=? and status_id in ($categorization_status_ids) order by id asc", $album_id));
				foreach ($category_titles as $k=>$v)
				{
					if (strpos($v, ',')!==false)
					{
						$category_titles[$k]=str_replace(",","\\,",$v);
					}
				}
				$res['categories']=implode(", ",$category_titles);
			}
			if ($is_models_selected==1)
			{
				$model_titles=mr2array_list(sql_pr("select $title_selector from $config[tables_prefix]models_albums inner join $config[tables_prefix]models using (model_id) where album_id=? and status_id in ($categorization_status_ids) order by id asc", $album_id));
				foreach ($model_titles as $k=>$v)
				{
					if (strpos($v, ',')!==false)
					{
						$model_titles[$k]=str_replace(",","\\,",$v);
					}
				}
				$res['models']=implode(", ",$model_titles);
			}
			if ($is_tags_selected==1)
			{
				$res['tags']=implode(", ",mr2array_list(sql_pr("select $tag_selector from $config[tables_prefix]tags_albums inner join $config[tables_prefix]tags using (tag_id) where album_id=? and status_id in ($categorization_status_ids) order by id asc", $album_id)));
			}

			foreach ($_POST['fields'] as $field)
			{
				if ($res['is_private']==1)
				{
					$res['type_title']="Private";
				} elseif ($res['is_private']==2)
				{
					$res['type_title']="Premium";
				} else {
					$res['type_title']="Public";
				}

				if ($res['status_id']=='1')
				{
					$res['status_title']="Active";
				} elseif ($res['status_id']=='0')
				{
					$res['status_title']="Disabled";
				} else {
					$res['status_title']="Error";
				}

				if ($res['access_level_id']=='1')
				{
					$res['access_level_title']="All";
				} elseif ($res['access_level_id']=='2')
				{
					$res['access_level_title']="Members";
				} elseif ($res['access_level_id']=='3')
				{
					$res['access_level_title']="Premium";
				} else {
					$res['access_level_title']="Inherit";
				}

				$res['images']=mr2array_list(sql("select image_id from $config[tables_prefix]albums_images where album_id=$album_id order by image_id asc"));

				switch ($field)
				{
					case 'album_id':$export_result_str.="{$separator}$album_id";break;
					case 'title':$export_result_str.="{$separator}$res[title]";break;
					case 'directory':$export_result_str.="{$separator}$res[dir]";break;
					case 'description':$export_result_str.="{$separator}".str_replace(["\n", "\r", "\t"]," ","$res[description]");break;
					case 'categories':$export_result_str.="{$separator}$res[categories]";break;
					case 'models':$export_result_str.="{$separator}$res[models]";break;
					case 'tags':$export_result_str.="{$separator}$res[tags]";break;
					case 'content_source':$export_result_str.="{$separator}$res[content_source_title]";break;
					case 'content_source/url':$export_result_str.="{$separator}$res[content_source_url]";break;
					case 'website_link':
						if ($res['dir']<>'')
						{
							$export_result_str.="{$separator}$config[project_url]/".str_replace("%ID%",$album_id,str_replace("%DIR%",$res['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
						} else {
							$export_result_str.="$separator";
						}
						break;
					case 'post_date':$export_result_str.="{$separator}$res[post_date]";break;
					case 'added_date':$export_result_str.="{$separator}$res[added_date]";break;
					case 'rating':$export_result_str.="{$separator}$res[rating]";break;
					case 'rating_percent':$export_result_str.="{$separator}$res[rating_percent]%";break;
					case 'rating_amount':$export_result_str.="{$separator}$res[rating_amount]";break;
					case 'album_viewed':$export_result_str.="{$separator}$res[album_viewed]";break;
					case 'user':$export_result_str.="{$separator}$res[user_title]";break;
					case 'status':$export_result_str.="{$separator}$res[status_title]";break;
					case 'type':$export_result_str.="{$separator}$res[type_title]";break;
					case 'access_level':$export_result_str.="{$separator}$res[access_level_title]";break;
					case 'tokens':$export_result_str.="{$separator}$res[tokens_required]";break;
					case 'admin_flag':$export_result_str.="{$separator}$res[flag_title]";break;
					case 'custom_1':$export_result_str.="{$separator}".str_replace(["\n", "\r", "\t"]," ","$res[custom1]");break;
					case 'custom_2':$export_result_str.="{$separator}".str_replace(["\n", "\r", "\t"]," ","$res[custom2]");break;
					case 'custom_3':$export_result_str.="{$separator}".str_replace(["\n", "\r", "\t"]," ","$res[custom3]");break;
					case 'gallery_url':
						$export_result_str.="{$separator}$res[gallery_url]";
						break;
					case 'image_preview_source':
						$file_path="sources/$dir_path/$album_id/$res[main_photo_id].jpg";
						$hash=md5($config['cv'].$file_path);
						$export_result_str.="{$separator}$config[project_url]/get_image/$res[server_group_id]/$hash/$file_path/";
						break;
					case 'main_images_sources':
						$export_result_str.="$separator";
						$is=0;
						foreach ($res['images'] as $image_id)
						{
							$file_path="sources/$dir_path/$album_id/$image_id.jpg";
							$hash=md5($config['cv'].$file_path);
							$export_result_str.="$config[project_url]/get_image/$res[server_group_id]/$hash/$file_path/";
							if ($is<array_cnt($res['images'])-1)
							{
								$export_result_str.=",";
							}
							$is++;
						}
						break;
				}
				foreach ($list_satellites as $satellite)
				{
					if ($field=="website_link/{$satellite['multi_prefix']}")
					{
						if ($res['dir']!='' && $satellite['website_ui_data']['WEBSITE_LINK_PATTERN_ALBUM']!='')
						{
							$satellite_dir=$res['dir'];
							if ($satellite['website_ui_data']['locale']!='' && $res['dir_'.$satellite['website_ui_data']['locale']]!='')
							{
								$satellite_dir=$res['dir_'.$satellite['website_ui_data']['locale']];
							}
							$export_result_str.="{$separator}$satellite[project_url]/".str_replace("%ID%",$album_id,str_replace("%DIR%",$satellite_dir,$satellite['website_ui_data']['WEBSITE_LINK_PATTERN_ALBUM']));
						} else {
							$export_result_str.="$separator";
						}
						break;
					}
				}
				foreach ($languages as $language)
				{
					if ($field=="title_{$language['code']}")
					{
						$export_result_str.="{$separator}{$res["title_$language[code]"]}";
						break;
					} elseif ($field=="description_{$language['code']}")
					{
						$export_result_str.="{$separator}{$res["description_$language[code]"]}";
						break;
					} elseif ($field=="directory_{$language['code']}")
					{
						$export_result_str.="{$separator}{$res["dir_$language[code]"]}";
						break;
					}
				}
				foreach ($list_formats_images_preview as $format_image)
				{
					if ($field=="image_preview_{$format_image['format_album_id']}")
					{
						$file_path="preview/$format_image[size]/$dir_path/$album_id/preview.jpg";
						$hash=md5($config['cv'].$file_path);
						$export_result_str.="{$separator}$config[project_url]/get_image/$res[server_group_id]/$hash/$file_path/";
						break;
					}
				}
				foreach ($list_formats_images_main as $format_image)
				{
					if ($field=="main_images_{$format_image['format_album_id']}")
					{
						$export_result_str.="$separator";
						$is=0;
						foreach ($res['images'] as $image_id)
						{
							$file_path="main/$format_image[size]/$dir_path/$album_id/$image_id.jpg";
							$hash=md5($config['cv'].$file_path);
							$export_result_str.="$config[project_url]/get_image/$res[server_group_id]/$hash/$file_path/";
							if ($is<array_cnt($res['images'])-1)
							{
								$export_result_str.=",";
							}
							$is++;
						}
						break;
					}
					if ($field=="main_images_zip_{$format_image['format_album_id']}")
					{
						$export_result_str.="$separator";
						$file_path="main/$format_image[size]/$dir_path/$album_id/$album_id-$format_image[size].zip";
						$hash=md5($config['cv'].$file_path);
						$export_result_str.="$config[project_url]/get_image/$res[server_group_id]/$hash/$file_path/";
						break;
					}
				}
			}

			$export_result_str=substr($export_result_str,strlen($separator));
			file_put_contents($export_filename,"{$export_result_str}$line_separator",FILE_APPEND);
		}

		return_ajax_success("$page_name?action=export_as_file&export_id=$export_id");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_REQUEST['action'] == 'export_as_file')
{
	$export_id = intval($_REQUEST['export_id']);
	if ($export_id > 0)
	{
		$export_date = date("Y-m-d_H-i");
		$export_file = "$config[temporary_path]/export-$export_id.dat";
		header('Content-type: text/plain; charset=utf-8');
		header("Content-Disposition: attachment; filename=\"export_data_$export_date.txt\"");
		if (is_file($export_file) && filesize($export_file) > 0)
		{
			header("Content-Length: " . filesize($export_file));
			readfile($export_file);
		} else
		{
			header("Content-Length: " . strlen($lang['albums']['export_result_no_data']));
			echo $lang['albums']['export_result_no_data'];
		}
	}
	die;
}

if (array_cnt($_POST['fields']) == 0)
{
	$_POST['fields'] = [];
	for ($i = 1; $i <= 999; $i++)
	{
		if (isset($_POST["field{$i}"]))
		{
			$_POST['fields'][] = $_POST["field{$i}"];
		} else
		{
			break;
		}
	}
	if (array_cnt($_POST['fields']) == 0)
	{
		$_POST['fields'] = ['', '', '', '', ''];
	}
}

$smarty=new mysmarty();
$smarty->assign('options',$options);
$smarty->assign('list_formats_images_main',$list_formats_images_main);
$smarty->assign('list_formats_images_preview',$list_formats_images_preview);
$smarty->assign('list_satellites',$list_satellites);
$smarty->assign('list_flags_admins',mr2array(sql("select * from $config[tables_prefix]flags where group_id=2 and is_admin_flag=1 order by title asc")));
$smarty->assign('list_presets',$ALBUMS_EXPORT_PRESETS);
$smarty->assign('list_languages',$languages);

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

$smarty->assign('page_title',$lang['albums']['export_header_export']);

$content_scheduler_days=intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days='';
	$sorting_content_scheduler_days='desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$now_date = date("Y-m-d 00:00:00");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days='asc';
	}
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]albums where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
