{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de {{if $smarty.post.status_id!=1}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="format_screenshot">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.settings.submenu_option_formats_screenshots_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.format_screenshot_add}}{{else}}{{$lang.settings.format_screenshot_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.format_screenshot_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_title}}</td>
				<td class="de_control"><input type="text" name="title" maxlength="100" value="{{$smarty.post.title}}"/></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_group}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="group_id" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
						<option value="1" {{if $smarty.post.group_id==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_group_main}}</option>
						<option value="2" {{if $smarty.post.group_id==2}}selected{{/if}}>{{$lang.settings.format_screenshot_field_group_timeline}}</option>
						<option value="3" {{if $smarty.post.group_id==3}}selected{{/if}}>{{$lang.settings.format_screenshot_field_group_posters}}</option>
					</select>
					<span class="de_hint group_id_1">{{$lang.settings.format_screenshot_field_group_main_hint}}</span>
					<span class="de_hint group_id_2">{{$lang.settings.format_screenshot_field_group_timeline_hint}}</span>
					<span class="de_hint group_id_3">{{$lang.settings.format_screenshot_field_group_posters_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_size}}</td>
				<td class="de_control">
					<input type="text" name="size" maxlength="9" size="10" value="{{$smarty.post.size}}" {{if $smarty.get.action!='add_new'}}disabled{{/if}}/>
					<span class="de_hint">{{$lang.common.size_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_image_type}}</td>
				<td class="de_control">
					<select name="image_type" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
						<option value="0" {{if $smarty.post.image_type==0}}selected{{/if}}>{{$lang.settings.format_screenshot_field_image_type_jpg}}</option>
						<option value="1" {{if $smarty.post.image_type==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_image_type_webp}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_image_type_hint}}</span>
				</td>
			</tr>
			<tr class="group_id_1 group_id_2">
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_im_options}}</td>
				<td class="de_control">
					<textarea name="im_options" cols="30" rows="3">{{$smarty.post.im_options}}</textarea>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_im_options_hint}}</span>
				</td>
			</tr>
			<tr class="group_id_1 group_id_3">
				<td class="de_label de_required">{{$lang.settings.format_screenshot_field_im_options_manual}}</td>
				<td class="de_control">
					<textarea name="im_options_manual" cols="30" rows="3">{{$smarty.post.im_options_manual}}</textarea>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_im_options_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_aspect_ratio}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="group_label">
								{{$lang.settings.format_screenshot_field_aspect_ratio_horizontal}}:
							</td>
							<td class="de_vis_sw_select">
								<select name="aspect_ratio_id">
									<option value="1" {{if $smarty.post.aspect_ratio_id==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_preserve_source}}</option>
									<option value="2" {{if $smarty.post.aspect_ratio_id==2}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_convert_to_target}}</option>
									<option value="3" {{if $smarty.post.aspect_ratio_id==3}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_dynamic_size}}</option>
								</select>
								<span class="aspect_ratio_id_2">
									{{$lang.settings.format_screenshot_field_aspect_ratio_gravity}}:
									<select name="aspect_ratio_gravity">
										<option value="" {{if $smarty.post.aspect_ratio_gravity==''}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_center}}</option>
										<option value="North" {{if $smarty.post.aspect_ratio_gravity=='North'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_north}}</option>
										<option value="West" {{if $smarty.post.aspect_ratio_gravity=='West'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_west}}</option>
										<option value="East" {{if $smarty.post.aspect_ratio_gravity=='East'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_east}}</option>
										<option value="South" {{if $smarty.post.aspect_ratio_gravity=='South'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_south}}</option>
									</select>
								</span>
							</td>
						</tr>
						<tr>
							<td class="group_label">
								{{$lang.settings.format_screenshot_field_aspect_ratio_vertical}}:
							</td>
							<td class="de_vis_sw_select">
								<select name="vertical_aspect_ratio_id">
									<option value="1" {{if $smarty.post.vertical_aspect_ratio_id==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_preserve_source}}</option>
									<option value="2" {{if $smarty.post.vertical_aspect_ratio_id==2}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_convert_to_target}}</option>
									<option value="3" {{if $smarty.post.vertical_aspect_ratio_id==3}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_dynamic_size}}</option>
								</select>
								<span class="vertical_aspect_ratio_id_2">
									{{$lang.settings.format_screenshot_field_aspect_ratio_gravity}}:
									<select name="vertical_aspect_ratio_gravity">
										<option value="" {{if $smarty.post.vertical_aspect_ratio_gravity==''}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_center}}</option>
										<option value="North" {{if $smarty.post.vertical_aspect_ratio_gravity=='North'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_north}}</option>
										<option value="West" {{if $smarty.post.vertical_aspect_ratio_gravity=='West'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_west}}</option>
										<option value="East" {{if $smarty.post.vertical_aspect_ratio_gravity=='East'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_east}}</option>
										<option value="South" {{if $smarty.post.vertical_aspect_ratio_gravity=='South'}}selected{{/if}}>{{$lang.settings.format_screenshot_field_aspect_ratio_gravity_south}}</option>
									</select>
								</span>
							</td>
						</tr>
					</table>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_aspect_ratio_hint|replace:"%1%":$lang.settings.format_screenshot_field_aspect_ratio_preserve_source|replace:"%2%":$lang.settings.format_screenshot_field_aspect_ratio_convert_to_target|replace:"%3%":$lang.settings.format_screenshot_field_aspect_ratio_dynamic_size}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_interlace}}</td>
				<td class="de_control">
					<select name="interlace_id">
						<option value="0" {{if $smarty.post.interlace_id==0}}selected{{/if}}>{{$lang.settings.format_screenshot_field_interlace_none}}</option>
						<option value="1" {{if $smarty.post.interlace_id==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_interlace_line}}</option>
						<option value="2" {{if $smarty.post.interlace_id==2}}selected{{/if}}>{{$lang.settings.format_screenshot_field_interlace_plane}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_interlace_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_comment}}</td>
				<td class="de_control">
					<input type="text" name="comment" maxlength="255" value="{{$smarty.post.comment}}"/>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_comment_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_create_zip}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_create_zip" value="1" {{if $smarty.post.is_create_zip==1}}checked{{/if}}/><label>{{$lang.settings.format_screenshot_field_create_zip_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_create_zip_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.format_screenshot_divider_watermark}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_watermark_image}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.format_screenshot_field_watermark_image}}</span>
							<span class="js_param">accept=png</span>
							{{if $smarty.post.watermark_image_url!=''}}
								<span class="js_param">preview_url={{$smarty.post.watermark_image_url}}</span>
							{{/if}}
						</div>
						<input type="text" name="watermark_image" maxlength="100" {{if $smarty.post.watermark_image!=''}}value="{{$smarty.post.watermark_image}}"{{/if}}/>
						<input type="hidden" name="watermark_image_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.format_screenshot_field_watermark_image_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_screenshot_field_watermark_position}}</td>
				<td class="de_control">
					<select name="watermark_position_id">
						<option value="0" {{if $smarty.post.watermark_position_id==0}}selected{{/if}}>{{$lang.settings.format_screenshot_field_watermark_position_random}}</option>
						<option value="1" {{if $smarty.post.watermark_position_id==1}}selected{{/if}}>{{$lang.settings.format_screenshot_field_watermark_position_top_left}}</option>
						<option value="2" {{if $smarty.post.watermark_position_id==2}}selected{{/if}}>{{$lang.settings.format_screenshot_field_watermark_position_top_right}}</option>
						<option value="3" {{if $smarty.post.watermark_position_id==3}}selected{{/if}}>{{$lang.settings.format_screenshot_field_watermark_position_bottom_right}}</option>
						<option value="4" {{if $smarty.post.watermark_position_id==4}}selected{{/if}}>{{$lang.settings.format_screenshot_field_watermark_position_bottom_left}}</option>
					</select>
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

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/></td>
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
					{{assign var="group_id" value=1}}
					{{section name="groups" start=0 step=1 loop=3}}
						<tr class="dg_group_header">
							<td colspan="{{$table_columns_visible}}">
								{{if $group_id==1}}
									{{$lang.settings.format_screenshot_field_group_main}}
								{{elseif $group_id==2}}
									{{$lang.settings.format_screenshot_field_group_timeline}}
								{{elseif $group_id==3}}
									{{$lang.settings.format_screenshot_field_group_posters}}
								{{/if}}
							</td>
						</tr>
						{{if count($data[$group_id])>0}}
							{{foreach name="data" item="item" from=$data[$group_id]|smarty:nodefaults}}
								<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0 || $item.status_id==3}}disabled{{/if}}">
									<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled/></td>
									{{assign var="table_columns_display_mode" value="data"}}
									{{include file="table_columns_inc.tpl"}}
									<td class="nowrap">
										<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
										{{if ($item.status_id!=0 && $item.status_id!=3)}}
											<a class="additional" title="{{$lang.common.dg_actions_additional}}">
												<i class="icon icon-action-settings"></i>
												<span class="js_params">
													<span class="js_param">id={{$item.$table_key_name}}</span>
													<span class="js_param">name={{$item.title}}</span>
													{{if $item.status_id==2}}
														<span class="js_param">recreate_hide=true</span>
													{{elseif $item.status_id==4}}
														<span class="js_param">delete_hide=true</span>
														<span class="js_param">recreate_hide=true</span>
													{{else}}
														<span class="js_param">restart_hide=true</span>
													{{/if}}
												</span>
											</a>
										{{/if}}
									</td>
								</tr>
							{{/foreach}}
						{{else}}
							<tr class="dg_empty">
								<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
							</tr>
						{{/if}}
						{{assign var="group_id" value=$group_id+1}}
					{{/section}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.format_screenshot_action_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${delete_hide}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=recreate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.format_screenshot_action_recreate}}</span>
					<span class="js_param">confirm={{$lang.settings.format_screenshot_action_recreate_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${recreate_hide}</span>
					<span class="js_param">icon=action-redo</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.format_screenshot_action_restart}}</span>
					<span class="js_param">confirm={{$lang.settings.format_screenshot_action_restart_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${restart_hide}</span>
					<span class="js_param">icon=action-redo</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>
			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>
		</div>
	</form>
</div>

{{/if}}