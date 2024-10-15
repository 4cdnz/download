{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="theme_history">
	<div class="de_main">
		<div class="de_header">
			<h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_theme_history}}</a> / {{$lang.website_ui.page_history_edit|replace:"%1%":$smarty.post.$table_key_name}}</h1>
		</div>
		<table class="de_editor">
			<colgroup>
				<col width="5%"/>
				<col width="45%"/>
				<col width="5%"/>
				<col width="45%"/>
			</colgroup>
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="4">
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
				<td class="de_label">{{$lang.website_ui.page_history_field_object}}</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.object_link}}
						<a href="{{$smarty.post.object_link}}">{{$smarty.post.object}}</a>
					{{else}}
						<span>{{$smarty.post.object}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_history_field_path}}</td>
				<td class="de_control" colspan="3">
					<span>{{$config.project_path}}{{$smarty.post.path}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_history_field_version}}</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.prev_version.change_id > 0}}
						<span><a href="{{$page_name}}?action=change&item_id={{$smarty.post.prev_version.change_id}}">{{$smarty.post.prev_version.version}}</a> ({{$smarty.post.prev_version.added_date|date_format:$smarty.session.userdata.full_date_format}})</span>
						<span><--</span>
					{{/if}}
					<span><b>{{$smarty.post.version}} ({{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}})</b></span>
					{{if $smarty.post.next_version.change_id > 0}}
						<span>--></span>
						<span><a href="{{$page_name}}?action=change&item_id={{$smarty.post.next_version.change_id}}">{{$smarty.post.next_version.version}}</a> ({{$smarty.post.next_version.added_date|date_format:$smarty.session.userdata.full_date_format}})</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_history_field_author}}</td>
				<td class="de_control" colspan="3">
					<span>{{$smarty.post.username}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_history_field_new_content}}</td>
				<td class="de_control">
					<div class="code_editor">
						<textarea name="new_content" rows="30" cols="40">{{$smarty.post.file_content}}</textarea>
					</div>
					<span class="de_hint">{{$lang.website_ui.page_history_field_new_content_hint}}</span>
				</td>
				<td class="de_label">{{$lang.website_ui.page_history_field_old_content}}</td>
				<td class="de_control">
					<div class="code_editor">
						<textarea name="old_content" rows="30" cols="40">{{$smarty.post.prev_version.file_content}}</textarea>
					</div>
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
					<select name="se_type">
						<option value="">{{$lang.website_ui.page_history_field_object_type}}...</option>
						<option value="pages" {{if $smarty.session.save.$page_name.se_type=='pages'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_pages}}</option>
						<option value="blocks" {{if $smarty.session.save.$page_name.se_type=='blocks'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_blocks}}</option>
						<option value="global" {{if $smarty.session.save.$page_name.se_type=='global'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_global}}</option>
						<option value="components" {{if $smarty.session.save.$page_name.se_type=='components'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_components}}</option>
						<option value="advertising" {{if $smarty.session.save.$page_name.se_type=='advertising'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_advertising}}</option>
						<option value="files" {{if $smarty.session.save.$page_name.se_type=='files'}}selected{{/if}}>{{$lang.website_ui.page_history_field_object_type_files}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_object">
						<option value="">{{$lang.website_ui.page_history_field_object}}...</option>
						{{assign var="last_type" value=""}}
						{{foreach name="objects" from=$list_objects item="item"}}
							{{if $last_type!=$item.type && ($item.type == 'page' || $item.type == 'component' || $item.type == 'global_template' || $item.type == 'spot')}}
								{{if $last_type==''}}
									<optgroup label="{{if $item.type=='page'}}{{$lang.website_ui.page_history_field_object_type_pages}}{{elseif $item.type=='component'}}{{$lang.website_ui.page_history_field_object_type_components}}{{elseif $item.type=='global_template'}}{{$lang.website_ui.page_history_field_object_type_global}}{{elseif $item.type=='spot'}}{{$lang.website_ui.page_history_field_object_type_advertising}}{{/if}}">
								{{/if}}
								{{if $last_type!=''}}
									</optgroup>
									<optgroup label="{{if $item.type=='page'}}{{$lang.website_ui.page_history_field_object_type_pages}}{{elseif $item.type=='component'}}{{$lang.website_ui.page_history_field_object_type_components}}{{elseif $item.type=='global_template'}}{{$lang.website_ui.page_history_field_object_type_global}}{{elseif $item.type=='spot'}}{{$lang.website_ui.page_history_field_object_type_advertising}}{{/if}}">
								{{/if}}
								{{assign var="last_type" value=$item.type}}
							{{/if}}
							{{assign var="object_key" value=""}}
							{{assign var="object_name" value=""}}
							{{if $item.type =='page'}}
								{{assign var="object_key" value="page/`$item.page_id`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_page|replace:"%1%":$item.page_name|replace:"<strong>":""|replace:"</strong>":""}}
							{{elseif $item.type =='block_template'}}
								{{assign var="object_key" value="block/`$item.page_id`/`$item.block_id`_`$item.block_internal_name`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_block|replace:"%1%":$item.block_name|replace:"%2%":$item.page_name|replace:"<strong>":""|replace:"</strong>":""}}
								{{assign var="object_name" value="--- `$object_name`"}}
							{{elseif $item.type =='global_template'}}
								{{assign var="object_key" value="global/`$item.block_id`_`$item.block_internal_name`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_global|replace:"%1%":$item.block_name|replace:"<strong>":""|replace:"</strong>":""}}
							{{elseif $item.type =='component'}}
								{{assign var="object_key" value="component/`$item.component_id`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_component|replace:"%1%":$item.component_name|replace:"<strong>":""|replace:"</strong>":""}}
							{{elseif $item.type =='spot'}}
								{{assign var="object_key" value="spot/`$item.spot_id`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_ad_spot|replace:"%1%":$item.spot_name|replace:"<strong>":""|replace:"</strong>":""}}
							{{elseif $item.type =='ad'}}
								{{assign var="object_key" value="ad/`$item.spot_id`/`$item.ad_id`"}}
								{{assign var="object_name" value=$lang.website_ui.page_history_field_object_ad|replace:"%1%":$item.ad_name|replace:"%2%":$item.spot_name|replace:"<strong>":""|replace:"</strong>":""}}
								{{assign var="object_name" value="--- `$object_name`"}}
							{{/if}}
							{{if $object_key}}
								<option value="{{$object_key}}" {{if $smarty.session.save.$page_name.se_object==$object_key}}selected{{/if}}>
									{{$object_name}}
								</option>
							{{/if}}
							{{if $smarty.foreach.objects.last}}
								</optgroup>
							{{/if}}
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_username">
						<option value="">{{$lang.website_ui.page_history_field_author}}...</option>
						{{foreach from=$list_usernames item="item"}}
							<option value="{{$item}}" {{if $smarty.session.save.$page_name.se_username==$item}}selected{{/if}}>{{$item}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_from" value="{{$smarty.session.save.$page_name.se_date_from}}" placeholder="{{$lang.website_ui.page_history_filter_added_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_to" value="{{$smarty.session.save.$page_name.se_date_to}}" placeholder="{{$lang.website_ui.page_history_filter_added_date_to}}...">
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/><span></span></td>
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
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
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