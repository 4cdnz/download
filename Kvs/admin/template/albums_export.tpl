{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_export">
	<div class="de_main">
		<div class="de_header"><h1><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.export_header_export}}</h1></div>
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
					<select name="preset_id" class="de_switcher" {{if $smarty.post.se_file_ids>0}}data-switcher-params="se_file_ids={{$smarty.post.se_file_ids}}"{{/if}}>
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach key="key" item="item" from=$list_presets|smarty:nodefaults}}
							<option value="{{$key}}" {{if $smarty.get.preset_id==$key}}selected{{/if}}>{{$key}}</option>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.albums.export_divider_fields}}</h2></td>
			</tr>
			{{foreach from=$smarty.post.fields|smarty:nodefaults item="field" name="fields"}}
				<tr data-endless-list-item="fields[]">
					<td class="de_label" data-endless-list-text="{{$lang.albums.import_export_field|replace:"%1%":"\${index}"}}">{{$lang.albums.import_export_field|replace:"%1%":$smarty.foreach.fields.iteration}}</td>
					<td class="de_control">
						<select name="fields[]">
							<option value="">{{$lang.common.select_default_option}}</option>
							<optgroup label="{{$lang.albums.import_export_group_general}}">
								<option value="album_id" {{if $field=='album_id'}}selected{{/if}}>{{$lang.albums.import_export_field_id}}</option>
								<option value="title" {{if $field=='title'}}selected{{/if}}>{{$lang.albums.import_export_field_title}}</option>
								<option value="description" {{if $field=='description'}}selected{{/if}}>{{$lang.albums.import_export_field_description}}</option>
								<option value="directory" {{if $field=='directory'}}selected{{/if}}>{{$lang.albums.import_export_field_directory}}</option>
								<option value="website_link" {{if $field=='website_link'}}selected{{/if}}>{{$lang.albums.import_export_field_website_link}}</option>
								{{foreach item="item" from=$list_satellites|smarty:nodefaults}}
									{{if is_array($item.website_ui_data)}}
										<option value="website_link/{{$item.multi_prefix}}" {{if $field=="website_link/`$item.multi_prefix`"}}selected{{/if}}>{{$lang.albums.import_export_field_satellite_link}} ({{$item.project_url}})</option>
									{{/if}}
								{{/foreach}}
								<option value="post_date" {{if $field=='post_date'}}selected{{/if}}>{{$lang.albums.import_export_field_post_date}}</option>
								<option value="added_date" {{if $field=='added_date'}}selected{{/if}}>{{$lang.albums.import_export_field_added_date}}</option>
								<option value="rating" {{if $field=='rating'}}selected{{/if}}>{{$lang.albums.import_export_field_rating}}</option>
								<option value="rating_percent" {{if $field=='rating_percent'}}selected{{/if}}>{{$lang.albums.import_export_field_rating_percent}}</option>
								<option value="rating_amount" {{if $field=='rating_amount'}}selected{{/if}}>{{$lang.albums.import_export_field_rating_amount}}</option>
								<option value="album_viewed" {{if $field=='album_viewed'}}selected{{/if}}>{{$lang.albums.import_export_field_visits}}</option>
								<option value="user" {{if $field=='user'}}selected{{/if}}>{{$lang.albums.import_export_field_user}}</option>
								<option value="status" {{if $field=='status'}}selected{{/if}}>{{$lang.albums.import_export_field_status}}</option>
								<option value="type" {{if $field=='type'}}selected{{/if}}>{{$lang.albums.import_export_field_type}}</option>
								<option value="access_level" {{if $field=='access_level'}}selected{{/if}}>{{$lang.albums.import_export_field_access_level}}</option>
								{{if $config.installation_type>=2}}
									<option value="tokens" {{if $field=='tokens'}}selected{{/if}}>{{$lang.albums.import_export_field_tokens_cost}}</option>
								{{/if}}
								<option value="admin_flag" {{if $field=='admin_flag'}}selected{{/if}}>{{$lang.albums.import_export_field_admin_flag}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_categorization}}">
								<option value="categories" {{if $field=='categories'}}selected{{/if}}>{{$lang.albums.import_export_field_categories}}</option>
								<option value="models" {{if $field=='models'}}selected{{/if}}>{{$lang.albums.import_export_field_models}}</option>
								<option value="tags" {{if $field=='tags'}}selected{{/if}}>{{$lang.albums.import_export_field_tags}}</option>
								<option value="content_source" {{if $field=='content_source'}}selected{{/if}}>{{$lang.albums.import_export_field_content_source}}</option>
								<option value="content_source/url" {{if $field=='content_source/url'}}selected{{/if}}>{{$lang.albums.import_export_field_content_source_url}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_content}}">
								<option value="image_preview_source" {{if $field=="image_preview_source"}}selected{{/if}}>{{$lang.albums.import_export_field_image_preview_source}}</option>
								{{foreach item="item" from=$list_formats_images_preview|smarty:nodefaults}}
									<option value="image_preview_{{$item.format_album_id}}" {{if $field=="image_preview_`$item.format_album_id`"}}selected{{/if}}>{{$lang.albums.import_export_field_image_preview_format|replace:"%1%":$item.title}}</option>
								{{/foreach}}
								<option value="main_images_sources" {{if $field=="main_images_sources"}}selected{{/if}}>{{$lang.albums.import_export_field_images_main_sources}}</option>
								{{foreach item="item" from=$list_formats_images_main|smarty:nodefaults}}
									<option value="main_images_{{$item.format_album_id}}" {{if $field=="main_images_`$item.format_album_id`"}}selected{{/if}}>{{$lang.albums.import_export_field_images_main_format|replace:"%1%":$item.title}}</option>
									{{if $item.is_create_zip==1}}
										<option value="main_images_zip_{{$item.format_album_id}}" {{if $field=="main_images_zip_`$item.format_album_id`"}}selected{{/if}}>{{$lang.albums.import_export_field_images_main_format_zip|replace:"%1%":$item.title}}</option>
									{{/if}}
								{{/foreach}}
								<option value="gallery_url" {{if $field=='gallery_url'}}selected{{/if}}>{{$lang.albums.import_export_field_gallery_url}}</option>
							</optgroup>
							<optgroup label="{{$lang.albums.import_export_group_custom}}">
								<option value="custom_1" {{if $field=='custom_1'}}selected{{/if}}>{{$options.ALBUM_FIELD_1_NAME}}</option>
								<option value="custom_2" {{if $field=='custom_2'}}selected{{/if}}>{{$options.ALBUM_FIELD_2_NAME}}</option>
								<option value="custom_3" {{if $field=='custom_3'}}selected{{/if}}>{{$options.ALBUM_FIELD_3_NAME}}</option>
							</optgroup>
							{{if count($list_languages)>0}}
								<optgroup label="{{$lang.albums.import_export_group_localization}}">
									{{foreach item="item" from=$list_languages|smarty:nodefaults}}
										<option value="title_{{$item.code}}" {{if $field=="title_`$item.code`"}}selected{{/if}}>{{$lang.albums.import_export_field_title}} ({{$item.title}})</option>
										<option value="description_{{$item.code}}" {{if $field=="description_`$item.code`"}}selected{{/if}}>{{$lang.albums.import_export_field_description}} ({{$item.title}})</option>
										{{if $item.is_directories_localize==1}}
											<option value="directory_{{$item.code}}" {{if $field=="directory_`$item.code`"}}selected{{/if}}>{{$lang.albums.import_export_field_directory}} ({{$item.title}})</option>
										{{/if}}
									{{/foreach}}
								</optgroup>
							{{/if}}
						</select>
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
			<tr>
				<td class="de_label">{{$lang.albums.export_field_header_row}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_header_row" value="1" {{if $smarty.post.is_header_row==1}}checked{{/if}}/><label>{{$lang.albums.export_field_header_row_yes}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_order}}</td>
				<td class="de_control">
					<select name="order_by">
						<option value="post_date" {{if $smarty.post.order_by=="post_date"}}selected{{/if}}>{{$lang.albums.import_export_field_post_date}}</option>
						<option value="album_id" {{if $smarty.post.order_by=="album_id"}}selected{{/if}}>{{$lang.albums.import_export_field_id}}</option>
						<option value="title" {{if $smarty.post.order_by=="title"}}selected{{/if}}>{{$lang.albums.import_export_field_title}}</option>
						<option value="description" {{if $smarty.post.order_by=="description"}}selected{{/if}}>{{$lang.albums.import_export_field_description}}</option>
						<option value="content_source" {{if $smarty.post.order_by=="content_source"}}selected{{/if}}>{{$lang.albums.import_export_field_content_source}}</option>
						<option value="rating" {{if $smarty.post.order_by=="rating"}}selected{{/if}}>{{$lang.albums.import_export_field_rating}}</option>
						<option value="album_viewed" {{if $smarty.post.order_by=="album_viewed"}}selected{{/if}}>{{$lang.albums.import_export_field_visits}}</option>
						<option value="user" {{if $smarty.post.order_by=="user"}}selected{{/if}}>{{$lang.albums.import_export_field_user}}</option>
						<option value="custom_1" {{if $smarty.post.order_by=="custom_1"}}selected{{/if}}>{{$options.ALBUM_FIELD_1_NAME}}</option>
						<option value="custom_2" {{if $smarty.post.order_by=="custom_2"}}selected{{/if}}>{{$options.ALBUM_FIELD_2_NAME}}</option>
						<option value="custom_3" {{if $smarty.post.order_by=="custom_3"}}selected{{/if}}>{{$options.ALBUM_FIELD_3_NAME}}</option>
						<option value="rand" {{if $smarty.post.order_by=="rand"}}selected{{/if}}>{{$lang.albums.export_field_order_random}}</option>
					</select>
					<select name="order_direction">
						<option value="desc" {{if $smarty.post.order_direction=="desc"}}selected{{/if}}>{{$lang.common.order_desc}}</option>
						<option value="asc" {{if $smarty.post.order_direction=="asc"}}selected{{/if}}>{{$lang.common.order_asc}}</option>
					</select>
				</td>
			</tr>
			{{if count($list_languages)>0}}
				<tr>
					<td class="de_label">{{$lang.albums.export_field_language}}</td>
					<td class="de_control">
						<select name="language">
							<option value="">{{$lang.albums.export_field_language_default}}</option>
							{{foreach name="data" item="item" from=$list_languages|smarty:nodefaults}}
								<option value="{{$item.code}}" {{if $smarty.post.language==$item.code}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			{{/if}}
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
				<td class="de_label">{{$lang.albums.export_field_limit}}</td>
				<td class="de_control">
					<input type="text" name="limit" size="10" value="{{$smarty.post.limit}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.albums.export_divider_filters}}</h2></td>
			</tr>
			{{if $smarty.post.se_file_ids>0}}
				<tr>
					<td class="de_label">{{$lang.albums.export_field_search_ids_list}}</td>
					<td class="de_control">
						<span>
							{{$lang.albums.export_field_search_ids_list_value|replace:"%1%":$smarty.post.se_file_ids_count}}
							<input type="hidden" name="se_file_ids" value="{{$smarty.post.se_file_ids}}"/>
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.albums.export_field_search_string}}</td>
				<td class="de_control">
					<input type="text" name="se_text" size="30" value="{{$smarty.post.se_text}}"/>
					<span class="de_hint">{{$lang.albums.export_field_search_string_hint|replace:"%1%":$lang.albums.import_export_field_title|replace:"%2%":$lang.albums.import_export_field_description}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_status}}</td>
				<td class="de_control">
					<select name="se_status_id">
						<option value="">{{$lang.albums.export_field_status_all}}</option>
						<option value="0" {{if $smarty.post.se_status_id=="0"}}selected{{/if}}>{{$lang.albums.export_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.se_status_id=="1"}}selected{{/if}}>{{$lang.albums.export_field_status_active}}</option>
						<option value="2" {{if $smarty.post.se_status_id=="2"}}selected{{/if}}>{{$lang.albums.export_field_status_error}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_categorization_status}}</td>
				<td class="de_control">
					<select name="se_categorization_status_id">
						<option value="">{{$lang.albums.export_field_categorization_status_all}}</option>
						<option value="1" {{if $smarty.post.se_categorization_status_id=="1"}}selected{{/if}}>{{$lang.albums.export_field_categorization_status_active}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_review_flag}}</td>
				<td class="de_control">
					<select name="se_review_flag">
						<option value="">{{$lang.common.dg_filter_option_all}}</option>
						<option value="1" {{if $smarty.post.se_review_flag=="1"}}selected{{/if}}>{{$lang.albums.export_field_review_flag_yes}}</option>
						<option value="2" {{if $smarty.post.se_review_flag=="2"}}selected{{/if}}>{{$lang.albums.export_field_review_flag_no}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_admins}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_admins}}</span>
							<span class="js_param">url=async/insight.php?type=admins</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=se_admin_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_admins_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.admins|smarty:nodefaults}}
							<input type="hidden" name="se_admin_ids[]" value="{{$item.user_id}}" alt="{{$item.login}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_admin"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_admins_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_users}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_users}}</span>
							<span class="js_param">url=async/insight.php?type=users</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=se_user_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_users_empty}}</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.users|smarty:nodefaults}}
							<input type="hidden" name="se_user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_user"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_users_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_categories}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=se_category_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_categories_empty}}</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="se_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_category"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_categories_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_models}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=se_model_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_models_empty}}</span>
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
							<input type="hidden" name="se_model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_model"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_models_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_tags}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=simple</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_tags_empty}}</span>
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						<input type="hidden" name="se_tags" value="{{$smarty.post.se_tags}}"/>
						<div class="controls">
							<input type="text" name="new_tag"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_tags_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_content_sources}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.albums.export_field_content_sources}}</span>
							<span class="js_param">url=async/insight.php?type=content_sources</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=se_cs_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.export_field_content_sources_empty}}</span>
							{{if in_array('content_sources|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.content_sources|smarty:nodefaults}}
							<input type="hidden" name="se_cs_ids[]" value="{{$item.content_source_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_cs"/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.export_field_content_sources_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_type}}</td>
				<td class="de_control">
					<select name="se_is_private">
						<option value="">{{$lang.common.dg_filter_option_all}}</option>
						<option value="0" {{if $smarty.post.se_is_private=="0"}}selected{{/if}}>{{$lang.albums.export_field_type_public}}</option>
						<option value="1" {{if $smarty.post.se_is_private=="1"}}selected{{/if}}>{{$lang.albums.export_field_type_private}}</option>
						<option value="2" {{if $smarty.post.se_is_private=="2"}}selected{{/if}}>{{$lang.albums.export_field_type_premium}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_admin_flag}}</td>
				<td class="de_control">
					<select name="se_admin_flag_id">
						<option value="">{{$lang.common.dg_filter_option_all}}</option>
						{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.se_admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.export_field_post_time}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_post_time_considered" value="1" {{if $smarty.post.is_post_time_considered==1}}checked{{/if}} /><label>{{$lang.albums.export_field_post_time_enabled}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label is_post_date_range_enabled_on">{{$lang.albums.export_field_post_date_range}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_post_date_range_enabled" value="1" {{if $smarty.post.is_post_date_range_enabled==1}}checked{{/if}}/><label>{{$lang.albums.export_field_post_date_range_enable}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.albums.export_field_post_date_range_from}}:
									<span class="calendar">
										<input type="text" name="post_date_from" value="{{$smarty.post.post_date_from}}" class="is_post_date_range_enabled_on" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
								<span>
									{{$lang.albums.export_field_post_date_range_to}}:
									<span class="calendar">
										<input type="text" name="post_date_to" value="{{$smarty.post.post_date_to}}" class="is_post_date_range_enabled_on" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label is_added_date_range_enabled_on">{{$lang.albums.export_field_added_date_range}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_added_date_range_enabled" value="1" {{if $smarty.post.is_added_date_range_enabled==1}}checked{{/if}}/><label>{{$lang.albums.export_field_added_date_range_enable}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.albums.export_field_added_date_range_from}}:
									<span class="calendar">
										<input type="text" name="added_date_from" value="{{$smarty.post.added_date_from}}" class="is_added_date_range_enabled_on" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
								<span>
									{{$lang.albums.export_field_added_date_range_to}}:
									<span class="calendar">
										<input type="text" name="added_date_to" value="{{$smarty.post.added_date_to}}" class="is_added_date_range_enabled_on" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label is_id_range_enabled_on">{{$lang.albums.export_field_id_range}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_id_range_enabled" value="1" {{if $smarty.post.is_id_range_enabled==1}}checked{{/if}}/><label>{{$lang.albums.export_field_id_range_enable}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.albums.export_field_id_range_from}}:
									<input type="text" name="id_range_from" size="10" class="is_id_range_enabled_on" value="{{$smarty.post.id_range_from}}"/>
								</span>
								<span>
									{{$lang.albums.export_field_id_range_to}}:
									<input type="text" name="id_range_to" size="10" class="is_id_range_enabled_on" value="{{$smarty.post.id_range_to}}"/>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="start_export"/>
		<input type="submit" name="save_default" value="{{$lang.albums.export_btn_export}}"/>
		{{if $smarty.get.preset_id!=''}}
			<span class="de_separated_group">
				<input type="submit" name="delete_preset" value="{{$lang.albums.import_export_btn_delete_preset}}" class="destructive" data-confirm="{{$lang.albums.import_export_btn_delete_preset_confirm|replace:"%1%":$smarty.get.preset_id}}"/>
			</span>
		{{/if}}
	</div>
</form>