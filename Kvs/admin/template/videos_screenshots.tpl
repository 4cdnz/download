{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

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

<form action="{{$page_name}}" method="post" class="de {{if $can_manage_screenshots==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="video_screenshots">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a> /
				<a href="videos.php?action=change&amp;item_id={{$data_video.video_id}}">{{if $data_video.title!=''}}{{$lang.videos.video_edit|replace:"%1%":$data_video.title}}{{else}}{{$lang.videos.video_edit|replace:"%1%":$data_video.video_id}}{{/if}}</a> /
				{{$lang.videos.screenshots_header_mgmt}}
			</h1>
			<div class="drop">
				<i class="icon icon-action-settings"></i><span>{{$lang.common.dg_actions}}</span>
				<ul>
					{{if $data_video.website_link!=''}}
						<li><a href="{{$data_video.website_link}}"><i class="icon icon-action-open"></i>{{$lang.common.dg_actions_website_link}}</a></li>
					{{/if}}
					<li><a href="videos.php?action=video_log&amp;item_id={{$data_video.video_id}}" rel="log"><i class="icon icon-action-log"></i>{{$lang.videos.video_action_view_log}}</a></li>
					{{if in_array('system|background_tasks',$smarty.session.permissions)}}
						<li><a href="log_background_tasks.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id={{$data_video.video_id}}"><i class="icon icon-type-task"></i>{{$lang.videos.video_action_view_tasks}}</a></li>
					{{/if}}
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li><a href="log_audit.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id={{$data_video.video_id}}"><i class="icon icon-type-audit"></i>{{$lang.common.dg_actions_additional_view_audit_log}}</a></li>
					{{/if}}
					{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
						<li><a href="stats_videos.php?no_filter=true&amp;se_group_by=date&amp;se_id={{$data_video.video_id}}"><i class="icon icon-type-traffic"></i>{{$lang.videos.video_action_view_stats}}</a></li>
					{{/if}}
					<li><a href="videos.php?action=video_validate&amp;item_id={{$data_video.video_id}}" rel="log"><i class="icon icon-type-system"></i>{{$lang.videos.video_action_validate}}</a></li>
				</ul>
			</div>
		</div>
		<table class="de_editor">
			<colgroup>
				<col/>
				<col/>
			</colgroup>
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
			<td class="de_separator" colspan="2"><h2>{{$lang.videos.screenshots_mgmt_divider_gui_control}}</h2></td>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_mgmt_field_display}}</td>
				<td class="de_control de_vis_sw_select">
					<span>
						<select name="group_id" class="de_switcher" data-switcher-params="item_id={{$data_video.video_id}}">
							<option value="1" {{if $group_id==1}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_overview}} ({{$overview_amount}})</option>
							<option value="2" {{if $group_id==2}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_timeline}} ({{$timeline_amount}})</option>
							<option value="3" {{if $group_id==3}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_posters}} ({{$poster_amount}})</option>
						</select>
					</span>
					<span class="group_id_1">
						<select name="overview_format_id" class="de_switcher" data-switcher-params="item_id={{$data_video.video_id}};group_id=1">
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="sources" {{if $smarty.session.save.$page_name.overview_format_id=='sources'}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
							{{foreach item="item" from=$list_formats_overview|smarty:nodefaults}}
								<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.overview_format_id==$item.format_screenshot_id}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</span>
					<span class="group_id_2 de_vis_sw_select">
						<select name="timeline_video_format_id" class="de_switcher" data-switcher-params="item_id={{$data_video.video_id}};group_id=2">
							<option value="">{{$lang.common.select_default_option}}</option>
							{{assign var="timeline_video_format_id_sw" value=""}}
							{{foreach item="item" from=$list_formats_videos_timelined|smarty:nodefaults}}
								{{assign var="timeline_video_format_id_sw" value="`$timeline_video_format_id_sw` timeline_video_format_id_`$item.format_video_id`"}}
								<option value="{{$item.format_video_id}}" {{if $smarty.session.save.$page_name.timeline_video_format_id==$item.format_video_id}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</span>
					<span class="group_id_2 {{$timeline_video_format_id_sw}}">
						<select name="timeline_format_id" class="de_switcher" data-switcher-params="item_id={{$data_video.video_id}};group_id=2;timeline_video_format_id={{$smarty.session.save.$page_name.timeline_video_format_id}}">
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="sources" {{if $smarty.session.save.$page_name.timeline_format_id=='sources'}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
							{{foreach item="item" from=$list_formats_timeline|smarty:nodefaults}}
								<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.timeline_format_id==$item.format_screenshot_id}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</span>
					<span class="group_id_3">
						<select name="poster_format_id" class="de_switcher" data-switcher-params="item_id={{$data_video.video_id}};group_id=3">
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="sources" {{if $smarty.session.save.$page_name.poster_format_id=='sources'}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
							{{foreach item="item" from=$list_formats_posters|smarty:nodefaults}}
								<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.poster_format_id==$item.format_screenshot_id}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</span>
					<span class="de_hint">{{$lang.videos.screenshots_mgmt_field_display_hint}}</span>
				</td>
			</tr>
			{{if $screen_amount>0}}
				<tr data-field-name="source_zip_file">
					<td class="de_label">{{$lang.videos.screenshots_mgmt_field_source_zip_file}}</td>
					<td class="de_control">
						<a href="?action=sources_zip&amp;item_id={{$data_video.video_id}}" rel="file">{{$data_video.dir|default:$data_video.video_id}}-sources.zip</a>
					</td>
				</tr>
			{{/if}}
			{{if $can_manage_screenshots==1 && ($group_id==1 || $group_id==3)}}
				<tr>
					<td class="de_label">{{$lang.videos.screenshots_mgmt_field_replace}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.screenshots_mgmt_field_replace}}</span>
								<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
								<span class="js_param">multiple=true</span>
							</div>
							<input type="text" name="replace_screenshots" maxlength="100"/>
							<input type="hidden" name="replace_screenshots_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.screenshots_mgmt_field_replace_hint}}</span>
					</td>
				</tr>
				{{if $group_id==1 && $grabbing_possible==1}}
					<tr data-field-name="manual_grabbing">
						<td class="de_label">{{$lang.videos.screenshots_mgmt_field_manual_grabbing}}</td>
						<td class="de_control">
							<a href="videos_screenshots_grabbing.php?item_id={{$data_video.video_id}}">{{$lang.videos.screenshots_mgmt_field_manual_grabbing_link}}</a>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2">
					<h2>
						{{if $group_id==1}}
							{{if $format_id=='sources'}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_overview_sources}}
							{{else}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_overview|replace:"%1%":$data_format.size}}
							{{/if}}
						{{elseif $group_id==2}}
							{{if $format_id=='sources'}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline_sources|replace:"%1%":$timeline_video_format_title}}
							{{else}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline|replace:"%1%":$data_format.size|replace:"%2%":$timeline_video_format_title}}
							{{/if}}
						{{elseif $group_id==3}}
							{{if $format_id=='sources'}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_sources}}
							{{else}}
								{{$lang.videos.screenshots_mgmt_divider_screenshots_posters|replace:"%1%":$data_format.size}}
							{{/if}}
						{{/if}}
					</h2>
				</td>
			</tr>
			{{if $screen_amount>0}}
				<tr>
					<td class="de_control" colspan="2">
						<div class="de_img_list">
							{{if $group_id==1 || $group_id==3}}
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
							{{/if}}
							<div class="de_img_list_main">
								{{assign var="pos" value=1}}
								{{section name="screenshots" start="0" step="1" loop=$screen_amount}}
									<div class="de_img_list_item {{if $screen_main==$pos}}main{{/if}}">
										<a class="de_img_list_thumb" href="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}">
											{{if $group_id==1}}
												{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_auto}}
												{{if $screenshots_data[$pos].type=='uploaded'}}
													{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_uploaded}}
												{{/if}}
												{{if $screen_url}}
													<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}, {{$screenshot_type}}"/>
												{{else}}
													<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
												{{/if}}
												<i>{{$screenshot_type}}</i>
											{{elseif $group_id==2}}
												{{if $screen_url}}
													<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{if $timeline_titles[$pos].text}}{{$timeline_titles[$pos].text}}{{else}}{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}{{/if}}"/>
												{{else}}
													<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{if $timeline_titles[$pos].text}}{{$timeline_titles[$pos].text}}{{else}}{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}{{/if}}"/>
												{{/if}}
												<i>{{$lang.videos.screenshots_mgmt_field_type_auto}}</i>
											{{elseif $group_id==3}}
												{{if $screen_url}}
													<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
												{{else}}
													<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
												{{/if}}
												<i>{{$lang.videos.screenshots_mgmt_field_type_uploaded}}</i>
											{{/if}}
										</a>
										{{if $group_id==1 || $group_id==3}}
											{{if $can_manage_screenshots==1}}
												<div class="de_img_list_options">
													<div class="de_fu">
														<div class="js_params">
															<span class="js_param">title={{if $group_id==1}}{{$lang.videos.screenshots_mgmt_file_title_screenshot|replace:"%1%":$pos}}{{elseif $group_id==3}}{{$lang.videos.screenshots_mgmt_file_title_poster|replace:"%1%":$pos}}{{/if}}</span>
															<span class="js_param">accept=jpg</span>
														</div>
														<input type="text" maxlength="100" name="file_{{$pos}}"/>
														<input type="hidden" name="file_{{$pos}}_hash"/>
														<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_replace}}"/>
													</div>
												</div>
												<div class="de_img_list_options basic">
													<span class="de_lv_pair"><input type="radio" name="main" value="{{$pos}}" {{if $screen_main==$pos}}checked{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_main}}</label></span>
													<span class="de_lv_pair"><input type="checkbox" name="delete[]" value="{{$pos}}" autocomplete="off"/><label>{{$lang.videos.screenshots_mgmt_field_delete}}</label></span>
												</div>
											{{/if}}
											{{if is_array($rotator_data) || $screenshots_data_score=='true'}}
												<div class="de_img_list_options">
													<span>
														{{if is_array($rotator_data)}}
															{{$lang.videos.screenshots_mgmt_field_ctr}}: {{$rotator_data[$pos].ctr|default:0|number_format:2}}
															&nbsp;/&nbsp;
															{{$lang.videos.screenshots_mgmt_field_clicks}}: {{$rotator_data[$pos].clicks|default:0}}
															{{if $screenshots_data_score=='true'}}&nbsp;/&nbsp;{{/if}}
														{{/if}}
														{{if $screenshots_data_score=='true'}}
															{{$lang.videos.screenshots_mgmt_field_score}}: {{$screenshots_data[$pos].score|default:0|number_format:4}}
														{{/if}}
													</span>
												</div>
											{{/if}}
										{{elseif $group_id==2}}
											<div class="de_img_list_options">
												<input type="text" name="title_{{$pos}}" size="10" value="{{$timeline_titles[$pos].text}}"/>
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
					<td class="de_control" colspan="2">
						{{if $group_id==2}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline_none}}
						{{elseif $group_id==3}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_none}}
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_status',$smarty.session.permissions) || in_array('videos|edit_admin_flag',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.screenshots_mgmt_divider_video_data}}</h2></td>
				</tr>
				{{if in_array('videos|edit_status',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.videos.screenshots_mgmt_field_status}}</td>
						<td class="de_control">
							<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $data_video.status_id=='1'}}checked{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_status_active}}</label></span>
						</td>
					</tr>
				{{/if}}
				{{if in_array('videos|edit_admin_flag',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.videos.screenshots_mgmt_field_admin_flag}}</td>
						<td class="de_control">
							<select name="admin_flag_id">
								<option value="0" {{if 0==$data_video.admin_flag_id}}selected{{/if}}>{{$lang.videos.screenshots_mgmt_field_admin_flag_reset}}</option>
								{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
									<option value="{{$item.flag_id}}" {{if $item.flag_id==$data_video.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
								{{/foreach}}
							</select>
						</td>
					</tr>
				{{/if}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_screenshots"/>
		<input type="hidden" name="item_id" value="{{$data_video.video_id}}"/>
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