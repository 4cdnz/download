{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('flags|edit_all',$smarty.session.permissions) || (in_array('flags|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="flag_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.categorization.submenu_option_flags_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.categorization.flag_add}}{{else}}{{$lang.categorization.flag_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/52-categorization-best-practices/">Categorization best practices</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.categorization.flag_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.categorization.flag_field_external_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}"/>
					<span class="de_hint">{{$lang.categorization.flag_field_external_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.flag_field_group}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="group_id" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
						<option value="1" {{if $smarty.post.group_id==1}}selected{{/if}}>{{$lang.categorization.flag_field_group_videos}}</option>
						{{if $config.installation_type==4}}
							<option value="2" {{if $smarty.post.group_id==2}}selected{{/if}}>{{$lang.categorization.flag_field_group_albums}}</option>
							<option value="3" {{if $smarty.post.group_id==3}}selected{{/if}}>{{$lang.categorization.flag_field_group_dvds}}</option>
						{{/if}}
						{{if $config.installation_type>=3}}
							<option value="4" {{if $smarty.post.group_id==4}}selected{{/if}}>{{$lang.categorization.flag_field_group_posts}}</option>
						{{/if}}
						{{if $config.installation_type>=2}}
							<option value="5" {{if $smarty.post.group_id==5}}selected{{/if}}>{{$lang.categorization.flag_field_group_playlists}}</option>
						{{/if}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.flag_field_admin_panel}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr class="group_id_1 group_id_2">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_admin_flag" value="1" {{if $smarty.post.is_admin_flag==1}}checked{{/if}}/><label>{{$lang.categorization.flag_field_admin_panel_admin_flag_yes}}</label></span>
								<span class="de_hint">{{$lang.categorization.flag_field_admin_panel_admin_flag_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_vis_sw_checkbox de_lv_pair"><input type="checkbox" name="is_alert" value="1" {{if $smarty.post.is_alert==1}}checked{{/if}}/><label>{{$lang.categorization.flag_field_admin_panel_alert_show}}</label></span>
								<input type="text" name="alert_min_count" maxlength="10" size="10" class="is_alert_on" value="{{$smarty.post.alert_min_count|default:'1'}}"/>
								<span class="de_hint">{{$lang.categorization.flag_field_admin_panel_alert_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.flag_field_voting}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_vis_sw_checkbox de_lv_pair"><input type="checkbox" name="is_event" value="1" {{if $smarty.post.is_event==1}}checked{{/if}}/><label>{{$lang.categorization.flag_field_voting_event_create}}</label></span>
								<span class="de_hint">{{$lang.categorization.flag_field_voting_event_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_vis_sw_checkbox de_lv_pair"><input type="checkbox" name="is_rating" value="1" {{if $smarty.post.is_rating!=0}}checked{{/if}}/><label>{{$lang.categorization.flag_field_voting_rating_weight_use}}</label></span>
								<input type="text" name="rating_weight" maxlength="10" size="10" class="is_rating_on" value="{{$smarty.post.rating_weight|default:'0'}}"/>
								<span class="de_hint">{{$lang.categorization.flag_field_voting_rating_weight_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_vis_sw_checkbox de_lv_pair"><input type="checkbox" name="is_tokens" value="1" {{if $smarty.post.is_tokens!=0}}checked{{/if}}/><label>{{$lang.categorization.flag_field_voting_tokens_cost_use}}</label></span>
								<input type="text" name="tokens_required" maxlength="10" size="10" class="is_tokens_on" value="{{$smarty.post.tokens_required|default:'0'}}"/>
								<span class="de_hint">{{$lang.categorization.flag_field_voting_tokens_cost_hint}}</span>
							</td>
						</tr>
					</table>
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

{{if in_array('flags|delete',$smarty.session.permissions)}}
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
					{{foreach item="group" key="group_id" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header">
							<td colspan="{{$table_columns_visible}}">
								{{if $group_id==1}}
									{{$lang.categorization.flag_field_group_videos}}
								{{elseif $group_id==2}}
									{{$lang.categorization.flag_field_group_albums}}
								{{elseif $group_id==3}}
									{{$lang.categorization.flag_field_group_dvds}}
								{{elseif $group_id==4}}
									{{$lang.categorization.flag_field_group_posts}}
								{{elseif $group_id==5}}
									{{$lang.categorization.flag_field_group_playlists}}
								{{/if}}
							</td>
						</tr>
						{{foreach name="data" item="item" from=$group|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td class="nowrap">
									<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">name={{$item.title}}</span>
										</span>
									</a>
								</td>
							</tr>
						{{foreachelse}}
							<tr class="dg_data">
								<td colspan="{{$table_columns_visible}}">{{$lang.categorization.flag_field_group_no_flags}}</td>
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

{{/if}}