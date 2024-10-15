{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="conversion_server_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_conversion_servers_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.conversion_server_add}}{{else}}{{$lang.settings.conversion_server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
		<table class="de_editor">
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="3">
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
				<td class="de_simple_text" colspan="3">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/50-video-conversion-engine-and-video-conversion-speed/">Video conversion engine and video conversion speed</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/118-how-to-add-remote-conversion-server-in-kvs/">How to add remote conversion server in KVS</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.settings.conversion_server_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
				{{if is_array($sidebar_fields)}}
					{{assign var="sidebar_rowspan" value="7"}}
					{{include file="editor_sidebar_inc.tpl"}}
				{{/if}}
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.settings.conversion_server_field_status}}</td>
					<td class="de_control">
						{{if $smarty.post.status_id==2}}
							<span>
								{{$lang.settings.conversion_server_field_status_init}}
							</span>
							<input type="hidden" name="status_id" value="2"/>
							<span class="de_hint">{{$lang.settings.conversion_server_field_status_hint}}</span>
						{{else}}
							<select name="status_id">
								<option value="0" {{if $smarty.post.status_id==0}}selected{{/if}}>{{$lang.settings.conversion_server_field_status_disabled}}</option>
								<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.settings.conversion_server_field_status_active}}</option>
							</select>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_task_types}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="video_admins" {{if in_array('video_admins', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_video_admins}}</label></span>
								<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="video_feeds" {{if in_array('video_feeds', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_video_feeds}}</label></span>
								<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="video_grabbers" {{if in_array('video_grabbers', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_video_grabbers}}</label></span>
								<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="video_users" {{if in_array('video_users', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_video_users}}</label></span>
								<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="video_update" {{if in_array('video_update', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_video_update}}</label></span>
								{{if $config.installation_type>=4}}
									<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="album_admins" {{if in_array('album_admins', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_album_admins}}</label></span>
									<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="album_grabbers" {{if in_array('album_grabbers', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_album_grabbers}}</label></span>
									<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="album_users" {{if in_array('album_users', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_album_users}}</label></span>
									<span class="de_lv_pair"><input type="checkbox" name="task_types[]" value="album_update" {{if in_array('album_update', $smarty.post.task_types) || count($smarty.post.task_types)==0}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_album_update}}</label></span>
								{{/if}}
								<span class="de_hint">{{$lang.settings.conversion_server_field_task_types_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_allow_any_tasks" value="1" {{if $smarty.post.is_allow_any_tasks==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_task_types_allow_any_task}}</label></span>
								<span class="de_hint">{{$lang.settings.conversion_server_field_task_types_allow_any_task_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_max_tasks}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="max_tasks" maxlength="10" size="10" value="{{$smarty.post.max_tasks}}"/>
					</span>
					<span>
						<span class="de_lv_pair"><input type="checkbox" name="max_tasks_priority" value="1" {{if $smarty.post.max_tasks_priority==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_max_tasks_priority}}</label></span>
					</span>
					<span class="de_hint">{{$lang.settings.conversion_server_field_max_tasks_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_priority}}</td>
				<td class="de_control">
					<select name="process_priority">
						<option value="0" {{if $smarty.post.process_priority==0}}selected{{/if}}>{{$lang.settings.conversion_server_field_priority_realtime}}</option>
						<option value="4" {{if $smarty.post.process_priority==4}}selected{{/if}}>{{$lang.settings.conversion_server_field_priority_high}}</option>
						<option value="9" {{if $smarty.post.process_priority==9}}selected{{/if}}>{{$lang.settings.conversion_server_field_priority_medium}}</option>
						<option value="14" {{if $smarty.post.process_priority==14}}selected{{/if}}>{{$lang.settings.conversion_server_field_priority_low}}</option>
						<option value="19" {{if $smarty.post.process_priority==19}}selected{{/if}}>{{$lang.settings.conversion_server_field_priority_very_low}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.conversion_server_field_priority_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_option_optimization}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="option_storage_servers" value="1" {{if $smarty.post.option_storage_servers==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_storage}}</label></span>
								<span class="de_hint">{{$lang.settings.conversion_server_option_optimization_storage_hint}}</span>
							</td>
						</tr>
						<tr class="connection_type_id_1 connection_type_id_2">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="option_pull_source_files" value="1" {{if $smarty.post.option_pull_source_files==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_source}}</label></span>
								<span class="de_hint">{{$lang.settings.conversion_server_option_optimization_source_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $smarty.post.server_id>0}}
				<tr>
					<td class="de_label">{{$lang.settings.conversion_server_field_enable_debug}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_enable_debug_enabled}}</label></span>
						{{if $smarty.post.is_debug_enabled==1}}
							<span>
								(<a href="{{$page_name}}?action=view_debug_log&amp;id={{$smarty.post.server_id}}" rel="log">{{$lang.settings.conversion_server_field_enable_debug_log}}</a>)
							</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.conversion_server_field_log}}</td>
					<td class="de_control">
						<textarea cols="40" rows="5" readonly>{{$smarty.post.log}}</textarea>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.settings.conversion_server_divider_connection}}</h2></td>
			</tr>
			{{if $smarty.get.action=='add_new'}}
				<tr class="connection_type_id_1 connection_type_id_2">
					<td class="de_simple_text" colspan="3">
						<span class="de_hint">{{$lang.settings.conversion_server_divider_connection_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_connection_type}}</td>
				<td class="de_control de_vis_sw_select" colspan="2">
					<select name="connection_type_id">
						<option value="0" {{if $smarty.post.connection_type_id==0}}selected{{/if}}>{{$lang.settings.conversion_server_field_connection_type_local}}</option>
						<option value="1" {{if $smarty.post.connection_type_id==1}}selected{{/if}}>{{$lang.settings.conversion_server_field_connection_type_mount}}</option>
						<option value="2" {{if $smarty.post.connection_type_id==2}}selected{{/if}}>{{$lang.settings.conversion_server_field_connection_type_ftp}}</option>
					</select>
					<span class="de_lv_pair connection_type_id_2"><input type="checkbox" name="ftp_force_ssl" value="1" {{if $smarty.post.ftp_force_ssl==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_field_connection_type_ftp_force_ssl}}</label></span>
					<span class="de_hint">{{$lang.settings.conversion_server_field_connection_type_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_0 connection_type_id_1">
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_path}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="path" maxlength="150" value="{{$smarty.post.path}}"/>
					<span class="de_hint">{{$lang.settings.conversion_server_field_path_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_host}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="ftp_host" maxlength="150" value="{{$smarty.post.ftp_host}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_port}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="ftp_port" maxlength="150" value="{{$smarty.post.ftp_port|default:'21'}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_user}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="ftp_user" maxlength="150" value="{{$smarty.post.ftp_user}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label {{if $smarty.post.connection_type_id!=2}}de_required{{/if}}">{{$lang.settings.conversion_server_field_ftp_password}}</td>
				<td class="de_control {{if $smarty.post.ftp_pass!=''}}de_passw{{/if}}" colspan="2">
					{{if $smarty.post.ftp_pass!=''}}
						<input type="text" data-name="ftp_pass" value="{{$lang.common.password_hidden}}" maxlength="150"/>
					{{else}}
						<input type="password" name="ftp_pass" maxlength="150"/>
					{{/if}}
					<span class="de_hint">{{$lang.settings.conversion_server_field_ftp_password_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label">{{$lang.settings.conversion_server_field_ftp_folder}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="ftp_folder" maxlength="150" value="{{$smarty.post.ftp_folder}}"/>
					<span class="de_hint">{{$lang.settings.conversion_server_field_ftp_folder_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_timeout}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="ftp_timeout" maxlength="150" size="10" value="{{$smarty.post.ftp_timeout|default:'20'}}"/>
				</td>
			</tr>
			{{if $smarty.post.server_id>0}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.settings.conversion_server_divider_config}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="3">
						<span class="de_hint">{{$lang.settings.conversion_server_divider_config_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.conversion_server_field_libraries}}</td>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td>{{$lang.settings.conversion_server_field_libraries_name}}</td>
								<td>{{$lang.settings.conversion_server_field_libraries_path}}</td>
								<td>{{$lang.settings.conversion_server_field_libraries_response}}</td>
							</tr>
							{{if is_array($smarty.post.libraries)}}
								{{foreach key="key" item="item" from=$smarty.post.libraries|smarty:nodefaults}}
									<tr class="eg_data">
										<td>{{$key}}</td>
										<td>{{$item.path|default:$lang.common.undefined}}</td>
										<td>
											{{if $item.is_error==1}}
												<span class="highlighted_text">{{$lang.settings.conversion_server_field_libraries_response_error}}</span>
											{{else}}
												{{$item.message}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							{{else}}
								<tr class="eg_data">
									<td colspan="3">{{$lang.settings.conversion_server_field_libraries_empty}}</td>
								</tr>
							{{/if}}
						</table>
					</td>
				</tr>
				{{if $smarty.post.config!=''}}
					<tr>
						<td class="de_label">{{$lang.settings.conversion_server_field_configuration}}</td>
						<td class="de_control" colspan="2">
							<textarea name="config" cols="40" rows="10">{{$smarty.post.config}}</textarea>
						</td>
					</tr>
				{{/if}}
			{{/if}}
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
		{{if $smarty.post.has_old_api==1}}
			<span class="de_separated_group">
				<input type="submit" name="update_api_version" value="{{$lang.settings.conversion_server_action_update_api}}" data-confirm="{{$lang.settings.conversion_server_action_update_api_confirm|replace:"%1%":$smarty.post.title}}" data-destructive="false"/>
			</span>
		{{/if}}
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
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/></td>
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
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.title}}</span>
										{{if $item.has_old_api!=1}}
											<span class="js_param">update_api_hide=true</span>
										{{/if}}
										{{if $item.status_id==1}}
											<span class="js_param">activate_hide=true</span>
										{{elseif $item.status_id==0}}
											<span class="js_param">deactivate_hide=true</span>
										{{else}}
											<span class="js_param">activate_hide=true</span>
											<span class="js_param">deactivate_hide=true</span>
										{{/if}}
										{{if $item.is_debug_enabled==1}}
											<span class="js_param">enable_debug_hide=true</span>
										{{else}}
											<span class="js_param">disable_debug_hide=true</span>
											<span class="js_param">view_debug_log_hide=true</span>
										{{/if}}
										{{if $item.has_debug_log!=1}}
											<span class="js_param">view_debug_log_disable=true</span>
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
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
					<span class="js_param">hide=${activate_hide}</span>
					<span class="js_param">icon=action-activate</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
					<span class="js_param">hide=${deactivate_hide}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-deactivate</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=update_api&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.conversion_server_action_update_api}}</span>
					<span class="js_param">hide=${update_api_hide}</span>
					<span class="js_param">confirm={{$lang.settings.conversion_server_action_update_api_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-update</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=enable_debug&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
					<span class="js_param">hide=${enable_debug_hide}</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-add</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=disable_debug&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
					<span class="js_param">hide=${disable_debug_hide}</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-delete</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=view_debug_log&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">hide=${view_debug_log_hide}</span>
					<span class="js_param">disable=${view_debug_log_disable}</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=view_conversion_log&amp;id=${id}</span>
					<span class="js_param">title={{$lang.settings.conversion_server_action_view_remote_log}}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-conversion</span>
					<span class="js_param">subicon=action-search</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
					<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
					<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
					<option value="enable_debug">{{$lang.common.dg_batch_actions_enable_debug|replace:"%1%":'${count}'}}</option>
					<option value="disable_debug">{{$lang.common.dg_batch_actions_disable_debug|replace:"%1%":'${count}'}}</option>
					<option value="update_api">{{$lang.settings.conversion_server_batch_action_update_api|replace:"%1%":'${count}'}}</option>
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=update_api</span>
					<span class="js_param">confirm={{$lang.settings.conversion_server_batch_action_update_api_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}