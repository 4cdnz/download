{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 0.1
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('messages|edit_all',$smarty.session.permissions) || (in_array('messages|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="message_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_messages_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.message_add}}{{else}}{{$lang.users.message_edit|replace:"%1%":$smarty.post.message_id}}{{/if}}</h1></div>
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
				{{if $smarty.get.action=='add_new'}}
					<td class="de_label de_required">{{$lang.users.message_field_sender}}</td>
					<td class="de_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$smarty.post.user_from_id}}</span>
								{{/if}}
							</div>
							<input type="text" name="user_from" maxlength="255" value="{{$smarty.request.user_from}}"/>
						</div>
						<span class="de_hint">{{$lang.users.message_field_sender_hint}}</span>
					</td>
				{{else}}
					<td class="de_label">{{$lang.users.message_field_sender}}</td>
					<td class="de_control">
						{{if in_array('users|view',$smarty.session.permissions)}}
							<a href="users.php?action=change&amp;item_id={{$smarty.post.user_from_id}}">{{$smarty.post.user_from}}</a>
						{{else}}
							<span>{{$smarty.post.user_from}}</span>
						{{/if}}
					</td>
				{{/if}}
			</tr>
			<tr>
				{{if $smarty.get.action=='add_new'}}
					<td class="de_label de_required">{{$lang.users.message_field_recipient}}</td>
					<td class="de_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$smarty.post.user_from_id}}</span>
								{{/if}}
							</div>
							<input type="text" name="user" maxlength="255" value="{{$smarty.request.user}}"/>
						</div>
						<span class="de_hint">{{$lang.users.message_field_recipient_hint}}</span>
					</td>
				{{else}}
					<td class="de_label">{{$lang.users.message_field_recipient}}</td>
					<td class="de_control">
						{{if in_array('users|view',$smarty.session.permissions)}}
							<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
						{{else}}
							<span>{{$smarty.post.user}}</span>
						{{/if}}
					</td>
				{{/if}}
			</tr>
			<tr>
				<td class="de_label {{if $smarty.post.type_id==0}}de_required{{/if}}">{{$lang.users.message_field_message}}</td>
				<td class="de_control">
					{{if $smarty.post.type_id==0}}
						<textarea name="message" rows="20" cols="40">{{$smarty.post.message}}</textarea>
					{{elseif $smarty.post.type_id==1}}
						<span>{{$lang.users.message_field_message_add_to_friends}}</span>
					{{elseif $smarty.post.type_id==2}}
						<span>{{$lang.users.message_field_message_reject_add_to_friends}}</span>
					{{elseif $smarty.post.type_id==3}}
						<span>{{$lang.users.message_field_message_remove_friends}}</span>
					{{elseif $smarty.post.type_id==4}}
						<span>{{$lang.users.message_field_message_approve_add_to_friends}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.users.message_field_status}}</td>
					<td class="de_control">{{if $smarty.post.is_read==1}}{{$lang.users.message_field_status_read}}{{else}}{{$lang.users.message_field_status_unread}}{{/if}}</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.message_field_added_date}}</td>
					<td class="de_control">{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</td>
				</tr>
				{{if $smarty.post.is_read==1}}
					<tr>
						<td class="de_label">{{$lang.users.message_field_read_date}}</td>
						<td class="de_control">{{$smarty.post.read_date|date_format:$smarty.session.userdata.full_date_format}}</td>
					</tr>
				{{/if}}
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

{{if in_array('messages|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}

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
					<select name="se_is_read">
						<option value="">{{$lang.users.message_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_is_read==='0'}}selected{{/if}}>{{$lang.users.message_field_status_unread}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_is_read==='1'}}selected{{/if}}>{{$lang.users.message_field_status_read}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_type_id">
						<option value="">{{$lang.users.message_field_message}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_type_id==='0'}}selected{{/if}}>{{$lang.users.message_field_message_text}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_type_id==='1'}}selected{{/if}}>{{$lang.users.message_field_message_add_to_friends}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_type_id==='4'}}selected{{/if}}>{{$lang.users.message_field_message_approve_add_to_friends}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_type_id==='2'}}selected{{/if}}>{{$lang.users.message_field_message_reject_add_to_friends}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_type_id==='3'}}selected{{/if}}>{{$lang.users.message_field_message_remove_friends}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_from_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_user_from" value="{{$smarty.session.save.$page_name.se_user_from}}" placeholder="{{$lang.users.message_field_sender}}..."/>
					</div>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.users.message_field_recipient}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.users.message_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_is_spam">
						<option value="">{{$lang.users.message_field_spam}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_is_spam==='1'}}selected{{/if}}>{{$lang.common.term_yes}}</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_is_spam==='0'}}selected{{/if}}>{{$lang.common.term_no}}</option>
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
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_read==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.$table_key_name}}</span>
										<span class="js_param">user_from_id={{$item.user_from_id}}</span>
										<span class="js_param">user_from_name={{$item.user_from}}</span>
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete_from_user&amp;row_select[]=${user_from_id}</span>
						<span class="js_param">title={{$lang.users.message_action_delete_from_user}}</span>
						<span class="js_param">confirm={{$lang.users.message_action_delete_from_user_confirm|replace:"%1%":'${user_from_name}'}}</span>
						<span class="js_param">prompt_value=yes</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
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
						{{if $spam_count>0}}
							<option value="delete_spam">{{$lang.users.message_batch_delete_spam|count_format:"%1%":$spam_count}}</option>
						{{/if}}
					{{/if}}
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_spam</span>
					<span class="js_param">confirm={{$lang.users.message_batch_delete_spam_confirm|count_format:"%1%":$spam_count}}</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}