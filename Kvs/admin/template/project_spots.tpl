{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new_spot' || $smarty.get.action=='change_spot'}}

{{if in_array('advertising|edit_all',$smarty.session.permissions) || (in_array('advertising|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_spot_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_advertisements_list}}</a> / {{if $smarty.get.action=='add_new_spot'}}{{$lang.website_ui.spot_add}}{{else}}{{$lang.website_ui.spot_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
				<td class="de_label de_required">{{$lang.website_ui.spot_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.spot_field_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}" {{if $smarty.get.action!='add_new_spot'}}disabled{{/if}}/>
					<span class="de_hint">{{$lang.website_ui.spot_field_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.spot_field_insert_code}}</td>
				<td class="de_control">
					{{if $smarty.get.action=='add_new_spot'}}
						<input type="text" data-autopopulate-from="external_id" data-autopopulate-pattern='{{$smarty.ldelim}}insert name="getAdv" place_id="${value}"{{$smarty.rdelim}}' readonly/>
					{{else}}
						<input type="text" value="{{$smarty.ldelim}}insert name=&quot;getAdv&quot; place_id=&quot;{{$smarty.post.external_id}}&quot;{{$smarty.rdelim}}" readonly/>
					{{/if}}
					<span class="de_hint">{{$lang.website_ui.spot_field_insert_code_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new_spot'}}
				<tr>
					<td class="de_label">{{$lang.website_ui.spot_field_usages}}</td>
					<td class="de_control">
						<span>
							{{if count($smarty.post.usages)>0}}
								{{foreach name="data" item="item" from=$smarty.post.usages|smarty:nodefaults}}
									{{if $item.is_player==1}}
										<a href="{{$item.url}}">{{if $item.is_embed==1}}{{$lang.website_ui.spot_field_usages_embed}}{{else}}{{$lang.website_ui.spot_field_usages_player}}{{/if}} - {{if $item.type=='start'}}{{$lang.website_ui.spot_field_usages_type_start}}{{elseif $item.type=='pre'}}{{$lang.website_ui.spot_field_usages_type_pre}}{{elseif $item.type=='post'}}{{$lang.website_ui.spot_field_usages_type_post}}{{elseif $item.type=='pause'}}{{$lang.website_ui.spot_field_usages_type_pause}}{{/if}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
									{{elseif $item.external_id!=''}}
										<a href="project_pages.php?action=change&amp;item_id={{$item.external_id}}">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
									{{elseif $item.block_uid!=''}}
										<a href="project_pages.php?action=change_block&amp;item_id={{$item.block_uid}}&amp;item_name={{$item.block_title}}">{{$item.title}} / {{$item.block_title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
									{{elseif $item.page_component_id!=''}}
										<a href="project_pages_components.php?action=change&amp;item_id={{$item.page_component_id}}">{{$item.page_component_id}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
									{{/if}}
								{{/foreach}}
							{{else}}
								{{$lang.website_ui.spot_field_usages_none}}
							{{/if}}
						</span>
					</td>
				</tr>
				{{if $smarty.post.version.change_id>0}}
					<tr>
						<td class="de_label">{{$lang.website_ui.spot_field_version}}</td>
						<td class="de_control">
							<span>
								{{if in_array('website_ui|view',$smarty.session.permissions)}}
									<a href="project_pages_history.php?action=change&item_id={{$smarty.post.version.change_id}}">{{$smarty.post.version.version|intval}}</a>
								{{else}}
									{{$smarty.post.version.version|intval}}
								{{/if}}
								{{assign var="version_date" value=$smarty.post.version.added_date|date_format:$smarty.session.userdata.full_date_format}}
								{{$lang.website_ui.spot_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.version.username}}
							</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.website_ui.spot_field_template_code}}</td>
				<td class="de_control">
					<div class="code_editor" data-syntax="html">
						<textarea name="template" rows="5" cols="40">{{$smarty.post.template}}</textarea>
					</div>
					<span class="de_hint">{{$lang.website_ui.spot_field_template_code_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new_spot'}}
			<input type="hidden" name="action" value="add_new_spot_complete"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
			{{/if}}
		{{else}}
			<input type="hidden" name="action" value="change_spot_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('advertising|edit_all',$smarty.session.permissions) || (in_array('advertising|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_advertising_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.website_ui.submenu_option_advertisements_list}}</a> /
				{{if $smarty.get.action=='add_new'}}
					{{$lang.website_ui.advertisement_add}}
				{{else}}
					<a href="{{$page_name}}?action=change_spot&amp;item_id={{$smarty.post.spot_id}}">{{$smarty.post.spot_title}}</a> /
					{{$lang.website_ui.advertisement_edit|replace:"%1%":$smarty.post.title}}
				{{/if}}
			</h1>
		</div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/158-theme-customization-how-to-show-ads-inside-video-lists/">How to show ads inside video lists</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/121-how-to-show-specific-html-advertising-code-for-specific-categories/">How to show specific HML / advertising code for specific categories</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.advertisement_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_spot}}</td>
				<td class="de_control">
					<select name="spot_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$list_spots|smarty:nodefaults}}
							<option value="{{$item.external_id}}" {{if $item.external_id==$smarty.post.spot_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_spot_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_status}}</td>
				<td class="de_control">
					<select name="is_active">
						<option value="1" {{if $smarty.post.is_active=='1'}}selected{{/if}}>{{$lang.website_ui.advertisement_field_status_active}}</option>
						<option value="0" {{if $smarty.post.is_active=='0'}}selected{{/if}}>{{$lang.website_ui.advertisement_field_status_disabled}}</option>
					</select>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new' && $smarty.post.version.change_id>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.advertisement_field_version}}</td>
					<td class="de_control">
						<span>
							{{if in_array('website_ui|view',$smarty.session.permissions)}}
								<a href="project_pages_history.php?action=change&item_id={{$smarty.post.version.change_id}}">{{$smarty.post.version.version|intval}}</a>
							{{else}}
								{{$smarty.post.version.version|intval}}
							{{/if}}
							{{assign var="version_date" value=$smarty.post.version.added_date|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.website_ui.advertisement_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.version.username}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_html_code}}</td>
				<td class="de_control">
					<div class="code_editor" data-syntax="html">
						<textarea name="code" cols="40" rows="15">{{$smarty.post.code}}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_url}}</td>
				<td class="de_control">
					<input type="text" name="url" maxlength="255" value="{{$smarty.post.url}}"/>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_url_hint|replace:"%1%":$lang.website_ui.advertisement_field_html_code}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.advertisement_divider_restrictions}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_show_date}}</td>
				<td class="de_control">
					<span>
						{{$lang.website_ui.advertisement_field_show_date_from}}:
						<span class="calendar">
							<span class="js_params">
								<span class="js_param">type=datetime</span>
								{{if $can_edit_all!=1}}
									<span class="js_param">forbid_edit=true</span>
								{{/if}}
							</span>
							<input type="text" name="show_from_date" value="{{$smarty.post.show_from_date}}" placeholder="{{$lang.common.select_default_option}}">
						</span>
					</span>
					<span>
						{{$lang.website_ui.advertisement_field_show_date_to}}:
						<span class="calendar">
							<span class="js_params">
								<span class="js_param">type=datetime</span>
								{{if $can_edit_all!=1}}
									<span class="js_param">forbid_edit=true</span>
								{{/if}}
							</span>
							<input type="text" name="show_to_date" value="{{$smarty.post.show_to_date}}" placeholder="{{$lang.common.select_default_option}}">
						</span>
					</span>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_show_date_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_show_time}}</td>
				<td class="de_control">
					<span>
						{{$lang.website_ui.advertisement_field_show_time_from}}:
						<input type="text" name="show_from_time" maxlength="5" size="4" value="{{$smarty.post.show_from_time}}"/>
					</span>
					<span>
						{{$lang.website_ui.advertisement_field_show_time_to}}:
						<input type="text" name="show_to_time" maxlength="5" size="4" value="{{$smarty.post.show_to_time}}"/>
					</span>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_show_time_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_devices}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="devices[]" value="pc" {{if in_array('pc', $smarty.post.devices) || count($smarty.post.devices)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_pc}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="devices[]" value="phone" {{if in_array('phone', $smarty.post.devices) || count($smarty.post.devices)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_phone}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="devices[]" value="tablet" {{if in_array('tablet', $smarty.post.devices) || count($smarty.post.devices)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_tablet}}</label></span>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_devices_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_browsers}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="chrome" {{if in_array('chrome', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_chrome}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="firefox" {{if in_array('firefox', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_firefox}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="safari" {{if in_array('safari', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_safari}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="msie" {{if in_array('msie', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_msie}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="opera" {{if in_array('opera', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_opera}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="yandex" {{if in_array('yandex', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_yandex}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="uc" {{if in_array('uc', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_uc}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="samsung" {{if in_array('samsung', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_samsung}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="bot" {{if in_array('bot', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_bot}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="browsers[]" value="other" {{if in_array('other', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_other}}</label></span>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_browsers_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.advertisement_field_users}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="users[]" value="guest" {{if in_array('guest', $smarty.post.users) || count($smarty.post.users)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_guest}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="users[]" value="active" {{if in_array('active', $smarty.post.users) || count($smarty.post.users)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_active}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="users[]" value="premium" {{if in_array('premium', $smarty.post.users) || count($smarty.post.users)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_premium}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="users[]" value="webmaster" {{if in_array('webmaster', $smarty.post.users) || count($smarty.post.users)==0}}checked{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_webmaster}}</label></span>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_users_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_categories}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.website_ui.advertisement_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_categories_empty}}</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_category"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_categories_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_categories2}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.website_ui.advertisement_field_categories2}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=exclude_category_ids[]</span>
							<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_categories2_empty}}</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach item="item" from=$smarty.post.exclude_categories|smarty:nodefaults}}
							<input type="hidden" name="exclude_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_category"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_categories2_all}}"/>
							</div>
						{{/if}}
					</div>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_categories2_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.advertisement_field_countries}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.website_ui.advertisement_field_countries}}</span>
							<span class="js_param">url=async/insight.php?type=countries</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=countries[]</span>
							<span class="js_param">allow_creation=true</span>
							<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_countries_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach item="item" from=$smarty.post.countries|smarty:nodefaults}}
							<input type="hidden" name="countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_country_{{$index}}"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_countries_all}}"/>
							</div>
						{{/if}}
					</div>
					<span class="de_hint">{{$lang.website_ui.advertisement_field_countries_hint}}</span>
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

{{if in_array('advertising|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('advertising|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit" value=1}}
{{else}}
	{{assign var="can_edit" value=0}}
{{/if}}

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
				<a class="dgf_filters"><i class="icon icon-action-filter"></i>{{$lang.common.dg_filter_filters}}</a>
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$lang.common.dg_filter_btn_submit}}"/>
				</div>
			</div>
			<div class="dgf_advanced_filters">
				<div class="dgf_filter">
					<select name="se_status">
						<option value="" {{if $smarty.session.save.$page_name.se_status==''}}selected{{/if}}>{{$lang.website_ui.advertisement_filter_status}}...</option>
						<option value="active" {{if $smarty.session.save.$page_name.se_status=='active'}}selected{{/if}}>{{$lang.website_ui.advertisement_filter_status_active}}</option>
						<option value="disabled" {{if $smarty.session.save.$page_name.se_status=='disabled'}}selected{{/if}}>{{$lang.website_ui.advertisement_filter_status_disabled}}</option>
						<option value="now" {{if $smarty.session.save.$page_name.se_status=='now'}}selected{{/if}}>{{$lang.website_ui.advertisement_filter_status_now}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_device">
						<option value="" {{if $smarty.session.save.$page_name.se_device==''}}selected{{/if}}>{{$lang.website_ui.advertisement_field_devices}}...</option>
						<option value="pc" {{if $smarty.session.save.$page_name.se_device=='pc'}}selected{{/if}}>{{$lang.website_ui.advertisement_field_devices_pc}}</option>
						<option value="phone" {{if $smarty.session.save.$page_name.se_device=='phone'}}selected{{/if}}>{{$lang.website_ui.advertisement_field_devices_phone}}</option>
						<option value="tablet" {{if $smarty.session.save.$page_name.se_device=='tablet'}}selected{{/if}}>{{$lang.website_ui.advertisement_field_devices_tablet}}</option>
					</select>
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
					{{foreach name="data_spots" item="item_spot" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_spot.external_id}}" {{if count($item_spot.usages)>0}}disabled{{/if}}/></td>
							<td colspan="{{$table_columns_visible-2}}">
								<a href="{{$page_name}}?action=change_spot&amp;item_id={{$item_spot.external_id}}" class="{{if $item_spot.errors==1}}highlighted_text{{elseif $item_spot.warnings==1 || $item_spot.is_debug_enabled==1}}warning_text{{/if}}">{{$item_spot.title}}</a>
								{{if $item_spot.is_debug_enabled==1}}
									<span class="warning_text">({{$lang.website_ui.spot_warning_debug_enabled}})</span>
								{{/if}}
							</td>
							<td class="nowrap">
								<a {{if $item_spot.is_editing_forbidden!=1}}href="{{$page_name}}?action=change_spot&amp;item_id={{$item_spot.external_id}}"{{/if}} class="edit {{if $item_spot.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item_spot.external_id}}</span>
										<span class="js_param">history_id=spot/{{$item_spot.external_id}}</span>
										<span class="js_param">name={{$item_spot.title}}</span>
										<span class="js_param">activate_hide=true</span>
										<span class="js_param">deactivate_hide=true</span>
										{{if count($item_spot.usages)>0}}
											<span class="js_param">delete_disable=true</span>
										{{/if}}
										{{if $item_spot.is_debug_enabled==1}}
											<span class="js_param">enable_debug_hide=true</span>
										{{else}}
											<span class="js_param">disable_debug_hide=true</span>
										{{/if}}
										{{if $item_spot.has_debug_log!=1}}
											<span class="js_param">view_debug_log_hide=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
						{{foreach name="data" item="item" from=$item_spot.ads|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_active==0}}disabled{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_spot.external_id}}/{{$item.advertisement_id}}"/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td class="nowrap">
									<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item_spot.external_id}}/{{$item.advertisement_id}}</span>
											<span class="js_param">history_id=ad/{{$item_spot.external_id}}/{{$item.advertisement_id}}</span>
											<span class="js_param">name={{$item.title}}</span>
											{{if $item.is_active==1}}
												<span class="js_param">activate_hide=true</span>
											{{else}}
												<span class="js_param">deactivate_hide=true</span>
											{{/if}}
											<span class="js_param">enable_debug_hide=true</span>
											<span class="js_param">disable_debug_hide=true</span>
											<span class="js_param">view_debug_log_hide=true</span>
										</span>
									</a>
								</td>
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
						<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
						<span class="js_param">hide=${activate_hide}</span>
						<span class="js_param">icon=action-activate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
						<span class="js_param">hide=${deactivate_hide}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
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
					<li class="js_params">
						<span class="js_param">href=?action=view_debug_log&amp;id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
						<span class="js_param">popup=true</span>
						<span class="js_param">hide=${view_debug_log_hide}</span>
						<span class="js_param">icon=action-log</span>
						<span class="js_param">subicon=action-search</span>
					</li>
				{{/if}}
				{{if in_array('website_ui|view',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=project_pages_history.php?no_filter=true&amp;se_object=${history_id}</span>
						<span class="js_param">title={{$lang.website_ui.common_action_change_history}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-history</span>
					</li>
				{{/if}}
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
						<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
						<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
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
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}