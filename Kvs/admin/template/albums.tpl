{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='mark_deleted' || $smarty.get.action=='change_deleted'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_deleted_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.albums.submenu_option_albums_list}}</a>
				/
				{{if $smarty.get.action=='mark_deleted'}}
					{{$lang.albums.album_mark_deleted}}
				{{else}}
					{{if $smarty.post.title!=''}}
						{{$lang.albums.album_edit_deleted|replace:"%1%":$smarty.post.title}}
					{{else}}
						{{$lang.albums.album_edit_deleted|replace:"%1%":$smarty.post.album_id}}
					{{/if}}
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
			{{if is_array($smarty.post.delete_albums)}}
				<tr>
					<td class="de_label">{{$lang.albums.album_field_delete_count}}</td>
					<td class="de_control">
						{{$smarty.post.delete_albums|@count}}
						{{if count($smarty.post.delete_albums)<=20}}
							{{assign var="delete_titles" value=""}}
							{{foreach from=$smarty.post.delete_albums|smarty:nodefaults item="item" name="deleted"}}
								{{assign var="delete_titles" value="`$delete_titles``$item.album_id` / `$item.title`"}}
								{{if !$smarty.foreach.deleted.last}}
									{{assign var="delete_titles" value="`$delete_titles`, "}}
								{{/if}}
							{{/foreach}}
							({{$delete_titles}})
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.website_link!=''}}
				<tr data-field-name="website_link">
					<td class="de_label">{{$lang.albums.album_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.albums.album_field_delete_reason}}</td>
				<td class="de_control">
					<select name="top_delete_reasons">
						<option value="{{$smarty.post.delete_reason}}">{{$lang.common.select_default_option}}</option>
						{{foreach from=$smarty.post.top_delete_reasons|smarty:nodefaults item="item"}}
							<option value="{{$item.delete_reason}}">{{$item.delete_reason}} ({{$item.total_albums}})</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.albums.album_field_delete_reason_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.album_field_delete_reason_text}}</td>
				<td class="de_control">
					<textarea name="delete_reason" cols="40" rows="3" data-autopopulate-from="top_delete_reasons">{{$smarty.post.delete_reason}}</textarea>
					<span class="de_hint">{{$lang.albums.album_field_delete_reason_text_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action=='mark_deleted'}}
				<tr>
					<td></td>
					<td class="de_control"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="delete_send_email" value="1"/><label>{{$lang.albums.album_field_delete_email}}</label></span></td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.albums.album_field_delete_email_to}}</td>
					<td class="de_control">
						<input type="text" name="delete_send_email_to"/>
					</td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.albums.album_field_delete_email_subject}}</td>
					<td class="de_control">
						<input type="text" name="delete_send_email_subject" value="{{$smarty.session.save.$page_name.delete_send_email_subject}}"/>
						<span class="de_hint">{{$lang.albums.album_field_delete_email_subject_hint}}</span>
					</td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.albums.album_field_delete_email_body}}</td>
					<td class="de_control">
						<textarea name="delete_send_email_body" rows="10" cols="40">{{$smarty.session.save.$page_name.delete_send_email_body}}</textarea>
						<span class="de_hint">{{$lang.albums.album_field_delete_email_body_hint}}</span>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='mark_deleted'}}
			<input type="hidden" name="action" value="mark_deleted_complete"/>
			<input type="hidden" name="delete_id" value="{{$smarty.get.delete_id}}"/>
			<input type="submit" name="save_default" value="{{$lang.albums.album_btn_mark_deleted}}"/>
		{{else}}
			<input type="hidden" name="action" value="change_deleted_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{elseif $smarty.request.action=='change_images'}}

