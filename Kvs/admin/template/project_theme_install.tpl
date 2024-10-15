{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_install">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.website_ui.theme_install_title}} - {{$lang.common.wizard_step|replace:"%1%":$data.step}}</h1></div>
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
			{{if $data.step==1}}
				<tr>
					<td class="de_label de_required">{{$lang.website_ui.theme_install_field_theme_archive}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.website_ui.theme_install_field_theme_archive}}</span>
								<span class="js_param">accept=zip,gz</span>
							</div>
							<input type="text" name="theme_archive" maxlength="100"/>
							<input type="hidden" name="theme_archive_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.website_ui.theme_install_field_theme_archive_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.website_ui.theme_install_field_confirmation}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="confirm_continue" value="1"/><label>{{$lang.website_ui.theme_install_field_confirmation_confirm}}</label></span>
						<span class="de_hint">{{$lang.website_ui.theme_install_field_confirmation_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label {{if is_array($data.dirs_need_permissions)}}de_required{{/if}}">{{$lang.website_ui.theme_install_field_permissions}}</td>
					<td class="de_control">
						{{if is_array($data.dirs_need_permissions)}}
							<table class="control_group">
								<tr>
									<td>
										{{$lang.website_ui.theme_install_field_permissions_value}}
									</td>
								</tr>
								{{foreach from=$data.dirs_need_permissions|smarty:nodefaults item="path"}}
									<tr><td class="de_dependent">{{$path}}</td></tr>
								{{/foreach}}
							</table>
						{{else}}
							<span>{{$lang.website_ui.theme_install_field_permissions_all_set}}</span>
						{{/if}}
					</td>
				</tr>
			{{elseif $data.step==2}}
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_name}}</td>
					<td class="de_control">
						<span>{{$data.theme.name}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_version}}</td>
					<td class="de_control">
						<span>{{$data.theme.version}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_developer}}</td>
					<td class="de_control">
						<span>{{$data.theme.developer}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_install_divider_dependencies}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.website_ui.theme_install_divider_dependencies_hint}}</span>
					</td>
				</tr>
				{{foreach from=$data.theme.missing_references|smarty:nodefaults item="dependency"}}
					<tr>
						<td class="de_label {{if $dependency.required==1}}de_required{{/if}}">
							{{if $dependency.summary_url && ($dependency.summary_ur_permission=='' || in_array($dependency.summary_ur_permission, $smarty.session.permissions))}}
								<a href="{{$dependency.summary_url}}">{{$dependency.title}}</a>
							{{else}}
								{{$dependency.title}}
							{{/if}}
						</td>
						<td class="de_control">
							{{if is_array($dependency.value)}}
								{{foreach from=$dependency.value|smarty:nodefaults item="dependency_value"}}
									<select name="{{$dependency.id}}[{{$dependency_value}}]">
										<option value="">{{$lang.website_ui.theme_install_field_dependency_switch_off}}</option>
										<option value="{{$dependency_value}}" selected>{{$lang.website_ui.theme_install_field_dependency_create|replace:"%1%":$dependency_value}}</option>
										{{foreach from=$dependency.existing|smarty:nodefaults item="existing_value"}}
											<option value="{{$existing_value.id}}">{{$lang.website_ui.theme_install_field_dependency_change|replace:"%1%":$existing_value.title}}</option>
										{{/foreach}}
									</select>
								{{/foreach}}
							{{else}}
								<select name="{{$dependency.id}}">
									{{if $dependency.required==0}}
										<option value="">{{$lang.website_ui.theme_install_field_dependency_switch_off}}</option>
									{{/if}}
									<option value="{{$dependency.value}}" selected>
										{{if $dependency.background_task.task_id>0}}
											{{$lang.website_ui.theme_install_field_dependency_wait|replace:"%1%":$dependency.value|replace:"%2%":$dependency.background_task.pc_complete}}
										{{else}}
											{{if $dependency.affected_objects_number>0}}
												{{$lang.website_ui.theme_install_field_dependency_create_big|replace:"%1%":$dependency.value|replace:"%2%":$dependency.affected_objects_text}}
											{{else}}
												{{$lang.website_ui.theme_install_field_dependency_create|replace:"%1%":$dependency.value}}
											{{/if}}
										{{/if}}
									</option>
									{{foreach from=$dependency.existing|smarty:nodefaults item="existing_value"}}
										<option value="{{$existing_value.id}}">{{$lang.website_ui.theme_install_field_dependency_change|replace:"%1%":$existing_value.title}}</option>
									{{/foreach}}
								</select>
							{{/if}}
							{{if $dependency.hint!=''}}
								<span class="de_hint">{{$dependency.hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{foreachelse}}
					<tr>
						<td class="de_control" colspan="2">
							{{$lang.website_ui.theme_install_divider_dependencies_none}}
						</td>
					</tr>
				{{/foreach}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_install_divider_options}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.website_ui.theme_install_divider_options_hint}}</span>
					</td>
				</tr>
				{{foreach from=$data.theme.mismatching_options|smarty:nodefaults item="option"}}
					<tr>
						<td class="de_label">
							{{if in_array('system|system_settings',$smarty.session.permissions)}}
								<a href="options.php?page=general_settings">{{$option.title}}</a>
							{{else}}
								{{$option.title}}
							{{/if}}
						</td>
						<td class="de_control">
							<span class="de_lv_pair"><input type="checkbox" name="{{$option.id}}" value="change" {{if $option.choice=='change'}}checked{{/if}}/><label>{{$option.current_value}} <span class="icon icon-action-forward"></span> {{$option.required_value}}</label></span>
						</td>
					</tr>
				{{foreachelse}}
					<tr>
						<td class="de_control" colspan="2">
							{{$lang.website_ui.theme_install_divider_options_none}}
						</td>
					</tr>
				{{/foreach}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_install_divider_scripts}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.website_ui.theme_install_divider_scripts_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="2">
						<span>
							{{if $data.theme.has_customization_scripts == 1}}
								{{if $data.theme.customization_scripts_missing_permission == 1}}
									{{$lang.website_ui.theme_install_divider_scripts_permission}}
								{{else}}
									{{$lang.website_ui.theme_install_divider_scripts_copy}}
								{{/if}}
							{{else}}
								{{$lang.website_ui.theme_install_divider_scripts_none}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{elseif $data.step==3}}
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_name}}</td>
					<td class="de_control">
						<span>{{$data.theme.name}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_version}}</td>
					<td class="de_control">
						<span>{{$data.theme.version}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_install_field_theme_developer}}</td>
					<td class="de_control">
						<span>{{$data.theme.developer}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_install_divider_obsolete}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.website_ui.theme_install_divider_obsolete_hint}}</span>
					</td>
				</tr>
				{{if count($data.theme.unused_formats.unused_formats_screenshots_overview)+count($data.theme.unused_formats.unused_formats_albums_main)+count($data.theme.unused_formats.unused_formats_albums_preview)==0}}
					<tr>
						<td class="de_control" colspan="2">
							{{$lang.website_ui.theme_install_divider_obsolete_none}}
						</td>
					</tr>
				{{else}}
					{{if count($data.theme.unused_formats.unused_formats_screenshots_overview)>0}}
						<tr>
							<td class="de_label">{{$lang.website_ui.theme_install_field_unused_formats_screenshots_overview}}</td>
							<td class="de_control">
								{{foreach from=$data.theme.unused_formats.unused_formats_screenshots_overview|smarty:nodefaults key="key" item="item"}}
									<span class="de_lv_pair"><input type="checkbox" name="delete_formats_screenshots_overview[]" value="{{$key}}"/><label>{{$item}}</label></span>
								{{/foreach}}
							</td>
						</tr>
					{{/if}}
					{{if count($data.theme.unused_formats.unused_formats_albums_main)>0}}
						<tr>
							<td class="de_label">{{$lang.website_ui.theme_install_field_unused_formats_albums_main}}</td>
							<td class="de_control">
								{{foreach from=$data.theme.unused_formats.unused_formats_albums_main|smarty:nodefaults key="key" item="item"}}
									<span class="de_lv_pair"><input type="checkbox" name="delete_formats_albums_main[]" value="{{$key}}"/><label>{{$item}}</label></span>
								{{/foreach}}
							</td>
						</tr>
					{{/if}}
					{{if count($data.theme.unused_formats.unused_formats_albums_preview)>0}}
						<tr>
							<td class="de_label">{{$lang.website_ui.theme_install_field_unused_formats_albums_preview}}</td>
							<td class="de_control">
								{{foreach from=$data.theme.unused_formats.unused_formats_albums_preview|smarty:nodefaults key="key" item="item"}}
									<span class="de_lv_pair"><input type="checkbox" name="delete_formats_albums_preview[]" value="{{$key}}"/><label>{{$item}}</label></span>
								{{/foreach}}
							</td>
						</tr>
					{{/if}}
				{{/if}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		{{if $data.step==2}}
			<input type="submit" name="back" value="{{$lang.common.btn_back}}"/>
		{{/if}}
		{{if $data.step<3}}
			<input type="submit" name="save_default" value="{{$lang.common.btn_next}}"/>
		{{/if}}
		{{if $data.step==3}}
			<input type="submit" name="save_default" value="{{$lang.common.btn_finish}}"/>
		{{/if}}
		{{if $data.step==2}}
			<span class="de_separated_group">
				<input type="submit" name="cancel" class="destructive" value="{{$lang.common.btn_cancel}}" data-confirm="{{$lang.common.btn_cancel_confirm}}"/>
			</span>
		{{/if}}
	</div>
</form>