{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="video_exporting_feed_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.videos.submenu_option_feeds_export}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.videos.feed_add}}{{else}}{{$lang.videos.feed_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
		<table class="de_editor">
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="2">
					<div class="err_header">{{$lang.validation.common_header}}</div>
					<div class="err_content">
						{{if is_array($smarty.post.errors)}}
							<ul>
								{{foreach item="error" from=$smarty.post.errors|smarty:nodefaults}}
									<li>{{$error}}</li>
								{{/foreach}}
							</ul>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/280-how-to-migrate-your-data-from-1-kvs-site-to-another/">How to migrate your data from 1 KVS site to another</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_status}}</td>
				<td class="de_control">
					<select name="status_id">
						<option value="0" {{if $smarty.post.status_id==0}}selected{{/if}}>{{$lang.videos.feed_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.videos.feed_field_status_active}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_external_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_external_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_max_limit}}</td>
				<td class="de_control">
					<input type="text" name="max_limit" maxlength="10" size="10" value="{{$smarty.post.max_limit}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_max_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_cache}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="cache" maxlength="10" size="10" value="{{$smarty.post.cache}}"/>
						{{$lang.videos.feed_field_cache_seconds}}
					</span>
					<span class="de_hint">{{$lang.videos.feed_field_cache_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_password}}</td>
				<td class="de_control">
					<input type="text" name="password" maxlength="100" value="{{$smarty.post.password}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_password_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_affiliate_param_name}}</td>
				<td class="de_control">
					<input type="text" name="affiliate_param_name" maxlength="100" value="{{$smarty.post.affiliate_param_name}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_affiliate_param_name_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.videos.feed_field_feed_url}}</td>
					<td class="de_control">
						<a href="{{$config.project_url}}/admin/feeds/{{$smarty.post.external_id}}/">{{$config.project_url}}/admin/feeds/{{$smarty.post.external_id}}/</a>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.feed_field_last_exec_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
								{{$lang.common.undefined}}
							{{else}}
								{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}} ({{$smarty.post.last_exec_duration|number_format:4:".":""}}{{$lang.common.second_truncated}})
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_filters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_status}}</td>
				<td class="de_control">
					<select name="video_status_id">
						<option value="0" {{if $smarty.post.video_status_id==0}}selected{{/if}}>{{$lang.videos.feed_field_video_status_active}}</option>
						<option value="1" {{if $smarty.post.video_status_id==1}}selected{{/if}}>{{$lang.videos.feed_field_video_status_disabled}}</option>
						<option value="2" {{if $smarty.post.video_status_id==2}}selected{{/if}}>{{$lang.videos.feed_field_video_status_active_disabled}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_type}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="video_is_private[]" value="0" {{if in_array(0, $smarty.post.video_is_private) || count($smarty.post.video_is_private)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_type_public}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="video_is_private[]" value="1" {{if in_array(1, $smarty.post.video_is_private) || count($smarty.post.video_is_private)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_type_private}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="video_is_private[]" value="2" {{if in_array(2, $smarty.post.video_is_private) || count($smarty.post.video_is_private)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_type_premium}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_load_type}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="video_load_type_ids[]" value="1" {{if in_array(1, $smarty.post.video_load_type_ids) || count($smarty.post.video_load_type_ids)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_load_type_file}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="video_load_type_ids[]" value="2" {{if in_array(2, $smarty.post.video_load_type_ids) || count($smarty.post.video_load_type_ids)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_load_type_url}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="video_load_type_ids[]" value="3" {{if in_array(3, $smarty.post.video_load_type_ids) || count($smarty.post.video_load_type_ids)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_load_type_embed}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="video_load_type_ids[]" value="5" {{if in_array(5, $smarty.post.video_load_type_ids) || count($smarty.post.video_load_type_ids)==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_video_load_type_pseudo}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_categories}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.feed_field_video_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=video_category_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.feed_field_video_categories_empty}}</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.video_categories|smarty:nodefaults}}
							<input type="hidden" name="video_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_category"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.feed_field_video_categories_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_models}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.feed_field_video_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=video_model_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.feed_field_video_models_empty}}</span>
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.video_models|smarty:nodefaults}}
							<input type="hidden" name="video_model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_model"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.feed_field_video_models_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_tags}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.feed_field_video_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=video_tag_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.feed_field_video_tags_empty}}</span>
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.video_tags|smarty:nodefaults}}
							<input type="hidden" name="video_tag_ids[]" value="{{$item.tag_id}}" alt="{{$item.tag}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_tag"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.feed_field_video_tags_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_content_sources}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.feed_field_video_content_sources}}</span>
							<span class="js_param">url=async/insight.php?type=content_sources</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=video_content_source_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.feed_field_video_content_sources_empty}}</span>
							{{if in_array('content_sources|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.video_content_sources|smarty:nodefaults}}
							<input type="hidden" name="video_content_source_ids[]" value="{{$item.content_source_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_cs"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.feed_field_video_content_sources_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_dvds}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.feed_field_video_dvds}}</span>
							<span class="js_param">url=async/insight.php?type=dvds</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=video_dvd_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.feed_field_video_dvds_empty}}</span>
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.video_dvds|smarty:nodefaults}}
							<input type="hidden" name="video_dvd_ids[]" value="{{$item.dvd_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_dvd"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.feed_field_video_dvds_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_admin_flag}}</td>
				<td class="de_control">
					<select name="video_admin_flag_id_option">
						<option value="include" {{if $smarty.post.video_admin_flag_id_option=='include'}}selected{{/if}}>{{$lang.videos.feed_field_video_admin_flag_include}}</option>
						<option value="exclude" {{if $smarty.post.video_admin_flag_id_option=='exclude'}}selected{{/if}}>{{$lang.videos.feed_field_video_admin_flag_exclude}}</option>
					</select>
					<select name="video_admin_flag_id">
						<option value="">{{$lang.common.dg_filter_option_all}}</option>
						{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.video_admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_data}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_video_content_type}}</td>
				<td class="de_control">
					<select name="video_content_type_id">
						<option value="1" {{if $smarty.post.video_content_type_id==1}}selected{{/if}}>{{$lang.videos.feed_field_video_content_type_pseudo}}</option>
						<option value="2" {{if $smarty.post.video_content_type_id==2}}selected{{/if}}>{{$lang.videos.feed_field_video_content_type_hotlink}}</option>
						<option value="3" {{if $smarty.post.video_content_type_id==3}}selected{{/if}}>{{$lang.videos.feed_field_video_content_type_embed}}</option>
						<option value="4" {{if $smarty.post.video_content_type_id==4}}selected{{/if}}>{{$lang.videos.feed_field_video_content_type_temp_link}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_video_content_type_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_options}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_search" value="1" {{if $smarty.post.enable_search==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_search}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_search_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_categories" value="1" {{if $smarty.post.enable_categories==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_categories}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_categories_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_tags" value="1" {{if $smarty.post.enable_tags==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_tags}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_tags_hint}}</span>
							</td>
						</tr>
						{{if $config.installation_type>=2}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="enable_models" value="1" {{if $smarty.post.enable_models==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_models}}</label></span>
									<span class="de_hint">{{$lang.videos.feed_field_options_enable_models_hint}}</span>
								</td>
							</tr>
						{{/if}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_content_sources" value="1" {{if $smarty.post.enable_content_sources==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_content_sources}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_content_sources_hint}}</span>
							</td>
						</tr>
						{{if $config.installation_type==4}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="enable_dvds" value="1" {{if $smarty.post.enable_dvds==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_dvds}}</label></span>
									<span class="de_hint">{{$lang.videos.feed_field_options_enable_dvds_hint}}</span>
								</td>
							</tr>
						{{/if}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_screenshot_sources" value="1" {{if $smarty.post.enable_screenshot_sources==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_screen_sources}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_screen_sources_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_custom_fields" value="1" {{if $smarty.post.enable_custom_fields==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_custom_fields}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_custom_fields_hint}}</span>
							</td>
						</tr>
						{{if count($list_languages)>0}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="enable_localization" value="1" {{if $smarty.post.enable_localization==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_localization}}</label></span>
									<span class="de_hint">{{$lang.videos.feed_field_options_enable_localization_hint}}</span>
								</td>
							</tr>
						{{/if}}
						{{if count($list_satellites)>0}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="enable_satellites" value="1" {{if $smarty.post.enable_satellites==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_satellites}}</label></span>
									<span class="de_hint">{{$lang.videos.feed_field_options_enable_satellites_hint}}</span>
								</td>
							</tr>
						{{/if}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="enable_future_dates" value="1" {{if $smarty.post.enable_future_dates==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_enable_future_dates}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_enable_future_dates_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="with_rotation_finished" value="1" {{if $smarty.post.with_rotation_finished==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_with_rotation_finished}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_with_rotation_finished_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="active_categorization" value="1" {{if $smarty.post.active_categorization==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_active_categorization}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_options_active_categorization_hint}}</span>
							</td>
						</tr>
						{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="with_upload_zone_site" value="1" {{if $smarty.post.with_upload_zone_site==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_options_with_upload_zone_site}}</label></span>
									<span class="de_hint">{{$lang.videos.feed_field_options_with_upload_zone_site_hint}}</span>
								</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
			{{/if}}
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{else}}

{{assign var="can_delete" value=1}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
			</div>
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
				<div class="drop">
					<i class="icon icon-action-list"></i><span>{{$lang.common.dg_list_view}}</span>
					<ul>
						<li><a href="{{$page_name}}?se_grid_preset=">{{$lang.common.dg_list_view_default}}</a></li>
						{{foreach from=$grid_presets item="preset"}}
							<li {{if $smarty.session.save.$page_name.grid_preset==$preset.title}}class="selected"{{/if}}><a href="{{$page_name}}?se_grid_preset={{$preset.title}}">{{$preset.title}}</a></li>
						{{/foreach}}
					</ul>
				</div>
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$lang.common.dg_filter_btn_submit}}"/>
				</div>
			</div>
			<div class="dgf_advanced_columns">
				{{assign var="table_columns_display_mode" value="selector"}}
				{{include file="table_columns_inc.tpl"}}
			</div>
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
					<col width="1%"/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						{{assign var="table_columns_display_mode" value="header"}}
						{{include file="table_columns_inc.tpl"}}
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=2}}
					{{foreach from=$table_fields|smarty:nodefaults item="field"}}
						{{if $field.is_enabled==1}}
							{{assign var="table_columns_visible" value=$table_columns_visible+1}}
						{{/if}}
					{{/foreach}}
					<tr class="err_list {{if (count($data)>0 || $total_num==0) && !is_array($smarty.post.errors)}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{if count($data)==0 && $total_num>0}}{{$lang.common.dg_list_error}}{{else}}{{$lang.validation.common_header}}{{/if}}</div>
							<div class="err_content">
								{{if is_array($smarty.post.errors)}}
									<ul>
										{{foreach item="error" from=$smarty.post.errors|smarty:nodefaults}}
											<li>{{$error}}</li>
										{{/foreach}}
									</ul>
								{{/if}}
							</div>
						</td>
					</tr>
					{{if count($data)==0 && $total_num==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">name={{$item.title}}</span>
											<span class="js_param">external_id={{$item.external_id}}</span>
											<span class="js_param">password={{$item.password}}</span>
										</span>
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href={{$config.project_url}}/admin/feeds/${external_id}/?password=${password}</span>
					<span class="js_param">title={{$lang.videos.feed_action_test_feed}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=action-open</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
					{{/if}}
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}