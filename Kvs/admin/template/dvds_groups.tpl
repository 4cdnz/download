{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('dvds_groups|edit_all',$smarty.session.permissions) || (in_array('dvds_groups|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="dvd_group_edit">
	<div>
		{{if $options.DVD_GROUP_COVER_OPTION==0}}
			<input type="hidden" name="cover2" value="{{$smarty.post.cover2}}"/>
			<input type="hidden" name="cover2_hash"/>
		{{/if}}
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
		<input type="hidden" name="custom4" value="{{$smarty.post.custom4}}"/>
		<input type="hidden" name="custom5" value="{{$smarty.post.custom5}}"/>
	</div>
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.videos.submenu_option_dvd_groups_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.videos.dvd_group_add}}{{else}}{{$lang.videos.dvd_group_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
				<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_group_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.dvd_group_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
				{{if is_array($sidebar_fields)}}
					{{assign var="sidebar_rowspan" value="6"}}
					{{if $smarty.post.website_link!=''}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
					{{/if}}
					{{if $options.DVD_GROUP_COVER_OPTION>0}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
					{{/if}}

					{{assign var="image_field" value=""}}
					{{if $smarty.post.cover1!=''}}
						{{assign var="image_field" value="cover1"}}
					{{/if}}
					{{if $options.DVD_GROUP_COVER_OPTION>0}}
						{{assign var="image_size1" value="x"|explode:$options.DVD_GROUP_COVER_1_SIZE}}
						{{assign var="image_size2" value="x"|explode:$options.DVD_GROUP_COVER_2_SIZE}}
						{{if ($image_size1[0]>$image_size2[0] || $smarty.post.cover1=='') && $smarty.post.cover2!=''}}
							{{assign var="image_field" value="cover2"}}
						{{/if}}
					{{/if}}
					{{if $image_field!=''}}
						{{assign var="sidebar_image_url" value="`$config.content_url_dvds`/groups/`$smarty.post.dvd_group_id`/`$smarty.post.$image_field`"}}
					{{/if}}

					{{include file="editor_sidebar_inc.tpl"}}
				{{/if}}
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_group_field_directory}}</td>
					<td class="de_control">
						<input type="text" name="dir" maxlength="255" value="{{$smarty.post.dir}}"/>
						<span class="de_hint">{{$lang.videos.dvd_group_field_directory_hint|replace:"%1%":$lang.videos.dvd_group_field_title}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.website_link!=''}}
				<tr data-field-name="website_link">
					<td class="de_label">{{$lang.videos.dvd_group_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_external_id}}</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" value="{{$smarty.post.external_id}}"/>
					<span class="de_hint">{{$lang.videos.dvd_group_field_external_id_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_description}}</td>
				<td class="de_control">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}}/><label>{{$lang.videos.dvd_group_field_status_active}}</label></span>
					<span class="de_hint">{{$lang.videos.dvd_group_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_cover1}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_group_field_cover1}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover1!='' && in_array(end(explode(".",$smarty.post.cover1)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_dvds}}/groups/{{$smarty.post.dvd_group_id}}/{{$smarty.post.cover1}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover1" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover1!=''}}value="{{$smarty.post.cover1}}"{{/if}}/>
						<input type="hidden" name="cover1_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.videos.dvd_group_field_cover1_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_GROUP_COVER_1_SIZE}}</a>)</span>
				</td>
			</tr>
			{{if $options.DVD_GROUP_COVER_OPTION>0}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_group_field_cover2}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.dvd_group_field_cover2}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.cover2!='' && in_array(end(explode(".",$smarty.post.cover2)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$config.content_url_dvds}}/groups/{{$smarty.post.dvd_group_id}}/{{$smarty.post.cover2}}</span>
								{{/if}}
							</div>
							<input type="text" name="cover2" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover2!=''}}value="{{$smarty.post.cover2}}"{{/if}}/>
							<input type="hidden" name="cover2_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.dvd_group_field_cover2_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_GROUP_COVER_2_SIZE}}</a>){{if $options.DVD_GROUP_COVER_OPTION==1}}; {{$lang.videos.dvd_group_field_cover2_hint2|replace:"%1%":$lang.videos.dvd_group_field_cover1}}{{/if}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_group_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_tags}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_group_field_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=simple</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
							{{if in_array('tags|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_group_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_tag"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_group_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_categories}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_group_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_group_field_categories_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_category"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_group_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_models}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_group_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=model_ids[]</span>
							{{if in_array('models|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_group_field_models_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
							<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_model"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_group_field_models_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $options.ENABLE_DVD_GROUP_FIELD_1==1 || $options.ENABLE_DVD_GROUP_FIELD_2==1 || $options.ENABLE_DVD_GROUP_FIELD_3==1 || $options.ENABLE_DVD_GROUP_FIELD_4==1 || $options.ENABLE_DVD_GROUP_FIELD_5==1}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_group_divider_customization}}</h2></td>
				</tr>
				{{if $options.ENABLE_DVD_GROUP_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.DVD_GROUP_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom1" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_GROUP_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.DVD_GROUP_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom2" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_GROUP_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.DVD_GROUP_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom3" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_GROUP_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.DVD_GROUP_FIELD_4_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom4" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom4}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_GROUP_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.DVD_GROUP_FIELD_5_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom5" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom5}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_group_divider_dvds}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_group_field_add_dvds}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_group_field_add_dvds}}</span>
							<span class="js_param">url=async/insight.php?type=dvds</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=add_dvd_ids[]</span>
							{{if in_array('dvds|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_group_field_add_dvds_empty}}</span>
						</div>
						<div class="list"></div>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_dvd"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_group_field_add_dvds_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if count($smarty.post.dvds)>0}}
				<tr>
					<td class="de_simple_text" colspan="3">
						<span class="de_hint">{{$lang.videos.dvd_group_divider_dvds_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="3">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td class="eg_selector"><input type="checkbox"/><label>{{$lang.common.dg_actions_detach}}</label></td>
								<td>{{$lang.videos.dvd_field_id}}</td>
								<td>{{$lang.videos.dvd_field_title}}</td>
								<td>{{$lang.videos.dvd_field_videos}}</td>
								<td>{{$lang.videos.dvd_field_visits}}</td>
								<td>{{$lang.videos.dvd_field_rating}}</td>
								<td>{{$lang.videos.dvd_field_order}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.dvds|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="eg_selector"><input type="checkbox" name="delete_dvd_ids[]" value="{{$item.dvd_id}}"/></td>
									<td class="nowrap">
										{{if in_array('dvds|view',$smarty.session.permissions)}}
											<a href="dvds.php?action=change&amp;item_id={{$item.dvd_id}}">{{$item.dvd_id}}</a>
										{{else}}
											{{$item.dvd_id}}
										{{/if}}
									</td>
									<td>{{$item.title}}</td>
									<td class="nowrap {{if $item.total_videos==0}}disabled{{/if}}">{{$item.total_videos|number_format:0:".":" "}}</td>
									<td class="nowrap {{if $item.dvd_viewed==0}}disabled{{/if}}">{{$item.dvd_viewed|number_format:0:".":" "}}</td>
									<td class="nowrap {{if $item.rating==0}}disabled{{/if}}">{{$item.rating|number_format:1:".":" "}}</td>
									<td>
										<input type="text" name="dvd_sorting_{{$item.dvd_id}}" maxlength="32" value="{{$item.sort_id}}" size="3" autocomplete="off"/>
									</td>
								</tr>
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
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
				<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
			{{else}}
				<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
				<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
			{{/if}}
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('dvds_groups|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('dvds_groups|edit_all',$smarty.session.permissions)}}
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
						<option value="">{{$lang.videos.dvd_group_field_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.videos.dvd_group_field_status_active}}</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.videos.dvd_group_field_status_disabled}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=tags</span>
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_tag_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.videos.dvd_group_field_tag}}"/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=categories</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_category_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.videos.dvd_group_field_category}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=models</span>
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_model_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_model" value="{{$smarty.session.save.$page_name.se_model}}" placeholder="{{$lang.videos.dvd_group_field_model}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_description}}</option>
						<option value="empty/cover1" {{if $smarty.session.save.$page_name.se_field=="empty/cover1"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_cover1}}</option>
						<option value="empty/cover2" {{if $smarty.session.save.$page_name.se_field=="empty/cover2"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_cover2}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_categories}}</option>
						<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_group_field_models}}</option>
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_GROUP_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_GROUP_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_description}}</option>
						<option value="filled/cover1" {{if $smarty.session.save.$page_name.se_field=="filled/cover1"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_cover1}}</option>
						<option value="filled/cover2" {{if $smarty.session.save.$page_name.se_field=="filled/cover2"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_cover2}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_categories}}</option>
						<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_group_field_models}}</option>
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_GROUP_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_GROUP_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_usage">
						<option value="">{{$lang.common.dg_filter_usage}}...</option>
						<option value="used/dvds" {{if $smarty.session.save.$page_name.se_usage=="used/dvds"}}selected{{/if}}>{{$lang.common.dg_filter_usage_dvds}}</option>
						<option value="notused/dvds" {{if $smarty.session.save.$page_name.se_usage=="notused/dvds"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_dvds}}</option>
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
		{{assign var="fields_other_than_thumb" value="0"}}
		{{foreach from=$table_fields|smarty:nodefaults item="field"}}
			{{if $field.is_enabled==1}}
				{{if $field.type!='thumb' && $field.type!='id'}}
					{{assign var="fields_other_than_thumb" value=$fields_other_than_thumb+1}}
				{{/if}}
			{{/if}}
		{{/foreach}}
		<div class="dg {{if $fields_other_than_thumb==0}}thumbs{{/if}}">
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
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
										{{if $item.website_link==''}}
											<span class="js_param">website_link_disable=true</span>
										{{else}}
											<span class="js_param">website_link={{$item.website_link}}</span>
										{{/if}}
										{{if $item.status_id==0}}
											<span class="js_param">deactivate_hide=true</span>
										{{else}}
											<span class="js_param">activate_hide=true</span>
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
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
				{{/if}}
				{{if $can_edit==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
						<span class="js_param">hide=${activate_hide}</span>
						<span class="js_param">icon=action-activate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">hide=${deactivate_hide}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=${website_link}</span>
					<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
					<span class="js_param">disable=${website_link_disable}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=action-open</span>
				</li>
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=10&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-audit</span>
						<span class="js_param">subicon=action-search</span>
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
					{{if $can_edit==1}}
						<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
						<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.type=='sorting' && $field.is_enabled==1}}
								<option value="reorder">{{$lang.common.dg_batch_actions_reorder|replace:"%1%":'${all}'}}</option>
							{{/if}}
						{{/foreach}}
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
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=reorder</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_reorder_confirm|replace:"%1%":'${all}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}