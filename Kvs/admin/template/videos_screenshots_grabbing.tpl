{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="video_screenshots_grabbing">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a> / <a href="videos.php?action=change&amp;item_id={{$data_video.video_id}}">{{if $data_video.title!=''}}{{$lang.videos.video_edit|replace:"%1%":$data_video.title}}{{else}}{{$lang.videos.video_edit|replace:"%1%":$data_video.video_id}}{{/if}}</a> / <a href="videos_screenshots.php?item_id={{$data_video.video_id}}">{{$lang.videos.screenshots_header_mgmt}}</a> / {{$lang.videos.screenshots_header_grabbing}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.screenshots_divider_grabbing_options}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.screenshots_grabbing_field_grab_from}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="source_file_id">
						{{if $source_file!=''}}
							<option value="" {{if $smarty.session.save.$page_name.source_file_id==0}}selected{{/if}}>{{$lang.videos.screenshots_grabbing_field_grab_from_source_file}} [{{$source_file.dimensions.0}}x{{$source_file.dimensions.1}}, {{$source_file.duration_string}}]</option>
						{{elseif ($data_video.load_type_id==2 || $data_video.load_type_id==3) && $data_video.file_url!=''}}
							<option value="" {{if $smarty.session.save.$page_name.source_file_id==0}}selected{{/if}}>{{$lang.videos.screenshots_grabbing_field_grab_from_source_file}} ({{$lang.videos.screenshots_grabbing_field_grab_from_download}})</option>
						{{/if}}
						{{assign var="timelined_format_classes" value=""}}
						{{foreach item="item" from=$formats|smarty:nodefaults}}
							<option value="{{$item.format_video_id}}" {{if $smarty.session.save.$page_name.source_file_id==$item.format_video_id}}selected{{/if}}>{{$item.title}} [{{$item.dimensions.0}}x{{$item.dimensions.1}}, {{$item.duration_string}}{{if $item.timeline_screen_amount>0}}, {{$lang.videos.screenshots_grabbing_field_grab_from_timelines|replace:"%1%":$item.timeline_screen_amount}}{{/if}}]</option>
							{{if $item.timeline_screen_amount>0}}
								{{assign var="timelined_format_classes" value="`$timelined_format_classes` source_file_id_`$item.format_video_id`"}}
							{{/if}}
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.screenshots_grabbing_field_method}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td><span class="de_lv_pair"><input id="method_use_timelines" type="radio" name="method" value="1" class="{{$timelined_format_classes}}" {{if $timelined_format_classes==''}}disabled{{/if}} {{if $smarty.session.save.$page_name.method==1 && $timelined_format_classes!=''}}checked{{/if}}/><label>{{$lang.videos.screenshots_grabbing_field_method_use_timeline_screenshots}}</label></span></td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="method_new_screenshots" type="radio" name="method" value="2" {{if $smarty.session.save.$page_name.method==2}}checked{{/if}}/><label>{{$lang.videos.screenshots_grabbing_field_method_new_screenshots}}:</label></span>
								<span>
									<input class="method_new_screenshots" type="text" name="interval" maxlength="32" size="3" value="{{$smarty.session.save.$page_name.interval}}"/> {{$lang.common.second_truncated}}
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_crop}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span>
									{{$lang.videos.screenshots_grabbing_field_screenshots_crop_left}}:
									<input type="text" name="screenshots_crop_left" maxlength="1000" size="5" value="{{$smarty.session.save.$page_name.screenshots_crop_left}}"/>
									<select name="screenshots_crop_left_unit">
										<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_left_unit==1}}selected{{/if}}>px</option>
										<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_left_unit==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.videos.screenshots_grabbing_field_screenshots_crop_top}}:
									<input type="text" name="screenshots_crop_top" maxlength="1000" size="5" value="{{$smarty.session.save.$page_name.screenshots_crop_top}}"/>
									<select name="screenshots_crop_top_unit">
										<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_top_unit==1}}selected{{/if}}>px</option>
										<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_top_unit==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.videos.screenshots_grabbing_field_screenshots_crop_right}}:
									<input type="text" name="screenshots_crop_right" maxlength="1000" size="5" value="{{$smarty.session.save.$page_name.screenshots_crop_right}}"/>
									<select name="screenshots_crop_right_unit">
										<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_right_unit==1}}selected{{/if}}>px</option>
										<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_right_unit==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.videos.screenshots_grabbing_field_screenshots_crop_bottom}}:
									<input type="text" name="screenshots_crop_bottom" maxlength="1000" size="5" value="{{$smarty.session.save.$page_name.screenshots_crop_bottom}}"/>
									<select name="screenshots_crop_bottom_unit">
										<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_bottom_unit==1}}selected{{/if}}>px</option>
										<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_bottom_unit==2}}selected{{/if}}>%</option>
									</select>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="screenshots_crop_trim" value="1" {{if $smarty.session.save.$page_name.screenshots_crop_trim==1}}checked{{/if}}/><label>{{$lang.videos.screenshots_grabbing_field_screenshots_crop_trim}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_offset}}</td>
				<td class="de_control">
					<input type="text" name="screenshots_offset" maxlength="9" size="5" value="{{$smarty.session.save.$page_name.screenshots_offset|default:"0"}}"/>
					<span class="de_hint">{{$lang.videos.screenshots_grabbing_field_screenshots_offset_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="slow_method" value="1"/><label>{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method_enabled}}</label></span>
					<span class="de_hint">{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_grabbing_field_display_size}}</td>
				<td class="de_control">
					<input type="text" name="display_size" maxlength="9" size="9" value="{{$smarty.session.save.$page_name.display_size}}"/>
					<span class="de_hint">{{$lang.videos.screenshots_grabbing_field_display_size_hint}}</span>
				</td>
			</tr>
			{{if $data_amount>0}}
				<tr>
					<td></td>
					<td class="de_control">
						<input type="submit" name="start_grabbing" value="{{$lang.videos.screenshots_grabbing_btn_restart}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.screenshots_divider_grabbing_images}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.videos.screenshots_divider_grabbing_images_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="2">
						<div class="de_img_list">
							<div class="de_img_list_main">
								{{assign var="pos" value=0}}
								{{section name="screenshots" start="0" step="1" loop=$data_amount}}
									<div class="de_img_list_item" data-grabbing-image-container>
										<a class="de_img_list_thumb">
											<img src="{{$data[$pos]}}?rnd={{$smarty.now}}" alt=""/>
											<i></i>
										</a>
										<div class="de_img_list_options">
											<select name="save_as_screenshot_{{$pos}}">
												<option value="">{{$lang.videos.screenshots_grabbing_images_field_save_as}}</option>
												{{section name="sp" start="0" step="1" loop=$data_video.screen_amount}}
													<option value="{{$smarty.section.sp.iteration}}">{{$lang.videos.screenshots_grabbing_images_field_save_as_screenshot|replace:"%1%":$smarty.section.sp.iteration}}</option>
												{{/section}}
												<option value="new">{{$lang.videos.screenshots_grabbing_images_field_save_as_new_screenshot}}</option>
											</select>
										</div>
									</div>
									{{assign var="pos" value=$pos+1}}
								{{/section}}
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="item_id" value="{{$data_video.video_id}}"/>
		{{if $data_amount==0}}
			<input type="hidden" name="action" value="start_grabbing"/>
			<input type="submit" name="save_default" value="{{$lang.videos.screenshots_grabbing_btn_start}}"/>
		{{else}}
			<input type="hidden" name="action" value="save_screenshots"/>
			<input type="hidden" name="grabbing_id" value="{{$smarty.request.grabbing_id}}"/>
			<input type="hidden" name="data_amount" value="{{$data_amount}}"/>
			<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>