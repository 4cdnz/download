{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="conversion_server_edit">
	<div class="de_main">
		<div class="de_header"><h1>{{if $smarty.post.server_id<1}}{{$lang.settings.conversion_server_add}}{{else}}{{$lang.settings.conversion_server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
			{{if $smarty.post.server_id>0}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.settings.conversion_server_divider_general}}</h2></td>
				</tr>
			{{/if}}
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
			<tr>
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_path}}</td>
				<td class="de_control">
					<input type="text" name="path" maxlength="150" value="{{$smarty.post.path}}"/>
					<span class="de_hint">{{$lang.settings.conversion_server_field_path_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_max_tasks}}</td>
				<td class="de_control">
					<input type="text" name="max_tasks" maxlength="10" size="10" value="{{$smarty.post.max_tasks|default:5}}"/>
					<span class="de_hint">{{$lang.settings.conversion_server_field_max_tasks_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_option_optimization}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="option_storage_servers" value="1" {{if $smarty.post.option_storage_servers==1}}checked{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_storage}}</label></span>
					<span class="de_hint">{{$lang.settings.conversion_server_option_optimization_storage_hint}}</span>
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
							{{foreachelse}}
								<tr class="eg_data_text">
									<td colspan="3">{{$lang.settings.conversion_server_field_libraries_empty}}</td>
								</tr>
							{{/foreach}}
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
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.post.server_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
		{{if $smarty.post.has_old_api==1}}
			<span class="de_separated_group">
				<input type="submit" name="update_api_version" value="{{$lang.settings.conversion_server_action_update_api}}" data-confirm="{{$lang.settings.conversion_server_action_update_api_confirm|replace:"%1%":$smarty.post.title}}" data-destructive="false"/>
			</span>
		{{/if}}
	</div>
</form>