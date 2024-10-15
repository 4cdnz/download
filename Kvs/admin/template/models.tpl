{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('models|edit_all',$smarty.session.permissions) || (in_array('models|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="model_edit">
	<div>
		{{if $options.MODELS_SCREENSHOT_OPTION==0}}
			<input type="hidden" name="screenshot2" value="{{$smarty.post.screenshot2}}"/>
			<input type="hidden" name="screenshot2_hash"/>
		{{/if}}
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
		<input type="hidden" name="custom4" value="{{$smarty.post.custom4}}"/>
		<input type="hidden" name="custom5" value="{{$smarty.post.custom5}}"/>
		<input type="hidden" name="custom6" value="{{$smarty.post.custom6}}"/>
		<input type="hidden" name="custom7" value="{{$smarty.post.custom7}}"/>
		<input type="hidden" name="custom8" value="{{$smarty.post.custom8}}"/>
		<input type="hidden" name="custom9" value="{{$smarty.post.custom9}}"/>
		<input type="hidden" name="custom10" value="{{$smarty.post.custom10}}"/>
		<input type="hidden" name="custom_file1" value="{{$smarty.post.custom_file1}}"/>
		<input type="hidden" name="custom_file1_hash"/>
		<input type="hidden" name="custom_file2" value="{{$smarty.post.custom_file2}}"/>
		<input type="hidden" name="custom_file2_hash"/>
		<input type="hidden" name="custom_file3" value="{{$smarty.post.custom_file3}}"/>
		<input type="hidden" name="custom_file3_hash"/>
		<input type="hidden" name="custom_file4" value="{{$smarty.post.custom_file4}}"/>
		<input type="hidden" name="custom_file4_hash"/>
		<input type="hidden" name="custom_file5" value="{{$smarty.post.custom_file5}}"/>
		<input type="hidden" name="custom_file5_hash"/>
	</div>
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.categorization.submenu_option_models_list}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.categorization.model_add}}
				{{else}}
					{{if $smarty.post.model_group_id>0}}
						{{if in_array('models_groups|view',$smarty.session.permissions)}}
							<a href="models_groups.php?action=change&amp;item_id={{$smarty.post.model_group_id}}">{{$smarty.post.model_group}}</a>
						{{else}}
							{{$smarty.post.model_group}}
						{{/if}}
						/
					{{/if}}
					{{$lang.categorization.model_edit|replace:"%1%":$smarty.post.title}}
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
				<td class="de_simple_text" colspan="3">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/52-categorization-best-practices/">Categorization best practices</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.categorization.model_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.categorization.model_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
				{{if is_array($sidebar_fields)}}
					{{assign var="sidebar_rowspan" value="9"}}
					{{if $smarty.post.website_link!=''}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
					{{/if}}
					{{if $options.MODELS_SCREENSHOT_OPTION>0}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
					{{/if}}

					{{assign var="image_field" value=""}}
					{{if $smarty.post.screenshot1!=''}}
						{{assign var="image_field" value="screenshot1"}}
					{{/if}}
					{{if $options.MODELS_SCREENSHOT_OPTION>0}}
						{{assign var="image_size1" value="x"|explode:$options.MODELS_SCREENSHOT_1_SIZE}}
						{{assign var="image_size2" value="x"|explode:$options.MODELS_SCREENSHOT_2_SIZE}}
						{{if ($image_size1[0]>$image_size2[0] || $smarty.post.screenshot1=='') && $smarty.post.screenshot2!=''}}
							{{assign var="image_field" value="screenshot2"}}
						{{/if}}
					{{/if}}
					{{if $image_field!=''}}
						{{assign var="sidebar_image_url" value="`$config.content_url_models`/`$smarty.post.model_id`/`$smarty.post.$image_field`"}}
					{{/if}}

					{{include file="editor_sidebar_inc.tpl"}}
				{{/if}}
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.categorization.model_field_directory}}</td>
					<td class="de_control">
						<input type="text" name="dir" maxlength="255" value="{{$smarty.post.dir}}"/>
						<span class="de_hint">{{$lang.categorization.model_field_directory_hint|replace:"%1%":$lang.categorization.model_field_title}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.website_link!=''}}
				<tr data-field-name="website_link">
					<td class="de_label">{{$lang.categorization.model_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_alias}}</td>
				<td class="de_control">
					<input type="text" name="alias" maxlength="500" value="{{$smarty.post.alias}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_description}}</td>
				<td class="de_control">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}}/><label>{{$lang.categorization.model_field_status_active}}</label></span>
					<span class="de_hint">{{$lang.categorization.model_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_group}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=model_groups</span>
							{{if in_array('models_groups|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.model_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="model_group" maxlength="255" value="{{$smarty.post.model_group}}"/>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_screenshot1}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.categorization.model_field_screenshot1}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.screenshot1!='' && in_array(end(explode(".",$smarty.post.screenshot1)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.screenshot1}}</span>
							{{/if}}
						</div>
						<input type="text" name="screenshot1" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.screenshot1!=''}}value="{{$smarty.post.screenshot1}}"{{/if}}/>
						<input type="hidden" name="screenshot1_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.categorization.model_field_screenshot1_hint}} (<a href="options.php?page=general_settings">{{$options.MODELS_SCREENSHOT_1_SIZE}}</a>)</span>
				</td>
			</tr>
			{{if $options.MODELS_SCREENSHOT_OPTION>0}}
				<tr>
					<td class="de_label">{{$lang.categorization.model_field_screenshot2}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.categorization.model_field_screenshot2}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.screenshot2!='' && in_array(end(explode(".",$smarty.post.screenshot2)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.screenshot2}}</span>
								{{/if}}
							</div>
							<input type="text" name="screenshot2" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.screenshot2!=''}}value="{{$smarty.post.screenshot2}}"{{/if}}/>
							<input type="hidden" name="screenshot2_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.categorization.model_field_screenshot2_hint}} (<a href="options.php?page=general_settings">{{$options.MODELS_SCREENSHOT_2_SIZE}}</a>){{if $options.MODELS_SCREENSHOT_OPTION==1}}; {{$lang.categorization.model_field_screenshot2_hint2|replace:"%1%":$lang.categorization.model_field_screenshot1}}{{/if}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.categorization.model_field_rating}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="avg_rating" size="10" value="{{$smarty.post.rating|replace:",":"."|floatval|round:1}}"/>
					</span>
					<span>
						{{$lang.categorization.model_field_rating_votes}}:
						<input type="text" name="rating_amount" size="5" value="{{$smarty.post.rating_amount}}"/>
					</span>
					<span class="de_hint">{{$lang.categorization.model_field_rating_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_access_level}}</td>
				<td class="de_control">
					<select name="access_level_id">
						<option value="0" {{if $smarty.post.access_level_id==0}}selected{{/if}}>{{$lang.categorization.model_field_access_level_any}}</option>
						<option value="1" {{if $smarty.post.access_level_id==1}}selected{{/if}}>{{$lang.categorization.model_field_access_level_member}}</option>
						<option value="2" {{if $smarty.post.access_level_id==2}}selected{{/if}}>{{$lang.categorization.model_field_access_level_premium}}</option>
					</select>
					<span class="de_hint">{{$lang.categorization.model_field_access_level_hint}}</span>
				</td>
			</tr>
			{{if $smarty.post.gallery_url!=''}}
				<tr>
					<td class="de_label">{{$lang.categorization.model_field_gallery_url}}</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.gallery_url}}">{{$smarty.post.gallery_url}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.categorization.model_divider_origin}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_country}}</td>
				<td class="de_control" colspan="2">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=countries</span>
						</div>
						<input type="text" name="country" maxlength="255" value="{{$smarty.post.country}}"/>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_city}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="city" maxlength="100" size="40" value="{{$smarty.post.city}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_state}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="state" maxlength="100" size="40" value="{{$smarty.post.state}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.categorization.model_divider_parameters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_gender}}</td>
				<td class="de_control" colspan="2">
					<select name="gender_id">
						<option value="0">{{$lang.common.select_default_option}}</option>
						{{foreach from=$list_gender_values key="key" item="item"}}
							{{if $key>0}}
								<option value="{{$key}}" {{if $smarty.post.gender_id==$key}}selected{{/if}}>{{$item}}</option>
							{{/if}}
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_height}}</td>
				<td class="de_control" colspan="2"><input type="text" name="height" maxlength="100" size="40" value="{{$smarty.post.height}}"/></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_weight}}</td>
				<td class="de_control" colspan="2"><input type="text" name="weight" maxlength="100" size="40" value="{{$smarty.post.weight}}"/></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_hair}}</td>
				<td class="de_control" colspan="2">
					<select name="hair_id">
						<option value="0">{{$lang.common.select_default_option}}</option>
						{{foreach from=$list_hair_values key="key" item="item"}}
							{{if $key>0}}
								<option value="{{$key}}" {{if $smarty.post.hair_id==$key}}selected{{/if}}>{{$item}}</option>
							{{/if}}
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_eye_color}}</td>
				<td class="de_control" colspan="2">
					<select name="eye_color_id">
						<option value="0">{{$lang.common.select_default_option}}</option>
						{{foreach from=$list_eye_color_values key="key" item="item"}}
							{{if $key>0}}
								<option value="{{$key}}" {{if $smarty.post.eye_color_id==$key}}selected{{/if}}>{{$item}}</option>
							{{/if}}
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_measurements}}</td>
				<td class="de_control" colspan="2">
					<input type="text" name="measurements" maxlength="100" size="40" value="{{$smarty.post.measurements}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_age}}</td>
				<td class="de_control" colspan="2">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_birthdate" type="radio" name="age_option" value="1" {{if $smarty.post.birth_date!='0000-00-00' || $smarty.post.age==0}}checked{{/if}}/><label>{{$lang.categorization.model_field_age_birth_date}}:</label></span>
								<span class="calendar">
									<span class="js_params">
										<span class="js_param">type=date</span>
										{{if $can_edit_all!=1}}
											<span class="js_param">forbid_edit=true</span>
										{{/if}}
									</span>
									<input class="option_birthdate" type="text" name="birth_date" value="{{$smarty.post.birth_date}}" placeholder="{{$lang.common.select_default_option}}">
								</span>
								<span>
									{{$lang.categorization.model_field_age_death_date}}:
									<span class="calendar">
										<span class="js_params">
											<span class="js_param">type=date</span>
											{{if $can_edit_all!=1}}
												<span class="js_param">forbid_edit=true</span>
											{{/if}}
										</span>
										<input class="option_birthdate" type="text" name="death_date" value="{{$smarty.post.death_date}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
								<span class="de_hint">{{$lang.categorization.model_field_age_birth_date_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="option_fixed_age" type="radio" name="age_option" value="2" {{if $smarty.post.birth_date=='0000-00-00' && $smarty.post.age>0}}checked{{/if}}/><label>{{$lang.categorization.model_field_age_fixed}}:</label></span>
								<input type="text" name="age" class="option_fixed_age" size="10" value="{{if $smarty.post.age>0}}{{$smarty.post.age}}{{/if}}"/>
								<span class="de_hint">{{$lang.categorization.model_field_age_fixed_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.categorization.model_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_tags}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.categorization.model_field_tags}}</span>
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
							<span class="js_param">empty_message={{$lang.categorization.model_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_tag"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.categorization.model_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.model_field_categories}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.categorization.model_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.categorization.model_field_categories_empty}}</span>
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
								<input type="button" class="all" value="{{$lang.categorization.model_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $options.ENABLE_MODEL_FIELD_1==1 || $options.ENABLE_MODEL_FIELD_2==1 || $options.ENABLE_MODEL_FIELD_3==1 || $options.ENABLE_MODEL_FIELD_4==1 || $options.ENABLE_MODEL_FIELD_5==1 || $options.ENABLE_MODEL_FIELD_6==1 || $options.ENABLE_MODEL_FIELD_7==1 || $options.ENABLE_MODEL_FIELD_8==1 || $options.ENABLE_MODEL_FIELD_9==1 || $options.ENABLE_MODEL_FIELD_10==1
				|| $options.ENABLE_MODEL_FILE_FIELD_1==1 || $options.ENABLE_MODEL_FILE_FIELD_2==1 || $options.ENABLE_MODEL_FILE_FIELD_3==1 || $options.ENABLE_MODEL_FILE_FIELD_4==1 || $options.ENABLE_MODEL_FILE_FIELD_5==1}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.categorization.model_divider_customization}}</h2></td>
				</tr>
				{{if $options.ENABLE_MODEL_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom1" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom2" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom3" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_4_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom4" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom4}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_5_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom5" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom5}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_6==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_6_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom6" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom6}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_7==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_7_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom7" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom7}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_8==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_8_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom8" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom8}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_9==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_9_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom9" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom9}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FIELD_10==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FIELD_10_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom10" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom10}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FILE_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FILE_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.MODEL_FILE_FIELD_1_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file1)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file1}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file1}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file1" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}value="{{$smarty.post.custom_file1}}"{{/if}}/>
								<input type="hidden" name="custom_file1_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FILE_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FILE_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.MODEL_FILE_FIELD_2_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file2)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file2}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file2}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file2" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}value="{{$smarty.post.custom_file2}}"{{/if}}/>
								<input type="hidden" name="custom_file2_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FILE_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FILE_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.MODEL_FILE_FIELD_3_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file3)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file3}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file3}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file3" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}value="{{$smarty.post.custom_file3}}"{{/if}}/>
								<input type="hidden" name="custom_file3_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FILE_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FILE_FIELD_4_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.MODEL_FILE_FIELD_4_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file4)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file4}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file4}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file4" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}value="{{$smarty.post.custom_file4}}"{{/if}}/>
								<input type="hidden" name="custom_file4_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_MODEL_FILE_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.MODEL_FILE_FIELD_5_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.MODEL_FILE_FIELD_5_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file5)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file5}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_models}}/{{$smarty.post.model_id}}/{{$smarty.post.custom_file5}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file5" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}value="{{$smarty.post.custom_file5}}"{{/if}}/>
								<input type="hidden" name="custom_file5_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
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
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('models|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('models|edit_all',$smarty.session.permissions)}}
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
						<option value="">{{$lang.categorization.model_field_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.categorization.model_field_status_active}}</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.categorization.model_field_status_disabled}}</option>
					</select>
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
						<input type="text" name="se_model_group" value="{{$smarty.session.save.$page_name.se_model_group}}" placeholder="{{$lang.categorization.model_field_group}}..."/>
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
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.categorization.model_field_tag}}..."/>
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
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.categorization.model_field_category}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_description}}</option>
						<option value="empty/alias" {{if $smarty.session.save.$page_name.se_field=="empty/alias"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_alias}}</option>
						<option value="empty/group" {{if $smarty.session.save.$page_name.se_field=="empty/group"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_group}}</option>
						<option value="empty/screenshot1" {{if $smarty.session.save.$page_name.se_field=="empty/screenshot1"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_screenshot1}}</option>
						<option value="empty/screenshot2" {{if $smarty.session.save.$page_name.se_field=="empty/screenshot2"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_screenshot2}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_rating}}</option>
						<option value="empty/model_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/model_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_visits}}</option>
						<option value="empty/country" {{if $smarty.session.save.$page_name.se_field=="empty/country"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_country}}</option>
						<option value="empty/city" {{if $smarty.session.save.$page_name.se_field=="empty/city"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_city}}</option>
						<option value="empty/state" {{if $smarty.session.save.$page_name.se_field=="empty/state"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_state}}</option>
						<option value="empty/height" {{if $smarty.session.save.$page_name.se_field=="empty/height"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_height}}</option>
						<option value="empty/weight" {{if $smarty.session.save.$page_name.se_field=="empty/weight"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_weight}}</option>
						<option value="empty/hair_id" {{if $smarty.session.save.$page_name.se_field=="empty/hair_id"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_hair}}</option>
						<option value="empty/eye_color_id" {{if $smarty.session.save.$page_name.se_field=="empty/eye_color_id"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_eye_color}}</option>
						<option value="empty/measurements" {{if $smarty.session.save.$page_name.se_field=="empty/measurements"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_measurements}}</option>
						<option value="empty/gallery_url" {{if $smarty.session.save.$page_name.se_field=="empty/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_gallery_url}}</option>
						<option value="empty/age" {{if $smarty.session.save.$page_name.se_field=="empty/age"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_age}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.model_field_categories}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="MODEL_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_MODEL_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="MODEL_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_MODEL_FILE_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_description}}</option>
						<option value="filled/alias" {{if $smarty.session.save.$page_name.se_field=="filled/alias"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_alias}}</option>
						<option value="filled/group" {{if $smarty.session.save.$page_name.se_field=="filled/group"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_group}}</option>
						<option value="filled/screenshot1" {{if $smarty.session.save.$page_name.se_field=="filled/screenshot1"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_screenshot1}}</option>
						<option value="filled/screenshot2" {{if $smarty.session.save.$page_name.se_field=="filled/screenshot2"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_screenshot2}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_rating}}</option>
						<option value="filled/model_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/model_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_visits}}</option>
						<option value="filled/country" {{if $smarty.session.save.$page_name.se_field=="filled/country"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_country}}</option>
						<option value="filled/city" {{if $smarty.session.save.$page_name.se_field=="filled/city"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_city}}</option>
						<option value="filled/state" {{if $smarty.session.save.$page_name.se_field=="filled/state"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_state}}</option>
						<option value="filled/height" {{if $smarty.session.save.$page_name.se_field=="filled/height"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_height}}</option>
						<option value="filled/weight" {{if $smarty.session.save.$page_name.se_field=="filled/weight"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_weight}}</option>
						<option value="filled/hair_id" {{if $smarty.session.save.$page_name.se_field=="filled/hair_id"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_hair}}</option>
						<option value="filled/eye_color_id" {{if $smarty.session.save.$page_name.se_field=="filled/eye_color_id"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_eye_color}}</option>
						<option value="filled/measurements" {{if $smarty.session.save.$page_name.se_field=="filled/measurements"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_measurements}}</option>
						<option value="filled/gallery_url" {{if $smarty.session.save.$page_name.se_field=="filled/gallery_url"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_gallery_url}}</option>
						<option value="filled/age" {{if $smarty.session.save.$page_name.se_field=="filled/age"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_age}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.model_field_categories}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="MODEL_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_MODEL_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="MODEL_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_MODEL_FILE_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_usage">
						<option value="">{{$lang.common.dg_filter_usage}}...</option>
						<option value="used/videos" {{if $smarty.session.save.$page_name.se_usage=="used/videos"}}selected{{/if}}>{{$lang.common.dg_filter_usage_videos}}</option>
						<option value="used/albums" {{if $smarty.session.save.$page_name.se_usage=="used/albums"}}selected{{/if}}>{{$lang.common.dg_filter_usage_albums}}</option>
						<option value="used/posts" {{if $smarty.session.save.$page_name.se_usage=="used/posts"}}selected{{/if}}>{{$lang.common.dg_filter_usage_posts}}</option>
						<option value="used/other" {{if $smarty.session.save.$page_name.se_usage=="used/other"}}selected{{/if}}>{{$lang.common.dg_filter_usage_other}}</option>
						<option value="used/all" {{if $smarty.session.save.$page_name.se_usage=="used/all"}}selected{{/if}}>{{$lang.common.dg_filter_usage_any}}</option>
						<option value="notused/videos" {{if $smarty.session.save.$page_name.se_usage=="notused/videos"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_videos}}</option>
						<option value="notused/albums" {{if $smarty.session.save.$page_name.se_usage=="notused/albums"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_albums}}</option>
						<option value="notused/posts" {{if $smarty.session.save.$page_name.se_usage=="notused/posts"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_posts}}</option>
						<option value="notused/other" {{if $smarty.session.save.$page_name.se_usage=="notused/other"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_other}}</option>
						<option value="notused/all" {{if $smarty.session.save.$page_name.se_usage=="notused/all"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_any}}</option>
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
				{{if in_array('users|manage_comments',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=4&amp;object_id=${id}</span>
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
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=4&amp;se_object_id=${id}</span>
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