{{if in_array('albums|manage_images',$smarty.session.permissions)}}
	{{assign var="can_manage_images" value=1}}
{{else}}
	{{assign var="can_manage_images" value=0}}
{{/if}}
{{if in_array('albums|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_manage_images==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="album_images">
	<div class="de_main">
		<div class="de_header">
			<h1><a href="{{$page_name}}">{{$lang.albums.submenu_option_albums_list}}</a> / <a href="{{$page_name}}?action=change&amp;item_id={{$smarty.post.album_info.album_id}}">{{if $smarty.post.album_info.title!=''}}{{$lang.albums.album_edit|replace:"%1%":$smarty.post.album_info.title}}{{else}}{{$lang.albums.album_edit|replace:"%1%":$smarty.post.album_info.album_id}}{{/if}}</a> / {{$lang.albums.images_header_mgmt}}</h1>
			<div class="drop">
				<i class="icon icon-action-settings"></i><span>{{$lang.common.dg_actions}}</span>
				<ul>
					{{if $smarty.post.album_info.website_link!=''}}
						<li><a href="{{$smarty.post.album_info.website_link}}"><i class="icon icon-action-open"></i>{{$lang.common.dg_actions_website_link}}</a></li>
					{{/if}}
					<li><a href="?action=album_log&amp;item_id={{$smarty.post.album_info.album_id}}" rel="log"><i class="icon icon-action-log"></i>{{$lang.albums.album_action_view_log}}</a></li>
					{{if in_array('system|background_tasks',$smarty.session.permissions)}}
						<li><a href="log_background_tasks.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id={{$smarty.post.album_info.album_id}}"><i class="icon icon-type-task"></i>{{$lang.albums.album_action_view_tasks}}</a></li>
					{{/if}}
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li><a href="log_audit.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id={{$smarty.post.album_info.album_id}}"><i class="icon icon-type-audit"></i>{{$lang.common.dg_actions_additional_view_audit_log}}</a></li>
					{{/if}}
					{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
						<li><a href="stats_albums.php?no_filter=true&amp;se_group_by=date&amp;se_id={{$smarty.post.album_info.album_id}}"><i class="icon icon-type-traffic"></i>{{$lang.albums.album_action_view_stats}}</a></li>
					{{/if}}
					<li><a href="?action=album_validate&amp;item_id={{$smarty.post.album_info.album_id}}" rel="log"><i class="icon icon-type-system"></i>{{$lang.albums.album_action_validate}}</a></li>
				</ul>
			</div>
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
			<td class="de_separator" colspan="2"><h2>{{$lang.albums.images_divider_gui_control}}</h2></td>
			<tr>
				<td class="de_label">{{$lang.albums.images_mgmt_field_image_preview}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.images_mgmt_field_image_preview}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.post.preview_image!=''}}
								<span class="js_param">preview_url={{$smarty.post.preview_image}}?rnd={{$smarty.now}}/</span>
							{{/if}}
						</div>
						<input type="text" name="preview" maxlength="100" value="{{if $smarty.post.preview_image!=''}}preview.jpg{{/if}}"/>
						<input type="hidden" name="preview_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.albums.images_mgmt_field_image_preview_hint}}</span>
				</td>
			</tr>
			{{foreach item="item" from=$smarty.post.zip_files|smarty:nodefaults}}
				{{if $item.size=='source'}}
					<tr data-field-name="source_zip_file">
						<td class="de_label">{{$lang.albums.images_mgmt_field_source_zip_file}}</td>
						<td class="de_control">
							<span>
								<a href="{{$item.file_url}}">{{$item.file_name}}</a> ({{$item.file_size_string}})
							</span>
						</td>
					</tr>
				{{else}}
					<tr data-field-name="{{$item.size}}_zip_file">
						<td class="de_label">{{$lang.albums.images_mgmt_field_format_zip_file|replace:"%1%":$item.size}}</td>
						<td class="de_control">
							<span>
								<a href="{{$item.file_url}}">{{$item.file_name}}</a> ({{$item.file_size_string}})
							</span>
						</td>
					</tr>
				{{/if}}
			{{/foreach}}
			<tr>
				<td class="de_label">{{$lang.albums.images_mgmt_field_display}}</td>
				<td class="de_control">
					<select name="format_id" class="de_switcher" data-switcher-params="action=change_images;item_id={{$smarty.post.album_info.album_id}}">
						<option value="sources" {{if $smarty.post.format_id=='sources'}}selected{{/if}}>{{$lang.albums.images_mgmt_field_display_format_sources}}</option>
						{{foreach item="item" from=$smarty.post.list_formats_main|smarty:nodefaults}}
							<option value="{{$item.format_album_id}}" {{if $smarty.post.format_id==$item.format_album_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.albums.images_mgmt_field_display_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2">
					<h2>
						{{if $smarty.post.format_id=='sources'}}
							{{$lang.albums.images_divider_sources}}
						{{else}}
							{{$lang.albums.images_divider_images|replace:"%1%":$smarty.post.format_info.size}}
						{{/if}}
					</h2>
				</td>
			</tr>
			{{if $can_manage_images==1}}
				<tr>
					<td class="de_label">{{$lang.albums.images_mgmt_field_new_images}}</td>
					<td class="de_control">
						<div class="de_fu">
							{{if $smarty.post.has_background_task==1}}
								<input type="text" maxlength="100" value="{{$lang.albums.images_mgmt_field_new_images_in_progress}}"/>
							{{else}}
								<div class="js_params">
									<span class="js_param">title={{$lang.albums.images_mgmt_field_new_images}}</span>
									<span class="js_param">accept={{$config.image_allowed_ext}},zip</span>
									<span class="js_param">multiple=true</span>
								</div>
								<input type="text" name="new_images" maxlength="100"/>
								<input type="hidden" name="new_images_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
						</div>
						<span class="de_hint">{{$lang.albums.images_mgmt_field_new_images_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.album_info.photos_amount>0}}
				<tr>
					<td class="de_control" colspan="2">
						<div class="de_img_list">
							<div class="de_img_list_header">
								<span>
									<span class="label">{{$lang.albums.images_mgmt_field_display_mode}}:</span>
									<select class="de_switcher de_img_list_display_mode">
										<option value="full">{{$lang.albums.images_mgmt_field_display_mode_full}}</option>
										<option value="basic">{{$lang.albums.images_mgmt_field_display_mode_basic}}</option>
									</select>
								</span>
								<span>
									<span class="label">{{$lang.albums.images_mgmt_field_click_mode}}:</span>
									<select class="de_switcher de_img_list_click_mode">
										<option value="viewer">{{$lang.albums.images_mgmt_field_click_mode_viewer}}</option>
										{{if $can_manage_images==1}}
											<option value="select">{{$lang.albums.images_mgmt_field_click_mode_select}}</option>
											<option value="main">{{$lang.albums.images_mgmt_field_click_mode_main}}</option>
										{{/if}}
									</select>
								</span>
								{{if $can_manage_images==1}}
									<span class="de_lv_pair"><input type="checkbox" class="de_switcher de_img_list_delete_all" autocomplete="off"/><label>{{$lang.albums.images_mgmt_field_select_all}}</label></span>
									<span class="de_lv_pair"><input type="checkbox" class="de_switcher de_img_list_do_not_fade" autocomplete="off" value="1"/><label>{{$lang.albums.images_mgmt_field_select_do_not_fade}}</label></span>
								{{/if}}
							</div>
							<div class="de_img_list_main">
								{{assign var="pos" value=0}}
								{{section name="images" start="0" step="1" loop=$smarty.post.album_info.photos_amount}}
									<div class="de_img_list_item {{if $smarty.post.list_images[$pos].image_id==$smarty.post.album_info.main_photo_id}}main{{/if}}">
										<a class="de_img_list_thumb" href="{{$smarty.post.list_images[$pos].source_url}}?rnd={{$smarty.now}}">
											<img src="{{$smarty.post.list_images[$pos].file_url}}?rnd={{$smarty.now}}" alt="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}" title="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}"/>
											<i>{{$smarty.post.list_images[$pos].image_id}}.jpg</i>
										</a>
										{{if $can_manage_images==1}}
											<div class="de_img_list_options">
												<textarea name="title_{{$smarty.post.list_images[$pos].image_id}}" rows="4" cols="15">{{$smarty.post.list_images[$pos].title}}</textarea>
											</div>
											<div class="de_img_list_options">
												<div class="de_fu">
													<div class="js_params">
														<span class="js_param">title={{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}</span>
														<span class="js_param">accept={{$config.image_allowed_ext}}</span>
													</div>
													<input type="text" maxlength="100" name="file_{{$smarty.post.list_images[$pos].image_id}}"/>
													<input type="hidden" name="file_{{$smarty.post.list_images[$pos].image_id}}_hash"/>
													<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
												</div>
											</div>
											<div class="de_img_list_options basic">
												<span class="de_lv_pair"><input type="radio" name="main" value="{{$smarty.post.list_images[$pos].image_id}}" {{if $smarty.post.list_images[$pos].image_id==$smarty.post.album_info.main_photo_id}}checked{{/if}}/><label>{{$lang.albums.images_mgmt_field_main}}</label></span>
												<span class="de_lv_pair"><input type="checkbox" name="delete[]" value="{{$smarty.post.list_images[$pos].image_id}}" autocomplete="off"/><label>{{$lang.albums.images_mgmt_field_delete}}</label></span>
											</div>
										{{/if}}
										{{assign var="pos" value=$pos+1}}
									</div>
								{{/section}}
							</div>
						</div>
					</td>
				</tr>
				{{if in_array('albums|edit_status',$smarty.session.permissions) || in_array('albums|edit_admin_flag',$smarty.session.permissions)}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.albums.images_divider_album_data}}</h2></td>
					</tr>
					{{if in_array('albums|edit_status',$smarty.session.permissions)}}
						<tr>
							<td class="de_label">{{$lang.albums.images_mgmt_field_status}}</td>
							<td class="de_control">
								<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.album_info.status_id=='1'}}checked{{/if}} {{if !in_array('albums|edit_status',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.images_mgmt_field_status_active}}</label></span>
							</td>
						</tr>
					{{/if}}
					{{if in_array('albums|edit_admin_flag',$smarty.session.permissions)}}
						<tr>
							<td class="de_label">{{$lang.albums.images_mgmt_field_admin_flag}}</td>
							<td class="de_control">
								<select name="admin_flag_id" {{if !in_array('albums|edit_admin_flag',$smarty.session.permissions)}}disabled{{/if}}>
									<option value="0" {{if 0==$smarty.post.album_info.admin_flag_id}}selected{{/if}}>{{$lang.albums.images_mgmt_field_admin_flag_reset}}</option>
									{{foreach name="data" item="item" from=$list_flags_admins|smarty:nodefaults}}
										<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.album_info.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								</select>
							</td>
						</tr>
					{{/if}}
				{{/if}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="process_images"/>
		<input type="hidden" name="item_id" value="{{$smarty.post.album_info.album_id}}"/>
		{{if $smarty.session.save.options.default_save_button==1}}
			<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{else}}
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
		{{if $can_delete}}
			<span class="de_separated_group">
				<input type="submit" name="delete_and_edit" class="destructive" value="{{$lang.common.btn_delete_and_edit_next}}" data-confirm="{{$lang.common.btn_delete_and_edit_next_confirm}}"/>
			</span>
		{{/if}}
	</div>
</form>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('albums|edit_all',$smarty.session.permissions) || (in_array('albums|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}
{{if in_array('albums|edit_title',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_title" value=1}}
{{else}}
	{{assign var="can_edit_title" value=0}}
{{/if}}
{{if in_array('albums|edit_dir',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_dir" value=1}}
{{else}}
	{{assign var="can_edit_dir" value=0}}
{{/if}}
{{if in_array('albums|edit_description',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_description" value=1}}
{{else}}
	{{assign var="can_edit_description" value=0}}
{{/if}}
{{if in_array('albums|edit_post_date',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_post_date" value=1}}
{{else}}
	{{assign var="can_edit_post_date" value=0}}
{{/if}}
{{if in_array('albums|edit_user',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_user" value=1}}
{{else}}
	{{assign var="can_edit_user" value=0}}
{{/if}}
{{if in_array('albums|edit_status',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
{{/if}}
{{if in_array('albums|edit_type',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_type" value=1}}
{{else}}
	{{assign var="can_edit_type" value=0}}
{{/if}}
{{if in_array('albums|edit_access_level',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_access_level" value=1}}
{{else}}
	{{assign var="can_edit_access_level" value=0}}
{{/if}}
{{if in_array('albums|edit_tokens',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_tokens" value=1}}
{{else}}
	{{assign var="can_edit_tokens" value=0}}
{{/if}}
{{if in_array('albums|edit_content_source',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_content_source" value=1}}
{{else}}
	{{assign var="can_edit_content_source" value=0}}
{{/if}}
{{if in_array('albums|edit_categories',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_categories" value=1}}
{{else}}
	{{assign var="can_edit_categories" value=0}}
{{/if}}
{{if in_array('albums|edit_tags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_tags" value=1}}
{{else}}
	{{assign var="can_edit_tags" value=0}}
{{/if}}
{{if in_array('albums|edit_models',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_models" value=1}}
{{else}}
	{{assign var="can_edit_models" value=0}}
{{/if}}
{{if in_array('albums|edit_flags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_flags" value=1}}
{{else}}
	{{assign var="can_edit_flags" value=0}}
{{/if}}
{{if in_array('albums|edit_custom',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_custom" value=1}}
{{else}}
	{{assign var="can_edit_custom" value=0}}
{{/if}}
{{if in_array('albums|edit_admin_flag',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_admin_flag" value=1}}
{{else}}
	{{assign var="can_edit_admin_flag" value=0}}
{{/if}}
{{if in_array('albums|edit_is_locked',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_is_locked" value=1}}
{{else}}
	{{assign var="can_edit_is_locked" value=0}}
{{/if}}
{{if in_array('albums|edit_storage',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_storage" value=1}}
{{else}}
	{{assign var="can_edit_storage" value=0}}
{{/if}}
{{if in_array('albums|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}

{{assign var="can_edit_translations" value=0}}
{{foreach name="data" item="item" from=$list_languages|smarty:nodefaults}}
	{{assign var="permission_id" value="localization|`$item.code`"}}
	{{if in_array($permission_id,$smarty.session.permissions)}}
		{{assign var="can_edit_translations" value=1}}
	{{/if}}
{{/foreach}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="album_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.albums.submenu_option_albums_list}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.albums.album_add}}
				{{else}}
					{{if $smarty.post.title!=''}}
						{{$lang.albums.album_edit|replace:"%1%":$smarty.post.title}}
					{{else}}
						{{$lang.albums.album_edit|replace:"%1%":$smarty.post.album_id}}
					{{/if}}
				{{/if}}
			</h1>
			{{if $smarty.get.action=='change'}}
				<div class="drop">
					<i class="icon icon-action-settings"></i><span>{{$lang.common.dg_actions}}</span>
					<ul>
						{{if $smarty.post.website_link!=''}}
							<li><a href="{{$smarty.post.website_link}}"><i class="icon icon-action-open"></i>{{$lang.common.dg_actions_website_link}}</a></li>
						{{/if}}
						<li><a href="?action=album_log&amp;item_id={{$smarty.post.album_id}}" rel="log"><i class="icon icon-action-log"></i>{{$lang.albums.album_action_view_log}}</a></li>
						{{if in_array('system|background_tasks',$smarty.session.permissions)}}
							<li><a href="log_background_tasks.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id={{$smarty.post.album_id}}"><i class="icon icon-type-task"></i>{{$lang.albums.album_action_view_tasks}}</a></li>
						{{/if}}
						{{if in_array('system|administration',$smarty.session.permissions)}}
							<li><a href="log_audit.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id={{$smarty.post.album_id}}"><i class="icon icon-type-audit"></i>{{$lang.common.dg_actions_additional_view_audit_log}}</a></li>
						{{/if}}
						{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
							<li><a href="stats_albums.php?no_filter=true&amp;se_group_by=date&amp;se_id={{$smarty.post.album_id}}"><i class="icon icon-type-traffic"></i>{{$lang.albums.album_action_view_stats}}</a></li>
						{{/if}}
						<li><a href="?action=album_validate&amp;item_id={{$smarty.post.album_info.album_id}}" rel="log"><i class="icon icon-type-system"></i>{{$lang.albums.album_action_validate}}</a></li>
					</ul>
				</div>
			{{/if}}
		</div>
		<table class="de_editor">
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
			{{if $smarty.post.is_review_needed==1 && $can_edit_status==1}}
				<tr>
					<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_review}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.albums.album_divider_review_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.album_field_review_action}}</td>
					<td class="de_control" colspan="3">
						<table class="control_group">
							<tr>
								<td class="de_vis_sw_select">
									<select name="is_reviewed" class="preserve_editing">
										<option value="0">{{$lang.albums.album_field_review_action_none}}</option>
										<option value="1">{{$lang.albums.album_field_review_action_approve}}</option>
										<option value="2" {{if $can_delete==0}}disabled{{/if}}>{{$lang.albums.album_field_review_action_delete}}</option>
									</select>
								</td>
							</tr>
							{{if $smarty.post.status_id==0}}
								<tr class="is_reviewed_1">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_activate" value="1" class="preserve_editing"/><label>{{$lang.albums.album_field_review_action_activate}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_disable_user" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('users|edit_all',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.album_field_review_action_disable_user|replace:"%1%":$smarty.post.user}}</label></span>
									</td>
								</tr>
								{{if $smarty.post.user_domain!='' && $smarty.post.user_domain_blocked!=1}}
									<tr class="is_reviewed_2">
										<td>
											<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_domain" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.album_field_review_action_block_domain|replace:"%1%":$smarty.post.user_domain}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
							{{if $smarty.post.ip_mask!='0.0.0.*' && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_mask" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.album_field_review_action_block_mask|replace:"%1%":$smarty.post.ip_mask}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.ip!='0.0.0.0' && $smarty.post.ip_blocked!=1 && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_ip" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.album_field_review_action_block_ip|replace:"%1%":$smarty.post.ip}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								{{if $smarty.post.other_albums_need_review>0}}
									<tr class="is_reviewed_2">
										<td>
											{{assign var="max_delete_on_review" value=$config.max_delete_on_review|intval}}
											{{if $max_delete_on_review==0}}
												{{assign var="max_delete_on_review" value=30}}
											{{/if}}
											<span class="de_lv_pair"><input type="checkbox" name="is_delete_all_albums_from_user" value="1" class="is_reviewed_delete preserve_editing" {{if $can_delete!=1 || $smarty.post.other_albums_need_review>$max_delete_on_review}}disabled{{/if}}/><label>{{$lang.albums.album_field_review_action_delete_other|replace:"%1%":$smarty.post.other_albums_need_review}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
						</table>
					</td>
				</tr>
				{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
					<tr>
						<td class="de_label">{{$lang.albums.album_field_af_upload_zone}}</td>
						<td class="de_control">
							<select name="af_upload_zone" class="preserve_editing">
								<option value="0" {{if $smarty.post.af_upload_zone==0}}selected{{/if}}>{{$lang.albums.album_field_af_upload_zone_site}}</option>
								<option value="1" {{if $smarty.post.af_upload_zone==1}}selected{{/if}}>{{$lang.albums.album_field_af_upload_zone_memberarea}}</option>
							</select>
							<span class="de_hint">{{$lang.albums.album_field_af_upload_zone_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label status_id_on">{{$lang.albums.album_field_title}}</td>
				<td class="de_control" colspan="3">
					<input type="text" name="title" maxlength="255" class="{{if $can_edit_title==1}}preserve_editing{{/if}}" value="{{$smarty.post.title}}"/>
					<span class="de_hint">{{$lang.albums.album_field_title_hint}}, <span class="de_str_len_value"></span></span>
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.albums.album_field_directory}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="dir" maxlength="255" class="{{if $can_edit_dir==1}}preserve_editing{{/if}}" value="{{$smarty.post.dir}}" {{if $options.ALBUM_REGENERATE_DIRECTORIES==1}}readonly{{/if}}/>
						{{if $options.ALBUM_REGENERATE_DIRECTORIES==1}}
							<span class="de_hint">{{$lang.albums.album_field_directory_hint2|replace:"%1%":$lang.albums.album_field_title}}</span>
						{{else}}
							<span class="de_hint">{{$lang.albums.album_field_directory_hint|replace:"%1%":$lang.albums.album_field_title}}</span>
						{{/if}}
					</td>
				</tr>
				{{if $smarty.post.website_link!=''}}
					<tr data-field-name="website_link" {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$lang.albums.album_field_website_link}}</td>
						<td class="de_control" colspan="3">
							<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.albums.album_field_description}}</td>
				<td class="de_control" colspan="3">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_albums=='1'}}tinymce{{/if}} {{if $can_edit_description==1}}preserve_editing{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.albums.album_field_content_source}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=content_sources</span>
							{{if in_array('content_sources|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('content_sources|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.content_source_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="content_source" maxlength="255" class="{{if $can_edit_content_source==1}}preserve_editing{{/if}}" value="{{$smarty.post.content_source.title}}"/>
					</div>
					<span class="de_hint">{{$lang.albums.album_field_content_source_hint}}</span>
				</td>
				<td class="de_label de_required">{{$lang.albums.album_field_user}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="user" maxlength="255" class="{{if $can_edit_user==1}}preserve_editing{{/if}}" value="{{$smarty.post.user}}"/>
					</div>
					<span class="de_hint">{{$lang.albums.album_field_user_hint}}</span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label de_required">{{$lang.albums.album_field_post_date}}</td>
				<td class="de_control nowrap">
					{{if $config.relative_post_dates=='true'}}
						<table class="control_group de_vis_sw_radio">
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option!='1'}}checked{{/if}}/><label>{{$lang.albums.album_field_post_date_fixed}}</label></span>
									<span class="calendar">
										<span class="js_params">
											<span class="js_param">type=datetime</span>
											{{if $can_edit_post_date!=1}}
												<span class="js_param">forbid_edit=true</span>
											{{/if}}
										</span>
										<input class="post_date_option_fixed" type="text" name="post_date" value="{{$smarty.post.post_date}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
									<span class="de_hint">{{$lang.albums.album_field_post_date_hint1}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option=='1'}}checked{{/if}}/><label>{{$lang.albums.album_field_post_date_relative}}</label></span>
									<span>
										<input type="text" name="relative_post_date" size="4" maxlength="5" class="post_date_option_relative {{if $can_edit_post_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.relative_post_date}}"/>
										{{$lang.albums.album_field_post_date_relative_days}}
									</span>
									<span class="de_hint">{{$lang.albums.album_field_post_date_hint2}}</span>
								</td>
							</tr>
						</table>
					{{else}}
						<span>
							<span class="calendar">
								<span class="js_params">
									<span class="js_param">type=datetime</span>
									{{if $can_edit_post_date!=1}}
										<span class="js_param">forbid_edit=true</span>
									{{/if}}
								</span>
								<input type="text" name="post_date" value="{{$smarty.post.post_date}}" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} placeholder="{{$lang.common.select_default_option}}">
							</span>
							<input id="button_post_date_now" type="button" value="{{$lang.javascript.now}}" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}}>
						</span>
						<span class="de_hint">{{$lang.albums.album_field_post_date_hint}}</span>
					{{/if}}
				</td>
				<td class="de_label de_required">{{$lang.albums.album_field_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="is_private" {{if $can_edit_type==1}}class="preserve_editing"{{/if}}>
						<option value="0" {{if $smarty.post.is_private=='0'}}selected{{/if}}>{{$lang.albums.album_field_type_public}}</option>
						<option value="1" {{if $smarty.post.is_private=='1'}}selected{{/if}}>{{$lang.albums.album_field_type_private}}</option>
						<option value="2" {{if $smarty.post.is_private=='2'}}selected{{/if}}>{{$lang.albums.album_field_type_premium}}</option>
					</select>
					<span class="de_hint is_private_0">
						{{if $options.PUBLIC_ALBUMS_ACCESS==0}}{{$lang.albums.album_field_type_hint_all}}{{elseif $options.PUBLIC_ALBUMS_ACCESS==1}}{{$lang.albums.album_field_type_hint_members}}{{elseif $options.PUBLIC_ALBUMS_ACCESS==2}}{{$lang.albums.album_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.albums.album_field_type_hint_configure}}</a>
						{{/if}}
					</span>
					<span class="de_hint is_private_1">
						{{if $options.PRIVATE_ALBUMS_ACCESS==3}}{{$lang.albums.album_field_type_hint_all}}{{elseif $options.PRIVATE_ALBUMS_ACCESS==0}}{{$lang.albums.album_field_type_hint_members}}{{elseif $options.PRIVATE_ALBUMS_ACCESS==1}}{{$lang.albums.album_field_type_hint_friends}}{{elseif $options.PRIVATE_ALBUMS_ACCESS==2}}{{$lang.albums.album_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.albums.album_field_type_hint_configure}}</a>
						{{/if}}
					</span>
					<span class="de_hint is_private_2">
						{{if $options.PREMIUM_ALBUMS_ACCESS==0}}{{$lang.albums.album_field_type_hint_all}}{{elseif $options.PREMIUM_ALBUMS_ACCESS==1}}{{$lang.albums.album_field_type_hint_members}}{{elseif $options.PREMIUM_ALBUMS_ACCESS==2}}{{$lang.albums.album_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.albums.album_field_type_hint_configure}}</a>
						{{/if}}
					</span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.albums.album_field_server_group}}</td>
				<td class="de_control">
					<div class="nowrap de_vis_sw_button">
						<select name="server_group_id" class="{{if $can_edit_storage==1}}preserve_editing{{/if}} change_storage_group" {{if $smarty.get.action=='change'}}disabled{{/if}}>
							{{if $smarty.get.action=='add_new'}}
								<option value="">{{$lang.albums.album_field_server_group_auto}}</option>
							{{/if}}
							{{foreach name="data" item="item" from=$list_server_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $item.group_id==$smarty.post.server_group_id}}selected{{/if}}>{{$item.title}} ({{$lang.albums.album_field_server_group_free_space|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
							{{/foreach}}
						</select>
						{{if $smarty.get.action!='add_new' && $can_edit_storage==1}}
							<input name="change_storage_group" type="button" value="{{$lang.albums.album_field_server_group_change}}" {{if $smarty.post.server_group_migration_not_finished>0}}disabled{{/if}} data-confirm="{{$lang.albums.album_field_server_group_change_warning}}" data-destructive="false"/>
						{{/if}}
						<span class="de_hint">{{$lang.albums.album_field_server_group_hint}}</span>
					</div>
				</td>
				<td class="de_label">{{$lang.albums.album_field_access_level}}</td>
				<td class="de_control">
					<select name="access_level_id" {{if $can_edit_access_level==1}}class="preserve_editing"{{/if}}>
						<option value="0" {{if $smarty.post.access_level_id==0}}selected{{/if}}>{{$lang.albums.album_field_access_level_inherit}}</option>
						<option value="1" {{if $smarty.post.access_level_id==1}}selected{{/if}}>{{$lang.albums.album_field_access_level_all}}</option>
						<option value="2" {{if $smarty.post.access_level_id==2}}selected{{/if}}>{{$lang.albums.album_field_access_level_members}}</option>
						<option value="3" {{if $smarty.post.access_level_id==3}}selected{{/if}}>{{$lang.albums.album_field_access_level_premium}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.album_field_access_level_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.album_field_admin_flag}}</td>
				<td class="de_control">
					<select name="admin_flag_id" {{if $can_edit_admin_flag==1}}class="preserve_editing"{{/if}}>
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.albums.album_field_admin_flag_hint}}</span>
				</td>
				<td class="de_label">{{$lang.albums.album_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}} {{if $can_edit_status==1}}class="preserve_editing"{{/if}}/><label>{{$lang.albums.album_field_status_active}}</label></span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.albums.album_field_lock_website}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked{{/if}} {{if $can_edit_is_locked==1}}class="preserve_editing"{{/if}}/><label>{{$lang.albums.album_field_lock_website_locked}}</label></span>
						<span class="de_hint">{{$lang.albums.album_field_lock_website_hint}}</span>
					</td>
					<td class="de_label">{{$lang.albums.album_field_ip}}</td>
					<td class="de_control">
						<span>
							{{if $config.safe_mode!='true'}}
								{{$smarty.post.ip}}
							{{else}}
								0.0.0.0
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=2 && (($smarty.post.is_private==2 && $memberzone_data.ENABLE_TOKENS_PREMIUM_ALBUM==1) || ($smarty.post.is_private==1 && $memberzone_data.ENABLE_TOKENS_PRIVATE_ALBUM==1) || ($smarty.post.is_private==0 && $memberzone_data.ENABLE_TOKENS_PUBLIC_ALBUM==1))}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.albums.album_field_tokens_cost}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="tokens_required" maxlength="10" size="10" {{if $can_edit_tokens==1}}class="preserve_editing"{{/if}} value="{{$smarty.post.tokens_required}}"/>
						<span class="de_hint is_private_0">{{$lang.albums.album_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PUBLIC_ALBUM}}</span>
						<span class="de_hint is_private_1">{{$lang.albums.album_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PRIVATE_ALBUM}}</span>
						<span class="de_hint is_private_2">{{$lang.albums.album_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PREMIUM_ALBUM}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.gallery_url!=''}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.albums.album_field_gallery_url}}</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.gallery_url}}">{{$smarty.post.gallery_url}}</a>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.connected_video_title!=''}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.albums.album_field_connected_video}}</td>
					<td class="de_control" colspan="3">
						{{if in_array('videos|view',$smarty.session.permissions)}}
							<a href="videos.php?action=change&amp;item_id={{$smarty.post.connected_video_id}}">{{$smarty.post.connected_video_title}}</a>
						{{else}}
							<span>{{$smarty.post.connected_video_title}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action!='add_new'}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.albums.album_field_images}}</td>
					<td class="de_control" colspan="3">
						<a href="albums.php?action=change_images&amp;item_id={{$smarty.get.item_id}}">{{$lang.albums.album_action_manage_images}}</a>
						<span class="de_hint">{{$lang.albums.album_field_images_hint2}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='change' && in_array('localization|view',$smarty.session.permissions) && $smarty.session.save.options.album_edit_show_translations=='1'}}
				{{assign var="header_output" value="1"}}
				{{foreach name="data" item="item" from=$list_languages|smarty:nodefaults}}
					{{assign var="permission_id" value="localization|`$item.code`"}}
					{{assign var="title_selector" value="title_`$item.code`"}}
					{{assign var="dir_selector" value="dir_`$item.code`"}}
					{{assign var="desc_selector" value="description_`$item.code`"}}
					{{if in_array($permission_id,$smarty.session.permissions)}}
						{{if $header_output==1}}
							<tr>
								<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_localization}}</h2></td>
							</tr>
							{{assign var="header_output" value="0"}}
						{{/if}}
						<tr>
							<td class="de_label">{{$lang.common.title_translation|replace:"%1%":$item.title}}</td>
							<td class="de_control" colspan="3">
								<input type="text" name="{{$title_selector}}" maxlength="255" class="preserve_editing" value="{{$smarty.post.$title_selector}}"/>
								<span class="de_hint"><span class="de_str_len_value"></span></span>
							</td>
						</tr>
						{{if $item.is_directories_localize==1}}
							<tr>
								<td class="de_label">{{$lang.common.directory_translation|replace:"%1%":$item.title}}</td>
								<td class="de_control" colspan="3">
									<input type="text" name="{{$dir_selector}}" maxlength="255" class="preserve_editing" value="{{$smarty.post.$dir_selector}}"/>
									<span class="de_hint">{{$lang.albums.album_field_directory_hint_translation|replace:"%1%":$item.title|replace:"%2%":$lang.common.title_translation|replace:"%1%":$item.title}}</span>
								</td>
							</tr>
						{{/if}}
						{{if $item.translation_scope_albums==0}}
							<tr>
								<td class="de_label">{{$lang.common.description_translation|replace:"%1%":$item.title}}</td>
								<td class="de_control" colspan="3">
									<textarea name="{{$desc_selector}}" class="preserve_editing {{if $smarty.session.userdata.is_wysiwyg_enabled_albums=='1'}}tinymce{{/if}}" cols="40" rows="3">{{$smarty.post.$desc_selector}}</textarea>
									<span class="de_hint"><span class="de_str_len_value"></span></span>
								</td>
							</tr>
						{{/if}}
					{{/if}}
				{{/foreach}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.album_field_tags}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.album_field_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=simple</span>
							{{if $can_edit_tags!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
							{{if in_array('tags|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.albums.album_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_tags==1}}
							<div class="controls">
								<input type="text" name="new_tag" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.albums.album_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.album_field_categories}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.album_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.albums.album_field_categories_empty}}</span>
							{{if $can_edit_categories!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_categories==1}}
							<div class="controls">
								<input type="text" name="new_category" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.albums.album_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.album_field_models}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.album_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=model_ids[]</span>
							{{if in_array('models|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.albums.album_field_models_empty}}</span>
							{{if $can_edit_models!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
							<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_models==1}}
							<div class="controls">
								<input type="text" name="new_model" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.albums.album_field_models_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.albums.album_field_flags}}</td>
					<td class="de_control" colspan="3">
						<div class="de_deletable_list">
							<div class="js_params">
								<span class="js_param">submit_name=delete_flags[]</span>
								<span class="js_param">empty_message={{$lang.albums.album_field_flags_empty}}</span>
								{{if $can_edit_flags!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
							</div>
							<div class="list">
								{{if count($smarty.post.flags)>0}}
									{{foreach name="data" item="item" from=$smarty.post.flags|smarty:nodefaults}}
										<span class="item"><a data-item-id="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}})</a>{{if !$smarty.foreach.data.last}}<span class="separator">,</span>{{/if}}</span>
									{{/foreach}}
								{{else}}
									{{$lang.albums.album_field_flags_empty}}
								{{/if}}
							</div>
						</div>
					</td>
				</tr>
				{{if count($list_post_process_plugins)>0}}
					<tr>
						<td class="de_label">{{$lang.albums.album_field_categorization_plugins}}</td>
						<td class="de_control">
							<table class="control_group">
								{{foreach item="item" from=$list_post_process_plugins|smarty:nodefaults}}
									<tr><td>
										<span class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}" class="preserve_editing"/> <label>{{$lang.albums.album_field_categorization_plugins_run|replace:"%1%":$item.title}}</label></span>
									</td></tr>
								{{/foreach}}
							</table>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.get.action=='add_new'}}
				<tr>
					<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_content}}</h2></td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.albums.album_field_images}}</td>
					<td class="de_control" colspan="3">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.album_field_images}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}},zip</span>
								<span class="js_param">multiple=true</span>
							</div>
							<input type="text" name="images" maxlength="100"/>
							<input type="hidden" name="images_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.albums.album_field_images_hint1}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.album_field_image_preview}}</td>
					<td class="de_control" colspan="3">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.album_field_image_preview}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							</div>
							<input type="text" name="preview" maxlength="100"/>
							<input type="hidden" name="preview_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.albums.album_field_image_preview_hint}}</span>
					</td>
				</tr>
			{{elseif count($smarty.post.list_images)>0}}
				<tr>
					<td class="de_separator" colspan="4">
						<h2>
							{{assign var="showing_images" value=$smarty.post.list_images|@count}}
							{{$lang.albums.album_divider_images|replace:"%1%":$showing_images|replace:"%2%":$smarty.post.photos_amount}}
						</h2>
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="4">
						<a href="albums.php?action=change_images&amp;item_id={{$smarty.get.item_id}}">{{$lang.albums.album_action_manage_images}}</a>
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="4">
						<div class="de_img_list">
							<div class="de_img_list_main">
								{{assign var="pos" value=0}}
								{{section name="images" start="0" step="1" loop=$smarty.post.list_images|count}}
									<div class="de_img_list_item">
										<a class="de_img_list_thumb" href="{{$config.project_url}}/get_image/{{$smarty.post.server_group_id}}/{{$smarty.post.list_images[$pos].source_path}}/?rnd={{$smarty.now}}">
											<img src="{{$config.project_url}}/get_image/{{$smarty.post.server_group_id}}/{{$smarty.post.list_images[$pos].file_path}}/?rnd={{$smarty.now}}" alt="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}" title="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}"/>
											<i>{{$smarty.post.list_images[$pos].image_id}}.jpg</i>
										</a>
									</div>
									{{assign var="pos" value=$pos+1}}
								{{/section}}
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_ALBUM_FIELD_1==1 || $options.ENABLE_ALBUM_FIELD_2==1 || $options.ENABLE_ALBUM_FIELD_3==1 || $options.ENABLE_ALBUM_FLAG_1==1 || $options.ENABLE_ALBUM_FLAG_2==1 || $options.ENABLE_ALBUM_FLAG_3==1}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_separator" colspan="4"><h2>{{$lang.albums.album_divider_customization}}</h2></td>
				</tr>
				{{if $options.ENABLE_ALBUM_FIELD_1==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.ALBUM_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom1" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_albums=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_ALBUM_FIELD_2==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.ALBUM_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom2" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_albums=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_ALBUM_FIELD_3==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.ALBUM_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom3" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_albums=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_ALBUM_FLAG_1==1 || $options.ENABLE_ALBUM_FLAG_2==1 || $options.ENABLE_ALBUM_FLAG_3==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.album_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$lang.albums.album_field_custom_flags}}</td>
						<td class="de_control" colspan="3">
							{{if $options.ENABLE_ALBUM_FLAG_1==1}}
								<input type="hidden" name="af_custom1" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="af_custom1" value="{{$smarty.post.af_custom1|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom1>0}}checked{{/if}}/><label>{{$options.ALBUM_FLAG_1_NAME}} {{if $smarty.post.af_custom1>1}}({{$smarty.post.af_custom1}}){{/if}}</label></span>
							{{/if}}
							{{if $options.ENABLE_ALBUM_FLAG_2==1}}
								<input type="hidden" name="af_custom2" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="af_custom2" value="{{$smarty.post.af_custom2|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom2>0}}checked{{/if}}/><label>{{$options.ALBUM_FLAG_2_NAME}} {{if $smarty.post.af_custom2>1}}({{$smarty.post.af_custom2}}){{/if}}</label></span>
							{{/if}}
							{{if $options.ENABLE_ALBUM_FLAG_3==1}}
								<input type="hidden" name="af_custom3" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="af_custom3" value="{{$smarty.post.af_custom3|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom3>0}}checked{{/if}}/><label>{{$options.ALBUM_FLAG_3_NAME}} {{if $smarty.post.af_custom3>1}}({{$smarty.post.af_custom3}}){{/if}}</label></span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
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
			{{if $can_delete}}
				<span class="de_separated_group">
					<input type="submit" name="delete_and_edit" class="destructive" value="{{$lang.common.btn_delete_and_edit_next}}" data-confirm="{{$lang.common.btn_delete_and_edit_next_confirm}}"/>
				</span>
			{{/if}}
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('albums|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('albums|edit_status',$smarty.session.permissions)}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
{{/if}}
{{if in_array('albums|mass_edit',$smarty.session.permissions)}}
	{{assign var="can_mass_edit" value=1}}
{{else}}
	{{assign var="can_mass_edit" value=0}}
{{/if}}
{{if in_array('albums|export',$smarty.session.permissions)}}
	{{assign var="can_export" value=1}}
{{else}}
	{{assign var="can_export" value=0}}
{{/if}}
{{if in_array('system|background_tasks',$smarty.session.permissions)}}
	{{assign var="can_restart" value=1}}
{{else}}
	{{assign var="can_restart" value=0}}
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
				{{if $smarty.session.save.$page_name.se_ids!=''}}
					<div class="dgf_filter">
						<input type="text" name="se_ids" value="{{$smarty.session.save.$page_name.se_ids}}" placeholder="{{$lang.albums.album_field_ids}}..."/>
					</div>
				{{elseif $smarty.session.save.$page_name.se_file_ids!=''}}
					<select name="se_file_ids">
						<option value="">{{$lang.albums.album_field_ids}}...</option>
						<option value="{{$smarty.session.save.$page_name.se_file_ids}}" selected>{{$smarty.session.save.$page_name.se_file_ids}}</option>
					</select>
				{{/if}}
				<div class="dgf_filter">
					<select name="se_status_id">
						<option value="">{{$lang.albums.album_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.albums.album_field_status_disabled}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.albums.album_field_status_active}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected{{/if}}>{{$lang.albums.album_field_status_error}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected{{/if}}>{{$lang.albums.album_field_status_in_process}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected{{/if}}>{{$lang.albums.album_field_status_deleting}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_status_id=='5'}}selected{{/if}}>{{$lang.albums.album_field_status_deleted}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_is_private">
						<option value="">{{$lang.albums.album_field_type}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_is_private=='0'}}selected{{/if}}>{{$lang.albums.album_field_type_public}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_is_private=='1'}}selected{{/if}}>{{$lang.albums.album_field_type_private}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_is_private=='2'}}selected{{/if}}>{{$lang.albums.album_field_type_premium}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_access_level_id">
						<option value="">{{$lang.albums.album_field_access_level}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_access_level_id=='0'}}selected{{/if}}>{{$lang.albums.album_field_access_level_inherit}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_access_level_id=='1'}}selected{{/if}}>{{$lang.albums.album_field_access_level_all}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_access_level_id=='2'}}selected{{/if}}>{{$lang.albums.album_field_access_level_members}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_access_level_id=='3'}}selected{{/if}}>{{$lang.albums.album_field_access_level_premium}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.albums.album_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=content_sources</span>
							{{if in_array('content_sources|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_content_source_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_content_source" value="{{$smarty.session.save.$page_name.se_content_source}}" placeholder="{{$lang.albums.album_field_content_source}}..."/>
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
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.albums.album_field_category}}..."/>
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
						<input type="text" name="se_model" value="{{$smarty.session.save.$page_name.se_model}}" placeholder="{{$lang.albums.album_field_model}}..."/>
					</div>
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
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.albums.album_field_tag}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.albums.album_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=content_source_groups</span>
							{{if in_array('content_sources_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_content_source_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_content_source_group" value="{{$smarty.session.save.$page_name.se_content_source_group}}" placeholder="{{$lang.albums.album_field_content_source_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=category_groups</span>
							{{if in_array('category_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_category_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_category_group" value="{{$smarty.session.save.$page_name.se_category_group}}" placeholder="{{$lang.albums.album_field_category_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=model_groups</span>
							{{if in_array('models_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_model_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_model_group" value="{{$smarty.session.save.$page_name.se_model_group}}" placeholder="{{$lang.albums.album_field_model_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_flag_id">
						<option value="">{{$lang.common.dg_filter_flag}}...</option>
						{{foreach item="item_flag" from=$list_flags_albums|smarty:nodefaults}}
							<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected{{/if}}>{{$item_flag.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/title" {{if $smarty.session.save.$page_name.se_field=="empty/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_title}}</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_description}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_rating}}</option>
						<option value="empty/album_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/album_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_visits}}</option>
						<option value="empty/album_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="empty/album_viewed_unique"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_unique_visits}}</option>
						<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_tokens_cost}}</option>
						<option value="empty/content_source" {{if $smarty.session.save.$page_name.se_field=="empty/content_source"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_content_source}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_categories}}</option>
						<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_models}}</option>
						<option value="empty/admin" {{if $smarty.session.save.$page_name.se_field=="empty/admin"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_admin}}</option>
						<option value="empty/admin_flag" {{if $smarty.session.save.$page_name.se_field=="empty/admin_flag"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_admin_flag}}</option>
						<option value="empty/gallery_url" {{if $smarty.session.save.$page_name.se_field=="empty/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_gallery_url}}</option>
						<option value="empty/comments" {{if $smarty.session.save.$page_name.se_field=="empty/comments"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_comments}}</option>
						<option value="empty/favourites" {{if $smarty.session.save.$page_name.se_field=="empty/favourites"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_favourites}}</option>
						<option value="empty/purchases" {{if $smarty.session.save.$page_name.se_field=="empty/purchases"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.albums.album_field_purchases}}</option>
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="ALBUM_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_ALBUM_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="af_custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="ALBUM_FLAG_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_ALBUM_FLAG_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						<option value="filled/title" {{if $smarty.session.save.$page_name.se_field=="filled/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_title}}</option>
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_description}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_rating}}</option>
						<option value="filled/album_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/album_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_visits}}</option>
						<option value="filled/album_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="filled/album_viewed_unique"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_unique_visits}}</option>
						<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_tokens_cost}}</option>
						<option value="filled/content_source" {{if $smarty.session.save.$page_name.se_field=="filled/content_source"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_content_source}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_categories}}</option>
						<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_models}}</option>
						<option value="filled/admin" {{if $smarty.session.save.$page_name.se_field=="filled/admin"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_admin}}</option>
						<option value="filled/admin_flag" {{if $smarty.session.save.$page_name.se_field=="filled/admin_flag"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_admin_flag}}</option>
						<option value="filled/gallery_url" {{if $smarty.session.save.$page_name.se_field=="filled/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_gallery_url}}</option>
						<option value="filled/comments" {{if $smarty.session.save.$page_name.se_field=="filled/comments"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_comments}}</option>
						<option value="filled/favourites" {{if $smarty.session.save.$page_name.se_field=="filled/favourites"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_favourites}}</option>
						<option value="filled/purchases" {{if $smarty.session.save.$page_name.se_field=="filled/purchases"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.albums.album_field_purchases}}</option>
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="ALBUM_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_ALBUM_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="af_custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="ALBUM_FLAG_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_ALBUM_FLAG_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_show_id">
						<option value="">{{$lang.albums.album_filter_other}}...</option>
						<option value="13" {{if $smarty.session.save.$page_name.se_show_id==13}}selected{{/if}}>{{$lang.albums.album_filter_other_from_admin}}</option>
						<option value="14" {{if $smarty.session.save.$page_name.se_show_id==14}}selected{{/if}}>{{$lang.albums.album_filter_other_from_website}}</option>
						<option value="15" {{if $smarty.session.save.$page_name.se_show_id==15}}selected{{/if}}>{{$lang.albums.album_filter_other_from_webmasters}}</option>
						<option value="16" {{if $smarty.session.save.$page_name.se_show_id==16}}selected{{/if}}>{{$lang.albums.album_filter_other_from_grabbers}}</option>
						<option value="17" {{if $smarty.session.save.$page_name.se_show_id==17}}selected{{/if}}>{{$lang.albums.album_filter_other_from_import}}</option>
						{{foreach item="item_lang" from=$list_languages|smarty:nodefaults}}
							<option value="wl/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wl/`$item_lang.code`"}}selected{{/if}}>{{$lang.albums.album_filter_other_language_w|replace:"%1%":$item_lang.title}}</option>
						{{/foreach}}
						{{foreach item="item_lang" from=$list_languages|smarty:nodefaults}}
							<option value="wol/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wol/`$item_lang.code`"}}selected{{/if}}>{{$lang.albums.album_filter_other_language_wo|replace:"%1%":$item_lang.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_storage_group_id">
						<option value="">{{$lang.albums.album_field_server_group}}...</option>
						{{foreach item="item" from=$list_server_groups|smarty:nodefaults}}
							<option value="{{$item.group_id}}" {{if $smarty.session.save.$page_name.se_storage_group_id==$item.group_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_review_flag">
						<option value="">{{$lang.common.dg_filter_review_flag}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_admin_user_id">
						<option value="">{{$lang.albums.album_field_admin}}...</option>
						{{foreach item="item" from=$list_admin_users|smarty:nodefaults}}
							<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_user_id==$item.user_id}}selected{{/if}}>{{$item.login}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_posted">
						<option value="">{{$lang.common.dg_filter_posted}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_posted=="yes"}}selected{{/if}}>{{$lang.common.dg_filter_posted_yes}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_posted=="no"}}selected{{/if}}>{{$lang.common.dg_filter_posted_no}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_post_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_post_date_from" value="{{$smarty.session.save.$page_name.se_post_date_from}}" placeholder="{{$lang.common.dg_filter_post_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_post_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_post_date_to" value="{{$smarty.session.save.$page_name.se_post_date_to}}" placeholder="{{$lang.common.dg_filter_post_date_to}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_has_errors">
						<option value="">{{$lang.albums.album_field_has_errors}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_has_errors=="1"}}selected{{/if}}>{{$lang.albums.album_field_has_errors_1}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_has_errors=="10"}}selected{{/if}}>{{$lang.albums.album_field_has_errors_10}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_locked">
						<option value="">{{$lang.albums.album_field_lock_website}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_locked=='yes'}}selected{{/if}}>{{$lang.albums.album_field_lock_website_locked}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_locked=='no'}}selected{{/if}}>{{$lang.albums.album_field_lock_website_unlocked}}</option>
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0 || $item.status_id==4 || $item.status_id==5}}disabled{{/if}}">
							<td class="dg_selector">
								<input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/>
								<input type="hidden" name="row_all[]" value="{{$item.$table_key_name}}"/>
								<span class="js_params">
									{{if $item.status_id==0}}
										<span class="js_param">inactive=true</span>
									{{/if}}
								</span>
							</td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							{{if $item.status_id==4}}
								<td class="nowrap"></td>
							{{else}}
								<td class="nowrap">
									{{if $item.status_id==5 || $item.status_id==4 || $item.status_id==3 || $item.status_id==2}}
										{{assign var="functionality_disabled" value=1}}
									{{else}}
										{{assign var="functionality_disabled" value=0}}
									{{/if}}
									<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>

									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">name={{if $item.title!=''}}{{$item.title}}{{else}}{{$item.$table_key_name}}{{/if}}</span>
											{{if $item.status_id!=2}}
												<span class="js_param">restart_hide=true</span>
											{{/if}}
											{{if $item.status_id!=0 && $item.status_id!=1 && $item.status_id!=2}}
												<span class="js_param">soft_delete_hide=true</span>
											{{/if}}
											{{if $item.website_link==''}}
												<span class="js_param">website_link_disable=true</span>
											{{else}}
												<span class="js_param">website_link={{$item.website_link}}</span>
											{{/if}}
											{{if $functionality_disabled==0}}
												{{if $item.status_id==1}}
													<span class="js_param">activate_hide=true</span>
												{{else}}
													<span class="js_param">deactivate_hide=true</span>
												{{/if}}
												{{if $item.is_review_needed!=1}}
													<span class="js_param">mark_reviewed_hide=true</span>
												{{/if}}
											{{else}}
												<span class="js_param">activate_hide=true</span>
												<span class="js_param">deactivate_hide=true</span>
												<span class="js_param">mark_reviewed_hide=true</span>
												<span class="js_param">validate_hide=true</span>
												<span class="js_param">preview_hide=true</span>
											{{/if}}
										</span>
									</a>
								</td>
							{{/if}}
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
					<li class="js_params">
						<span class="js_param">href=?batch_action=soft_delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_soft_delete}}</span>
						<span class="js_param">hide=${soft_delete_hide}</span>
						<span class="js_param">icon=action-softdelete</span>
					</li>
				{{/if}}
				{{if $can_restart==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.albums.album_action_restart}}</span>
						<span class="js_param">hide=${restart_hide}</span>
						<span class="js_param">icon=action-redo</span>
					</li>
				{{/if}}
				{{if $can_edit_status==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
						<span class="js_param">hide=${activate_hide}</span>
						<span class="js_param">icon=action-activate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
						<span class="js_param">hide=${deactivate_hide}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
						<span class="js_param">hide=${mark_reviewed_hide}</span>
						<span class="js_param">icon=action-approve</span>
					</li>
				{{/if}}
				{{if in_array('users|manage_comments',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=2&amp;object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-comment</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=${website_link}</span>
					<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
					<span class="js_param">disable=${website_link_disable}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=action-open</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=album_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.albums.album_action_view_log}}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
				</li>
				{{if in_array('system|background_tasks',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_background_tasks.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.albums.album_action_view_tasks}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=action-log</span>
					</li>
				{{/if}}
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=2&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=action-log</span>
					</li>
				{{/if}}
				{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=stats_albums.php?no_filter=true&amp;se_group_by=date&amp;se_id=${id}</span>
						<span class="js_param">title={{$lang.albums.album_action_view_stats}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-traffic</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=preview_album.php?album_id=${id}</span>
					<span class="js_param">title={{$lang.albums.album_action_preview}}</span>
					<span class="js_param">hide=${preview_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-player</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=album_validate&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.albums.album_action_validate}}</span>
					<span class="js_param">hide=${validate_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-system</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_delete}}">
							<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
							<option value="soft_delete">{{$lang.common.dg_batch_actions_soft_delete|replace:"%1%":'${count}'}}</option>
							{{if $failed_count>0}}
								<option value="delete_failed">{{$lang.albums.album_batch_delete_failed|replace:"%1%":$failed_count}}</option>
							{{/if}}
							{{if $can_edit_status==1}}
								<option value="delete_and_activate">{{$lang.albums.album_batch_delete_and_activate|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_edit_status==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_status}}">
							<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
							<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
							<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed|replace:"%1%":'${count}'}}</option>
							{{if $can_delete==1}}
								<option value="activate_and_delete">{{$lang.albums.album_batch_activate_and_delete|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_mass_edit==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_massedit}}">
							<option value="mass_edit">{{$lang.albums.album_batch_mass_edit|replace:"%1%":'${count}'}}</option>
							{{if $total_num>0}}
								<option value="mass_edit_filtered">{{$lang.albums.album_batch_mass_edit_filtered|replace:"%1%":$total_num}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_restart==1 || $can_export==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_other}}">
							{{if $can_restart==1}}
								{{if $failed_count>0}}
									<option value="restart">{{$lang.albums.album_batch_restart_failed|replace:"%1%":$failed_count}}</option>
								{{/if}}
								<option value="inc_priority">{{$lang.albums.album_batch_inc_priority|replace:"%1%":'${count}'}}</option>
							{{/if}}
							{{if $can_export==1}}
								<option value="export">{{$lang.albums.album_batch_export|replace:"%1%":'${count}'}}</option>
							{{/if}}
						</optgroup>
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
					<span class="js_param">value=activate_and_delete</span>
					<span class="js_param">confirm={{$lang.albums.album_batch_activate_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_activate</span>
					<span class="js_param">confirm={{$lang.albums.album_batch_delete_and_activate_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=mass_edit_filtered</span>
					<span class="js_param">requires_selection=false</span>
					{{if $mass_edit_all_count==$total_num}}
						<span class="js_param">destructive=true</span>
						<span class="js_param">confirm={{$lang.albums.album_batch_mass_edit_filtered_confirm|count_format:"%1%":$total_num}}</span>
					{{/if}}
				</li>
				<li class="js_params">
					<span class="js_param">value=restart</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.albums.album_batch_restart_failed_confirm|count_format:"%1%":$failed_count}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_failed</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.albums.album_batch_delete_failed_confirm|count_format:"%1%":$failed_count}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}