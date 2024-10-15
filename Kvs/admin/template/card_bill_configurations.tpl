{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if in_array('billing|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
	{{assign var="can_delete" value=0}}
{{/if}}

{{if $smarty.get.action=='change_provider'}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="billing_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_card_billing}}</a> / {{$lang.users.card_bill_config_edit|replace:"%1%":$smarty.post.title}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/109-how-to-add-custom-payment-processor-in-kvs-50/">How to add custom payment processor</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_config_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_bill_type}}</td>
				<td class="de_control">
					<a href="{{$smarty.post.url}}">{{$smarty.post.title}}</a>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_features}}</td>
				<td class="de_control">
					<span>{{if $smarty.post.cf_pkg_rebills==1}}✔️{{else}}❌{{/if}}{{$lang.users.card_bill_config_field_features_rebills}}</span>
					<span>{{if $smarty.post.cf_pkg_trials==1}}✔️{{else}}❌{{/if}}{{$lang.users.card_bill_config_field_features_trials}}</span>
					<span>{{if $smarty.post.cf_pkg_tokens==1}}✔️{{else}}❌{{/if}}{{$lang.users.card_bill_config_field_features_tokens}}</span>
					<span>{{if $smarty.post.cf_pkg_setprice==1}}✔️{{else}}❌{{/if}}{{$lang.users.card_bill_config_field_features_dynamic_pricing}}</span>
					<span>{{if $smarty.post.cf_pkg_oneclick==1}}✔️{{else}}❌{{/if}}{{$lang.users.card_bill_config_field_features_one_click}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_status}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="status_id">
						<option value="0" {{if $smarty.post.status_id=='0'}}selected{{/if}}>{{$lang.users.card_bill_config_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.status_id=='1'}}selected{{/if}}>{{$lang.users.card_bill_config_field_status_active}}</option>
					</select>
					<span class="de_hint">{{$lang.users.card_bill_config_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_default}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_default" value="1" class="status_id_1" {{if $smarty.post.is_default==1}}checked{{/if}}/><label>{{$lang.users.card_bill_config_field_default}}</label></span>
					<span class="de_hint">{{$lang.users.card_bill_config_field_default_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.cf_pkg_trials==1}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_trials}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_trials_as_active" name="options[is_trials_as_active]" value="1" {{if $smarty.post.options.is_trials_as_active==1}}checked{{/if}}/><label>{{$lang.users.card_bill_config_field_trials_as_active}}</label></span>
						<input type="text" name="options[trial_tokens]" class="is_trials_as_active_on" size="10" value="{{$smarty.post.options.trial_tokens|default:"0"}}"/>
						<span class="de_hint">{{$lang.users.card_bill_config_field_trials_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.internal_id!='tokens'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_config_divider_postback}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_url}}</td>
					<td class="de_control">
						<span>{{$config.project_url}}/admin/billings/{{$smarty.post.internal_id}}/{{$config.billing_scripts_name}}.php</span>
					</td>
				</tr>
				{{if $smarty.post.internal_id=='segpay'}}
					<tr>
						<td class="de_label">{{$lang.users.card_bill_config_field_postback_username}}</td>
						<td class="de_control">
							<input type="text" name="postback_username" maxlength="255" value="{{$smarty.post.postback_username}}"/>
							<span class="de_hint">{{$lang.users.card_bill_config_field_postback_username_hint}}</span>
						</td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.users.card_bill_config_field_postback_password}}</td>
						<td class="de_control">
							<input type="text" name="postback_password" maxlength="255" value="{{$smarty.post.postback_password}}"/>
							<span class="de_hint">{{$lang.users.card_bill_config_field_postback_password_hint}}</span>
						</td>
					</tr>
				{{elseif $smarty.post.internal_id=='epoch'}}
					<tr>
						<td class="de_label">{{$lang.users.card_bill_config_field_postback_ip_prefix}}</td>
						<td class="de_control">
							<input type="text" name="postback_ip_protection" maxlength="255" value="{{$smarty.post.postback_ip_protection}}"/>
							<span class="de_hint">{{$lang.users.card_bill_config_field_postback_ip_prefix_hint}}</span>
						</td>
					</tr>
				{{elseif $smarty.post.internal_id=='zombaio'}}
					<tr>
						<td class="de_label">{{$lang.users.card_bill_config_field_postback_password}}</td>
						<td class="de_control">
							<input type="text" name="postback_password" maxlength="255" value="{{$smarty.post.postback_password}}"/>
							<span class="de_hint">{{$lang.users.card_bill_config_field_postback_password_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_reseller_param}}</td>
					<td class="de_control">
						<input type="text" name="postback_reseller_param" maxlength="255" value="{{$smarty.post.postback_reseller_param}}"/>
						<span class="de_hint">{{$lang.users.card_bill_config_field_postback_reseller_param_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_repost_url}}</td>
					<td class="de_control">
						<input type="text" name="postback_repost_url" maxlength="255" value="{{$smarty.post.postback_repost_url}}"/>
						<span class="de_hint">{{$lang.users.card_bill_config_field_postback_repost_url_hint}}</span>
					</td>
				</tr>
				{{if $smarty.post.cf_pkg_setprice==1}}
					<tr>
						<td class="de_label status_id_1">{{$lang.users.card_bill_config_field_signature}}</td>
						<td class="de_control">
							<input type="text" name="signature" maxlength="255" value="{{$smarty.post.signature}}"/>
							<span class="de_hint">{{$lang.users.card_bill_config_field_signature_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.post.internal_id=='ccbill'|| $smarty.post.internal_id=='ccbilldyn'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_config_divider_datalink}}</h2></td>
				</tr>
				<tr>
					<td class="de_label status_id_1">{{$lang.users.card_bill_config_field_datalink_account}}</td>
					<td class="de_control">
						<input type="text" name="account_id" maxlength="50" value="{{$smarty.post.account_id}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label status_id_1">{{$lang.users.card_bill_config_field_datalink_subaccount}}</td>
					<td class="de_control">
						<input type="text" name="sub_account_id" maxlength="50" value="{{$smarty.post.sub_account_id}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label status_id_1">{{$lang.users.card_bill_config_field_datalink_username}}</td>
					<td class="de_control">
						<input type="text" name="datalink_username" maxlength="255" value="{{$smarty.post.datalink_username}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label status_id_1">{{$lang.users.card_bill_config_field_datalink_password}}</td>
					<td class="de_control">
						<input type="text" name="datalink_password" maxlength="255" value="{{$smarty.post.datalink_password}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_datalink_use_ip}}</td>
					<td class="de_control">
						<input type="text" name="datalink_use_ip" maxlength="25" value="{{$smarty.post.datalink_use_ip}}"/>
						<span class="de_hint">{{$lang.users.card_bill_config_field_datalink_use_ip_hint}}</span>
					</td>
				</tr>
			{{elseif $smarty.post.internal_id=='nats' || $smarty.post.internal_id=='natsum'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_config_divider_datalink}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_datalink_url}}</td>
					<td class="de_control">
						<input type="text" name="datalink_url" maxlength="255" value="{{$smarty.post.datalink_url}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_datalink_username}}</td>
					<td class="de_control">
						<input type="text" name="datalink_username" maxlength="255" value="{{$smarty.post.datalink_username}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_datalink_password}}</td>
					<td class="de_control">
						<input type="text" name="datalink_password" maxlength="255" value="{{$smarty.post.datalink_password}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_datalink_use_ip}}</td>
					<td class="de_control">
						<input type="text" name="datalink_use_ip" maxlength="25" value="{{$smarty.post.datalink_use_ip}}"/>
						<span class="de_hint">{{$lang.users.card_bill_config_field_datalink_use_ip_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_config_divider_packages}}</h2></td>
			</tr>
			{{if $can_edit_all==1}}
				<tr>
					<td class="de_control" colspan="2">
						<input type="submit" name="save_and_add_package" value="{{$lang.users.card_bill_package_action_add}}"/>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col/>
							<col/>
							<col/>
							<col/>
							<col/>
							<col class="eg_column_small"/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.users.card_bill_package_field_id}}</td>
							<td>{{$lang.users.card_bill_package_field_title}}</td>
							<td>{{$lang.users.card_bill_package_field_countries}}</td>
							<td>{{$lang.users.card_bill_package_field_order}}</td>
							<td>{{$lang.users.card_bill_package_field_status_active}}</td>
							<td>{{$lang.users.card_bill_package_field_default}}</td>
							<td>{{$lang.users.card_bill_package_action_delete}}</td>
						</tr>
						{{foreach item="item" from=$smarty.post.packages|smarty:nodefaults}}
							<tr class="eg_data">
								<td><a href="{{$page_name}}?action=change&amp;item_id={{$item.package_id}}">{{$item.package_id}}</a></td>
								<td><input type="text" name="title_{{$item.package_id}}" value="{{$item.title}}" maxlength="255"/></td>
								<td>
									{{foreach name="data_countries" item="country" from=$item.countries|smarty:nodefaults}}
										{{$country.title}}{{if !$smarty.foreach.data_countries.last}}, {{/if}}
									{{/foreach}}
								</td>
								<td><input type="text" name="order_{{$item.package_id}}" value="{{$item.sort_id}}" size="3" maxlength="10"/></td>
								<td><input type="checkbox" name="is_active_{{$item.package_id}}" value="1" {{if $item.status_id==1}}checked{{/if}} {{if $item.payment_page_url==''}}disabled{{/if}}/></td>
								<td><input type="radio" name="default_package_id" value="{{$item.package_id}}" {{if $item.is_default==1}}checked{{/if}}/></td>
								<td><input type="checkbox" name="delete_{{$item.package_id}}" value="1"/></td>
							</tr>
						{{foreachelse}}
							<tr class="eg_data_text">
								<td colspan="7">{{$lang.users.card_bill_config_divider_packages_hint}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_provider_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
		<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
	</div>
</form>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="billing_package_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.users.submenu_option_card_billing}}</a>
				/
				<a href="{{$page_name}}?action=change_provider&amp;item_id={{$smarty.post.provider.provider_id}}">{{$smarty.post.provider.title}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.users.card_bill_package_add}}
				{{else}}
					{{$lang.users.card_bill_package_edit|replace:"%1%":$smarty.post.title}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_package_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.card_bill_package_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_package_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id==1}}checked{{/if}}/><label>{{$lang.users.card_bill_package_field_status_active}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.card_bill_package_field_external_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}" {{if $smarty.post.provider.cf_pkg_setprice!=0}}readonly{{/if}}/>
					<span class="de_hint">{{$lang.users.card_bill_package_field_external_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label option_duration option_tokens">{{$lang.users.card_bill_package_field_access_type}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1" {{if $smarty.post.duration_initial==0 && $smarty.post.tokens==0}}checked{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_unlimited}}</label></span>
								<span class="de_hint">{{$lang.users.card_bill_package_field_access_type_unlimited_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2" {{if $smarty.post.duration_initial!=0 && $smarty.post.tokens==0}}checked{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_duration}}:</label></span>
								<span>
									<input type="text" name="duration_initial" maxlength="10" size="10" class="option_duration" value="{{$smarty.post.duration_initial}}"/>
								</span>
								{{if $smarty.post.provider.cf_pkg_rebills==1}}
									<span>
										{{$lang.users.card_bill_package_field_access_type_duration_recurring}}:
										<input type="text" name="duration_rebill" maxlength="10" size="10" class="option_duration" value="{{if $smarty.post.duration_rebill!='0'}}{{$smarty.post.duration_rebill}}{{/if}}"/>
									</span>
								{{/if}}
								<span class="de_hint">{{$lang.users.card_bill_package_field_access_type_duration_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3" {{if $smarty.post.tokens>0}}checked{{/if}} {{if $smarty.post.provider.cf_pkg_tokens==0}}disabled{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_tokens}}:</label></span>
								<span>
									<input type="text" name="tokens" maxlength="10" size="10" class="option_tokens" value="{{$smarty.post.tokens}}"/>
								</span>
								{{if $smarty.post.provider.cf_pkg_tokens==1}}
									<span class="de_hint">{{$lang.users.card_bill_package_field_access_type_tokens_hint}}</span>
								{{else}}
									<span class="de_hint">{{$lang.users.card_bill_package_field_access_type_tokens_hint2}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $smarty.post.provider.cf_pkg_setprice!=0}}
				<tr>
					<td class="de_label de_required">{{$lang.users.card_bill_package_field_price}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="price_initial" maxlength="20" size="10" value="{{$smarty.post.price_initial}}"/>
							<select name="price_initial_currency">
								{{if $smarty.post.provider.internal_id=='tokens'}}
									<option value="TOK" {{if $smarty.post.price_initial_currency=='TOK'}}selected{{/if}}>Tokens</option>
								{{else}}
									<option value="USD" {{if $smarty.post.price_initial_currency=='USD'}}selected{{/if}}>USD</option>
									<option value="EUR" {{if $smarty.post.price_initial_currency=='EUR'}}selected{{/if}}>EUR</option>
									<option value="GBP" {{if $smarty.post.price_initial_currency=='GBP'}}selected{{/if}}>GBP</option>
									<option value="AUD" {{if $smarty.post.price_initial_currency=='AUD'}}selected{{/if}}>AUD</option>
									<option value="CAD" {{if $smarty.post.price_initial_currency=='CAD'}}selected{{/if}}>CAD</option>
									<option value="CHF" {{if $smarty.post.price_initial_currency=='CHF'}}selected{{/if}}>CHF</option>
									<option value="DKK" {{if $smarty.post.price_initial_currency=='DKK'}}selected{{/if}}>DKK</option>
									<option value="NOK" {{if $smarty.post.price_initial_currency=='NOK'}}selected{{/if}}>NOK</option>
									<option value="SEK" {{if $smarty.post.price_initial_currency=='SEK'}}selected{{/if}}>SEK</option>
									<option value="RUB" {{if $smarty.post.price_initial_currency=='RUB'}}selected{{/if}}>RUB</option>
								{{/if}}
							</select>
						</span>
						{{if $smarty.post.provider.cf_pkg_rebills==1}}
							<span>
								{{$lang.users.card_bill_package_field_price_recurring}}:
								<input type="text" name="price_rebill" maxlength="20" size="10" class="option_duration" value="{{$smarty.post.price_rebill}}"/>
									<select name="price_rebill_currency" class="option_duration">
									{{if $smarty.post.provider.internal_id=='tokens'}}
										<option value="TOK" {{if $smarty.post.price_rebill_currency=='TOK'}}selected{{/if}}>Tokens</option>
									{{else}}
										<option value="USD" {{if $smarty.post.price_rebill_currency=='USD'}}selected{{/if}}>USD</option>
										<option value="EUR" {{if $smarty.post.price_rebill_currency=='EUR'}}selected{{/if}}>EUR</option>
										<option value="GBP" {{if $smarty.post.price_rebill_currency=='GBP'}}selected{{/if}}>GBP</option>
										<option value="AUD" {{if $smarty.post.price_rebill_currency=='AUD'}}selected{{/if}}>AUD</option>
										<option value="CAD" {{if $smarty.post.price_rebill_currency=='CAD'}}selected{{/if}}>CAD</option>
										<option value="CHF" {{if $smarty.post.price_rebill_currency=='CHF'}}selected{{/if}}>CHF</option>
										<option value="DKK" {{if $smarty.post.price_rebill_currency=='DKK'}}selected{{/if}}>DKK</option>
										<option value="NOK" {{if $smarty.post.price_rebill_currency=='NOK'}}selected{{/if}}>NOK</option>
										<option value="SEK" {{if $smarty.post.price_rebill_currency=='SEK'}}selected{{/if}}>SEK</option>
										<option value="RUB" {{if $smarty.post.price_rebill_currency=='RUB'}}selected{{/if}}>RUB</option>
									{{/if}}
								</select>
							</span>
						{{/if}}
						<span class="de_hint">{{$lang.users.card_bill_package_field_price_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.provider.internal_id!='tokens'}}
				<tr>
					<td class="de_label de_required">{{$lang.users.card_bill_package_field_payment_page_url}}</td>
					<td class="de_control">
						<input type="text" name="payment_page_url" maxlength="400" value="{{$smarty.post.payment_page_url}}"/>
						<span class="de_hint">
							{{$lang.users.card_bill_package_field_payment_page_url_hint}}
							{{if $smarty.post.provider.example_payment_url!=''}}
								<br/>{{$lang.users.card_bill_package_field_urls_example|replace:"%1%":$smarty.post.provider.example_payment_url}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.provider.cf_pkg_oneclick!=0}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_package_field_oneclick_page_url}}</td>
					<td class="de_control">
						<input type="text" name="oneclick_page_url" maxlength="400" value="{{$smarty.post.oneclick_page_url}}"/>
						<span class="de_hint">
							{{$lang.users.card_bill_package_field_oneclick_page_url_hint}}
							{{if $smarty.post.provider.example_oneclick_url!=''}}
								<br/>{{$lang.users.card_bill_package_field_urls_example|replace:"%1%":$smarty.post.provider.example_oneclick_url}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.card_bill_package_divider_limitation}}</h2></td>
			</tr>
			<td class="de_simple_text" colspan="2">
				<span class="de_hint">{{$lang.users.card_bill_package_divider_limitation_hint}}</span>
			</td>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_package_field_scope}}</td>
				<td class="de_control">
					<select name="scope_id">
						{{if $smarty.post.provider.internal_id!='tokens'}}
							<option value="0" {{if $smarty.post.scope_id==0}}selected{{/if}}>{{$lang.users.card_bill_package_field_scope_all}}</option>
							<option value="1" {{if $smarty.post.scope_id==1}}selected{{/if}}>{{$lang.users.card_bill_package_field_scope_signup}}</option>
						{{/if}}
						<option value="2" {{if $smarty.post.scope_id==2}}selected{{/if}}>{{$lang.users.card_bill_package_field_scope_upgrade}}</option>
					</select>
					<span class="de_hint">{{$lang.users.card_bill_package_field_scope_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_package_field_include_countries}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.card_bill_package_field_include_countries}}</span>
							<span class="js_param">url=async/insight.php?type=countries</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=include_countries[]</span>
							<span class="js_param">allow_creation=true</span>
							<span class="js_param">empty_message={{$lang.users.card_bill_package_field_include_countries_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.include_countries|smarty:nodefaults}}
							<input type="hidden" name="include_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_country_include"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.users.card_bill_package_field_include_countries_all}}"/>
						</div>
						<span class="de_hint">{{$lang.users.card_bill_package_field_include_countries_hint}}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_package_field_exclude_countries}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.card_bill_package_field_exclude_countries}}</span>
							<span class="js_param">url=async/insight.php?type=countries</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=exclude_countries[]</span>
							<span class="js_param">allow_creation=true</span>
							<span class="js_param">empty_message={{$lang.users.card_bill_package_field_exclude_countries_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.exclude_countries|smarty:nodefaults}}
							<input type="hidden" name="exclude_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_country_include"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.users.card_bill_package_field_exclude_countries_all}}"/>
						</div>
						<span class="de_hint">{{$lang.users.card_bill_package_field_exclude_countries_hint}}</span>
					</div>
				</td>
			</tr>
			{{if count($list_satellites)>0}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_package_field_limit_satellite}}</td>
					<td class="de_control">
						<select name="satellite_prefix">
							<option value="">{{$lang.users.card_bill_package_field_limit_satellite_none}}</option>
							<option value="main">{{$lang.users.card_bill_package_field_limit_satellite_main}}</option>
							{{foreach name="data" item="item" from=$list_satellites|smarty:nodefaults}}
								<option value="{{$item.multi_prefix}}" {{if $smarty.post.satellite_prefix==$item.multi_prefix}}selected{{/if}}>{{$item.host}}</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.users.card_bill_package_field_limit_satellite_hint}}</span>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
			<input type="hidden" name="provider_id" value="{{$smarty.post.provider.provider_id}}"/>
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
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
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
					{{foreach name="data_providers" item="item_provider" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header {{if $item_provider.status_id==0}}disabled{{/if}}">
							<td colspan="{{$table_columns_visible-1}}">
								<a href="{{$page_name}}?action=change_provider&amp;item_id={{$item_provider.provider_id}}">{{$item_provider.title}}</a>
								{{if $item_provider.status_id==1}}({{$lang.users.card_bill_config_field_status_active}}{{if $item_provider.is_default==1}}, {{$lang.users.card_bill_config_field_default}}{{/if}}){{/if}}
							</td>
							<td class="nowrap">
								<a href="{{$page_name}}?action=change_provider&amp;item_id={{$item_provider.provider_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">delete_hide=true</span>
										<span class="js_param">internal_id={{$item_provider.internal_id}}</span>
									</span>
								</a>
							</td>
						</tr>
						{{foreach name="data" item="item" from=$item_provider.packages|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td class="nowrap">
									<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.package_id}}</span>
											<span class="js_param">name={{$item.title}}</span>
											<span class="js_param">bill_log_hide=true</span>
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
						<span class="js_param">hide=${delete_hide}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_bill.php?no_filter=true&amp;se_internal_provider_id=${internal_id}</span>
						<span class="js_param">title={{$lang.users.card_bill_config_action_view_log}}</span>
						<span class="js_param">hide=${bill_log_hide}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=action-log</span>
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
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			{{include file="navigation.tpl"}}

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

{{/if}}