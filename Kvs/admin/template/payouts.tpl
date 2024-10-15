{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if in_array('payouts|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="payout_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_payouts_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.payout_add}}{{else}}{{$lang.users.payout_edit|replace:"%1%":$smarty.post.payout_id}}{{/if}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.users.payout_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_status}}</td>
				<td class="de_control">
					<select name="status_id" {{if $smarty.post.status_id!=1}}disabled{{/if}}>
						<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.users.payout_field_status_in_progress}}</option>
						<option value="2" {{if $smarty.post.status_id==2}}selected{{/if}}>{{$lang.users.payout_field_status_closed}}</option>
						<option value="3" {{if $smarty.post.status_id==3}}selected{{/if}}>{{$lang.users.payout_field_status_cancelled}}</option>
					</select>
					<span class="de_hint">{{$lang.users.payout_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_awards}}</td>
				<td class="de_control">
					{{if $smarty.get.action=='add_new'}}
						{{foreach key="key" item="item" from=$list_all_award_types|smarty:nodefaults}}
							<span class="de_lv_pair"><input type="checkbox" name="award_types[]" value="{{$key}}" {{if in_array($key, $smarty.post.award_types)}}checked{{/if}}/><label>{{$item}}</label></span>
						{{/foreach}}
						<span class="de_hint">{{$lang.users.payout_field_awards_hint}}</span>
					{{else}}
						<span>
							{{foreach item="item" name="data" from=$smarty.post.award_types|smarty:nodefaults}}
								{{foreach key="key" item="item_type" from=$list_all_award_types|smarty:nodefaults}}
									{{if $item==$key}}
										{{$item_type}}{{if !$smarty.foreach.data.last}}, {{/if}}
									{{/if}}
								{{/foreach}}
							{{/foreach}}
						</span>
						<span class="de_hint">{{$lang.users.payout_field_awards_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_description}}</td>
				<td class="de_control">
					<textarea name="description" rows="3">{{$smarty.post.description}}</textarea>
					<span class="de_hint">{{$lang.users.payout_field_description_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_exclude_users}}</td>
				<td class="de_control">
					{{if $smarty.get.action!='add_new'}}
						<span>{{$smarty.post.excluded_users|default:$lang.common.users_empty}}</span>
						<span class="de_hint">{{$lang.users.payout_field_exclude_users_hint}}</span>
					{{else}}
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.users.payout_field_exclude_users}}</span>
								<span class="js_param">url=async/insight.php?type=users</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=excluded_users[]</span>
								<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach item="item" from=$smarty.post.excluded_users|smarty:nodefaults}}
								<input type="hidden" name="excluded_users[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_user"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.common.users_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.users.payout_field_exclude_users_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_include_users}}</td>
				<td class="de_control">
					{{if $smarty.get.action!='add_new'}}
						<span>{{$smarty.post.included_users|default:$lang.common.users_empty}}</span>
						<span class="de_hint">{{$lang.users.payout_field_include_users_hint}}</span>
					{{else}}
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.users.payout_field_include_users}}</span>
								<span class="js_param">url=async/insight.php?type=users</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=included_users[]</span>
								<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach item="item" from=$smarty.post.included_users|smarty:nodefaults}}
								<input type="hidden" name="included_users[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
							{{/foreach}}
							<div class="controls">
								<input type="text" name="new_user"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.common.users_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.users.payout_field_include_users_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.payout_field_conversion}}</td>
				<td class="de_control">
					<input type="text" name="conversion" maxlength="20" size="5" value="{{$smarty.post.conversion|replace:",":"."}}" {{if $smarty.get.action!='add_new'}}readonly{{/if}}/>
					{{if $smarty.get.action!='add_new'}}
						<span>{{$smarty.post.conversion_currency}}</span>
					{{else}}
						<select name="conversion_currency">
							<option value="USD" {{if $smarty.post.conversion_currency=='USD'}}selected{{/if}}>USD</option>
							<option value="EUR" {{if $smarty.post.conversion_currency=='EUR'}}selected{{/if}}>EUR</option>
							<option value="GBP" {{if $smarty.post.conversion_currency=='GBP'}}selected{{/if}}>GBP</option>
							<option value="AUD" {{if $smarty.post.conversion_currency=='AUD'}}selected{{/if}}>AUD</option>
							<option value="CAD" {{if $smarty.post.conversion_currency=='CAD'}}selected{{/if}}>CAD</option>
							<option value="CHF" {{if $smarty.post.conversion_currency=='CHF'}}selected{{/if}}>CHF</option>
							<option value="DKK" {{if $smarty.post.conversion_currency=='DKK'}}selected{{/if}}>DKK</option>
							<option value="NOK" {{if $smarty.post.conversion_currency=='NOK'}}selected{{/if}}>NOK</option>
							<option value="SEK" {{if $smarty.post.conversion_currency=='SEK'}}selected{{/if}}>SEK</option>
							<option value="RUB" {{if $smarty.post.conversion_currency=='RUB'}}selected{{/if}}>RUB</option>
						</select>
					{{/if}}
					<span class="de_hint">{{$lang.users.payout_field_conversion_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.payout_field_min_tokens_limit}}</td>
				<td class="de_control">
					<input type="text" name="min_tokens_limit" maxlength="20" size="5" value="{{$smarty.post.min_tokens_limit}}" {{if $smarty.get.action!='add_new'}}readonly{{/if}}/>
					<span class="de_hint">{{$lang.users.payout_field_min_tokens_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_gateway}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="gateway" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
						<option value="manual" {{if $smarty.post.gateway=='manual'}}selected{{/if}}>{{$lang.users.payout_field_gateway_manual}}</option>
						<option value="paypal" {{if $smarty.post.gateway=='paypal'}}selected{{/if}}>{{$lang.users.payout_field_gateway_paypal}}</option>
					</select>
					{{if $smarty.get.action!='add_new' && $smarty.post.gateway!='manual'}}
						<a href="?action=instructions&amp;item_id={{$smarty.post.payout_id}}">{{$lang.users.payout_field_gateway_download}}</a>
					{{/if}}
					<span class="de_hint gateway_manual">{{$lang.users.payout_field_gateway_manual_hint}}</span>
					<span class="de_hint gateway_paypal">{{$lang.users.payout_field_gateway_paypal_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.users.payout_field_tokens}}</td>
					<td class="de_control">
						<input type="text" maxlength="20" size="5" value="{{$smarty.post.tokens}}" readonly/>
						<span class="de_hint">{{$lang.users.payout_field_tokens_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.payout_field_amount}}</td>
					<td class="de_control">
						<input type="text" maxlength="20" size="5" value="{{$smarty.post.amount|replace:",":"."}}" readonly/>
						<span>{{$smarty.post.conversion_currency}}</span>
						<span class="de_hint">{{$lang.users.payout_field_amount_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.payout_field_comment}}</td>
				<td class="de_control">
					<input type="text" name="comment" maxlength="255" value="{{$smarty.post.comment}}" {{if $smarty.get.action!='add_new' && $smarty.post.status_id!=1}}readonly{{/if}}/>
					<span class="de_hint">{{$lang.users.payout_field_comment_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.last_comment!=''}}
				<tr>
					<td class="de_label">{{$lang.users.payout_field_last_comment}}</td>
					<td class="de_control">
						<span>{{$smarty.post.last_comment|replace:"\n":"<br/>"}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.payout_divider_users}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td class="eg_selector"><input type="checkbox" {{if $smarty.post.status_id!=1}}disabled{{/if}}/><label>{{$lang.users.payout_field_delete}}</label></td>
							<td>{{$lang.users.payout_field_user}}</td>
							<td>
								{{if $smarty.post.gateway=='paypal'}}
									{{$lang.users.payout_field_gateway_paypal}}
								{{/if}}
							</td>
							<td>{{$lang.users.payout_field_tokens}}</td>
							<td>{{$lang.users.payout_field_amount}}</td>
						</tr>
						{{if count($smarty.post.user_payments)>0}}
							{{foreach item="item" from=$smarty.post.user_payments|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="eg_selector"><input type="checkbox" name="delete_user[]" value="{{$item.user_id}}" {{if $smarty.post.status_id!=1}}disabled{{/if}}/></td>
									<td>
										{{if $item.username==''}}
											{{$lang.users.payout_field_user_deleted|replace:"%1%":$item.user_id}}
										{{else}}
											{{if in_array('users|view',$smarty.session.permissions)}}
												<a href="users.php?action=change&amp;item_id={{$item.user_id}}">{{$item.username}}</a>
											{{else}}
												{{$item.username}}
											{{/if}}
										{{/if}}
									</td>
									<td>
										{{$item.account}}
									</td>
									<td>
										{{$item.tokens}}
									</td>
									<td>
										{{$item.amount}} {{$item.amount_currency}}
									</td>
								</tr>
							{{/foreach}}
						{{elseif $smarty.get.action=='add_new'}}
							<tr class="eg_data_text">
								<td colspan="5">{{$lang.users.payout_divider_users_hint}}</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
			<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
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
						<option value="">{{$lang.users.payout_field_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.users.payout_field_status_in_progress}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected{{/if}}>{{$lang.users.payout_field_status_closed}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected{{/if}}>{{$lang.users.payout_field_status_cancelled}}</option>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.users.payout_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter de_vis_sw_select">
					<select name="se_period_id">
						<option value="">{{$lang.stats.common_period}}...</option>
						<option value="today" {{if $smarty.session.save.$page_name.se_period_id=='today'}}selected{{/if}}>{{$lang.stats.common_period_today}}</option>
						<option value="yesterday" {{if $smarty.session.save.$page_name.se_period_id=='yesterday'}}selected{{/if}}>{{$lang.stats.common_period_yesterday}}</option>
						<option value="days7" {{if $smarty.session.save.$page_name.se_period_id=='days7'}}selected{{/if}}>{{$lang.stats.common_period_days7}}</option>
						<option value="days30" {{if $smarty.session.save.$page_name.se_period_id=='days30'}}selected{{/if}}>{{$lang.stats.common_period_days30}}</option>
						<option value="current_month" {{if $smarty.session.save.$page_name.se_period_id=='current_month'}}selected{{/if}}>{{$lang.stats.common_period_current_month}}</option>
						<option value="prev_month" {{if $smarty.session.save.$page_name.se_period_id=='prev_month'}}selected{{/if}}>{{$lang.stats.common_period_prev_month}}</option>
						<option value="custom" {{if $smarty.session.save.$page_name.se_period_id=='custom'}}selected{{/if}}>{{$lang.stats.common_period_custom}}</option>
					</select>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_from" value="{{$smarty.session.save.$page_name.se_date_from}}" placeholder="{{$lang.stats.common_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_to" value="{{$smarty.session.save.$page_name.se_date_to}}" placeholder="{{$lang.stats.common_date_to}}...">
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==3}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">

									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
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