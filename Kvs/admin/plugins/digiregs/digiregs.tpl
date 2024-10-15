{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.digiregs.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.digiregs.long_desc}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.digiregs.divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.digiregs.field_api_key}}</td>
				<td class="de_control">
					<input type="text" name="api_key" value="{{$smarty.post.api_key}}"/>
					<span class="de_hint">{{$lang.plugins.digiregs.field_api_key_hint|smarty:nodefaults}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.digiregs.field_balance}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.balance_submissions>0}}
							{{$lang.plugins.digiregs.field_balance_value|replace:"%1%":$smarty.post.balance_submissions}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.digiregs.field_on_empty_balance}}</td>
				<td class="de_control">
					<select name="on_empty_balance">
						<option value="0" {{if $smarty.post.on_empty_balance==0}}selected{{/if}}>{{$lang.plugins.digiregs.field_on_empty_balance_wait}}</option>
						<option value="1" {{if $smarty.post.on_empty_balance==1}}selected{{/if}}>{{$lang.plugins.digiregs.field_on_empty_balance_ignore}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.digiregs.field_on_empty_balance_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.digiregs.field_enable_debug}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_enable_debug_enabled}}</label></span>
					{{if $smarty.post.is_debug_enabled==1}}
						<span>(<a href="{{$page_name}}?plugin_id=digiregs&amp;action=get_debug_log" rel="log">{{$lang.plugins.digiregs.field_enable_debug_log}}</a>)</span>
					{{/if}}
					<span class="de_hint">{{$lang.plugins.digiregs.field_enable_debug_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.digiregs.divider_copyright}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.digiregs.divider_copyright_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.digiregs.field_copyright_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_is_enabled" value="1" {{if $smarty.post.copyright_is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_enable_hint}}</span>
				</td>
			</tr>
			<tr class="copyright_is_enabled_on">
				<td class="de_label de_required">{{$lang.plugins.digiregs.field_apply_to}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="values" value="admins,site,import,feeds,grabbers,ftp,manual"}}
						{{foreach from=","|explode:$values item="item"}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="copyright_apply_to_{{$item}}" name="copyright_apply_to[]" value="{{$item}}" {{if in_array($item, $smarty.post.copyright_apply_to)}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_apply_to_list.$item}}</label></span>
									{{assign var="item_hint" value="`$item`_hint"}}
									{{if $lang.plugins.digiregs.field_apply_to_list.$item_hint}}
										<span class="de_hint">{{$lang.plugins.digiregs.field_apply_to_list.$item_hint}}</span>
									{{/if}}
								</td>
							</tr>
							{{if $item=='feeds'}}
								<tr class="copyright_apply_to_{{$item}}_on">
									<td class="de_dependent de_vis_sw_select">
										<select name="copyright_apply_to_feeds_type">
											<option value="0" {{if count($smarty.post.copyright_apply_to_feeds)==0}}selected{{/if}}>{{$lang.plugins.digiregs.field_apply_to_feeds_all}}</option>
											<option value="1" {{if count($smarty.post.copyright_apply_to_feeds)>0}}selected{{/if}}>{{$lang.plugins.digiregs.field_apply_to_feeds_selected}}</option>
										</select>
									</td>
								</tr>
								{{foreach from=$smarty.post.feeds|smarty:nodefaults item="feed"}}
									<tr class="copyright_apply_to_{{$item}}_on copyright_apply_to_feeds_type_1">
										<td class="de_dependent">
											<span class="de_lv_pair"><input type="checkbox" name="copyright_apply_to_feeds[]" value="{{$feed.feed_id}}" {{if in_array($feed.feed_id, $smarty.post.copyright_apply_to_feeds)}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_apply_to_feeds_feed|replace:"%1%":$feed.title}}</label></span>
										</td>
									</tr>
								{{/foreach}}
							{{/if}}
						{{/foreach}}
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_apply_only_with_empty_content_source" value="1" {{if $smarty.post.copyright_apply_only_with_empty_content_source==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_apply_only_with_empty_content_source}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_apply_only_with_empty_content_source_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="copyright_is_enabled_on">
				<td class="de_label">{{$lang.plugins.digiregs.field_copyright_known_action}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="copyright_known_action">
									<option value="0" {{if $smarty.post.copyright_known_action==0}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_known_action_delete}}</option>
									<option value="1" {{if $smarty.post.copyright_known_action==1}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_known_action_allow_only_with_duration_limit}}</option>
									<option value="2" {{if $smarty.post.copyright_known_action==2}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_known_action_allow_only_from_known_sources}}</option>
									<option value="3" {{if $smarty.post.copyright_known_action==3}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_known_action_allow_all}}</option>
								</select>
								<span class="de_hint copyright_known_action_0">{{$lang.plugins.digiregs.field_copyright_known_action_delete_hint}}</span>
								<span class="de_hint copyright_known_action_1">{{$lang.plugins.digiregs.field_copyright_known_action_allow_only_with_duration_limit_hint}}</span>
								<span class="de_hint copyright_known_action_2">{{$lang.plugins.digiregs.field_copyright_known_action_allow_only_from_known_sources_hint}}</span>
								<span class="de_hint copyright_known_action_3">{{$lang.plugins.digiregs.field_copyright_known_action_allow_all_hint}}</span>
							</td>
						</tr>
						<tr class="copyright_known_action_1 copyright_known_action_2 copyright_known_action_3">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="copyright_known_deactivate" value="1" {{if $smarty.post.copyright_known_deactivate==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_deactivate}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_known_deactivate_hint}}</span>
							</td>
						</tr>
						{{if count($smarty.post.admin_flags)>0}}
							<tr class="copyright_known_action_1 copyright_known_action_2 copyright_known_action_3">
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_known_set_admin_flag_type" value="1" {{if $smarty.post.copyright_known_set_admin_flag>0}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_set_admin_flag}}</label></span>
									<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_known_set_admin_flag_hint}}</span>
								</td>
							</tr>
							<tr class="copyright_known_action_1 copyright_known_action_2 copyright_known_action_3 copyright_known_set_admin_flag_type_on">
								<td class="de_dependent">
									<select name="copyright_known_set_admin_flag">
										{{foreach from=$smarty.post.admin_flags|smarty:nodefaults item="admin_flag"}}
											<option value="{{$admin_flag.flag_id}}" {{if $admin_flag.flag_id==$smarty.post.copyright_known_set_admin_flag}}selected{{/if}}>{{$admin_flag.title}}</option>
										{{/foreach}}
									</select>
								</td>
							</tr>
						{{/if}}
						<tr class="copyright_known_action_2 copyright_known_action_3">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="copyright_known_truncate_duration" value="1" {{if $smarty.post.copyright_known_truncate_duration==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_truncate_duration}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_known_truncate_duration_hint}}</span>
							</td>
						</tr>
						<tr class="copyright_known_action_1 copyright_known_action_3">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_known_create_content_sources" value="1" {{if $smarty.post.copyright_known_create_content_sources==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_create_content_sources}}</label></span>
								<span class="de_lv_pair copyright_known_create_content_sources_on"><input type="checkbox" name="copyright_known_create_content_sources_disabled" value="1" {{if $smarty.post.copyright_known_create_content_sources_disabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_create_content_sources_disabled}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_known_create_content_sources_hint}}</span>
							</td>
						</tr>
						{{if $config.installation_type>=4}}
							<tr class="copyright_known_action_1 copyright_known_action_3">
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_known_create_dvds" value="1" {{if $smarty.post.copyright_known_create_dvds==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_create_dvds}}</label></span>
									<span class="de_lv_pair copyright_known_create_dvds_on"><input type="checkbox" name="copyright_known_create_dvds_disabled" value="1" {{if $smarty.post.copyright_known_create_dvds_disabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_known_create_dvds_disabled}}</label></span>
									<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_known_create_dvds_hint}}</span>
								</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
			<tr class="copyright_is_enabled_on">
				<td class="de_label">{{$lang.plugins.digiregs.field_copyright_unknown_action}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="copyright_unknown_action">
									<option value="0" {{if $smarty.post.copyright_unknown_action==0}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_unknown_action_delete}}</option>
									<option value="2" {{if $smarty.post.copyright_unknown_action==2}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_unknown_action_allow_only_from_known_sources}}</option>
									<option value="3" {{if $smarty.post.copyright_unknown_action==3}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_unknown_action_allow_all}}</option>
								</select>
								<span class="de_hint copyright_unknown_action_0">{{$lang.plugins.digiregs.field_copyright_unknown_action_delete_hint}}</span>
								<span class="de_hint copyright_unknown_action_2">{{$lang.plugins.digiregs.field_copyright_unknown_action_allow_only_from_known_sources_hint}}</span>
								<span class="de_hint copyright_unknown_action_3">{{$lang.plugins.digiregs.field_copyright_unknown_action_allow_all_hint}}</span>
							</td>
						</tr>
						<tr class="copyright_unknown_action_2 copyright_unknown_action_3">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="copyright_unknown_deactivate" value="1" {{if $smarty.post.copyright_unknown_deactivate==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_deactivate}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_unknown_deactivate_hint}}</span>
							</td>
						</tr>
						{{if count($smarty.post.admin_flags)>0}}
							<tr class="copyright_unknown_action_2 copyright_unknown_action_3">
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_unknown_set_admin_flag_type" value="1" {{if $smarty.post.copyright_unknown_set_admin_flag>0}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_set_admin_flag}}</label></span>
									<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_unknown_set_admin_flag_hint}}</span>
								</td>
							</tr>
							<tr class="copyright_unknown_action_2 copyright_unknown_action_3 copyright_unknown_set_admin_flag_type_on">
								<td class="de_dependent">
									<select name="copyright_unknown_set_admin_flag">
										{{foreach from=$smarty.post.admin_flags|smarty:nodefaults item="admin_flag"}}
											<option value="{{$admin_flag.flag_id}}" {{if $admin_flag.flag_id==$smarty.post.copyright_unknown_set_admin_flag}}selected{{/if}}>{{$admin_flag.title}}</option>
										{{/foreach}}
									</select>
								</td>
							</tr>
						{{/if}}
						<tr class="copyright_unknown_action_3">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_unknown_create_content_sources" value="1" {{if $smarty.post.copyright_unknown_create_content_sources==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_create_content_sources}}</label></span>
								<span class="de_lv_pair copyright_unknown_create_content_sources_on"><input type="checkbox" name="copyright_unknown_create_content_sources_disabled" value="1" {{if $smarty.post.copyright_unknown_create_content_sources_disabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_create_content_sources_disabled}}</label></span>
								<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_unknown_create_content_sources_hint}}</span>
							</td>
						</tr>
						{{if $config.installation_type>=4}}
							<tr class="copyright_unknown_action_3">
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="copyright_unknown_create_dvds" value="1" {{if $smarty.post.copyright_unknown_create_dvds==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_create_dvds}}</label></span>
									<span class="de_lv_pair copyright_unknown_create_dvds_on"><input type="checkbox" name="copyright_unknown_create_dvds_disabled" value="1" {{if $smarty.post.copyright_unknown_create_dvds_disabled==1}}checked{{/if}}/><label>{{$lang.plugins.digiregs.field_copyright_unknown_create_dvds_disabled}}</label></span>
									<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_unknown_create_dvds_hint}}</span>
								</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
			<tr class="copyright_is_enabled_on">
				<td class="de_label">{{$lang.plugins.digiregs.field_copyright_empty_action}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="copyright_is_delete_with_empty">
						<option value="1" {{if $smarty.post.copyright_is_delete_with_empty==1}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_empty_action_delete}}</option>
						<option value="0" {{if $smarty.post.copyright_is_delete_with_empty==0}}selected{{/if}}>{{$lang.plugins.digiregs.field_copyright_empty_action_allow_all}}</option>
					</select>
					<span class="de_hint copyright_is_delete_with_empty_1">{{$lang.plugins.digiregs.field_copyright_empty_action_delete_hint}}</span>
					<span class="de_hint copyright_is_delete_with_empty_0">{{$lang.plugins.digiregs.field_copyright_empty_action_allow_all_hint}}</span>
				</td>
			</tr>
			<tr class="copyright_is_enabled_on">
				<td class="de_label">{{$lang.plugins.digiregs.field_copyright_blacklisted_holders}}</td>
				<td class="de_control">
					<textarea name="copyright_blacklist" rows="3" cols="40">{{$smarty.post.copyright_blacklist}}</textarea>
					<span class="de_hint">{{$lang.plugins.digiregs.field_copyright_blacklisted_holders_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.digiregs.btn_save}}"/>
	</div>
</form>