{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="log_import_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_imports_log}}</a> / {{$lang.settings.import_view|replace:"%1%":$smarty.post.import_id}}</h1></div>
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
				<td class="de_label">{{$lang.settings.import_field_status}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.status_id=='0'}}
							{{$lang.settings.import_field_status_scheduled}}
						{{elseif $smarty.post.status_id=='1'}}
							{{$lang.settings.import_field_status_in_process}}
						{{elseif $smarty.post.status_id=='2'}}
							{{$lang.settings.import_field_status_completed}}
						{{elseif $smarty.post.status_id=='3'}}
							{{$lang.settings.import_field_status_cancelled}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.import_field_type}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.type_id=='1'}}
							{{$lang.settings.import_field_type_videos_import}}
						{{elseif $smarty.post.type_id=='2'}}
							{{$lang.settings.import_field_type_albums_import}}
						{{elseif $smarty.post.type_id=='3'}}
							{{$lang.settings.import_field_type_videos_update}}
						{{elseif $smarty.post.type_id=='4'}}
							{{$lang.settings.import_field_type_albums_update}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.import_field_data}}</td>
				<td class="de_control">
					<textarea name="data" rows="10" cols="40">{{$smarty.post.data}}</textarea>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.import_field_log}}</td>
				<td class="de_control">
					<textarea name="log" rows="10" cols="40">{{$smarty.post.log}}</textarea>
				</td>
			</tr>
		</table>
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
						<option value="">{{$lang.settings.import_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.settings.import_field_status_scheduled}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.settings.import_field_status_in_process}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected{{/if}}>{{$lang.settings.import_field_status_completed}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected{{/if}}>{{$lang.settings.import_field_status_cancelled}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_type_id">
						<option value="">{{$lang.settings.import_field_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_type_id=='1'}}selected{{/if}}>{{$lang.settings.import_field_type_videos_import}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_type_id=='2'}}selected{{/if}}>{{$lang.settings.import_field_type_albums_import}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_type_id=='3'}}selected{{/if}}>{{$lang.settings.import_field_type_videos_update}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_type_id=='4'}}selected{{/if}}>{{$lang.settings.import_field_type_albums_update}}</option>
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
										{{if $item.status_id==0}}
											<span class="js_param">log_disable=true</span>
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
					<span class="js_param">href=?action=import_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.import_action_view_log}}</span>
					<span class="js_param">disable=${log_disable}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=new_import&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.import_action_new_import}}</span>
					<span class="js_param">icon=action-copy</span>
				</li>
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