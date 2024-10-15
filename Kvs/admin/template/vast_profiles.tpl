{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="player_advertising_profile_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_vast_profiles_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.vast_profile_add}}{{else}}{{$lang.settings.vast_profile_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/83-how-to-maximize-your-tube-revenue-with-kvs-advertising-system/">How to maximize your tube revenue with KVS advertising system</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.vast_profile_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.vast_profile_field_enable_debug}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.settings.vast_profile_field_enable_debug_enabled}}</label></span>
					{{if $smarty.post.is_debug_enabled==1}}
						<span>
							(<a href="{{$page_name}}?action=view_debug_log&amp;id={{$smarty.post.$table_key_name}}" rel="log">{{$lang.settings.vast_profile_field_enable_debug_log}}</a>)
						</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.settings.vast_profile_field_usages}}</td>
					<td class="de_control">
						<span>
							{{if count($smarty.post.usages)>0}}
								{{foreach name="data" item="item" from=$smarty.post.usages|smarty:nodefaults}}
									<a href="{{$item.url}}">{{if $item.is_embed==1}}{{$lang.settings.vast_profile_field_usages_embed}}{{else}}{{$lang.settings.vast_profile_field_usages_player}}{{/if}} - {{if $item.type=='pre'}}{{$lang.settings.vast_profile_field_usages_type_pre}}{{elseif $item.type=='post'}}{{$lang.settings.vast_profile_field_usages_type_post}}{{elseif $item.type=='video_click'}}{{$lang.settings.vast_profile_field_usages_type_video_click}}{{elseif $item.type=='popunder'}}{{$lang.settings.vast_profile_field_usages_type_popunder}}{{/if}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
								{{/foreach}}
							{{else}}
								{{$lang.settings.vast_profile_field_usages_none}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{section name="data" start=0 step=1 loop=$limit_providers}}
				{{assign var="index" value=$smarty.section.data.iteration-1}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.vast_profile_divider_advertiser|replace:"%1%":$smarty.section.data.iteration}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.vast_profile_field_enable}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_provider_{{$index}}" value="1" {{if $smarty.post.providers[$index].is_enabled==1 || ($smarty.get.action=='add_new' && $index==0)}}checked{{/if}}/><label>{{$lang.settings.vast_profile_field_enable_enabled}}</label></span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label de_required">{{$lang.settings.vast_profile_field_config_url}}</td>
					<td class="de_control">
						<input type="text" name="provider_{{$index}}_url" value="{{$smarty.post.providers[$index].url}}"/>
						<span class="de_hint">{{$lang.settings.vast_profile_field_config_url_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_config_alt_url}}</td>
					<td class="de_control">
						<textarea name="provider_{{$index}}_alt_url" rows="3" cols="40">{{$smarty.post.providers[$index].alt_url}}</textarea>
						<span class="de_hint">{{$lang.settings.vast_profile_field_config_alt_url_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label de_required">{{$lang.settings.vast_profile_field_devices}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_devices[]" value="pc" {{if in_array('pc', $smarty.post.providers[$index].devices) || count($smarty.post.providers[$index].devices)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_field_devices_pc}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_devices[]" value="phone" {{if in_array('phone', $smarty.post.providers[$index].devices) || count($smarty.post.providers[$index].devices)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_field_devices_phone}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_devices[]" value="tablet" {{if in_array('tablet', $smarty.post.providers[$index].devices) || count($smarty.post.providers[$index].devices)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_field_devices_tablet}}</label></span>
						<span class="de_hint">{{$lang.settings.vast_profile_field_devices_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label de_required">{{$lang.settings.vast_profile_browsers}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="chrome" {{if in_array('chrome', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_chrome}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="firefox" {{if in_array('firefox', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_firefox}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="safari" {{if in_array('safari', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_safari}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="msie" {{if in_array('msie', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_msie}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="opera" {{if in_array('opera', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_opera}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="yandex" {{if in_array('yandex', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_yandex}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="uc" {{if in_array('uc', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_uc}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="samsung" {{if in_array('samsung', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_samsung}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="bot" {{if in_array('bot', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_bot}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="provider_{{$index}}_browsers[]" value="other" {{if in_array('other', $smarty.post.providers[$index].browsers) || count($smarty.post.providers[$index].browsers)==0}}checked{{/if}}/><label>{{$lang.settings.vast_profile_browsers_other}}</label></span>
						<span class="de_hint">{{$lang.settings.vast_profile_browsers_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_include_categories}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.settings.vast_profile_field_include_categories}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=provider_{{$index}}_categories[]</span>
								<span class="js_param">empty_message={{$lang.settings.vast_profile_field_include_categories_empty}}</span>
								{{if in_array('categories|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.providers[$index].categories|smarty:nodefaults}}
								<input type="hidden" name="provider_{{$index}}_categories[]" value="{{$item}}" alt="{{$list_categories[$item]}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_category_{{$index}}" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.settings.vast_profile_field_include_categories_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.settings.vast_profile_field_include_categories_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_exclude_categories}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.settings.vast_profile_field_exclude_categories}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=provider_{{$index}}_exclude_categories[]</span>
								<span class="js_param">empty_message={{$lang.settings.vast_profile_field_exclude_categories_empty}}</span>
								{{if in_array('categories|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.providers[$index].exclude_categories|smarty:nodefaults}}
								<input type="hidden" name="provider_{{$index}}_exclude_categories[]" value="{{$item}}" alt="{{$list_categories[$item]}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_category_{{$index}}" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.settings.vast_profile_field_exclude_categories_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.settings.vast_profile_field_exclude_categories_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_include_countries}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.settings.vast_profile_field_include_countries}}</span>
								<span class="js_param">url=async/insight.php?type=countries</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=provider_{{$index}}_countries[]</span>
								<span class="js_param">allow_creation=true</span>
								<span class="js_param">empty_message={{$lang.settings.vast_profile_field_include_countries_empty}}</span>
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.providers[$index].countries|smarty:nodefaults}}
								<input type="hidden" name="provider_{{$index}}_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_country_{{$index}}" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.settings.vast_profile_field_include_countries_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.settings.vast_profile_field_include_countries_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_exclude_countries}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.settings.vast_profile_field_exclude_countries}}</span>
								<span class="js_param">url=async/insight.php?type=countries</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=provider_{{$index}}_exclude_countries[]</span>
								<span class="js_param">allow_creation=true</span>
								<span class="js_param">empty_message={{$lang.settings.vast_profile_field_exclude_countries_empty}}</span>
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.providers[$index].exclude_countries|smarty:nodefaults}}
								<input type="hidden" name="provider_{{$index}}_exclude_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_country_{{$index}}" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.settings.vast_profile_field_exclude_countries_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.settings.vast_profile_field_exclude_countries_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_include_referers}}</td>
					<td class="de_control">
						<textarea name="provider_{{$index}}_referers" rows="3" cols="40">{{$smarty.post.providers[$index].referers}}</textarea>
						<span class="de_hint">{{$lang.settings.vast_profile_field_include_referers_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_exclude_referers}}</td>
					<td class="de_control">
						<textarea name="provider_{{$index}}_exclude_referers" rows="3" cols="40">{{$smarty.post.providers[$index].exclude_referers}}</textarea>
						<span class="de_hint">{{$lang.settings.vast_profile_field_exclude_referers_hint}}</span>
					</td>
				</tr>
				<tr class="is_provider_{{$index}}_on">
					<td class="de_label">{{$lang.settings.vast_profile_field_weight}}</td>
					<td class="de_control">
						<input type="text" name="provider_{{$index}}_weight" maxlength="10" size="10" value="{{$smarty.post.providers[$index].weight|default:"0"}}"/>
						<span class="de_hint">{{$lang.settings.vast_profile_field_weight_hint}}</span>
					</td>
				</tr>
			{{/section}}
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
			<input type="hidden" name="item_id" value="{{$smarty.post.$table_key_name}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{else}}

