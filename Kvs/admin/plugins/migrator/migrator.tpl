{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.migrator.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.migrator.long_desc}}
				</td>
			</tr>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.migrator.divider_parameters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_script}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="old_script">
						<option value="no">{{$lang.common.select_default_option}}</option>
						{{foreach key="key" item="item" from=$smarty.post.migrators|smarty:nodefaults}}
							<option value="{{$key}}" {{if $smarty.post.old_script==$key || ($smarty.post.old_script=='' && $key==$smarty.post.migrators_default)}}selected{{/if}}>{{$item}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_path}}</td>
				<td class="de_control">
					<input type="text" name="old_path" maxlength="1000" value="{{$smarty.post.old_path}}"/>
					<span class="de_hint">{{$lang.plugins.migrator.field_old_path_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_url}}</td>
				<td class="de_control">
					<input type="text" name="old_url" maxlength="1000" value="{{$smarty.post.old_url}}"/>
					<span class="de_hint">{{$lang.plugins.migrator.field_old_url_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_url}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_url" maxlength="1000" value="{{$smarty.post.old_mysql_url|default:"localhost"}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_port}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_port" maxlength="1000" value="{{$smarty.post.old_mysql_port|default:"3306"}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_user}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_user" maxlength="1000" value="{{$smarty.post.old_mysql_user}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_pass}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_pass" maxlength="1000" value="{{$smarty.post.old_mysql_pass}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_name}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_name" maxlength="1000" value="{{$smarty.post.old_mysql_name}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_old_mysql_charset}}</td>
				<td class="de_control">
					<input type="text" name="old_mysql_charset" maxlength="1000" value="{{$smarty.post.old_mysql_charset|default:"utf8"}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.migrator.field_migrate_data}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.tags==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_tags" value="1" {{if $smarty.post.migrate_tags==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_tags}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.categories==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_categories" value="1" {{if $smarty.post.migrate_categories==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_categories}}</label></span></td>
						</tr>
						{{if $config.installation_type>=2}}
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.models==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_models" value="1" {{if $smarty.post.migrate_models==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_models}}</label></span></td>
							</tr>
						{{/if}}
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.content_sources==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_content_sources" value="1" {{if $smarty.post.migrate_content_sources==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_content_sources}}</label></span></td>
						</tr>
						{{if $config.installation_type==4}}
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.dvds==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_dvds" value="1" {{if $smarty.post.migrate_dvds==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_dvds}}</label></span></td>
							</tr>
						{{/if}}
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.videos==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_videos" value="1" {{if $smarty.post.migrate_videos==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_videos}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" class="migrate_videos_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.videos_screenshots==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_videos_screenshots" value="1" {{if $smarty.post.migrate_videos_screenshots==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_videos_screenshots}}</label></span></td>
						</tr>
						{{if $config.installation_type==4}}
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.albums==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_albums" value="1" {{if $smarty.post.migrate_albums==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_albums}}</label></span></td>
							</tr>
						{{/if}}
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.comments==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_comments" value="1" {{if $smarty.post.migrate_comments==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_comments}}</label></span></td>
						</tr>
						{{if $config.installation_type>=2}}
							<tr>
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" class="old_script_no {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.users==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_users" value="1" {{if $smarty.post.migrate_users==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_users}}</label></span></td>
							</tr>
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" class="migrate_users_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.favourites==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_favourites" value="1" {{if $smarty.post.migrate_favourites==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_favourites}}</label></span></td>
							</tr>
							{{if $config.installation_type==4}}
								<tr>
									<td><span class="de_lv_pair"><input type="checkbox" class="migrate_users_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.friends==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_friends" value="1" {{if $smarty.post.migrate_friends==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_friends}}</label></span></td>
								</tr>
								<tr>
									<td><span class="de_lv_pair"><input type="checkbox" class="migrate_users_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.messages==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_messages" value="1" {{if $smarty.post.migrate_messages==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_messages}}</label></span></td>
								</tr>
								<tr>
									<td><span class="de_lv_pair"><input type="checkbox" class="migrate_users_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.subscriptions==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_subscriptions" value="1" {{if $smarty.post.migrate_subscriptions==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_subscriptions}}</label></span></td>
								</tr>
								<tr>
									<td><span class="de_lv_pair"><input type="checkbox" class="migrate_users_on {{foreach from=$smarty.post.migrators_supported_data key="key" item="item"}}{{if $item.playlists==1}}old_script_{{$key}} {{/if}}{{/foreach}}" name="migrate_playlists" value="1" {{if $smarty.post.migrate_playlists==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_migrate_data_playlists}}</label></span></td>
								</tr>
							{{/if}}
						{{/if}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.migrator.field_override_objects}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="override_objects" value="1" {{if $smarty.post.override_objects==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_override_objects_yes}}</label></span>
					<span class="de_hint">{{$lang.plugins.migrator.field_override_objects_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.migrator.field_upload_hotlinked_videos}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="upload_hotlinked_videos" class="migrate_videos_on" value="1" {{if $smarty.post.upload_hotlinked_videos==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_upload_hotlinked_videos_yes}}</label></span>
					<span class="de_hint">{{$lang.plugins.migrator.field_upload_hotlinked_videos_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.migrator.field_test_mode}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="test_mode" value="1" {{if $smarty.post.test_mode==1}}checked{{/if}}/><label>{{$lang.plugins.migrator.field_test_mode_enabled}}</label></span>
					<input type="text" name="test_mode_limit" class="test_mode_on" size="10" maxlength="10" value="{{$smarty.post.test_mode_limit}}"/>
					<span class="de_hint">{{$lang.plugins.migrator.field_test_mode_hint}}</span>
				</td>
			</tr>
			<tr class="old_script_no {{foreach from=$smarty.post.migrators_options key="key" item="item"}}{{if count($item)>0}}old_script_{{$key}} {{/if}}{{/foreach}}">
				<td class="de_label">{{$lang.plugins.migrator.field_options}}</td>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td class="nowrap">{{$lang.plugins.migrator.field_options_name}}</td>
							<td>{{$lang.plugins.migrator.field_options_value}}</td>
						</tr>
						{{foreach from=$smarty.post.migrators_options key="key" item="item"}}
							{{foreach from=$item key="option" item="option_value"}}
								<tr class="eg_data old_script_{{$key}}">
									<td class="nowrap">{{$option}}</td>
									<td class="nowrap"><input type="text" name="{{$option}}" value="{{$option_value}}" maxlength="500"/></td>
								</tr>
							{{/foreach}}
						{{/foreach}}
					</table>
				</td>
			</tr>
			{{if $smarty.get.action=='display_result'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.migrator.divider_summary}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.migrator.field_summary_duration}}</td>
					<td class="de_control">
						{{$lang.plugins.migrator.field_summary_duration_value|replace:"%1%":$smarty.post.summary.duration}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.migrator.field_summary_memory}}</td>
					<td class="de_control">
						{{assign var="memory_usage" value=$smarty.post.summary.memory_usage}}
						{{if $memory_usage<1024}}
							{{$lang.plugins.migrator.field_summary_memory_bytes|replace:"%1%":$memory_usage}}
						{{else}}
							{{assign var="memory_usage" value=$memory_usage/1024|intval}}
							{{if $memory_usage<1024}}
								{{$lang.plugins.migrator.field_summary_memory_kilobytes|replace:"%1%":$memory_usage}}
							{{else}}
								{{assign var="memory_usage" value=$memory_usage/1024|intval}}
								{{$lang.plugins.migrator.field_summary_memory_megabytes|replace:"%1%":$memory_usage}}
							{{/if}}
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.migrator.field_summary_log}}</td>
					<td class="de_control">
						<a href="?plugin_id=migrator&amp;action=log&amp;task_id={{$smarty.post.log_file}}" rel="log">{{$smarty.post.log_file_size}}</a>
					</td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.plugins.migrator.dg_summary_col_objects}}</td>
								<td>{{$lang.plugins.migrator.dg_summary_col_total}}</td>
								<td>{{$lang.plugins.migrator.dg_summary_col_inserted}}</td>
								<td>{{$lang.plugins.migrator.dg_summary_col_updated}}</td>
								<td>{{$lang.plugins.migrator.dg_summary_col_errors}}</td>
							</tr>
							{{foreach key="key" item="item" from=$smarty.post.summary.migration|smarty:nodefaults}}
								<tr class="eg_data">
									<td>{{$lang.plugins.migrator.dg_summary_col_objects_values[$key]}}</td>
									<td>{{$item.total}}</td>
									<td>{{$item.inserted}}</td>
									<td>{{$item.updated}}</td>
									<td>{{$item.errors}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.migrator.divider_recent_migrations}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					{{if count($smarty.post.recent_migrations)>0}}
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.plugins.migrator.dg_recent_migrations_col_time}}</td>
								<td>{{$lang.plugins.migrator.dg_recent_migrations_col_results}}</td>
								<td>{{$lang.plugins.migrator.dg_recent_migrations_col_log}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.recent_migrations|smarty:nodefaults}}
								<tr class="eg_data">
									<td>
										{{$item.date|date_format:$smarty.session.userdata.full_date_format}}
									</td>
									<td>
										{{if $item.process>0}}
											{{$lang.plugins.migrator.dg_recent_migrations_col_results_in_process|replace:"%1%":$item.process}}
										{{else}}
											<a href="?plugin_id=migrator&amp;action=display_result&amp;task_id={{$item.key}}">
												{{$lang.plugins.migrator.dg_recent_migrations_col_results_value|replace:"%1%":$item.inserted_count|replace:"%2%":$item.updated_count|replace:"%3%":$item.errors_count}}
											</a>
										{{/if}}
									</td>
									<td>
										{{if $item.has_log}}
											<a href="?plugin_id=migrator&amp;action=log&amp;task_id={{$item.key}}" rel="log">task-log-{{$item.key}}.dat</a>
										{{/if}}
									</td>
								</tr>
							{{/foreach}}
						</table>
					{{else}}
						{{$lang.plugins.migrator.divider_recent_migrations_none}}
					{{/if}}
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="start_migration"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.migrator.btn_start}}"/>
	</div>
</form>