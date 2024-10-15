{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="emailing_edit">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.users.emailing_create}}</h1></div>
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
			<tr class="option_email option_test">
				<td class="de_label de_required">{{$lang.users.emailing_field_subject}}</td>
				<td class="de_control">
					<input type="text" name="subject" maxlength="255" value="{{$smarty.post.subject}}"/>
					<span class="de_hint">{{$lang.users.emailing_field_subject_hint}}</span>
				</td>
			</tr>
			<tr class="option_email option_test option_internal">
				<td class="de_label de_required">{{$lang.users.emailing_field_body}}</td>
				<td class="de_control">
					<textarea name="body" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="15">{{$smarty.post.body}}</textarea>
					<span class="de_hint">{{$lang.users.emailing_field_body_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.emailing_field_message_type}}</td>
				<td class="de_control de_vis_sw_radio">
					<span class="de_lv_pair"><input id="option_test" type="radio" name="send_to" value="1" checked/><label>{{$lang.users.emailing_field_message_type_test}}</label></span>
					<span class="de_lv_pair"><input id="option_email" type="radio" name="send_to" value="2"/><label>{{$lang.users.emailing_field_message_type_email}}</label></span>
					<span class="de_lv_pair"><input id="option_internal" type="radio" name="send_to" value="3"/><label>{{$lang.users.emailing_field_message_type_internal}}</label></span>
					<span class="de_lv_pair"><input id="option_export" type="radio" name="send_to" value="4"/><label>{{$lang.users.emailing_field_message_type_export}}</label></span>
				</td>
			</tr>
			<tr class="option_email option_test">
				<td class="de_label de_required">{{$lang.users.emailing_field_headers}}</td>
				<td class="de_control">
					<textarea name="headers" cols="40" rows="5">{{$smarty.session.save.$page_name.headers|default:$config.default_email_headers}}</textarea>
					<span class="de_hint">{{$lang.users.emailing_field_headers_hint}}</span>
				</td>
			</tr>
			<tr class="option_email option_internal">
				<td class="de_label de_required">{{$lang.users.emailing_field_delay}}</td>
				<td class="de_control">
					<input type="text" name="delay" maxlength="32" size="10" value="{{$smarty.session.save.$page_name.delay|default:"0"}}"/>
					<span class="de_hint">{{$lang.users.emailing_field_delay_hint}}</span>
				</td>
			</tr>
			<tr class="option_test">
				<td class="de_label de_required">{{$lang.users.emailing_field_test_mailbox}}</td>
				<td class="de_control">
					<input type="text" name="test_email" maxlength="255" value="{{$smarty.session.save.$page_name.test_email}}"/>
					<span class="de_hint">{{$lang.users.emailing_field_test_mailbox_hint}}</span>
				</td>
			</tr>
			<tr class="option_internal">
				<td class="de_label de_required">{{$lang.users.emailing_field_sender}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<input type="text" name="user_from" maxlength="255" value="{{$smarty.session.save.$page_name.user_from}}"/>
					</div>
					<span class="de_hint">{{$lang.users.emailing_field_sender_hint}}</span>
				</td>
			</tr>
			<tr class="option_email option_internal option_export">
				<td class="de_label de_required">{{$lang.users.emailing_field_receivers}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="2" checked/><label>{{$lang.users.emailing_field_receivers_active}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="3" checked/><label>{{$lang.users.emailing_field_receivers_premium}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="6" checked/><label>{{$lang.users.emailing_field_receivers_webmasters}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="start"/>
		<input type="submit" value="{{$lang.users.emailing_btn_send}}"/>
	</div>
</form>