<?php
function list_postsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data,$list_countries;

	if ($_REQUEST['action'] == 'delete_posts' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id'] > 0)
		{
			$user_id = intval($_SESSION['user_id']);
			$delete_ids = implode(",", array_map("intval", $_REQUEST['delete']));
			$delete_ids = mr2array_list(sql("select post_id from $config[tables_prefix]posts where user_id=$user_id and post_id in ($delete_ids) and is_locked=0"));
			if (array_cnt($delete_ids) > 0)
			{
				$delete_ids_str = implode(",", $delete_ids);

				if (isset($block_config['allow_delete_created_posts']))
				{
					$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=12"));
					$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

					$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $config[tables_prefix]categories_posts where post_id in ($delete_ids_str)")));
					$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $config[tables_prefix]models_posts where post_id in ($delete_ids_str)")));
					$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $config[tables_prefix]tags_posts where post_id in ($delete_ids_str)")));

					sql("delete from $config[tables_prefix]posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]tags_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]categories_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]models_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]rating_history where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_history where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_messages where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]users_events where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=12");

					if (strlen($list_ids_comments) > 0)
					{
						sql("update $config[tables_prefix]users set
							comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
							comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_comments)"
						);
					}

					require_once("$config[project_path]/admin/include/functions_admin.php");
					update_tags_posts_totals($list_ids_tags);
					update_categories_posts_totals($list_ids_categories);
					update_models_posts_totals($list_ids_models);
					update_users_posts_totals([$_SESSION['user_id']]);

					foreach ($delete_ids as $delete_id)
					{
						$dir_path = get_dir_by_id($delete_id);
						$custom_files = get_contents_from_dir("$config[content_path_posts]/$dir_path/$delete_id", 1);
						foreach ($custom_files as $custom_file)
						{
							@unlink("$config[content_path_posts]/$dir_path/$delete_id/$custom_file");
						}
						@rmdir("$config[content_path_posts]/$dir_path/$delete_id");
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=12, added_date=?", $_SESSION['user_id'], $_SESSION['username'], $delete_id, date("Y-m-d H:i:s"));
					}
				} else
				{
					if ($_REQUEST['mode'] == 'async')
					{
						async_return_request_status(array(array('error_code' => 'delete_forbidden', 'block' => 'list_posts')));
					} else
					{
						header("Location: ?action=delete_forbidden");
						die;
					}
				}
			}
			if ($_REQUEST['mode'] == 'async')
			{
				async_return_request_status();
			} else
			{
				header("Location: ?action=delete_done");
				die;
			}
		} elseif ($_REQUEST['mode'] == 'async')
		{
			async_return_request_status(array(array('error_code' => 'not_logged_in', 'block' => 'list_posts')));
		}
	}

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$post_types=array();
	$post_types_temp=mr2array(sql("select * from $config[tables_prefix]posts_types"));
	foreach ($post_types_temp as $post_type_temp)
	{
		$post_types[$post_type_temp['post_type_id']]=$post_type_temp;
	}

	$where='';
	$where_posts="$database_selectors[where_posts]";
	$sort_by_relevance='';

	if ($block_config['post_type']!='' || (isset($block_config['var_post_type']) && $_REQUEST[$block_config['var_post_type']]!=''))
	{
		$post_type_external_id=$block_config['post_type'];
		if (isset($block_config['var_post_type']) && $_REQUEST[$block_config['var_post_type']]!='')
		{
			$post_type_external_id=$_REQUEST[$block_config['var_post_type']];
		}

		$is_known_post_type=false;
		foreach ($post_types as $post_type)
		{
			if ($post_type['external_id']==$post_type_external_id)
			{
				$is_known_post_type=true;
				$where.=" and post_type_id=".intval($post_type['post_type_id']);

				$storage[$object_id]['post_type_info']=$post_type;
				$smarty->assign('post_type_info',$post_type);
			}
		}
		if (!$is_known_post_type)
		{
			return 'status_404';
		}
	}

	if (isset($block_config['var_post_date_from']) && trim($_REQUEST[$block_config['var_post_date_from']]) != '')
	{
		$date_from = trim($_REQUEST[$block_config['var_post_date_from']]);
		if (strpos($date_from, '-') !== false)
		{
			$date_from_converted = explode('-', $date_from);
		} else
		{
			$date_from_converted = explode('-', date('Y-m-d', time() - intval($date_from) * 86400));
		}
		if (array_cnt($date_from_converted) >= 3)
		{
			$date_from_converted = date("Y-m-d 00:00:00", mktime(0, 0, 0, intval($date_from_converted[1]), intval($date_from_converted[2]), intval($date_from_converted[0])));
			$where .= " and $database_selectors[generic_post_date_selector] >= '$date_from_converted'";

			$storage[$object_id]['post_date_from'] = $date_from;
			$smarty->assign('post_date_from', $date_from);
		}
	}
	if (isset($block_config['var_post_date_to']) && trim($_REQUEST[$block_config['var_post_date_to']]) != '')
	{
		$date_to = trim($_REQUEST[$block_config['var_post_date_to']]);
		if (strpos($date_to, '-') !== false)
		{
			$date_to_converted = explode('-', $date_to);
		} else
		{
			$date_to_converted = explode('-', date('Y-m-d', time() - intval($date_to) * 86400));
		}
		if (array_cnt($date_to_converted) >= 3)
		{
			$date_to_converted = date("Y-m-d 23:59:59", mktime(0, 0, 0, intval($date_to_converted[1]), intval($date_to_converted[2]), intval($date_to_converted[0])));
			$where .= " and $database_selectors[generic_post_date_selector] <= '$date_to_converted'";

			$storage[$object_id]['post_date_to'] = $date_to;
			$smarty->assign('post_date_to', $date_to);
		}
	}

	$join_tables=array();

	if (isset($block_config['mode_created']))
	{
		$my_mode_created=false;
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$my_mode_created=true;

			$where_posts="$database_selectors[where_posts_active_disabled]";
		} else {
			if ($_GET['mode']=='async')
			{
				header('HTTP/1.0 403 Forbidden');die;
			}

			$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
			if (isset($block_config['redirect_unknown_user_to']))
			{
				$url=process_url($block_config['redirect_unknown_user_to']);
				return "status_302: $url";
			} else
			{
				return "status_302: $config[project_url]";
			}
		}

		$user_info=mr2array_single(sql("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=$user_id"));
		if (!empty($user_info))
		{
			$where.=" and user_id=$user_id ";

			$smarty->assign("user_id",$user_id);
			$smarty->assign("username",$user_info['username']);
			$smarty->assign("display_name",$user_info['display_name']);
			$smarty->assign("avatar",$user_info['avatar']);
			$smarty->assign("gender_id",$user_info['gender_id']);
			$smarty->assign("city",$user_info['city']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['username']=$user_info['username'];
			$storage[$object_id]['display_name']=$user_info['display_name'];
			$storage[$object_id]['avatar']=$user_info['avatar'];
			$storage[$object_id]['gender_id']=$user_info['gender_id'];
			$storage[$object_id]['city']=$user_info['city'];
			if ($user_info['country_id']>0)
			{
				$smarty->assign("country_id",$user_info['country_id']);
				$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
				$storage[$object_id]['country_id']=$user_info['country_id'];
				$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
			}

			if ($my_mode_created)
			{
				$smarty->assign("can_manage",1);
				$storage[$object_id]['can_manage']=1;
				if (isset($block_config['allow_delete_created_posts']))
				{
					$smarty->assign("can_delete",1);
					$storage[$object_id]['can_delete']=1;
				} else
				{
					$smarty->assign("can_delete",0);
					$storage[$object_id]['can_delete']=0;
				}
			}
		} else {
			return 'status_404';
		}
	} elseif (intval($block_config['mode_related'])>0 || (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0))
	{
		//1 - tags
		//2 - categories
		//3 - models
		//4-5 - title

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$result=null;
		if (isset($block_config['var_post_id']) && intval($_REQUEST[$block_config['var_post_id']])>0)
		{
			$result=sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where post_id=?",intval($_REQUEST[$block_config['var_post_id']]));
		} elseif (trim($_REQUEST[$block_config['var_post_dir']])<>'')
		{
			$result=sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where dir=?",trim($_REQUEST[$block_config['var_post_dir']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$mode_related_name='';
			$data_temp=mr2array_single($result);
			$post_id=$data_temp["post_id"];

			$where.=" and $config[tables_prefix]posts.post_id<>$post_id";
			if ($mode_related==1)
			{
				$mode_related_name='tags';

				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags_posts where post_id=?",$post_id));
				if (array_cnt($tag_ids)>0)
				{
					$tag_ids=implode(",",$tag_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)";
				}
			} elseif ($mode_related==2)
			{
				$mode_related_name='categories';

				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_posts where post_id=?",$post_id));
				if (array_cnt($category_ids)>0 && isset($block_config['mode_related_category_group_id']))
				{
					$category_ids=implode(',',$category_ids);
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_id in ($category_ids) and (category_group_id=? or category_group_id in (select category_group_id from $config[tables_prefix]categories_groups where external_id=?))",intval($block_config['mode_related_category_group_id']),trim($block_config['mode_related_category_group_id'])));
				}
				if (array_cnt($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids)";
				}
			} elseif ($mode_related==3)
			{
				$mode_related_name='models';

				$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_posts where post_id=?",$post_id));
				if (array_cnt($model_ids)>0 && isset($block_config['mode_related_model_group_id']))
				{
					$model_ids=implode(',',$model_ids);
					$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_id in ($model_ids) and (model_group_id=? or model_group_id in (select model_group_id from $config[tables_prefix]models_groups where external_id=?))",intval($block_config['mode_related_model_group_id']),trim($block_config['mode_related_model_group_id'])));
				}
				if (array_cnt($model_ids)>0)
				{
					$model_ids=implode(",",$model_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)";
				}
			} elseif ($mode_related==4 || $mode_related==5)
			{
				$mode_related_name='title';

				$title=$data_temp["title"];
				$title=sql_escape($title);

				$search_modifier='';
				if ($mode_related==5)
				{
					$search_modifier='WITH QUERY EXPANSION';
				}
				$where.=" and MATCH(title) AGAINST('$title' $search_modifier)";
				$sort_by_relevance="MATCH(title) AGAINST('$title' $search_modifier) desc";
			}
			$storage[$object_id]['list_type']="related";
			$storage[$object_id]['related_mode']=$mode_related;
			$storage[$object_id]['related_mode_name']=$mode_related_name;
			$smarty->assign('list_type',"related");
			$smarty->assign('related_mode',$mode_related);
			$smarty->assign('related_mode_name',$mode_related_name);
		}
	} elseif (isset($block_config['mode_connected_video']))
	{
		$video_id=0;
		if (isset($block_config['var_connected_video_id']) && intval($_REQUEST[$block_config['var_connected_video_id']])>0)
		{
			$video_id=intval($_REQUEST[$block_config['var_connected_video_id']]);
		} elseif (trim($_REQUEST[$block_config['var_connected_video_dir']])<>'')
		{
			$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_connected_video_dir']]),trim($_REQUEST[$block_config['var_connected_video_dir']])));
		}
		$where.=" and connected_video_id=$video_id";
	}

	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$q=sql_escape(trim($_REQUEST[$block_config['var_title_section']]));
		$where.=" and title like '$q%'";

		$storage[$object_id]['list_type']="title_section";
		$storage[$object_id]['title_section']=trim($_REQUEST[$block_config['var_title_section']]);
		$smarty->assign('list_type',"title_section");
		$smarty->assign('title_section',trim($_REQUEST[$block_config['var_title_section']]));
	}

	for ($i=1;$i<=10;$i++)
	{
		if (isset($block_config["var_custom$i"]) && trim($_REQUEST[$block_config["var_custom$i"]])!='')
		{
			$where.=" and custom$i='".sql_escape(trim($_REQUEST[$block_config["var_custom$i"]]))."'";
		}
	}

	for ($i=1;$i<=3;$i++)
	{
		if (isset($block_config["var_custom_flag$i"]) && trim($_REQUEST[$block_config["var_custom_flag$i"]])!='')
		{
			if (strpos(trim($_REQUEST[$block_config["var_custom_flag$i"]]),',')!==false)
			{
				$where.=" and af_custom$i in (".implode(",",array_map("intval",explode(",",trim($_REQUEST[$block_config["var_custom_flag$i"]])))).")";
			} else {
				$where.=" and af_custom$i=".intval($_REQUEST[$block_config["var_custom_flag$i"]]);
			}
		}
	}

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',          'plural'=>'models',            'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true, 'where_single'=>$database_selectors['where_models_active_disabled'],            'where_plural'=>$database_selectors['where_models'],            'base_files_url'=>$config['content_url_models'],               'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',    'plural'=>'models_groups',     'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_models_groups_active_disabled'],     'where_plural'=>$database_selectors['where_models_groups'],     'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true, 'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',  'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

	$dynamic_filters_types=array();
	foreach ($dynamic_filters as $df)
	{
		$df_id="{$df['single']}_id";
		$df_selector=$database_selectors[$df['plural']];
		$df_selector_locale_dir=$database_selectors["where_locale_{$df['dir']}"];
		$df_table="$config[tables_prefix]{$df['plural']}";
		$df_join_table="";
		if ($df['join_table'])
		{
			$df_join_table="$config[tables_prefix]{$df['plural']}_posts";
		}

		$df_basetable="";
		$df_basetable_id="";
		$df_join_basetable="";
		if ($df['is_group'])
		{
			$df_basetable=str_replace("_groups","",$df_table);
			$df_basetable_id=str_replace("_group","",$df_id);
			$df_join_basetable=str_replace("_groups","",$df_join_table);
		}

		$df_var_id="var_{$df['single']}_id";
		$df_var_ids="var_{$df['single']}_ids";
		$df_var_dir="var_{$df['single']}_dir";
		if (isset($block_config[$df_var_ids]) && $_REQUEST[$block_config[$df_var_ids]]<>'')
		{
			$df_ids_value=$_REQUEST[$block_config[$df_var_ids]];
			$df_where_plural=$df['where_plural'];
			if (!$df_where_plural)
			{
				$df_where_plural='1=1';
			}
			if (strpos($df_ids_value,"|")!==false)
			{
				$ids_groups=explode("|",$df_ids_value);
				$df_ids_value=array(0);
				foreach ($ids_groups as $ids_group)
				{
					$ids_group=array_map("intval",explode(",",trim($ids_group,"() ")));
					if (array_cnt($ids_group)>0)
					{
						$df_ids_value=array_merge($df_ids_value,$ids_group);
						$ids_group=implode(',',$ids_group);
						if ($df_join_table!='')
						{
							if ($df['is_group'])
							{
								$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct post_id from $df_join_table where $df_id in ($ids_group)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$where.=" and $df_id in ($ids_group)";
							}
						}
					}
				}
				$df_ids_value=implode(',',$df_ids_value);

				$df_objects=mr2array(sql_pr("select $df_selector from $df_table where $df_where_plural and $df_id in ($df_ids_value)"));
				if ($df['base_files_url']!='' || $df['link_pattern']!='')
				{
					foreach ($df_objects as $k=>$v)
					{
						if ($df['base_files_url']!='')
						{
							$df_objects[$k]['base_files_url']=$df['base_files_url'].'/'.$v[$df_id];
						}
						if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
						{
							$pattern=str_replace("%ID%",$v[$df_id],str_replace("%DIR%",$v[$df['dir']],$website_ui_data[$df['link_pattern']]));
							$df_objects[$k]['view_page_url']="$config[project_url]/$pattern";
						}
					}
				}

				$storage[$object_id]["list_type"]="multi_{$df['plural']}";
				$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
				$smarty->assign("list_type","multi_{$df['plural']}");
				$smarty->assign("{$df['plural']}_info",$df_objects);
				$dynamic_filters_types[]="multi_{$df['plural']}";
			} else {
				$df_all_met=false;
				$df_ids_value=explode(",",trim($df_ids_value,"() "));
				if (in_array('all',$df_ids_value))
				{
					$df_all_met=true;
				}
				$df_ids_value=array_map("intval",$df_ids_value);
				if (array_cnt($df_ids_value)>0)
				{
					if ($df_all_met)
					{
						foreach ($df_ids_value as $df_ids_value_id)
						{
							if ($df_ids_value_id>0)
							{
								if ($df_join_table)
								{
									if ($df['is_group'])
									{
										$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct post_id from $df_join_table where $df_id=$df_ids_value_id";
									}
								} else {
									if ($df['is_group'])
									{
										$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$where.=" and $df_id=$df_ids_value_id";
									}
								}
							}
						}
						$df_ids_value=implode(',',$df_ids_value);
					} else {
						$df_ids_value=implode(',',$df_ids_value);
						if ($df_join_table)
						{
							if ($df['is_group'])
							{
								$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct post_id from $df_join_table where $df_id in ($df_ids_value)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$where.=" and $df_id in ($df_ids_value)";
							}
						}
					}

					$df_objects=mr2array(sql_pr("select $df_selector from $df_table where $df_where_plural and $df_id in ($df_ids_value)"));
					if ($df['base_files_url']!='' || $df['link_pattern']!='')
					{
						foreach ($df_objects as $k=>$v)
						{
							if ($df['base_files_url']!='')
							{
								$df_objects[$k]['base_files_url']=$df['base_files_url'].'/'.$v[$df_id];
							}
							if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
							{
								$pattern=str_replace("%ID%",$v[$df_id],str_replace("%DIR%",$v[$df['dir']],$website_ui_data[$df['link_pattern']]));
								$df_objects[$k]['view_page_url']="$config[project_url]/$pattern";
							}
						}
					}

					$storage[$object_id]["list_type"]="multi_{$df['plural']}";
					$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
					$smarty->assign("list_type","multi_{$df['plural']}");
					$smarty->assign("{$df['plural']}_info",$df_objects);
					$dynamic_filters_types[]="multi_{$df['plural']}";
				}
			}
		} elseif ((isset($block_config[$df_var_dir]) && $_REQUEST[$block_config[$df_var_dir]]!='') || (isset($block_config[$df_var_id]) && $_REQUEST[$block_config[$df_var_id]]!=''))
		{
			$df_where_single=$df['where_single'];
			if (!$df_where_single)
			{
				$df_where_single='1=1';
			}

			if ($_REQUEST[$block_config[$df_var_dir]]!='')
			{
				$result=sql_pr("select $df_selector from $df_table where $df_where_single and ({$df['dir']}=? or $df_selector_locale_dir)",trim($_REQUEST[$block_config[$df_var_dir]]),trim($_REQUEST[$block_config[$df_var_dir]]));
			} else {
				$result=sql_pr("select $df_selector from $df_table where $df_where_single and $df_id=?",intval($_REQUEST[$block_config[$df_var_id]]));
			}

			if (isset($result) && mr2rows($result)>0)
			{
				$data_temp=mr2array_single($result);
				$df_object_id=$data_temp[$df_id];

				if ($df['base_files_url']!='')
				{
					$data_temp['base_files_url']=$df['base_files_url'].'/'.$data_temp[$df_id];
				}
				if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
				{
					$pattern=str_replace("%ID%",$data_temp[$df_id],str_replace("%DIR%",$data_temp[$df['dir']],$website_ui_data[$df['link_pattern']]));
					$data_temp['view_page_url']="$config[project_url]/$pattern";
				}
				if ($df['supports_grouping'] && $data_temp["{$df['single']}_group_id"]>0)
				{
					$data_temp["{$df['single']}_group"]=mr2array_single(sql_pr("select {$database_selectors["$df[plural]_groups"]} from $config[tables_prefix]$df[plural]_groups where {$database_selectors["where_$df[plural]_groups"]} and {$df['single']}_group_id=?",$data_temp["{$df['single']}_group_id"]));
				}
				if ($df['sub_categories'])
				{
					$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_{$df['plural']} on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_{$df['plural']}.category_id where $database_selectors[where_categories] and $df_id=? order by id asc",$data_temp[$df_id]));
					foreach ($data_temp['categories'] as $v)
					{
						$data_temp['categories_as_string'].=$v['title'].", ";
					}
					$data_temp['categories_as_string']=rtrim($data_temp['categories_as_string'],", ");
				}
				if ($df['sub_tags'])
				{
					$data_temp['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_{$df['plural']} on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_{$df['plural']}.tag_id where $database_selectors[where_tags] and $df_id=? order by id asc",$data_temp[$df_id]));
					foreach ($data_temp['tags'] as $v)
					{
						$data_temp['tags_as_string'].=$v['tag'].", ";
					}
					$data_temp['tags_as_string']=rtrim($data_temp['tags_as_string'],", ");
				}
				if ($data_temp['country_id']>0)
				{
					$data_temp['country']=$list_countries['name'][$data_temp['country_id']];
				}

				$storage[$object_id]["list_type"]="{$df['plural']}";
				$storage[$object_id]["{$df['single']}"]=$data_temp[$df['title']];
				$storage[$object_id]["{$df['single']}_info"]=$data_temp;
				$smarty->assign("list_type","{$df['plural']}");
				$smarty->assign("{$df['single']}",$data_temp[$df['title']]);
				$smarty->assign("{$df['single']}_info",$data_temp);
				$dynamic_filters_types[]="{$df['plural']}";

				if ($df_join_table)
				{
					if ($df['is_group'])
					{
						$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct post_id from $df_join_table where $df_id=$df_object_id";
					}
				} else {
					if ($df['is_group'])
					{
						$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$where.=" and $df_id=$df_object_id";
					}
				}
			} else
			{
				return 'status_404';
			}
		}
	}

	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']] != '')
	{
		$q = trim(str_replace('[dash]', '-', str_replace('-', ' ', str_replace('--', '[dash]', str_replace('?', '', $_REQUEST[$block_config['var_search']])))));

		$unblocked_q = $q;
		$q = trim(process_blocked_words($q, false));
		if ($unblocked_q != $q)
		{
			$storage[$object_id]['is_search_contains_blocked_words'] = "1";
			$smarty->assign('is_search_contains_blocked_words', "1");

			if (isset($block_config['search_blocked_404']))
			{
				http_response_code(404);
				return 'nocache';
			}
			if (isset($block_config['search_blocked_redirect_to']))
			{
				$pattern = urldecode(str_replace('%QUERY%', $unblocked_q, trim($block_config['search_blocked_redirect_to'])));
				if (is_url($pattern))
				{
					return "status_302:$pattern";
				} else
				{
					return "status_302:$config[project_url]/" . ltrim($pattern, '/');
				}
			}
		}

		$unescaped_q = $q;
		$search_keyword_info=null;
		if ($q == '')
		{
			$where .= " and 1=0";
		} else
		{
			$search_keyword_info = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]stats_search where query_md5=md5(lower(?))", $unescaped_q));
			if (!empty($search_keyword_info) && $search_keyword_info['status_id'] == 0)
			{
				if (isset($block_config['search_disabled_404']))
				{
					http_response_code(404);
					return 'nocache';
				}
				if (isset($block_config['search_disabled_redirect_to']))
				{
					$pattern = urldecode(str_replace('%QUERY%', $unescaped_q, trim($block_config['search_disabled_redirect_to'])));
					if (is_url($pattern))
					{
						return "status_302:$pattern";
					} else
					{
						return "status_302:$config[project_url]/" . ltrim($pattern, '/');
					}
				}
			}

			$where_temp_str = '';

			$escaped_q = sql_escape(str_replace(['%', '_'], ['\%', '\_'], $q));
			if (isset($block_config['enable_search_on_categories']))
			{
				$category_ids = mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where $database_selectors[locale_field_title]=? or synonyms like '%$escaped_q%'", $q));
				if (array_cnt($category_ids) > 0)
				{
					$category_ids = implode(',', array_map('intval', $category_ids));
					$where_temp_str .= " or $config[tables_prefix]posts.post_id in (select post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids))";
				}
			}

			if (isset($block_config['enable_search_on_tags']))
			{
				$tag_ids = mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags where $database_selectors[locale_field_tag]=? or synonyms like '%$escaped_q%'", $q));
				if (array_cnt($tag_ids) > 0)
				{
					$tag_ids = implode(',', array_map('intval', $tag_ids));
					$where_temp_str .= " or $config[tables_prefix]posts.post_id in (select post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids))";
				}
			}

			if (isset($block_config['enable_search_on_models']))
			{
				$model_ids = mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where $database_selectors[locale_field_title]=? or alias like '%$escaped_q%'", $q));
				if (array_cnt($model_ids) > 0)
				{
					$model_ids = implode(',', array_map('intval', $model_ids));
					$where_temp_str .= " or $config[tables_prefix]posts.post_id in (select post_id from $config[tables_prefix]models_posts where model_id in ($model_ids))";
				}
			}

			if (isset($block_config['enable_search_on_users']))
			{
				$user_ids = mr2array_list(sql_pr("select user_id from $config[tables_prefix]users where username=? or display_name=?", $q, $q));
				if (array_cnt($user_ids) > 0)
				{
					$user_ids = implode(',', array_map('intval', $user_ids));
					$where_temp_str .= " or user_id in ($user_ids)";
				}
			}

			if (!isset($block_config['search_method']))
			{
				$block_config['search_method'] = 3;
			}

			$q = sql_escape($q);
			$search_scope = intval($block_config['search_scope']);
			if ($search_scope == 2)
			{
				$where2 = '1=0';
				if (isset($block_config['enable_search_on_custom_fields']))
				{
					$q = str_replace(['%', '_'], ['\%', '\_'], $q);
					$where2 .= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%' or custom4 like '%$q%' or custom5 like '%$q%' or custom6 like '%$q%' or custom7 like '%$q%' or custom8 like '%$q%' or custom9 like '%$q%' or custom10 like '%$q%'";
				}
				if (is_numeric($q))
				{
					$where2 .= " or $config[tables_prefix]posts.post_id=" . intval($q);
				}
				$where .= " and (($where2) $where_temp_str)";
			} else
			{
				if ($block_config['search_method'] == 3 || $block_config['search_method'] == 4 || $block_config['search_method'] == 5)
				{
					$search_modifier = '';
					if ($block_config['search_method'] == 4)
					{
						$search_modifier = 'IN BOOLEAN MODE';
					} elseif ($block_config['search_method'] == 5)
					{
						$search_modifier = 'WITH QUERY EXPANSION';
					}
					if ($search_scope == 0)
					{
						$where2 = "MATCH (title, description, content) AGAINST ('$q' $search_modifier)";
						$sort_by_relevance = "MATCH (title, description, content) AGAINST ('$q' $search_modifier) desc";
					} else
					{
						$where2 = "MATCH (title) AGAINST ('$q' $search_modifier)";
						$sort_by_relevance = "MATCH (title) AGAINST ('$q' $search_modifier) desc";
					}
					if (isset($block_config['enable_search_on_custom_fields']))
					{
						$q = str_replace(['%', '_'], ['\%', '\_'], $q);
						$where2 .= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%' or custom4 like '%$q%' or custom5 like '%$q%' or custom6 like '%$q%' or custom7 like '%$q%' or custom8 like '%$q%' or custom9 like '%$q%' or custom10 like '%$q%'";
					}
					if (is_numeric($q))
					{
						$where2 .= " or $config[tables_prefix]posts.post_id=" . intval($q);
					}
					$where .= " and (($where2) $where_temp_str)";

					$storage[$object_id]['is_search_supports_relevance'] = "1";
					$smarty->assign('is_search_supports_relevance', "1");
				} else if ($block_config['search_method'] == 2)
				{
					$where2 = '';
					$temp = explode(" ", $q);
					foreach ($temp as $temp_value)
					{
						$length = strlen($temp_value);
						if (function_exists('mb_detect_encoding'))
						{
							$length = mb_strlen($temp_value, mb_detect_encoding($temp_value));
						}
						if ($length > 2)
						{
							$temp_value = str_replace(['%', '_'], ['\%', '\_'], $temp_value);
							if ($search_scope == 0)
							{
								$where2 .= " or title like '%$temp_value%' or description like '%$temp_value%' or content like '%$temp_value%'";
							} else
							{
								$where2 .= " or title like '%$temp_value%'";
							}
							if (isset($block_config['enable_search_on_custom_fields']))
							{
								$where2 .= " or custom1 like '%$temp_value%' or custom2 like '%$temp_value%' or custom3 like '%$temp_value%' or custom4 like '%$temp_value%' or custom5 like '%$temp_value%' or custom6 like '%$temp_value%' or custom7 like '%$temp_value%' or custom8 like '%$temp_value%' or custom9 like '%$temp_value%' or custom10 like '%$temp_value%'";
							}
							if (is_numeric($q))
							{
								$where2 .= " or $config[tables_prefix]posts.post_id=" . intval($q);
							}
						}
					}
					if ($where2)
					{
						$where2 = substr($where2, 4);
					} else
					{
						$q = str_replace(['%', '_'], ['\%', '\_'], $q);
						if ($search_scope == 0)
						{
							$where2 .= "title like '%$q%' or description like '%$q%' or content like '%$q%'";
						} else
						{
							$where2 .= "title like '%$q%'";
						}
						if (isset($block_config['enable_search_on_custom_fields']))
						{
							$where2 .= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%' or custom4 like '%$q%' or custom5 like '%$q%' or custom6 like '%$q%' or custom7 like '%$q%' or custom8 like '%$q%' or custom9 like '%$q%' or custom10 like '%$q%'";
						}
						if (is_numeric($q))
						{
							$where2 .= " or $config[tables_prefix]posts.post_id=" . intval($q);
						}
					}
					$where .= " and (($where2) $where_temp_str)";
				} else
				{
					$where2 = '';
					$q = str_replace(['%', '_'], ['\%', '\_'], $q);
					if (isset($block_config['enable_search_on_custom_fields']))
					{
						$where2 .= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%' or custom4 like '%$q%' or custom5 like '%$q%' or custom6 like '%$q%' or custom7 like '%$q%' or custom8 like '%$q%' or custom9 like '%$q%' or custom10 like '%$q%'";
					}
					if (is_numeric($q))
					{
						$where2 .= " or $config[tables_prefix]posts.post_id=" . intval($q);
					}
					if ($search_scope == 0)
					{
						$where .= " and ((title like '%$q%' or description like '%$q%' or content like '%$q%' $where2) $where_temp_str)";
					} else
					{
						$where .= " and ((title like '%$q%' $where2) $where_temp_str)";
					}
				}
			}
		}

		$storage[$object_id]['list_type'] = "search";
		$storage[$object_id]['search_keyword'] = $unescaped_q;
		$storage[$object_id]['search_keyword_info'] = $search_keyword_info;
		$storage[$object_id]['url_prefix'] = "?$block_config[var_search]=$unescaped_q&";
		$smarty->assign('list_type', "search");
		$smarty->assign('search_keyword', $unescaped_q);
		$smarty->assign('search_keyword_info', $search_keyword_info);
	}

	if (isset($block_config['skip_categories']))
	{
		$category_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_categories'])));
		if (array_cnt($category_ids) > 0)
		{
			if (in_array('categories', $dynamic_filters_types) && in_array($storage[$object_id]['category_info']['category_id'], $category_ids))
			{
				$category_ids = array_diff($category_ids, [$storage[$object_id]['category_info']['category_id']]);
			}
			if (in_array('multi_categories', $dynamic_filters_types))
			{
				foreach ($storage[$object_id]['categories_info'] as $category_info)
				{
					if (in_array($category_info['category_id'], $category_ids))
					{
						$category_ids = array_diff($category_ids, [$category_info['category_id']]);
					}
				}
			}
			if (array_cnt($category_ids) > 0)
			{
				$category_ids = implode(',', $category_ids);
				$where .= " and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids))";
			}
		}
	}

	if (isset($block_config['show_categories']))
	{
		$category_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_categories'])));
		if (array_cnt($category_ids) > 0)
		{
			$category_ids = implode(',', $category_ids);
			$join_tables[] = "select distinct post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids)";
		}
	}

	if (isset($block_config['skip_category_groups']))
	{
		$category_group_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_category_groups'])));
		if (in_array('categories_groups', $dynamic_filters_types) && in_array($storage[$object_id]['category_group_info']['category_group_id'], $category_group_ids))
		{
			$category_group_ids = array_diff($category_group_ids, [$storage[$object_id]['category_group_info']['category_group_id']]);
		}
		if (in_array('multi_categories_groups', $dynamic_filters_types))
		{
			foreach ($storage[$object_id]['categories_groups_info'] as $category_group_info)
			{
				if (in_array($category_group_info['category_group_id'], $category_group_ids))
				{
					$category_group_ids = array_diff($category_group_ids, [$category_group_info['category_group_id']]);
				}
			}
		}
		if (array_cnt($category_group_ids) > 0)
		{
			$category_group_ids = implode(',', $category_group_ids);
			$category_ids = mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_group_id in ($category_group_ids)"));
			if (array_cnt($category_ids) > 0)
			{
				if (in_array('categories', $dynamic_filters_types) && in_array($storage[$object_id]['category_info']['category_id'], $category_ids))
				{
					$category_ids = array_diff($category_ids, [$storage[$object_id]['category_info']['category_id']]);
				}
				if (in_array('multi_categories', $dynamic_filters_types))
				{
					foreach ($storage[$object_id]['categories_info'] as $category_info)
					{
						if (in_array($category_info['category_id'], $category_ids))
						{
							$category_ids = array_diff($category_ids, [$category_info['category_id']]);
						}
					}
				}
				if (array_cnt($category_ids) > 0)
				{
					$category_ids = implode(',', $category_ids);
					$where .= " and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids))";
				}
			}
		}
	}

	if (isset($block_config['show_category_groups']))
	{
		$category_group_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_category_groups'])));
		if (array_cnt($category_group_ids) > 0)
		{
			$category_group_ids = implode(',', $category_group_ids);
			$category_ids = mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_group_id in ($category_group_ids)"));
			if (array_cnt($category_ids) > 0)
			{
				$category_ids = implode(',', $category_ids);
				$join_tables[] = "select distinct post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids)";
			}
		}
	}

	if (isset($block_config['skip_tags']))
	{
		$tag_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_tags'])));
		if (array_cnt($tag_ids) > 0)
		{
			if (in_array('tags', $dynamic_filters_types) && in_array($storage[$object_id]['tag_info']['tag_id'], $tag_ids))
			{
				$tag_ids = array_diff($tag_ids, [$storage[$object_id]['tag_info']['tag_id']]);
			}
			if (in_array('multi_tags', $dynamic_filters_types))
			{
				foreach ($storage[$object_id]['tags_info'] as $tag_info)
				{
					if (in_array($tag_info['tag_id'], $tag_ids))
					{
						$tag_ids = array_diff($tag_ids, [$tag_info['tag_id']]);
					}
				}
			}
			if (array_cnt($tag_ids) > 0)
			{
				$tag_ids = implode(",", $tag_ids);
				$where .= " and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)) ";
			}
		}
	}

	if (isset($block_config['show_tags']))
	{
		$tag_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_tags'])));
		if (array_cnt($tag_ids) > 0)
		{
			$tag_ids = implode(',', $tag_ids);
			$join_tables[] = "select distinct post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)";
		}
	}

	if (isset($block_config['skip_models']))
	{
		$model_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_models'])));
		if (array_cnt($model_ids) > 0)
		{
			if (in_array('models', $dynamic_filters_types) && in_array($storage[$object_id]['model_info']['model_id'], $model_ids))
			{
				$model_ids = array_diff($model_ids, [$storage[$object_id]['model_info']['model_id']]);
			}
			if (in_array('multi_models', $dynamic_filters_types))
			{
				foreach ($storage[$object_id]['models_info'] as $model_info)
				{
					if (in_array($model_info['model_id'], $model_ids))
					{
						$model_ids = array_diff($model_ids, [$model_info['model_id']]);
					}
				}
			}
			if (array_cnt($model_ids) > 0)
			{
				$model_ids = implode(',', $model_ids);
				$where .= " and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)) ";
			}
		}
	}

	if (isset($block_config['show_models']))
	{
		$model_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_models'])));
		if (array_cnt($model_ids) > 0)
		{
			$model_ids = implode(',', $model_ids);
			$join_tables[] = "select distinct post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)";
		}
	}

	if (isset($block_config['skip_model_groups']))
	{
		$model_group_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_model_groups'])));
		if (in_array('models_groups', $dynamic_filters_types) && in_array($storage[$object_id]['model_group_info']['model_group_id'], $model_group_ids))
		{
			$model_group_ids = array_diff($model_group_ids, [$storage[$object_id]['model_group_info']['model_group_id']]);
		}
		if (in_array('multi_models_groups', $dynamic_filters_types))
		{
			foreach ($storage[$object_id]['models_groups_info'] as $model_group_info)
			{
				if (in_array($model_group_info['model_group_id'], $model_group_ids))
				{
					$model_group_ids = array_diff($model_group_ids, [$model_group_info['model_group_id']]);
				}
			}
		}
		if (array_cnt($model_group_ids) > 0)
		{
			$model_group_ids = implode(',', $model_group_ids);
			$model_ids = mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_group_id in ($model_group_ids)"));
			if (array_cnt($model_ids) > 0)
			{
				if (in_array('models', $dynamic_filters_types) && in_array($storage[$object_id]['model_info']['model_id'], $model_ids))
				{
					$model_ids = array_diff($model_ids, [$storage[$object_id]['model_info']['model_id']]);
				}
				if (in_array('multi_models', $dynamic_filters_types))
				{
					foreach ($storage[$object_id]['models_info'] as $model_info)
					{
						if (in_array($model_info['model_id'], $model_ids))
						{
							$model_ids = array_diff($model_ids, [$model_info['model_id']]);
						}
					}
				}
				if (array_cnt($model_ids) > 0)
				{
					$model_ids = implode(',', $model_ids);
					$where .= " and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]models_posts where model_id in ($model_ids))";
				}
			}
		}
	}

	if (isset($block_config['show_model_groups']))
	{
		$model_group_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_model_groups'])));
		if (array_cnt($model_group_ids) > 0)
		{
			$model_group_ids = implode(',', $model_group_ids);
			$model_ids = mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_group_id in ($model_group_ids)"));
			if (array_cnt($model_ids) > 0)
			{
				$model_ids = implode(',', $model_ids);
				$join_tables[] = "select distinct post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)";
			}
		}
	}

	if (isset($block_config['skip_users']))
	{
		$user_ids = array_map('intval', array_map('trim', explode(',', $block_config['skip_users'])));
		if (array_cnt($user_ids) > 0)
		{
			$user_ids = implode(',', $user_ids);
			$where .= " and $config[tables_prefix]posts.user_id not in ($user_ids) ";
		}
	}

	if (isset($block_config['show_users']))
	{
		$user_ids = array_map('intval', array_map('trim', explode(',', $block_config['show_users'])));
		if (array_cnt($user_ids) > 0)
		{
			$user_ids = implode(',', $user_ids);
			$where .= " and $config[tables_prefix]posts.user_id in ($user_ids) ";
		}
	}

	if (isset($block_config['days_passed_from']))
	{
		$date_passed_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - intval($block_config['days_passed_from']) + 1, date("Y")));
		$where .= " and $database_selectors[generic_post_date_selector]<='$date_passed_from'";
	}
	if (isset($block_config['days_passed_to']))
	{
		$date_passed_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - intval($block_config['days_passed_to']) + 1, date("Y")));
		$where .= " and $database_selectors[generic_post_date_selector]>='$date_passed_from'";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and description<>''";
	}

	if (is_array($config['advanced_filtering']))
	{
		foreach ($config['advanced_filtering'] as $advanced_filter)
		{
			if ($advanced_filter=='upload_zone')
			{
				$where.=' and af_upload_zone=0';
			}
		}
	}

	$data=list_postsMetaData();
	foreach ($data as $res)
	{
		if (strpos($res['type'],"SORTING")!==false)
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="post_date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower(title)";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";

	if ($sort_by_clear=='rating')
	{
		$sort_by="rating/rating_amount desc, rating_amount desc";
	} elseif ($sort_by_clear=='post_viewed')
	{
		$sort_by="post_viewed desc";
	} elseif ($sort_by_clear=='most_commented')
	{
		$sort_by="comments_count $direction";
	} else
	{
		if ($sort_by_clear=='post_date') {$sort_by="$database_selectors[generic_post_date_selector] $direction, $config[tables_prefix]posts.post_id $direction";} else
		if ($sort_by_clear=='post_date_and_popularity') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, post_viewed desc";} else
		if ($sort_by_clear=='post_date_and_rating') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, rating/rating_amount desc, rating_amount desc";} else
		if ($sort_by_clear=='last_time_view_date_and_popularity') {$sort_by="date(last_time_view_date) $direction, post_viewed desc";} else
		if ($sort_by_clear=='last_time_view_date_and_rating') {$sort_by="date(last_time_view_date) $direction, rating/rating_amount desc, rating_amount desc";}
	}

	$sort_by="order by $sort_by";
	if ($sort_by_relevance<>'' && trim($_REQUEST[$block_config['var_sort_by']])=='')
	{
		$sort_by = "order by $sort_by_relevance";
		$storage[$object_id]['sort_by'] = 'relevance';
		$smarty->assign('sort_by', 'relevance');
	}

	$from_clause="$config[tables_prefix]posts";
	for ($i=1;$i<=array_cnt($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.post_id=$config[tables_prefix]posts.post_id";
	}

	$total_count = 0;
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $where_posts $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("SELECT $database_selectors[posts] from $from_clause where $where_posts $where $sort_by LIMIT $from, $block_config[items_per_page]"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else {
		$limit='limit 0';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("SELECT $database_selectors[posts] from $from_clause where $where_posts $where $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	if ($storage[$object_id]['list_type'] == 'search')
	{
		$search_results_count = intval($total_count);
		if ($search_results_count == 0)
		{
			$search_results_count = array_cnt($data);
		}

		if ($search_results_count == 0)
		{
			if (isset($block_config['search_empty_404']))
			{
				http_response_code(404);
				return 'nocache';
			}
			if (isset($block_config['search_empty_redirect_to']))
			{
				$pattern = urldecode(str_replace('%QUERY%', $storage[$object_id]['search_keyword'], trim($block_config['search_empty_redirect_to'])));
				if (is_url($pattern))
				{
					return "status_302:$pattern";
				} else
				{
					return "status_302:$config[project_url]/" . ltrim($pattern, '/');
				}
			}
		} elseif ($search_results_count == 1 && isset($block_config['search_redirect_enabled']))
		{
			$pattern = urldecode(str_replace('%ID%', $data[0]['post_id'], str_replace('%DIR%', $data[0]['dir'], @trim($block_config['search_redirect_pattern']) ?: $post_types[$data[0]['post_type_id']]['url_pattern'])));
			if (is_url($pattern))
			{
				return "status_302:$pattern";
			} else
			{
				return "status_302:$config[project_url]/" . ltrim($pattern, '/');
			}
		}
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where category_id in (select category_id from $config[tables_prefix]categories_posts where $database_selectors[where_categories] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags where tag_id in (select tag_id from $config[tables_prefix]tags_posts where $database_selectors[where_tags] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_models_info']))
		{
			$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models where model_id in (select model_id from $config[tables_prefix]models_posts where $database_selectors[where_models] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_user_info']))
		{
			$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
		}
		if (isset($block_config['show_connected_info']) && $data[$k]['connected_video_id']>0)
		{
			$connected_video_info=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and video_id=".$data[$k]['connected_video_id']));
			if ($connected_video_info['video_id']>0)
			{
				$connected_video_info['time_passed_from_adding']=get_time_passed($connected_video_info['post_date']);
				$connected_video_info['duration_array']=get_duration_splitted($connected_video_info['duration']);
				$connected_video_info['formats']=get_video_formats($connected_video_info['video_id'],$connected_video_info['file_formats'],$connected_video_info['server_group_id']);
				$connected_video_info['dir_path']=get_dir_by_id($connected_video_info['video_id']);

				$screen_url_base=load_balance_screenshots_url();
				$connected_video_info['screen_url']=$screen_url_base.'/'.get_dir_by_id($connected_video_info['video_id']).'/'.$connected_video_info['video_id'];

				$pattern=str_replace("%ID%",$connected_video_info['video_id'],str_replace("%DIR%",$connected_video_info['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$connected_video_info['view_page_url']="$config[project_url]/$pattern";
				$data[$k]['connected_video']=$connected_video_info;
			}
		}

		$dir_path=get_dir_by_id($data[$k]['post_id']);
		$data[$k]['base_files_url']="$config[content_url_posts]/$dir_path/".$data[$k]['post_id'];

		if (isset($post_types[$data[$k]['post_type_id']]))
		{
			$pattern=str_replace("%ID%",$data[$k]['post_id'],str_replace("%DIR%",$data[$k]['dir'],$post_types[$data[$k]['post_type_id']]['url_pattern']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}
	}

	if (array_cnt($data)>0)
	{
		$storage[$object_id]['first_object_id']=$data[0]['post_id'];
		$storage[$object_id]['first_object_title']=$data[0]['title'];
		$storage[$object_id]['first_object_description']=$data[0]['description'];
		$update_storage_keys=array(
			'category_info',
			'category_group_info',
			'tag_info',
			'model_info',
			'model_group_info'
		);
		foreach ($update_storage_keys as $update_storage_key)
		{
			if (isset($storage[$object_id][$update_storage_key]))
			{
				$storage[$object_id][$update_storage_key]['first_object_id']=$data[0]['post_id'];
				$storage[$object_id][$update_storage_key]['first_object_title']=$data[0]['title'];
				$storage[$object_id][$update_storage_key]['first_object_description']=$data[0]['description'];
			}
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_postsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_model_ids=trim($_REQUEST[$block_config['var_model_ids']]);
	$var_model_group_dir=trim($_REQUEST[$block_config['var_model_group_dir']]);
	$var_model_group_id=trim($_REQUEST[$block_config['var_model_group_id']]);
	$var_model_group_ids=trim($_REQUEST[$block_config['var_model_group_ids']]);
	$var_post_date_from=trim($_REQUEST[$block_config['var_post_date_from']]);
	$var_post_date_to=trim($_REQUEST[$block_config['var_post_date_to']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_post_type=trim($_REQUEST[$block_config['var_post_type']]);
	$var_mode_related=trim($_REQUEST[$block_config['var_mode_related']]);
	$var_post_dir=trim($_REQUEST[$block_config['var_post_dir']]);
	$var_post_id=trim($_REQUEST[$block_config['var_post_id']]);
	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_connected_video_dir=trim($_REQUEST[$block_config['var_connected_video_dir']]);
	$var_connected_video_id=trim($_REQUEST[$block_config['var_connected_video_id']]);
	$var_user_id=trim($_REQUEST[$block_config['var_user_id']]);

	$var_custom='';
	for ($i=1;$i<=10;$i++)
	{
		$var_custom.=trim($_REQUEST[$block_config["var_custom$i"]])."|";
	}
	$var_custom_flag1=trim($_REQUEST[$block_config['var_custom_flag1']]);
	$var_custom_flag2=trim($_REQUEST[$block_config['var_custom_flag2']]);
	$var_custom_flag3=trim($_REQUEST[$block_config['var_custom_flag3']]);

	if (isset($block_config['mode_created']) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	} else
	{
		if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']] != '')
		{
			$number_of_words = 1 + max(substr_count($_REQUEST[$block_config['var_search']], ' '), substr_count($_REQUEST[$block_config['var_search']], '-'));
			$search_caching_words = max(1, intval($block_config['search_caching_words']));
			if ($number_of_words > $search_caching_words)
			{
				return 'runtime_nocache';
			}
		}
		$result = "$from|$items_per_page|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_post_date_from|$var_post_date_to|$var_search|$var_sort_by|$var_custom|$var_custom_flag1|$var_custom_flag2|$var_custom_flag3|$var_post_type|$var_mode_related|$var_post_dir|$var_post_id|$var_title_section|$var_connected_video_dir|$var_connected_video_id|$var_user_id";
		return $result;
	}
}

function list_postsCacheControl($block_config)
{
	if (isset($block_config['mode_created']) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_postsAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_posts') && is_array($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_postsShow($block_config,null);
	}
}

function list_postsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"12"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[post_id,title,dir,post_date,post_date_and_popularity,post_date_and_rating,last_time_view_date,last_time_view_date_and_popularity,last_time_view_date_and_rating,rating,post_viewed,most_commented]", "is_required"=>1, "default_value"=>"post_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"post_type",                  "group"=>"static_filters", "type"=>"STRING",   "is_required"=>0, "default_value"=>""),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"skip_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_category_groups",       "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_category_groups",       "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_model_groups",          "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_model_groups",          "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_users",                 "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_users",                 "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",             "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_post_type",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_type"),
		array("name"=>"var_title_section",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
		array("name"=>"var_category_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_ids",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_ids"),
		array("name"=>"var_category_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group"),
		array("name"=>"var_category_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_id"),
		array("name"=>"var_category_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_ids"),
		array("name"=>"var_tag_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_tag_ids",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_ids"),
		array("name"=>"var_model_dir",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model"),
		array("name"=>"var_model_id",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_id"),
		array("name"=>"var_model_ids",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_ids"),
		array("name"=>"var_model_group_dir",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group"),
		array("name"=>"var_model_group_id",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_id"),
		array("name"=>"var_model_group_ids",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_ids"),
		array("name"=>"var_post_date_from",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_from"),
		array("name"=>"var_post_date_to",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_to"),
		array("name"=>"var_custom1",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom1"),
		array("name"=>"var_custom2",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom2"),
		array("name"=>"var_custom3",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom3"),
		array("name"=>"var_custom4",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom4"),
		array("name"=>"var_custom5",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom5"),
		array("name"=>"var_custom6",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom6"),
		array("name"=>"var_custom7",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom7"),
		array("name"=>"var_custom8",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom8"),
		array("name"=>"var_custom9",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom9"),
		array("name"=>"var_custom10",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom10"),
		array("name"=>"var_custom_flag1",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag1"),
		array("name"=>"var_custom_flag2",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag2"),
		array("name"=>"var_custom_flag3",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag3"),

		// search
		array("name"=>"var_search",                     "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",                  "group"=>"search", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"search_scope",                   "group"=>"search", "type"=>"CHOICE[0,1,2]",     "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_redirect_enabled",        "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_redirect_pattern",        "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>""),
		array("name"=>"search_empty_404",               "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_empty_redirect_to",       "group"=>"search", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"search_blocked_404",             "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_blocked_redirect_to",     "group"=>"search", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"search_disabled_404",            "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_disabled_redirect_to",    "group"=>"search", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"search_caching_words",           "group"=>"search", "type"=>"INT",               "is_required"=>0, "default_value"=>"1"),
		array("name"=>"enable_search_on_tags",          "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_categories",    "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_models",        "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_users",         "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_custom_fields", "group"=>"search", "type"=>"",                  "is_required"=>0),

		// related
		array("name"=>"mode_related",                   "group"=>"related", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_post_dir",                   "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_post_id",                    "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"id"),
		array("name"=>"mode_related_category_group_id", "group"=>"related", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"mode_related_model_group_id",    "group"=>"related", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"var_mode_related",               "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"mode_related"),

		// display modes
		array("name"=>"mode_created",               "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"var_user_id",                "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",   "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_created_posts", "group"=>"display_modes", "type"=>"",       "is_required"=>0),

		// connected
		array("name"=>"mode_connected_video",    "group"=>"connected_videos", "type"=>"",       "is_required"=>0),
		array("name"=>"var_connected_video_dir", "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_connected_video_id",  "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_models_info",     "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_user_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_connected_info",  "group"=>"subselects", "type"=>"", "is_required"=>0),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
