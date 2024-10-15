{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $import_id>0 && $smarty.get.action=='import_start'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_import_validation">
	<div class="de_main">
		<div class="de_header"><h1><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / <a href="{{$page_name}}?action=back_import&amp;import_id={{$import_id}}">{{$lang.albums.import_header_import}}</a> / {{$lang.albums.import_header_preview}}</h1></div>
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
				<td class="de_label">{{$lang.albums.import_preview_total_empty_lines}}</td>
				<td class="de_control">
					<span>{{$smarty.post.import_stats.empty_lines}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_preview_total_items}}</td>
				<td class="de_control">
					<span>{{$smarty.post.import_stats.total_items}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_preview_total_errors}}</td>
				<td class="de_control">
					<span>{{$smarty.post.import_stats.errors}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{if $smarty.post.import_mode==1}}{{$lang.albums.import_preview_total_to_update}}{{else}}{{$lang.albums.import_preview_total_to_import}}{{/if}}</td>
				<td class="de_control">
					<b>{{if $smarty.post.import_stats.ok_items==0}}<span class="highlighted_text">{{$smarty.post.import_stats.ok_items}}</span>{{else}}{{$smarty.post.import_stats.ok_items}}{{/if}}</b>
				</td>
			</tr>
			{{if count($smarty.post.import_result)>0}}
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col class="eg_column_small"/>
								<col class="eg_column_medium"/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.albums.import_preview_col_row}}</td>
								<td>{{$lang.albums.import_preview_col_type}}</td>
								<td>{{$lang.albums.import_preview_col_message}}</td>
							</tr>
							{{foreach key="group_id" item="item" from=$smarty.post.import_result|smarty:nodefaults}}
								<tr class="eg_group_header">
									<td colspan="3">
										<span data-accordeon="message_group_{{$group_id}}">
											{{if $group_id=='required'}}
												{{$lang.albums.import_preview_group_required}}
											{{elseif $group_id=='invalid'}}
												{{$lang.albums.import_preview_group_invalid}}
											{{elseif $group_id=='blacklist'}}
												{{$lang.albums.import_preview_group_blacklist}}
											{{elseif $group_id=='update_error'}}
												{{$lang.albums.import_preview_group_update_error}}
											{{elseif $group_id=='update_empty'}}
												{{$lang.albums.import_preview_group_update_empty}}
											{{elseif $group_id=='duplicates'}}
												{{$lang.albums.import_preview_group_duplicates}}
											{{elseif $group_id=='object_creation_not_allowed'}}
												{{$lang.albums.import_preview_group_object_creation_not_allowed}}
											{{elseif $group_id=='object_creation_ignored'}}
												{{$lang.albums.import_preview_group_object_creation_ignored}}
											{{elseif $group_id=='object_creation'}}
												{{$lang.albums.import_preview_group_object_creation}}
											{{elseif $group_id=='grabbers'}}
												{{$lang.albums.import_preview_group_grabbers}}
											{{elseif $group_id=='filters'}}
												{{$lang.albums.import_preview_group_filters}}
											{{elseif $group_id=='success'}}
												{{$lang.albums.import_preview_group_success}}
											{{/if}}
											({{$item|@count}})
										</span>
									</td>
								</tr>
								{{assign var="row_num" value=1}}
								{{foreach item="message" from=$item|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}} message_group_{{$group_id}}">
										<td>{{$message.line}}</td>
										{{if $message.type=='errors'}}
											<td class="highlighted_text">{{$lang.albums.import_preview_col_type_error}}</td>
										{{elseif $message.type=='warnings'}}
											<td class="warning_text">{{$lang.albums.import_preview_col_type_warning}}</td>
										{{else}}
											<td>{{$lang.albums.import_preview_col_type_info}}</td>
										{{/if}}
										<td>{{$message.message}}</td>
									</tr>
									{{assign var="row_num" value=$row_num+1}}
								{{/foreach}}
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="import_id" value="{{$import_id}}"/>
		<input type="submit" name="back_import" value="{{$lang.albums.import_btn_back}}"/>
		{{if $smarty.post.import_stats.errors==0}}
			<input type="submit" name="save_default" value="{{$lang.albums.import_btn_confirmed}}"/>
		{{else}}
			<input type="submit" name="save_default" value="{{$lang.albums.import_btn_skip}}" {{if $smarty.post.import_stats.ok_items==0}}disabled{{/if}}/>
		{{/if}}
	</div>
