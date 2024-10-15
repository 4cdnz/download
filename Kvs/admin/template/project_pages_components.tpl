{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions) || (in_array('website_ui|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_component_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_page_components}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.website_ui.page_component_add}}{{else}}{{$lang.website_ui.page_component_edit|replace:"%1%":$smarty.post.external_id}}{{/if}}</h1></div>
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
				<td class="de_label de_required">{{$lang.website_ui.page_component_field_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}" {{if $smarty.get.action!='add_new'}}disabled{{/if}}/>
					<span class="de_hint">{{$lang.website_ui.page_component_field_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_component_field_insert_code}}</td>
				<td class="de_control">
					{{if $smarty.get.action=='add_new'}}
						<input type="text" data-autopopulate-from="external_id" data-autopopulate-pattern='{{$smarty.ldelim}}include file="${value}.tpl"{{$smarty.rdelim}}' readonly/>
					{{else}}
						<input type="text" value="{{$smarty.ldelim}}include file=&quot;{{$smarty.post.external_id}}.tpl&quot;{{$smarty.rdelim}}" readonly/>
					{{/if}}
					<span class="de_hint">{{$lang.website_ui.page_component_field_insert_code_hint}}</span>
				</td>
			</tr>
			{{if count($smarty.post.template_includes)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_component_field_uses_components}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" from=$smarty.post.template_includes item="item"}}
								<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if count($smarty.post.spot_inserts)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_component_field_uses_spots}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" from=$smarty.post.spot_inserts item="item"}}
								<a href="project_spots.php?action=change_spot&amp;item_id={{$item.spot_id}}">{{$item.title}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{assign var="page_component_template" value="`$smarty.post.external_id`.tpl"}}
			{{if count($data.$page_component_template)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_component_field_usages}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data2" item="item2" from=$data.$page_component_template|smarty:nodefaults}}
								{{if $item2.external_id!=''}}
									<a href="project_pages.php?action=change&amp;item_id={{$item2.external_id}}">{{$item2.title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
								{{elseif $item2.block_uid!=''}}
									<a href="project_pages.php?action=change_block&amp;item_id={{$item2.block_uid}}&amp;item_name={{$item2.block_title}}">{{$item2.title}} / {{$item2.block_title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
								{{elseif $item2.page_component_id!=''}}
									<a href="project_pages_components.php?action=change&amp;item_id={{$item2.page_component_id}}">{{$item2.page_component_id}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
								{{/if}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action!='add_new' && $smarty.post.version.change_id>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_component_field_version}}</td>
					<td class="de_control">
						<span>
							<a href="project_pages_history.php?action=change&item_id={{$smarty.post.version.change_id}}">{{$smarty.post.version.version|intval}}</a>
							{{assign var="version_date" value=$smarty.post.version.added_date|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.website_ui.page_component_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.version.username}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.page_component_field_template_code}}</td>
				<td class="de_control">
					<div class="code_editor" data-syntax="smarty">
						<textarea name="template" rows="25" cols="40">{{$smarty.post.template}}</textarea>
						<div class="toolbar">
							<input type="button" value="{{$lang.website_ui.common_action_quick_save}}" {{if $smarty.get.action=='add_new'}}disabled {{else}}data-quicksave-action="quick_save"{{/if}}/>
							<div class="separated-group">
								<div class="drop up">
									<i class="icon icon-action-add-code"></i><span>{{$lang.website_ui.common_action_insert_smarty}}</span>
									<ul>
										<li data-insert="{{$smarty.ldelim}}${selection}$variable${selection}{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_variable}}</li>
										<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if}}</li>
										<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}else{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if_else}}</li>
										<li data-insert="{{$smarty.ldelim}}foreach item=&quot;item&quot; from=${selection}$variable${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$item{{$smarty.rdelim}}\n{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_foreach}}</li>
										<li data-insert="{{$smarty.ldelim}}section name=&quot;name&quot; start=0 step=1 loop=${selection}10${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$smarty.section.name.iteration{{$smarty.rdelim}}\n{{$smarty.ldelim}}/section{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_section}}</li>
										<li data-insert="{{$smarty.ldelim}}assign var=&quot;${selection}name${selection}&quot; value=&quot;value&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_assign}}</li>
									</ul>
								</div>
								<div class="drop up">
									<i class="icon icon-action-add-code"></i><span>{{$lang.website_ui.common_action_insert_kvs}}</span>
									<ul>
										<li data-insert="{{$smarty.ldelim}}include file=&quot;${selection}component_id${selection}&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_kvs_component}}</li>
										<li data-insert="{{$smarty.ldelim}}insert name=&quot;getAdv&quot; place_id=&quot;${selection}ad_spot_uid${selection}&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_kvs_ad_spot}}</li>
										<li data-insert="{{$smarty.ldelim}}insert name=&quot;getGlobal&quot; global_id=&quot;${selection}global_block_uid${selection}&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_kvs_global_block}}</li>
										<li data-insert="{{$smarty.ldelim}}$lang.${selection}text_id${selection}{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_kvs_text}}</li>
									</ul>
								</div>
								<div class="push" data-fullscreen-action>
									<i class="icon icon-action-extend"></i>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_component_field_template_doc}}</td>
				<td class="de_control">
					<a href="http://www.smarty.net/docsv2/en/">http://www.smarty.net/docsv2/en/</a>
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
			<input type="hidden" name="item_id" value="{{$smarty.post.external_id}}"/>
			<input type="hidden" name="template_hash" value="{{$smarty.post.template_hash}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('website_ui|add',$smarty.session.permissions)}}
	{{assign var="can_add" value=1}}
{{else}}
	{{assign var="can_add" value=0}}
{{/if}}
{{if in_array('website_ui|delete',$smarty.session.permissions)}}
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
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$lang.common.dg_filter_btn_submit}}"/>
				</div>
			</div>
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						<td>{{$lang.website_ui.page_component_field_id}}</td>
						<td>{{$lang.website_ui.page_component_field_usages}}</td>
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=4}}
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
					{{foreach key="key" name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$key}}" {{if count($item)>0}}disabled{{/if}}/></td>
							<td>
								{{if is_array($smarty.post.invalid_templates) && in_array($key,$smarty.post.invalid_templates)}}
									<a href="{{$page_name}}?action=change&amp;item_id={{$key}}" class="highlighted_text">{{$key}}</a>
								{{elseif is_array($smarty.post.warning_templates) && in_array($key,$smarty.post.warning_templates)}}
									<a href="{{$page_name}}?action=change&amp;item_id={{$key}}" class="warning_text">{{$key}}</a>
								{{else}}
									<a href="{{$page_name}}?action=change&amp;item_id={{$key}}">{{$key}}</a>
								{{/if}}
							</td>
							<td>
								{{foreach name="data2" item="item2" from=$item|smarty:nodefaults}}
									{{if $item2.external_id!=''}}
										<a href="project_pages.php?action=change&amp;item_id={{$item2.external_id}}">{{$item2.title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
									{{elseif $item2.block_uid!=''}}
										<a href="project_pages.php?action=change_block&amp;item_id={{$item2.block_uid}}&amp;item_name={{$item2.block_title}}">{{$item2.title}} / {{$item2.block_title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
									{{elseif $item2.page_component_id!=''}}
										<a href="project_pages_components.php?action=change&amp;item_id={{$item2.page_component_id}}">{{$item2.page_component_id}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
									{{/if}}
								{{/foreach}}
							</td>
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$key}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$key}}</span>
										<span class="js_param">history_id=component/{{$key|replace:".tpl":""}}</span>
										<span class="js_param">name={{$key}}</span>
										<span class="js_param">existing_id={{$key|replace:".tpl":""}}</span>
										{{if count($item)!=0}}
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
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">disable=${delete_disable}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				{{if $can_add==1}}
					<li class="js_params">
						<span class="js_param">href=?action=duplicate&amp;item_id=${id}&amp;external_id=${external_id}</span>
						<span class="js_param">title={{$lang.website_ui.page_component_action_duplicate}}</span>
						<span class="js_param">confirm={{$lang.website_ui.page_component_field_new_id|replace:"%1%":'${existing_id}'}}:</span>
						<span class="js_param">prompt_variable=external_id</span>
						<span class="js_param">icon=action-copy</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=project_pages_history.php?no_filter=true&amp;se_object=${history_id}</span>
					<span class="js_param">title={{$lang.website_ui.common_action_change_history}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=type-history</span>
				</li>
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