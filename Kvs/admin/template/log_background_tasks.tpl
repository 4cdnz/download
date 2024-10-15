{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="log_background_task_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_background_tasks_log}}</a> / {{$lang.settings.background_task_log_view}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/50-video-conversion-engine-and-video-conversion-speed/">Video conversion engine and video conversion speed</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_status}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.status_id==2}}
							{{$lang.settings.background_task_log_field_status_error}} ({{$smarty.post.message}})
						{{elseif $smarty.post.status_id==3}}
							{{$lang.settings.background_task_log_field_status_completed}}
						{{elseif $smarty.post.status_id==4}}
							{{$lang.settings.background_task_log_field_status_cancelled}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_type}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.type_id==1}}
							{{$lang.settings.common_background_task_type_new_video}}
						{{elseif $smarty.post.type_id==2}}
							{{$lang.settings.common_background_task_type_delete_video}}
						{{elseif $smarty.post.type_id==3}}
							{{$lang.settings.common_background_task_type_upload_video_format_file}} ({{$smarty.post.format_postfix}})
						{{elseif $smarty.post.type_id==4}}
							{{$lang.settings.common_background_task_type_create_video_format_file}} ({{$smarty.post.format_postfix}})
						{{elseif $smarty.post.type_id==5}}
							{{$lang.settings.common_background_task_type_delete_video_format_file}} ({{$smarty.post.format_postfix}})
						{{elseif $smarty.post.type_id==6}}
							{{$lang.settings.common_background_task_type_delete_video_format}} ({{$smarty.post.format_postfix}})
						{{elseif $smarty.post.type_id==7}}
							{{$lang.settings.common_background_task_type_create_screenshot_format}} ({{$smarty.post.format_size}})
						{{elseif $smarty.post.type_id==8}}
							{{$lang.settings.common_background_task_type_create_timeline_screenshots}}
						{{elseif $smarty.post.type_id==9}}
							{{$lang.settings.common_background_task_type_delete_screenshot_format}} ({{$smarty.post.format_size}})
						{{elseif $smarty.post.type_id==10}}
							{{$lang.settings.common_background_task_type_new_album}}
						{{elseif $smarty.post.type_id==11}}
							{{$lang.settings.common_background_task_type_delete_album}}
						{{elseif $smarty.post.type_id==12}}
							{{$lang.settings.common_background_task_type_create_album_format}} ({{$smarty.post.format_size}})
						{{elseif $smarty.post.type_id==13}}
							{{$lang.settings.common_background_task_type_delete_album_format}} ({{$smarty.post.format_size}})
						{{elseif $smarty.post.type_id==14}}
							{{$lang.settings.common_background_task_type_upload_album_images}}
						{{elseif $smarty.post.type_id==15}}
							{{$lang.settings.common_background_task_type_change_storage_group_video}}
						{{elseif $smarty.post.type_id==16}}
							{{$lang.settings.common_background_task_type_create_screenshots_zip}}
						{{elseif $smarty.post.type_id==17}}
							{{$lang.settings.common_background_task_type_delete_screenshots_zip}}
						{{elseif $smarty.post.type_id==18}}
							{{$lang.settings.common_background_task_type_create_images_zip}}
						{{elseif $smarty.post.type_id==19}}
							{{$lang.settings.common_background_task_type_delete_images_zip}}
						{{elseif $smarty.post.type_id==20}}
							{{$lang.settings.common_background_task_type_delete_timeline_screenshots}}
						{{elseif $smarty.post.type_id==21}}
							{{$lang.settings.common_background_task_type_create_images_zip}}
						{{elseif $smarty.post.type_id==22}}
							{{$lang.settings.common_background_task_type_album_images_manipulation}}
						{{elseif $smarty.post.type_id==23}}
							{{$lang.settings.common_background_task_type_change_storage_group_album}}
						{{elseif $smarty.post.type_id==24}}
							{{$lang.settings.common_background_task_type_create_overview_screenshots}}
						{{elseif $smarty.post.type_id==26}}
							{{$lang.settings.common_background_task_type_update_resolution_type}}
						{{elseif $smarty.post.type_id==27}}
							{{$lang.settings.common_background_task_type_sync_storage_server}}
						{{elseif $smarty.post.type_id==28}}
							{{$lang.settings.common_background_task_type_delete_overview_screenshots}}
						{{elseif $smarty.post.type_id==29}}
							{{$lang.settings.common_background_task_type_recreate_screenshot_formats}}
						{{elseif $smarty.post.type_id==30}}
							{{$lang.settings.common_background_task_type_recreate_album_formats}}
						{{elseif $smarty.post.type_id==31}}
							{{$lang.settings.common_background_task_type_recreate_player_preview}}
						{{elseif $smarty.post.type_id==50}}
							{{$lang.settings.common_background_task_type_videos_import}}
						{{elseif $smarty.post.type_id==51}}
							{{$lang.settings.common_background_task_type_albums_import}}
						{{elseif $smarty.post.type_id==52}}
							{{$lang.settings.common_background_task_type_videos_mass_edit}}
						{{elseif $smarty.post.type_id==53}}
							{{$lang.settings.common_background_task_type_albums_mass_edit}}
						{{/if}}
					</span>
				</td>
			</tr>
			{{if $smarty.post.server_id>0}}
				<tr>
					<td class="de_label">{{$lang.settings.background_task_log_field_server}}</td>
					<td class="de_control">
						{{if in_array('system|servers',$smarty.session.permissions)}}
							<a href="{{if $config.installation_type>=3}}servers_conversion.php?action=change&item_id={{$smarty.post.server_id}}{{else}}servers_conversion_basic.php{{/if}}">{{$smarty.post.server|default:$smarty.post.server_id}}</a>
						{{else}}
							<span>{{$smarty.post.server|default:$smarty.post.server_id}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_start_date}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.start_date=='0000-00-00 00:00:00'}}
							{{$lang.common.undefined}}
						{{else}}
							{{$smarty.post.start_date|date_format:$smarty.session.userdata.full_date_format}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_end_date}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.end_date=='0000-00-00 00:00:00'}}
							{{$lang.common.undefined}}
						{{else}}
							{{$smarty.post.end_date|date_format:$smarty.session.userdata.full_date_format}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_duration}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.start_date=='0000-00-00 00:00:00'}}
							{{$lang.common.undefined}}
						{{else}}
							{{$smarty.post.duration}}
						{{/if}}
					</span>
					<a href="{{$page_name}}?action=task_log&amp;item_id={{$smarty.post.task_id}}" rel="log">{{$lang.settings.background_task_log_action_view_log_task}}</a>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_details}}</td>
				<td class="de_control">
					{{if count($smarty.post.phases)>0}}
						{{assign var="current_level" value=0}}
						{{section loop=$smarty.post.phases|@count name="phases"}}
							{{assign var="index" value=$smarty.section.phases.index}}
							{{assign var="index_next" value=$smarty.section.phases.index+1}}
							{{assign var="index_prev" value=$smarty.section.phases.index-1}}

							{{if !$smarty.section.phases.first && $smarty.post.phases[$index_prev].level<$smarty.post.phases[$index].level}}
								<block style="display: block; padding-left: 20px; padding-top: 5px; padding-bottom: 5px" {{if $smarty.post.phases[$index_prev].level==0}}class="phase-{{$smarty.post.phases[$index_prev].id}} hidden"{{/if}}>
								{{assign var="current_level" value=$smarty.post.phases[$index].level}}
							{{/if}}

							{{if $current_level==0}}
								<block style="display: block; margin-bottom: 5px;">
									{{if $smarty.post.phases[$index].id!='PE' && $smarty.post.phases[$index].id!='CE' && $smarty.post.phases[$index].id!='FE' && $smarty.post.phases[$index].id!='E' && $smarty.post.phases[$index].id!='I' && $smarty.post.phases[$index].id!='IE'}}
										<span data-accordeon="phase-{{$smarty.post.phases[$index].id}}">{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</span><br/>
									{{else}}
										<span>{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</span><br/>
									{{/if}}
								</block>
							{{else}}
								<span>{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</span><br/>
							{{/if}}

							{{if !$smarty.section.phases.last && $smarty.post.phases[$index_next].level<$smarty.post.phases[$index].level}}
								{{section name="closing_phase" loop=$smarty.post.phases[$index].level-$smarty.post.phases[$index_next].level}}
									</block>
								{{/section}}
								{{assign var="current_level" value=$smarty.post.phases[$index_next].level}}
							{{/if}}
						{{/section}}
						{{section name="closing_phase" loop=$current_level}}
							</block>
						{{/section}}
					{{else}}
						<span>{{$lang.settings.background_task_log_field_details_empty}}</span>
					{{/if}}
				</td>
			</tr>
		</table>
	</div>
</form>

{{else}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
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
				<a class="dgf_filters"><i class="icon icon-action-filter"></i>{{$lang.common.dg_filter_filters}}</a>
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$lang.common.dg_filter_btn_submit}}"/>
				</div>
			</div>
			<div class="dgf_advanced_filters">
				<div class="dgf_filter">
					<select name="se_status_id">
						<option value="">{{$lang.settings.background_task_log_field_status}}...</option>
						{{foreach from=$list_status_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_status_id!='' && $smarty.session.save.$page_name.se_status_id=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_type_id">
						<option value="">{{$lang.settings.background_task_log_field_type}}...</option>
						{{foreach from=$list_type_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_type_id>0 && $smarty.session.save.$page_name.se_type_id=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_error_code">
						<option value="">{{$lang.settings.background_task_log_field_error_code}}...</option>
						{{foreach from=$list_error_code_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_error_code>0 && $smarty.session.save.$page_name.se_error_code=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_server_id">
						<option value="">{{$lang.settings.background_task_log_field_server}}...</option>
						{{foreach from=$list_conversion_servers|smarty:nodefaults item="item"}}
							<option value="{{$item.server_id}}" {{if $smarty.session.save.$page_name.se_server_id==$item.server_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_object_type_id">
						<option value="">{{$lang.common.object_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected{{/if}}>{{$lang.common.object_type_videos}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected{{/if}}>{{$lang.common.object_type_albums}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_object_id" size="10" value="{{$smarty.session.save.$page_name.se_object_id}}" placeholder="{{$lang.common.object_id}}..."/>
				</div>
				<div class="dgf_filter de_vis_sw_select">
					<select name="se_period_id">
						<option value="">{{$lang.stats.common_period}}...</option>
						<option value="today" {{if $smarty.session.save.$page_name.se_period_id=='today'}}selected{{/if}}>{{$lang.stats.common_period_today}}</option>
						<option value="yesterday" {{if $smarty.session.save.$page_name.se_period_id=='yesterday'}}selected{{/if}}>{{$lang.stats.common_period_yesterday}}</option>
						<option value="days7" {{if $smarty.session.save.$page_name.se_period_id=='days7'}}selected{{/if}}>{{$lang.stats.common_period_days7}}</option>
						<option value="days30" {{if $smarty.session.save.$page_name.se_period_id=='days30'}}selected{{/if}}>{{$lang.stats.common_period_days30}}</option>
						<option value="current_month" {{if $smarty.session.save.$page_name.se_period_id=='current_month'}}selected{{/if}}>{{$lang.stats.common_period_current_month}}</option>
						<option value="prev_month" {{if $smarty.session.save.$page_name.se_period_id=='prev_month'}}selected{{/if}}>{{$lang.stats.common_period_prev_month}}</option>
						<option value="custom" {{if $smarty.session.save.$page_name.se_period_id=='custom'}}selected{{/if}}>{{$lang.stats.common_period_custom}}</option>
					</select>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_from" value="{{$smarty.session.save.$page_name.se_date_from}}" placeholder="{{$lang.stats.common_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_to" value="{{$smarty.session.save.$page_name.se_date_to}}" placeholder="{{$lang.stats.common_date_to}}...">
					</div>
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										{{if $item.video_id>0}}
											<span class="js_param">video_id={{$item.video_id}}</span>
										{{else}}
											<span class="js_param">video_log_hide=true</span>
										{{/if}}
										{{if $item.album_id>0}}
											<span class="js_param">album_id={{$item.album_id}}</span>
										{{else}}
											<span class="js_param">album_log_hide=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?action=task_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_task}}</span>
					<span class="js_param">disable=${task_log_disable}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=videos.php?action=video_log&amp;item_id=${video_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_video}}</span>
					<span class="js_param">hide=${video_log_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-video</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=albums.php?action=album_log&amp;item_id=${album_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_album}}</span>
					<span class="js_param">hide=${album_log_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-album</span>
					<span class="js_param">subicon=action-search</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>
		</div>
	</form>
</div>

{{/if}}