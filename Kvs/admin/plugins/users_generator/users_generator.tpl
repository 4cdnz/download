{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.users_generator.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.users_generator.long_desc}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.users_generator.divider_parameters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.users_generator.field_generate}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="generate">
						<option value="access_codes" {{if $smarty.post.generate=='access_codes'}}selected{{/if}}>{{$lang.plugins.users_generator.field_generate_access_codes}}</option>
						<option value="accounts" {{if $smarty.post.generate=='accounts'}}selected{{/if}}>{{$lang.plugins.users_generator.field_generate_accounts}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.users_generator.field_amount}}</td>
				<td class="de_control">
					<input type="text" name="amount" maxlength="10" value="{{$smarty.post.amount}}"/>
					<span class="de_hint">{{$lang.plugins.users_generator.field_amount_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label access_type_2 access_type_3">{{$lang.plugins.users_generator.field_access_type}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="radio" name="access_type" value="1" {{if $smarty.post.access_type==1}}checked{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_premium_unlimited}}</label></span>
								<span class="de_hint">{{$lang.plugins.users_generator.field_access_type_premium_unlimited_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="radio" name="access_type" value="2" {{if $smarty.post.access_type==2}}checked{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_premium_duration}}:</label></span>
								<input type="text" name="duration" maxlength="10" class="access_type_2" size="4" value="{{$smarty.post.duration}}"/>
								<span class="de_hint">{{$lang.plugins.users_generator.field_access_type_premium_duration_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="radio" name="access_type" value="3" {{if $smarty.post.access_type==3}}checked{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_tokens}}:</label></span>
								<input type="text" name="tokens" maxlength="10" class="access_type_3" size="4" value="{{$smarty.post.tokens}}"/>
								<span class="de_hint">{{$lang.plugins.users_generator.field_access_type_tokens_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="generate_accounts">
				<td class="de_label de_required">{{$lang.plugins.users_generator.field_username_length}}</td>
				<td class="de_control">
					<input type="text" name="username_length" maxlength="10" value="{{$smarty.post.username_length}}"/>
				</td>
			</tr>
			<tr class="generate_accounts">
				<td class="de_label de_required">{{$lang.plugins.users_generator.field_password_length}}</td>
				<td class="de_control">
					<input type="text" name="password_length" maxlength="10" value="{{$smarty.post.password_length}}"/>
				</td>
			</tr>
			<tr class="generate_access_codes">
				<td class="de_label de_required">{{$lang.plugins.users_generator.field_access_code_length}}</td>
				<td class="de_control">
					<input type="text" name="access_code_length" maxlength="10" value="{{$smarty.post.access_code_length}}"/>
				</td>
			</tr>
			<tr class="generate_access_codes">
				<td class="de_label">{{$lang.plugins.users_generator.field_access_code_referral_award}}</td>
				<td class="de_control">
					<input type="text" name="access_code_referral_award" maxlength="10" value="{{$smarty.post.access_code_referral_award}}"/>
					<span class="de_hint">{{$lang.plugins.users_generator.field_access_code_referral_award_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.results}}
				{{if $smarty.post.generate=='access_codes'}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.plugins.users_generator.divider_summary_access_codes}}</h2></td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.users_generator.field_last_exec_date}}</td>
						<td class="de_control">
							<span>
								{{$smarty.post.results_time|date_format:$smarty.session.userdata.full_date_format}}
							</span>
						</td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.users_generator.field_access_codes}}</td>
						<td class="de_control">
							<textarea rows="5" cols="4">{{$smarty.post.results}}</textarea>
							<span class="de_hint">{{$lang.plugins.users_generator.field_access_codes_hint}}</span>
						</td>
					</tr>
				{{else}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.plugins.users_generator.divider_summary_accounts}}</h2></td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.users_generator.field_users}}</td>
						<td class="de_control">
							<textarea rows="5" cols="4">{{$smarty.post.results}}</textarea>
							<span class="de_hint">{{$lang.plugins.users_generator.field_users_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="generate"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.users_generator.btn_generate}}"/>
	</div>
</form>