{{assign var="can_delete" value=1}}
{{assign var="can_edit" value=1}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
			</div>
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
				<div class="drop">
					<i class="icon icon-action-list"></i><span>{{$lang.common.dg_list_view}}</span>
					<ul>
						<li><a href="{{$page_name}}?se_grid_preset=">{{$lang.common.dg_list_view_default}}</a></li>
						{{foreach from=$grid_presets item="preset"}}
							<li {{if $smarty.session.save.$page_name.grid_preset==$preset.title}}class="selected"{{/if}}><a href="{{$page_name}}?se_grid_preset={{$preset.title}}">{{$preset.title}}</a></li>
						{{/foreach}}
					</ul>
				</div>
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
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
					{{foreach name="data_profiles" item="item_profile" from=$data|smarty:nodefaults}}
						{{assign var="group_colspan" value=0}}
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.is_enabled==1}}
								{{assign var="group_colspan" value=$group_colspan+1}}
							{{/if}}
						{{/foreach}}
						<tr class="dg_group_header">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_profile.$table_key_name}}"/></td>
							<td colspan="{{$table_columns_visible-2}}">
								<a href="{{$page_name}}?action=change&amp;item_id={{$item_profile.$table_key_name}}" class="{{if $item_profile.has_errors==1}}highlighted_text{{elseif $item_profile.has_warnings==1 || $item_profile.is_debug_enabled==1}}warning_text{{/if}}">{{$item_profile.title}}</a>
								{{if $item_profile.is_debug_enabled==1}}
									<span class="warning_text">({{$lang.settings.vast_profile_warning_debug_enabled}})</span>
								{{/if}}
							</td>
							<td class="nowrap">
								{{if $item_profile.$table_key_name!=''}}
									<a {{if $item_profile.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item_profile.$table_key_name}}"{{/if}} class="edit {{if $item_profile.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item_profile.$table_key_name}}</span>
											<span class="js_param">name={{$item_profile.title}}</span>
											{{if $item_profile.is_debug_enabled==1}}
												<span class="js_param">enable_debug_hide=true</span>
											{{else}}
												<span class="js_param">disable_debug_hide=true</span>
											{{/if}}
											{{if count($item_profile.usages)>0}}
												<span class="js_param">delete_disable=true</span>
											{{/if}}
										</span>
									</a>
								{{/if}}
							</td>
						</tr>
						{{foreach name="data" item="item" from=$item_profile.providers|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" disabled/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td></td>
							</tr>
						{{/foreach}}
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">disable=${delete_disable}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				{{if $can_edit==1}}
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
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=?action=view_debug_log&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
					{{/if}}
					{{if $can_edit==1}}
						<option value="enable_debug">{{$lang.common.dg_batch_actions_enable_debug|replace:"%1%":'${count}'}}</option>
						<option value="disable_debug">{{$lang.common.dg_batch_actions_disable_debug|replace:"%1%":'${count}'}}</option>
					{{/if}}
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
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}