{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='restore_pages'}}

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
			<div class="dgf_text">
				{{$lang.website_ui.page_restore}}
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
						<td>{{$lang.website_ui.page_field_page_id}}</td>
						<td>{{$lang.website_ui.page_field_display_name}}</td>
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=4}}
					<tr class="err_list {{if (count($deleted_pages)>0 || $total_num==0) && !is_array($smarty.post.errors)}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{if count($deleted_pages)==0 && $total_num>0}}{{$lang.common.dg_list_error}}{{else}}{{$lang.validation.common_header}}{{/if}}</div>
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
					{{if count($deleted_pages)==0 && $total_num==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$deleted_pages|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}"/></td>
							<td>{{$item.external_id}}</td>
							<td>{{$item.title}}</td>
							<td class="nowrap">
								<a class="edit disabled" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.external_id}}</span>
										<span class="js_param">name={{$item.title}}</span>
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
						<span class="js_param">href=?batch_action=wipeout_page&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.page_action_wipeout}}</span>
						<span class="js_param">confirm={{$lang.website_ui.page_action_wipeout_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				{{if $can_add==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=restore_page&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.page_action_restore}}</span>
						<span class="js_param">confirm={{$lang.website_ui.page_action_restore_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-redo</span>
					</li>
				{{/if}}
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<option value="wipeout_page">{{$lang.website_ui.page_batch_action_wipeout}}</option>
					{{/if}}
					{{if $can_add==1}}
						<option value="restore_page">{{$lang.website_ui.page_batch_action_restore}}</option>
					{{/if}}
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=wipeout_page</span>
					<span class="js_param">confirm={{$lang.website_ui.page_batch_action_wipeout_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=restore_page</span>
					<span class="js_param">confirm={{$lang.website_ui.page_batch_action_restore_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{elseif $smarty.get.action=='restore_blocks'}}

{{if in_array('website_ui|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_text">
				{{$lang.website_ui.block_restore}}
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
					<col/>
					<col width="1%"/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						<td>{{$lang.website_ui.block_field_type}}</td>
						<td>{{$lang.website_ui.block_field_name}}</td>
						<td>{{$lang.website_ui.block_field_insert_code}}</td>
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=5}}
					<tr class="err_list {{if (count($deleted_blocks)>0 || $total_num==0) && !is_array($smarty.post.errors)}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{if count($deleted_blocks)==0 && $total_num>0}}{{$lang.common.dg_list_error}}{{else}}{{$lang.validation.common_header}}{{/if}}</div>
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
					{{if count($deleted_blocks)==0 && $total_num==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$deleted_blocks|smarty:nodefaults}}
						<tr class="dg_group_header">
							<td colspan="5"><a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}">{{$lang.website_ui.block_field_page|replace:"%1%":$item.title}}</a></td>
						</tr>
						{{foreach name="data_blocks" item="item_block" from=$item.blocks|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data_blocks.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}||{{$item_block.block_id}}_{{$item_block.block_name_mod}}"/></td>
								<td>{{$item_block.block_id}}</td>
								<td>{{$item_block.block_name}}</td>
								<td>{{$smarty.ldelim}}insert name="getBlock" block_id="{{$item_block.block_id}}" block_name="{{$item_block.block_name}}"{{$smarty.rdelim}}</td>
								<td class="nowrap">
									<a {{if $item.is_editing_forbidden!=1}}href="project_pages.php?action=change_block&amp;item_id={{$item.external_id}}||{{$item_block.block_id}}||{{$item_block.block_name_mod}}&amp;item_name={{$item_block.block_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.external_id}}||{{$item_block.block_id}}_{{$item_block.block_name_mod}}</span>
											<span class="js_param">name={{$item_block.block_name}}</span>
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
						<span class="js_param">href=?batch_action=wipeout_block&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.block_action_wipeout}}</span>
						<span class="js_param">confirm={{$lang.website_ui.block_action_wipeout_confirm|replace:"%1%":'${name}'}}</span>
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
						<option value="wipeout_block">{{$lang.website_ui.block_batch_action_wipeout|replace:"%1%":'${count}'}}</option>
					{{/if}}
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=wipeout_block</span>
					<span class="js_param">confirm={{$lang.website_ui.block_batch_action_wipeout_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions) || (in_array('website_ui|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_page_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_pages_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.website_ui.page_add}}{{else}}{{$lang.website_ui.page_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
			{{assign var="is_page_without_caching" value=0}}
			{{foreach item="item" from=$smarty.post.blocks|smarty:nodefaults}}
				{{if $item.no_cache==1}}
					{{assign var="is_page_without_caching" value=1}}
				{{/if}}
			{{/foreach}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.page_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.page_field_display_name}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			{{if $smarty.get.action=='add_new'}}
				<tr>
					<td class="de_label de_required">{{$lang.website_ui.page_field_page_id}}</td>
					<td class="de_control">
						<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}"/>
						<span class="de_hint">{{$lang.website_ui.page_field_page_id_hint}}</span>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_field_page_id}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.external_id}} [<a href="{{$config.project_url}}/{{$smarty.post.external_id}}.php">{{$config.project_url}}/{{$smarty.post.external_id}}.php</a>]
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_cache_time}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="cache_time" maxlength="32" size="10" value="{{$smarty.post.cache_time}}" {{if $is_page_without_caching==1}}disabled{{/if}}/>
						{{$lang.common.seconds}}
					</span>
					<span class="de_hint">{{$lang.website_ui.page_field_cache_time_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_field_htaccess_rules}}</td>
					<td class="de_control">
						<div class="code_editor" data-syntax="htaccess">
							<textarea name="htaccess_rules" rows="4" cols="40" readonly>{{$smarty.post.htaccess_rules}}</textarea>
						</div>
						<span class="de_hint">{{$lang.website_ui.page_field_htaccess_rules_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.website_ui.page_field_page_seo}}</td>
					<td class="de_control">
						<span>
							<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=html&amp;se_page={{$smarty.post.external_id}}">{{$lang.website_ui.page_field_page_seo_show}}</a>
						</span>
					</td>
				</tr>
				{{if count($smarty.post.template_includes)>0}}
					<tr>
						<td class="de_label">{{$lang.website_ui.page_field_uses_components}}</td>
						<td class="de_control">
							<span>
								{{foreach item="item" name="data" from=$smarty.post.template_includes}}
									<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
								{{/foreach}}
							</span>
						</td>
					</tr>
				{{/if}}
				{{if count($smarty.post.spot_inserts)>0}}
					<tr>
						<td class="de_label">{{$lang.website_ui.page_field_uses_spots}}</td>
						<td class="de_control">
							<span>
								{{foreach item="item" name="data" from=$smarty.post.spot_inserts}}
									<a href="project_spots.php?action=change_spot&amp;item_id={{$item.spot_id}}">{{$item.title}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
								{{/foreach}}
							</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.get.action!='add_new' && $smarty.post.version.change_id>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_field_version}}</td>
					<td class="de_control">
						<span>
							<a href="project_pages_history.php?action=change&item_id={{$smarty.post.version.change_id}}">{{$smarty.post.version.version|intval}}</a>
							{{assign var="version_date" value=$smarty.post.version.added_date|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.website_ui.page_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.version.username}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.page_field_template_code}}</td>
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
										<li data-insert="{{$smarty.ldelim}}insert name=&quot;getBlock&quot; block_id=&quot;${selection}block_id${selection}&quot; block_name=&quot;${selection}Block Name${selection}&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_kvs_block}}</li>
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
				<td class="de_label">{{$lang.website_ui.page_field_template_doc}}</td>
				<td class="de_control">
					<span>
						<a href="http://www.smarty.net/docsv2/en/">http://www.smarty.net/docsv2/en/</a>
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.page_divider_advanced}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_status}}</td>
				<td class="de_control">
					<select name="is_disabled">
						<option value="0" {{if $smarty.post.is_disabled==0}}selected{{/if}}>{{$lang.website_ui.page_field_status_active}}</option>
						<option value="1" {{if $smarty.post.is_disabled==1}}selected{{/if}}>{{$lang.website_ui.page_field_status_disabled}}</option>
					</select>
					<span class="de_hint">{{$lang.website_ui.page_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_dynamic_http_params}}</td>
				<td class="de_control">
					<input type="text" name="dynamic_http_params" maxlength="100" value="{{$smarty.post.dynamic_http_params}}" {{if $is_page_without_caching==1}}disabled{{/if}}/>
					<span class="de_hint">{{$lang.website_ui.page_field_dynamic_http_params_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_content_type}}</td>
				<td class="de_control">
					<select name="content_type">
						<option value="0" {{if $smarty.post.content_type==0}}selected{{/if}}>{{$lang.website_ui.page_field_content_type_html}}</option>
						<option value="1" {{if $smarty.post.content_type==1}}selected{{/if}}>{{$lang.website_ui.page_field_content_type_xml}}</option>
						<option value="2" {{if $smarty.post.content_type==2}}selected{{/if}}>{{$lang.website_ui.page_field_content_type_json}}</option>
						<option value="3" {{if $smarty.post.content_type==3}}selected{{/if}}>{{$lang.website_ui.page_field_content_type_text}}</option>
					</select>
					<span class="de_hint">{{$lang.website_ui.page_field_content_type_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_memory_limit}}</td>
				<td class="de_control">
					<input type="text" name="memory_limit" maxlength="5" size="10" value="{{if $smarty.post.memory_limit>0}}{{$smarty.post.memory_limit}}{{/if}}"/>
					<span class="de_hint">{{$lang.website_ui.page_field_memory_limit_hint|replace:"%1%":$default_memory_limit}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_block_access}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="access_type_id">
						<option value="0">{{$lang.common.select_default_option}}</option>
						<option value="1" {{if $smarty.post.access_type_id==1}}selected{{/if}}>{{$lang.website_ui.page_field_block_access_anonymous}}</option>
						<option value="2" {{if $smarty.post.access_type_id==2}}selected{{/if}}>{{$lang.website_ui.page_field_block_access_except_premium}}</option>
						<option value="3" {{if $smarty.post.access_type_id==3}}selected{{/if}}>{{$lang.website_ui.page_field_block_access_except_webmaster}}</option>
						<option value="4" {{if $smarty.post.access_type_id==4}}selected{{/if}}>{{$lang.website_ui.page_field_block_access_except_trusted}}</option>
					</select>
					<span class="de_hint">{{$lang.website_ui.page_field_block_access_hint}}</span>
				</td>
			</tr>
			<tr class="access_type_id_1 access_type_id_2 access_type_id_3 access_type_id_4">
				<td class="de_label de_dependent">{{$lang.website_ui.page_field_block_access_url}}</td>
				<td class="de_control">
					<input type="text" name="access_type_redirect_url" maxlength="255" value="{{$smarty.post.access_type_redirect_url}}"/>
					<span class="de_hint">{{$lang.website_ui.page_field_block_access_url_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.page_divider_content}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							{{foreach item="item" from=$smarty.post.blocks|smarty:nodefaults}}
								<tr class="eg_header">
									<td>
										{{if $item.is_global==1}}
											<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_name_dir}}&amp;item_name={{$item.block_name|escape}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.block_name}}</a>
										{{else}}
											<a href="{{$page_name}}?action=change_block&amp;item_id={{$smarty.post.external_id}}||{{$item.block_id}}||{{$item.block_name_dir}}&amp;item_name={{$item.block_name|escape}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.block_name}}</a>
										{{/if}}
									</td>
									<td class="nowrap {{if $item.errors==1}}highlighted_text{{/if}}" colspan="2">
										{{$item.block_id}}{{if $item.is_global==1}} [G]{{/if}}
										{{if $item.critical_errors==0}}<input type="text" name="{{if $item.is_global==1}}global_{{/if}}cache_time_{{$item.block_name_dir}}" maxlength="32" size="7" value="{{$item.cache_time}}" {{if $item.no_cache==1 || $item.is_global==1}}disabled{{/if}}/>{{else}}{{$item.cache_time}}{{/if}} {{$lang.common.second_truncated}}
									</td>
									<td class="{{if $item.critical_errors==1}}highlighted_text{{/if}}">{{if $item.is_global!=1}}$storage.{{$item.block_id}}_{{$item.block_name_dir}}{{else}}$global_storage.{{$item.block_id}}_{{$item.block_name_dir}}{{/if}}</td>
								</tr>
								{{if $item.critical_errors==0}}
									{{foreach item="param" from=$item.params|smarty:nodefaults}}
										<tr class="eg_data">
											<td class="nowrap">
												<span {{if $param.is_deprecated==1}}class="deprecated_text"{{/if}}>{{$param.name}} ({{$param.type_name}})</span>
												<input type="hidden" name="{{if $item.is_global==1}}global_{{/if}}is_{{$item.block_name_dir}}_{{$param.name}}" value="1"/>
											</td>
											<td class="nowrap">
												{{if $param.type=='STRING' || $param.type=='INT' || $param.type=='INT_LIST'}}
													<input type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}" value="{{$param.value}}" {{if $item.is_global==1}}disabled{{/if}}/>
												{{elseif $param.type=='INT_PAIR'}}
													<input size="10" type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}1" value="{{$param.value.0}}" {{if $item.is_global==1}}disabled{{/if}}/>
													/
													<input size="10" type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}2" value="{{$param.value.1}}" {{if $item.is_global==1}}disabled{{/if}}/>
												{{elseif $param.type=='CHOICE' || $param.type=='LIST_BLOCK'}}
													<select name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}" {{if $item.is_global==1}}disabled{{/if}}>
														{{if is_array($param.options)}}
															{{assign var="selected_option_found" value="false"}}
															{{assign var="last_option_group" value=""}}
															{{if $last_option_group!=''}}
																<optgroup label="{{$last_option_group}}">
															{{/if}}
															{{foreach from=$param.options|smarty:nodefaults item="option"}}
																{{if ($last_option_group=='' || $last_option_group!=$option.group) && $option.group!=''}}
																	{{if $last_option_group!=''}}
																		</optgroup>
																	{{/if}}
																	<optgroup label="{{$option.group_desc}}">
																	{{assign var="last_option_group" value=$option.group}}
																{{/if}}
																<option value="{{$option.name}}" {{if $param.value==$option.name || in_array($param.value, $option.obsolete_names)}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$option.desc|default:$option.name}}</option>
															{{/foreach}}
															{{if $last_option_group!=''}}
																</optgroup>
															{{/if}}
															{{if $selected_option_found=='false'}}
																<option value="{{$param.value}}" selected>{{$param.value}}</option>
															{{/if}}
														{{else}}
															{{assign var="selected_option_found" value="false"}}
															{{foreach key="key" item="value" from=$param.values|smarty:nodefaults}}
																<option value="{{$key}}" {{if $param.value==$key}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$value}}</option>
															{{/foreach}}
															{{if $selected_option_found=='false'}}
																<option value="{{$param.value}}" selected>{{$param.value}}</option>
															{{/if}}
														{{/if}}
													</select>
												{{elseif $param.type=='SORTING'}}
													<select name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}1" {{if $item.is_global==1}}disabled{{/if}}>
														{{if is_array($param.options)}}
															{{assign var="selected_option_found" value="false"}}
															{{assign var="last_option_group" value=""}}
															{{if $last_option_group!=''}}
																<optgroup label="{{$last_option_group}}">
															{{/if}}
															{{foreach from=$param.options|smarty:nodefaults item="option"}}
																{{if ($last_option_group=='' || $last_option_group!=$option.group) && $option.group!=''}}
																	{{if $last_option_group!=''}}
																		</optgroup>
																	{{/if}}
																	<optgroup label="{{$option.group_desc}}">
																	{{assign var="last_option_group" value=$option.group}}
																{{/if}}
																<option value="{{$option.name}}" {{if $param.value==$option.name || in_array($param.value, $option.obsolete_names)}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$option.desc|default:$option.name}}</option>
															{{/foreach}}
															{{if $last_option_group!=''}}
																</optgroup>
															{{/if}}
															{{if $selected_option_found=='false'}}
																<option value="{{$param.value}}" selected>{{$param.value}}</option>
															{{/if}}
														{{else}}
															{{assign var="selected_option_found" value="false"}}
															{{foreach key="key" item="value" from=$param.values|smarty:nodefaults}}
																<option value="{{$key}}" {{if $param.value==$key}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$value}}</option>
															{{/foreach}}
															{{if $selected_option_found=='false'}}
																<option value="{{$param.value}}" selected>{{$param.value}}</option>
															{{/if}}
														{{/if}}
													</select>
													<select name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}2" {{if $item.is_global==1}}disabled{{/if}}>
														<option value="desc" {{if $param.value_modifier=='desc'}}selected{{/if}}>{{$lang.common.order_desc}}</option>
														<option value="asc" {{if $param.value_modifier=='asc'}}selected{{/if}}>{{$lang.common.order_asc}}</option>
													</select>
												{{else}}
													{{$lang.website_ui.page_blocks_parameter_enabled}}
												{{/if}}
											</td>
											<td colspan="2"><span class="de_hint">{{$param.desc}}</span></td>
										</tr>
									{{/foreach}}
								{{/if}}
							{{/foreach}}
						</table>
					</td>
				</tr>
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
			<input type="hidden" name="item_id" value="{{$smarty.post.external_id}}"/>
			<input type="hidden" name="template_hash" value="{{$smarty.post.template_hash}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{elseif $smarty.get.action=='change_block'}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions) || (in_array('website_ui|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_block_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				{{if $smarty.post.page_info.is_global=='true'}}
					<a href="project_pages_global.php">{{$lang.website_ui.submenu_option_global_blocks}}</a> / {{$lang.website_ui.block_global_edit|replace:"%1%":$smarty.post.block_name}}
				{{else}}
					<a href="{{$page_name}}">{{$lang.website_ui.submenu_option_pages_list}}</a> / <a href="{{$page_name}}?action=change&amp;item_id={{$smarty.post.page_info.external_id}}">{{$smarty.post.page_info.title}}</a> / {{$lang.website_ui.block_edit|replace:"%1%":$smarty.post.block_name}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.block_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_name}}</td>
				<td class="de_control">
					<span>{{$smarty.post.block_name}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_uid}}</td>
				<td class="de_control">
					<span>{{$smarty.post.block_uid}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_type}}</td>
				<td class="de_control">
					<span>
						<a href="project_blocks.php?action=show_long_desc&amp;block_id={{$smarty.post.block_id}}">{{$smarty.post.block_id}}</a>
					</span>
					<span data-accordeon="doc_expander">{{$lang.website_ui.block_field_type_expander_doc}}</span>
					<span data-accordeon="deftempl_expander">{{$lang.website_ui.block_field_type_expander_template}}</span>
				</td>
			</tr>
			<tr class="doc_expander hidden">
				<td class="de_label">{{$lang.website_ui.block_field_description}}</td>
				<td class="de_control">
					<div class="scrollable_message">
						{{$smarty.post.description}}
						<hr/>
						{{$smarty.post.block_examples}}
					</div>
				</td>
			</tr>
			<tr class="deftempl_expander hidden">
				<td class="de_label">{{$lang.website_ui.block_field_default_template_code}}</td>
				<td class="de_control">
					<div class="code_editor" data-syntax="smarty">
						<textarea name="default_template" rows="30" cols="40" readonly>{{$smarty.post.default_template}}</textarea>
					</div>
					<span class="de_hint">{{$lang.website_ui.block_field_default_template_code_hint}}</span>
				</td>
			</tr>
			{{if count($smarty.post.template_includes)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.block_field_uses_components}}</td>
					<td class="de_control">
						<span>
							{{foreach item="item" name="data" from=$smarty.post.template_includes}}
								<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if count($smarty.post.spot_inserts)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.block_field_uses_spots}}</td>
					<td class="de_control">
						<span>
							{{foreach item="item" name="data" from=$smarty.post.spot_inserts}}
								<a href="project_spots.php?action=change_spot&amp;item_id={{$item.spot_id}}">{{$item.title}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.page_info.is_global=='true'}}
				<tr>
					<td class="de_label">{{$lang.website_ui.block_field_insert_code}}</td>
					<td class="de_control">
						<span>
							{{$smarty.ldelim}}insert name="getGlobal" global_id="{{$smarty.post.block_uid}}"{{$smarty.rdelim}}
						</span>
						<span class="de_hint">{{$lang.website_ui.block_field_insert_code_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.version.change_id>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.block_field_version_template}}</td>
					<td class="de_control">
						<span>
							<a href="project_pages_history.php?action=change&item_id={{$smarty.post.version.change_id}}">{{$smarty.post.version.version|intval}}</a>
							{{assign var="version_date" value=$smarty.post.version.added_date|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.website_ui.block_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.version.username}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.params_version.change_id>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.block_field_version_params}}</td>
					<td class="de_control">
						<span>
							<a href="project_pages_history.php?action=change&item_id={{$smarty.post.params_version.change_id}}">{{$smarty.post.params_version.version|intval}}</a>
							{{assign var="version_date" value=$smarty.post.params_version.added_date|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.website_ui.block_field_version_description|replace:"%1%":$version_date|replace:"%2%":$smarty.post.params_version.username}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.block_field_template_code}}</td>
				<td class="de_control">
					<div class="code_editor" data-syntax="smarty">
						<textarea name="template" rows="30" cols="40">{{$smarty.post.template}}</textarea>
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
				<td class="de_label">{{$lang.website_ui.block_field_template_doc}}</td>
				<td class="de_control">
					<span>
						<a href="http://www.smarty.net/docsv2/en/">http://www.smarty.net/docsv2/en/</a>
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_cache_time}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span>
									<input type="text" name="cache_time" maxlength="32" size="10" value="{{$smarty.post.cache_time}}" {{if $smarty.post.no_cache==1}}disabled{{/if}}/>
									{{$lang.common.seconds}}
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_not_cached_for_members" value="1" {{if $smarty.post.no_cache==1}}disabled{{/if}} {{if $smarty.post.is_not_cached_for_members==1}}checked{{/if}}/><label>{{$lang.website_ui.block_field_cache_time_members}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_dynamic_http_params}}</td>
				<td class="de_control">
					<input type="text" name="dynamic_http_params" maxlength="100" value="{{$smarty.post.dynamic_http_params}}" {{if $smarty.post.no_cache==1}}disabled{{/if}}/>
					<span class="de_hint">{{$lang.website_ui.block_field_dynamic_http_params_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.block_divider_params}}</h2></td>
			</tr>
			{{if count($smarty.post.params)>0}}
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.website_ui.block_params_col_name}}</td>
								<td>{{$lang.website_ui.block_params_col_value}}</td>
								<td>{{$lang.website_ui.block_params_col_description}}</td>
							</tr>
							{{assign var="last_group" value=""}}
							{{foreach item="item" from=$smarty.post.params|smarty:nodefaults}}
								{{if ($last_group=='' || $last_group!=$item.group) && $item.group!=''}}
									{{assign var="last_group" value=$item.group}}
									<tr class="eg_group_header">
										<td colspan="3">{{$item.group_desc}}</td>
									</tr>
								{{/if}}
								<tr class="eg_data">
									<td {{if $item.is_required==1}}class="de_required"{{/if}}>
										<span class="de_lv_pair de_vis_sw_checkbox nowrap">
											<input type="checkbox" name="is_{{$item.name}}" value="1" {{if $item.is_required==1 || $item.is_enabled==1}}checked{{/if}} {{if $item.is_required==1}}disabled{{/if}}/>
											<label class="{{if $item.is_deprecated==1}}deprecated_text{{/if}}">{{$item.name}} ({{$item.type_name}})</label>
										</span>
									</td>
									<td class="nowrap">
										{{if $item.type=='STRING' || $item.type=='INT' || $item.type=='INT_LIST'}}
											<input class="is_{{$item.name}}_on" type="text" name="{{$item.name}}" value="{{$item.value}}" {{if $item.is_enabled!=1}}disabled{{/if}}/>
										{{elseif $item.type=='INT_PAIR'}}
											<input size="10" class="is_{{$item.name}}_on" type="text" name="{{$item.name}}1" value="{{$item.value.0}}" {{if $item.is_enabled!=1}}disabled{{/if}}/>
											/
											<input size="10" class="is_{{$item.name}}_on" type="text" name="{{$item.name}}2" value="{{$item.value.1}}" {{if $item.is_enabled!=1}}disabled{{/if}}/>
										{{elseif $item.type=='CHOICE' || $item.type=='LIST_BLOCK'}}
											<select class="is_{{$item.name}}_on" name="{{$item.name}}" {{if $item.is_enabled!=1}}disabled{{/if}}>
												{{if is_array($item.options)}}
													{{assign var="selected_option_found" value="false"}}
													{{assign var="last_option_group" value=""}}
													{{if $last_option_group!=''}}
														<optgroup label="{{$last_option_group}}">
													{{/if}}
													{{foreach from=$item.options|smarty:nodefaults item="option"}}
														{{if ($last_option_group=='' || $last_option_group!=$option.group) && $option.group!=''}}
															{{if $last_option_group!=''}}
																</optgroup>
															{{/if}}
															<optgroup label="{{$option.group_desc}}">
															{{assign var="last_option_group" value=$option.group}}
														{{/if}}
														<option value="{{$option.name}}" {{if $item.value==$option.name || in_array($item.value, $option.obsolete_names)}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$option.desc|default:$option.name}}</option>
													{{/foreach}}
													{{if $last_option_group!=''}}
														</optgroup>
													{{/if}}
													{{if $selected_option_found=='false'}}
														<option value="{{$item.value}}" selected>{{$item.value}}</option>
													{{/if}}
												{{else}}
													{{assign var="selected_option_found" value="false"}}
													{{foreach key="key" item="value" from=$item.values|smarty:nodefaults}}
														<option value="{{$key}}" {{if $item.value==$key}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$value}}</option>
													{{/foreach}}
													{{if $selected_option_found=='false'}}
														<option value="{{$item.value}}" selected>{{$item.value}}</option>
													{{/if}}
												{{/if}}
											</select>
										{{elseif $item.type=='SORTING'}}
											<select class="is_{{$item.name}}_on" name="{{$item.name}}1" {{if $item.is_enabled!=1}}disabled{{/if}}>
												{{if is_array($item.options)}}
													{{assign var="selected_option_found" value="false"}}
													{{assign var="last_option_group" value=""}}
													{{if $last_option_group!=''}}
														<optgroup label="{{$last_option_group}}">
													{{/if}}
													{{foreach from=$item.options|smarty:nodefaults item="option"}}
														{{if ($last_option_group=='' || $last_option_group!=$option.group) && $option.group!=''}}
															{{if $last_option_group!=''}}
																</optgroup>
															{{/if}}
															<optgroup label="{{$option.group_desc}}">
															{{assign var="last_option_group" value=$option.group}}
														{{/if}}
														<option value="{{$option.name}}" {{if $item.value==$option.name || in_array($item.value, $option.obsolete_names)}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$option.desc|default:$option.name}}</option>
													{{/foreach}}
													{{if $last_option_group!=''}}
														</optgroup>
													{{/if}}
													{{if $selected_option_found=='false'}}
														<option value="{{$item.value}}" selected>{{$item.value}}</option>
													{{/if}}
												{{else}}
													{{assign var="selected_option_found" value="false"}}
													{{foreach key="key" item="value" from=$item.values|smarty:nodefaults}}
														<option value="{{$key}}" {{if $item.value==$key}}{{assign var="selected_option_found" value="true"}}selected{{/if}}>{{$value}}</option>
													{{/foreach}}
													{{if $selected_option_found=='false'}}
														<option value="{{$item.value}}" selected>{{$item.value}}</option>
													{{/if}}
												{{/if}}
											</select>
											<select class="is_{{$item.name}}_on" name="{{$item.name}}2" {{if $item.is_enabled!=1}}disabled{{/if}}>
												<option value="desc" {{if $item.value_modifier=='desc'}}selected{{/if}}>{{$lang.common.order_desc}}</option>
												<option value="asc" {{if $item.value_modifier=='asc'}}selected{{/if}}>{{$lang.common.order_asc}}</option>
											</select>
										{{/if}}
									</td>
									<td><span class="de_hint">{{$item.desc}}</span></td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_control" colspan="2">{{$lang.website_ui.block_divider_params_nothing}}</td>
				</tr>
			{{/if}}
		 </table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_block_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		<input type="hidden" name="item_name" value="{{$smarty.post.block_name}}"/>
		<input type="hidden" name="template_hash" value="{{$smarty.post.template_hash}}"/>
		<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
		<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
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
{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit" value=1}}
{{else}}
	{{assign var="can_edit" value=0}}
{{/if}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
			</div>
			<div class="dgf_filter">
				<div class="drop">
					<i class="icon icon-action-groupby"></i><span>{{$lang.website_ui.dg_pages_filter_show}}</span>
					<ul>
						<li {{if $smarty.session.save.$page_name.se_show_id==''}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=">{{$lang.website_ui.dg_pages_filter_show_all}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_show_id=='active'}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=active">{{$lang.website_ui.dg_pages_filter_show_active}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_show_id=='disabled'}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=disabled">{{$lang.website_ui.dg_pages_filter_show_disabled}}</a></li>
						{{if $collect_performance_stats==1}}
							<li {{if $smarty.session.save.$page_name.se_show_id=='visited'}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=visited">{{$lang.website_ui.dg_pages_filter_show_visited}}</a></li>
							<li {{if $smarty.session.save.$page_name.se_show_id=='slow'}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=slow">{{$lang.website_ui.dg_pages_filter_show_slow}}</a></li>
							<li {{if $smarty.session.save.$page_name.se_show_id=='popular'}}class="selected"{{/if}}><a href="{{$page_name}}?se_show_id=popular">{{$lang.website_ui.dg_pages_filter_show_popular}}</a></li>
						{{/if}}
					</ul>
				</div>
			</div>
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
			</div>
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col/>
					<col width="15%"/>
					<col width="10%"/>
					{{if $collect_performance_stats==1}}
						<col width="5%"/>
						<col width="15%"/>
					{{/if}}
					<col width="1%"/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						<td>{{$lang.website_ui.dg_pages_col_page_block_name}}</td>
						<td>{{$lang.website_ui.dg_pages_col_block_id}}</td>
						<td>{{$lang.website_ui.dg_pages_col_cache}}</td>
						{{if $collect_performance_stats==1}}
							<td>{{$lang.website_ui.dg_pages_col_loads}}</td>
							<td>{{$lang.website_ui.dg_pages_col_performance}}</td>
						{{/if}}
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=5}}
					{{if $collect_performance_stats==1}}
						{{assign var="table_columns_visible" value=$table_columns_visible+2}}
					{{/if}}

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

					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}

						{{assign var="is_page_without_caching" value=0}}
						{{foreach name="data_blocks" item="item_blocks" from=$item.blocks|smarty:nodefaults}}
							{{if $item_blocks.no_cache==1}}
								{{assign var="is_page_without_caching" value=1}}
							{{/if}}
						{{/foreach}}

						<tr class="dg_group_header {{if $item.is_disabled=='1'}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}" {{if $item.is_system==1}}disabled{{/if}}/></td>
							<td colspan="2"><a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}" {{if $item.errors=='1'}}class="highlighted_text" {{elseif $item.warnings=='1'}}class="warning_text"{{/if}}>{{$item.title}} {{if $item.content_type=='1'}}(XML){{elseif $item.content_type=='2'}}(JSON){{elseif $item.content_type=='3'}}(TXT){{/if}}</a></td>
							<td>{{if $item.errors!='1'}}<input type="text" name="cache_time_{{$item.external_id}}" maxlength="32" size="6" value="{{$item.cache_time}}" {{if $can_edit==0 || $is_page_without_caching}}disabled{{/if}}/>{{else}}{{$item.cache_time}}{{/if}} {{$lang.common.second_truncated}}</td>
							{{if $collect_performance_stats==1}}
								<td class="nowrap">{{$item.total_requests|strrev|wordwrap:3:".":true|strrev|default:"0"}}{{if $item.total_requests_needs_k}}{{$lang.common.traffic_k}}{{/if}}</td>
								<td class="nowrap">{{$item.cached_avg_time_s|default:"0"|number_format:2}} / {{$item.uncached_avg_time_s|default:"0"|number_format:2}} / {{$item.cache_pc|default:"0"|intval}}% / {{$item.max_memory|default:"0"|sizeToHumanString}}</td>
							{{/if}}
							<td class="nowrap">
								<a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.external_id}}</span>
										<span class="js_param">history_id=page/{{$item.external_id}}</span>
										<span class="js_param">name={{$item.title}}</span>
										<span class="js_param">existing_id={{$item.external_id}}</span>
										{{if $item.is_system==1}}
											<span class="js_param">delete_hide=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>

						{{assign var="global_blocks" value="0"}}
						{{assign var="global_blocks_slow" value="0"}}
						{{assign var="global_blocks_cached_time" value="0"}}
						{{assign var="global_blocks_uncached_time" value="0"}}
						{{assign var="global_blocks_cache_pc" value="0"}}
						{{assign var="global_blocks_max_memory" value="0"}}
						{{foreach name="data_blocks" item="item_blocks" from=$item.blocks|smarty:nodefaults}}
							{{if $item_blocks.is_global==1}}
								{{assign var="global_blocks" value=$global_blocks+1}}
								{{assign var="global_blocks_cached_time" value=$global_blocks_cached_time+$item_blocks.cached_avg_time_s}}
								{{assign var="global_blocks_uncached_time" value=$global_blocks_uncached_time+$item_blocks.uncached_avg_time_s}}
								{{assign var="global_blocks_cache_pc" value=$global_blocks_cache_pc+$item_blocks.cache_pc}}
								{{assign var="global_blocks_max_memory" value=$global_blocks_max_memory+$item_blocks.max_memory}}
								{{if $item_blocks.is_slow==1}}
									{{assign var="global_blocks_slow" value="1"}}
								{{/if}}
							{{/if}}
						{{/foreach}}
						{{if $global_blocks>0}}
							{{assign var="global_blocks_cache_pc" value=$global_blocks_cache_pc/$global_blocks}}
						{{/if}}

						{{assign var="blocks_iteration" value="0"}}
						{{assign var="global_expander" value="0"}}
						{{foreach name="data_blocks" item="item_blocks" from=$item.blocks|smarty:nodefaults}}
							{{if $item_blocks.is_global==1 && $global_blocks>1 && $global_expander==0}}
								<tr class="dg_data{{if $blocks_iteration % 2==0}} dg_even{{/if}}">
									<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_blocks.block_id}}" disabled/></td>
									<td>
										<span data-accordeon="{{$item.external_id}}_gb" class="collapsed">{{$lang.website_ui.dg_pages_group_global_blocks|replace:"%1%":$global_blocks}}</span>
									</td>
									<td class="nowrap"></td>
									<td></td>
									{{if $collect_performance_stats==1}}
										<td></td>
										<td class="nowrap {{if $global_blocks_slow==1}}warning_text{{/if}}">
											{{$global_blocks_cached_time|default:"0"|number_format:2}} / {{$global_blocks_uncached_time|default:"0"|number_format:2}} / {{$global_blocks_cache_pc|default:"0"|intval}}% / {{$global_blocks_max_memory|default:"0"|sizeToHumanString}}
										</td>
									{{/if}}
									<td></td>
								</tr>
								{{assign var="blocks_iteration" value=$blocks_iteration+1}}
								{{assign var="global_expander" value="1"}}
							{{/if}}
							<tr class="dg_data{{if $blocks_iteration % 2==0}} dg_even{{/if}} {{if $item.is_disabled=='1'}}disabled{{/if}} {{if $item_blocks.is_global==1 && $global_blocks>1}}{{$item.external_id}}_gb{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_blocks.block_id}}" disabled/></td>
								<td>
									{{if $item_blocks.is_global==1}}
										<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" {{if $item_blocks.errors=='1'}}class="highlighted_text" {{elseif $item_blocks.warnings=='1'}}class="warning_text"{{/if}}>{{$item_blocks.block_name}}</a>
									{{else}}
										<a href="{{$page_name}}?action=change_block&amp;item_id={{$item.external_id}}||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" {{if $item_blocks.errors=='1'}}class="highlighted_text" {{elseif $item_blocks.warnings=='1'}}class="warning_text"{{/if}}>{{$item_blocks.block_name}}</a>
									{{/if}}
								</td>
								<td class="nowrap">{{$item_blocks.block_id}}{{if $item_blocks.is_global==1}} [G]{{/if}}</td>
								<td>{{if $item_blocks.errors!='1' && $item_blocks.is_global!=1}}<input type="text" name="cache_time_{{$item.external_id}}_{{$item_blocks.block_name_dir}}" maxlength="32" size="6" value="{{$item_blocks.cache_time}}" {{if $can_edit==0 || $item_blocks.no_cache==1}}disabled{{/if}}/>{{else}}{{$item_blocks.cache_time}}{{/if}} {{$lang.common.second_truncated}}</td>
								{{if $collect_performance_stats==1}}
									<td>{{$item_blocks.total_requests|strrev|wordwrap:3:".":true|strrev|default:"0"}}{{if $item_blocks.total_requests_needs_k}}{{$lang.common.traffic_k}}{{/if}}</td>
									<td class="nowrap {{if $item_blocks.is_slow==1}}warning_text{{/if}}">{{$item_blocks.cached_avg_time_s|default:"0"|number_format:2}} / {{$item_blocks.uncached_avg_time_s|default:"0"|number_format:2}} / {{$item_blocks.cache_pc|default:"0"|intval}}% / {{$item_blocks.max_memory|default:"0"|sizeToHumanString}}</td>
								{{/if}}
								<td class="nowrap">
									{{if $item_blocks.is_global==1}}
										<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" class="edit" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									{{else}}
										<a href="{{$page_name}}?action=change_block&amp;item_id={{$item.external_id}}||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" class="edit" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									{{/if}}
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">history_id=block/{{if $item_blocks.is_global==1}}$global{{else}}{{$item.external_id}}{{/if}}/{{$item_blocks.block_id}}_{{$item_blocks.block_name_dir}}</span>
											<span class="js_param">delete_hide=true</span>
											<span class="js_param">duplicate_hide=true</span>
										</span>
									</a>
								</td>
							</tr>
							{{assign var="blocks_iteration" value=$blocks_iteration+1}}
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
				{{if $can_add==1}}
					<li class="js_params">
						<span class="js_param">href=?action=duplicate&amp;item_id=${id}&amp;external_id=${external_id}</span>
						<span class="js_param">title={{$lang.website_ui.dg_pages_action_duplicate}}</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_pages_field_new_id|replace:"%1%":'${existing_id}'}}:</span>
						<span class="js_param">prompt_variable=external_id</span>
						<span class="js_param">hide=${duplicate_hide}</span>
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
					{{if $can_edit==1}}
						<option value="save_caching">{{$lang.website_ui.dg_pages_btn_save_caching}}</option>
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
				<li class="js_params">
					<span class="js_param">value=save_caching</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.website_ui.dg_pages_btn_save_caching_confirm}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}