</form>

{{else}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_import">
	<div class="de_main">
		<div class="de_header"><h1><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.import_header_import}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/280-how-to-migrate-your-data-from-1-kvs-site-to-another/">How to migrate your data from 1 KVS site to another</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_export_field_preset}}</td>
				<td class="de_control">
					<select name="preset_id" class="de_switcher">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach key="key" item="item" from=$list_presets|smarty:nodefaults}}
							<option value="{{$key}}" {{if $smarty.get.preset_id==$key || $smarty.post.preset_id==$key || $smarty.post.preset_name==$key}}selected{{/if}}>{{$key}}</option>
						{{/foreach}}
					</select>
					<span>
						{{$lang.albums.import_export_field_preset_create}}:
						<input type="text" name="preset_name" maxlength="50" size="20"/>
					</span>
					<span class="de_lv_pair"><input type="checkbox" name="is_default_preset" value="1" {{if $smarty.post.is_default_preset==1}}checked{{/if}}/><label>{{$lang.albums.import_export_field_preset_default}}</label></span>
					<span class="de_hint">{{$lang.albums.import_export_field_preset_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_export_field_preset_description}}</td>
				<td class="de_control">
					<textarea name="preset_description" cols="40" rows="3">{{$smarty.post.preset_description}}</textarea>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_export_field_preset_blacklist}}</td>
				<td class="de_control">
					<textarea name="preset_blacklist" cols="40" rows="3">{{$smarty.post.preset_blacklist}}</textarea>
					<span class="de_hint">{{$lang.albums.import_export_field_preset_blacklist_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.albums.import_divider_data}}</h2></td>
			</tr>
			{{if in_array('albums|mass_edit',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.import_field_mode}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="import_mode">
							<option value="0">{{$lang.albums.import_field_mode_new}}</option>
							<option value="1" {{if $smarty.post.import_mode==1}}selected{{/if}}>{{$lang.albums.import_field_mode_update}}</option>
						</select>
						<span class="de_hint import_mode_1">{{$lang.albums.import_field_mode_update_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.albums.import_field_data_text}}</td>
				<td class="de_control">
					<div class="code_editor">
						<textarea name="data" cols="40" rows="8">{{$smarty.post.data}}</textarea>
					</div>
					<span class="de_hint">{{$lang.albums.import_field_data_text_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.albums.import_field_data_file}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.import_field_data_file}}</span>
						</div>
						<input type="text" name="file" value="{{$smarty.post.file}}"/>
						<input type="hidden" name="file_hash" value="{{$smarty.post.file_hash}}"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.albums.import_export_field_separator_fields}}</td>
				<td class="de_control">
					<input type="text" name="separator" size="10" value="{{$smarty.post.separator|default:"\\t"}}"/>
					<span class="de_hint">{{$lang.albums.import_export_field_separator_fields_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.albums.import_export_field_separator_lines}}</td>
				<td class="de_control">
					<input type="text" name="line_separator" size="10" value="{{$smarty.post.line_separator|default:"\\r\\n"}}"/>
					<span class="de_hint">{{$lang.albums.import_export_field_separator_lines_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.albums.import_divider_fields}}</h2></td>
			</tr>
			{{foreach from=$smarty.post.fields|smarty:nodefaults item="field" name="fields"}}
				<tr data-endless-list-item="fields[]">
					<td class="de_label" data-endless-list-text="{{$lang.albums.import_export_field|replace:"%1%":"\${index}"}}">{{$lang.albums.import_export_field|replace:"%1%":$smarty.foreach.fields.iteration}}</td>
					<td class="de_control">
						<select name="fields[]" data-import-field-select>
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="skip" {{if $field=='skip'}}selected{{/if}}>{{$lang.albums.import_export_field_skip}}</option>
							<optgroup label="{{$lang.albums.import_export_group_general}}">
								<option value="album_id" {{if $field=='album_id'}}selected{{/if}} title="{{$lang.albums.import_export_field_id_hint}}">{{$lang.albums.import_export_field_id}}</option>
								<option value="title" {{if $field=='title'}}selected{{/if}} title="{{$lang.albums.import_export_field_title_hint}}">{{$lang.albums.import_export_field_title}}</option>
								<option value="description" {{if $field=='description'}}selected{{/if}} title="{{$lang.albums.import_export_field_description_hint}}">{{$lang.albums.import_export_field_description}}</option>
								<option value="directory" {{if $field=='directory'}}selected{{/if}} title="{{$lang.albums.import_export_field_directory_hint}}">{{$lang.albums.import_export_field_directory}}</option>
								<option value="post_date" {{if $field=='post_date'}}selected{{/if}} title="{{$lang.albums.import_export_field_post_date_hint}}">{{$lang.albums.import_export_field_post_date}}</option>
								{{if $config.relative_post_dates=='true'}}
									<option value="relative_post_date" {{if $field=='relative_post_date'}}selected{{/if}} title="{{$lang.albums.import_export_field_post_date_relative_hint}}">{{$lang.albums.import_export_field_post_date_relative}}</option>
								{{/if}}
								<option value="rating" {{if $field=='rating'}}selected{{/if}} title="{{$lang.albums.import_export_field_rating_hint}}">{{$lang.albums.import_export_field_rating}}</option>
								<option value="rating_percent" {{if $field=='rating_percent'}}selected{{/if}} title="{{$lang.albums.import_export_field_rating_percent_hint}}">{{$lang.albums.import_export_field_rating_percent}}</option>
								<option value="rating_amount" {{if $field=='rating_amount'}}selected{{/if}} title="{{$lang.albums.import_export_field_rating_amount_hint}}">{{$lang.albums.import_export_field_rating_amount}}</option>
								<option value="album_viewed" {{if $field=='album_viewed'}}selected{{/if}} title="{{$lang.albums.import_export_field_visits_hint}}">{{$lang.albums.import_export_field_visits}}</option>
								<option value="user" {{if $field=='user'}}selected{{/if}} title="{{$lang.albums.import_export_field_user_hint}}">{{$lang.albums.import_export_field_user}}</option>
								<option value="status" {{if $field=='status'}}selected{{/if}} title="{{$lang.albums.import_export_field_status_hint}}">{{$lang.albums.import_export_field_status}}</option>
								<option value="type" {{if $field=='type'}}selected{{/if}} title="{{$lang.albums.import_export_field_type_hint}}">{{$lang.albums.import_export_field_type}}</option>
								<option value="access_level" {{if $field=='access_level'}}selected{{/if}} title="{{$lang.albums.import_export_field_access_level_hint}}">{{$lang.albums.import_export_field_access_level}}</option>
								{{if $config.installation_type>=2}}
									<option value="tokens" {{if $field=='tokens'}}selected{{/if}} title="{{$lang.albums.import_export_field_tokens_cost_hint}}">{{$lang.albums.import_export_field_tokens_cost}}</option>
								{{/if}}
								<option value="admin_flag" {{if $field=='admin_flag'}}selected{{/if}} title="{{$lang.albums.import_export_field_admin_flag_hint}}">{{$lang.albums.import_export_field_admin_flag}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_categorization}}">
								<option value="categories" {{if $field=='categories'}}selected{{/if}} title="{{$lang.albums.import_export_field_categories_hint}}">{{$lang.albums.import_export_field_categories}}</option>
								{{foreach item="item" from=$list_categories_groups|smarty:nodefaults}}
									<option value="category_group_{{$item.category_group_id}}" {{if $field=="category_group_`$item.category_group_id`"}}selected{{/if}} title="{{$lang.albums.import_export_field_categories_hint}}">{{$lang.albums.import_export_field_categories}} ({{$item.title}})</option>
								{{/foreach}}
								<option value="models" {{if $field=='models'}}selected{{/if}} title="{{$lang.albums.import_export_field_models_hint}}">{{$lang.albums.import_export_field_models}}</option>
								<option value="tags" {{if $field=='tags'}}selected{{/if}} title="{{$lang.albums.import_export_field_tags_hint}}">{{$lang.albums.import_export_field_tags}}</option>
								<option value="content_source" {{if $field=='content_source'}}selected{{/if}} title="{{$lang.albums.import_export_field_content_source_hint}}">{{$lang.albums.import_export_field_content_source}}</option>
								<option value="content_source/url" {{if $field=='content_source/url'}}selected{{/if}} title="{{$lang.albums.import_export_field_content_source_url_hint}}">{{$lang.albums.import_export_field_content_source_url}}</option>
								<option value="content_source/group" {{if $field=='content_source/group'}}selected{{/if}} title="{{$lang.albums.import_export_field_content_source_group_hint}}">{{$lang.albums.import_export_field_content_source_group}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_content}}">
								<option value="images_zip" {{if $field=='images_zip'}}selected{{/if}} title="{{$lang.albums.import_export_field_images_zip_hint}}">{{$lang.albums.import_export_field_images_zip}}</option>
								<option value="images_sources" {{if $field=='images_sources'}}selected{{/if}} title="{{$lang.albums.import_export_field_images_sources_hint}}">{{$lang.albums.import_export_field_images_sources}}</option>
								<option value="image_main_number" {{if $field=='image_main_number'}}selected{{/if}} title="{{$lang.albums.import_export_field_image_main_number_hint}}">{{$lang.albums.import_export_field_image_main_number}}</option>
								<option value="image_preview" {{if $field=='image_preview'}}selected{{/if}} title="{{$lang.albums.import_export_field_image_preview_source_hint}}">{{$lang.albums.import_export_field_image_preview_source}}</option>
								<option value="gallery_url" {{if $field=='gallery_url'}}selected{{/if}} title="{{$lang.albums.import_export_field_gallery_url_hint}}">{{$lang.albums.import_export_field_gallery_url}}</option>
								<option value="server_group" {{if $field=='server_group'}}selected{{/if}} title="{{$lang.albums.import_export_field_server_group_hint}}">{{$lang.albums.import_export_field_server_group}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_custom}}">
								<option value="custom1" {{if $field=='custom1'}}selected{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_1_NAME}}</option>
								<option value="custom2" {{if $field=='custom2'}}selected{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_2_NAME}}</option>
								<option value="custom3" {{if $field=='custom3'}}selected{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_3_NAME}}</option>
							</optgroup>
							{{if count($list_languages)>0}}
								<optgroup label="{{$lang.albums.import_export_group_localization}}">
									{{foreach item="item" from=$list_languages|smarty:nodefaults}}
										<option value="title_{{$item.code}}" {{if $field=="title_`$item.code`"}}selected{{/if}} title="{{$lang.albums.import_export_field_title_hint}}">{{$lang.albums.import_export_field_title}} ({{$item.title}})</option>
										<option value="description_{{$item.code}}" {{if $field=="description_`$item.code`"}}selected{{/if}} title="{{$lang.albums.import_export_field_description_hint}}">{{$lang.albums.import_export_field_description}} ({{$item.title}})</option>
										{{if $item.is_directories_localize==1}}
											<option value="directory_{{$item.code}}" {{if $field=="directory_`$item.code`"}}selected{{/if}} title="{{$lang.albums.import_export_field_directory_hint}}">{{$lang.albums.import_export_field_directory}} ({{$item.title}})</option>
										{{/if}}
									{{/foreach}}
								</optgroup>
							{{/if}}
						</select>
						<span data-import-field-desc></span>
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td></td>
				<td class="de_control">{{$lang.albums.import_export_field_more}}</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.albums.import_export_divider_options}}</h2></td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_threads}}</td>
				<td class="de_control">
					<select name="threads">
						{{section name="threads" start="1" loop="21"}}
							<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.threads}}selected{{/if}}>{{$smarty.section.threads.iteration}}</option>
						{{/section}}
					</select>
					<span class="de_hint">{{$lang.albums.import_field_threads_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_field_limit_title}}</td>
				<td class="de_control">
					<input type="text" name="title_limit" value="{{$smarty.post.title_limit}}" maxlength="10" size="4"/>
					<select name="title_limit_type_id">
						<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected{{/if}}>{{$lang.albums.import_field_limit_title_words}}</option>
						<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected{{/if}}>{{$lang.albums.import_field_limit_title_characters}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.import_field_limit_title_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_field_limit_description}}</td>
				<td class="de_control">
					<input type="text" name="description_limit" value="{{$smarty.post.description_limit}}" maxlength="10" size="4"/>
					<select name="description_limit_type_id">
						<option value="1" {{if $smarty.post.description_limit_type_id=="1"}}selected{{/if}}>{{$lang.albums.import_field_limit_description_words}}</option>
						<option value="2" {{if $smarty.post.description_limit_type_id=="2"}}selected{{/if}}>{{$lang.albums.import_field_limit_description_characters}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.import_field_limit_description_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_status_after_import}}</td>
				<td class="de_control">
					<select name="status_after_import_id">
						<option value="0" {{if $smarty.post.status_after_import_id=="0"}}selected{{/if}}>{{$lang.albums.import_field_status_after_import_active}}</option>
						<option value="1" {{if $smarty.post.status_after_import_id=="1"}}selected{{/if}}>{{$lang.albums.import_field_status_after_import_disabled}}</option>
					</select>
					<span class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.is_review_needed==1}}checked{{/if}}/><label>{{$lang.albums.import_field_options_need_review}}</label></span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_admin_flag}}</td>
				<td class="de_control">
					<select name="admin_flag_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.albums.import_field_admin_flag_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_album_type}}</td>
				<td class="de_control">
					<select name="default_album_type">
						<option value="public" {{if $smarty.post.default_album_type=="public"}}selected{{/if}}>{{$lang.albums.import_field_album_type_public}}</option>
						<option value="private" {{if $smarty.post.default_album_type=="private"}}selected{{/if}}>{{$lang.albums.import_field_album_type_private}}</option>
						<option value="premium" {{if $smarty.post.default_album_type=="premium"}}selected{{/if}}>{{$lang.albums.import_field_album_type_premium}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.import_field_album_type_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_content_source}}</td>
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
						<input type="text" name="content_source" maxlength="255" value="{{$smarty.post.content_source}}"/>
					</div>
					<span class="de_hint">{{$lang.albums.import_field_content_source_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_content_source_categories}}</td>
				<td class="de_control">
					<select name="content_source_categories_id">
						<option value="0" {{if $smarty.post.content_source_categories_id==0}}selected{{/if}}>{{$lang.albums.import_field_content_source_categories_no}}</option>
						<option value="1" {{if $smarty.post.content_source_categories_id==1}}selected{{/if}}>{{$lang.albums.import_field_content_source_categories_empty}}</option>
						<option value="2" {{if $smarty.post.content_source_categories_id==2}}selected{{/if}}>{{$lang.albums.import_field_content_source_categories_always}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.import_field_content_source_categories_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_model_categories}}</td>
				<td class="de_control">
					<select name="model_categories_id">
						<option value="0" {{if $smarty.post.model_categories_id==0}}selected{{/if}}>{{$lang.albums.import_field_model_categories_no}}</option>
						<option value="1" {{if $smarty.post.model_categories_id==1}}selected{{/if}}>{{$lang.albums.import_field_model_categories_empty}}</option>
						<option value="2" {{if $smarty.post.model_categories_id==2}}selected{{/if}}>{{$lang.albums.import_field_model_categories_always}}</option>
					</select>
					<span class="de_hint">{{$lang.albums.import_field_model_categories_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_users}}</td>
				<td class="de_control" colspan="3">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_insight_list">
									<div class="js_params">
										<span class="js_param">title={{$lang.albums.import_field_users}}</span>
										<span class="js_param">url=async/insight.php?type=users</span>
										<span class="js_param">submit_mode=compound</span>
										<span class="js_param">submit_name=user_ids[]</span>
										{{if in_array('users|add',$smarty.session.permissions)}}
											<span class="js_param">allow_creation=true</span>
										{{/if}}
										{{if in_array('users|view',$smarty.session.permissions)}}
											<span class="js_param">allow_view=true</span>
										{{/if}}
										<span class="js_param">empty_message={{$lang.albums.import_field_users_empty}}</span>
									</div>
									<div class="list"></div>
									{{foreach name="data" item="item" from=$smarty.post.users|smarty:nodefaults}}
										<input type="hidden" name="user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
									{{/foreach}}
									<div class="controls">
										<input type="text" name="new_user"/>
										<input type="button" class="add" value="{{$lang.common.add}}"/>
										<input type="button" class="all" value="{{$lang.albums.import_field_users_all}}"/>
									</div>
								</div>
							</td>
						</tr>
						{{if in_array('users|add',$smarty.session.permissions)}}
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="is_username_randomization" value="1" {{if $smarty.post.is_username_randomization==1}}checked{{/if}}/><label>{{$lang.albums.import_field_users_generate}}</label></span>
								</td>
							</tr>
						{{/if}}
					</table>
					<span class="de_hint">{{$lang.albums.import_field_users_hint}}</span>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_post_date}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_post_time_randomization" value="1" {{if $smarty.post.is_post_time_randomization==1}}checked{{/if}}/><label>{{$lang.albums.import_field_post_date_enable_time_random}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.albums.import_field_post_date_interval_from}}:
									<input type="text" name="post_time_randomization_from" maxlength="5" class="is_post_time_randomization_on" size="5" value="{{$smarty.post.post_time_randomization_from}}"/>
								</span>
								<span>
									{{$lang.albums.import_field_post_date_interval_to}}:
									<input type="text" name="post_time_randomization_to" maxlength="5" class="is_post_time_randomization_on" size="5" value="{{$smarty.post.post_time_randomization_to}}"/>
								</span>
								<span class="de_hint">{{$lang.albums.import_field_post_date_enable_time_random_hint}}</span>
							</td>
						</tr>
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input data-mutually-exclusive="is_post_date_randomization_days" type="checkbox" name="is_post_date_randomization" value="1" {{if $smarty.post.is_post_date_randomization==1}}checked{{/if}}/><label>{{$lang.albums.import_field_post_date_enable_date_random}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								{{if $config.relative_post_dates=='true'}}
									<table class="control_group de_vis_sw_radio">
										<tr>
											<td>
												<span class="de_lv_pair"><input id="post_date_randomization_option_fixed" type="radio" name="post_date_randomization_option" class="is_post_date_randomization_on" value="0" {{if $smarty.post.post_date_randomization_option!='1'}}checked{{/if}}/><label>{{$lang.albums.import_field_post_date_option_fixed}}</label></span>
												<span>
													{{$lang.albums.import_field_post_date_interval_from}}:
													<span class="calendar">
														<input type="text" name="post_date_randomization_from" class="is_post_date_randomization_on post_date_randomization_option_fixed" value="{{$smarty.post.post_date_randomization_from}}" placeholder="{{$lang.common.select_default_option}}">
													</span>
												</span>
												<span>
													{{$lang.albums.import_field_post_date_interval_to}}:
													<span class="calendar">
														<input type="text" name="post_date_randomization_to" class="is_post_date_randomization_on post_date_randomization_option_fixed" value="{{$smarty.post.post_date_randomization_to}}" placeholder="{{$lang.common.select_default_option}}">
													</span>
												</span>
												<span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint}}</span>
											</td>
										</tr>
										<tr>
											<td>
												<span class="de_lv_pair"><input id="post_date_randomization_option_relative" type="radio" name="post_date_randomization_option" class="is_post_date_randomization_on" value="1" {{if $smarty.post.post_date_randomization_option=='1'}}checked{{/if}}/><label>{{$lang.albums.import_field_post_date_option_relative}}</label></span>
												<span>
													{{$lang.albums.import_field_post_date_interval_from}}:
													<input type="text" name="relative_post_date_randomization_from" size="5" class="is_post_date_randomization_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_from}}" maxlength="5"/>
												</span>
												<span>
													{{$lang.albums.import_field_post_date_interval_to}}:
													<input type="text" name="relative_post_date_randomization_to" size="5" class="is_post_date_randomization_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_to}}" maxlength="5"/>
												</span>
												<span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint2}}</span>
											</td>
										</tr>
									</table>
								{{else}}
									<span>
										{{$lang.albums.import_field_post_date_interval_from}}:
										<span class="calendar">
											<input type="text" name="post_date_randomization_from" class="is_post_date_randomization_on" value="{{$smarty.post.post_date_randomization_from}}" placeholder="{{$lang.common.select_default_option}}">
										</span>
									</span>
									<span>
										{{$lang.albums.import_field_post_date_interval_to}}:
										<span class="calendar">
											<input type="text" name="post_date_randomization_to" class="is_post_date_randomization_on" value="{{$smarty.post.post_date_randomization_to}}" placeholder="{{$lang.common.select_default_option}}">
										</span>
									</span>
									<span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input data-mutually-exclusive="is_post_date_randomization" type="checkbox" name="is_post_date_randomization_days" value="1" {{if $smarty.post.is_post_date_randomization_days==1}}checked{{/if}}/><label>{{$lang.albums.import_field_post_date_enable_date_random2}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<input type="text" name="post_date_randomization_days" class="is_post_date_randomization_days_on" size="5" value="{{$smarty.post.post_date_randomization_days}}"/>
								<span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random2_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_field_duplicates}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles==1}}checked{{/if}}/><label>{{$lang.albums.import_field_duplicates_skip_duplicate_titles}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_urls" value="1" {{if $smarty.post.is_skip_duplicate_urls==1 || $smarty.post.fields.0==''}}checked{{/if}}/><label>{{$lang.albums.import_field_duplicates_skip_duplicate_urls}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_validation}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_validate_image_urls" value="1" {{if $smarty.post.is_validate_image_urls==1 || $smarty.post.fields.0==''}}checked{{/if}}/><label>{{$lang.albums.import_field_validation_validate_image_urls}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_validate_grabber_urls" value="1" {{if $smarty.post.is_validate_grabber_urls==1}}checked{{/if}}/><label>{{$lang.albums.import_field_validation_validate_grabber_urls}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.import_field_new_objects}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories==1}}checked{{/if}}/><label>{{$lang.albums.import_field_new_objects_categories}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models==1}}checked{{/if}}/><label>{{$lang.albums.import_field_new_objects_models}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources==1}}checked{{/if}}/><label>{{$lang.albums.import_field_new_objects_content_sources}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="import_mode_0">
				<td class="de_label">{{$lang.albums.import_field_options}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_use_rename_as_copy" value="1" {{if $smarty.post.is_use_rename_as_copy==1}}checked{{/if}}/><label>{{$lang.albums.import_field_options_use_rename_as_copy}}</label></span>
								<span class="de_hint">{{$lang.albums.import_field_options_use_rename_as_copy_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="start_import"/>
		<input type="submit" name="save_default" value="{{$lang.albums.import_btn_import}}"/>
		{{if $smarty.get.preset_id!=''}}
			<span class="de_separated_group">
				<input type="submit" name="delete_preset" value="{{$lang.albums.import_export_btn_delete_preset}}" class="destructive" data-confirm="{{$lang.albums.import_export_btn_delete_preset_confirm|replace:"%1%":$smarty.get.preset_id}}"/>
			</span>
		{{/if}}
	</div>
</form>

{{/if}}