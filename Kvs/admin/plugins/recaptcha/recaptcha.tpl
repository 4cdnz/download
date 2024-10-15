{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.recaptcha.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.recaptcha.long_desc}}
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
				<td class="de_label">{{$lang.plugins.recaptcha.field_enable}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="is_enabled">
						<option value="0">{{$lang.plugins.recaptcha.field_enable_kvs}}</option>
						<option value="1" {{if $smarty.post.is_enabled==1}}selected{{/if}}>{{$lang.plugins.recaptcha.field_enable_recaptcha}}</option>
						<option value="2" {{if $smarty.post.is_enabled==2}}selected{{/if}}>{{$lang.plugins.recaptcha.field_enable_turnstile}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.recaptcha.field_enable_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_1 is_enabled_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.recaptcha.field_site_key}}</td>
				<td class="de_control">
					<input type="text" name="site_key" maxlength="400" value="{{$smarty.post.site_key}}"/>
					<span class="de_hint">{{$lang.plugins.recaptcha.field_site_key_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_1 is_enabled_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.recaptcha.field_secret_key}}</td>
				<td class="de_control">
					<input type="text" name="secret_key" maxlength="400" value="{{$smarty.post.secret_key}}"/>
					<span class="de_hint">{{$lang.plugins.recaptcha.field_secret_key_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_1 is_enabled_2">
				<td class="de_label de_dependent">{{$lang.plugins.recaptcha.field_aliases}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_aliases" value="1" {{if $smarty.post.is_aliases==1}}checked{{/if}}/><label>{{$lang.plugins.recaptcha.field_aliases_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.recaptcha.field_aliases_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_1 is_enabled_2 is_aliases_on">
				<td></td>
				<td class="de_control">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.plugins.recaptcha.field_alias_number}}</td>
							<td class="eg_selector"><input type="checkbox"/><label>{{$lang.plugins.recaptcha.field_alias_delete}}</label></td>
							<td>{{$lang.plugins.recaptcha.field_alias_domain}}</td>
							<td>{{$lang.plugins.recaptcha.field_site_key}}</td>
							<td>{{$lang.plugins.recaptcha.field_secret_key}}</td>
						</tr>
						{{foreach from=$smarty.post.aliases item="alias" name="aliases"}}
							<tr class="eg_data" data-endless-list-item="aliases[domain][]">
								<td data-endless-list-text="${index}">{{$smarty.foreach.aliases.iteration}}</td>
								<td class="eg_selector">{{if $alias.domain}}<input type="checkbox" name="aliases[delete][]" value="1"/>{{/if}}</td>
								<td><input type="text" name="aliases[domain][]" maxlength="400" value="{{$alias.domain}}"/></td>
								<td><input type="text" name="aliases[site_key][]" maxlength="400" value="{{$alias.site_key}}"/></td>
								<td><input type="text" name="aliases[secret_key][]" maxlength="400" value="{{$alias.secret_key}}"/></td>
							</tr>
						{{foreachelse}}
							<tr class="eg_data" data-endless-list-item="aliases[domain][]">
								<td data-endless-list-text="${index}">1</td>
								<td class="eg_selector"></td>
								<td><input type="text" name="aliases[domain][]" maxlength="400" value=""/></td>
								<td><input type="text" name="aliases[site_key][]" maxlength="400" value=""/></td>
								<td><input type="text" name="aliases[secret_key][]" maxlength="400" value=""/></td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.recaptcha.btn_save}}"/>
	</div>
</form>