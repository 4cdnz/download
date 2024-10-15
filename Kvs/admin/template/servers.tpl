{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new_group' || $smarty.get.action=='change_group'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="storage_group_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_storage_servers_list}}</a> / {{if $smarty.get.action=='add_new_group'}}{{$lang.settings.server_group_add}}{{else}}{{$lang.settings.server_group_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/36-storage-system-in-kvs-tube-script/">Storage system in KVS</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.server_group_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.server_group_field_title}}</td>
				<td class="de_control"><input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_group_field_content_type}}</td>
				<td class="de_control">
					<select name="content_type_id" {{if $smarty.get.action!='add_new_group'}}disabled{{/if}}>
						<option value="1" {{if $smarty.post.content_type_id==1}}selected{{/if}}>{{$lang.settings.server_group_field_content_type_videos}}</option>
						<option value="2" {{if $smarty.post.content_type_id==2}}selected{{/if}}>{{$lang.settings.server_group_field_content_type_albums}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_group_field_status}}</td>
				<td class="de_control">
					<select name="status_id">
						<option value="0" {{if $smarty.post.status_id==0}}selected{{/if}}>{{$lang.settings.server_group_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.settings.server_group_field_status_active}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.server_group_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.server_group_divider_load_balancing}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<tr class="eg_header">
							<td>{{$lang.settings.server_group_servers_name}}</td>
							<td>{{$lang.settings.server_group_servers_status}}</td>
							<td>{{$lang.settings.server_group_servers_weight}}</td>
							<td>{{$lang.settings.server_group_servers_countries}}</td>
						</tr>
						{{if count($smarty.post.servers)<2}}
							<tr class="eg_data">
								<td colspan="4">{{$lang.settings.server_group_divider_load_balancing_empty}}</td>
							</tr>
						{{else}}
							{{foreach item="item" from=$smarty.post.servers|smarty:nodefaults}}
								<tr class="eg_data {{if $item.status_id==0}}disabled{{/if}}">
									<td>
										<a href="servers.php?action=change&amp;item_id={{$item.server_id}}">{{$item.title}}</a>
									</td>
									<td>
										<select name="status_id_{{$item.server_id}}">
											<option value="0" {{if $item.status_id==0}}selected{{/if}}>{{$lang.settings.server_group_servers_status_disabled}}</option>
											<option value="1" {{if $item.status_id==1}}selected{{/if}}>{{$lang.settings.server_group_servers_status_active}}</option>
										</select>
									</td>
									<td>
										<input type="text" name="weight_{{$item.server_id}}" size="10" value="{{$item.lb_weight}}" maxlength="5"/>
									</td>
									<td>
										<div class="de_insight_list">
											<div class="js_params">
												<span class="js_param">url=async/insight.php?type=countries</span>
												<span class="js_param">submit_mode=compound</span>
												<span class="js_param">submit_name=countries_{{$item.server_id}}[]</span>
												<span class="js_param">allow_creation=true</span>
												<span class="js_param">empty_message={{$lang.settings.server_group_servers_countries_empty}}</span>
											</div>
											<div class="list"></div>
											{{foreach name="data" item="country" from=$item.lb_countries|smarty:nodefaults}}
												<input type="hidden" name="countries_{{$item.server_id}}[]" value="{{$country}}" alt="{{$list_countries[$country]}}"/>
											{{/foreach}}
											<div class="controls">
												<input type="text"/>
												<input type="button" class="add" value="{{$lang.common.add}}"/>
												<input type="button" class="all" value="{{$lang.settings.vast_profile_field_include_countries_all}}"/>
											</div>
										</div>
									</td>
								</tr>
							{{/foreach}}
						{{/if}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new_group'}}
			<input type="hidden" name="action" value="add_new_group_complete"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
			{{/if}}
		{{else}}
			<input type="hidden" name="action" value="change_group_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="storage_server_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_storage_servers_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.server_add}}{{else}}<a href="{{$page_name}}?action=change_group&amp;item_id={{$smarty.post.group_id}}">{{$smarty.post.group_title}}</a> / {{$lang.settings.server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/36-storage-system-in-kvs-tube-script/">Storage system in KVS</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/41-how-to-add-remote-content-servers-for-videos-and-photos-into-kvs-tube-script/">How to add remote content servers for videos and photos</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.settings.server_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.server_field_title}}</td>
				<td class="de_control"><input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/></td>
				{{if is_array($sidebar_fields)}}
					{{assign var="sidebar_rowspan" value="4"}}
					{{include file="editor_sidebar_inc.tpl"}}
				{{/if}}
			</tr>
			{{assign var="group_vis_str_videos" value=""}}
			{{assign var="group_vis_str_albums" value=""}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.server_field_group}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="group_id" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
						{{if $smarty.get.action=='add_new'}}
							<option value="new">{{$lang.settings.server_field_group_new}}</option>
						{{/if}}
						<optgroup label="{{$lang.settings.server_field_group_videos}}">
							{{foreach item="item" from=$list_groups_videos|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $smarty.post.group_id==$item.group_id}}selected{{/if}}>{{$item.title}} ({{$lang.settings.server_field_group_videos}})</option>
								{{assign var="group_vis_str_videos" value="`$group_vis_str_videos` group_id_`$item.group_id`"}}
							{{/foreach}}
						</optgroup>
						<optgroup label="{{$lang.settings.server_field_group_albums}}">
							{{foreach item="item" from=$list_groups_albums|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $smarty.post.group_id==$item.group_id}}selected{{/if}}>{{$item.title}} ({{$lang.settings.server_field_group_albums}})</option>
								{{assign var="group_vis_str_albums" value="`$group_vis_str_albums` group_id_`$item.group_id`"}}
							{{/foreach}}
						</optgroup>
					</select>
					{{if $smarty.get.action=='add_new'}}
						<span class="group_id_new">
							<select name="content_type_id">
								<option value="1">{{$lang.settings.server_field_group_videos}}</option>
								<option value="2">{{$lang.settings.server_field_group_albums}}</option>
							</select>
						</span>
					{{/if}}
					<span class="de_hint">{{$lang.settings.server_field_group_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_field_streaming_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="streaming_type_id">
						<option value="0" {{if $smarty.post.streaming_type_id==0}}selected{{/if}}>{{$lang.settings.server_field_streaming_type_nginx}}</option>
						<option value="1" {{if $smarty.post.streaming_type_id==1}}selected{{/if}}>{{$lang.settings.server_field_streaming_type_apache}}</option>
						<option value="4" {{if $smarty.post.streaming_type_id==4}}selected{{/if}}>{{$lang.settings.server_field_streaming_type_cdn}}</option>
						<option value="5" {{if $smarty.post.streaming_type_id==5}}selected{{/if}}>{{$lang.settings.server_field_streaming_type_backup}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.server_field_streaming_type_hint}}</span>
				</td>
			</tr>
			<tr class="streaming_type_id_0 streaming_type_id_1 streaming_type_id_4">
				<td class="de_label de_required">{{$lang.settings.server_field_urls}}</td>
				<td class="de_control">
					<input type="text" name="urls" value="{{$smarty.post.urls}}"/>
					<span class="de_hint {{$group_vis_str_videos}}">{{$lang.settings.server_field_urls_videos_hint}}</span>
					<span class="de_hint {{$group_vis_str_albums}}">{{$lang.settings.server_field_urls_albums_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.nginx_config_rules!=''}}
				<tr class="streaming_type_id_0">
					<td class="de_label">{{$lang.settings.server_field_nginx_config_rules}}</td>
					<td class="de_control">
						<div class="code_editor">
							<textarea name="nginx_config" rows="{{$smarty.post.nginx_config_rules_rows|default:"3"}}" readonly>{{$smarty.post.nginx_config_rules}}</textarea>
						</div>
						{{if $smarty.post.content_type_id==1}}
							<span class="de_hint">{{$lang.settings.server_field_nginx_config_rules_videos_hint}}</span>
						{{elseif $smarty.post.content_type_id==2}}
							<span class="de_hint">{{$lang.settings.server_field_nginx_config_rules_albums_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr class="streaming_type_id_4">
				<td class="de_label de_required">{{$lang.settings.server_field_streaming_script}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="streaming_script" size="30" value="{{$smarty.post.streaming_script}}" maxlength="255"/>
					</span>
					<span>
						<a href="{{$page_name}}?action=download_api_cdn">{{$lang.settings.server_field_streaming_script_dl}}</a>
					</span>
					<span class="de_hint">{{$lang.settings.server_field_streaming_script_hint}}</span>
				</td>
			</tr>
			<tr class="streaming_type_id_4">
				<td class="de_label de_required">{{$lang.settings.server_field_streaming_secret_key}}</td>
				<td class="de_control">
					<input type="text" name="streaming_key" size="30" value="{{$smarty.post.streaming_key}}" maxlength="255"/>
					<span class="de_hint">{{$lang.settings.server_field_streaming_secret_key_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.server_id>0}}
				<tr>
					<td class="de_label">{{$lang.settings.server_field_enable_debug}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.settings.server_field_enable_debug_enabled}}</label></span>
						{{if $smarty.post.is_debug_enabled==1}}
							<span>
								(<a href="{{$page_name}}?action=view_debug_log&amp;id={{$smarty.post.server_id}}" rel="log">{{$lang.settings.server_field_enable_debug_log}}</a>)
							</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.settings.server_divider_connection}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="3">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/3875-how-to-use-amazon-s3-in-kvs-for-storage-streaming-and-backup-storage/">How to use Amazon S3 in KVS for storage / streaming and backup storage</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_field_connection_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="connection_type_id">
						<option value="0" {{if $smarty.post.connection_type_id==0}}selected{{/if}}>{{$lang.settings.server_field_connection_type_local}}</option>
						<option value="1" {{if $smarty.post.connection_type_id==1}}selected{{/if}}>{{$lang.settings.server_field_connection_type_mount}}</option>
						<option value="2" {{if $smarty.post.connection_type_id==2}}selected{{/if}}>{{$lang.settings.server_field_connection_type_ftp}}</option>
						<option value="3" {{if $smarty.post.connection_type_id==3}}selected{{/if}}>{{$lang.settings.server_field_connection_type_s3}}</option>
					</select>
					<span class="de_lv_pair connection_type_id_2"><input type="checkbox" name="ftp_force_ssl" value="1" {{if $smarty.post.ftp_force_ssl==1}}checked{{/if}}/><label>{{$lang.settings.server_field_connection_type_ftp_force_ssl}}</label></span>
					<span class="de_hint">{{$lang.settings.server_field_connection_type_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_0 connection_type_id_1">
				<td class="de_label de_required">{{$lang.settings.server_field_path}}</td>
				<td class="de_control">
					<input type="text" name="path" maxlength="150" value="{{$smarty.post.path}}"/>
					<span class="de_hint {{$group_vis_str_videos}}">{{$lang.settings.server_field_path_videos_hint}}</span>
					<span class="de_hint {{$group_vis_str_albums}}">{{$lang.settings.server_field_path_albums_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.server_field_ftp_host}}</td>
				<td class="de_control">
					<input type="text" name="ftp_host" maxlength="150" value="{{$smarty.post.ftp_host}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.server_field_ftp_port}}</td>
				<td class="de_control">
					<input type="text" name="ftp_port" maxlength="150" value="{{$smarty.post.ftp_port|default:'21'}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.server_field_ftp_user}}</td>
				<td class="de_control">
					<input type="text" name="ftp_user" maxlength="150" value="{{$smarty.post.ftp_user}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label {{if $smarty.post.connection_type_id!=2}}de_required{{/if}}">{{$lang.settings.server_field_ftp_password}}</td>
				<td class="de_control {{if $smarty.post.ftp_pass!=''}}de_passw{{/if}}">
					{{if $smarty.post.ftp_pass!=''}}
						<input type="text" data-name="ftp_pass" value="{{$lang.common.password_hidden}}" maxlength="150"/>
					{{else}}
						<input type="password" name="ftp_pass" maxlength="150"/>
					{{/if}}
					<span class="de_hint">{{$lang.settings.server_field_ftp_password_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label">{{$lang.settings.server_field_ftp_folder}}</td>
				<td class="de_control">
					<input type="text" name="ftp_folder" maxlength="150" value="{{$smarty.post.ftp_folder}}"/>
					<span class="de_hint">{{$lang.settings.server_field_ftp_folder_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_2">
				<td class="de_label de_required">{{$lang.settings.server_field_ftp_timeout}}</td>
				<td class="de_control">
					<input type="text" name="ftp_timeout" maxlength="150" size="10" value="{{$smarty.post.ftp_timeout|default:'20'}}"/>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label de_required">{{$lang.settings.server_field_s3_region}}</td>
				<td class="de_control">
					<input type="text" name="s3_region" maxlength="150" value="{{$smarty.post.s3_region}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_region_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label">{{$lang.settings.server_field_s3_endpoint}}</td>
				<td class="de_control">
					<input type="text" name="s3_endpoint" maxlength="150" value="{{$smarty.post.s3_endpoint}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_endpoint_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label de_required">{{$lang.settings.server_field_s3_bucket}}</td>
				<td class="de_control">
					<input type="text" name="s3_bucket" maxlength="150" value="{{$smarty.post.s3_bucket}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_bucket_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label">{{$lang.settings.server_field_s3_prefix}}</td>
				<td class="de_control">
					<input type="text" name="s3_prefix" maxlength="150" value="{{$smarty.post.s3_prefix}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_prefix_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label de_required">{{$lang.settings.server_field_s3_api_key}}</td>
				<td class="de_control">
					<input type="text" name="s3_api_key" maxlength="150" value="{{$smarty.post.s3_api_key}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_api_key_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label {{if $smarty.post.connection_type_id!=3}}de_required{{/if}}">{{$lang.settings.server_field_s3_api_secret}}</td>
				<td class="de_control {{if $smarty.post.s3_api_secret!=''}}de_passw{{/if}}">
					{{if $smarty.post.s3_api_secret!=''}}
						<input type="text" data-name="s3_api_secret" value="{{$lang.common.password_hidden}}" maxlength="150"/>
					{{else}}
						<input type="password" name="s3_api_secret" maxlength="150"/>
					{{/if}}
					<span class="de_hint">{{$lang.settings.server_field_s3_api_secret_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label">{{$lang.settings.server_field_s3_timeout}}</td>
				<td class="de_control">
					<input type="text" name="s3_timeout" maxlength="10" size="10" value="{{if $smarty.post.s3_timeout>0}}{{$smarty.post.s3_timeout}}{{/if}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_timeout_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label">{{$lang.settings.server_field_s3_upload_chunk_size_mb}}</td>
				<td class="de_control">
					<input type="text" name="s3_upload_chunk_size_mb" maxlength="10" size="10" value="{{if $smarty.post.s3_upload_chunk_size_mb>0}}{{$smarty.post.s3_upload_chunk_size_mb}}{{/if}}"/>
					<span class="de_hint">{{$lang.settings.server_field_s3_upload_chunk_size_mb_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_3">
				<td class="de_label">{{$lang.settings.server_field_s3_is_endpoint_subdirectory}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="s3_is_endpoint_subdirectory" value="1" {{if $smarty.post.s3_is_endpoint_subdirectory==1}}checked{{/if}}/><label>{{$lang.settings.server_field_s3_is_endpoint_subdirectory_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.server_field_s3_is_endpoint_subdirectory_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
				<td class="de_label de_required">{{$lang.settings.server_field_control_script_url}}</td>
				<td class="de_control">
					<input type="text" name="control_script_url" maxlength="150" readonly data-autopopulate-from="urls" data-autopopulate-pattern="${protocol}//${host}/remote_control.php"/>
					<span class="de_hint">{{$lang.settings.server_field_control_script_url_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
				<td class="de_label">{{$lang.settings.server_field_control_script_api_version}}</td>
				<td class="de_control">
					<span>
						{{$smarty.post.control_script_url_version|default:$lang.common.undefined}}
					</span>
					<span>
						<a href="{{$page_name}}?action=download_api">{{$lang.settings.server_field_control_script_api_version_dl|replace:"%1%":$latest_api_version}}</a>
					</span>
					<span class="de_hint">{{$lang.settings.server_field_control_script_api_version_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
				<td class="de_label">{{$lang.settings.server_field_control_script_lock_ip}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="control_script_url_lock_ip" value="1" {{if $smarty.post.control_script_url_lock_ip==1}}checked{{/if}} {{if $smarty.post.control_script_url_version!='' && $smarty.post.control_script_url_lock_ip==0 && $smarty.post.numeric_control_script_url_version<391}}disabled{{/if}}/><label>{{$lang.settings.server_field_control_script_lock_ip_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.server_field_control_script_lock_ip_hint}}</span>
				</td>
			</tr>
			<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
				<td class="de_label">{{$lang.settings.server_field_time_offset}}</td>
				<td class="de_control">
					<input type="text" name="time_offset" maxlength="5" size="10" value="{{$smarty.post.time_offset|default:0}}"/>
					<span class="de_hint">{{$lang.settings.server_field_time_offset_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.settings.server_divider_advanced}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_field_replace_domain_on_satellite}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_replace_domain_on_satellite" value="1" {{if $smarty.post.is_replace_domain_on_satellite==1}}checked{{/if}}/><label>{{$lang.settings.server_field_replace_domain_on_satellite_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.server_field_replace_domain_on_satellite_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.server_field_skip_ssl_check}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="streaming_skip_ssl_check" value="1" {{if $smarty.post.streaming_skip_ssl_check==1}}checked{{/if}}/><label>{{$lang.settings.server_field_skip_ssl_check_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.server_field_skip_ssl_check_hint}}</span>
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

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
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
					{{foreach item="item_group" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header {{if $item_group.status_id==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_group.group_id}}" disabled/></td>
							{{assign var="item" value=$item_group|smarty:nodefaults}}
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item_group.is_editing_forbidden!=1}}href="{{$page_name}}?action=change_group&amp;item_id={{$item_group.group_id}}"{{/if}} class="edit {{if $item_server.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								{{if $item_group.total_servers_amount==0}}
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id=0</span>
											<span class="js_param">g_id={{$item_group.group_id}}</span>
											<span class="js_param">name={{$item_group.title}}</span>
											<span class="js_param">delete_hide=true</span>
											<span class="js_param">enable_debug_hide=true</span>
											<span class="js_param">disable_debug_hide=true</span>
											<span class="js_param">view_debug_log_hide=true</span>
											<span class="js_param">activate_hide=true</span>
											<span class="js_param">deactivate_hide=true</span>
											<span class="js_param">sync_hide=true</span>
											<span class="js_param">test_hide=true</span>
										</span>
									</a>
								{{/if}}
							</td>
						</tr>
						{{foreach name="data_servers" item="item_server" from=$item_group.servers|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data_servers.iteration % 2==0}} dg_even{{/if}} {{if $item_server.status_id==0}}disabled{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_server.server_id}}"/></td>
								{{assign var="item" value=$item_server|smarty:nodefaults}}
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td class="nowrap">
									<a {{if $item_server.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item_server.server_id}}"{{/if}} class="edit {{if $item_server.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item_server.server_id}}</span>
											<span class="js_param">name={{$item_server.title}}</span>
											<span class="js_param">g_id=0</span>
											<span class="js_param">delete_group_hide=true</span>
											{{if $item_server.status_id==1 && $item_group.active_servers_amount==1 && $item_group.total_content|intval>0}}
												<span class="js_param">delete_disable=true</span>
											{{/if}}
											{{if $item_group.active_servers_amount==1}}
												<span class="js_param">deactivate_disable=true</span>
											{{/if}}
											{{if $item_server.status_id==1}}
												<span class="js_param">activate_hide=true</span>
											{{else}}
												<span class="js_param">deactivate_hide=true</span>
											{{/if}}
											{{if $item_group.total_servers_amount<=1}}
												<span class="js_param">sync_hide=true</span>
											{{/if}}
											{{if $sync_tasks_count>0}}
												<span class="js_param">sync_disable=true</span>
											{{/if}}
											{{if $item_group.total_content|intval==0}}
												<span class="js_param">test_disable=true</span>
											{{/if}}
											{{if $item_server.is_debug_enabled==1}}
												<span class="js_param">enable_debug_hide=true</span>
											{{else}}
												<span class="js_param">disable_debug_hide=true</span>
											{{/if}}
											{{if $item_server.has_debug_log!=1}}
												<span class="js_param">view_debug_log_hide=true</span>
											{{/if}}
										</span>
									</a>
								</td>
							</tr>
						{{/foreach}}
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?action=delete&amp;g_id=${g_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${delete_group_hide}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=delete&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.server_action_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${delete_hide}</span>
					<span class="js_param">disable=${delete_disable}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=activate&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
					<span class="js_param">hide=${activate_hide}</span>
					<span class="js_param">confirm={{$lang.settings.server_action_activate_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-activate</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=deactivate&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
					<span class="js_param">hide=${deactivate_hide}</span>
					<span class="js_param">disable=${deactivate_disable}</span>
					<span class="js_param">confirm={{$lang.settings.server_action_deactivate_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-deactivate</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=sync&amp;id=${id}</span>
					<span class="js_param">title={{$lang.settings.server_action_sync}}</span>
					<span class="js_param">hide=${sync_hide}</span>
					<span class="js_param">disable=${sync_disable}</span>
					<span class="js_param">confirm={{$lang.settings.server_action_sync_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">icon=action-sync</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=enable_debug&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
					<span class="js_param">hide=${enable_debug_hide}</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-add</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=disable_debug&amp;id=${id}</span>
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
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=servers_test.php?server_id=${id}</span>
					<span class="js_param">title={{$lang.settings.server_action_test_content}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">hide=${test_hide}</span>
					<span class="js_param">disable=${test_disable}</span>
					<span class="js_param">icon=action-approve</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>
		</div>
	</form>
</div>

{{/if}}