{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.request.page=='general_settings'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_content">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.system_header}}</h1></div>
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
			{{if $smarty.session.userdata.is_superadmin==2}}
				<tr>
					<td class="de_label">{{$lang.settings.system_field_debug_logging}}</td>
					<td class="de_control">
						<input type="text" name="DEBUG_LOGGING" value="{{$data.DEBUG_LOGGING}}"/>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_file_upload_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_file_upload_local}}</td>
				<td class="de_control">
					<select name="FILE_UPLOAD_DISK_OPTION">
						<option value="public" {{if $data.FILE_UPLOAD_DISK_OPTION=='public'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_local_public}}</option>
						<option value="members" {{if $data.FILE_UPLOAD_DISK_OPTION=='members'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_local_members}}</option>
						<option value="admins" {{if $data.FILE_UPLOAD_DISK_OPTION=='admins'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_local_admins}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_file_upload_url}}</td>
				<td class="de_control">
					<select name="FILE_UPLOAD_URL_OPTION">
						<option value="public" {{if $data.FILE_UPLOAD_URL_OPTION=='public'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_url_public}}</option>
						<option value="members" {{if $data.FILE_UPLOAD_URL_OPTION=='members'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_url_members}}</option>
						<option value="admins" {{if $data.FILE_UPLOAD_URL_OPTION=='admins'}}selected{{/if}}>{{$lang.settings.system_field_file_upload_url_admins}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_file_upload_size_limit}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="FILE_UPLOAD_SIZE_LIMIT" maxlength="20" size="8" value="{{if $data.FILE_UPLOAD_SIZE_LIMIT>0}}{{$data.FILE_UPLOAD_SIZE_LIMIT}}{{/if}}"/>
						{{$lang.settings.system_field_file_upload_size_limit_units}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_file_upload_size_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_file_download_speed_limit}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="FILE_DOWNLOAD_SPEED_LIMIT" maxlength="20" size="8" value="{{if $data.FILE_DOWNLOAD_SPEED_LIMIT>0}}{{$data.FILE_DOWNLOAD_SPEED_LIMIT}}{{/if}}"/>
						{{$lang.settings.system_field_file_download_speed_limit_units}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_file_download_speed_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_images_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.system_divider_images_settings_hint|replace:"%1%":$lang.settings.common_screenshot_resize_option_fixed_size|replace:"%2%":$lang.settings.common_screenshot_resize_option_dyn_size|replace:"%3%":$lang.settings.common_screenshot_resize_option_dyn_height|replace:"%4%":$lang.settings.common_screenshot_resize_option_dyn_width}}</span>
				</td>
			</tr>
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_label de_required">{{$lang.settings.system_field_user_avatar_size}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="USER_AVATAR_SIZE" maxlength="20" size="8" value="{{$data.USER_AVATAR_SIZE}}"/>
							<select name="USER_AVATAR_TYPE">
								<option value="need_size" {{if $data.USER_AVATAR_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.USER_AVATAR_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.USER_AVATAR_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.USER_AVATAR_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						<span class="de_vis_sw_select">
							{{$lang.settings.common_screenshot2_option}}:
							<select name="USER_COVER_OPTION">
								<option value="0" {{if $data.USER_COVER_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
								<option value="1" {{if $data.USER_COVER_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
								<option value="2" {{if $data.USER_COVER_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
							</select>
							<span class="USER_COVER_OPTION_1 USER_COVER_OPTION_2">
								<input type="text" name="USER_COVER_SIZE" maxlength="20" size="8" value="{{$data.USER_COVER_SIZE}}"/>
								<select name="USER_COVER_TYPE">
									<option value="need_size" {{if $data.USER_COVER_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
									<option value="max_size" {{if $data.USER_COVER_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
									<option value="max_width" {{if $data.USER_COVER_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
									<option value="max_height" {{if $data.USER_COVER_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
								</select>
							</span>
						</span>
						<span class="de_hint">{{$lang.common.size_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_category_screenshot_size}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="CATEGORY_AVATAR_SIZE" maxlength="20" size="8" value="{{$data.CATEGORY_AVATAR_SIZE}}"/>
						<select name="CATEGORY_AVATAR_TYPE">
							<option value="need_size" {{if $data.CATEGORY_AVATAR_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.CATEGORY_AVATAR_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.CATEGORY_AVATAR_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.CATEGORY_AVATAR_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
					</span>
					<span class="de_vis_sw_select">
						{{$lang.settings.common_screenshot2_option}}:
						<select name="CATEGORY_AVATAR_OPTION">
							<option value="0" {{if $data.CATEGORY_AVATAR_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.CATEGORY_AVATAR_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.CATEGORY_AVATAR_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						<span class="CATEGORY_AVATAR_OPTION_1 CATEGORY_AVATAR_OPTION_2">
							<input type="text" name="CATEGORY_AVATAR_2_SIZE" maxlength="20" size="8" value="{{$data.CATEGORY_AVATAR_2_SIZE}}"/>
							<select name="CATEGORY_AVATAR_2_TYPE">
								<option value="need_size" {{if $data.CATEGORY_AVATAR_2_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
					</span>
					<span class="de_hint">{{$lang.common.size_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_cs_screenshot_size}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="CS_SCREENSHOT_1_SIZE" maxlength="20" size="8" value="{{$data.CS_SCREENSHOT_1_SIZE}}"/>
						<select name="CS_SCREENSHOT_1_TYPE">
							<option value="need_size" {{if $data.CS_SCREENSHOT_1_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.CS_SCREENSHOT_1_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.CS_SCREENSHOT_1_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.CS_SCREENSHOT_1_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
					</span>
					<span class="de_vis_sw_select">
						{{$lang.settings.common_screenshot2_option}}:
						<select name="CS_SCREENSHOT_OPTION">
							<option value="0" {{if $data.CS_SCREENSHOT_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.CS_SCREENSHOT_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.CS_SCREENSHOT_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						<span class="CS_SCREENSHOT_OPTION_1 CS_SCREENSHOT_OPTION_2">
							<input type="text" name="CS_SCREENSHOT_2_SIZE" maxlength="20" size="8" value="{{$data.CS_SCREENSHOT_2_SIZE}}"/>
							<select name="CS_SCREENSHOT_2_TYPE">
								<option value="need_size" {{if $data.CS_SCREENSHOT_2_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.CS_SCREENSHOT_2_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.CS_SCREENSHOT_2_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.CS_SCREENSHOT_2_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
					</span>
					<span class="de_hint">{{$lang.common.size_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_label de_required">{{$lang.settings.system_field_model_screenshot_size}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="MODELS_SCREENSHOT_1_SIZE" maxlength="20" size="8" value="{{$data.MODELS_SCREENSHOT_1_SIZE}}"/>
							<select name="MODELS_SCREENSHOT_1_TYPE">
								<option value="need_size" {{if $data.MODELS_SCREENSHOT_1_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						<span class="de_vis_sw_select">
							{{$lang.settings.common_screenshot2_option}}:
							<select name="MODELS_SCREENSHOT_OPTION">
								<option value="0" {{if $data.MODELS_SCREENSHOT_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
								<option value="1" {{if $data.MODELS_SCREENSHOT_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
								<option value="2" {{if $data.MODELS_SCREENSHOT_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
							</select>
							<span class="MODELS_SCREENSHOT_OPTION_1 MODELS_SCREENSHOT_OPTION_2">
								<input type="text" name="MODELS_SCREENSHOT_2_SIZE" maxlength="20" size="8" value="{{$data.MODELS_SCREENSHOT_2_SIZE}}"/>
								<select name="MODELS_SCREENSHOT_2_TYPE">
									<option value="need_size" {{if $data.MODELS_SCREENSHOT_2_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
									<option value="max_size" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
									<option value="max_width" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
									<option value="max_height" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
								</select>
							</span>
						</span>
						<span class="de_hint">{{$lang.common.size_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label de_required">{{$lang.settings.system_field_dvd_cover_size}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="DVD_COVER_1_SIZE" maxlength="20" size="8" value="{{$data.DVD_COVER_1_SIZE}}"/>
							<select name="DVD_COVER_1_TYPE">
								<option value="need_size" {{if $data.DVD_COVER_1_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.DVD_COVER_1_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.DVD_COVER_1_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.DVD_COVER_1_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						<span class="de_vis_sw_select">
							{{$lang.settings.common_screenshot2_option}}:
							<select name="DVD_COVER_OPTION">
								<option value="0" {{if $data.DVD_COVER_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
								<option value="1" {{if $data.DVD_COVER_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
								<option value="2" {{if $data.DVD_COVER_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
							</select>
							<span class="DVD_COVER_OPTION_1 DVD_COVER_OPTION_2">
								<input type="text" name="DVD_COVER_2_SIZE" maxlength="20" size="8" value="{{$data.DVD_COVER_2_SIZE}}"/>
								<select name="DVD_COVER_2_TYPE">
									<option value="need_size" {{if $data.DVD_COVER_2_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
									<option value="max_size" {{if $data.DVD_COVER_2_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
									<option value="max_width" {{if $data.DVD_COVER_2_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
									<option value="max_height" {{if $data.DVD_COVER_2_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
								</select>
							</span>
						</span>
						<span class="de_hint">{{$lang.common.size_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.settings.system_field_dvd_group_cover_size}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="DVD_GROUP_COVER_1_SIZE" maxlength="20" size="8" value="{{$data.DVD_GROUP_COVER_1_SIZE}}"/>
							<select name="DVD_GROUP_COVER_1_TYPE">
								<option value="need_size" {{if $data.DVD_GROUP_COVER_1_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						<span class="de_vis_sw_select">
							{{$lang.settings.common_screenshot2_option}}:
							<select name="DVD_GROUP_COVER_OPTION">
								<option value="0" {{if $data.DVD_GROUP_COVER_OPTION==0}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
								<option value="1" {{if $data.DVD_GROUP_COVER_OPTION==1}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
								<option value="2" {{if $data.DVD_GROUP_COVER_OPTION==2}}selected{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
							</select>
							<span class="DVD_GROUP_COVER_OPTION_1 DVD_GROUP_COVER_OPTION_2">
								<input type="text" name="DVD_GROUP_COVER_2_SIZE" maxlength="20" size="8" value="{{$data.DVD_GROUP_COVER_2_SIZE}}"/>
								<select name="DVD_GROUP_COVER_2_TYPE">
									<option value="need_size" {{if $data.DVD_GROUP_COVER_2_TYPE=='need_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
									<option value="max_size" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_size'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
									<option value="max_width" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_width'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
									<option value="max_height" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_height'}}selected{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
								</select>
							</span>
						</span>
						<span class="de_hint">{{$lang.common.size_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_categorization_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_tags_disable}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<input type="hidden" name="TAGS_DISABLE_ALL" value="0"/>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="TAGS_DISABLE_ALL" value="1" {{if $data.TAGS_DISABLE_ALL==1}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_new}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_new_hint}}</span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAGS_DISABLE_COMPOUND" value="1" {{if $data.TAGS_DISABLE_COMPOUND>0}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_compound}}</label></span>
								<span>
									<input type="text" name="TAGS_DISABLE_COMPOUND" class="ENABLE_TAGS_DISABLE_COMPOUND_on" value="{{if $data.TAGS_DISABLE_COMPOUND==0}}{{else}}{{$data.TAGS_DISABLE_COMPOUND}}{{/if}}" maxlength="5" size="5"/>
									{{$lang.settings.system_field_tags_disable_words}}
								</span>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_compound_hint}}</span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAGS_DISABLE_LENGTH_MIN" value="1" {{if $data.TAGS_DISABLE_LENGTH_MIN>0}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_size_min}}</label></span>
								<span>
									<input type="text" name="TAGS_DISABLE_LENGTH_MIN" class="ENABLE_TAGS_DISABLE_LENGTH_MIN_on" value="{{if $data.TAGS_DISABLE_LENGTH_MIN==0}}{{else}}{{$data.TAGS_DISABLE_LENGTH_MIN}}{{/if}}" maxlength="5" size="5"/>
									{{$lang.settings.system_field_tags_disable_characters}}
								</span>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_size_min_hint}}</span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAGS_DISABLE_LENGTH_MAX" value="1" {{if $data.TAGS_DISABLE_LENGTH_MAX>0}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_size_max}}</label></span>
								<span>
									<input type="text" name="TAGS_DISABLE_LENGTH_MAX" class="ENABLE_TAGS_DISABLE_LENGTH_MAX_on" value="{{if $data.TAGS_DISABLE_LENGTH_MAX==0}}{{else}}{{$data.TAGS_DISABLE_LENGTH_MAX}}{{/if}}" maxlength="5" size="5"/>
									{{$lang.settings.system_field_tags_disable_characters}}
								</span>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_size_max_hint}}</span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAGS_DISABLE_CHARACTERS" value="1" {{if $data.TAGS_DISABLE_CHARACTERS}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_contains}}</label></span>
								<input type="text" name="TAGS_DISABLE_CHARACTERS" class="ENABLE_TAGS_DISABLE_CHARACTERS_on" value="{{$data.TAGS_DISABLE_CHARACTERS}}" maxlength="1000" size="5"/>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_contains_hint}}</span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<input type="hidden" name="TAGS_DISABLE_LIST_ENABLED" value="0"/>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="TAGS_DISABLE_LIST_ENABLED" value="1" {{if $data.TAGS_DISABLE_LIST_ENABLED==1}}checked{{/if}}/><label>{{$lang.settings.system_field_tags_disable_list}}</label></span>
							</td>
						</tr>
						<tr class="TAGS_DISABLE_ALL_off">
							<td>
								<textarea name="TAGS_DISABLE_LIST" rows="3" class="TAGS_DISABLE_LIST_ENABLED_on">{{$data.TAGS_DISABLE_LIST}}</textarea>
								<span class="de_hint">{{$lang.settings.system_field_tags_disable_list_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_new_tags}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<input type="hidden" name="TAGS_FORCE_LOWERCASE" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="TAGS_FORCE_LOWERCASE" value="1" {{if $data.TAGS_FORCE_LOWERCASE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_new_tags_lowercase}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_new_tags_lowercase_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<input type="hidden" name="TAGS_FORCE_DISABLED" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="TAGS_FORCE_DISABLED" value="1" {{if $data.TAGS_FORCE_DISABLED==1}}checked{{/if}}/><label>{{$lang.settings.system_field_new_tags_deactivate}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_new_tags_deactivate_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_renamed_tags}}</td>
				<td class="de_control">
					<input type="hidden" name="TAGS_ADD_SYNONYMS_ON_RENAME" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="TAGS_ADD_SYNONYMS_ON_RENAME" value="1" {{if $data.TAGS_ADD_SYNONYMS_ON_RENAME==1}}checked{{/if}}/><label>{{$lang.settings.system_field_renamed_tags_add_synonyms}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_renamed_tags_add_synonyms_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_label">{{$lang.settings.system_field_rank_models}}</td>
					<td class="de_control">
						<select name="MODELS_RANK_BY">
							<option value="" {{if $data.MODELS_RANK_BY==''}}selected{{/if}}>{{$lang.settings.system_field_rank_by_disabled}}</option>
							<option value="rating" {{if $data.MODELS_RANK_BY=='rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_rating}}</option>
							<option value="model_viewed" {{if $data.MODELS_RANK_BY=='model_viewed'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_visits}}</option>
							<option value="comments_count" {{if $data.MODELS_RANK_BY=='comments_count'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_comments}}</option>
							<option value="subscribers_count" {{if $data.MODELS_RANK_BY=='subscribers_count'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_subscribers}}</option>
							<option value="total_videos" {{if $data.MODELS_RANK_BY=='total_videos'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos}}</option>
							<option value="avg_videos_rating" {{if $data.MODELS_RANK_BY=='avg_videos_rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos_rating}}</option>
							<option value="avg_videos_popularity" {{if $data.MODELS_RANK_BY=='avg_videos_popularity'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos_visits}}</option>
							<option value="total_albums" {{if $data.MODELS_RANK_BY=='total_albums'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums}}</option>
							<option value="avg_albums_rating" {{if $data.MODELS_RANK_BY=='avg_albums_rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums_rating}}</option>
							<option value="avg_albums_popularity" {{if $data.MODELS_RANK_BY=='avg_albums_popularity'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums_visits}}</option>
							<option value="added_date" {{if $data.MODELS_RANK_BY=='added_date'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_added_date}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_rank_models_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.system_field_rank_cs}}</td>
				<td class="de_control">
					<select name="CS_RANK_BY">
						<option value="" {{if $data.CS_RANK_BY==''}}selected{{/if}}>{{$lang.settings.system_field_rank_by_disabled}}</option>
						<option value="rating" {{if $data.CS_RANK_BY=='rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_rating}}</option>
						<option value="cs_viewed" {{if $data.CS_RANK_BY=='cs_viewed'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_visits}}</option>
						<option value="comments_count" {{if $data.CS_RANK_BY=='comments_count'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_comments}}</option>
						<option value="subscribers_count" {{if $data.CS_RANK_BY=='subscribers_count'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_subscribers}}</option>
						<option value="total_videos" {{if $data.CS_RANK_BY=='total_videos'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos}}</option>
						<option value="avg_videos_rating" {{if $data.CS_RANK_BY=='avg_videos_rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos_rating}}</option>
						<option value="avg_videos_popularity" {{if $data.CS_RANK_BY=='avg_videos_popularity'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_videos_visits}}</option>
						<option value="total_albums" {{if $data.CS_RANK_BY=='total_albums'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums}}</option>
						<option value="avg_albums_rating" {{if $data.CS_RANK_BY=='avg_albums_rating'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums_rating}}</option>
						<option value="avg_albums_popularity" {{if $data.CS_RANK_BY=='avg_albums_popularity'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_albums_visits}}</option>
						<option value="added_date" {{if $data.CS_RANK_BY=='added_date'}}selected{{/if}}>{{$lang.settings.system_field_rank_by_added_date}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_rank_cs_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_directories_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.system_divider_directories_settings_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_directories_max_length}}</td>
				<td class="de_control">
					<input type="text" name="DIRECTORIES_MAX_LENGTH" maxlength="1000" size="10" value="{{$data.DIRECTORIES_MAX_LENGTH}}"/>
					<span class="de_hint">{{$lang.settings.system_field_directories_max_length_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_directories_translit}}</td>
				<td class="de_control">
					<input type="hidden" name="DIRECTORIES_TRANSLIT" value="0"/>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="DIRECTORIES_TRANSLIT" value="1" {{if $data.DIRECTORIES_TRANSLIT==1}}checked{{/if}} data-confirm-save="{{$lang.settings.system_field_directories_translit_confirm}}" data-destructive="false"/><label>{{$lang.settings.system_field_directories_translit_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_directories_translit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_dependent">{{$lang.settings.system_field_directories_translit_rules}}</td>
				<td class="de_control">
					<textarea name="DIRECTORIES_TRANSLIT_RULES" rows="3" class="DIRECTORIES_TRANSLIT_on" data-confirm-save="{{$lang.settings.system_field_directories_translit_confirm}}" data-destructive="false">{{$data.DIRECTORIES_TRANSLIT_RULES}}</textarea>
					<span class="de_hint">{{$lang.settings.system_field_directories_translit_rules_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_conversion_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/50-video-conversion-engine-and-video-conversion-speed/">Video conversion engine and video conversion speed</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_pause_tasks_processing}}</td>
				<td class="de_control">
					<input type="hidden" name="ENABLE_BACKGROUND_TASKS_PAUSE" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="ENABLE_BACKGROUND_TASKS_PAUSE" value="1" {{if $data.ENABLE_BACKGROUND_TASKS_PAUSE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_pause_tasks_processing_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_pause_tasks_processing_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_conversion_limit}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="LIMIT_CONVERSION_LA_ENABLE" value="1" {{if $data.LIMIT_CONVERSION_LA!=''}}checked{{/if}}/><label>{{$lang.settings.system_field_conversion_limit_la}}</label></span>
								<input type="text" name="LIMIT_CONVERSION_LA" class="LIMIT_CONVERSION_LA_ENABLE_on" value="{{$data.LIMIT_CONVERSION_LA}}" maxlength="5" size="5"/>
								<span class="de_hint">{{$lang.settings.system_field_conversion_limit_la_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="LIMIT_CONVERSION_TIME_ENABLE" value="1" {{if $data.LIMIT_CONVERSION_TIME_FROM!='' || $data.LIMIT_CONVERSION_TIME_TO!=''}}checked{{/if}}/><label>{{$lang.settings.system_field_conversion_limit_time_from}}</label></span>
								<span>
									<input type="text" name="LIMIT_CONVERSION_TIME_FROM" class="LIMIT_CONVERSION_TIME_ENABLE_on" value="{{$data.LIMIT_CONVERSION_TIME_FROM}}" maxlength="5" size="5"/>
								</span>
								<span>
									{{$lang.settings.system_field_conversion_limit_time_to}}
									<input type="text" name="LIMIT_CONVERSION_TIME_TO" class="LIMIT_CONVERSION_TIME_ENABLE_on" value="{{$data.LIMIT_CONVERSION_TIME_TO}}" maxlength="5" size="5"/>
								</span>
								<span class="de_hint">{{$lang.settings.system_field_conversion_limit_time_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_nice}}</td>
				<td class="de_control">
					<select name="GLOBAL_CONVERTATION_PRIORITY">
						<option value="0" {{if $data.GLOBAL_CONVERTATION_PRIORITY==0}}selected{{/if}}>{{$lang.settings.system_field_nice_realtime}}</option>
						<option value="4" {{if $data.GLOBAL_CONVERTATION_PRIORITY==4}}selected{{/if}}>{{$lang.settings.system_field_nice_high}}</option>
						<option value="9" {{if $data.GLOBAL_CONVERTATION_PRIORITY==9}}selected{{/if}}>{{$lang.settings.system_field_nice_medium}}</option>
						<option value="14" {{if $data.GLOBAL_CONVERTATION_PRIORITY==14}}selected{{/if}}>{{$lang.settings.system_field_nice_low}}</option>
						<option value="19" {{if $data.GLOBAL_CONVERTATION_PRIORITY==19}}selected{{/if}}>{{$lang.settings.system_field_nice_very_low}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_nice_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_user_content_priority_videos}}</td>
				<td class="de_control">
					<span>
						{{$lang.settings.system_field_user_content_priority_standard}}:
						<select name="USER_TASKS_VIDEOS_PRIORITY_STANDARD">
							<option value="14" {{if $data.USER_TASKS_VIDEOS_PRIORITY_STANDARD==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
							<option value="12" {{if $data.USER_TASKS_VIDEOS_PRIORITY_STANDARD==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
							<option value="10" {{if $data.USER_TASKS_VIDEOS_PRIORITY_STANDARD==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
							<option value="8" {{if $data.USER_TASKS_VIDEOS_PRIORITY_STANDARD==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
							<option value="6" {{if $data.USER_TASKS_VIDEOS_PRIORITY_STANDARD==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.system_field_user_content_priority_trusted}}:
						<select name="USER_TASKS_VIDEOS_PRIORITY_TRUSTED">
							<option value="14" {{if $data.USER_TASKS_VIDEOS_PRIORITY_TRUSTED==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
							<option value="12" {{if $data.USER_TASKS_VIDEOS_PRIORITY_TRUSTED==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
							<option value="10" {{if $data.USER_TASKS_VIDEOS_PRIORITY_TRUSTED==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
							<option value="8" {{if $data.USER_TASKS_VIDEOS_PRIORITY_TRUSTED==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
							<option value="6" {{if $data.USER_TASKS_VIDEOS_PRIORITY_TRUSTED==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.system_field_user_content_priority_webmaster}}:
						<select name="USER_TASKS_VIDEOS_PRIORITY_WEBMASTER">
							<option value="14" {{if $data.USER_TASKS_VIDEOS_PRIORITY_WEBMASTER==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
							<option value="12" {{if $data.USER_TASKS_VIDEOS_PRIORITY_WEBMASTER==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
							<option value="10" {{if $data.USER_TASKS_VIDEOS_PRIORITY_WEBMASTER==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
							<option value="8" {{if $data.USER_TASKS_VIDEOS_PRIORITY_WEBMASTER==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
							<option value="6" {{if $data.USER_TASKS_VIDEOS_PRIORITY_WEBMASTER==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.system_field_user_content_priority_premium}}:
						<select name="USER_TASKS_VIDEOS_PRIORITY_PREMIUM">
							<option value="14" {{if $data.USER_TASKS_VIDEOS_PRIORITY_PREMIUM==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
							<option value="12" {{if $data.USER_TASKS_VIDEOS_PRIORITY_PREMIUM==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
							<option value="10" {{if $data.USER_TASKS_VIDEOS_PRIORITY_PREMIUM==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
							<option value="8" {{if $data.USER_TASKS_VIDEOS_PRIORITY_PREMIUM==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
							<option value="6" {{if $data.USER_TASKS_VIDEOS_PRIORITY_PREMIUM==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
						</select>
					</span>
					<span class="de_hint">{{$lang.settings.system_field_user_content_priority_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.settings.system_field_user_content_priority_albums}}</td>
					<td class="de_control">
						<span>
							{{$lang.settings.system_field_user_content_priority_standard}}:
							<select name="USER_TASKS_ALBUMS_PRIORITY_STANDARD">
								<option value="14" {{if $data.USER_TASKS_ALBUMS_PRIORITY_STANDARD==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
								<option value="12" {{if $data.USER_TASKS_ALBUMS_PRIORITY_STANDARD==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
								<option value="10" {{if $data.USER_TASKS_ALBUMS_PRIORITY_STANDARD==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
								<option value="8" {{if $data.USER_TASKS_ALBUMS_PRIORITY_STANDARD==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
								<option value="6" {{if $data.USER_TASKS_ALBUMS_PRIORITY_STANDARD==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_user_content_priority_trusted}}:
							<select name="USER_TASKS_ALBUMS_PRIORITY_TRUSTED">
								<option value="14" {{if $data.USER_TASKS_ALBUMS_PRIORITY_TRUSTED==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
								<option value="12" {{if $data.USER_TASKS_ALBUMS_PRIORITY_TRUSTED==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
								<option value="10" {{if $data.USER_TASKS_ALBUMS_PRIORITY_TRUSTED==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
								<option value="8" {{if $data.USER_TASKS_ALBUMS_PRIORITY_TRUSTED==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
								<option value="6" {{if $data.USER_TASKS_ALBUMS_PRIORITY_TRUSTED==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_user_content_priority_webmaster}}:
							<select name="USER_TASKS_ALBUMS_PRIORITY_WEBMASTER">
								<option value="14" {{if $data.USER_TASKS_ALBUMS_PRIORITY_WEBMASTER==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
								<option value="12" {{if $data.USER_TASKS_ALBUMS_PRIORITY_WEBMASTER==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
								<option value="10" {{if $data.USER_TASKS_ALBUMS_PRIORITY_WEBMASTER==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
								<option value="8" {{if $data.USER_TASKS_ALBUMS_PRIORITY_WEBMASTER==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
								<option value="6" {{if $data.USER_TASKS_ALBUMS_PRIORITY_WEBMASTER==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_user_content_priority_premium}}:
							<select name="USER_TASKS_ALBUMS_PRIORITY_PREMIUM">
								<option value="14" {{if $data.USER_TASKS_ALBUMS_PRIORITY_PREMIUM==14}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
								<option value="12" {{if $data.USER_TASKS_ALBUMS_PRIORITY_PREMIUM==12}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
								<option value="10" {{if $data.USER_TASKS_ALBUMS_PRIORITY_PREMIUM==10}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
								<option value="8" {{if $data.USER_TASKS_ALBUMS_PRIORITY_PREMIUM==8}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
								<option value="6" {{if $data.USER_TASKS_ALBUMS_PRIORITY_PREMIUM==6}}selected{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
							</select>
						</span>
						<span class="de_hint">{{$lang.settings.system_field_user_content_priority_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.system_field_failed_tasks_auto_restart}}</td>
				<td class="de_control">
					<select name="FAILED_TASKS_AUTO_RESTART">
						<option value="0" {{if $data.FAILED_TASKS_AUTO_RESTART==0}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_no}}</option>
						<option value="1" {{if $data.FAILED_TASKS_AUTO_RESTART==1}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_times|replace:"%1%":"1"}}</option>
						<option value="2" {{if $data.FAILED_TASKS_AUTO_RESTART==2}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_times|replace:"%1%":"2"}}</option>
						<option value="3" {{if $data.FAILED_TASKS_AUTO_RESTART==3}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_times|replace:"%1%":"3"}}</option>
						<option value="4" {{if $data.FAILED_TASKS_AUTO_RESTART==4}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_times|replace:"%1%":"4"}}</option>
						<option value="5" {{if $data.FAILED_TASKS_AUTO_RESTART==5}}selected{{/if}}>{{$lang.settings.system_field_failed_tasks_auto_restart_times|replace:"%1%":"5"}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_failed_tasks_auto_restart_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_min_server_space_to_alert}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="MAIN_SERVER_MIN_FREE_SPACE_MB" maxlength="1000" size="10" value="{{$data.MAIN_SERVER_MIN_FREE_SPACE_MB}}"/>
						{{$lang.settings.system_field_min_server_space_to_alert_mb}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_min_server_space_to_alert_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_min_server_group_space_to_alert}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="SERVER_GROUP_MIN_FREE_SPACE_MB" maxlength="1000" size="10" value="{{$data.SERVER_GROUP_MIN_FREE_SPACE_MB}}"/>
						{{$lang.settings.system_field_min_server_group_space_to_alert_mb}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_min_server_group_space_to_alert_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_memory_limit}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="LIMIT_MEMORY" maxlength="1000" size="10" value="{{$data.LIMIT_MEMORY}}"/>
						{{$lang.settings.system_field_memory_limit_mb}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_memory_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_videos_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_videos_half_processing}}</td>
				<td class="de_control">
					<input type="hidden" name="VIDEOS_HALF_PROCESSING" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="VIDEOS_HALF_PROCESSING" value="1" {{if $data.VIDEOS_HALF_PROCESSING==1}}checked{{/if}}/><label>{{$lang.settings.system_field_videos_half_processing_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_videos_half_processing_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_videos_initial_rating}}</td>
				<td class="de_control">
					<select name="VIDEO_INITIAL_RATING">
						<option value="0" {{if $data.VIDEO_INITIAL_RATING==0}}selected{{/if}}>0</option>
						<option value="1" {{if $data.VIDEO_INITIAL_RATING==1}}selected{{/if}}>1</option>
						<option value="2" {{if $data.VIDEO_INITIAL_RATING==2}}selected{{/if}}>2</option>
						<option value="3" {{if $data.VIDEO_INITIAL_RATING==3}}selected{{/if}}>3</option>
						<option value="4" {{if $data.VIDEO_INITIAL_RATING==4}}selected{{/if}}>4</option>
						<option value="5" {{if $data.VIDEO_INITIAL_RATING==5}}selected{{/if}}>5</option>
						<option value="6" {{if $data.VIDEO_INITIAL_RATING==6}}selected{{/if}}>6</option>
						<option value="7" {{if $data.VIDEO_INITIAL_RATING==7}}selected{{/if}}>7</option>
						<option value="8" {{if $data.VIDEO_INITIAL_RATING==8}}selected{{/if}}>8</option>
						<option value="9" {{if $data.VIDEO_INITIAL_RATING==9}}selected{{/if}}>9</option>
						<option value="10" {{if $data.VIDEO_INITIAL_RATING==10}}selected{{/if}}>10</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_videos_initial_rating_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_videos_default_server_group}}</td>
				<td class="de_control">
					<select name="DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO">
						<option value="auto" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO=='auto'}}selected{{/if}}>{{$lang.settings.system_field_videos_default_server_group_auto}}</option>
						<option value="rand" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO=='rand'}}selected{{/if}}>{{$lang.settings.system_field_videos_default_server_group_rand}}</option>
						{{foreach name="data" item="item" from=$list_server_groups_videos|smarty:nodefaults}}
							<option value="{{$item.group_id}}" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO==$item.group_id}}selected{{/if}}>{{$item.title}} ({{$item.free_space}})</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.settings.system_field_videos_default_server_group_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label VIDEOS_DUPLICATE_TITLE_OPTION_1">{{$lang.settings.system_field_videos_duplicate_title}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="VIDEOS_DUPLICATE_TITLE_OPTION">
						<option value="0" {{if $data.VIDEOS_DUPLICATE_TITLE_OPTION=='0'}}selected{{/if}}>{{$lang.settings.system_field_videos_duplicate_title_ignore}}</option>
						<option value="1" {{if $data.VIDEOS_DUPLICATE_TITLE_OPTION=='1'}}selected{{/if}}>{{$lang.settings.system_field_videos_duplicate_title_postfix}}</option>
					</select>
					<input type="text" name="VIDEOS_DUPLICATE_TITLE_POSTFIX" class="VIDEOS_DUPLICATE_TITLE_OPTION_1" size="20" value="{{$data.VIDEOS_DUPLICATE_TITLE_POSTFIX}}" maxlength="1000"/>
					<span class="de_hint">{{$lang.settings.system_field_videos_duplicate_title_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_videos_duplicate_file}}</td>
				<td class="de_control">
					<select name="VIDEOS_DUPLICATE_FILE_OPTION">
						<option value="0" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='0'}}selected{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_ignore}}</option>
						<option value="1" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='1'}}selected{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_ignore_if_deleted}}</option>
						<option value="2" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='2'}}selected{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_disallow}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_videos_duplicate_file_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_save_source_files}}</td>
				<td class="de_control">
					<input type="hidden" name="KEEP_VIDEO_SOURCE_FILES" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="KEEP_VIDEO_SOURCE_FILES" value="1" {{if $data.KEEP_VIDEO_SOURCE_FILES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_save_source_files_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_save_source_files_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_videos_screenshots_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.system_divider_videos_screenshots_settings_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_screenshots_count}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_fixed" type="radio" name="SCREENSHOTS_COUNT_UNIT" value="1" {{if $data.SCREENSHOTS_COUNT_UNIT==1}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_count_fixed}}</label></span>
								<input type="text" name="SCREENSHOTS_COUNT_FIXED" class="option_fixed" value="{{$data.SCREENSHOTS_COUNT_FIXED}}" maxlength="5" size="5"/>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_count_fixed_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_dynamic" type="radio" name="SCREENSHOTS_COUNT_UNIT" value="2" {{if $data.SCREENSHOTS_COUNT_UNIT==2}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_count_dynamic}}</label></span>
								<span>
									<input type="text" name="SCREENSHOTS_COUNT_DYNAMIC" class="option_dynamic" value="{{$data.SCREENSHOTS_COUNT_DYNAMIC}}" maxlength="5" size="5"/>
									{{$lang.settings.system_field_screenshots_count_dynamic_seconds}}
								</span>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_count_dynamic_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_SCREENSHOTS_MERGE_VERTICAL" value="1" {{if $data.SCREENSHOTS_MERGE_VERTICAL>0}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_merge_vertical}}</label></span>
								<select name="SCREENSHOTS_MERGE_VERTICAL" class="ENABLE_SCREENSHOTS_MERGE_VERTICAL_on">
									<option value="2" {{if $data.SCREENSHOTS_MERGE_VERTICAL==2}}selected{{/if}}>{{$lang.settings.system_field_screenshots_merge_vertical_2}}</option>
									<option value="3" {{if $data.SCREENSHOTS_MERGE_VERTICAL==3}}selected{{/if}}>{{$lang.settings.system_field_screenshots_merge_vertical_3}}</option>
									<option value="4" {{if $data.SCREENSHOTS_MERGE_VERTICAL==4}}selected{{/if}}>{{$lang.settings.system_field_screenshots_merge_vertical_4}}</option>
								</select>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_merge_vertical_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_screenshots_crop}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span>
									{{$lang.settings.system_field_screenshots_crop_left}}:
									<input type="text" name="SCREENSHOTS_CROP_LEFT" maxlength="1000" size="5" value="{{$data.SCREENSHOTS_CROP_LEFT}}"/>
									<select name="SCREENSHOTS_CROP_LEFT_UNIT">
										<option value="1" {{if $data.SCREENSHOTS_CROP_LEFT_UNIT==1}}selected{{/if}}>px</option>
										<option value="2" {{if $data.SCREENSHOTS_CROP_LEFT_UNIT==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.settings.system_field_screenshots_crop_top}}:
									<input type="text" name="SCREENSHOTS_CROP_TOP" maxlength="1000" size="5" value="{{$data.SCREENSHOTS_CROP_TOP}}"/>
									<select name="SCREENSHOTS_CROP_TOP_UNIT">
										<option value="1" {{if $data.SCREENSHOTS_CROP_TOP_UNIT==1}}selected{{/if}}>px</option>
										<option value="2" {{if $data.SCREENSHOTS_CROP_TOP_UNIT==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.settings.system_field_screenshots_crop_right}}:
									<input type="text" name="SCREENSHOTS_CROP_RIGHT" maxlength="1000" size="5" value="{{$data.SCREENSHOTS_CROP_RIGHT}}"/>
									<select name="SCREENSHOTS_CROP_RIGHT_UNIT">
										<option value="1" {{if $data.SCREENSHOTS_CROP_RIGHT_UNIT==1}}selected{{/if}}>px</option>
										<option value="2" {{if $data.SCREENSHOTS_CROP_RIGHT_UNIT==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span>
									{{$lang.settings.system_field_screenshots_crop_bottom}}:
									<input type="text" name="SCREENSHOTS_CROP_BOTTOM" maxlength="1000" size="5" value="{{$data.SCREENSHOTS_CROP_BOTTOM}}"/>
									<select name="SCREENSHOTS_CROP_BOTTOM_UNIT">
										<option value="1" {{if $data.SCREENSHOTS_CROP_BOTTOM_UNIT==1}}selected{{/if}}>px</option>
										<option value="2" {{if $data.SCREENSHOTS_CROP_BOTTOM_UNIT==2}}selected{{/if}}>%</option>
									</select>
								</span>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_crop_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<input type="hidden" name="SCREENSHOTS_CROP_TRIM_SIDES" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_CROP_TRIM_SIDES" value="1" {{if $data.SCREENSHOTS_CROP_TRIM_SIDES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_crop_trim_sides}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_crop_trim_sides_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_screenshots_crop_customize}}</td>
				<td class="de_control">
					<select name="SCREENSHOTS_CROP_CUSTOMIZE">
						<option value="0" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==0}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_no}}</option>
						<option value="1" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==1}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
						<option value="2" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==2}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
						<option value="3" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==3}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
						<option value="4" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==4}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
						<option value="5" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==5}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
						<option value="6" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==6}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
						<option value="7" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==7}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
						<option value="8" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==8}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
						<option value="9" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==9}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
						<option value="10" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==10}}selected{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_screenshots_crop_customize_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_screenshots_uploaded}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<input type="hidden" name="SCREENSHOTS_UPLOADED_CROP" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_UPLOADED_CROP" value="1" {{if $data.SCREENSHOTS_UPLOADED_CROP==1}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_uploaded_crop}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_uploaded_crop_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<input type="hidden" name="SCREENSHOTS_UPLOADED_WATERMARK" value="0"/>
								<span class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_UPLOADED_WATERMARK" value="1" {{if $data.SCREENSHOTS_UPLOADED_WATERMARK==1}}checked{{/if}}/><label>{{$lang.settings.system_field_screenshots_uploaded_watermark}}</label></span>
								<span class="de_hint">{{$lang.settings.system_field_screenshots_uploaded_watermark_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_screenshots_seconds_offset}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="SCREENSHOTS_SECONDS_OFFSET" maxlength="1000" size="10" value="{{$data.SCREENSHOTS_SECONDS_OFFSET}}"/>
						{{$lang.common.seconds}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_screenshots_seconds_offset_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_screenshots_seconds_offset_end}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="SCREENSHOTS_SECONDS_OFFSET_END" maxlength="1000" size="10" value="{{$data.SCREENSHOTS_SECONDS_OFFSET_END}}"/>
						{{$lang.common.seconds}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_screenshots_seconds_offset_end_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_screenshots_main_number}}</td>
				<td class="de_control">
					<input type="text" name="SCREENSHOTS_MAIN_NUMBER" maxlength="1000" size="10" value="{{$data.SCREENSHOTS_MAIN_NUMBER}}"/>
					<span class="de_hint">{{$lang.settings.system_field_screenshots_main_number_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_video_file_protection_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/14-how-to-protect-your-videos-from-being-downloaded-or-parsed/">How to protect your videos from being downloaded or parsed</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_antihotlink_enable}}</td>
				<td class="de_control">
					<input type="hidden" name="ENABLE_ANTI_HOTLINK" value="0"/>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ANTI_HOTLINK" value="1" {{if $data.ENABLE_ANTI_HOTLINK==1}}checked{{/if}}/><label>{{$lang.settings.system_field_antihotlink_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_enable_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="ANTI_HOTLINK_TYPE">
						<option value="0" {{if $data.ANTI_HOTLINK_TYPE==0}}selected{{/if}}>{{$lang.settings.system_field_antihotlink_type_referer}}</option>
						<option value="1" {{if $data.ANTI_HOTLINK_TYPE==1}}selected{{/if}}>{{$lang.settings.system_field_antihotlink_type_ip}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_type_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_formats_disabled}}</td>
				<td class="de_control">
					<span>
						{{assign var="has_video_formats_where_protection_disabled" value="false"}}
						{{foreach item="item" from=$list_formats_videos|smarty:nodefaults}}
							{{if $item.is_hotlink_protection_disabled==1}}
								{{if $has_video_formats_where_protection_disabled=="true"}},{{/if}}
								{{if in_array('system|formats',$smarty.session.permissions)}}
									<a href="{{if $config.installation_type>=2}}formats_videos.php?action=change&amp;item_id={{$item.format_video_id}}{{else}}formats_videos_basic.php{{/if}}">{{$item.title}}</a>
								{{else}}
									{{$item.title}}
								{{/if}}
								{{assign var="has_video_formats_where_protection_disabled" value="true"}}
							{{/if}}
						{{/foreach}}
						{{if $has_video_formats_where_protection_disabled=="false"}}
							{{$lang.settings.system_field_antihotlink_formats_disabled_none}}
						{{/if}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_formats_disabled_hint}}</span>
				</td>
			</tr>
			<tr class="ANTI_HOTLINK_TYPE_0 ENABLE_ANTI_HOTLINK_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_white_domains}}</td>
				<td class="de_control">
					<input type="text" name="ANTI_HOTLINK_WHITE_DOMAINS" maxlength="1000" value="{{$data.ANTI_HOTLINK_WHITE_DOMAINS}}"/>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_white_domains_hint}}</span>
				</td>
			</tr>
			{{if !$config.project_url|strpos:"/":10}}
				<tr class="ENABLE_ANTI_HOTLINK_on">
					<td class="de_label">{{$lang.settings.system_field_antihotlink_encode_links}}</td>
					<td class="de_control">
						<input type="hidden" name="ANTI_HOTLINK_ENCODE_LINKS" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="ANTI_HOTLINK_ENCODE_LINKS" value="1" {{if $data.ANTI_HOTLINK_ENCODE_LINKS==1}}checked{{/if}}/><label>{{$lang.settings.system_field_antihotlink_encode_links_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_antihotlink_encode_links_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="ENABLE_ANTI_HOTLINK_on">
				<td class="de_label ANTI_HOTLINK_ENABLE_IP_LIMIT_on">{{$lang.settings.system_field_antihotlink_limitation}}</td>
				<td class="de_control">
					<input type="hidden" name="ANTI_HOTLINK_ENABLE_IP_LIMIT" value="0"/>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ANTI_HOTLINK_ENABLE_IP_LIMIT" value="1" {{if $data.ANTI_HOTLINK_ENABLE_IP_LIMIT==1}}checked{{/if}}/><label>{{$lang.settings.system_field_antihotlink_limitation_enabled}}</label></span>
					<span>
						<input type="text" name="ANTI_HOTLINK_N_VIDEOS" maxlength="1000" class="ANTI_HOTLINK_ENABLE_IP_LIMIT_on" size="5" value="{{$data.ANTI_HOTLINK_N_VIDEOS}}"/>
						{{$lang.settings.system_field_antihotlink_limitation_videos}}
					</span>
					<span>
						<input type="text" name="ANTI_HOTLINK_N_HOURS" maxlength="1000" class="ANTI_HOTLINK_ENABLE_IP_LIMIT_on" size="5" value="{{$data.ANTI_HOTLINK_N_HOURS}}"/>
						{{$lang.settings.system_field_antihotlink_limitation_minutes}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_limitation_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on ANTI_HOTLINK_ENABLE_IP_LIMIT_on">
				<td class="de_label de_dependent">{{$lang.settings.system_field_antihotlink_limitation_disabled}}</td>
				<td class="de_control">
					<span>
						{{assign var="has_video_formats_where_ip_disabled" value="false"}}
						{{foreach item="item" from=$list_formats_videos_std|smarty:nodefaults}}
							{{if $item.poxtfix=='_preview.mp4' || ($item.limit_number_parts>1 && $item.limit_total_duration<=30)}}
								{{if $has_video_formats_where_ip_disabled=="true"}},{{/if}}
								{{if in_array('system|formats',$smarty.session.permissions)}}
									<a href="{{if $config.installation_type>=2}}formats_videos.php?action=change&amp;item_id={{$item.format_video_id}}{{else}}formats_videos_basic.php{{/if}}">{{$item.title}}</a>
								{{else}}
									{{$item.title}}
								{{/if}}
								{{assign var="has_video_formats_where_ip_disabled" value="true"}}
							{{/if}}
						{{/foreach}}
						{{foreach item="item" from=$list_formats_videos_premium|smarty:nodefaults}}
							{{if $item.poxtfix=='_preview.mp4' || ($item.limit_number_parts>1 && $item.limit_total_duration<=30)}}
								{{if $has_video_formats_where_ip_disabled=="true"}},{{/if}}
								{{if in_array('system|formats',$smarty.session.permissions)}}
									<a href="{{if $config.installation_type>=2}}formats_videos.php?action=change&amp;item_id={{$item.format_video_id}}{{else}}formats_videos_basic.php{{/if}}">{{$item.title}}</a>
								{{else}}
									{{$item.title}}
								{{/if}}
								{{assign var="has_video_formats_where_ip_disabled" value="true"}}
							{{/if}}
						{{/foreach}}
						{{if $has_video_formats_where_ip_disabled=="false"}}
							{{$lang.settings.system_field_antihotlink_limitation_disabled_none}}
						{{/if}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_limitation_disabled_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on ANTI_HOTLINK_ENABLE_IP_LIMIT_on">
				<td class="de_label de_dependent">{{$lang.settings.system_field_antihotlink_own_ip}}</td>
				<td class="de_control">
					<span>
						{{$data.ANTI_HOTLINK_OWN_IP}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_own_ip_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on ANTI_HOTLINK_ENABLE_IP_LIMIT_on">
				<td class="de_label de_dependent">{{$lang.settings.system_field_antihotlink_blocked_ips}}</td>
				<td class="de_control">
					<span>
						{{$data.BLOCKED_IPS|default:$lang.common.undefined}}
					</span>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_blocked_ips_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_white_ips}}</td>
				<td class="de_control">
					<input type="text" name="ANTI_HOTLINK_WHITE_IPS" maxlength="65535" value="{{$data.ANTI_HOTLINK_WHITE_IPS}}"/>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_white_ips_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_ANTI_HOTLINK_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_custom_file}}</td>
				<td class="de_control">
					<input type="text" name="ANTI_HOTLINK_FILE" maxlength="1000" value="{{$data.ANTI_HOTLINK_FILE}}"/>
					<span class="de_hint">{{$lang.settings.system_field_antihotlink_custom_file_hint}}</span>
				</td>
			</tr>
			{{if $config.disable_rotator!='true'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_rotator_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_rotator_videos_enable}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<input type="hidden" name="ROTATOR_VIDEOS_ENABLE" value="0"/>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ROTATOR_VIDEOS_ENABLE" value="1" {{if $data.ROTATOR_VIDEOS_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_enabled}}</label></span>
									<span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_hint}}</span>
								</td>
							</tr>
							<tr class="ROTATOR_VIDEOS_ENABLE_on">
								<td>
									<input type="hidden" name="ROTATOR_VIDEOS_CATEGORIES_ENABLE" value="0"/>
									<span class="de_lv_pair"><input type="checkbox" name="ROTATOR_VIDEOS_CATEGORIES_ENABLE" value="1" {{if $data.ROTATOR_VIDEOS_CATEGORIES_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_categories}}</label></span>
									<span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_categories_hint}}</span>
								</td>
							</tr>
							<tr class="ROTATOR_VIDEOS_ENABLE_on">
								<td>
									<input type="hidden" name="ROTATOR_VIDEOS_TAGS_ENABLE" value="0"/>
									<span class="de_lv_pair"><input type="checkbox" name="ROTATOR_VIDEOS_TAGS_ENABLE" value="1" {{if $data.ROTATOR_VIDEOS_TAGS_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_tags}}</label></span>
									<span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_tags_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="ROTATOR_VIDEOS_ENABLE_on">
					<td class="de_label">{{$lang.settings.system_field_rotator_screenshots_enable}}</td>
					<td class="de_control">
						<input type="hidden" name="ROTATOR_SCREENSHOTS_ENABLE" value="0"/>
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ROTATOR_SCREENSHOTS_ENABLE" value="1" {{if $data.ROTATOR_SCREENSHOTS_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_rotator_screenshots_enable_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_enable_hint}}</span>
					</td>
				</tr>
				<tr class="ROTATOR_VIDEOS_ENABLE_on ROTATOR_SCREENSHOTS_ENABLE_on">
					<td class="de_label de_dependent">{{$lang.settings.system_field_rotator_screenshots_only_one_enable}}</td>
					<td class="de_control">
						<input type="hidden" name="ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE" value="1" {{if $data.ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_rotator_screenshots_only_one_enable_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_only_one_enable_hint}}</span>
					</td>
				</tr>
				<tr class="ROTATOR_VIDEOS_ENABLE_on ROTATOR_SCREENSHOTS_ENABLE_on">
					<td class="de_label de_dependent de_required">{{$lang.settings.system_field_rotator_screenshots_min_shows}}</td>
					<td class="de_control">
						<input type="text" name="ROTATOR_SCREENSHOTS_MIN_SHOWS" maxlength="1000" size="10" value="{{$data.ROTATOR_SCREENSHOTS_MIN_SHOWS}}"/>
						<span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_min_shows_hint}}</span>
					</td>
				</tr>
				<tr class="ROTATOR_VIDEOS_ENABLE_on ROTATOR_SCREENSHOTS_ENABLE_on">
					<td class="de_label de_dependent de_required">{{$lang.settings.system_field_rotator_screenshots_min_clicks}}</td>
					<td class="de_control">
						<input type="text" name="ROTATOR_SCREENSHOTS_MIN_CLICKS" maxlength="1000" size="10" value="{{$data.ROTATOR_SCREENSHOTS_MIN_CLICKS}}"/>
						<span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_min_clicks_hint}}</span>
					</td>
				</tr>
				<tr class="ROTATOR_VIDEOS_ENABLE_on ROTATOR_SCREENSHOTS_ENABLE_on">
					<td class="de_label de_dependent ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION">{{$lang.settings.system_field_rotator_screenshots_delete}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION">
							<option value="0" {{if $data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT==0}}selected{{/if}}>{{$lang.settings.system_field_rotator_screenshots_delete_no}}</option>
							<option value="1" {{if $data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT!=0}}selected{{/if}}>{{$lang.settings.system_field_rotator_screenshots_delete_yes}}</option>
						</select>
						<input type="text" name="ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT" class="ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION_1" size="10" value="{{$data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT}}"/>
					</td>
				</tr>
				{{if count($rotator_completeness)>0}}
					<tr class="ROTATOR_VIDEOS_ENABLE_on ROTATOR_SCREENSHOTS_ENABLE_on">
						<td class="de_label de_dependent">{{$lang.settings.system_field_rotator_screenshots_completeness}}</td>
						<td class="de_control">
							{{foreach name="data" item="item" from=$rotator_completeness|smarty:nodefaults}}
								{{assign var="width" value=`$item.value*300`}}
								<div style="height: 12px; width: 80px; font-size: 10px; float: left">
									{{if $smarty.foreach.data.iteration==1}}
										0% - 20%
									{{elseif $smarty.foreach.data.iteration==2}}
										21% - 40%
									{{elseif $smarty.foreach.data.iteration==3}}
										41% - 60%
									{{elseif $smarty.foreach.data.iteration==4}}
										61% - 80%
									{{elseif $smarty.foreach.data.iteration==5}}
										81% - 100%
									{{elseif $smarty.foreach.data.iteration==6}}
										{{if $item.amount>0}}
											<a href="videos.php?no_filter=true&amp;se_show_id=23">100%</a>
										{{else}}
											100%
										{{/if}}
									{{/if}}
								</div>
								<div style="height: 10px; width: {{if $width<1}}1{{else}}{{$width|string_format:"%d"}}{{/if}}px; background: #aeaeae; float: left; margin: 1px 0"></div>
								<div style="height: 12px; padding-left: 5px; font-size: 10px; float: left">{{$item.percent}}% ({{$item.shows}}, {{$item.clicks}})</div>
								<div style="clear: both"></div>
							{{/foreach}}
						</td>
					</tr>
				{{/if}}
				<tr class="ROTATOR_VIDEOS_ENABLE_on">
					<td class="de_label de_required">{{$lang.settings.system_field_rotator_schedule}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="ROTATOR_SCHEDULE_INTERVAL" value="{{$data.ROTATOR_SCHEDULE_INTERVAL}}" size="5"/>
							{{$lang.common.minutes}}
						</span>
						<span>
							{{$lang.settings.system_field_rotator_schedule_pause_from}}
							<input type="text" name="ROTATOR_SCHEDULE_PAUSE_FROM" value="{{$data.ROTATOR_SCHEDULE_PAUSE_FROM}}" size="5"/>
						</span>
						<span>
							{{$lang.settings.system_field_rotator_schedule_pause_to}}
							<input type="text" name="ROTATOR_SCHEDULE_PAUSE_TO" value="{{$data.ROTATOR_SCHEDULE_PAUSE_TO}}" size="5"/>
						</span>
						<span class="de_hint">{{$lang.settings.system_field_rotator_schedule_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_albums_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_albums_initial_rating}}</td>
					<td class="de_control">
						<select name="ALBUM_INITIAL_RATING">
							<option value="0" {{if $data.ALBUM_INITIAL_RATING==0}}selected{{/if}}>0</option>
							<option value="1" {{if $data.ALBUM_INITIAL_RATING==1}}selected{{/if}}>1</option>
							<option value="2" {{if $data.ALBUM_INITIAL_RATING==2}}selected{{/if}}>2</option>
							<option value="3" {{if $data.ALBUM_INITIAL_RATING==3}}selected{{/if}}>3</option>
							<option value="4" {{if $data.ALBUM_INITIAL_RATING==4}}selected{{/if}}>4</option>
							<option value="5" {{if $data.ALBUM_INITIAL_RATING==5}}selected{{/if}}>5</option>
							<option value="6" {{if $data.ALBUM_INITIAL_RATING==6}}selected{{/if}}>6</option>
							<option value="7" {{if $data.ALBUM_INITIAL_RATING==7}}selected{{/if}}>7</option>
							<option value="8" {{if $data.ALBUM_INITIAL_RATING==8}}selected{{/if}}>8</option>
							<option value="9" {{if $data.ALBUM_INITIAL_RATING==9}}selected{{/if}}>9</option>
							<option value="10" {{if $data.ALBUM_INITIAL_RATING==10}}selected{{/if}}>10</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_albums_initial_rating_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_albums_default_server_group}}</td>
					<td class="de_control">
						<select name="DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM">
							<option value="auto" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM=='auto'}}selected{{/if}}>{{$lang.settings.system_field_albums_default_server_group_auto}}</option>
							<option value="rand" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM=='rand'}}selected{{/if}}>{{$lang.settings.system_field_albums_default_server_group_rand}}</option>
							{{foreach name="data" item="item" from=$list_server_groups_albums|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM==$item.group_id}}selected{{/if}}>{{$item.title}} ({{$item.free_space}})</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.settings.system_field_albums_default_server_group_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label ALBUMS_DUPLICATE_TITLE_OPTION_1">{{$lang.settings.system_field_albums_duplicate_title}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="ALBUMS_DUPLICATE_TITLE_OPTION">
							<option value="0" {{if $data.ALBUMS_DUPLICATE_TITLE_OPTION=='0'}}selected{{/if}}>{{$lang.settings.system_field_albums_duplicate_title_ignore}}</option>
							<option value="1" {{if $data.ALBUMS_DUPLICATE_TITLE_OPTION=='1'}}selected{{/if}}>{{$lang.settings.system_field_albums_duplicate_title_postfix}}</option>
						</select>
						<input type="text" name="ALBUMS_DUPLICATE_TITLE_POSTFIX" class="ALBUMS_DUPLICATE_TITLE_OPTION_1" size="20" value="{{$data.ALBUMS_DUPLICATE_TITLE_POSTFIX}}" maxlength="1000"/>
						<span class="de_hint">{{$lang.settings.system_field_albums_duplicate_title_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_albums_crop}}</td>
					<td class="de_control">
						<span>
							{{$lang.settings.system_field_albums_crop_left}}:
							<input type="text" name="ALBUMS_CROP_LEFT" maxlength="1000" size="5" value="{{$data.ALBUMS_CROP_LEFT}}"/>
							<select name="ALBUMS_CROP_LEFT_UNIT">
								<option value="1" {{if $data.ALBUMS_CROP_LEFT_UNIT==1}}selected{{/if}}>px</option>
								<option value="2" {{if $data.ALBUMS_CROP_LEFT_UNIT==2}}selected{{/if}}>%</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_albums_crop_top}}:
							<input type="text" name="ALBUMS_CROP_TOP" maxlength="1000" size="5" value="{{$data.ALBUMS_CROP_TOP}}"/>
							<select name="ALBUMS_CROP_TOP_UNIT">
								<option value="1" {{if $data.ALBUMS_CROP_TOP_UNIT==1}}selected{{/if}}>px</option>
								<option value="2" {{if $data.ALBUMS_CROP_TOP_UNIT==2}}selected{{/if}}>%</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_albums_crop_right}}:
							<input type="text" name="ALBUMS_CROP_RIGHT" maxlength="1000" size="5" value="{{$data.ALBUMS_CROP_RIGHT}}"/>
							<select name="ALBUMS_CROP_RIGHT_UNIT">
								<option value="1" {{if $data.ALBUMS_CROP_RIGHT_UNIT==1}}selected{{/if}}>px</option>
								<option value="2" {{if $data.ALBUMS_CROP_RIGHT_UNIT==2}}selected{{/if}}>%</option>
							</select>
						</span>
						<span>
							{{$lang.settings.system_field_albums_crop_bottom}}:
							<input type="text" name="ALBUMS_CROP_BOTTOM" maxlength="1000" size="5" value="{{$data.ALBUMS_CROP_BOTTOM}}"/>
							<select name="ALBUMS_CROP_BOTTOM_UNIT">
								<option value="1" {{if $data.ALBUMS_CROP_BOTTOM_UNIT==1}}selected{{/if}}>px</option>
								<option value="2" {{if $data.ALBUMS_CROP_BOTTOM_UNIT==2}}selected{{/if}}>%</option>
							</select>
						</span>
						<span class="de_hint">{{$lang.settings.system_field_albums_crop_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_albums_crop_customize}}</td>
					<td class="de_control">
						<select name="ALBUMS_CROP_CUSTOMIZE">
							<option value="0" {{if $data.ALBUMS_CROP_CUSTOMIZE==0}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_no}}</option>
							<option value="1" {{if $data.ALBUMS_CROP_CUSTOMIZE==1}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
							<option value="2" {{if $data.ALBUMS_CROP_CUSTOMIZE==2}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
							<option value="3" {{if $data.ALBUMS_CROP_CUSTOMIZE==3}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
							<option value="4" {{if $data.ALBUMS_CROP_CUSTOMIZE==4}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
							<option value="5" {{if $data.ALBUMS_CROP_CUSTOMIZE==5}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
							<option value="6" {{if $data.ALBUMS_CROP_CUSTOMIZE==6}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
							<option value="7" {{if $data.ALBUMS_CROP_CUSTOMIZE==7}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
							<option value="8" {{if $data.ALBUMS_CROP_CUSTOMIZE==8}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
							<option value="9" {{if $data.ALBUMS_CROP_CUSTOMIZE==9}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
							<option value="10" {{if $data.ALBUMS_CROP_CUSTOMIZE==10}}selected{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_albums_crop_customize_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_video_edit_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_video_default_user}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$data.DEFAULT_USER_IN_ADMIN_ADD_VIDEO_ID}}</span>
							{{/if}}
						</div>
						<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_VIDEO" maxlength="1000" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_VIDEO}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.system_field_add_video_default_user_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_video_default_status}}</td>
				<td class="de_control">
					<select name="DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO">
						<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO==1}}selected{{/if}}>{{$lang.settings.system_field_add_video_default_status_active}}</option>
						<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO!=1}}selected{{/if}}>{{$lang.settings.system_field_add_video_default_status_disabled}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_video_post_date_time}}</td>
				<td class="de_control">
					<select name="USE_POST_DATE_RANDOMIZATION">
						<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION==0}}selected{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_none}}</option>
						<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION==1}}selected{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_random}}</option>
						<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION==2}}selected{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_current}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.system_field_add_video_post_date_time_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_video_directory_autogeneration}}</td>
				<td class="de_control">
					<input type="hidden" name="VIDEO_REGENERATE_DIRECTORIES" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="VIDEO_REGENERATE_DIRECTORIES" value="1" {{if $data.VIDEO_REGENERATE_DIRECTORIES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_video_directory_autogeneration_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_edit_video_directory_autogeneration_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_video_check_duplicate_titles}}</td>
				<td class="de_control">
					<input type="hidden" name="VIDEO_CHECK_DUPLICATE_TITLES" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="VIDEO_CHECK_DUPLICATE_TITLES" value="1" {{if $data.VIDEO_CHECK_DUPLICATE_TITLES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_video_check_duplicate_titles_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_edit_video_check_duplicate_titles_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_video_screenshot_size_validation}}</td>
				<td class="de_control">
					<input type="hidden" name="VIDEO_VALIDATE_SCREENSHOT_SIZES" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="VIDEO_VALIDATE_SCREENSHOT_SIZES" value="1" {{if $data.VIDEO_VALIDATE_SCREENSHOT_SIZES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_video_screenshot_size_validation_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_edit_video_screenshot_size_validation_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_album_edit_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_album_default_user}}</td>
					<td class="de_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$data.DEFAULT_USER_IN_ADMIN_ADD_ALBUM_ID}}</span>
								{{/if}}
							</div>
							<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_ALBUM" maxlength="1000" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_ALBUM}}"/>
						</div>
						<span class="de_hint">{{$lang.settings.system_field_add_album_default_user_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_album_default_status}}</td>
					<td class="de_control">
						<select name="DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM">
							<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM==1}}selected{{/if}}>{{$lang.settings.system_field_add_album_default_status_active}}</option>
							<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM!=1}}selected{{/if}}>{{$lang.settings.system_field_add_album_default_status_disabled}}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_album_post_date_time}}</td>
					<td class="de_control">
						<select name="USE_POST_DATE_RANDOMIZATION_ALBUM">
							<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==0}}selected{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_none}}</option>
							<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==1}}selected{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_random}}</option>
							<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==2}}selected{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_current}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_add_album_post_date_time_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_edit_album_directory_autogeneration}}</td>
					<td class="de_control">
						<input type="hidden" name="ALBUM_REGENERATE_DIRECTORIES" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="ALBUM_REGENERATE_DIRECTORIES" value="1" {{if $data.ALBUM_REGENERATE_DIRECTORIES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_album_directory_autogeneration_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_edit_album_directory_autogeneration_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_edit_album_check_duplicate_titles}}</td>
					<td class="de_control">
						<input type="hidden" name="ALBUM_CHECK_DUPLICATE_TITLES" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="ALBUM_CHECK_DUPLICATE_TITLES" value="1" {{if $data.ALBUM_CHECK_DUPLICATE_TITLES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_album_check_duplicate_titles_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_edit_album_check_duplicate_titles_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_edit_album_image_size_validation}}</td>
					<td class="de_control">
						<input type="hidden" name="ALBUM_VALIDATE_IMAGE_SIZES" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="ALBUM_VALIDATE_IMAGE_SIZES" value="1" {{if $data.ALBUM_VALIDATE_IMAGE_SIZES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_album_image_size_validation_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_edit_album_image_size_validation_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=3}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_post_edit_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_posts_initial_rating}}</td>
					<td class="de_control">
						<select name="POST_INITIAL_RATING">
							<option value="0" {{if $data.POST_INITIAL_RATING==0}}selected{{/if}}>0</option>
							<option value="1" {{if $data.POST_INITIAL_RATING==1}}selected{{/if}}>1</option>
							<option value="2" {{if $data.POST_INITIAL_RATING==2}}selected{{/if}}>2</option>
							<option value="3" {{if $data.POST_INITIAL_RATING==3}}selected{{/if}}>3</option>
							<option value="4" {{if $data.POST_INITIAL_RATING==4}}selected{{/if}}>4</option>
							<option value="5" {{if $data.POST_INITIAL_RATING==5}}selected{{/if}}>5</option>
							<option value="6" {{if $data.POST_INITIAL_RATING==6}}selected{{/if}}>6</option>
							<option value="7" {{if $data.POST_INITIAL_RATING==7}}selected{{/if}}>7</option>
							<option value="8" {{if $data.POST_INITIAL_RATING==8}}selected{{/if}}>8</option>
							<option value="9" {{if $data.POST_INITIAL_RATING==9}}selected{{/if}}>9</option>
							<option value="10" {{if $data.POST_INITIAL_RATING==10}}selected{{/if}}>10</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_posts_initial_rating_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_post_default_user}}</td>
					<td class="de_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$data.DEFAULT_USER_IN_ADMIN_ADD_POST_ID}}</span>
								{{/if}}
							</div>
							<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_POST" maxlength="1000" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_POST}}"/>
						</div>
						<span class="de_hint">{{$lang.settings.system_field_add_post_default_user_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_post_default_status}}</td>
					<td class="de_control">
						<select name="DEFAULT_STATUS_IN_ADMIN_ADD_POST">
							<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_POST==1}}selected{{/if}}>{{$lang.settings.system_field_add_post_default_status_active}}</option>
							<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_POST!=1}}selected{{/if}}>{{$lang.settings.system_field_add_post_default_status_disabled}}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_add_post_post_date_time}}</td>
					<td class="de_control">
						<select name="USE_POST_DATE_RANDOMIZATION_POST">
							<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==0}}selected{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_none}}</option>
							<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==1}}selected{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_random}}</option>
							<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==2}}selected{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_current}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.system_field_add_post_post_date_time_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_edit_post_directory_autogeneration}}</td>
					<td class="de_control">
						<input type="hidden" name="POST_REGENERATE_DIRECTORIES" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="POST_REGENERATE_DIRECTORIES" value="1" {{if $data.POST_REGENERATE_DIRECTORIES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_post_directory_autogeneration_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_edit_post_directory_autogeneration_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.system_field_edit_post_check_duplicate_titles}}</td>
					<td class="de_control">
						<input type="hidden" name="POST_CHECK_DUPLICATE_TITLES" value="0"/>
						<span class="de_lv_pair"><input type="checkbox" name="POST_CHECK_DUPLICATE_TITLES" value="1" {{if $data.POST_CHECK_DUPLICATE_TITLES==1}}checked{{/if}}/><label>{{$lang.settings.system_field_edit_post_check_duplicate_titles_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.system_field_edit_post_check_duplicate_titles_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.system_divider_api_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_api_enable}}</td>
				<td class="de_control">
					<input type="hidden" name="API_ENABLE" value="0"/>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="API_ENABLE" value="1" {{if $data.API_ENABLE==1}}checked{{/if}}/><label>{{$lang.settings.system_field_api_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.system_field_api_enable_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_dependent API_ENABLE_on">{{$lang.settings.system_field_api_password}}</td>
				<td class="de_control">
					<input type="text" name="API_PASSWORD" maxlength="1000" class="API_ENABLE_on" value="{{$data.API_PASSWORD}}"/>
					<span class="de_hint">{{$lang.settings.system_field_api_password_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_dependent">{{$lang.settings.system_field_api_url}}</td>
				<td class="de_control">
					<span>
						{{$config.project_url}}/admin/api/{{$config.billing_scripts_name}}.php
					</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{elseif $smarty.request.page=='website_settings'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_stats">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.website_header}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.website_divider_general_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_disable_website}}</td>
				<td class="de_control">
					<input type="hidden" name="DISABLE_WEBSITE" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="DISABLE_WEBSITE" value="1" {{if $data.DISABLE_WEBSITE==1}}checked{{else}} data-confirm-save="{{$lang.settings.website_field_disable_website_confirm}}"{{/if}}/><label>{{$lang.settings.website_field_disable_website_disabled}}</label></span>
					<span class="de_hint">{{$lang.settings.website_field_disable_website_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_website_caching}}</td>
				<td class="de_control">
					<select name="WEBSITE_CACHING">
						<option value="">{{$lang.settings.website_field_website_caching_full}}</option>
						<option value="1" {{if $data.WEBSITE_CACHING=='1'}}selected{{/if}}>{{$lang.settings.website_field_website_caching_file}}</option>
						<option value="2" {{if $data.WEBSITE_CACHING=='2'}}selected{{/if}} data-confirm-save="{{$lang.settings.website_field_website_caching_confirm}}">{{$lang.settings.website_field_website_caching_disabled}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.website_field_website_caching_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_dynamic_params}}</td>
				<td class="de_control">
					<table>
						<tr>
							<td>{{$lang.settings.website_field_dynamic_params_names}}</td>
							<td>
								<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" size="10" value="{{$data.DYNAMIC_PARAMS.0}}"/>
								<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" size="10" value="{{$data.DYNAMIC_PARAMS.1}}"/>
								<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" size="10" value="{{$data.DYNAMIC_PARAMS.2}}"/>
								<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" size="10" value="{{$data.DYNAMIC_PARAMS.3}}"/>
								<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" size="10" value="{{$data.DYNAMIC_PARAMS.4}}"/>
							</td>
						</tr>
						<tr>
							<td>{{$lang.settings.website_field_dynamic_params_default_values}}</td>
							<td>
								<input type="text" name="DYNAMIC_PARAMS_VALUES[]" size="10" value="{{$data.DYNAMIC_PARAMS_VALUES.0}}"/>
								<input type="text" name="DYNAMIC_PARAMS_VALUES[]" size="10" value="{{$data.DYNAMIC_PARAMS_VALUES.1}}"/>
								<input type="text" name="DYNAMIC_PARAMS_VALUES[]" size="10" value="{{$data.DYNAMIC_PARAMS_VALUES.2}}"/>
								<input type="text" name="DYNAMIC_PARAMS_VALUES[]" size="10" value="{{$data.DYNAMIC_PARAMS_VALUES.3}}"/>
								<input type="text" name="DYNAMIC_PARAMS_VALUES[]" size="10" value="{{$data.DYNAMIC_PARAMS_VALUES.4}}"/>
							</td>
						</tr>
						<tr>
							<td>{{$lang.settings.website_field_dynamic_params_lifetimes}}</td>
							<td>
								<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" size="10" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.0|default:"360"}}"/>
								<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" size="10" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.1|default:"360"}}"/>
								<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" size="10" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.2|default:"360"}}"/>
								<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" size="10" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.3|default:"360"}}"/>
								<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" size="10" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.4|default:"360"}}"/>
							</td>
						</tr>
					</table>
					<span class="de_hint">{{$lang.settings.website_field_dynamic_params_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_disabled_content_availability}}</td>
				<td class="de_control">
					<select name="DISABLED_CONTENT_AVAILABILITY">
						<option value="2" {{if $data.DISABLED_CONTENT_AVAILABILITY=='2'}}selected{{/if}}>{{$lang.settings.website_field_disabled_content_availability_2}}</option>
						<option value="0" {{if $data.DISABLED_CONTENT_AVAILABILITY=='0'}}selected{{/if}}>{{$lang.settings.website_field_disabled_content_availability_0}}</option>
						<option value="1" {{if $data.DISABLED_CONTENT_AVAILABILITY=='1'}}selected{{/if}}>{{$lang.settings.website_field_disabled_content_availability_1}}</option>
						<option value="3" {{if $data.DISABLED_CONTENT_AVAILABILITY=='3'}}selected{{/if}}>{{$lang.settings.website_field_disabled_content_availability_3}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.website_field_disabled_content_availability_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_allow_iframing}}</td>
				<td class="de_control">
					<input type="hidden" name="ALLOW_IFRAMES" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="ALLOW_IFRAMES" value="1" {{if $data.ALLOW_IFRAMES==1}}checked{{/if}}/><label>{{$lang.settings.website_field_allow_iframing_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.website_field_allow_iframing_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_allow_multisession}}</td>
				<td class="de_control">
					<input type="hidden" name="ALLOW_MULTISESSION" value="0"/>
					<span class="de_lv_pair"><input type="checkbox" name="ALLOW_MULTISESSION" value="1" {{if $data.ALLOW_MULTISESSION==1}}checked{{/if}}/><label>{{$lang.settings.website_field_allow_multisession_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.website_field_allow_multisession_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.website_divider_url_patterns}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.website_divider_url_patterns_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.website_field_video_website_link_pattern}}</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN}}"/>
					<span class="de_hint">{{$lang.settings.website_field_video_website_link_pattern_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label de_required">{{$lang.settings.website_field_album_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_ALBUM" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_ALBUM}}"/>
						<span class="de_hint">{{$lang.settings.website_field_album_website_link_pattern_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.settings.website_field_album_image_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_IMAGE" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_IMAGE}}"/>
						<span class="de_hint">{{$lang.settings.website_field_album_image_website_link_pattern_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_label">{{$lang.settings.website_field_playlist_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_PLAYLIST" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_PLAYLIST}}"/>
						<span class="de_hint">{{$lang.settings.website_field_playlist_website_link_pattern_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.website_field_model_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_MODEL" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_MODEL}}"/>
						<span class="de_hint">{{$lang.settings.website_field_model_website_link_pattern_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.website_field_content_source_website_link_pattern}}</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_CS" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_CS}}"/>
					<span class="de_hint">{{$lang.settings.website_field_content_source_website_link_pattern_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.settings.website_field_dvd_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_DVD" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_DVD}}"/>
						<span class="de_hint">{{$lang.settings.website_field_dvd_website_link_pattern_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.website_field_dvd_group_website_link_pattern}}</td>
					<td class="de_control">
						<input type="text" name="WEBSITE_LINK_PATTERN_DVD_GROUP" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_DVD_GROUP}}"/>
						<span class="de_hint">{{$lang.settings.website_field_dvd_group_website_link_pattern_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.website_field_search_website_link_pattern}}</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_SEARCH" maxlength="1000" value="{{$data.WEBSITE_LINK_PATTERN_SEARCH}}"/>
					<span class="de_hint">{{$lang.settings.website_field_search_website_link_pattern_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_301_redirect_append_parameters}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="APPEND_PARAMETERS_FOR_301" value="1" {{if $data.APPEND_PARAMETERS_FOR_301==1}}checked{{/if}}/><label>{{$lang.settings.website_field_301_redirect_append_parameters_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.website_field_301_redirect_append_parameters_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.website_divider_overload}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_overload_min_mysql_processes}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="OVERLOAD_MIN_MYSQL_PROCESSES" maxlength="10" size="10" value="{{$data.OVERLOAD_MIN_MYSQL_PROCESSES}}"/>
					</span>
					<span>
						{{$lang.settings.website_field_overload_min_mysql_processes_recommended}}
					</span>
					<span class="de_hint">{{$lang.settings.website_field_overload_min_mysql_processes_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_overload_max_mysql_processes}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="OVERLOAD_MAX_MYSQL_PROCESSES" maxlength="10" size="10" value="{{$data.OVERLOAD_MAX_MYSQL_PROCESSES}}"/>
					</span>
					<span>
						{{$lang.settings.website_field_overload_max_mysql_processes_recommended}}
					</span>
					<span class="de_hint">{{$lang.settings.website_field_overload_max_mysql_processes_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_overload_max_la_blocks}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="OVERLOAD_MAX_LA_BLOCKS" maxlength="10" size="10" value="{{$data.OVERLOAD_MAX_LA_BLOCKS}}"/>
					</span>
					<span>
						{{$lang.settings.website_field_overload_max_la_blocks_recommended}}
					</span>
					<span class="de_hint">
						{{$lang.settings.website_field_overload_max_la_blocks_hint}}
					</span>
					<span class="de_hint">
						{{$lang.settings.website_field_overload_max_la_blocks_hint2|replace:"%1%":$config.project_url|smarty:nodefaults}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_overload_max_la_pages}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="OVERLOAD_MAX_LA_PAGES" maxlength="10" size="10" value="{{$data.OVERLOAD_MAX_LA_PAGES}}"/>
					</span>
					<span>
						{{$lang.settings.website_field_overload_max_la_pages_recommended}}
					</span>
					<span class="de_hint">
						{{$lang.settings.website_field_overload_max_la_pages_hint}}
					</span>
					<span class="de_hint">
						{{$lang.settings.website_field_overload_max_la_pages_hint2|replace:"%1%":$config.project_url|smarty:nodefaults}}
					</span>
				</td>
			</tr>
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_label">{{$lang.settings.website_field_user_online_status_refresh}}</td>
					<td class="de_control">
						<input type="hidden" name="ENABLE_USER_ONLINE_STATUS_REFRESH" value="0"/>
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_ONLINE_STATUS_REFRESH" value="1" {{if $data.ENABLE_USER_ONLINE_STATUS_REFRESH==1}}checked{{/if}}/><label>{{$lang.settings.website_field_user_online_status_refresh_enabled}}</label></span>
						<input type="text" name="USER_ONLINE_STATUS_REFRESH_INTERVAL" maxlength="10" size="10" class="ENABLE_USER_ONLINE_STATUS_REFRESH_on" value="{{$data.USER_ONLINE_STATUS_REFRESH_INTERVAL}}"/>
						<span class="de_hint">{{$lang.settings.website_field_user_online_status_refresh_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.settings.website_field_user_new_messages_refresh}}</td>
					<td class="de_control">
						<input type="hidden" name="ENABLE_USER_MESSAGES_REFRESH" value="0"/>
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_MESSAGES_REFRESH" value="1" {{if $data.ENABLE_USER_MESSAGES_REFRESH==1}}checked{{/if}}/><label>{{$lang.settings.website_field_user_new_messages_refresh_enabled}}</label></span>
						<input type="text" name="USER_MESSAGES_REFRESH_INTERVAL" maxlength="10" size="10" class="ENABLE_USER_MESSAGES_REFRESH_on" value="{{$data.USER_MESSAGES_REFRESH_INTERVAL}}"/>
						<span class="de_hint">{{$lang.settings.website_field_user_new_messages_refresh_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.website_divider_blocked_words}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.website_divider_blocked_words_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_blocked_words}}</td>
				<td class="de_control">
					<textarea name="BLOCKED_WORDS" cols="30" rows="3">{{$data.BLOCKED_WORDS}}</textarea>
					<span class="de_hint">{{$lang.settings.website_field_blocked_words_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_blocked_words_replacement}}</td>
				<td class="de_control">
					<input type="text" name="BLOCKED_WORDS_REPLACEMENT" maxlength="1000" value="{{$data.BLOCKED_WORDS_REPLACEMENT}}"/>
					<span class="de_hint">{{$lang.settings.website_field_blocked_words_replacement_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_regexp_replacements}}</td>
				<td class="de_control">
					<textarea name="REGEX_REPLACEMENTS" cols="30" rows="3">{{$data.REGEX_REPLACEMENTS}}</textarea>
					<span class="de_hint">{{$lang.settings.website_field_regexp_replacements_hint|smarty:nodefalts}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.website_divider_other}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_pseudo_video_behavior}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="PSEUDO_VIDEO_BEHAVIOR">
									<option value="0" {{if $data.PSEUDO_VIDEO_BEHAVIOR==0}}selected{{/if}}>{{$lang.settings.website_field_pseudo_video_behavior_redirect}}</option>
									<option value="1" {{if $data.PSEUDO_VIDEO_BEHAVIOR==1}}selected{{/if}}>{{$lang.settings.website_field_pseudo_video_behavior_show_page}}</option>
								</select>
								<span class="de_hint">{{$lang.settings.website_field_pseudo_video_behavior_hint}}</span>
							</td>
						</tr>
						<tr class="PSEUDO_VIDEO_BEHAVIOR_0">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="PSEUDO_VIDEO_BEHAVIOR_OUTS" value="1" {{if $data.PSEUDO_VIDEO_BEHAVIOR_OUTS==1}}checked{{/if}}/><label>{{$lang.settings.website_field_pseudo_video_behavior_redirect_outs}}</label></span>
								<span class="de_hint">{{$lang.settings.website_field_pseudo_video_behavior_redirect_outs_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_website_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{elseif $smarty.request.page=='memberzone_settings'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_memberzone">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.memberzone_header}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.memberzone_divider_general_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_status_after_premium}}</td>
				<td class="de_control">
					<select name="STATUS_AFTER_PREMIUM">
						<option value="0" {{if $data.STATUS_AFTER_PREMIUM==0}}selected{{/if}}>{{$lang.settings.memberzone_field_status_after_premium_disabled}}</option>
						<option value="2" {{if $data.STATUS_AFTER_PREMIUM==2}}selected{{/if}}>{{$lang.settings.memberzone_field_status_after_premium_active}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.memberzone_field_status_after_premium_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_affiliate_param_name}}</td>
				<td class="de_control">
					<input type="text" name="AFFILIATE_PARAM_NAME" maxlength="100" value="{{$data.AFFILIATE_PARAM_NAME}}"/>
					<span class="de_hint">{{$lang.settings.memberzone_field_affiliate_param_name_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_generated_users_reuse_probability}}</td>
				<td class="de_control">
					<input type="text" name="GENERATED_USERS_REUSE_PROBABILITY" maxlength="100" value="{{$data.GENERATED_USERS_REUSE_PROBABILITY}}"/>
					<span class="de_hint">{{$lang.settings.memberzone_field_generated_users_reuse_probability_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.memberzone_divider_access_rules}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_access_rules_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_videos_access}}</td>
				<td class="de_control">
					<span>
						{{$lang.settings.memberzone_field_videos_access_type_public}}:
						<select name="PUBLIC_VIDEOS_ACCESS">
							<option value="0" {{if $data.PUBLIC_VIDEOS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
							<option value="1" {{if $data.PUBLIC_VIDEOS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
							<option value="2" {{if $data.PUBLIC_VIDEOS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.memberzone_field_videos_access_type_private}}:
						<select name="PRIVATE_VIDEOS_ACCESS">
							<option value="3" {{if $data.PRIVATE_VIDEOS_ACCESS==3}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
							<option value="0" {{if $data.PRIVATE_VIDEOS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
							<option value="1" {{if $data.PRIVATE_VIDEOS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_friends}}</option>
							<option value="2" {{if $data.PRIVATE_VIDEOS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.memberzone_field_videos_access_type_premium}}:
						<select name="PREMIUM_VIDEOS_ACCESS">
							<option value="0" {{if $data.PREMIUM_VIDEOS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
							<option value="1" {{if $data.PREMIUM_VIDEOS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
							<option value="2" {{if $data.PREMIUM_VIDEOS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
						</select>
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_videos_access_hint}}</span>
				</td>
			</tr>
			<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.settings.memberzone_field_albums_access}}</td>
				<td class="de_control">
					<span>
						{{$lang.settings.memberzone_field_albums_access_type_public}}:
						<select name="PUBLIC_ALBUMS_ACCESS">
							<option value="0" {{if $data.PUBLIC_ALBUMS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
							<option value="1" {{if $data.PUBLIC_ALBUMS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
							<option value="2" {{if $data.PUBLIC_ALBUMS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.memberzone_field_albums_access_type_private}}:
						<select name="PRIVATE_ALBUMS_ACCESS">
							<option value="3" {{if $data.PRIVATE_ALBUMS_ACCESS==3}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
							<option value="0" {{if $data.PRIVATE_ALBUMS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
							<option value="1" {{if $data.PRIVATE_ALBUMS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_friends}}</option>
							<option value="2" {{if $data.PRIVATE_ALBUMS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
						</select>
					</span>
					<span>
						{{$lang.settings.memberzone_field_albums_access_type_premium}}:
						<select name="PREMIUM_ALBUMS_ACCESS">
							<option value="0" {{if $data.PREMIUM_ALBUMS_ACCESS==0}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
							<option value="1" {{if $data.PREMIUM_ALBUMS_ACCESS==1}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
							<option value="2" {{if $data.PREMIUM_ALBUMS_ACCESS==2}}selected{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
						</select>
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_albums_access_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_purchase_videos}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PUBLIC_VIDEO" value="1" {{if $data.ENABLE_TOKENS_PUBLIC_VIDEO==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_videos_type_public}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PUBLIC_VIDEO" maxlength="10" size="5" class="ENABLE_TOKENS_PUBLIC_VIDEO_on" value="{{$data.DEFAULT_TOKENS_PUBLIC_VIDEO}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_videos_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PRIVATE_VIDEO" value="1" {{if $data.ENABLE_TOKENS_PRIVATE_VIDEO==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_videos_type_private}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PRIVATE_VIDEO" maxlength="10" size="5" class="ENABLE_TOKENS_PRIVATE_VIDEO_on" value="{{$data.DEFAULT_TOKENS_PRIVATE_VIDEO}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_videos_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PREMIUM_VIDEO" value="1" {{if $data.ENABLE_TOKENS_PREMIUM_VIDEO==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_videos_type_premium}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PREMIUM_VIDEO" maxlength="10" size="5" class="ENABLE_TOKENS_PREMIUM_VIDEO_on" value="{{$data.DEFAULT_TOKENS_PREMIUM_VIDEO}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_videos_tokens}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_purchase_videos_hint}}</span>
				</td>
			</tr>
			<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_purchase_albums}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PUBLIC_ALBUM" value="1" {{if $data.ENABLE_TOKENS_PUBLIC_ALBUM==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_albums_type_public}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PUBLIC_ALBUM" maxlength="10" size="5" class="ENABLE_TOKENS_PUBLIC_ALBUM_on" value="{{$data.DEFAULT_TOKENS_PUBLIC_ALBUM}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_albums_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PRIVATE_ALBUM" value="1" {{if $data.ENABLE_TOKENS_PRIVATE_ALBUM==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_albums_type_private}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PRIVATE_ALBUM" maxlength="10" size="5" class="ENABLE_TOKENS_PRIVATE_ALBUM_on" value="{{$data.DEFAULT_TOKENS_PRIVATE_ALBUM}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_albums_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_PREMIUM_ALBUM" value="1" {{if $data.ENABLE_TOKENS_PREMIUM_ALBUM==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_albums_type_premium}}</label></span>
					<span>
						<input type="text" name="DEFAULT_TOKENS_PREMIUM_ALBUM" maxlength="10" size="5" class="ENABLE_TOKENS_PREMIUM_ALBUM_on" value="{{$data.DEFAULT_TOKENS_PREMIUM_ALBUM}}"/>
						{{$lang.settings.memberzone_field_tokens_purchase_albums_tokens}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_purchase_albums_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_purchase_expiry}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="TOKENS_PURCHASE_EXPIRY" maxlength="10" size="5" value="{{$data.TOKENS_PURCHASE_EXPIRY}}"/>
						{{$lang.settings.memberzone_field_purchase_expiry_days}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_purchase_expiry_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_messages}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_MESSAGES_ACTIVE" value="1" {{if $data.ENABLE_TOKENS_MESSAGES_ACTIVE==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_messages_active}}</label></span>
					<span>
						<input type="text" name="TOKENS_MESSAGES_ACTIVE" maxlength="10" size="5" class="ENABLE_TOKENS_MESSAGES_ACTIVE_on" value="{{$data.TOKENS_MESSAGES_ACTIVE}}"/>
						{{$lang.settings.memberzone_field_tokens_messages_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_MESSAGES_PREMIUM" value="1" {{if $data.ENABLE_TOKENS_MESSAGES_PREMIUM==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_messages_premium}}</label></span>
					<span>
						<input type="text" name="TOKENS_MESSAGES_PREMIUM" maxlength="10" size="5" class="ENABLE_TOKENS_MESSAGES_PREMIUM_on" value="{{$data.TOKENS_MESSAGES_PREMIUM}}"/>
						{{$lang.settings.memberzone_field_tokens_messages_tokens}}
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_MESSAGES_WEBMASTERS" value="1" {{if $data.ENABLE_TOKENS_MESSAGES_WEBMASTERS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_messages_webmasters}}</label></span>
					<span>
						<input type="text" name="TOKENS_MESSAGES_WEBMASTERS" maxlength="10" size="5" class="ENABLE_TOKENS_MESSAGES_WEBMASTERS_on" value="{{$data.TOKENS_MESSAGES_WEBMASTERS}}"/>
						{{$lang.settings.memberzone_field_tokens_messages_tokens}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_messages_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.memberzone_divider_paid_subscriptions}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_paid_subscriptions_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label ENABLE_TOKENS_SUBSCRIBE_MEMBERS_on">{{$lang.settings.memberzone_field_tokens_subscribe_members}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_SUBSCRIBE_MEMBERS" value="1" {{if $data.ENABLE_TOKENS_SUBSCRIBE_MEMBERS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_subscribe_members_enabled}}</label></span>
					<span>
						<input type="text" name="TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE" class="ENABLE_TOKENS_SUBSCRIBE_MEMBERS_on" maxlength="10" size="5" value="{{$data.TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE}}"/>
						{{$lang.settings.memberzone_field_tokens_subscribe_members_tokens}}
					</span>
					<span>
						<input type="text" name="TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD" class="ENABLE_TOKENS_SUBSCRIBE_MEMBERS_on" maxlength="10" size="5" value="{{$data.TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD}}"/>
						{{$lang.settings.memberzone_field_tokens_subscribe_members_days}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_subscribe_members_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label ENABLE_TOKENS_SUBSCRIBE_DVDS_on">{{$lang.settings.memberzone_field_tokens_subscribe_dvds}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_SUBSCRIBE_DVDS" value="1" {{if $data.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_subscribe_dvds_enabled}}</label></span>
					<span>
						<input type="text" name="TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE" class="ENABLE_TOKENS_SUBSCRIBE_DVDS_on" maxlength="10" size="5" value="{{$data.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE}}"/>
						{{$lang.settings.memberzone_field_tokens_subscribe_dvds_tokens}}
					</span>
					<span>
						<input type="text" name="TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD" class="ENABLE_TOKENS_SUBSCRIBE_DVDS_on" maxlength="10" size="5" value="{{$data.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD}}"/>
						{{$lang.settings.memberzone_field_tokens_subscribe_dvds_days}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_subscribe_dvds_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.memberzone_divider_tokens_earnings}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_tokens_earnings_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_sale}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_VIDEOS" value="1" {{if $data.ENABLE_TOKENS_SALE_VIDEOS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_videos}}</label></span>
					{{if $config.installation_type==4}}
						<span class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_ALBUMS" value="1" {{if $data.ENABLE_TOKENS_SALE_ALBUMS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_albums}}</label></span>
					{{/if}}
					<span class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_MEMBERS" value="1" {{if $data.ENABLE_TOKENS_SALE_MEMBERS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_members}}</label></span>
					{{if $config.installation_type==4 && $config.dvds_mode=='channels'}}
						<span class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_DVDS" value="1" {{if $data.ENABLE_TOKENS_SALE_DVDS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_dvds}}</label></span>
					{{/if}}
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_dependent">{{$lang.settings.memberzone_field_tokens_sale_interest}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="TOKENS_SALE_INTEREST" maxlength="10" size="5" value="{{$data.TOKENS_SALE_INTEREST}}"/>
						{{$lang.settings.memberzone_field_tokens_sale_interest_percent}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_interest_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label ENABLE_TOKENS_TRAFFIC_VIDEOS_on">{{$lang.settings.memberzone_field_tokens_traffic_enable_videos}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_TRAFFIC_VIDEOS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_VIDEOS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_enabled}}</label></span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_VIDEOS_TOKENS" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_VIDEOS_on" value="{{$data.TOKENS_TRAFFIC_VIDEOS_TOKENS}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_tokens}}
					</span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_VIDEOS_UNIQUE" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_VIDEOS_on" value="{{$data.TOKENS_TRAFFIC_VIDEOS_UNIQUE}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_unique}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_hint}}</span>
				</td>
			</tr>
			<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
				<td class="de_label ENABLE_TOKENS_TRAFFIC_ALBUMS_on">{{$lang.settings.memberzone_field_tokens_traffic_enable_albums}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_TRAFFIC_ALBUMS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_ALBUMS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_enabled}}</label></span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_ALBUMS_TOKENS" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_ALBUMS_on" value="{{$data.TOKENS_TRAFFIC_ALBUMS_TOKENS}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_tokens}}
					</span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_ALBUMS_UNIQUE" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_ALBUMS_on" value="{{$data.TOKENS_TRAFFIC_ALBUMS_UNIQUE}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_unique}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label ENABLE_TOKENS_TRAFFIC_EMBEDS_on">{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_TRAFFIC_EMBEDS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_EMBEDS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_enabled}}</label></span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_EMBEDS_TOKENS" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_EMBEDS_on" value="{{$data.TOKENS_TRAFFIC_EMBEDS_TOKENS}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_tokens}}
					</span>
					<span>
						<input type="text" name="TOKENS_TRAFFIC_EMBEDS_UNIQUE" maxlength="10" size="5" class="ENABLE_TOKENS_TRAFFIC_EMBEDS_on" value="{{$data.TOKENS_TRAFFIC_EMBEDS_UNIQUE}}"/>
						{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_unique}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_enable_messages_revenue}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_MESSAGES_REVENUE" value="1" {{if $data.ENABLE_TOKENS_MESSAGES_REVENUE==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_enable_messages_revenue_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_enable_messages_revenue_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_TOKENS_MESSAGES_REVENUE_on">
				<td class="de_label de_dependent">{{$lang.settings.memberzone_field_tokens_message_interest}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="TOKENS_MESSAGES_REVENUE_INTEREST" maxlength="10" size="5" value="{{$data.TOKENS_MESSAGES_REVENUE_INTEREST}}"/>
						{{$lang.settings.memberzone_field_tokens_message_interest_percent}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_message_interest_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_sale_excludes}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.memberzone_field_tokens_sale_excludes}}</span>
							<span class="js_param">url=async/insight.php?type=users</span>
							<span class="js_param">submit_mode=simple</span>
							<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						<input type="hidden" name="TOKENS_SALE_EXCLUDES" value="{{$data.TOKENS_SALE_EXCLUDES}}"/>
						<div class="controls">
							<input type="text" name="new_user" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.common.users_all}}"/>
						</div>
					</div>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_excludes_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_tokens_enable_donations}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TOKENS_DONATIONS" value="1" {{if $data.ENABLE_TOKENS_DONATIONS==1}}checked{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_enable_donations_enabled}}</label></span>
					<input type="text" name="TOKENS_DONATION_MIN" maxlength="10" size="5" class="ENABLE_TOKENS_DONATIONS_on" value="{{$data.TOKENS_DONATION_MIN}}"/>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_enable_donations_hint}}</span>
				</td>
			</tr>
			<tr class="ENABLE_TOKENS_DONATIONS_on">
				<td class="de_label de_dependent">{{$lang.settings.memberzone_field_tokens_donation_interest}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="TOKENS_DONATION_INTEREST" maxlength="10" size="5" value="{{$data.TOKENS_DONATION_INTEREST}}"/>
						{{$lang.settings.memberzone_field_tokens_donation_interest_percent}}
					</span>
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_donation_interest_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.memberzone_divider_activity_awards}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_activity_index_formula}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<input type="text" name="ACTIVITY_INDEX_FORMULA" maxlength="1000" value="{{$data.ACTIVITY_INDEX_FORMULA}}"/>
								<span class="de_hint">{{$lang.settings.memberzone_field_activity_index_formula_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span data-accordeon="formula_details_expander">{{$lang.settings.memberzone_field_activity_index_formula_hint2_show}}</span>
								<div class="formula_details_expander hidden"><br/>{{$lang.settings.memberzone_field_activity_index_formula_hint2}}</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.memberzone_field_activity_index_excludes}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.memberzone_field_activity_index_excludes}}</span>
							<span class="js_param">url=async/insight.php?type=users</span>
							<span class="js_param">submit_mode=simple</span>
							<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						<input type="hidden" name="ACTIVITY_INDEX_INCLUDES" value="{{$data.ACTIVITY_INDEX_INCLUDES}}"/>
						<div class="controls">
							<input type="text" name="new_user" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.common.users_all}}"/>
						</div>
					</div>
					<span class="de_hint">{{$lang.settings.memberzone_field_activity_index_excludes_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<tr class="eg_header">
							<td>{{$lang.settings.memberzone_awards_col_action}}</td>
							<td>{{$lang.settings.memberzone_awards_col_condition}}</td>
							<td>{{$lang.settings.memberzone_awards_col_tokens}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_signup}}</td>
							<td><input type="text" maxlength="10" size="5" disabled/> {{$lang.common.undefined}}</td>
							<td><input type="text" name="AWARDS_SIGNUP" maxlength="10" size="5" value="{{$data.AWARDS_SIGNUP}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_avatar}}</td>
							<td><input type="text" maxlength="10" size="5" disabled/> {{$lang.common.undefined}}</td>
							<td><input type="text" name="AWARDS_AVATAR" maxlength="10" size="5" value="{{$data.AWARDS_AVATAR}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_cover}}</td>
							<td><input type="text" maxlength="10" size="5" disabled/> {{$lang.common.undefined}}</td>
							<td><input type="text" name="AWARDS_COVER" maxlength="10" size="5" value="{{$data.AWARDS_COVER}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_login}}</td>
							<td><input type="text" name="AWARDS_LOGIN_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_LOGIN_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_interval}}</td>
							<td><input type="text" name="AWARDS_LOGIN" maxlength="10" size="5" value="{{$data.AWARDS_LOGIN}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_video}}</td>
							<td><input type="text" name="AWARDS_COMMENT_VIDEO_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_VIDEO_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_VIDEO" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_VIDEO}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_album}}</td>
							<td><input type="text" name="AWARDS_COMMENT_ALBUM_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_ALBUM_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_ALBUM" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_ALBUM}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_content_source}}</td>
							<td><input type="text" name="AWARDS_COMMENT_CS_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_CS_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_CS" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_CS}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_model}}</td>
							<td><input type="text" name="AWARDS_COMMENT_MODEL_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_MODEL_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_MODEL" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_MODEL}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_dvd}}</td>
							<td><input type="text" name="AWARDS_COMMENT_DVD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_DVD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_DVD" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_DVD}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_post}}</td>
							<td><input type="text" name="AWARDS_COMMENT_POST_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_POST_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_POST" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_POST}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_comment_playlist}}</td>
							<td><input type="text" name="AWARDS_COMMENT_PLAYLIST_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_PLAYLIST_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_COMMENT_PLAYLIST" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_PLAYLIST}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_video_upload}}</td>
							<td><input type="text" name="AWARDS_VIDEO_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_VIDEO_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_duration}}</td>
							<td><input type="text" name="AWARDS_VIDEO_UPLOAD" maxlength="10" size="5" value="{{$data.AWARDS_VIDEO_UPLOAD}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_album_upload}}</td>
							<td><input type="text" name="AWARDS_ALBUM_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_ALBUM_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_images}}</td>
							<td><input type="text" name="AWARDS_ALBUM_UPLOAD" maxlength="10" size="5" value="{{$data.AWARDS_ALBUM_UPLOAD}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_post_upload}}</td>
							<td><input type="text" name="AWARDS_POST_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_POST_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
							<td><input type="text" name="AWARDS_POST_UPLOAD" maxlength="10" size="5" value="{{$data.AWARDS_POST_UPLOAD}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.memberzone_awards_col_action_referral_signup}}</td>
							<td><input type="text" name="AWARDS_REFERRAL_SIGNUP_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_REFERRAL_SIGNUP_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_ip_unique}}</td>
							<td><input type="text" name="AWARDS_REFERRAL_SIGNUP" maxlength="10" size="5" value="{{$data.AWARDS_REFERRAL_SIGNUP}}"/></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_memberzone_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{elseif $smarty.request.page=='antispam_settings'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_antispam">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.antispam_header}}</h1></div>
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
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.antispam_header_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_blacklisting}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_blacklisted_words}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<textarea name="ANTISPAM_BLACKLIST_WORDS" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_WORDS}}</textarea>
								<span class="de_hint">{{$lang.settings.antispam_field_blacklisted_words_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="ANTISPAM_BLACKLIST_WORDS_IGNORE_FEEDBACKS" value="1" {{if $data.ANTISPAM_BLACKLIST_WORDS_IGNORE_FEEDBACKS==1}}checked{{/if}}/><label>{{$lang.settings.antispam_field_blacklisted_words_ignore_feedbacks}}</label></span>
								<span class="de_hint">{{$lang.settings.antispam_field_blacklisted_words_ignore_feedbacks_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_blacklisted_domains}}</td>
				<td class="de_control">
					<textarea name="ANTISPAM_BLACKLIST_DOMAINS" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_DOMAINS}}</textarea>
					<span class="de_hint">{{$lang.settings.antispam_field_blacklisted_domains_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_blacklisted_ips}}</td>
				<td class="de_control">
					<textarea name="ANTISPAM_BLACKLIST_IPS" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_IPS}}</textarea>
					<span class="de_hint">{{$lang.settings.antispam_field_blacklisted_ips_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_blacklisted_action}}</td>
				<td class="de_control">
					<select name="ANTISPAM_BLACKLIST_ACTION">
						<option value="0" {{if $data.ANTISPAM_BLACKLIST_ACTION==0}}selected{{/if}}>{{$lang.settings.antispam_field_blacklisted_action_delete}}</option>
						<option value="1" {{if $data.ANTISPAM_BLACKLIST_ACTION==1}}selected{{/if}}>{{$lang.settings.antispam_field_blacklisted_action_deactivate}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.antispam_field_blacklisted_action_hint}}</span>
				</td>
			</tr>
			{{if $config.installation_type>=3}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_videos}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_VIDEOS_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_VIDEOS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_VIDEOS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_VIDEOS"}}
				{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_videos}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_albums}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_ALBUMS_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_ALBUMS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_ALBUMS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_ALBUMS"}}
				{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_albums}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			{{if $config.installation_type>=3}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_posts}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_POSTS_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_POSTS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_POSTS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_POSTS"}}
				{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_posts}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			{{if $config.installation_type>=2}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_playlists}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_PLAYLISTS_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_PLAYLISTS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_PLAYLISTS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_PLAYLISTS"}}
				{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_playlists}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_dvds}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_DVDS_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_DVDS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_DVDS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_DVDS"}}
				{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_dvds}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_comments}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
				<td class="de_control">
					<select name="ANTISPAM_COMMENTS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_COMMENTS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_COMMENTS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_COMMENTS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR,DUPLICATES"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{elseif $action=='DUPLICATES'}}
					{{assign var="action_key" value='antispam_field_action_duplicates'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}</td>
					<td class="de_control">
						{{if $action=='DUPLICATES'}}
							{{assign var="action_variable" value="`$section`_`$action`"}}
							{{assign var="action_label_key" value="`$action_key`_delete"}}
							<span class="de_lv_pair"><input type="checkbox" name="{{$action_variable}}" value="1" {{if $data.$action_variable==1}}checked{{/if}}/><label>{{$lang.settings.$action_label_key}}</label></span>
						{{else}}
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_comments}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
						{{/if}}
						<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
					</td>
				</tr>
			{{/foreach}}
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_messages}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
					<td class="de_control">
						<select name="ANTISPAM_MESSAGES_ANALYZE_HISTORY">
							<option value="0" {{if $data.ANTISPAM_MESSAGES_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
							<option value="1" {{if $data.ANTISPAM_MESSAGES_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					</td>
				</tr>
				{{assign var="section" value="ANTISPAM_MESSAGES"}}
				{{assign var="actions" value=","|explode:"AUTODELETE,ERROR,DUPLICATES"}}
				{{foreach from=$actions item="action"}}
					{{assign var="action_key" value=""}}
					{{if $action=='FORCE_CAPTCHA'}}
						{{assign var="action_key" value='antispam_field_action_force_captcha'}}
					{{elseif $action=='FORCE_DISABLED'}}
						{{assign var="action_key" value='antispam_field_action_deactivate'}}
					{{elseif $action=='AUTODELETE'}}
						{{assign var="action_key" value='antispam_field_action_autodelete'}}
					{{elseif $action=='ERROR'}}
						{{assign var="action_key" value='antispam_field_action_show_error'}}
					{{elseif $action=='DUPLICATES'}}
						{{assign var="action_key" value='antispam_field_action_duplicates'}}
					{{/if}}
					{{assign var="action_hint_key" value="`$action_key`_hint"}}
					<tr>
						<td class="de_label">{{$lang.settings.$action_key}}</td>
						<td class="de_control">
							{{if $action=='DUPLICATES'}}
								{{assign var="action_variable" value="`$section`_`$action`"}}
								{{assign var="action_label_key" value="`$action_key`_delete"}}
								<span class="de_lv_pair"><input type="checkbox" name="{{$action_variable}}" value="1" {{if $data.$action_variable==1}}checked{{/if}}/><label>{{$lang.settings.$action_label_key}}</label></span>
							{{else}}
								<span>
									{{assign var="action_variable" value="`$section`_`$action`_1"}}
									<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
									{{$lang.settings.antispam_field_unit_messages}}
								</span>
								<span>
									{{assign var="action_variable" value="`$section`_`$action`_2"}}
									<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
									{{$lang.settings.antispam_field_unit_seconds}}
								</span>
							{{/if}}
							<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						</td>
					</tr>
				{{/foreach}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.antispam_divider_feedbacks}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}</td>
				<td class="de_control">
					<select name="ANTISPAM_FEEDBACKS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_FEEDBACKS_ANALYZE_HISTORY=='0'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_FEEDBACKS_ANALYZE_HISTORY=='1'}}selected{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_FEEDBACKS"}}
			{{assign var="actions" value=","|explode:"AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{elseif $action=='DUPLICATES'}}
					{{assign var="action_key" value='antispam_field_action_duplicates'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}</td>
					<td class="de_control">
						{{if $action=='DUPLICATES'}}
							{{assign var="action_variable" value="`$section`_`$action`"}}
							{{assign var="action_label_key" value="`$action_key`_delete"}}
							<span class="de_lv_pair"><input type="checkbox" name="{{$action_variable}}" value="1" {{if $data.$action_variable==1}}checked{{/if}}/><label>{{$lang.settings.$action_label_key}}</label></span>
						{{else}}
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_1"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_feedbacks}}
							</span>
							<span>
								{{assign var="action_variable" value="`$section`_`$action`_2"}}
								<input type="text" name="{{$action_variable}}" maxlength="10" size="5" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
								{{$lang.settings.antispam_field_unit_seconds}}
							</span>
						{{/if}}
						<span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
					</td>
				</tr>
			{{/foreach}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_antispam_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{elseif $smarty.request.page=='stats_settings'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_stats">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.stats_header}}</h1></div>
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
				<td class="de_control" colspan="2">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_traffic_stats" value="1" {{if $data.collect_traffic_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats}}</label></span>
					<span>({{$database_stats_summary.traffic|sizeToHumanString}})</span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_hint}}</span>
				</td>
			</tr>
			<tr class="collect_traffic_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_countries" value="1" {{if $data.collect_traffic_stats_countries==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_countries}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_countries_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_devices" value="1" {{if $data.collect_traffic_stats_devices==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_devices}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_devices_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_embed_domains" value="1" {{if $data.collect_traffic_stats_embed_domains==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_embed_domains}}</label></span>
								<span>({{$database_stats_summary.embed|sizeToHumanString}})</span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_embed_domains_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_keep_stats_for}}:
									<input type="text" name="keep_traffic_stats_period" class="collect_traffic_stats_on" size="5" value="{{$data.keep_traffic_stats_period}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_player_stats" value="1" {{if $data.collect_player_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats}}</label></span>
					<span>({{$database_stats_summary.player|sizeToHumanString}})</span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_hint}}</span>
				</td>
			</tr>
			<tr class="collect_player_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_player_stats_countries" value="1" {{if $data.collect_player_stats_countries==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_countries}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_countries_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_player_stats_devices" value="1" {{if $data.collect_player_stats_devices==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_devices}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_devices_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_player_stats_embed_profiles" value="1" {{if $data.collect_player_stats_embed_profiles==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_embed_profiles}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_embed_profiles_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_collect_player_stats_reporting}}:
									<select name="player_stats_reporting">
										<option value="0" {{if $data.player_stats_reporting==0}}selected{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_kvs}}</option>
										<option value="1" {{if $data.player_stats_reporting==1}}selected{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_ga}}</option>
										<option value="2" {{if $data.player_stats_reporting==2}}selected{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_both}}</option>
									</select>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_reporting_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_keep_stats_for}}:
									<input type="text" name="keep_player_stats_period" class="collect_player_stats_on" size="5" value="{{$data.keep_player_stats_period}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_videos_stats" value="1" {{if $data.collect_videos_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats}}</label></span>
					<span>({{$database_stats_summary.videos|sizeToHumanString}})</span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_hint}}</span>
				</td>
			</tr>
			<tr class="collect_videos_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_unique" value="1" {{if $data.collect_videos_stats_unique==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_unique}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_unique_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_video_plays" value="1" {{if $data.collect_videos_stats_video_plays==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_video_plays}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_video_plays_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_video_files" value="1" {{if $data.collect_videos_stats_video_files==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_video_files}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_video_files_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_keep_stats_for}}:
									<input type="text" name="keep_videos_stats_period" class="collect_videos_stats_on" size="5" value="{{$data.keep_videos_stats_period}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_control" colspan="2">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_albums_stats" value="1" {{if $data.collect_albums_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats}}</label></span>
						<span>({{$database_stats_summary.albums|sizeToHumanString}})</span>
						<span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_hint}}</span>
					</td>
				</tr>
				<tr class="collect_albums_stats_on">
					<td class="de_control" colspan="2">
						<table class="control_group">
							<tr>
								<td class="de_dependent">
									<span class="de_lv_pair"><input type="checkbox" name="collect_albums_stats_unique" value="1" {{if $data.collect_albums_stats_unique==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats_unique}}</label></span>
									<span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_unique_hint}}</span>
								</td>
							</tr>
							<tr>
								<td class="de_dependent">
									<span class="de_lv_pair"><input type="checkbox" name="collect_albums_stats_album_images" value="1" {{if $data.collect_albums_stats_album_images==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats_album_images}}</label></span>
									<span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_album_images_hint}}</span>
								</td>
							</tr>
							<tr>
								<td class="de_dependent">
									<span>
										{{$lang.settings.stats_field_keep_stats_for}}:
										<input type="text" name="keep_albums_stats_period" class="collect_albums_stats_on" size="5" value="{{$data.keep_albums_stats_period}}"/>
									</span>
									<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_memberzone_stats" value="1" {{if $data.collect_memberzone_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats}}</label></span>
					<span>({{$database_stats_summary.memberzone|sizeToHumanString}})</span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_hint}}</span>
				</td>
			</tr>
			<tr class="collect_memberzone_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_memberzone_stats_video_files" value="1" {{if $data.collect_memberzone_stats_video_files==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats_video_files}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_video_files_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="collect_memberzone_stats_album_images" value="1" {{if $data.collect_memberzone_stats_album_images==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats_album_images}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_album_images_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_keep_stats_for}}:
									<input type="text" name="keep_memberzone_stats_period" class="collect_memberzone_stats_on" size="5" value="{{$data.keep_memberzone_stats_period}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="collect_search_stats" value="1" {{if $data.collect_search_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_search_stats}}</label></span>
					<span>({{$database_stats_summary.search|sizeToHumanString}})</span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_search_stats_hint}}</span>
				</td>
			</tr>
			<tr class="collect_search_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="search_inactive" value="1" {{if $data.search_inactive==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_search_create_inactive}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_search_create_inactive_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span class="de_lv_pair"><input type="checkbox" name="search_to_lowercase" value="1" {{if $data.search_to_lowercase==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_search_to_lowercase}}</label></span>
								<span class="de_hint">{{$lang.settings.stats_field_search_to_lowercase_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_keep_stats_for}}:
									<input type="text" name="keep_search_stats_period" class="collect_search_stats_on" size="5" value="{{$data.keep_search_stats_period}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_search_max_length}}:
									<input type="text" name="search_max_length" class="collect_search_stats_on" size="5" value="{{if $data.search_max_length>0}}{{$data.search_max_length}}{{/if}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_search_max_length_hint}}</span>
							</td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.settings.stats_field_search_stop_symbols}}:
									<input type="text" name="search_stop_symbols" class="collect_search_stats_on" size="20" value="{{$data.search_stop_symbols}}"/>
								</span>
								<span class="de_hint">{{$lang.settings.stats_field_search_stop_symbols_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair"><input type="checkbox" name="collect_performance_stats" value="1" {{if $data.collect_performance_stats==1}}checked{{/if}}/><label>{{$lang.settings.stats_field_collect_performance_stats}}</label></span>
					<span class="de_hint">{{$lang.settings.stats_field_collect_performance_stats_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_stats_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{elseif $smarty.request.page=='customization'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_customization">
	<div class="de_main">
		<input type="hidden" name="ENABLE_VIDEO_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FLAG_1" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FLAG_2" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FLAG_3" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FLAG_1" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FLAG_2" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FLAG_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_5" value="0"/>
		{{foreach name="data" item="item" from=$list_posts_types}}
			{{section name="fields" start="1" loop=11}}
				<input type="hidden" name="ENABLE_POST_{{$item.post_type_id}}_FIELD_{{$smarty.section.fields.index}}" value="0"/>
				<input type="hidden" name="ENABLE_POST_{{$item.post_type_id}}_FILE_FIELD_{{$smarty.section.fields.index}}" value="0"/>
			{{/section}}
		{{/foreach}}

		<div class="de_header"><h1>{{$lang.settings.customization_header}}</h1></div>
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
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_medium"/>
							<col class="eg_column_medium"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.customization_col_id}}</td>
							<td>{{$lang.settings.customization_col_type}}</td>
							<td>{{$lang.settings.customization_col_field_name}}</td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_video}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FIELD_1" value="1" {{if $data.ENABLE_VIDEO_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="VIDEO_FIELD_1_NAME" class="ENABLE_VIDEO_FIELD_1_on" value="{{$data.VIDEO_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FIELD_2" value="1" {{if $data.ENABLE_VIDEO_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="VIDEO_FIELD_2_NAME" class="ENABLE_VIDEO_FIELD_2_on" value="{{$data.VIDEO_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FIELD_3" value="1" {{if $data.ENABLE_VIDEO_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="VIDEO_FIELD_3_NAME" class="ENABLE_VIDEO_FIELD_3_on" value="{{$data.VIDEO_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FLAG_1" value="1" {{if $data.ENABLE_VIDEO_FLAG_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_flag}}</td>
							<td><input type="text" name="VIDEO_FLAG_1_NAME" class="ENABLE_VIDEO_FLAG_1_on" value="{{$data.VIDEO_FLAG_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FLAG_2" value="1" {{if $data.ENABLE_VIDEO_FLAG_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_flag}}</td>
							<td><input type="text" name="VIDEO_FLAG_2_NAME" class="ENABLE_VIDEO_FLAG_2_on" value="{{$data.VIDEO_FLAG_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_VIDEO_FLAG_3" value="1" {{if $data.ENABLE_VIDEO_FLAG_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_flag}}</td>
							<td><input type="text" name="VIDEO_FLAG_3_NAME" class="ENABLE_VIDEO_FLAG_3_on" value="{{$data.VIDEO_FLAG_3_NAME}}"/></td>
						</tr>
						{{if $config.installation_type==4}}
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_album}}</td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FIELD_1" value="1" {{if $data.ENABLE_ALBUM_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="ALBUM_FIELD_1_NAME" class="ENABLE_ALBUM_FIELD_1_on" value="{{$data.ALBUM_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FIELD_2" value="1" {{if $data.ENABLE_ALBUM_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="ALBUM_FIELD_2_NAME" class="ENABLE_ALBUM_FIELD_2_on" value="{{$data.ALBUM_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FIELD_3" value="1" {{if $data.ENABLE_ALBUM_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="ALBUM_FIELD_3_NAME" class="ENABLE_ALBUM_FIELD_3_on" value="{{$data.ALBUM_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FLAG_1" value="1" {{if $data.ENABLE_ALBUM_FLAG_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_flag}}</td>
								<td><input type="text" name="ALBUM_FLAG_1_NAME" class="ENABLE_ALBUM_FLAG_1_on" value="{{$data.ALBUM_FLAG_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FLAG_2" value="1" {{if $data.ENABLE_ALBUM_FLAG_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_flag}}</td>
								<td><input type="text" name="ALBUM_FLAG_2_NAME" class="ENABLE_ALBUM_FLAG_2_on" value="{{$data.ALBUM_FLAG_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_ALBUM_FLAG_3" value="1" {{if $data.ENABLE_ALBUM_FLAG_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_flag_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_flag}}</td>
								<td><input type="text" name="ALBUM_FLAG_3_NAME" class="ENABLE_ALBUM_FLAG_3_on" value="{{$data.ALBUM_FLAG_3_NAME}}"/></td>
							</tr>
						{{/if}}
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_category}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_1_NAME" class="ENABLE_CATEGORY_FIELD_1_on" value="{{$data.CATEGORY_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_2_NAME" class="ENABLE_CATEGORY_FIELD_2_on" value="{{$data.CATEGORY_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_3_NAME" class="ENABLE_CATEGORY_FIELD_3_on" value="{{$data.CATEGORY_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_4" value="1" {{if $data.ENABLE_CATEGORY_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_4_NAME" class="ENABLE_CATEGORY_FIELD_4_on" value="{{$data.CATEGORY_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_5" value="1" {{if $data.ENABLE_CATEGORY_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_5_NAME" class="ENABLE_CATEGORY_FIELD_5_on" value="{{$data.CATEGORY_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_6" value="1" {{if $data.ENABLE_CATEGORY_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_6}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_6_NAME" class="ENABLE_CATEGORY_FIELD_6_on" value="{{$data.CATEGORY_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_7" value="1" {{if $data.ENABLE_CATEGORY_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_7}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_7_NAME" class="ENABLE_CATEGORY_FIELD_7_on" value="{{$data.CATEGORY_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_8" value="1" {{if $data.ENABLE_CATEGORY_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_8}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_8_NAME" class="ENABLE_CATEGORY_FIELD_8_on" value="{{$data.CATEGORY_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_9" value="1" {{if $data.ENABLE_CATEGORY_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_9}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_9_NAME" class="ENABLE_CATEGORY_FIELD_9_on" value="{{$data.CATEGORY_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FIELD_10" value="1" {{if $data.ENABLE_CATEGORY_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_10}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_FIELD_10_NAME" class="ENABLE_CATEGORY_FIELD_10_on" value="{{$data.CATEGORY_FIELD_10_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CATEGORY_FILE_FIELD_1_NAME" class="ENABLE_CATEGORY_FILE_FIELD_1_on" value="{{$data.CATEGORY_FILE_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CATEGORY_FILE_FIELD_2_NAME" class="ENABLE_CATEGORY_FILE_FIELD_2_on" value="{{$data.CATEGORY_FILE_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CATEGORY_FILE_FIELD_3_NAME" class="ENABLE_CATEGORY_FILE_FIELD_3_on" value="{{$data.CATEGORY_FILE_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_4" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CATEGORY_FILE_FIELD_4_NAME" class="ENABLE_CATEGORY_FILE_FIELD_4_on" value="{{$data.CATEGORY_FILE_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_5" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CATEGORY_FILE_FIELD_5_NAME" class="ENABLE_CATEGORY_FILE_FIELD_5_on" value="{{$data.CATEGORY_FILE_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_category_group}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_GROUP_FIELD_1_NAME" class="ENABLE_CATEGORY_GROUP_FIELD_1_on" value="{{$data.CATEGORY_GROUP_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_GROUP_FIELD_2_NAME" class="ENABLE_CATEGORY_GROUP_FIELD_2_on" value="{{$data.CATEGORY_GROUP_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CATEGORY_GROUP_FIELD_3_NAME" class="ENABLE_CATEGORY_GROUP_FIELD_3_on" value="{{$data.CATEGORY_GROUP_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_tag}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAG_FIELD_1" value="1" {{if $data.ENABLE_TAG_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="TAG_FIELD_1_NAME" class="ENABLE_TAG_FIELD_1_on" value="{{$data.TAG_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAG_FIELD_2" value="1" {{if $data.ENABLE_TAG_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="TAG_FIELD_2_NAME" class="ENABLE_TAG_FIELD_2_on" value="{{$data.TAG_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAG_FIELD_3" value="1" {{if $data.ENABLE_TAG_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="TAG_FIELD_3_NAME" class="ENABLE_TAG_FIELD_3_on" value="{{$data.TAG_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAG_FIELD_4" value="1" {{if $data.ENABLE_TAG_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="TAG_FIELD_4_NAME" class="ENABLE_TAG_FIELD_4_on" value="{{$data.TAG_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_TAG_FIELD_5" value="1" {{if $data.ENABLE_TAG_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="TAG_FIELD_5_NAME" class="ENABLE_TAG_FIELD_5_on" value="{{$data.TAG_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_content_source}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_1" value="1" {{if $data.ENABLE_CS_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_1_NAME" class="ENABLE_CS_FIELD_1_on" value="{{$data.CS_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_2" value="1" {{if $data.ENABLE_CS_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_2_NAME" class="ENABLE_CS_FIELD_2_on" value="{{$data.CS_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_3" value="1" {{if $data.ENABLE_CS_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_3_NAME" class="ENABLE_CS_FIELD_3_on" value="{{$data.CS_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_4" value="1" {{if $data.ENABLE_CS_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_4_NAME" class="ENABLE_CS_FIELD_4_on" value="{{$data.CS_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_5" value="1" {{if $data.ENABLE_CS_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_5_NAME" class="ENABLE_CS_FIELD_5_on" value="{{$data.CS_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_6" value="1" {{if $data.ENABLE_CS_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_6}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_6_NAME" class="ENABLE_CS_FIELD_6_on" value="{{$data.CS_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_7" value="1" {{if $data.ENABLE_CS_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_7}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_7_NAME" class="ENABLE_CS_FIELD_7_on" value="{{$data.CS_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_8" value="1" {{if $data.ENABLE_CS_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_8}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_8_NAME" class="ENABLE_CS_FIELD_8_on" value="{{$data.CS_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_9" value="1" {{if $data.ENABLE_CS_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_9}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_9_NAME" class="ENABLE_CS_FIELD_9_on" value="{{$data.CS_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FIELD_10" value="1" {{if $data.ENABLE_CS_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_10}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_FIELD_10_NAME" class="ENABLE_CS_FIELD_10_on" value="{{$data.CS_FIELD_10_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_1" value="1" {{if $data.ENABLE_CS_FILE_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_1_NAME" class="ENABLE_CS_FILE_FIELD_1_on" value="{{$data.CS_FILE_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_2" value="1" {{if $data.ENABLE_CS_FILE_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_2_NAME" class="ENABLE_CS_FILE_FIELD_2_on" value="{{$data.CS_FILE_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_3" value="1" {{if $data.ENABLE_CS_FILE_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_3_NAME" class="ENABLE_CS_FILE_FIELD_3_on" value="{{$data.CS_FILE_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_4" value="1" {{if $data.ENABLE_CS_FILE_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_4_NAME" class="ENABLE_CS_FILE_FIELD_4_on" value="{{$data.CS_FILE_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_5" value="1" {{if $data.ENABLE_CS_FILE_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_5_NAME" class="ENABLE_CS_FILE_FIELD_5_on" value="{{$data.CS_FILE_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_6" value="1" {{if $data.ENABLE_CS_FILE_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_6}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_6_NAME" class="ENABLE_CS_FILE_FIELD_6_on" value="{{$data.CS_FILE_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_7" value="1" {{if $data.ENABLE_CS_FILE_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_7}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_7_NAME" class="ENABLE_CS_FILE_FIELD_7_on" value="{{$data.CS_FILE_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_8" value="1" {{if $data.ENABLE_CS_FILE_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_8}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_8_NAME" class="ENABLE_CS_FILE_FIELD_8_on" value="{{$data.CS_FILE_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_9" value="1" {{if $data.ENABLE_CS_FILE_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_9}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_9_NAME" class="ENABLE_CS_FILE_FIELD_9_on" value="{{$data.CS_FILE_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_FILE_FIELD_10" value="1" {{if $data.ENABLE_CS_FILE_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_10}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="CS_FILE_FIELD_10_NAME" class="ENABLE_CS_FILE_FIELD_10_on" value="{{$data.CS_FILE_FIELD_10_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_content_source_group}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_GROUP_FIELD_1" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_GROUP_FIELD_1_NAME" class="ENABLE_CS_GROUP_FIELD_1_on" value="{{$data.CS_GROUP_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_GROUP_FIELD_2" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_GROUP_FIELD_2_NAME" class="ENABLE_CS_GROUP_FIELD_2_on" value="{{$data.CS_GROUP_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_GROUP_FIELD_3" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_GROUP_FIELD_3_NAME" class="ENABLE_CS_GROUP_FIELD_3_on" value="{{$data.CS_GROUP_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_GROUP_FIELD_4" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_GROUP_FIELD_4_NAME" class="ENABLE_CS_GROUP_FIELD_4_on" value="{{$data.CS_GROUP_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_CS_GROUP_FIELD_5" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="CS_GROUP_FIELD_5_NAME" class="ENABLE_CS_GROUP_FIELD_5_on" value="{{$data.CS_GROUP_FIELD_5_NAME}}"/></td>
						</tr>
						{{if $config.installation_type>=2}}
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_model}}</td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_1" value="1" {{if $data.ENABLE_MODEL_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_1_NAME" class="ENABLE_MODEL_FIELD_1_on" value="{{$data.MODEL_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_2" value="1" {{if $data.ENABLE_MODEL_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_2_NAME" class="ENABLE_MODEL_FIELD_2_on" value="{{$data.MODEL_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_3" value="1" {{if $data.ENABLE_MODEL_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_3_NAME" class="ENABLE_MODEL_FIELD_3_on" value="{{$data.MODEL_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_4" value="1" {{if $data.ENABLE_MODEL_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_4_NAME" class="ENABLE_MODEL_FIELD_4_on" value="{{$data.MODEL_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_5" value="1" {{if $data.ENABLE_MODEL_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_5_NAME" class="ENABLE_MODEL_FIELD_5_on" value="{{$data.MODEL_FIELD_5_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_6" value="1" {{if $data.ENABLE_MODEL_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_6}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_6_NAME" class="ENABLE_MODEL_FIELD_6_on" value="{{$data.MODEL_FIELD_6_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_7" value="1" {{if $data.ENABLE_MODEL_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_7}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_7_NAME" class="ENABLE_MODEL_FIELD_7_on" value="{{$data.MODEL_FIELD_7_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_8" value="1" {{if $data.ENABLE_MODEL_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_8}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_8_NAME" class="ENABLE_MODEL_FIELD_8_on" value="{{$data.MODEL_FIELD_8_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_9" value="1" {{if $data.ENABLE_MODEL_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_9}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_9_NAME" class="ENABLE_MODEL_FIELD_9_on" value="{{$data.MODEL_FIELD_9_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FIELD_10" value="1" {{if $data.ENABLE_MODEL_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_10}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="MODEL_FIELD_10_NAME" class="ENABLE_MODEL_FIELD_10_on" value="{{$data.MODEL_FIELD_10_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FILE_FIELD_1" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="MODEL_FILE_FIELD_1_NAME" class="ENABLE_MODEL_FILE_FIELD_1_on" value="{{$data.MODEL_FILE_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FILE_FIELD_2" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="MODEL_FILE_FIELD_2_NAME" class="ENABLE_MODEL_FILE_FIELD_2_on" value="{{$data.MODEL_FILE_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FILE_FIELD_3" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="MODEL_FILE_FIELD_3_NAME" class="ENABLE_MODEL_FILE_FIELD_3_on" value="{{$data.MODEL_FILE_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FILE_FIELD_4" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="MODEL_FILE_FIELD_4_NAME" class="ENABLE_MODEL_FILE_FIELD_4_on" value="{{$data.MODEL_FILE_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_MODEL_FILE_FIELD_5" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="MODEL_FILE_FIELD_5_NAME" class="ENABLE_MODEL_FILE_FIELD_5_on" value="{{$data.MODEL_FILE_FIELD_5_NAME}}"/></td>
							</tr>
						{{/if}}
						{{if $config.installation_type==4}}
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_dvd}}</td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_1" value="1" {{if $data.ENABLE_DVD_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_1_NAME" class="ENABLE_DVD_FIELD_1_on" value="{{$data.DVD_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_2" value="1" {{if $data.ENABLE_DVD_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_2_NAME" class="ENABLE_DVD_FIELD_2_on" value="{{$data.DVD_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_3" value="1" {{if $data.ENABLE_DVD_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_3_NAME" class="ENABLE_DVD_FIELD_3_on" value="{{$data.DVD_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_4" value="1" {{if $data.ENABLE_DVD_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_4_NAME" class="ENABLE_DVD_FIELD_4_on" value="{{$data.DVD_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_5" value="1" {{if $data.ENABLE_DVD_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_5_NAME" class="ENABLE_DVD_FIELD_5_on" value="{{$data.DVD_FIELD_5_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_6" value="1" {{if $data.ENABLE_DVD_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_6}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_6_NAME" class="ENABLE_DVD_FIELD_6_on" value="{{$data.DVD_FIELD_6_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_7" value="1" {{if $data.ENABLE_DVD_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_7}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_7_NAME" class="ENABLE_DVD_FIELD_7_on" value="{{$data.DVD_FIELD_7_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_8" value="1" {{if $data.ENABLE_DVD_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_8}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_8_NAME" class="ENABLE_DVD_FIELD_8_on" value="{{$data.DVD_FIELD_8_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_9" value="1" {{if $data.ENABLE_DVD_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_9}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_9_NAME" class="ENABLE_DVD_FIELD_9_on" value="{{$data.DVD_FIELD_9_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FIELD_10" value="1" {{if $data.ENABLE_DVD_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_10}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_FIELD_10_NAME" class="ENABLE_DVD_FIELD_10_on" value="{{$data.DVD_FIELD_10_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FILE_FIELD_1" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="DVD_FILE_FIELD_1_NAME" class="ENABLE_DVD_FILE_FIELD_1_on" value="{{$data.DVD_FILE_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FILE_FIELD_2" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="DVD_FILE_FIELD_2_NAME" class="ENABLE_DVD_FILE_FIELD_2_on" value="{{$data.DVD_FILE_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FILE_FIELD_3" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="DVD_FILE_FIELD_3_NAME" class="ENABLE_DVD_FILE_FIELD_3_on" value="{{$data.DVD_FILE_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FILE_FIELD_4" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="DVD_FILE_FIELD_4_NAME" class="ENABLE_DVD_FILE_FIELD_4_on" value="{{$data.DVD_FILE_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_FILE_FIELD_5" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><input type="text" name="DVD_FILE_FIELD_5_NAME" class="ENABLE_DVD_FILE_FIELD_5_on" value="{{$data.DVD_FILE_FIELD_5_NAME}}"/></td>
							</tr>
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_dvd_group}}</td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_GROUP_FIELD_1" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_GROUP_FIELD_1_NAME" class="ENABLE_DVD_GROUP_FIELD_1_on" value="{{$data.DVD_GROUP_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_GROUP_FIELD_2" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_GROUP_FIELD_2_NAME" class="ENABLE_DVD_GROUP_FIELD_2_on" value="{{$data.DVD_GROUP_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_GROUP_FIELD_3" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_GROUP_FIELD_3_NAME" class="ENABLE_DVD_GROUP_FIELD_3_on" value="{{$data.DVD_GROUP_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_GROUP_FIELD_4" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_GROUP_FIELD_4_NAME" class="ENABLE_DVD_GROUP_FIELD_4_on" value="{{$data.DVD_GROUP_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_DVD_GROUP_FIELD_5" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="DVD_GROUP_FIELD_5_NAME" class="ENABLE_DVD_GROUP_FIELD_5_on" value="{{$data.DVD_GROUP_FIELD_5_NAME}}"/></td>
							</tr>
						{{/if}}
						{{if $config.installation_type>=2}}
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_user}}</td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_1" value="1" {{if $data.ENABLE_USER_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_1_NAME" class="ENABLE_USER_FIELD_1_on" value="{{$data.USER_FIELD_1_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_2" value="1" {{if $data.ENABLE_USER_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_2_NAME" class="ENABLE_USER_FIELD_2_on" value="{{$data.USER_FIELD_2_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_3" value="1" {{if $data.ENABLE_USER_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_3_NAME" class="ENABLE_USER_FIELD_3_on" value="{{$data.USER_FIELD_3_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_4" value="1" {{if $data.ENABLE_USER_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_4_NAME" class="ENABLE_USER_FIELD_4_on" value="{{$data.USER_FIELD_4_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_5" value="1" {{if $data.ENABLE_USER_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_5_NAME" class="ENABLE_USER_FIELD_5_on" value="{{$data.USER_FIELD_5_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_6" value="1" {{if $data.ENABLE_USER_FIELD_6==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_6}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_6_NAME" class="ENABLE_USER_FIELD_6_on" value="{{$data.USER_FIELD_6_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_7" value="1" {{if $data.ENABLE_USER_FIELD_7==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_7}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_7_NAME" class="ENABLE_USER_FIELD_7_on" value="{{$data.USER_FIELD_7_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_8" value="1" {{if $data.ENABLE_USER_FIELD_8==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_8}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_8_NAME" class="ENABLE_USER_FIELD_8_on" value="{{$data.USER_FIELD_8_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_9" value="1" {{if $data.ENABLE_USER_FIELD_9==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_9}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_9_NAME" class="ENABLE_USER_FIELD_9_on" value="{{$data.USER_FIELD_9_NAME}}"/></td>
							</tr>
							<tr class="eg_data">
								<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_USER_FIELD_10" value="1" {{if $data.ENABLE_USER_FIELD_10==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_10}}</label></span></td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><input type="text" name="USER_FIELD_10_NAME" class="ENABLE_USER_FIELD_10_on" value="{{$data.USER_FIELD_10_NAME}}"/></td>
							</tr>
						{{/if}}
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_referer}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FIELD_1" value="1" {{if $data.ENABLE_REFERER_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="REFERER_FIELD_1_NAME" class="ENABLE_REFERER_FIELD_1_on" value="{{$data.REFERER_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FIELD_2" value="1" {{if $data.ENABLE_REFERER_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="REFERER_FIELD_2_NAME" class="ENABLE_REFERER_FIELD_2_on" value="{{$data.REFERER_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FIELD_3" value="1" {{if $data.ENABLE_REFERER_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="REFERER_FIELD_3_NAME" class="ENABLE_REFERER_FIELD_3_on" value="{{$data.REFERER_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FILE_FIELD_1" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="REFERER_FILE_FIELD_1_NAME" class="ENABLE_REFERER_FILE_FIELD_1_on" value="{{$data.REFERER_FILE_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FILE_FIELD_2" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="REFERER_FILE_FIELD_2_NAME" class="ENABLE_REFERER_FILE_FIELD_2_on" value="{{$data.REFERER_FILE_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_REFERER_FILE_FIELD_3" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_file_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><input type="text" name="REFERER_FILE_FIELD_3_NAME" class="ENABLE_REFERER_FILE_FIELD_3_on" value="{{$data.REFERER_FILE_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="3">{{$lang.settings.customization_divider_feedback}}</td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_FEEDBACK_FIELD_1" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_1==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_1}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="FEEDBACK_FIELD_1_NAME" class="ENABLE_FEEDBACK_FIELD_1_on" value="{{$data.FEEDBACK_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_FEEDBACK_FIELD_2" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_2==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_2}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="FEEDBACK_FIELD_2_NAME" class="ENABLE_FEEDBACK_FIELD_2_on" value="{{$data.FEEDBACK_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_FEEDBACK_FIELD_3" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_3==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_3}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="FEEDBACK_FIELD_3_NAME" class="ENABLE_FEEDBACK_FIELD_3_on" value="{{$data.FEEDBACK_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_FEEDBACK_FIELD_4" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_4==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_4}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="FEEDBACK_FIELD_4_NAME" class="ENABLE_FEEDBACK_FIELD_4_on" value="{{$data.FEEDBACK_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="ENABLE_FEEDBACK_FIELD_5" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_5==1}}checked{{/if}}/><label>{{$lang.settings.custom_field_5}}</label></span></td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><input type="text" name="FEEDBACK_FIELD_5_NAME" class="ENABLE_FEEDBACK_FIELD_5_on" value="{{$data.FEEDBACK_FIELD_5_NAME}}"/></td>
						</tr>
						{{foreach name="data" item="item" from=$list_posts_types}}
							<tr class="eg_group_header">
								<td colspan="3">{{$lang.settings.customization_divider_post_type|replace:"%1%":$item.title}}</td>
							</tr>
							{{section name="fields" start="1" loop=11}}
								{{assign var="lang_key" value="custom_field_`$smarty.section.fields.index`"}}
								{{assign var="data_key_enable" value="ENABLE_POST_`$item.post_type_id`_FIELD_`$smarty.section.fields.index`"}}
								{{assign var="data_key_name" value="POST_`$item.post_type_id`_FIELD_`$smarty.section.fields.index`_NAME"}}
								<tr class="eg_data">
									<td><span class="de_lv_pair de_vis_sw_checkbox"><input  type="checkbox" name="{{$data_key_enable}}" value="1" {{if $data[$data_key_enable]==1}}checked{{/if}}/><label>{{$lang.settings.$lang_key}}</label></span></td>
									<td>{{$lang.settings.custom_type_text}}</td>
									<td><input type="text" name="{{$data_key_name}}" class="{{$data_key_enable}}_on" value="{{$data[$data_key_name]}}"/></td>
								</tr>
							{{/section}}
							{{section name="fields" start="1" loop=11}}
								{{assign var="lang_key" value="custom_file_field_`$smarty.section.fields.index`"}}
								{{assign var="data_key_enable" value="ENABLE_POST_`$item.post_type_id`_FILE_FIELD_`$smarty.section.fields.index`"}}
								{{assign var="data_key_name" value="POST_`$item.post_type_id`_FILE_FIELD_`$smarty.section.fields.index`_NAME"}}
								<tr class="eg_data">
									<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="{{$data_key_enable}}" value="1" {{if $data[$data_key_enable]==1}}checked{{/if}}/><label>{{$lang.settings.$lang_key}}</label></span></td>
									<td>{{$lang.settings.custom_type_file}}</td>
									<td><input type="text" name="{{$data_key_name}}" class="{{$data_key_enable}}_on" value="{{$data[$data_key_name]}}"/></td>
								</tr>
							{{/section}}
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_customization_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>

{{else}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="settings_personal">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.personal_header}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.personal_divider_user_settings}}</h2></td>
			</tr>
			{{if $smarty.session.userdata.is_superadmin==1}}
			   <tr>
				   <td class="de_label de_required">{{$lang.settings.personal_field_username}}</td>
				   <td class="de_control"><input type="text" name="login" maxlength="100" value="{{$personal_data.login}}"/></td>
			   </tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_password}}</td>
				<td class="de_control de_passw">
					<input type="text" data-name="pass" value="{{$lang.common.password_hidden}}" autocomplete="new-password"/>
					<span class="de_hint">{{$lang.settings.personal_field_password_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_password_confirm}}</td>
				<td class="de_control">
					<input type="password" name="pass_confirm" autocomplete="new-password"/>
					<span class="de_hint">{{$lang.settings.personal_field_password_confirm_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_short_date_format}}</td>
				<td class="de_control">
					<input type="text" name="short_date_format" maxlength="30" value="{{$personal_data.short_date_format}}"/>
					<span class="de_hint">{{$lang.settings.personal_field_short_date_format_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_full_date_format}}</td>
				<td class="de_control">
					<input type="text" name="full_date_format" maxlength="30" value="{{$personal_data.full_date_format}}"/>
					<span class="de_hint">{{$lang.settings.personal_field_full_date_format_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_email}}</td>
				<td class="de_control">
					<input type="text" name="email" maxlength="100" value="{{$personal_data.email}}"/>
					<span class="de_hint">{{$lang.settings.personal_field_email_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_language}}</td>
				<td class="de_control">
					<select name="lang">
						{{foreach item="item" from=$list_langs|smarty:nodefaults}}
							<option value="{{$item}}" {{if $item==$personal_data.lang}}selected{{/if}}>{{$item|mb_ucfirst}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_skin}}</td>
				<td class="de_control">
					<select name="skin">
						{{foreach item="item" from=$list_skins|smarty:nodefaults}}
							<option value="{{$item}}" {{if $item==$personal_data.skin}}selected{{/if}}>{{$item|mb_ucfirst}}</option>
						{{/foreach}}
					</select>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="skin_enable_night_mode" value="1" {{if $smarty.session.save.options.skin_enable_night_mode==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_skin_enable_night_mode}}</label></span>
					<span class="skin_enable_night_mode_on">
						<input type="text" name="skin_night_mode_from" size="5" value="{{$smarty.session.save.options.skin_night_mode_from|default:"00:00"}}">
						-
						<input type="text" name="skin_night_mode_to" size="5" value="{{$smarty.session.save.options.skin_night_mode_to|default:"00:00"}}">
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_main_menu_mode}}</td>
				<td class="de_control">
					<select name="main_menu_mode">
						<option value="default" {{if $smarty.session.save.options.main_menu_mode=='default'}}selected{{/if}}>{{$lang.settings.personal_field_main_menu_mode_default}}</option>
						<option value="only_icons" {{if $smarty.session.save.options.main_menu_mode=='only_icons'}}selected{{/if}}>{{$lang.settings.personal_field_main_menu_mode_only_icons}}</option>
						<option value="only_text" {{if $smarty.session.save.options.main_menu_mode=='only_text'}}selected{{/if}}>{{$lang.settings.personal_field_main_menu_mode_only_text}}</option>
					</select>
					<span class="de_lv_pair"><input type="checkbox" name="main_menu_memory" value="1" {{if $smarty.session.save.options.main_menu_memory==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_main_menu_memory}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_side_menu_mode}}</td>
				<td class="de_control">
					<select name="side_menu_mode">
						<option value="default" {{if $smarty.session.save.options.side_menu_mode=='default'}}selected{{/if}}>{{$lang.settings.personal_field_side_menu_mode_default}}</option>
						<option value="only_text" {{if $smarty.session.save.options.side_menu_mode=='only_text'}}selected{{/if}}>{{$lang.settings.personal_field_side_menu_mode_only_text}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_scrolling_mode}}</td>
				<td class="de_control">
					<select name="scrolling_mode">
						<option value="new" {{if $smarty.session.save.options.scrolling_mode=='new'}}selected{{/if}}>{{$lang.settings.personal_field_scrolling_mode_new}}</option>
						<option value="old" {{if $smarty.session.save.options.scrolling_mode=='old'}}selected{{/if}}>{{$lang.settings.personal_field_scrolling_mode_old}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_editor_mode}}</td>
				<td class="de_control">
					<select name="editor_mode">
						<option value="default" {{if $smarty.session.save.options.editor_mode=='default'}}selected{{/if}}>{{$lang.settings.personal_field_editor_mode_default}}</option>
						<option value="popups" {{if $smarty.session.save.options.editor_mode=='popups'}}selected{{/if}}>{{$lang.settings.personal_field_editor_mode_popups}}</option>
						<option value="tabs" {{if $smarty.session.save.options.editor_mode=='tabs'}}selected{{/if}}>{{$lang.settings.personal_field_editor_mode_tabs}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_popup_close}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="popup_close_by_click" value="1" {{if $smarty.session.save.options.popup_close_by_click==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_popup_close_by_click}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_urls_same_window}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="urls_same_window" value="1" {{if $smarty.session.save.options.urls_same_window==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_urls_same_window_yes}}</label></span>
					<span class="de_hint">{{$lang.settings.personal_field_urls_same_window_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_custom_css}}</td>
				<td class="de_control">
					<div class="code_editor">
						<textarea name="custom_css" cols="40" rows="5">{{$personal_data.custom_css}}</textarea>
					</div>
					<span class="de_hint">{{$lang.settings.personal_field_custom_css_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.personal_field_maximum_thumb_size}}</td>
				<td class="de_control">
					<input type="text" name="maximum_thumb_size" maxlength="10" size="10" value="{{$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}"/>
					<span class="de_hint">{{$lang.settings.personal_field_maximum_thumb_size_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_default_save_button}}</td>
				<td class="de_control">
					<select name="default_save_button">
						<option value="0" {{if $smarty.session.save.options.default_save_button=='0'}}selected{{/if}}>{{$lang.settings.personal_field_default_save_button0}}</option>
						<option value="1" {{if $smarty.session.save.options.default_save_button=='1'}}selected{{/if}}>{{$lang.settings.personal_field_default_save_button1}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.personal_field_default_save_button_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_enable_wysiwyg}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_videos" value="1" {{if $personal_data.is_wysiwyg_enabled_videos==1}}checked{{/if}} {{if $tinymce_enabled!='1'}}disabled{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_videos}}</label></span>
							</td>
						</tr>
						{{if in_array('albums|view',$smarty.session.permissions)}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_albums" value="1" {{if $personal_data.is_wysiwyg_enabled_albums==1}}checked{{/if}} {{if $tinymce_enabled!='1'}}disabled{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_albums}}</label></span>
								</td>
							</tr>
						{{/if}}
						{{if in_array('posts|view',$smarty.session.permissions)}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_posts" value="1" {{if $personal_data.is_wysiwyg_enabled_posts==1}}checked{{/if}} {{if $tinymce_enabled!='1'}}disabled{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_posts}}</label></span>
								</td>
							</tr>
						{{/if}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_other" value="1" {{if $personal_data.is_wysiwyg_enabled_other==1}}checked{{/if}} {{if $tinymce_enabled!='1'}}disabled{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_other}}</label></span>
							</td>
						</tr>
					</table>
					<span class="de_hint">{{$lang.settings.personal_field_enable_wysiwyg_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_ip_protection}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_ip_protection_disabled" value="1" {{if $personal_data.is_ip_protection_disabled==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_ip_protection_disabled}}</label></span>
					<span class="de_hint">{{$lang.settings.personal_field_ip_protection_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_syntax_highlight}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_syntax_highlight_disabled" value="1" {{if $smarty.session.save.options.is_syntax_highlight_disabled==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_syntax_highlight_disabled}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_content_scheduler_days}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="content_scheduler_days" maxlength="3" size="5" value="{{$personal_data.content_scheduler_days}}"/>
						<select name="content_scheduler_days_option">
							<option value="0" {{if $personal_data.content_scheduler_days_option=='0'}}selected{{/if}}>{{$lang.settings.personal_field_content_scheduler_days_last}}</option>
							<option value="1" {{if $personal_data.content_scheduler_days_option=='1'}}selected{{/if}}>{{$lang.settings.personal_field_content_scheduler_days_next}}</option>
						</select>
						{{$lang.settings.personal_field_content_scheduler_days_period}}
					</span>
					<span class="de_hint">{{$lang.settings.personal_field_content_scheduler_days_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_expert_mode}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_expert_mode" value="1" {{if $personal_data.is_expert_mode==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_expert_mode_hide_hints}}</label></span>
					<span class="de_lv_pair"><input type="checkbox" name="is_hide_forum_hints" value="1" {{if $personal_data.is_hide_forum_hints==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_expert_mode_hide_forum}}</label></span>
					<span class="de_hint">{{$lang.settings.personal_field_expert_mode_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_disable_toolbar}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="disable_toolbar" value="1" {{if $smarty.session.save.options.disable_toolbar==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_disable_toolbar_disabled}}</label></span>
					<span class="de_hint">{{$lang.settings.personal_field_disable_toolbar_hint}}</span>
				</td>
			</tr>
			{{if in_array('videos|view',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.personal_divider_videos_display_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.personal_field_video_edit_display_mode}}</td>
					<td class="de_control">
						<select name="video_edit_display_mode">
							<option value="full" {{if $smarty.session.save.options.video_edit_display_mode=='full'}}selected{{/if}}>{{$lang.settings.personal_field_video_edit_display_mode_full}}</option>
							<option value="descwriter" {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}selected{{/if}}>{{$lang.settings.personal_field_video_edit_display_mode_descwriter}}</option>
						</select>
						{{if in_array('localization|view',$smarty.session.permissions)}}
							<span class="de_lv_pair"><input type="checkbox" name="video_edit_show_translations" value="1" {{if $smarty.session.save.options.video_edit_show_translations==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_video_edit_display_mode_localization}}</label></span>
						{{/if}}
						<span class="de_lv_pair"><input type="checkbox" name="video_edit_show_player" value="1" {{if $smarty.session.save.options.video_edit_show_player==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_video_edit_display_mode_player}}</label></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.personal_field_screenshots_on_video_edit}}</td>
					<td class="de_control">
						<select name="screenshots_on_video_edit">
							<option value="0">{{$lang.settings.personal_field_screenshots_on_video_edit_no}}</option>
							{{foreach item="item" from=$list_formats_screenshots_overview|smarty:nodefaults}}
								<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.options.screenshots_on_video_edit==$item.format_screenshot_id}}selected{{/if}}>{{$lang.settings.personal_field_screenshots_on_video_edit_overview|replace:"%1%":$item.title}}</option>
							{{/foreach}}
							{{foreach item="item" from=$list_formats_screenshots_posters|smarty:nodefaults}}
								<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.options.screenshots_on_video_edit==$item.format_screenshot_id}}selected{{/if}}>{{$lang.settings.personal_field_screenshots_on_video_edit_posters|replace:"%1%":$item.title}}</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.settings.personal_field_screenshots_on_video_edit_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|view',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.personal_divider_albums_display_settings}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.personal_field_album_edit_display_mode}}</td>
					<td class="de_control">
						<select name="album_edit_display_mode">
							<option value="full" {{if $smarty.session.save.options.album_edit_display_mode=='full'}}selected{{/if}}>{{$lang.settings.personal_field_album_edit_display_mode_full}}</option>
							<option value="descwriter" {{if $smarty.session.save.options.album_edit_display_mode=='descwriter'}}selected{{/if}}>{{$lang.settings.personal_field_album_edit_display_mode_descwriter}}</option>
						</select>
						{{if in_array('localization|view',$smarty.session.permissions)}}
							<span class="de_lv_pair"><input type="checkbox" name="album_edit_show_translations" value="1" {{if $smarty.session.save.options.album_edit_show_translations==1}}checked{{/if}}/><label>{{$lang.settings.personal_field_album_edit_display_mode_localization}}</label></span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label de_required {{foreach item="item" from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}}">{{$lang.settings.personal_field_images_on_album_edit}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="images_on_album_edit">
							<option value="no" {{if $smarty.session.save.options.images_on_album_edit=='no'}}selected{{/if}}>{{$lang.settings.personal_field_images_on_album_edit_no}}</option>
							{{foreach item="item" from=$list_formats_albums|smarty:nodefaults}}
								<option value="{{$item.size}}" {{if $smarty.session.save.options.images_on_album_edit==$item.size}}selected{{/if}}>{{$lang.settings.personal_field_images_on_album_edit_format|replace:"%1%":$item.title}}</option>
							{{/foreach}}
						</select>
						<span class="{{foreach item="item" from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}}">
							<input type="text" name="images_on_album_edit_count" class="{{foreach item="item" from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}}" size="5" maxlength="2" value="{{$smarty.session.save.options.images_on_album_edit_count}}"/>
						</span>
						<span class="de_hint">{{$lang.settings.personal_field_images_on_album_edit_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.personal_divider_admin_panel_notifications}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_mute_notifications}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="has_notification_to_mute" value="false"}}
						{{assign var="has_plugin_notifications" value="false"}}
						{{foreach from=$notification_types|smarty:nodefaults item="item"}}
							{{if ($smarty.session.userdata.is_superadmin==0 || $item.severity!='error') && $item.severity!='critical'}}
								{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
									{{if $item.plugin_id}}
										{{assign var="has_plugin_notifications" value="true"}}
									{{else}}
										<tr>
											<td>
												<span class="de_lv_pair"><input type="checkbox" name="mute_notifications[]" value="{{$item.notification_id}}" {{if in_array($item.notification_id, $smarty.session.save.options.mute_notifications)}}checked{{/if}}/><label>{{$item.title|replace:"%1%":"N"}}</label></span>
											</td>
										</tr>
										{{assign var="has_notification_to_mute" value="true"}}
									{{/if}}
								{{/if}}
							{{/if}}
						{{/foreach}}
						{{if in_array('plugins|view',$smarty.session.permissions) && $has_plugin_notifications=='true'}}
							<tr>
								<td>
									{{assign var="plugins_mute_enabled" value="false"}}
									{{foreach from=$notification_types|smarty:nodefaults item="item"}}
										{{if ($smarty.session.userdata.is_superadmin==0 || $item.severity!='error') && $item.severity!='critical'}}
											{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
												{{if $item.plugin_id}}
													{{if in_array($item.notification_id, $smarty.session.save.options.mute_notifications)}}
														{{assign var="plugins_mute_enabled" value="true"}}
													{{/if}}
												{{/if}}
											{{/if}}
										{{/if}}
									{{/foreach}}
									<span class="de_lv_pair de_vis_sw_checkbox"><input name="mute_notifications_plugins" value="1" type="checkbox" {{if $plugins_mute_enabled=='true'}}checked{{/if}}/><label>{{$lang.settings.personal_field_mute_notifications_from_plugins}}</label></span>
								</td>
							</tr>
							{{foreach from=$notification_types|smarty:nodefaults item="item"}}
								{{if ($smarty.session.userdata.is_superadmin==0 || $item.severity!='error') && $item.severity!='critical'}}
									{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
										{{if $item.plugin_id}}
											<tr class="mute_notifications_plugins_on">
												<td class="de_dependent">
													<span class="de_lv_pair"><input type="checkbox" name="mute_notifications[]" value="{{$item.notification_id}}" {{if in_array($item.notification_id, $smarty.session.save.options.mute_notifications)}}checked{{/if}}/><label>{{$item.title|replace:"%1%":"N"}}</label></span>
												</td>
											</tr>
										{{/if}}
									{{/if}}
								{{/if}}
							{{/foreach}}
							{{assign var="has_notification_to_mute" value="true"}}
						{{/if}}
						{{if $has_notification_to_mute=='false'}}
							<tr><td>{{$lang.common.undefined}}</td></tr>
						{{/if}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2"></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_email_notifications}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="has_notification_to_email" value="false"}}
						{{assign var="has_plugin_notifications" value="false"}}
						{{if in_array('feedbacks|view',$smarty.session.permissions)}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="email_notifications_feedbacks" value="1" {{if in_array('feedback_submitted', $smarty.session.save.options.email_notifications.notifications)}}checked{{/if}}/><label>{{$lang.settings.personal_field_email_notifications_feedbacks}}</label></span>
								</td>
							</tr>
							<tr class="email_notifications_feedbacks_on">
								<td class="de_dependent">
									{{$lang.settings.personal_field_email_notifications_feedbacks_whitelist}}:
									<textarea name="email_notifications_feedbacks_whitelist" rows="3" cols="20">{{$smarty.session.save.options.email_notifications.feedback_submitted_whitelist}}</textarea>
									<span class="de_hint">{{$lang.settings.personal_field_email_notifications_feedbacks_whitelist_hint}}</span>
								</td>
							</tr>
							{{assign var="has_notification_to_email" value="true"}}
						{{/if}}
						{{foreach from=$notification_types|smarty:nodefaults item="item"}}
							{{if $item.is_emailable}}
								{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
									{{if $item.plugin_id}}
										{{assign var="has_plugin_notifications" value="true"}}
									{{else}}
										<tr>
											<td>
												<span class="de_lv_pair"><input type="checkbox" name="email_notifications[]" value="{{$item.notification_id}}" {{if in_array($item.notification_id, $smarty.session.save.options.email_notifications.notifications)}}checked{{/if}}/><label>{{$item.title|replace:"%1%":"N"}}</label></span>
											</td>
										</tr>
										{{assign var="has_notification_to_email" value="true"}}
									{{/if}}
								{{/if}}
							{{/if}}
						{{/foreach}}
						{{if in_array('plugins|view',$smarty.session.permissions) && $has_plugin_notifications=='true'}}
							<tr>
								<td>
									{{assign var="plugins_email_enabled" value="false"}}
									{{foreach from=$notification_types|smarty:nodefaults item="item"}}
										{{if $item.is_emailable}}
											{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
												{{if $item.plugin_id}}
													{{if in_array($item.notification_id, $smarty.session.save.options.email_notifications.notifications)}}
														{{assign var="plugins_email_enabled" value="true"}}
													{{/if}}
												{{/if}}
											{{/if}}
										{{/if}}
									{{/foreach}}
									<span class="de_lv_pair de_vis_sw_checkbox"><input name="email_notifications_plugins" value="1" type="checkbox" {{if $plugins_email_enabled=='true'}}checked{{/if}}/><label>{{$lang.settings.personal_field_email_notifications_from_plugins}}</label></span>
								</td>
							</tr>
							{{foreach from=$notification_types|smarty:nodefaults item="item"}}
								{{if $item.is_emailable}}
									{{if !$item.permission || in_array($item.permission, $smarty.session.permissions)}}
										{{if $item.plugin_id}}
											<tr class="email_notifications_plugins_on">
												<td class="de_dependent">
													<span class="de_lv_pair"><input type="checkbox" name="email_notifications[]" value="{{$item.notification_id}}" {{if in_array($item.notification_id, $smarty.session.save.options.email_notifications.notifications)}}checked{{/if}}/><label>{{$item.title|replace:"%1%":"N"}}</label></span>
												</td>
											</tr>
										{{/if}}
									{{/if}}
								{{/if}}
							{{/foreach}}
							{{assign var="has_notification_to_email" value="true"}}
						{{/if}}
						{{if $has_notification_to_email=='false'}}
							<tr><td>{{$lang.common.undefined}}</td></tr>
						{{/if}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_personal_setting_complete"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
		<span class="de_separated_group">
			<input type="submit" name="reset_admin_panel_settings" class="destructive" value="{{$lang.settings.personal_btn_reset_admin_panel_settings}}" data-confirm="{{$lang.settings.personal_btn_reset_admin_panel_settings_confirm}}"/>
		</span>
	</div>
</form>
{{/if}}