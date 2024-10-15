{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="language">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_languages_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.language_add}}{{else}}{{$lang.settings.language_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.language_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.language_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="100" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.language_field_code}}</td>
				<td class="de_control">
					<input type="text" name="code" maxlength="5" {{if $smarty.get.action!='add_new'}}disabled{{/if}} value="{{$smarty.post.code}}"/>
					<span class="de_hint">{{$lang.settings.language_field_code_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.language_field_url}}</td>
				<td class="de_control">
					<input type="text" name="url" maxlength="2" value="{{$smarty.post.url}}"/>
					<span class="de_hint">{{$lang.settings.language_field_url_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.language_divider_scope}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_videos}}</td>
				<td class="de_control">
					<select name="translation_scope_videos">
						<option value="0" {{if $smarty.post.translation_scope_videos==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_videos==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_albums}}</td>
				<td class="de_control">
					<select name="translation_scope_albums">
						<option value="0" {{if $smarty.post.translation_scope_albums==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_albums==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_content_sources}}</td>
				<td class="de_control">
					<select name="translation_scope_content_sources">
						<option value="0" {{if $smarty.post.translation_scope_content_sources==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_content_sources==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_content_source_groups}}</td>
				<td class="de_control">
					<select name="translation_scope_content_sources_groups">
						<option value="0" {{if $smarty.post.translation_scope_content_sources_groups==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_content_sources_groups==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_models}}</td>
				<td class="de_control">
					<select name="translation_scope_models">
						<option value="0" {{if $smarty.post.translation_scope_models==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_models==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_model_groups}}</td>
				<td class="de_control">
					<select name="translation_scope_models_groups">
						<option value="0" {{if $smarty.post.translation_scope_models_groups==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_models_groups==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_dvds}}</td>
				<td class="de_control">
					<select name="translation_scope_dvds">
						<option value="0" {{if $smarty.post.translation_scope_dvds==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_dvds==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_dvd_groups}}</td>
				<td class="de_control">
					<select name="translation_scope_dvds_groups">
						<option value="0" {{if $smarty.post.translation_scope_dvds_groups==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_dvds_groups==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_categories}}</td>
				<td class="de_control">
					<select name="translation_scope_categories">
						<option value="0" {{if $smarty.post.translation_scope_categories==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_categories==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.common.object_type_category_groups}}</td>
				<td class="de_control">
					<select name="translation_scope_categories_groups">
						<option value="0" {{if $smarty.post.translation_scope_categories_groups==0}}selected{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
						<option value="1" {{if $smarty.post.translation_scope_categories_groups==1}}selected{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.language_divider_directories}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.language_field_directories_localize}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_directories_localize" value="1" {{if $smarty.post.is_directories_localize==1}}checked{{/if}}/><label>{{$lang.settings.language_field_directories_localize_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.language_field_directories_localize_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.language_field_directories_translit}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_directories_translit" value="1" class="is_directories_localize_on" {{if $smarty.post.is_directories_translit==1}}checked{{/if}}/><label>{{$lang.settings.language_field_directories_translit_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.language_field_directories_translit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_dependent">{{$lang.settings.language_field_directories_translit_rules}}</td>
				<td class="de_control">
					<textarea name="directories_translit_rules" rows="3" class="is_directories_localize_on is_directories_translit_on">{{$smarty.post.directories_translit_rules}}</textarea>
					<span class="de_hint">{{$lang.settings.language_field_directories_translit_rules_hint}}</span>
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

{{assign var="can_delete" value=1}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/></td>
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
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $item.$table_key_name==''}}disabled{{/if}}/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								{{if $item.$table_key_name!=''}}
									<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">name={{$item.title}}</span>
										</span>
									</a>
								{{/if}}
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