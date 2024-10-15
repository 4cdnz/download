<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.awe_black_label.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.awe_black_label.long_desc}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.awe_black_label.divider_configuration}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_white_label_url}}</td>
				<td class="de_control">
					<input type="text" name="white_label_url" maxlength="400" value="{{$smarty.post.white_label_url}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_white_label_url_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_app_secret}}</td>
				<td class="de_control">
					<input type="text" name="app_secret" maxlength="400" value="{{$smarty.post.app_secret}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_app_secret_hint}}</span>
				</td>
			</tr>
			{{if count($smarty.post.languages)>0}}
				<tr>
					<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_language}}</td>
					<td class="de_control">
						<select name="language_code">
							<option value="auto" {{if $smarty.post.language_code=='auto'}}selected{{/if}}>{{$lang.plugins.awe_black_label.field_language_auto}}</option>
							{{foreach from=$smarty.post.languages|smarty:nodefaults item="language"}}
								<option value="{{$language.code}}" {{if $smarty.post.language_code==$language.code}}selected{{/if}}>{{$language.enName}}</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.plugins.awe_black_label.field_language_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_members_status_update}}</td>
				<td class="de_control">
					<input type="text" name="member_status_refresh_interval" maxlength="10" size="10" value="{{$smarty.post.member_status_refresh_interval|default:"60"}}"/> {{$lang.plugins.awe_black_label.field_members_status_update_min}}
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_members_status_update_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_enable_debug}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.awe_black_label.field_enable_debug_enabled}}</label></span>
					<span>(<a href="{{$page_name}}?plugin_id=awe_black_label&amp;action=get_debug_log" rel="log">{{$lang.plugins.awe_black_label.field_enable_debug_log}}</a>)</span>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_enable_debug_hint}}</span>
				</td>
			</tr>
			<tr class="is_debug_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.awe_black_label.field_debug_ips}}</td>
				<td class="de_control">
					<input type="text" name="debug_ips" size="10" value="{{$smarty.post.debug_ips}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_debug_ips_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_callback_url}}</td>
				<td class="de_control">
					<input type="text" value="{{$config.project_url}}/livecams/?status_refresh=1" readonly/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_callback_url_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.awe_black_label.divider_display}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_niche}}</td>
				<td class="de_control">
					<select name="niche">
						<option value="">{{$lang.plugins.awe_black_label.field_niche_all}}</option>
						<option value="girls" {{if $smarty.post.niche=='girls'}}selected{{/if}}>{{$lang.plugins.awe_black_label.field_niche_girls}}</option>
						<option value="boys" {{if $smarty.post.niche=='boys'}}selected{{/if}}>{{$lang.plugins.awe_black_label.field_niche_boys}}</option>
						<option value="tranny" {{if $smarty.post.niche=='tranny'}}selected{{/if}}>{{$lang.plugins.awe_black_label.field_niche_tranny}}</option>
						<option value="celebrity" {{if $smarty.post.niche=='celebrity'}}selected{{/if}}>{{$lang.plugins.awe_black_label.field_niche_celebrity}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_niche_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_primary_button_bg}}</td>
				<td class="de_control">
					<input type="text" name="primary_button_bg" size="10" value="{{$smarty.post.primary_button_bg}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_primary_button_bg_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_primary_button_color}}</td>
				<td class="de_control">
					<input type="text" name="primary_button_color" size="10" value="{{$smarty.post.primary_button_color}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_primary_button_color_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_terms_link_color}}</td>
				<td class="de_control">
					<input type="text" name="terms_link_color" size="10" value="{{$smarty.post.terms_link_color}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_terms_link_color_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.awe_black_label.field_terms_toggle_color}}</td>
				<td class="de_control">
					<input type="text" name="terms_toggle_color" size="10" value="{{$smarty.post.terms_toggle_color}}"/>
					<span class="de_hint">{{$lang.plugins.awe_black_label.field_terms_toggle_color_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.awe_black_label.btn_save}}"/>
	</div>
</form>