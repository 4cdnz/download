{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if in_array('billing|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0 || !($smarty.get.action=='add_new' || $smarty.post.status_id==1 || ($smarty.post.status_id==2 && $smarty.post.tokens_granted>0))}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="billing_transaction_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_billing_transactions}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.bill_transaction_add}}{{else}}{{$lang.users.bill_transaction_edit|replace:"%1%":$smarty.post.transaction_id}}{{/if}}</h1></div>
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
				<td class="de_label">{{$lang.users.bill_transaction_field_bill_type}}</td>
				<td class="de_control" colspan="3">
					<span>
						{{if $smarty.get.action=='add_new'}}
							{{$lang.users.bill_transaction_field_bill_type_manual}}
						{{else}}
							{{if $smarty.post.bill_type_id==5}}
								{{$lang.users.bill_transaction_field_bill_type_htpasswd}}
							{{elseif $smarty.post.bill_type_id==4}}
								{{$lang.users.bill_transaction_field_bill_type_api}}
							{{elseif $smarty.post.bill_type_id==3}}
								{{$lang.users.bill_transaction_field_bill_type_sms}} {{if $smarty.post.internal_provider!=''}}({{$smarty.post.internal_provider}}){{/if}}
							{{elseif $smarty.post.bill_type_id==2}}
								{{$lang.users.bill_transaction_field_bill_type_card}} {{if $smarty.post.internal_provider!=''}}({{$smarty.post.internal_provider}}){{/if}}
							{{elseif $smarty.post.bill_type_id==1}}
								{{$lang.users.bill_transaction_field_bill_type_manual}}
							{{/if}}
						{{/if}}
					</span>
				</td>
			</tr>
			{{if $smarty.get.action=='add_new'}}
				<tr>
					<td class="de_label de_required">{{$lang.users.bill_transaction_field_user}}</td>
					<td class="de_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<input type="text" name="user" maxlength="255" value="{{$smarty.request.user}}"/>
						</div>
						<span class="de_hint">{{$lang.users.bill_transaction_field_user_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label option_duration option_tokens">{{$lang.users.bill_transaction_field_access_type}}</td>
					<td class="de_control de_vis_sw_radio">
						<table class="control_group">
							<tr>
								<td>
									<span class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1"/><label>{{$lang.users.bill_transaction_field_access_type_unlimited}}</label></span>
									<span class="de_hint">{{$lang.users.bill_transaction_field_access_type_unlimited_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2"/><label>{{$lang.users.bill_transaction_field_access_type_duration}}:</label></span>
									<input type="text" name="duration" maxlength="10" size="5" class="option_duration"/>
									<span class="de_hint">{{$lang.users.bill_transaction_field_access_type_duration_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3"/><label>{{$lang.users.bill_transaction_field_access_type_tokens}}:</label></span>
									<input type="text" name="tokens" maxlength="10" size="5" class="option_tokens"/>
									<span class="de_hint">{{$lang.users.bill_transaction_field_access_type_tokens_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_description}}</td>
					<td class="de_control">
						<textarea name="transaction_log" rows="4" cols="40"></textarea>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_type}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.type_id==10}}
								{{$lang.users.bill_transaction_field_type_tokens}} ({{$smarty.post.tokens_granted}})
							{{elseif $smarty.post.type_id==6}}
								{{$lang.users.bill_transaction_field_type_void}}
							{{elseif $smarty.post.type_id==5}}
								{{$lang.users.bill_transaction_field_type_refund}}
							{{elseif $smarty.post.type_id==4}}
								{{$lang.users.bill_transaction_field_type_chargeback}}
							{{elseif $smarty.post.type_id==3}}
								{{$lang.users.bill_transaction_field_type_rebill}}
							{{elseif $smarty.post.type_id==2}}
								{{$lang.users.bill_transaction_field_type_conversion}}
							{{elseif $smarty.post.type_id==1}}
								{{$lang.users.bill_transaction_field_type_initial}} {{if $smarty.post.is_trial==1}}({{$lang.users.bill_transaction_field_type_initial_trial}}){{/if}}
							{{/if}}
						<span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_status}}</td>
					<td class="de_control">
						{{if $smarty.post.status_id=='1'}}
							<select name="status_id">
								<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_open}}</option>
								<option value="3" {{if $smarty.post.status_id==3}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
							</select>
						{{elseif $smarty.post.status_id=='0'}}
							<span>{{$lang.users.bill_transaction_field_status_approval}}</span>
						{{elseif $smarty.post.status_id=='2'}}
							{{if $smarty.post.tokens_granted>0}}
								<select name="status_id">
									<option value="2" {{if $smarty.post.status_id==2}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_closed}}</option>
									<option value="3" {{if $smarty.post.status_id==3}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
								</select>
							{{else}}
								<span>{{$lang.users.bill_transaction_field_status_closed}}</span>
							{{/if}}
						{{elseif $smarty.post.status_id=='4'}}
							<span>{{$lang.users.bill_transaction_field_status_pending}}</span>
						{{else}}
							<span>{{$lang.users.bill_transaction_field_status_cancelled}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_user}}</td>
					<td class="de_control">
						{{if $smarty.post.user_id==0}}
							<span>{{$lang.users.bill_transaction_field_user_waiting}}</span>
						{{else}}
							{{if $smarty.post.user!=''}}
								{{if in_array('users|view',$smarty.session.permissions)}}
									<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
								{{else}}
									<span>{{$smarty.post.user}}</span>
								{{/if}}
							{{else}}
								<span>{{$lang.common.user_deleted|replace:"%1%":$smarty.post.user_id}}</span>
							{{/if}}
						{{/if}}
					</td>
				</tr>
				{{if $smarty.post.access_code!=''}}
					<tr>
						<td class="de_label">{{$lang.users.bill_transaction_field_access_code}}</td>
						<td class="de_control">
							<span>{{$smarty.post.access_code}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_start_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.access_start_date=='0000-00-00 00:00:00'}}
								{{$lang.common.undefined}}
							{{else}}
								{{$smarty.post.access_start_date|date_format:$smarty.session.userdata.full_date_format}}
							{{/if}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_end_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.is_unlimited_access==1}}
								{{$lang.users.bill_transaction_field_end_date_unlimited}}
							{{elseif $smarty.post.access_end_date=='0000-00-00 00:00:00'}}
								{{$lang.common.undefined}} {{if $smarty.post.duration_rebill>0}}({{$lang.users.bill_transaction_field_end_date_waiting|count_format:"%1%":$smarty.post.duration_rebill}}){{/if}}
							{{else}}
								{{$smarty.post.access_end_date|date_format:$smarty.session.userdata.full_date_format}} {{if $smarty.post.duration_rebill>0}}({{$lang.users.bill_transaction_field_end_date_rebillable|count_format:"%1%":$smarty.post.duration_rebill}}){{/if}}
							{{/if}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_log}}</td>
					<td class="de_control">
						<textarea name="transaction_log" rows="10" cols="40" readonly>{{$smarty.post.transaction_log}}</textarea>
					</td>
				</tr>
			{{/if}}
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

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
				{{if count($search_fields)>0}}
					<div class="dgf_search_layer">
						<span>{{$lang.common.dg_filter_search_in}}:</span>
						<ul>
							{{assign var="search_everywhere" value="true"}}
							{{foreach from=$search_fields|smarty:nodefaults item="field"}}
								<li>
									{{assign var="option_id" value="se_text_`$field.id`"}}
									<input type="hidden" name="{{$option_id}}" value="0"/>
									<span class="de_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked{{/if}}/><label>{{$field.title}}</label></span>
									{{if $smarty.session.save.$page_name[$option_id]!=1}}
										{{assign var="search_everywhere" value="false"}}
									{{/if}}
								</li>
							{{/foreach}}
							<li class="dgf_everywhere">
								<span class="de_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></span>
							</li>
						</ul>
					</div>
				{{/if}}
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
					<select name="se_status_id">
						<option value="">{{$lang.users.bill_transaction_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_approval}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_open}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_closed}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected{{/if}}>{{$lang.users.bill_transaction_field_status_pending}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.users.bill_transaction_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.users.bill_transaction_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_bill_type_id">
						<option value="">{{$lang.users.bill_transaction_field_bill_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_bill_type_id==1}}selected{{/if}}>{{$lang.users.bill_transaction_field_bill_type_manual}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_bill_type_id==2}}selected{{/if}}>{{$lang.users.bill_transaction_field_bill_type_card}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_bill_type_id==4}}selected{{/if}}>{{$lang.users.bill_transaction_field_bill_type_api}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_bill_type_id==5}}selected{{/if}}>{{$lang.users.bill_transaction_field_bill_type_htpasswd}}</option>
						{{foreach item="item" from=$list_providers|smarty:nodefaults}}
							<option value="{{$item.internal_id}}" {{if $smarty.session.save.$page_name.se_bill_type_id==$item.internal_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_type_id">
						<option value="">{{$lang.users.bill_transaction_field_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_type_id==1}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_initial}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_type_id==2}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_conversion}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_type_id==3}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_rebill}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_type_id==4}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_chargeback}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_type_id==5}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_refund}}</option>
						<option value="6" {{if $smarty.session.save.$page_name.se_type_id==6}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_void}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_type_id==10}}selected{{/if}}>{{$lang.users.bill_transaction_field_type_tokens}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_start_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.users.bill_transaction_filter_start_date_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_start_date_from" value="{{$smarty.session.save.$page_name.se_start_date_from}}" placeholder="{{$lang.users.bill_transaction_filter_start_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_start_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.users.bill_transaction_filter_start_date_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_start_date_to" value="{{$smarty.session.save.$page_name.se_start_date_to}}" placeholder="{{$lang.users.bill_transaction_filter_start_date_to}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_end_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.users.bill_transaction_filter_end_date_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_end_date_from" value="{{$smarty.session.save.$page_name.se_end_date_from}}" placeholder="{{$lang.users.bill_transaction_filter_end_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_end_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.users.bill_transaction_filter_end_date_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_end_date_to" value="{{$smarty.session.save.$page_name.se_end_date_to}}" placeholder="{{$lang.users.bill_transaction_filter_end_date_to}}...">
					</div>
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
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								{{if $item.status_id==1 || ($item.status_id==2 && $item.tokens_granted>0)}}
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">user={{$item.user|default:$item.user_id}}</span>
										</span>
									</a>
								{{/if}}
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_edit_all==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=cancel&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.users.bill_transaction_action_cancel}}</span>
						<span class="js_param">confirm={{$lang.users.bill_transaction_action_cancel_confirm|replace:"%1%":'${id}'|replace:"%2%":'${user}'}}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>
		</div>
	</form>
</div>

{{/if}}