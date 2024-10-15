{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='mark_deleted' || $smarty.get.action=='change_deleted'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="video_deleted_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a>
				/
				{{if $smarty.get.action=='mark_deleted'}}
					{{$lang.videos.video_mark_deleted}}
				{{else}}
					{{if $smarty.post.title!=''}}
						{{$lang.videos.video_edit_deleted|replace:"%1%":$smarty.post.title}}
					{{else}}
						{{$lang.videos.video_edit_deleted|replace:"%1%":$smarty.post.video_id}}
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
			{{if count($smarty.post.delete_videos)>0}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_delete_count}}</td>
					<td class="de_control">
						{{$smarty.post.delete_videos|@count}}
						{{if count($smarty.post.delete_videos)<=20}}
							{{assign var="delete_titles" value=""}}
							{{foreach from=$smarty.post.delete_videos|smarty:nodefaults item="item" name="deleted"}}
								{{assign var="delete_titles" value="`$delete_titles``$item.video_id` / `$item.title`"}}
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
					<td class="de_label">{{$lang.videos.video_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_delete_reason}}</td>
				<td class="de_control">
					<select name="top_delete_reasons">
						<option value="{{$smarty.post.delete_reason}}">{{$lang.common.select_default_option}}</option>
						{{foreach from=$smarty.post.top_delete_reasons|smarty:nodefaults item="item"}}
							<option value="{{$item.delete_reason}}">{{$item.delete_reason}} ({{$item.total_videos}})</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.videos.video_field_delete_reason_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_delete_reason_text}}</td>
				<td class="de_control">
					<textarea name="delete_reason" cols="40" rows="3" data-autopopulate-from="top_delete_reasons">{{$smarty.post.delete_reason}}</textarea>
					<span class="de_hint">{{$lang.videos.video_field_delete_reason_text_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action=='mark_deleted'}}
				<tr>
					<td></td>
					<td class="de_control"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="delete_send_email" value="1"/><label>{{$lang.videos.video_field_delete_email}}</label></span></td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.videos.video_field_delete_email_to}}</td>
					<td class="de_control"><input type="text" name="delete_send_email_to"/></td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.videos.video_field_delete_email_subject}}</td>
					<td class="de_control">
						<input type="text" name="delete_send_email_subject" value="{{$smarty.session.save.$page_name.delete_send_email_subject}}"/>
						<span class="de_hint">{{$lang.videos.video_field_delete_email_subject_hint}}</span>
					</td>
				</tr>
				<tr class="delete_send_email_on">
					<td class="de_label de_required de_dependent">{{$lang.videos.video_field_delete_email_body}}</td>
					<td class="de_control">
						<textarea name="delete_send_email_body" rows="10" cols="40">{{$smarty.session.save.$page_name.delete_send_email_body}}</textarea>
						<span class="de_hint">{{$lang.videos.video_field_delete_email_body_hint}}</span>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='mark_deleted'}}
			<input type="hidden" name="action" value="mark_deleted_complete"/>
			<input type="hidden" name="delete_id" value="{{$smarty.get.delete_id}}"/>
			<input type="submit" name="save_default" value="{{$lang.videos.video_btn_mark_deleted}}"/>
		{{else}}
			<input type="hidden" name="action" value="change_deleted_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('videos|edit_all',$smarty.session.permissions) || (in_array('videos|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}
{{if in_array('videos|edit_title',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_title" value=1}}
{{else}}
	{{assign var="can_edit_title" value=0}}
{{/if}}
{{if in_array('videos|edit_dir',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_dir" value=1}}
{{else}}
	{{assign var="can_edit_dir" value=0}}
{{/if}}
{{if in_array('videos|edit_description',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_description" value=1}}
{{else}}
	{{assign var="can_edit_description" value=0}}
{{/if}}
{{if in_array('videos|edit_post_date',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_post_date" value=1}}
{{else}}
	{{assign var="can_edit_post_date" value=0}}
{{/if}}
{{if in_array('videos|edit_user',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_user" value=1}}
{{else}}
	{{assign var="can_edit_user" value=0}}
{{/if}}
{{if in_array('videos|edit_status',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
{{/if}}
{{if in_array('videos|edit_type',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_type" value=1}}
{{else}}
	{{assign var="can_edit_type" value=0}}
{{/if}}
{{if in_array('videos|edit_access_level',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_access_level" value=1}}
{{else}}
	{{assign var="can_edit_access_level" value=0}}
{{/if}}
{{if in_array('videos|edit_tokens',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_tokens" value=1}}
{{else}}
	{{assign var="can_edit_tokens" value=0}}
{{/if}}
{{if in_array('videos|edit_release_year',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_release_year" value=1}}
{{else}}
	{{assign var="can_edit_release_year" value=0}}
{{/if}}
{{if in_array('videos|edit_video_files',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_video_files" value=1}}
{{else}}
	{{assign var="can_edit_video_files" value=0}}
{{/if}}
{{if in_array('videos|edit_embed',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_embed" value=1}}
{{else}}
	{{assign var="can_edit_embed" value=0}}
{{/if}}
{{if in_array('videos|edit_url',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_url" value=1}}
{{else}}
	{{assign var="can_edit_url" value=0}}
{{/if}}
{{if in_array('videos|edit_pseudo_url',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_pseudo_url" value=1}}
{{else}}
	{{assign var="can_edit_pseudo_url" value=0}}
{{/if}}
{{if in_array('videos|edit_duration',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_duration" value=1}}
{{else}}
	{{assign var="can_edit_duration" value=0}}
{{/if}}
{{if in_array('videos|edit_dvd',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_dvd" value=1}}
{{else}}
	{{assign var="can_edit_dvd" value=0}}
{{/if}}
{{if in_array('videos|edit_content_source',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_content_source" value=1}}
{{else}}
	{{assign var="can_edit_content_source" value=0}}
{{/if}}
{{if in_array('videos|edit_categories',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_categories" value=1}}
{{else}}
	{{assign var="can_edit_categories" value=0}}
{{/if}}
{{if in_array('videos|edit_tags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_tags" value=1}}
{{else}}
	{{assign var="can_edit_tags" value=0}}
{{/if}}
{{if in_array('videos|edit_models',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_models" value=1}}
{{else}}
	{{assign var="can_edit_models" value=0}}
{{/if}}
{{if in_array('videos|edit_flags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_flags" value=1}}
{{else}}
	{{assign var="can_edit_flags" value=0}}
{{/if}}
{{if in_array('videos|edit_custom',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_custom" value=1}}
{{else}}
	{{assign var="can_edit_custom" value=0}}
{{/if}}
{{if in_array('videos|edit_admin_flag',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_admin_flag" value=1}}
{{else}}
	{{assign var="can_edit_admin_flag" value=0}}
{{/if}}
{{if in_array('videos|edit_is_locked',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_is_locked" value=1}}
{{else}}
	{{assign var="can_edit_is_locked" value=0}}
{{/if}}
{{if in_array('videos|edit_storage',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_storage" value=1}}
{{else}}
	{{assign var="can_edit_storage" value=0}}
{{/if}}
{{if in_array('videos|edit_connected_data',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_connected_data" value=1}}
{{else}}
	{{assign var="can_edit_connected_data" value=0}}
{{/if}}
{{if in_array('videos|manage_screenshots',$smarty.session.permissions)}}
	{{assign var="can_manage_screenshots" value=1}}
{{else}}
	{{assign var="can_manage_screenshots" value=0}}
{{/if}}
{{if in_array('videos|delete',$smarty.session.permissions)}}
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

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="video_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.videos.video_add}}
				{{else}}
					{{if $smarty.post.dvd_id>0}}
						{{if in_array('dvds|view',$smarty.session.permissions)}}
							<a href="dvds.php?action=change&amp;item_id={{$smarty.post.dvd_id}}">{{$smarty.post.dvd.title}}</a>
						{{else}}
							{{$smarty.post.dvd.title}}
						{{/if}}
						/
					{{/if}}
					{{if $smarty.post.title!=''}}
						{{$lang.videos.video_edit|replace:"%1%":$smarty.post.title}}
					{{else}}
						{{$lang.videos.video_edit|replace:"%1%":$smarty.post.video_id}}
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
						<li><a href="?action=video_log&amp;item_id={{$smarty.post.video_id}}" rel="log"><i class="icon icon-action-log"></i>{{$lang.videos.video_action_view_log}}</a></li>
						{{if in_array('system|background_tasks',$smarty.session.permissions)}}
							<li><a href="log_background_tasks.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id={{$smarty.post.video_id}}"><i class="icon icon-type-task"></i>{{$lang.videos.video_action_view_tasks}}</a></li>
						{{/if}}
						{{if in_array('system|administration',$smarty.session.permissions)}}
							<li><a href="log_audit.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id={{$smarty.post.video_id}}"><i class="icon icon-type-audit"></i>{{$lang.common.dg_actions_additional_view_audit_log}}</a></li>
						{{/if}}
						{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
							<li><a href="stats_videos.php?no_filter=true&amp;se_group_by=date&amp;se_id={{$smarty.post.video_id}}"><i class="icon icon-type-traffic"></i>{{$lang.videos.video_action_view_stats}}</a></li>
						{{/if}}
						<li><a href="?action=video_validate&amp;item_id={{$smarty.post.video_id}}" rel="log"><i class="icon icon-type-system"></i>{{$lang.videos.video_action_validate}}</a></li>
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
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/57-6-ways-to-add-videos-into-kvs/">6 ways to add videos into KVS</a></span>
				</td>
			</tr>
			{{if $smarty.post.is_review_needed==1 && $can_edit_status==1}}
				<tr>
					<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_review}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.videos.video_divider_review_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.video_field_review_action}}</td>
					<td class="de_control" colspan="3">
						<table class="control_group">
							<tr>
								<td class="de_vis_sw_select">
									<select name="is_reviewed" class="preserve_editing">
										<option value="0">{{$lang.videos.video_field_review_action_none}}</option>
										<option value="1">{{$lang.videos.video_field_review_action_approve}}</option>
										<option value="2" {{if $can_delete==0}}disabled{{/if}}>{{$lang.videos.video_field_review_action_delete}}</option>
									</select>
								</td>
							</tr>
							{{if $smarty.post.status_id==0}}
								<tr class="is_reviewed_1">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_activate" value="1" class="preserve_editing"/><label>{{$lang.videos.video_field_review_action_activate}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_disable_user" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('users|edit_all',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.videos.video_field_review_action_disable_user|replace:"%1%":$smarty.post.user}}</label></span>
									</td>
								</tr>
								{{if $smarty.post.user_domain!='' && $smarty.post.user_domain_blocked!=1}}
									<tr class="is_reviewed_2">
										<td>
											<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_domain" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.videos.video_field_review_action_block_domain|replace:"%1%":$smarty.post.user_domain}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
							{{if $smarty.post.ip_mask!='0.0.0.*' && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_mask" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.videos.video_field_review_action_block_mask|replace:"%1%":$smarty.post.ip_mask}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.ip!='0.0.0.0' && $smarty.post.ip_blocked!=1 && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_ip" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.videos.video_field_review_action_block_ip|replace:"%1%":$smarty.post.ip}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								{{if $smarty.post.other_videos_need_review>0}}
									<tr class="is_reviewed_2">
										<td>
											{{assign var="max_delete_on_review" value=$config.max_delete_on_review|intval}}
											{{if $max_delete_on_review==0}}
												{{assign var="max_delete_on_review" value=30}}
											{{/if}}
											<span class="de_lv_pair"><input type="checkbox" name="is_delete_all_videos_from_user" value="1" class="is_reviewed_delete preserve_editing" {{if $can_delete!=1 || $smarty.post.other_videos_need_review>$max_delete_on_review}}disabled{{/if}}/><label>{{$lang.videos.video_field_review_action_delete_other|replace:"%1%":$smarty.post.other_videos_need_review}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
						</table>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.video_field_review_similar_videos}}</td>
					<td class="de_control">
						{{if count($smarty.post.similar_videos)>0}}
							<div class="de_img_list" data-viewer-control="iframe">
								<div class="de_img_list_main">
									{{assign var="max_thumb_size" value=$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}
									{{assign var="max_thumb_size" value="x"|explode:$max_thumb_size}}
									{{foreach item="similar_video" from=$smarty.post.similar_videos|smarty:nodefaults}}
										<div class="de_img_list_item" style="{{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0+20}}px{{/if}};">
											<a class="de_img_list_thumb" href="preview_video.php?video_id={{$similar_video.video_id}}" style="{{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0+10}}px{{/if}};">
												<img src="?action=screenshot_source&amp;group_id=1&amp;item_id={{$similar_video.video_id}}&amp;index=1&amp;rnd={{$smarty.now}}" style="object-fit: contain; {{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.0>0}}height: {{$max_thumb_size.0*9/16}}px{{/if}}" alt="{{$similar_video.title}}"/>
												<i>
													{{$similar_video.duration|durationToHumanString}}
													<em class="separator"></em>
													<em class="icon icon-type-visit"></em> {{$similar_video.video_viewed|traffic_format}}
												</i>
											</a>
											<div class="de_img_list_options">
												<a rel="other" href="?action=change&amp;item_id={{$similar_video.video_id}}" title="{{$similar_video.title}}">{{$similar_video.title}}</a>
											</div>
										</div>
									{{/foreach}}
								</div>
							</div>
						{{else}}
							<span>{{$lang.videos.video_field_review_similar_videos_none}}</span>
						{{/if}}
					</td>
				</tr>
				{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
					<tr>
						<td class="de_label">{{$lang.videos.video_field_af_upload_zone}}</td>
						<td class="de_control">
							<select name="af_upload_zone" class="preserve_editing">
								<option value="0" {{if $smarty.post.af_upload_zone==0}}selected{{/if}}>{{$lang.videos.video_field_af_upload_zone_site}}</option>
								<option value="1" {{if $smarty.post.af_upload_zone==1}}selected{{/if}}>{{$lang.videos.video_field_af_upload_zone_memberarea}}</option>
							</select>
							<span class="de_hint">{{$lang.videos.video_field_af_upload_zone_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label status_id_on">{{$lang.videos.video_field_title}}</td>
				<td class="de_control" colspan="3">
					<input type="text" name="title" maxlength="255" class="{{if $can_edit_title==1}}preserve_editing{{/if}}" value="{{$smarty.post.title}}"/>
					<span class="de_hint">{{$lang.videos.video_field_title_hint}}, <span class="de_str_len_value"></span></span>
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_directory}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="dir" maxlength="255" class="{{if $can_edit_dir==1}}preserve_editing{{/if}}" value="{{$smarty.post.dir}}" {{if $options.VIDEO_REGENERATE_DIRECTORIES==1}}readonly{{/if}}/>
						<span class="de_hint">
							{{if $options.VIDEO_REGENERATE_DIRECTORIES==1}}
								{{$lang.videos.video_field_directory_hint2|replace:"%1%":$lang.videos.video_field_title}}
							{{else}}
								{{$lang.videos.video_field_directory_hint|replace:"%1%":$lang.videos.video_field_title}}
							{{/if}}
						</span>
					</td>
				</tr>
				{{if $smarty.post.website_link!=''}}
					<tr data-field-name="website_link" {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$lang.videos.video_field_website_link}}</td>
						<td class="de_control" colspan="3">
							<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_description}}</td>
				<td class="de_control" colspan="3">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_description==1}}preserve_editing{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			{{if is_array($smarty.post.copyright_data)}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_digiregs_copyright}}</td>
					<td class="de_control" colspan="3">
						{{if $smarty.post.copyright_data.watermark!=''}}
							{{if $smarty.post.copyright_data.type=='unknown'}}
								{{$lang.videos.video_field_digiregs_copyright_watermark}} ({{$smarty.post.copyright_data.watermark}})
							{{else}}
								{{$lang.videos.video_field_digiregs_copyright_studio}} ({{$smarty.post.copyright_data.watermark}}{{if $smarty.post.copyright_data.owner}} - {{$smarty.post.copyright_data.owner}}{{/if}})
							{{/if}}
						{{else}}
							{{$lang.videos.video_field_digiregs_copyright_empty}}
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_content_source}}</td>
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
					<span class="de_hint">{{$lang.videos.video_field_content_source_hint}}</span>
				</td>
				<td class="de_label de_required">{{$lang.videos.video_field_user}}</td>
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
					<span class="de_hint">{{$lang.videos.video_field_user_hint}}</span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_dvd}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvds</span>
							{{if in_array('dvds|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.dvd_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="dvd" maxlength="255" class="{{if $can_edit_dvd==1}}preserve_editing{{/if}}" value="{{$smarty.post.dvd.title}}"/>
					</div>
				</td>
				<td class="de_label">{{$lang.videos.video_field_release_year}}</td>
				<td class="de_control" colspan="3">
					<input type="text" name="release_year" maxlength="10" size="10" class="{{if $can_edit_release_year==1}}preserve_editing{{/if}}" value="{{$smarty.post.release_year}}"/>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label de_required">{{$lang.videos.video_field_post_date}}</td>
				<td class="de_control">
					{{if $config.relative_post_dates=='true'}}
						<table class="control_group de_vis_sw_radio">
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option!='1'}}checked{{/if}}/><label>{{$lang.videos.video_field_post_date_fixed}}</label></span>
									<span class="calendar">
										<span class="js_params">
											<span class="js_param">type=datetime</span>
											{{if $can_edit_post_date!=1}}
												<span class="js_param">forbid_edit=true</span>
											{{/if}}
										</span>
										<input class="post_date_option_fixed" type="text" name="post_date" value="{{$smarty.post.post_date}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
									<span class="de_hint">{{$lang.videos.video_field_post_date_hint1}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option=='1'}}checked{{/if}}/><label>{{$lang.videos.video_field_post_date_relative}}</label></span>
									<span>
										<input type="text" name="relative_post_date" size="4" maxlength="5" class="post_date_option_relative {{if $can_edit_post_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.relative_post_date}}"/>
										{{$lang.videos.video_field_post_date_relative_days}}
									</span>
									<span class="de_hint">{{$lang.videos.video_field_post_date_hint2}}</span>
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
						<span class="de_hint">{{$lang.videos.video_field_post_date_hint}}</span>
					{{/if}}
				</td>
				<td class="de_label de_required">{{$lang.videos.video_field_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="is_private" {{if $can_edit_type==1}}class="preserve_editing"{{/if}}>
						<option value="0" {{if $smarty.post.is_private=='0'}}selected{{/if}}>{{$lang.videos.video_field_type_public}}</option>
						<option value="1" {{if $smarty.post.is_private=='1'}}selected{{/if}}>{{$lang.videos.video_field_type_private}}</option>
						<option value="2" {{if $smarty.post.is_private=='2'}}selected{{/if}}>{{$lang.videos.video_field_type_premium}}</option>
					</select>
					<span class="de_hint is_private_0">
						{{if $options.PUBLIC_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PUBLIC_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PUBLIC_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.videos.video_field_type_hint_configure}}</a>
						{{/if}}
					</span>
					<span class="de_hint is_private_1">
						{{if $options.PRIVATE_VIDEOS_ACCESS==3}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_friends}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.videos.video_field_type_hint_configure}}</a>
						{{/if}}
					</span>
					<span class="de_hint is_private_2">
						{{if $options.PREMIUM_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PREMIUM_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PREMIUM_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
						{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
							<br/><a href="options.php?page=memberzone_settings">{{$lang.videos.video_field_type_hint_configure}}</a>
						{{/if}}
					</span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_server_group}}</td>
				<td class="de_control">
					<div class="nowrap option_video option_gallery de_vis_sw_button">
						<select name="server_group_id" class="{{if $can_edit_storage==1}}preserve_editing{{/if}} change_storage_group" {{if $smarty.get.action=='change'}}disabled{{/if}}>
							{{if $smarty.get.action=='add_new'}}
								<option value="">{{$lang.videos.video_field_server_group_auto}}</option>
							{{/if}}
							{{foreach name="data" item="item" from=$list_server_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $item.group_id==$smarty.post.server_group_id}}selected{{/if}}>{{$item.title}} ({{$lang.videos.video_field_server_group_free_space|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
							{{/foreach}}
						</select>
						{{if $smarty.get.action!='add_new' && $can_edit_storage==1}}
							<input name="change_storage_group" type="button" value="{{$lang.videos.video_field_server_group_change}}" {{if $smarty.post.server_group_migration_not_finished>0}}disabled{{/if}} data-confirm="{{$lang.videos.video_field_server_group_change_warning}}" data-destructive="false"/>
						{{/if}}
						<span class="de_hint">{{$lang.videos.video_field_server_group_hint}}</span>
					</div>
					<div class="option_video_url option_embed option_pseudo">
						{{$lang.common.undefined}}
					</div>
				</td>
				<td class="de_label">{{$lang.videos.video_field_access_level}}</td>
				<td class="de_control">
					<select name="access_level_id" {{if $can_edit_access_level==1}}class="preserve_editing"{{/if}}>
						<option value="0" {{if $smarty.post.access_level_id==0}}selected{{/if}}>{{$lang.videos.video_field_access_level_inherit}}</option>
						<option value="1" {{if $smarty.post.access_level_id==1}}selected{{/if}}>{{$lang.videos.video_field_access_level_all}}</option>
						<option value="2" {{if $smarty.post.access_level_id==2}}selected{{/if}}>{{$lang.videos.video_field_access_level_members}}</option>
						<option value="3" {{if $smarty.post.access_level_id==3}}selected{{/if}}>{{$lang.videos.video_field_access_level_premium}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.video_field_access_level_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_admin_flag}}</td>
				<td class="de_control">
					<select name="admin_flag_id" {{if $can_edit_admin_flag==1}}class="preserve_editing"{{/if}}>
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.videos.video_field_admin_flag_hint}}</span>
				</td>
				<td class="de_label">{{$lang.videos.video_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}} {{if $can_edit_status==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.video_field_status_active}}</label></span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_lock_website}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked{{/if}} {{if $can_edit_is_locked==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.video_field_lock_website_locked}}</label></span>
						<span class="de_hint">{{$lang.videos.video_field_lock_website_hint}}</span>
					</td>
					<td class="de_label">{{$lang.videos.video_field_ip}}</td>
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
			{{if $config.installation_type>=2 && (($smarty.post.is_private==2 && $memberzone_data.ENABLE_TOKENS_PREMIUM_VIDEO==1) || ($smarty.post.is_private==1 && $memberzone_data.ENABLE_TOKENS_PRIVATE_VIDEO==1) || ($smarty.post.is_private==0 && $memberzone_data.ENABLE_TOKENS_PUBLIC_VIDEO==1))}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_tokens_cost}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="tokens_required" maxlength="10" size="10" {{if $can_edit_tokens==1}}class="preserve_editing"{{/if}} value="{{$smarty.post.tokens_required}}"/>
						<span class="de_hint is_private_0">{{$lang.videos.video_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PUBLIC_VIDEO}}</span>
						<span class="de_hint is_private_1">{{$lang.videos.video_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PRIVATE_VIDEO}}</span>
						<span class="de_hint is_private_2">{{$lang.videos.video_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PREMIUM_VIDEO}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type==4 && $existing_albums_count>0}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_connected_albums}}</td>
					<td class="de_control" colspan="3">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.video_field_connected_albums}}</span>
								<span class="js_param">url=async/insight.php?type=albums</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=connected_album_ids[]</span>
								<span class="js_param">empty_message={{$lang.videos.video_field_connected_albums_empty}}</span>
								{{if $can_edit_connected_data!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
								{{if in_array('albums|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.connected_albums|smarty:nodefaults}}
								<input type="hidden" name="connected_album_ids[]" value="{{$item.album_id}}" alt="{{if $item.title!=''}}{{$item.title}}{{else}}{{$lang.common.object_type_with_id|replace:"%1%":$lang.common.object_type_album|replace:"%2%":$item.album_id}}{{/if}}"/>
							{{/foreach}}
							{{if $can_edit_connected_data==1}}
								<div class="controls">
									<input type="text" name="new_album" class="preserve_editing"/>
									<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
									<input type="button" class="all preserve_editing" value="{{$lang.videos.video_field_connected_albums_all}}"/>
								</div>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=3 && $existing_posts_count>0}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_connected_posts}}</td>
					<td class="de_control" colspan="3">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.video_field_connected_posts}}</span>
								<span class="js_param">url=async/insight.php?type=posts</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=connected_post_ids[]</span>
								<span class="js_param">empty_message={{$lang.videos.video_field_connected_posts_empty}}</span>
								{{if $can_edit_connected_data!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
								{{if in_array('posts|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							{{foreach name="data" item="item" from=$smarty.post.connected_posts|smarty:nodefaults}}
								<input type="hidden" name="connected_post_ids[]" value="{{$item.post_id}}" alt="{{if $item.title!=''}}{{$item.title}}{{else}}{{$lang.common.object_type_with_id|replace:"%1%":$lang.common.object_type_post|replace:"%2%":$item.post_id}}{{/if}}"/>
							{{/foreach}}
							{{if $can_edit_connected_data==1}}
								<div class="controls">
									<input type="text" name="new_post" class="preserve_editing"/>
									<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
									<input type="button" class="all preserve_editing" value="{{$lang.videos.video_field_connected_posts_all}}"/>
								</div>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_show_player==1}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_preview}}</td>
					<td class="de_control" colspan="3">
						{{if $smarty.post.show_preview==1}}
							{{if $smarty.post.load_type_id==3}}
								<div style="width: {{$smarty.post.preview_dimensions.0}}px; height: {{$smarty.post.preview_dimensions.1}}px">
									{{$smarty.post.preview_embed|smarty:nodefaults}}
								</div>
							{{else}}
								<script type="text/javascript" data-cfasync="false" src="{{$config.project_url|replace:"www.":""}}/player/kt_player.js?v={{$config.project_version}}"></script>
								<div style="width: {{$smarty.post.preview_dimensions.0}}px; height: {{$smarty.post.preview_dimensions.1}}px"><div id="kt_player"></div></div>
								<script type="text/javascript" data-cfasync="false">
									/* <![CDATA[ */
									var flashvars = {
										{{foreach key="name" item="value" name="flashvars" from=$smarty.post.preview_flashvars|smarty:nodefaults}}
											{{$name}}: '{{$value|replace:"'":"\'"|smarty:nodefaults}}'{{if !$smarty.foreach.flashvars.last}}, {{/if}}
										{{/foreach}}
									};
									kt_player('kt_player', '{{$config.project_url|replace:"www.":""}}/player/kt_player.swf?v={{$config.project_version}}', '{{$smarty.post.preview_dimensions.0}}', '{{$smarty.post.preview_dimensions.1}}', flashvars);
									/* ]]> */
								</script>
							{{/if}}
						{{else}}
							{{$lang.videos.video_field_preview_no}}
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='change' && in_array('localization|view',$smarty.session.permissions) && $smarty.session.save.options.video_edit_show_translations=='1'}}
				{{assign var="header_output" value="1"}}
				{{foreach name="data" item="item" from=$list_languages|smarty:nodefaults}}
					{{assign var="permission_id" value="localization|`$item.code`"}}
					{{assign var="title_selector" value="title_`$item.code`"}}
					{{assign var="dir_selector" value="dir_`$item.code`"}}
					{{assign var="desc_selector" value="description_`$item.code`"}}
					{{if in_array($permission_id,$smarty.session.permissions)}}
						{{if $header_output==1}}
							<tr>
								<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_localization}}</h2></td>
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
									<span class="de_hint">{{$lang.videos.video_field_directory_hint_translation|replace:"%1%":$item.title|replace:"%2%":$lang.common.title_translation|replace:"%1%":$item.title}}</span>
								</td>
							</tr>
						{{/if}}
						{{if $item.translation_scope_videos==0}}
							<tr>
								<td class="de_label">{{$lang.common.description_translation|replace:"%1%":$item.title}}</td>
								<td class="de_control" colspan="3">
									<textarea name="{{$desc_selector}}" class="preserve_editing {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}}" cols="40" rows="3">{{$smarty.post.$desc_selector}}</textarea>
									<span class="de_hint"><span class="de_str_len_value"></span></span>
								</td>
							</tr>
						{{/if}}
					{{/if}}
				{{/foreach}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_tags}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_tags}}</span>
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
							<span class="js_param">empty_message={{$lang.videos.video_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_tags==1}}
							<div class="controls">
								<input type="text" name="new_tag" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.videos.video_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_categories}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.video_field_categories_empty}}</span>
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
								<input type="button" class="all preserve_editing" value="{{$lang.videos.video_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_models}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=model_ids[]</span>
							{{if in_array('models|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.video_field_models_empty}}</span>
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
								<input type="button" class="all preserve_editing" value="{{$lang.videos.video_field_models_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_flags}}</td>
					<td class="de_control" colspan="3">
						<div class="de_deletable_list">
							<div class="js_params">
								<span class="js_param">submit_name=delete_flags[]</span>
								<span class="js_param">empty_message={{$lang.videos.video_field_flags_empty}}</span>
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
									{{$lang.videos.video_field_flags_empty}}
								{{/if}}
							</div>
						</div>
					</td>
				</tr>
				{{if count($list_post_process_plugins)>0}}
					<tr>
						<td class="de_label">{{$lang.videos.video_field_categorization_plugins}}</td>
						<td class="de_control">
							<table class="control_group">
								{{foreach item="item" from=$list_post_process_plugins|smarty:nodefaults}}
									<tr><td>
										<span class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}" class="preserve_editing"/> <label>{{$lang.videos.video_field_categorization_plugins_run|replace:"%1%":$item.title}}</label></span>
									</td></tr>
								{{/foreach}}
							</table>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_content}}</h2></td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/17-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different/">What video types are supported in KVS and how they are different</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/56-different-ways-to-upload-video-files-into-kvs/">Different ways to upload video files into KVS</a></span>
				</td>
			</tr>
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.videos.video_field_load_type}}</td>
				<td class="de_control de_vis_sw_radio" colspan="3">
					<span class="de_lv_pair"><input id="option_video" type="radio" name="video_adding_option" value="1" {{if $smarty.post.load_type_id==1 || $smarty.post.load_type_id==4}}checked{{/if}} {{if $smarty.get.action=='change'}}disabled{{/if}}/><label>{{$lang.videos.video_field_load_type_file}}</label></span>
					<span class="de_lv_pair"><input id="option_video_url" type="radio" name="video_adding_option" value="2" {{if $smarty.post.load_type_id==2}}checked{{/if}} {{if $smarty.get.action=='change'}}disabled{{/if}}/><label>{{$lang.videos.video_field_load_type_url}}</label></span>
					<span class="de_lv_pair"><input id="option_embed" type="radio" name="video_adding_option" value="3" {{if $smarty.post.load_type_id==3}}checked{{/if}} {{if $smarty.get.action=='change'}}disabled{{/if}}/><label>{{$lang.videos.video_field_load_type_embed}}</label></span>
					<span class="de_lv_pair"><input id="option_pseudo" type="radio" name="video_adding_option" value="5" {{if $smarty.post.load_type_id==5}}checked{{/if}} {{if $smarty.get.action=='change'}}disabled{{/if}}/><label>{{$lang.videos.video_field_load_type_pseudo}}</label></span>
				</td>
			</tr>
			{{if $smarty.post.gallery_url!=''}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_gallery_url}}</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.gallery_url}}">{{$smarty.post.gallery_url}}</a>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==1 || $smarty.post.source_file.url!=''}}
				<tr {{if $smarty.get.action=='add_new'}}class="option_video"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.videos.video_field_video_file}}</td>
					<td class="de_control" colspan="3">
						<div class="de_fu de_vis_sw_textfield">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.video_field_video_file}}</span>
								<span class="js_param">accept={{$config.video_allowed_ext}}</span>
								{{if $smarty.post.source_file.url!=''}}
									<span class="js_param">download_url={{$smarty.post.source_file.url}}</span>
								{{/if}}
							</div>
							<input type="text" name="video" maxlength="100" {{if $smarty.post.source_file.url!=''}}value="{{$smarty.post.video_id}}.tmp [{{$smarty.post.source_file.dimensions.0}}x{{$smarty.post.source_file.dimensions.1}}, {{$smarty.post.source_file.duration_string}}, {{$smarty.post.source_file.file_size_string}}]"{{/if}}/>
							<input type="hidden" name="video_hash"/>
							{{if $can_edit_video_files==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
						</div>
						{{if $smarty.get.action=='add_new'}}
							<span class="de_hint">{{$lang.videos.video_field_video_file_hint}}</span>
						{{/if}}
					</td>
				</tr>
				{{if $smarty.get.action!='add_new'}}
					<tr class="video_filled {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}hidden{{/if}}">
						<td></td>
						<td class="de_control" colspan="3">
							<table class="control_group">
								<tr>
									<td>
										{{if $smarty.post.load_type_id==1}}
											<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="video_recreate_formats" value="1" {{if $can_edit_video_files==1}}class="preserve_editing"{{/if}} {{if $smarty.post.source_file.url==''}}checked{{/if}}/><label>{{$lang.videos.video_field_video_file_formats}}</label></span>
										{{/if}}
										<span class="de_lv_pair"><input type="checkbox" name="video_recreate_screenshots" value="1" {{if $can_edit_video_files==1}}class="preserve_editing"{{/if}} {{if $smarty.post.source_file.url==''}}checked{{/if}}/><label>{{$lang.videos.video_field_video_file_screenshots}}</label></span>
									</td>
								</tr>
								{{if $smarty.post.load_type_id==1}}
									<tr class="video_recreate_formats_on">
										<td>
											{{foreach name="data" item="item" from=$smarty.post.formats_videos|smarty:nodefaults}}
												{{if $item.format_video_group_id==$smarty.post.format_video_group_id}}
													<span class="de_lv_pair"><input type="checkbox" name="video_recreate_format_{{$item.format_video_id}}" value="1" {{if $can_edit_video_files==1}}class="preserve_editing"{{/if}} {{if $item.video.file_path!='' && $item.video.task.task_id==0}}checked{{/if}} {{if $item.video.task.task_id>0}}disabled{{/if}}/><label>{{$item.title}}</label></span>
												{{/if}}
											{{/foreach}}
										</td>
									</tr>
								{{/if}}
							</table>
						</td>
					</tr>
				{{/if}}
				{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==1}}
					{{if count($list_formats_videos_groups)>1}}
						<tr {{if $smarty.get.action=='add_new'}}class="option_video"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
							<td class="de_label de_required">{{$lang.videos.video_field_format_group}}</td>
							<td class="de_control de_vis_sw_select" colspan="3">
								<select name="format_video_group_id" {{if $smarty.get.action!='add_new'}}disabled{{/if}}>
									<option value="0">{{$lang.videos.video_field_format_group_auto}}</option>
									{{foreach name="data" item="item" from=$list_formats_videos_groups|smarty:nodefaults}}
										<option value="{{$item.format_video_group_id}}" {{if $smarty.post.format_video_group_id==$item.format_video_group_id}}selected{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								</select>
								<span class="de_hint">{{$lang.videos.video_field_format_group_hint}}</span>
							</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$smarty.post.formats_videos|smarty:nodefaults}}
						{{if $smarty.get.action=='add_new' || $item.format_video_group_id==$smarty.post.format_video_group_id}}
							<tr {{if $smarty.get.action=='add_new'}}class="option_video format_video_group_id_{{$item.format_video_group_id}}"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
								{{assign var="is_format_required" value="0"}}
								{{if $smarty.get.action!='add_new' && $item.status_id==1}}
									{{assign var="is_format_required" value="1"}}
								{{/if}}
								<td class="de_label {{if $is_format_required==1}}de_required{{/if}}">{{$lang.videos.video_field_format_video|replace:"%1%":$item.title}}</td>
								<td class="de_control" colspan="3">
									<div class="de_fu">
										{{if $item.video.task.task_id>0}}
											{{if $item.video.task.type_id==3}}
												<input type="text" maxlength="100" value="{{$lang.videos.video_field_format_video_upload}}"/>
											{{elseif $item.video.task.type_id==4}}
												<input type="text" maxlength="100" value="{{$lang.videos.video_field_format_video_create}}"/>
											{{elseif $item.video.task.type_id==5}}
												<input type="text" maxlength="100" value="{{$lang.videos.video_field_format_video_delete}}"/>
											{{/if}}
										{{else}}
											<div class="js_params">
												<span class="js_param">title={{$lang.videos.video_field_format_video|replace:"%1%":$item.title}}</span>
												<span class="js_param">accept={{$config.video_allowed_ext}}{{if end(explode(".",$item.postfix))=='gif'}},gif{{/if}}</span>
												{{if $item.video.file_path!=''}}
													{{assign var="time" value=$smarty.now}}
													<span class="js_param">download_url={{$item.video.file_url}}?ttl={{$time}}&amp;dsc={{"`$config.cv`/`$item.video.file_path`/`$time`"|md5}}&amp;download=true</span>
												{{/if}}
												{{if in_array(end(explode(".",$item.video.file_path)),explode(",",$config.player_allowed_ext))}}
													<span class="js_param">preview_url=preview_video.php?video_id={{$smarty.post.video_id}}&amp;postfix={{$item.postfix|urlencode}}</span>
													<span class="js_param">preview_popup=true</span>
												{{elseif end(explode(".",$item.video.file_path))=='gif'}}
													{{assign var="time" value=$smarty.now}}
													<span class="js_param">preview_url={{$item.video.file_url}}?ttl={{$time}}&amp;dsc={{"`$config.cv`/`$item.video.file_path`/`$time`"|md5}}&amp;download=true</span>
												{{/if}}
											</div>
											<input type="text" name="format_video_{{$item.format_video_id}}" maxlength="100" {{if $item.video.file_path!=''}}value="{{$smarty.post.video_id}}{{$item.postfix}} [{{$item.video.dimensions.0}}x{{$item.video.dimensions.1}}, {{$item.video.duration_string}}, {{$item.video.file_size_string}}{{if $item.video.timeline_screen_amount>0}}, {{$lang.videos.video_field_format_video_timelines|count_format:"%1%":$item.video.timeline_screen_amount}}{{/if}}{{if $item.video.preroll_duration>0}}, {{$lang.videos.video_field_format_video_preroll|replace:"%1%":$item.video.preroll_duration}}{{/if}}]"{{/if}}/>
											<input type="hidden" name="format_video_{{$item.format_video_id}}_hash"/>
											{{if $can_edit_video_files==1}}
												<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
												{{if $is_format_required==0 || $smarty.get.action=='add_new'}}
													<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
												{{/if}}
											{{/if}}
										{{/if}}
									</div>
								</td>
							</tr>
						{{/if}}
					{{/foreach}}
				{{/if}}
			{{/if}}
			{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==3}}
				<tr {{if $smarty.get.action=='add_new'}}class="option_embed"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label de_required">{{$lang.videos.video_field_embed_code}}</td>
					<td class="de_control" colspan="3"><textarea name="embed" class="{{if $can_edit_embed==1}}preserve_editing{{/if}}" cols="40" rows="9">{{$smarty.post.embed}}</textarea></td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==5}}
				<tr {{if $smarty.get.action=='add_new'}}class="option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label de_required">{{$lang.videos.video_field_pseudo_url}}
					</td>
					<td class="de_control" colspan="3">
						<input type="text" name="pseudo_url" maxlength="255" class="{{if $can_edit_pseudo_url==1}}preserve_editing{{/if}}" value="{{$smarty.post.pseudo_url}}"/>
						<span class="de_hint">{{$lang.videos.video_field_pseudo_url_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==5 || $smarty.post.load_type_id==3 || $smarty.post.load_type_id==2}}
				<tr {{if $smarty.get.action=='add_new'}}class="option_video_url option_embed option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label {{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==2}}de_required{{/if}}">{{$lang.videos.video_field_video_url}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="video_url" maxlength="255" class="{{if $can_edit_url==1}}preserve_editing{{/if}}" value="{{$smarty.post.file_url}}"/>
						<span class="de_hint option_video_url">{{$lang.videos.video_field_video_url_hotlink_hint}}</span>
						<span class="de_hint option_embed">{{$lang.videos.video_field_video_url_embed_hint}}</span>
						<span class="de_hint option_pseudo">{{$lang.videos.video_field_video_url_pseudo_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr {{if $smarty.get.action=='add_new'}}class="option_embed option_video_url option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label {{if $smarty.get.action=='add_new'}}option_embed option_pseudo{{else}}de_required{{/if}}">{{$lang.videos.video_field_duration}}</td>
				<td class="de_control" colspan="3">
					<input type="text" name="duration" maxlength="32" size="10" class="{{if $can_edit_duration==1}}preserve_editing{{/if}}" value="{{$smarty.post.duration}}"/>
					<span class="de_hint">{{$lang.videos.video_field_duration_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action=='add_new'}}
				<tr>
					<td class="de_label option_embed option_pseudo">{{$lang.videos.video_field_screenshots_overview}}</td>
					<td class="de_control" colspan="3">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.video_field_screenshots_overview}}</span>
								<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
								<span class="js_param">multiple=true</span>
							</div>
							<input type="text" name="screenshots" maxlength="100"/>
							<input type="hidden" name="screenshots_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.video_field_screenshots_overview_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.video_field_screenshots_posters}}</td>
					<td class="de_control" colspan="3">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.video_field_screenshots_posters}}</span>
								<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
								<span class="js_param">multiple=true</span>
							</div>
							<input type="text" name="posters" maxlength="100"/>
							<input type="hidden" name="posters_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.video_field_screenshots_posters_hint}}</span>
					</td>
				</tr>
			{{elseif $smarty.post.screen_url==''}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_screenshots}}</td>
					<td class="de_control" colspan="3">
						<a href="videos_screenshots.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manage_screenshots}}</a>
						{{if $can_manage_screenshots==1 && $smarty.post.grabbing_possible==1}}
							&nbsp;|&nbsp;
							<a href="videos_screenshots_grabbing.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manual_grabbing}}</a>
						{{/if}}
						<span class="de_hint">{{$lang.videos.video_field_screenshots_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='change' && $smarty.post.rotator_enabled==1}}
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_rotator}}</h2></td>
				</tr>
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_rotator_main}}</td>
					<td class="de_control" colspan="3">
						<span>{{$lang.videos.video_field_rotator_main_value|replace:"%1%":$smarty.post.rotator_views|replace:"%2%":$smarty.post.rotator_clicks|replace:"%3%":$smarty.post.rotator_ctr|replace:"%4%":$smarty.post.rotator_rank}}</span>
					</td>
				</tr>
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_rotator_categories}}</td>
					<td class="de_control" colspan="3">
						<span>
							{{if count($smarty.post.rotator_categories)>0}}
								{{foreach name="data" item="item" from=$smarty.post.rotator_categories|smarty:nodefaults}}
									{{$item.title}}: <b>{{if $item.ctr==0}}0{{else}}{{$item.ctr|default:0|number_format:2}}{{/if}}</b>{{if !$smarty.foreach.data.last}},{{/if}}
								{{/foreach}}
							{{else}}
								{{$lang.videos.video_field_rotator_categories_no}}
							{{/if}}
						</span>
						{{if $smarty.post.rotator_categories_enabled==0}}
							<span class="de_hint">{{$lang.videos.video_field_rotator_categories_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_rotator_tags}}</td>
					<td class="de_control" colspan="3">
						<span>
							{{if count($smarty.post.rotator_tags)>0}}
								{{foreach name="data" item="item" from=$smarty.post.rotator_tags|smarty:nodefaults}}
									{{$item.title}}: <b>{{if $item.ctr==0}}0{{else}}{{$item.ctr|default:0|number_format:2}}{{/if}}</b>{{if !$smarty.foreach.data.last}},{{/if}}
								{{/foreach}}
							{{else}}
								{{$lang.videos.video_field_rotator_tags_no}}
							{{/if}}
						</span>
						{{if $smarty.post.rotator_tags_enabled==0}}
							<span class="de_hint">{{$lang.videos.video_field_rotator_tags_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='change' && $smarty.post.screen_url!=''}}
				<tr>
					<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_screenshots}}</h2></td>
				</tr>
				<tr>
					<td class="de_control" colspan="4">
						<a href="videos_screenshots.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manage_screenshots}}</a>
						{{if $can_manage_screenshots==1 && $smarty.post.grabbing_possible==1}}
							&nbsp;|&nbsp;
							<a href="videos_screenshots_grabbing.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manual_grabbing}}</a>
						{{/if}}
					</td>
				</tr>
				{{if $smarty.post.screen_amount>0}}
					<tr>
						<td class="de_control" colspan="4">
							<div class="de_img_list">
								<div class="de_img_list_header">
									<span>
										<span class="label">{{$lang.videos.screenshots_mgmt_field_display_mode}}:</span>
										<select class="de_switcher de_img_list_display_mode">
											<option value="full">{{$lang.videos.screenshots_mgmt_field_display_mode_full}}</option>
											<option value="basic">{{$lang.videos.screenshots_mgmt_field_display_mode_basic}}</option>
										</select>
									</span>
									<span>
										<span class="label">{{$lang.videos.screenshots_mgmt_field_click_mode}}:</span>
										<select class="de_switcher de_img_list_click_mode">
											<option value="viewer">{{$lang.videos.screenshots_mgmt_field_click_mode_viewer}}</option>
											{{if $can_manage_screenshots==1}}
												<option value="select">{{$lang.videos.screenshots_mgmt_field_click_mode_select}}</option>
												<option value="main">{{$lang.videos.screenshots_mgmt_field_click_mode_main}}</option>
											{{/if}}
										</select>
									</span>
									{{if $can_manage_screenshots==1}}
										<span class="de_lv_pair"><input type="checkbox" class="de_switcher de_img_list_delete_all" autocomplete="off"/><label>{{$lang.videos.screenshots_mgmt_field_select_all}}</label></span>
										<span class="de_lv_pair"><input type="checkbox" class="de_switcher de_img_list_do_not_fade" autocomplete="off" value="1"/><label>{{$lang.videos.screenshots_mgmt_field_select_do_not_fade}}</label></span>
									{{/if}}
								</div>
								<div class="de_img_list_main">
									<input type="hidden" name="screen_group" value="{{$smarty.post.screen_group}}"/>
									{{assign var="pos" value=1}}
									{{section name="screenshots" start="0" step="1" loop=$smarty.post.screen_amount}}
										<div class="de_img_list_item {{if $smarty.post.screen_main==$pos}}main{{/if}}">
											<a class="de_img_list_thumb" href="?action=screenshot_source&amp;group_id={{$smarty.post.screen_group}}&amp;item_id={{$smarty.post.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}">
												{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_auto}}
												{{if $smarty.post.info_screenshots[$pos].type=='uploaded'}}
													{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_uploaded}}
												{{/if}}
												<img src="{{$smarty.post.screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$smarty.post.screen_amount}}, {{$screenshot_type}}"/>
												<i>{{$screenshot_type}}</i>
											</a>
											{{if $can_manage_screenshots==1}}
												<div class="de_img_list_options basic">
													<span class="de_lv_pair"><input type="radio" name="screen_main" value="{{$pos}}" {{if $smarty.post.screen_main==$pos}}checked{{/if}} {{if $can_manage_screenshots==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_main}}</label></span>
													<span class="de_lv_pair"><input type="checkbox" name="screen_delete[]" value="{{$pos}}" autocomplete="off" {{if $can_manage_screenshots==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_delete}}</label></span>
												</div>
											{{/if}}
											{{if is_array($smarty.post.rotator_screenshots) || $smarty.post.info_screenshots_score=='true'}}
												<div class="de_img_list_options">
													{{if is_array($smarty.post.rotator_screenshots)}}
														{{$lang.videos.screenshots_mgmt_field_ctr}}: {{$smarty.post.rotator_screenshots[$pos].ctr|default:0|number_format:2}}
														&nbsp;/&nbsp;
														{{$lang.videos.screenshots_mgmt_field_clicks}}: {{$smarty.post.rotator_screenshots[$pos].clicks|default:0}}
														{{if $smarty.post.info_screenshots_score=='true'}}&nbsp;/&nbsp;{{/if}}
													{{/if}}
													{{if $smarty.post.info_screenshots_score=='true'}}
														{{$lang.videos.screenshots_mgmt_field_score}}: {{$smarty.post.info_screenshots[$pos].score|default:0|number_format:4}}
													{{/if}}
												</div>
											{{/if}}
										</div>
										{{assign var="pos" value=$pos+1}}
									{{/section}}
								</div>
							</div>
						</td>
					</tr>
				{{else}}
					<tr>
						<td class="de_control" colspan="4">{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_none}}</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.get.action=='change' && count($smarty.post.neuroscore_data) > 0}}
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_separator" colspan="4"><h2>{{$lang.plugins.neuroscore.title}}</h2></td>
				</tr>
				<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.plugins.neuroscore.field_tasks}}</td>
					<td class="de_control" colspan="3">
						{{foreach from=$smarty.post.neuroscore_data item="operation" name="neuroscore"}}
							<a href="https://neuroscore.ai/app/#/tasks/result/{{$operation.operation_task_id}}">{{if $operation.operation_type_id==1}}{{$lang.plugins.neuroscore.divider_score}}{{elseif $operation.operation_type_id==3}}{{$lang.plugins.neuroscore.divider_title}}{{elseif $operation.operation_type_id==4}}{{$lang.plugins.neuroscore.divider_categories}}{{elseif $operation.operation_type_id==5}}{{$lang.plugins.neuroscore.divider_models}}{{/if}}</a>{{if !$smarty.foreach.neuroscore.last}},{{/if}}
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_VIDEO_FIELD_1==1 || $options.ENABLE_VIDEO_FIELD_2==1 || $options.ENABLE_VIDEO_FIELD_3==1 || $options.ENABLE_VIDEO_FLAG_1==1 || $options.ENABLE_VIDEO_FLAG_2==1 || $options.ENABLE_VIDEO_FLAG_3==1}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_separator" colspan="4"><h2>{{$lang.videos.video_divider_customization}}</h2></td>
				</tr>
				{{if $options.ENABLE_VIDEO_FIELD_1==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.VIDEO_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom1" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_VIDEO_FIELD_2==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.VIDEO_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom2" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_VIDEO_FIELD_3==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$options.VIDEO_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="3">
							<textarea name="custom3" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_VIDEO_FLAG_1==1 || $options.ENABLE_VIDEO_FLAG_2==1 || $options.ENABLE_VIDEO_FLAG_3==1}}
					<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
						<td class="de_label">{{$lang.videos.video_field_custom_flags}}</td>
						<td class="de_control" colspan="3">
							{{if $options.ENABLE_VIDEO_FLAG_1==1}}
								<input type="hidden" name="af_custom1" value="0"/>
							{{/if}}
							{{if $options.ENABLE_VIDEO_FLAG_2==1}}
								<input type="hidden" name="af_custom2" value="0"/>
							{{/if}}
							{{if $options.ENABLE_VIDEO_FLAG_3==1}}
								<input type="hidden" name="af_custom3" value="0"/>
							{{/if}}
							{{if $options.ENABLE_VIDEO_FLAG_1==1}}
								<span class="de_lv_pair"><input type="checkbox" name="af_custom1" value="{{$smarty.post.af_custom1|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom1>0}}checked{{/if}}/><label>{{$options.VIDEO_FLAG_1_NAME}} {{if $smarty.post.af_custom1>1}}({{$smarty.post.af_custom1}}){{/if}}</label></span>
							{{/if}}
							{{if $options.ENABLE_VIDEO_FLAG_2==1}}
								<span class="de_lv_pair"><input type="checkbox" name="af_custom2" value="{{$smarty.post.af_custom2|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom2>0}}checked{{/if}}/><label>{{$options.VIDEO_FLAG_2_NAME}} {{if $smarty.post.af_custom2>1}}({{$smarty.post.af_custom2}}){{/if}}</label></span>
							{{/if}}
							{{if $options.ENABLE_VIDEO_FLAG_3==1}}
								<span class="de_lv_pair"><input type="checkbox" name="af_custom3" value="{{$smarty.post.af_custom3|max:1}}" {{if $can_edit_custom==1}}class="preserve_editing"{{/if}} {{if $smarty.post.af_custom3>0}}checked{{/if}}/><label>{{$options.VIDEO_FLAG_3_NAME}} {{if $smarty.post.af_custom3>1}}({{$smarty.post.af_custom3}}){{/if}}</label></span>
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

{{if in_array('videos|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('videos|edit_status',$smarty.session.permissions)}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
{{/if}}
{{if in_array('videos|mass_edit',$smarty.session.permissions)}}
	{{assign var="can_mass_edit" value=1}}
{{else}}
	{{assign var="can_mass_edit" value=0}}
{{/if}}
{{if in_array('videos|export',$smarty.session.permissions)}}
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
						<input type="text" name="se_ids" value="{{$smarty.session.save.$page_name.se_ids}}" placeholder="{{$lang.videos.video_field_ids}}..."/>
					</div>
				{{elseif $smarty.session.save.$page_name.se_file_ids!=''}}
					<div class="dgf_filter">
						<select name="se_file_ids">
							<option value="">{{$lang.videos.video_field_ids}}...</option>
							<option value="{{$smarty.session.save.$page_name.se_file_ids}}" selected>{{$smarty.session.save.$page_name.se_file_ids}}</option>
						</select>
					</div>
				{{/if}}
				<div class="dgf_filter">
					<select name="se_status_id">
						<option value="">{{$lang.videos.video_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.videos.video_field_status_disabled}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.videos.video_field_status_active}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected{{/if}}>{{$lang.videos.video_field_status_error}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected{{/if}}>{{$lang.videos.video_field_status_in_process}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected{{/if}}>{{$lang.videos.video_field_status_deleting}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_status_id=='5'}}selected{{/if}}>{{$lang.videos.video_field_status_deleted}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_load_type_id">
						<option value="">{{$lang.videos.video_field_load_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_load_type_id=='1'}}selected{{/if}}>{{$lang.videos.video_field_load_type_file}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_load_type_id=='2'}}selected{{/if}}>{{$lang.videos.video_field_load_type_url}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_load_type_id=='3'}}selected{{/if}}>{{$lang.videos.video_field_load_type_embed}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_load_type_id=='5'}}selected{{/if}}>{{$lang.videos.video_field_load_type_pseudo}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_is_private">
						<option value="">{{$lang.videos.video_field_type}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_is_private=='0'}}selected{{/if}}>{{$lang.videos.video_field_type_public}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_is_private=='1'}}selected{{/if}}>{{$lang.videos.video_field_type_private}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_is_private=='2'}}selected{{/if}}>{{$lang.videos.video_field_type_premium}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_access_level_id">
						<option value="">{{$lang.videos.video_field_access_level}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_access_level_id=='0'}}selected{{/if}}>{{$lang.videos.video_field_access_level_inherit}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_access_level_id=='1'}}selected{{/if}}>{{$lang.videos.video_field_access_level_all}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_access_level_id=='2'}}selected{{/if}}>{{$lang.videos.video_field_access_level_members}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_access_level_id=='3'}}selected{{/if}}>{{$lang.videos.video_field_access_level_premium}}</option>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.videos.video_field_user}}..."/>
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
						<input type="text" name="se_content_source" value="{{$smarty.session.save.$page_name.se_content_source}}" placeholder="{{$lang.videos.video_field_content_source}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvds</span>
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_dvd_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_dvd" value="{{$smarty.session.save.$page_name.se_dvd}}" placeholder="{{$lang.videos.video_field_dvd}}..."/>
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
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.videos.video_field_category}}..."/>
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
						<input type="text" name="se_model" value="{{$smarty.session.save.$page_name.se_model}}" placeholder="{{$lang.videos.video_field_model}}..."/>
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
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.videos.video_field_tag}}..."/>
					</div>
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
						<input type="text" name="se_content_source_group" value="{{$smarty.session.save.$page_name.se_content_source_group}}" placeholder="{{$lang.videos.video_field_content_source_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvd_groups</span>
							{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_dvd_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_dvd_group" value="{{$smarty.session.save.$page_name.se_dvd_group}}" placeholder="{{$lang.videos.video_field_dvd_group}}..."/>
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
						<input type="text" name="se_category_group" value="{{$smarty.session.save.$page_name.se_category_group}}" placeholder="{{$lang.videos.video_field_category_group}}..."/>
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
						<input type="text" name="se_model_group" value="{{$smarty.session.save.$page_name.se_model_group}}" placeholder="{{$lang.videos.video_field_model_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=playlists</span>
							{{if in_array('playlists|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_playlist_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_playlist" value="{{$smarty.session.save.$page_name.se_playlist}}" placeholder="{{$lang.videos.video_field_playlist}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.videos.video_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_flag_id">
						<option value="">{{$lang.common.dg_filter_flag}}...</option>
						{{foreach item="item_flag" from=$list_flags_videos|smarty:nodefaults}}
							<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected{{/if}}>{{$item_flag.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/title" {{if $smarty.session.save.$page_name.se_field=="empty/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_title}}</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_description}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_rating}}</option>
						<option value="empty/video_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/video_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_visits}}</option>
						<option value="empty/video_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="empty/video_viewed_unique"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_unique_visits}}</option>
						<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_tokens_cost}}</option>
						<option value="empty/content_source" {{if $smarty.session.save.$page_name.se_field=="empty/content_source"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_content_source}}</option>
						<option value="empty/dvd" {{if $smarty.session.save.$page_name.se_field=="empty/dvd"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_dvd}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_categories}}</option>
						<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_models}}</option>
						<option value="empty/admin" {{if $smarty.session.save.$page_name.se_field=="empty/admin"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_admin}}</option>
						<option value="empty/admin_flag" {{if $smarty.session.save.$page_name.se_field=="empty/admin_flag"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_admin_flag}}</option>
						<option value="empty/gallery_url" {{if $smarty.session.save.$page_name.se_field=="empty/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_gallery_url}}</option>
						<option value="empty/comments" {{if $smarty.session.save.$page_name.se_field=="empty/comments"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_comments}}</option>
						<option value="empty/favourites" {{if $smarty.session.save.$page_name.se_field=="empty/favourites"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_favourites}}</option>
						<option value="empty/purchases" {{if $smarty.session.save.$page_name.se_field=="empty/purchases"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_purchases}}</option>
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="VIDEO_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="af_custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="VIDEO_FLAG_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FLAG_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						<option value="filled/title" {{if $smarty.session.save.$page_name.se_field=="filled/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_title}}</option>
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_description}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_rating}}</option>
						<option value="filled/video_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/video_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_visits}}</option>
						<option value="filled/video_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="filled/video_viewed_unique"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_unique_visits}}</option>
						<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_tokens_cost}}</option>
						<option value="filled/content_source" {{if $smarty.session.save.$page_name.se_field=="filled/content_source"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_content_source}}</option>
						<option value="filled/dvd" {{if $smarty.session.save.$page_name.se_field=="filled/dvd"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_dvd}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_categories}}</option>
						<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_models}}</option>
						<option value="filled/admin" {{if $smarty.session.save.$page_name.se_field=="filled/admin"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_admin}}</option>
						<option value="filled/admin_flag" {{if $smarty.session.save.$page_name.se_field=="filled/admin_flag"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_admin_flag}}</option>
						<option value="filled/gallery_url" {{if $smarty.session.save.$page_name.se_field=="filled/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_gallery_url}}</option>
						<option value="filled/comments" {{if $smarty.session.save.$page_name.se_field=="filled/comments"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_comments}}</option>
						<option value="filled/favourites" {{if $smarty.session.save.$page_name.se_field=="filled/favourites"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_favourites}}</option>
						<option value="filled/purchases" {{if $smarty.session.save.$page_name.se_field=="filled/purchases"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_purchases}}</option>
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="VIDEO_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="4"}}
							{{assign var="custom_field_id" value="af_custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="VIDEO_FLAG_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FLAG_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_resolution_type">
						<option value="">{{$lang.videos.video_field_resolution_type}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_resolution_type=='0'}}selected{{/if}}>SD</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_resolution_type=='1'}}selected{{/if}}>HD</option>
						<option value="101" {{if $smarty.session.save.$page_name.se_resolution_type=='101'}}selected{{/if}}>HD+</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_resolution_type=='2'}}selected{{/if}}>FHD</option>
						<option value="102" {{if $smarty.session.save.$page_name.se_resolution_type=='102'}}selected{{/if}}>FHD+</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_resolution_type=='4'}}selected{{/if}}>4K</option>
						<option value="104" {{if $smarty.session.save.$page_name.se_resolution_type=='104'}}selected{{/if}}>4K+</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_resolution_type=='5'}}selected{{/if}}>5K</option>
						<option value="105" {{if $smarty.session.save.$page_name.se_resolution_type=='105'}}selected{{/if}}>5K+</option>
						<option value="6" {{if $smarty.session.save.$page_name.se_resolution_type=='6'}}selected{{/if}}>6K</option>
						<option value="106" {{if $smarty.session.save.$page_name.se_resolution_type=='106'}}selected{{/if}}>6K+</option>
						<option value="8" {{if $smarty.session.save.$page_name.se_resolution_type=='8'}}selected{{/if}}>8K</option>
						<option value="108" {{if $smarty.session.save.$page_name.se_resolution_type=='108'}}selected{{/if}}>8K+</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_show_id">
						<option value="">{{$lang.videos.video_filter_other}}...</option>
						<option value="is_vertical" {{if $smarty.session.save.$page_name.se_show_id=='is_vertical'}}selected{{/if}}>{{$lang.videos.video_filter_other_is_vertical}}</option>
						<option value="is_horizontal" {{if $smarty.session.save.$page_name.se_show_id=='is_horizontal'}}selected{{/if}}>{{$lang.videos.video_filter_other_is_horizontal}}</option>
						<option value="15" {{if $smarty.session.save.$page_name.se_show_id==15}}selected{{/if}}>{{$lang.videos.video_filter_other_from_admin}}</option>
						<option value="16" {{if $smarty.session.save.$page_name.se_show_id==16}}selected{{/if}}>{{$lang.videos.video_filter_other_from_website}}</option>
						<option value="17" {{if $smarty.session.save.$page_name.se_show_id==17}}selected{{/if}}>{{$lang.videos.video_filter_other_from_webmasters}}</option>
						<option value="18" {{if $smarty.session.save.$page_name.se_show_id==18}}selected{{/if}}>{{$lang.videos.video_filter_other_from_feed}}</option>
						<option value="19" {{if $smarty.session.save.$page_name.se_show_id==19}}selected{{/if}}>{{$lang.videos.video_filter_other_from_grabbers}}</option>
						<option value="20" {{if $smarty.session.save.$page_name.se_show_id==20}}selected{{/if}}>{{$lang.videos.video_filter_other_from_import}}</option>
						<option value="25" {{if $smarty.session.save.$page_name.se_show_id==25}}selected{{/if}}>{{$lang.videos.video_filter_other_from_plugins}}</option>
						<option value="21" {{if $smarty.session.save.$page_name.se_show_id==21}}selected{{/if}}>{{$lang.videos.video_filter_other_main_screen_1}}</option>
						<option value="22" {{if $smarty.session.save.$page_name.se_show_id==22}}selected{{/if}}>{{$lang.videos.video_filter_other_main_screen_not_1}}</option>
						<option value="23" {{if $smarty.session.save.$page_name.se_show_id==23}}selected{{/if}}>{{$lang.videos.video_filter_other_rotation_finished}}</option>
						<option value="24" {{if $smarty.session.save.$page_name.se_show_id==24}}selected{{/if}}>{{$lang.videos.video_filter_other_rotation_not_finished}}</option>
						{{foreach item="item_format" from=$list_formats_videos|smarty:nodefaults}}
							<option value="wf/{{$item_format.postfix}}" {{if $smarty.session.save.$page_name.se_show_id=="wf/`$item_format.postfix`"}}selected{{/if}}>{{$lang.videos.video_filter_other_format_w|replace:"%1%":$item_format.title}}</option>
						{{/foreach}}
						{{foreach item="item_format" from=$list_formats_videos|smarty:nodefaults}}
							<option value="wof/{{$item_format.postfix}}" {{if $smarty.session.save.$page_name.se_show_id=="wof/`$item_format.postfix`"}}selected{{/if}}>{{$lang.videos.video_filter_other_format_wo|replace:"%1%":$item_format.title}}</option>
						{{/foreach}}
						<option value="wq/360" {{if $smarty.session.save.$page_name.se_show_id=="wq/360"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"360p"}}</option>
						<option value="wq/480" {{if $smarty.session.save.$page_name.se_show_id=="wq/480"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"480p"}}</option>
						<option value="wq/720" {{if $smarty.session.save.$page_name.se_show_id=="wq/720"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"720p"}}</option>
						<option value="wq/1080" {{if $smarty.session.save.$page_name.se_show_id=="wq/1080"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"1080p"}}</option>
						<option value="wq/2160" {{if $smarty.session.save.$page_name.se_show_id=="wq/2160"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"2160p"}}</option>
						<option value="woq/360" {{if $smarty.session.save.$page_name.se_show_id=="woq/360"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"360p"}}</option>
						<option value="woq/480" {{if $smarty.session.save.$page_name.se_show_id=="woq/480"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"480p"}}</option>
						<option value="woq/720" {{if $smarty.session.save.$page_name.se_show_id=="woq/720"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"720p"}}</option>
						<option value="woq/1080" {{if $smarty.session.save.$page_name.se_show_id=="woq/1080"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"1080p"}}</option>
						<option value="woq/2160" {{if $smarty.session.save.$page_name.se_show_id=="woq/2160"}}selected{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"2160p"}}</option>
						{{foreach item="item_lang" from=$list_languages|smarty:nodefaults}}
							<option value="wl/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wl/`$item_lang.code`"}}selected{{/if}}>{{$lang.videos.video_filter_other_language_w|replace:"%1%":$item_lang.title}}</option>
						{{/foreach}}
						{{foreach item="item_lang" from=$list_languages|smarty:nodefaults}}
							<option value="wol/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wol/`$item_lang.code`"}}selected{{/if}}>{{$lang.videos.video_filter_other_language_wo|replace:"%1%":$item_lang.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_feed_id">
						<option value="">{{$lang.videos.video_filter_feed}}...</option>
						{{foreach item="item" from=$list_feeds_import|smarty:nodefaults}}
							<option value="{{$item.feed_id}}" {{if $smarty.session.save.$page_name.se_feed_id==$item.feed_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_storage_group_id">
						<option value="">{{$lang.videos.video_field_server_group}}...</option>
						{{foreach item="item" from=$list_server_groups|smarty:nodefaults}}
							<option value="{{$item.group_id}}" {{if $smarty.session.save.$page_name.se_storage_group_id==$item.group_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_format_video_group_id">
						<option value="">{{$lang.videos.video_field_format_group}}...</option>
						{{foreach item="item" from=$list_formats_videos_groups|smarty:nodefaults}}
							<option value="{{$item.format_video_group_id}}" {{if $smarty.session.save.$page_name.se_format_video_group_id==$item.format_video_group_id}}selected{{/if}}>{{$item.title}}</option>
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
						<option value="">{{$lang.videos.video_field_admin}}...</option>
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
					<input type="text" name="se_duration_from" value="{{if $smarty.session.save.$page_name.se_duration_from>0}}{{$smarty.session.save.$page_name.se_duration_from}}{{/if}}" placeholder="{{$lang.videos.video_filter_duration_from}}...">
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_duration_to" value="{{if $smarty.session.save.$page_name.se_duration_to>0}}{{$smarty.session.save.$page_name.se_duration_to}}{{/if}}" placeholder="{{$lang.videos.video_filter_duration_to}}...">
				</div>
				<div class="dgf_filter">
					<select name="se_has_errors">
						<option value="">{{$lang.videos.video_field_has_errors}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_has_errors=='1'}}selected{{/if}}>{{$lang.videos.video_field_has_errors_1}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_has_errors=='10'}}selected{{/if}}>{{$lang.videos.video_field_has_errors_10}}</option>
						<option value="100" {{if $smarty.session.save.$page_name.se_has_errors=='100'}}selected{{/if}}>{{$lang.videos.video_field_has_errors_100}}</option>
						<option value="1000" {{if $smarty.session.save.$page_name.se_has_errors=='1000'}}selected{{/if}}>{{$lang.videos.video_field_has_errors_1000}}</option>
						<option value="10000" {{if $smarty.session.save.$page_name.se_has_errors=='10000'}}selected{{/if}}>{{$lang.videos.video_field_has_errors_10000}}</option>
					</select>
				</div>
				{{if $neuroscore_enabled=='true'}}
					<div class="dgf_filter">
						<select name="se_neuroscore">
							<option value="">{{$lang.plugins.neuroscore.title}}...</option>
							<optgroup label="{{$lang.plugins.neuroscore.divider_score}}">
								<option value="score_missing" {{if $smarty.session.save.$page_name.se_neuroscore=='score_missing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_score}} - {{$lang.plugins.neuroscore.field_status_missing}}</option>
								<option value="score_postponed" {{if $smarty.session.save.$page_name.se_neuroscore=='score_postponed'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_score}} - {{$lang.plugins.neuroscore.field_status_postponed}}</option>
								<option value="score_processing" {{if $smarty.session.save.$page_name.se_neuroscore=='score_processing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_score}} - {{$lang.plugins.neuroscore.field_status_processing}}</option>
								<option value="score_finished" {{if $smarty.session.save.$page_name.se_neuroscore=='score_finished'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_score}} - {{$lang.plugins.neuroscore.field_status_finished}}</option>
							</optgroup>
							<optgroup label="{{$lang.plugins.neuroscore.divider_title}}">
								<option value="title_missing" {{if $smarty.session.save.$page_name.se_neuroscore=='title_missing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_title}} - {{$lang.plugins.neuroscore.field_status_missing}}</option>
								<option value="title_postponed" {{if $smarty.session.save.$page_name.se_neuroscore=='title_postponed'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_title}} - {{$lang.plugins.neuroscore.field_status_postponed}}</option>
								<option value="title_processing" {{if $smarty.session.save.$page_name.se_neuroscore=='title_processing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_title}} - {{$lang.plugins.neuroscore.field_status_processing}}</option>
								<option value="title_finished" {{if $smarty.session.save.$page_name.se_neuroscore=='title_finished'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_title}} - {{$lang.plugins.neuroscore.field_status_finished}}</option>
							</optgroup>
							<optgroup label="{{$lang.plugins.neuroscore.divider_categories}}">
								<option value="categories_missing" {{if $smarty.session.save.$page_name.se_neuroscore=='categories_missing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_categories}} - {{$lang.plugins.neuroscore.field_status_missing}}</option>
								<option value="categories_postponed" {{if $smarty.session.save.$page_name.se_neuroscore=='categories_postponed'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_categories}} - {{$lang.plugins.neuroscore.field_status_postponed}}</option>
								<option value="categories_processing" {{if $smarty.session.save.$page_name.se_neuroscore=='categories_processing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_categories}} - {{$lang.plugins.neuroscore.field_status_processing}}</option>
								<option value="categories_finished" {{if $smarty.session.save.$page_name.se_neuroscore=='categories_finished'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_categories}} - {{$lang.plugins.neuroscore.field_status_finished}}</option>
							</optgroup>
							<optgroup label="{{$lang.plugins.neuroscore.divider_models}}">
								<option value="models_missing" {{if $smarty.session.save.$page_name.se_neuroscore=='models_missing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_models}} - {{$lang.plugins.neuroscore.field_status_missing}}</option>
								<option value="models_postponed" {{if $smarty.session.save.$page_name.se_neuroscore=='models_postponed'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_models}} - {{$lang.plugins.neuroscore.field_status_postponed}}</option>
								<option value="models_processing" {{if $smarty.session.save.$page_name.se_neuroscore=='models_processing'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_models}} - {{$lang.plugins.neuroscore.field_status_processing}}</option>
								<option value="models_finished" {{if $smarty.session.save.$page_name.se_neuroscore=='models_finished'}}selected{{/if}}>{{$lang.plugins.neuroscore.divider_models}} - {{$lang.plugins.neuroscore.field_status_finished}}</option>
							</optgroup>
						</select>
					</div>
				{{/if}}
				{{if $digiregs_enabled=='true'}}
					<div class="dgf_filter">
						<select name="se_digiregs_copyright">
							<option value="">{{$lang.videos.video_field_digiregs_copyright}}...</option>
							<option value="copyright_applied" {{if $smarty.session.save.$page_name.se_digiregs_copyright=='copyright_applied'}}selected{{/if}}>{{$lang.videos.video_field_digiregs_copyright_applied}}</option>
							<option value="copyright_not_applied" {{if $smarty.session.save.$page_name.se_digiregs_copyright=='copyright_not_applied'}}selected{{/if}}>{{$lang.videos.video_field_digiregs_copyright_not_applied}}</option>
							<option value="copyright_empty" {{if $smarty.session.save.$page_name.se_digiregs_copyright=='copyright_empty'}}selected{{/if}}>{{$lang.videos.video_field_digiregs_copyright_empty}}</option>
							<option value="copyright_studio" {{if $smarty.session.save.$page_name.se_digiregs_copyright=='copyright_studio'}}selected{{/if}}>{{$lang.videos.video_field_digiregs_copyright_studio}}</option>
							<option value="copyright_watermark" {{if $smarty.session.save.$page_name.se_digiregs_copyright=='copyright_watermark'}}selected{{/if}}>{{$lang.videos.video_field_digiregs_copyright_watermark}}</option>
						</select>
					</div>
				{{/if}}
				<div class="dgf_filter">
					<select name="se_locked">
						<option value="">{{$lang.videos.video_field_lock_website}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_locked=='yes'}}selected{{/if}}>{{$lang.videos.video_field_lock_website_locked}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_locked=='no'}}selected{{/if}}>{{$lang.videos.video_field_lock_website_unlocked}}</option>
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
								{{if $item.status_id==0}}
									<span class="js_params">
										<span class="js_param">inactive=true</span>
									</span>
								{{/if}}
							</td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							{{if $item.status_id==4}}
								<td></td>
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
												{{if $item.load_type_id==5}}
													<span class="js_param">preview_hide=true</span>
												{{/if}}
											{{else}}
												<span class="js_param">activate_hide=true</span>
												<span class="js_param">deactivate_hide=true</span>
												<span class="js_param">mark_reviewed_hide=true</span>
												<span class="js_param">preview_hide=true</span>
												<span class="js_param">validate_hide=true</span>
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
						<span class="js_param">title={{$lang.videos.video_action_restart}}</span>
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
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=1&amp;object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-comment</span>
						<span class="js_param">subicon=action-add</span>
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
					<span class="js_param">href=?action=video_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.videos.video_action_view_log}}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				{{if in_array('system|background_tasks',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_background_tasks.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.videos.video_action_view_tasks}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-task</span>
						<span class="js_param">subicon=action-search</span>
					</li>
				{{/if}}
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-audit</span>
						<span class="js_param">subicon=action-search</span>
					</li>
				{{/if}}
				{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=stats_videos.php?no_filter=true&amp;se_group_by=date&amp;se_id=${id}</span>
						<span class="js_param">title={{$lang.videos.video_action_view_stats}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-traffic</span>
						<span class="js_param">subicon=action-search</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=preview_video.php?video_id=${id}</span>
					<span class="js_param">title={{$lang.videos.video_action_preview}}</span>
					<span class="js_param">hide=${preview_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-player</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=video_validate&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.videos.video_action_validate}}</span>
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
								<option value="delete_failed">{{$lang.videos.video_batch_delete_failed|replace:"%1%":$failed_count}}</option>
							{{/if}}
							{{if $can_edit_status==1}}
								<option value="delete_and_activate">{{$lang.videos.video_batch_delete_and_activate|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_edit_status==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_status}}">
							<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
							<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
							<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed|replace:"%1%":'${count}'}}</option>
							{{if $can_delete==1}}
								<option value="activate_and_delete">{{$lang.videos.video_batch_activate_and_delete|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_mass_edit==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_massedit}}">
							<option value="mass_edit">{{$lang.videos.video_batch_mass_edit|replace:"%1%":'${count}'}}</option>
							{{if $total_num>0}}
								<option value="mass_edit_filtered">{{$lang.videos.video_batch_mass_edit_filtered|replace:"%1%":$total_num}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_restart==1 || $can_export==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_other}}">
							{{if $can_restart==1}}
								{{if $failed_count>0}}
									<option value="restart">{{$lang.videos.video_batch_restart_failed|replace:"%1%":$failed_count}}</option>
								{{/if}}
								<option value="inc_priority">{{$lang.videos.video_batch_inc_priority|replace:"%1%":'${count}'}}</option>
							{{/if}}
							{{if $can_export==1}}
								<option value="export">{{$lang.videos.video_batch_export|replace:"%1%":'${count}'}}</option>
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
					<span class="js_param">confirm={{$lang.videos.video_batch_activate_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_activate</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_delete_and_activate_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=mass_edit_filtered</span>
					<span class="js_param">requires_selection=false</span>
					{{if $mass_edit_all_count==$total_num}}
						<span class="js_param">destructive=true</span>
						<span class="js_param">confirm={{$lang.videos.video_batch_mass_edit_filtered_confirm|count_format:"%1%":$total_num}}</span>
					{{/if}}
				</li>
				<li class="js_params">
					<span class="js_param">value=restart</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_restart_failed_confirm|count_format:"%1%":$failed_count}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_failed</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_delete_failed_confirm|count_format:"%1%":$failed_count}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}