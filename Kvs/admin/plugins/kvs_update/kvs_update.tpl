{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.kvs_update.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.kvs_update.long_desc}}
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
			{{if $smarty.post.current_step=='pre'}}
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_update_version}}</td>
					<td class="de_control">
						{{$smarty.post.update_version}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_update_info}}</td>
					<td class="de_control">
						{{if $smarty.post.update_info!=''}}
							{{$smarty.post.update_info}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</td>
				</tr>
				{{if count($smarty.post.custom_changes)>0}}
					<tr>
						<td class="de_label">{{$lang.plugins.kvs_update.field_custom_changes}}</td>
						<td class="de_control">
							<table class="de_edit_grid">
								<colgroup>
									<col/>
								</colgroup>
								<tr class="eg_header">
									<td>{{$lang.plugins.kvs_update.field_custom_changes_notice}}</td>
								</tr>
								{{foreach item="item" from=$smarty.post.custom_changes|smarty:nodefaults}}
									<tr class="eg_data">
										<td>{{$item}}</td>
									</tr>
								{{/foreach}}
							</table>
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="de_control">
							<span class="de_lv_pair"><input type="checkbox" name="confirm_continue" value="1"/><label>{{$lang.plugins.kvs_update.field_custom_changes_confirm}}</label></span>
						</td>
					</tr>
				{{/if}}
			{{elseif $smarty.post.current_step>0}}
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_step}}</td>
					<td class="de_control">
						{{$lang.plugins.kvs_update.field_step_value|replace:"%1%":$smarty.post.current_step|replace:"%2%":$smarty.post.total_steps}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_description}}</td>
					<td class="de_control">
						{{$smarty.post.step_description}}
					</td>
				</tr>
				{{if $smarty.post.mysql_update_log!=''}}
					<tr>
						<td class="de_label">{{$lang.plugins.kvs_update.field_mysql_update_summary}}</td>
						<td class="de_control">
							{{$lang.plugins.kvs_update.field_mysql_update_summary_value|replace:"%1%":$smarty.post.mysql_update_success_count|replace:"%2%":$smarty.post.mysql_update_errors_count}}
						</td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.kvs_update.field_mysql_update_log}}</td>
						<td class="de_control">
							<div class="code_editor">
								<textarea name="mysql_log" rows="20" cols="40" readonly>{{$smarty.post.mysql_update_log}}</textarea>
							</div>
						</td>
					</tr>
				{{/if}}
			{{else}}
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_get_update}}</td>
					<td class="de_control">
						<span>
							<a href="https://www.kernel-video-sharing.com/{{$smarty.session.userdata.lang|substr:0:2}}/">https://www.kernel-video-sharing.com/{{$smarty.session.userdata.lang|substr:0:2}}/</a>
						</span>
						<span class="de_hint">{{$lang.plugins.kvs_update.field_get_update_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.kvs_update.field_update_archive}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.plugins.kvs_update.field_update_archive}}</span>
								<span class="js_param">accept=zip</span>
							</div>
							<input type="text" name="update_archive" maxlength="100"/>
							<input type="hidden" name="update_archive_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.plugins.kvs_update.field_update_archive_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.kvs_update.field_validation_hash}}</td>
					<td class="de_control">
						<input type="text" name="validation_hash" maxlength="32"/>
						<span class="de_hint">{{$lang.plugins.kvs_update.field_validation_hash_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.kvs_update.field_backup}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="backup_done" value="1"/><label>{{$lang.plugins.kvs_update.field_backup_text}}</label></span>
						<span class="de_hint">{{$lang.plugins.kvs_update.field_backup_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_update_logs}}</td>
					<td class="de_control">
						<span>
							<a href="?plugin_id=kvs_update&amp;action=kvs_update_log" rel="log">kvs_update.log</a>
						</span>
						<span>
							<a href="?plugin_id=kvs_update&amp;action=mysql_update_log" rel="log">mysql_update.log</a>
						</span>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		{{if $smarty.post.current_step=='pre'}}
			<input type="hidden" name="action" value="validate_pre"/>
			{{if count($smarty.post.custom_changes)>0}}
				<input type="hidden" name="has_custom_changed" value="1"/>
			{{/if}}
			<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_continue}}"/>
			<input type="submit" name="cancel" value="{{$lang.plugins.kvs_update.btn_cancel}}"/>
		{{elseif $smarty.post.current_step>0}}
			<input type="hidden" name="action" value="validate_step"/>
			<input type="hidden" name="step" value="{{$smarty.post.current_step}}"/>
			{{if $smarty.post.current_step<$smarty.post.total_steps}}
				<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_validate_and_next}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_finish}}"/>
			{{/if}}
		{{else}}
			<input type="hidden" name="action" value="upload"/>
			<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_start}}"/>
		{{/if}}
	</div>
</form>