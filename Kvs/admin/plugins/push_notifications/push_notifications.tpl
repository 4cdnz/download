{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.push_notifications.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.push_notifications.long_desc}}
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
				<td class="de_label">{{$lang.plugins.push_notifications.field_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_enabled" value="1" {{if $smarty.post.is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.push_notifications.field_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_enable_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.is_library_missing==1}}
				<tr class="is_enabled_on">
					<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_js_library}}</td>
					<td class="de_control">
						<a href="?plugin_id=push_notifications&amp;action=library" rel="file">{{$lang.plugins.push_notifications.field_js_library_download}}</a>
						<span class="de_hint">{{$lang.plugins.push_notifications.field_js_library_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="is_enabled_on">
				<td class="de_label de_dependent is_enabled_on">{{$lang.plugins.push_notifications.field_refid}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="refid" size="20" value="{{$smarty.post.refid}}"/>
					</span>
					<span>
						<a href="https://publisher.ad-maven.com/#/register?source_id=kvs">{{$lang.plugins.push_notifications.field_refid_sign_up}}</a>
					</span>
					<span class="de_hint">
						{{if $smarty.post.is_https==1}}
							{{$lang.plugins.push_notifications.field_refid_hint_https}}
						{{else}}
							{{$lang.plugins.push_notifications.field_refid_hint_http}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_repeat}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="repeat">
						<option value="always" {{if $smarty.post.repeat=='always'}}selected{{/if}}>{{$lang.plugins.push_notifications.field_repeat_always}}</option>
						<option value="interval" {{if $smarty.post.repeat=='interval'}}selected{{/if}}>{{$lang.plugins.push_notifications.field_repeat_interval}}</option>
						<option value="once" {{if $smarty.post.repeat=='once'}}selected{{/if}}>{{$lang.plugins.push_notifications.field_repeat_once}}</option>
					</select>
					<span class="repeat_interval">
						<input type="text" name="repeat_interval" size="4" maxlength="10" value="{{$smarty.post.repeat_interval}}"/>
						{{$lang.plugins.push_notifications.field_repeat_interval_minutes}}
					</span>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_repeat_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_first_click}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="skip_first_click" value="1" {{if $smarty.post.skip_first_click==1}}checked{{/if}}/><label>{{$lang.plugins.push_notifications.field_first_click_skip}}</label></span>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_first_click_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_exclude_referers}}</td>
				<td class="de_control">
					<textarea name="exclude_referers" rows="4" cols="20">{{$smarty.post.exclude_referers}}</textarea>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_exclude_referers_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_include_referers}}</td>
				<td class="de_control">
					<textarea name="include_referers" rows="4" cols="20">{{$smarty.post.include_referers}}</textarea>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_include_referers_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_exclude_members}}</td>
				<td class="de_control">
					<select name="exclude_members">
						<option value="" {{if $smarty.post.exclude_members==''}}selected{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_none}}</option>
						<option value="all" {{if $smarty.post.exclude_members=='all'}}selected{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_all}}</option>
						<option value="premium" {{if $smarty.post.exclude_members=='premium'}}selected{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_premium}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.push_notifications.field_exclude_members_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.push_notifications.btn_save}}"/>
	</div>
</form>