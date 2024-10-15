{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="admin_group_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_groups_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.admin_user_group_add}}{{else}}{{$lang.settings.admin_user_group_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.admin_user_group_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.admin_user_group_field_title}}</td>
				<td class="de_control"><input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.admin_user_group_field_description}}</td>
				<td class="de_control">
					<textarea name="description" cols="40" rows="4">{{$smarty.post.description}}</textarea>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.admin_user_group_field_permissions}}</h2></td>
			</tr>
			<tr {{if $config.is_clone_db=='true'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.settings.admin_user_group_field_access_to_content}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_access_to_own_content" value="1" {{if $smarty.post.is_access_to_own_content==1}}checked{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_own}}</label></span>
								<span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_own_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_access_to_disabled_content" value="1" {{if $smarty.post.is_access_to_disabled_content==1}}checked{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_disabled}}</label></span>
								<span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_disabled_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_access_to_content_flagged_with" value="1" {{if count($smarty.post.is_access_to_content_flagged_with)>0}}checked{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_flagged}}</label></span>
								{{foreach from=$list_flags_admins|smarty:nodefaults item="item"}}
									<span class="de_lv_pair is_access_to_content_flagged_with_on"><input type="checkbox" name="is_access_to_content_flagged_with_flags[]" value="{{$item.flag_id}}" {{if in_array($item.flag_id, $smarty.post.is_access_to_content_flagged_with)}}checked{{/if}}/><label>{{$item.title}}</label></span>
								{{/foreach}}
								<span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_flagged_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{foreach key="key_gr" item="item_gr" from=$list_permissions|smarty:nodefaults}}
				{{assign var="is_no_access" value=1}}
				{{assign var="is_read_only" value=0}}
				{{assign var="is_full_access" value=1}}

				{{foreach name="data" key="key" item="item" from=$list_permissions.$key_gr|smarty:nodefaults}}
					{{if $item=="`$key_gr`|view" && is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}
						{{assign var="is_read_only" value=1}}
						{{assign var="is_no_access" value=0}}
					{{/if}}

					{{if is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}
						{{assign var="is_no_access" value=0}}
						{{if $item!="`$key_gr`|view"}}
							{{assign var="is_read_only" value=0}}
						{{/if}}
					{{else}}
						{{assign var="is_full_access" value=0}}
					{{/if}}
				{{/foreach}}
				{{if $is_full_access==1}}
					{{assign var="is_read_only" value=0}}
				{{/if}}
				<tr>
					<td class="de_label">
						<div class="access_level_{{$key_gr}}_no disabled">{{$lang.permissions.$key_gr}}</div>
						{{if count($list_permissions.$key_gr)>1}}
							<div class="access_level_{{$key_gr}}_rw access_level_{{$key_gr}}_read">{{$lang.permissions.$key_gr}}</div>
						{{/if}}
						<div class="access_level_{{$key_gr}}_full"><strong>{{$lang.permissions.$key_gr}}</strong></div>
					</td>
					<td class="de_control de_vis_sw_select">
						<select name="access_level_{{$key_gr}}">
							<option value="no" {{if $is_no_access==1}}selected{{/if}}>{{$lang.permissions.access_none}}</option>
							{{if count($list_permissions.$key_gr)>1}}
								{{assign var="read_check" value="`$key_gr`|view"}}
								{{if in_array($read_check,$list_permissions.$key_gr)}}
									<option value="read" {{if $is_read_only==1}}selected{{/if}}>{{$lang.permissions.access_readonly}}</option>
								{{/if}}
								<option value="rw" {{if $is_no_access!=1 && $is_read_only!=1 && $is_full_access!=1}}selected{{/if}}>{{$lang.permissions.access_read_write}}</option>
							{{/if}}
							<option value="full" {{if $is_full_access==1}}selected{{/if}}>{{$lang.permissions.access_full}}</option>
						</select>
						<div class="access_level_{{$key_gr}}_rw{{if $is_no_access==1 || $is_read_only==1 || $is_full_access==1}} hidden{{/if}}">
							{{if count($list_permissions.$key_gr)>1}}
								<table class="control_group">
									<colgroup>
										<col width="25%"/>
										<col width="25%"/>
										<col width="25%"/>
										<col width="25%"/>
									</colgroup>
									<tr>
									{{assign var="iteration" value=1}}
									{{foreach name="data" key="key" item="item" from=$list_permissions.$key_gr|smarty:nodefaults}}
										{{if $item!="`$key_gr`|view" && $lang.permissions.$item!=''}}
											<td><span class="de_lv_pair wrap"><input type="checkbox" name="permissions_ids[]" value="{{$key}}" {{if is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}checked{{/if}}/><label>{{$lang.permissions.$item}}</label></span></td>
											{{if $iteration%4==0 && !$smarty.foreach.data.last}}</tr><tr>{{/if}}
											{{assign var="iteration" value=$iteration+1}}
										{{/if}}
									{{/foreach}}
									{{if $iteration%4==2}}<td></td><td></td><td></td>{{/if}}
									{{if $iteration%4==3}}<td></td><td></td>{{/if}}
									{{if $iteration%4==0}}<td></td>{{/if}}
									</tr>
								</table>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/foreach}}
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
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $item.users_amount>0}}disabled{{/if}}/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.title}}</span>
										{{if $item.users_amount>0}}
											<span class="js_param">delete_disable=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">disable=${delete_disable}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
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
			</ul>
		</div>
	</form>
</div>

{{/if}}