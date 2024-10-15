{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='htaccess'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="theme_search">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_template_search}}</a> / {{$lang.website_ui.template_search_field_type_htaccess}}</h1></div>
		<table class="de_editor">
			<tr>
				<td class="de_control">
					<div class="code_editor" data-syntax="htaccess">
						<textarea name="htaccess_contents" rows="40" cols="40">{{$smarty.post.htaccess_contents}}</textarea>
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/></td>
						<td>{{$lang.website_ui.template_search_field_type}}</td>
						<td>{{$lang.website_ui.template_search_field_filename}}</td>
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{foreach key="group" item="item_group" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header">
							<td colspan="4">
								{{if $group=='templates'}}
									{{$lang.website_ui.template_search_group_templates}}
								{{elseif $group=='params'}}
									{{$lang.website_ui.template_search_group_params}}
								{{elseif $group=='htaccess'}}
									{{$lang.website_ui.template_search_group_htaccess}}
								{{elseif $group=='advertising'}}
									{{$lang.website_ui.template_search_group_advertising}}
								{{elseif $group=='texts'}}
									{{$lang.website_ui.template_search_group_texts}}
								{{elseif $group=='scripts'}}
									{{$lang.website_ui.template_search_group_scripts}}
								{{elseif $group=='styles'}}
									{{$lang.website_ui.template_search_group_styles}}
								{{/if}}
							</td>
						</tr>
						{{foreach name="data" item="item" from=$item_group|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled/></td>
								<td>
									<a href="{{$item.editor_url|default:$item.file_url}}">
										{{if $item.type=='htaccess'}}
											{{$lang.website_ui.template_search_field_type_htaccess}}
										{{elseif $item.type=='page'}}
											{{$lang.website_ui.template_search_field_type_page|replace:"%1%":$item.page_name}}
										{{elseif $item.type=='component'}}
											{{$lang.website_ui.template_search_field_type_component}}
										{{elseif $item.type=='block_template'}}
											{{$lang.website_ui.template_search_field_type_block|replace:"%1%":$item.block_name|replace:"%2%":$item.page_name}} ({{$lang.website_ui.template_search_field_type_modifier_template}})
										{{elseif $item.type=='block_params'}}
											{{$lang.website_ui.template_search_field_type_block|replace:"%1%":$item.block_name|replace:"%2%":$item.page_name}} ({{$lang.website_ui.template_search_field_type_modifier_params}})
										{{elseif $item.type=='global_block_template'}}
											{{$lang.website_ui.template_search_field_type_global_block|replace:"%1%":$item.block_name}} ({{$lang.website_ui.template_search_field_type_modifier_template}})
										{{elseif $item.type=='global_block_params'}}
											{{$lang.website_ui.template_search_field_type_global_block|replace:"%1%":$item.block_name}} ({{$lang.website_ui.template_search_field_type_modifier_params}})
										{{elseif $item.type=='lang_text'}}
											{{$lang.website_ui.template_search_field_type_text_item|replace:"%1%":$item.external_id}}
											{{if $item.language_title}}
												({{$item.language_title}})
											{{elseif $item.language_code=='default'}}
												({{$lang.website_ui.template_search_field_type_modifier_default_lang}})
											{{/if}}
										{{elseif $item.type=='ad_spot'}}
											{{$lang.website_ui.template_search_field_type_advertisement_spot|replace:"%1%":$item.spot_name}}
										{{elseif $item.type=='ad'}}
											{{$lang.website_ui.template_search_field_type_advertisement|replace:"%1%":$item.advertisement_name}}
										{{else}}
											{{$item.filename}}
										{{/if}}
									</a>
								</td>
								<td>
									{{$item.filename}}
								</td>
								<td class="nowrap">
									<a {{if $item.editor_url}}href="{{$item.editor_url}}"{{/if}} class="edit {{if !$item.editor_url}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								</td>
							</tr>
						{{/foreach}}
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>
		</div>
	</form>
</div>

{{/if}}