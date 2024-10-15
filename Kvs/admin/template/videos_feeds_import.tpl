{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="video_importing_feed_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.videos.submenu_option_feeds_import}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.videos.feed_add}}{{else}}{{$lang.videos.feed_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
			<td class="de_simple_text" colspan="2">
				<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/57-6-ways-to-add-videos-into-kvs/">6 ways to add videos into KVS</a></span>
				<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/280-how-to-migrate-your-data-from-1-kvs-site-to-another/">How to migrate your data from 1 KVS site to another</a></span>
			</td>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}}/><label>{{$lang.videos.feed_field_status_active}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_url}}</td>
				<td class="de_control">
					<input type="text" name="url" value="{{$smarty.post.url}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="feed_type_id">
						<option value="csv" {{if $smarty.post.feed_type_id=='csv'}}selected{{/if}}>{{$lang.videos.feed_field_type_csv}}</option>
						<option value="kvs" {{if $smarty.post.feed_type_id=='kvs'}}selected{{/if}}>{{$lang.videos.feed_field_type_kvs}}</option>
						<option value="rss" {{if $smarty.post.feed_type_id=='rss'}}selected{{/if}}>{{$lang.videos.feed_field_type_rss}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_type_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_direction}}</td>
				<td class="de_control">
					<select name="direction_id">
						<option value="0" {{if $smarty.post.direction_id=='0'}}selected{{/if}}>{{$lang.videos.feed_field_direction_forward}}</option>
						<option value="1" {{if $smarty.post.direction_id=='1'}}selected{{/if}}>{{$lang.videos.feed_field_direction_reverse}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_encoding}}</td>
				<td class="de_control">
					<input type="text" name="feed_charset" maxlength="50" value="{{$smarty.post.feed_charset}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_encoding_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_duplicates}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.feed_field_key_prefix}}</td>
				<td class="de_control">
					<input type="text" name="key_prefix" maxlength="255" value="{{$smarty.post.key_prefix}}" {{if $smarty.get.action!='add_new'}}data-confirm-save="{{$lang.videos.feed_field_key_prefix_confirm}}" data-destructive="false"{{/if}}/>
					<span class="de_hint">{{$lang.videos.feed_field_key_prefix_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_duplicate_options}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_duplicate_options_titles}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_duplicate_options_titles_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="is_skip_deleted_videos" value="1" {{if $smarty.post.is_skip_deleted_videos==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_duplicate_options_deleted}}</label></span>
								<span class="de_hint">{{$lang.videos.feed_field_duplicate_options_deleted_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_scheduling}}</h2></td>
			</tr>
			<tr>
				<td class="de_label exec_interval_only_once_off">{{$lang.videos.feed_field_exec_interval}}</td>
				<td class="de_control">
					<span>
						{{$lang.videos.feed_field_exec_interval_hours}}:
						<input type="text" name="exec_interval_hours" maxlength="10" size="4" class="exec_interval_only_once_off" value="{{$smarty.post.exec_interval_hours}}"/>
					</span>
					<span>
						{{$lang.videos.feed_field_exec_interval_minutes}}:
						<input type="text" name="exec_interval_minutes" maxlength="10" size="4" class="exec_interval_only_once_off" value="{{$smarty.post.exec_interval_minutes}}"/>
					</span>
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="exec_interval_only_once" value="1" {{if $smarty.post.exec_interval_hours==0 && $smarty.post.exec_interval_minutes==0}}checked{{/if}}/><label>{{$lang.videos.feed_field_exec_interval_only_once}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_exec_interval_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_max_videos_per_exec}}</td>
				<td class="de_control">
					<input type="text" name="max_videos_per_exec" maxlength="10" size="10" value="{{if $smarty.post.max_videos_per_exec>0}}{{$smarty.post.max_videos_per_exec}}{{/if}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_max_videos_per_exec_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.videos.feed_field_last_exec_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.last_exec_date!='0000-00-00 00:00:00'}}
								{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}} (<a href="log_feeds.php?no_filter=true&se_feed_id={{$smarty.post.feed_id}}">{{$lang.videos.feed_field_last_exec_date_stats|replace:"%1%":$smarty.post.last_exec_duration|replace:"%2%":$smarty.post.last_exec_videos_added|replace:"%3%":$smarty.post.last_exec_videos_skipped|replace:"%4%":$smarty.post.last_exec_videos_errored}}</a>)
							{{else}}
								{{$lang.common.undefined}}
							{{/if}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.feed_field_next_exec_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.next_exec_date!='0000-00-00 00:00:00'}}
								{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							{{else}}
								{{$lang.common.undefined}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_logging}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="keep_log_days" maxlength="10" size="5" value="{{if $smarty.post.keep_log_days>0}}{{$smarty.post.keep_log_days}}{{/if}}"/>
						{{$lang.videos.feed_field_logging_days}}
					</span>
					<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_logging_debug}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_logging_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_autodelete}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_autodelete" value="1" {{if $smarty.post.is_autodelete==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_autodelete_enabled}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_autodelete_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_csv feed_type_id_rss is_autodelete_on">
				<td class="de_label de_required de_dependent">{{$lang.videos.feed_field_autodelete_url}}</td>
				<td class="de_control">
					<input type="text" name="autodelete_url" value="{{$smarty.post.autodelete_url}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_autodelete_url_hint}}</span>
				</td>
			</tr>
			<tr class="is_autodelete_on">
				<td class="de_label de_required de_dependent">{{$lang.videos.feed_field_autodelete_exec_interval}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="autodelete_exec_interval" size="5" value="{{if $smarty.post.autodelete_exec_interval>0}}{{$smarty.post.autodelete_exec_interval}}{{/if}}"/>
						{{$lang.videos.feed_field_autodelete_exec_interval_hours}}
					</span>
					<span class="de_hint">{{$lang.videos.feed_field_autodelete_exec_interval_hint}}</span>
				</td>
			</tr>
			<tr class="is_autodelete_on">
				<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_mode}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="autodelete_mode">
						<option value="0" {{if $smarty.post.autodelete_mode=='0'}}selected{{/if}}>{{$lang.videos.feed_field_autodelete_mode_delete}}</option>
						<option value="1" {{if $smarty.post.autodelete_mode=='1'}}selected{{/if}}>{{$lang.videos.feed_field_autodelete_mode_mark}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_autodelete_mode_hint}}</span>
				</td>
			</tr>
			<tr class="is_autodelete_on autodelete_mode_1">
				<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_reason}}</td>
				<td class="de_control">
					<textarea name="autodelete_reason" cols="30" rows="3">{{$smarty.post.autodelete_reason}}</textarea>
					<span class="de_hint">{{$lang.videos.feed_field_autodelete_reason_hint}}</span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr class="is_autodelete_on">
					<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_last_exec_date}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.autodelete_last_exec_date!='0000-00-00 00:00:00'}}
								{{$smarty.post.autodelete_last_exec_date|date_format:$smarty.session.userdata.full_date_format}} (<a href="log_feeds.php?no_filter=true&se_feed_id={{$smarty.post.feed_id}}">{{$lang.videos.feed_field_autodelete_last_exec_date_stats|replace:"%1%":$smarty.post.autodelete_last_exec_duration|replace:"%2%":$smarty.post.autodelete_last_exec_videos}}</a>)
							{{else}}
								{{$lang.common.undefined}}
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_autopaginate}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_autopaginate" value="1" {{if $smarty.post.is_autopaginate==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_autopaginate_enabled}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_autopaginate_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_csv feed_type_id_rss is_autopaginate_on">
				<td class="de_label de_required de_dependent">{{$lang.videos.feed_field_autopaginate_param}}</td>
				<td class="de_control">
					<input type="text" name="autopaginate_param" size="20" value="{{$smarty.post.autopaginate_param}}" maxlength="50"/>
					<span class="de_hint">{{$lang.videos.feed_field_autopaginate_param_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_csv feed_type_id_kvs">
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_data}}</h2></td>
			</tr>
			<tr class="feed_type_id_csv">
				<td class="de_label">{{$lang.videos.feed_field_skip_first_row}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="csv_skip_first_row" value="1" {{if $smarty.post.csv_skip_first_row==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_skip_first_row_yes}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_skip_first_row_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_csv">
				<td class="de_label de_required">{{$lang.videos.feed_field_separator_fields}}</td>
				<td class="de_control">
					<input type="text" name="separator" size="5" value="{{$smarty.post.separator|default:"|"}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_separator_fields_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_csv">
				<td class="de_label de_required">{{$lang.videos.feed_field_separator_list_items}}</td>
				<td class="de_control">
					<select name="separator_list_items">
						<option value="," {{if $smarty.post.separator_list_items==','}}selected{{/if}}>{{$lang.videos.feed_field_separator_list_items_comma}}</option>
						<option value=";" {{if $smarty.post.separator_list_items==';'}}selected{{/if}}>{{$lang.videos.feed_field_separator_list_items_semicolon}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_separator_list_items_hint}}</span>
				</td>
			</tr>
			{{foreach item="field_name" name="fields" from=$smarty.post.csv_fields}}
				<tr class="feed_type_id_csv" data-endless-list-item="csv_fields[]">
					<td class="de_label" data-endless-list-text="{{$lang.videos.feed_field_data_field|replace:"%1%":"\${index}"}}">{{$lang.videos.feed_field_data_field|replace:"%1%":$smarty.foreach.fields.iteration}}</td>
					<td class="de_control">
						<select name="csv_fields[]">
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="pass" {{if $field_name=='pass'}}selected{{/if}}>{{$lang.videos.feed_field_data_pass}}</option>
							<optgroup label="{{$lang.videos.feed_field_data_group_general}}">
								<option value="external_key_field" {{if $field_name=='external_key_field'}}selected{{/if}}>{{$lang.videos.feed_field_data_external_key}}</option>
								<option value="id" {{if $field_name=='id'}}selected{{/if}}>{{$lang.videos.feed_field_data_id}}</option>
								<option value="title" {{if $field_name=='title'}}selected{{/if}}>{{$lang.videos.feed_field_data_title}}</option>
								<option value="description" {{if $field_name=='description'}}selected{{/if}}>{{$lang.videos.feed_field_data_description}}</option>
								<option value="dir" {{if $field_name=='dir'}}selected{{/if}}>{{$lang.videos.feed_field_data_directory}}</option>
								<option value="post_date" {{if $field_name=='post_date'}}selected{{/if}}>{{$lang.videos.feed_field_data_post_date}}</option>
								<option value="rating" {{if $field_name=='rating'}}selected{{/if}}>{{$lang.videos.feed_field_data_rating}} (0 - 5)</option>
								<option value="rating_percent" {{if $field_name=='rating_percent'}}selected{{/if}}>{{$lang.videos.feed_field_data_rating}} (0 - 100%)</option>
								<option value="votes" {{if $field_name=='votes'}}selected{{/if}}>{{$lang.videos.feed_field_data_rating_votes}}</option>
								<option value="popularity" {{if $field_name=='popularity'}}selected{{/if}}>{{$lang.videos.feed_field_data_visits}}</option>
								<option value="release_year" {{if $field_name=='release_year'}}selected{{/if}}>{{$lang.videos.feed_field_data_release_year}}</option>
								<option value="user" {{if $field_name=='user'}}selected{{/if}}>{{$lang.videos.feed_field_data_user}}</option>
							</optgroup>
							<optgroup label="{{$lang.videos.feed_field_data_group_categorization}}">
								<option value="categories" {{if $field_name=='categories'}}selected{{/if}}>{{$lang.videos.feed_field_data_categories}}</option>
								<option value="models" {{if $field_name=='models'}}selected{{/if}}>{{$lang.videos.feed_field_data_models}}</option>
								<option value="tags" {{if $field_name=='tags'}}selected{{/if}}>{{$lang.videos.feed_field_data_tags}}</option>
								<option value="content_source" {{if $field_name=='content_source'}}selected{{/if}}>{{$lang.videos.feed_field_data_content_source}}</option>
								<option value="content_source_url" {{if $field_name=='content_source_url'}}selected{{/if}}>{{$lang.videos.feed_field_data_content_source_url}}</option>
								<option value="content_source_group" {{if $field_name=='content_source_group'}}selected{{/if}}>{{$lang.videos.feed_field_data_content_source_group}}</option>
								<option value="dvd" {{if $field_name=='dvd'}}selected{{/if}}>{{$lang.videos.feed_field_data_dvd}}</option>
								<option value="dvd_group" {{if $field_name=='dvd_group'}}selected{{/if}}>{{$lang.videos.feed_field_data_dvd_group}}</option>
							</optgroup>
							<optgroup label="{{$lang.videos.feed_field_data_group_content}}">
								<option value="duration" {{if $field_name=='duration'}}selected{{/if}}>{{$lang.videos.feed_field_data_duration}}</option>
								<option value="video_file" {{if $field_name=='video_file'}}selected{{/if}}>{{$lang.videos.feed_field_data_video_file}}</option>
								<option value="website_link" {{if $field_name=='website_link'}}selected{{/if}}>{{$lang.videos.feed_field_data_website_link}}</option>
								<option value="embed_code" {{if $field_name=='embed_code'}}selected{{/if}}>{{$lang.videos.feed_field_data_embed_code}}</option>
							</optgroup>
							<optgroup label="{{$lang.videos.feed_field_data_group_custom}}">
								<option value="custom1" {{if $field_name=='custom1'}}selected{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
								<option value="custom2" {{if $field_name=='custom2'}}selected{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
								<option value="custom3" {{if $field_name=='custom3'}}selected{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
							</optgroup>
							<optgroup label="{{$lang.videos.feed_field_data_group_screenshots}}">
								<option value="screenshot_main_source" {{if $field_name=='screenshot_main_source'}}selected{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_source}}</option>
								<option value="overview_screenshots_sources" {{if $field_name=='overview_screenshots_sources'}}selected{{/if}}>{{$lang.videos.feed_field_data_screenshots_overview_sources}}</option>
								<option value="screen_main" {{if $field_name=='screen_main'}}selected{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_number}}</option>
							</optgroup>
							<optgroup label="{{$lang.videos.feed_field_data_group_posters}}">
								<option value="posters_sources" {{if $field_name=='posters_sources'}}selected{{/if}}>{{$lang.videos.feed_field_data_posters_sources}}</option>
								<option value="poster_main" {{if $field_name=='poster_main'}}selected{{/if}}>{{$lang.videos.feed_field_data_poster_main_number}}</option>
							</optgroup>
							{{if count($list_languages)>0}}
								<optgroup label="{{$lang.videos.feed_field_data_group_localization}}">
									{{foreach item="item" from=$list_languages|smarty:nodefaults}}
										<option value="title_{{$item.code}}" {{if $field_name=="title_`$item.code`"}}selected{{/if}}>{{$lang.videos.feed_field_data_title}} ({{$item.title}})</option>
										<option value="description_{{$item.code}}" {{if $field_name=="description_`$item.code`"}}selected{{/if}}>{{$lang.videos.feed_field_data_description}} ({{$item.title}})</option>
										<option value="dir_{{$item.code}}" {{if $field_name=="dir_`$item.code`"}}selected{{/if}}>{{$lang.videos.feed_field_data_directory}} ({{$item.title}})</option>
									{{/foreach}}
								</optgroup>
							{{/if}}
						</select>
					</td>
				</tr>
			{{/foreach}}
			<tr class="feed_type_id_csv">
				<td></td>
				<td class="de_control">{{$lang.videos.feed_field_data_field_more}}</td>
			</tr>
			<tr class="feed_type_id_csv">
				<td class="de_label de_required">{{$lang.videos.feed_field_data_key_field}}</td>
				<td class="de_control">
					<select name="key_field">
						<option value="">{{$lang.common.select_default_option}}</option>
						<option value="external_key_field" {{if $smarty.post.key_field=='external_key_field'}}selected{{/if}}>{{$lang.videos.feed_field_data_external_key}}</option>
						<option value="id" {{if $smarty.post.key_field=='id'}}selected{{/if}}>{{$lang.videos.feed_field_data_id}}</option>
						<option value="title" {{if $smarty.post.key_field=='title'}}selected{{/if}}>{{$lang.videos.feed_field_data_title}}</option>
						<option value="description" {{if $smarty.post.key_field=='description'}}selected{{/if}}>{{$lang.videos.feed_field_data_description}}</option>
						<option value="dir" {{if $smarty.post.key_field=='dir'}}selected{{/if}}>{{$lang.videos.feed_field_data_directory}}</option>
						<option value="video_file" {{if $smarty.post.key_field=='video_file'}}selected{{/if}}>{{$lang.videos.feed_field_data_video_file}}</option>
						<option value="website_link" {{if $smarty.post.key_field=='website_link'}}selected{{/if}}>{{$lang.videos.feed_field_data_website_link}}</option>
						<option value="embed_code" {{if $smarty.post.key_field=='embed_code'}}selected{{/if}}>{{$lang.videos.feed_field_data_embed_code}}</option>
						<option value="screenshot_main_source" {{if $smarty.post.key_field=='screenshot_main_source'}}selected{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_source}}</option>
						<option value="overview_screenshots_sources" {{if $smarty.post.key_field=='overview_screenshots_sources'}}selected{{/if}}>{{$lang.videos.feed_field_data_screenshots_overview_sources}}</option>
						<option value="custom1" {{if $smarty.post.key_field=='custom1'}}selected{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
						<option value="custom2" {{if $smarty.post.key_field=='custom2'}}selected{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
						<option value="custom3" {{if $smarty.post.key_field=='custom3'}}selected{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_data_key_field_hint}}</span>
				</td>
			</tr>
			<tr class="feed_type_id_kvs">
				<td class="de_label">{{$lang.videos.feed_field_data_fields}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="description" {{if in_array('description', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_description}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="dir" {{if in_array('dir', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_directory}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="rating" {{if in_array('rating', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_rating}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="votes" {{if in_array('votes', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_rating_votes}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="popularity" {{if in_array('popularity', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_visits}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="release_year" {{if in_array('release_year', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_release_year}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="post_date" {{if in_array('post_date', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_post_date}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="user" {{if in_array('user', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_user}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="tags" {{if in_array('tags', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_tags}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="categories" {{if in_array('categories', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_categories}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="models" {{if in_array('models', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_models}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="content_source" {{if in_array('content_source', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_content_source}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="content_source_group" {{if in_array('content_source_group', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_content_source_group}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="dvd" {{if in_array('dvd', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_dvd}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="dvd_group" {{if in_array('dvd_group', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_dvd_group}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="posters" {{if in_array('posters', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_group_posters}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="customization" {{if in_array('customization', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_group_custom}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="kvs_fields[]" value="localization" {{if in_array('localization', $smarty.post.kvs_fields) || in_array('all', $smarty.post.kvs_fields)}}checked{{/if}}/><label>{{$lang.videos.feed_field_data_group_localization}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_filters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_limit_duration}}</td>
				<td class="de_control">
					<span>
						{{$lang.videos.feed_field_limit_duration_from}}:
						<input type="text" name="limit_duration_from" maxlength="10" size="5" value="{{if $smarty.post.limit_duration_from>0}}{{$smarty.post.limit_duration_from}}{{/if}}"/>
					</span>
					<span>
						{{$lang.videos.feed_field_limit_duration_to}}:
						<input type="text" name="limit_duration_to" maxlength="10" size="5" value="{{if $smarty.post.limit_duration_to>0}}{{$smarty.post.limit_duration_to}}{{/if}}"/>
					</span>
					<span class="de_hint">{{$lang.videos.feed_field_limit_duration_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_limit_rating}}</td>
				<td class="de_control">
					<span>
						{{$lang.videos.feed_field_limit_rating_from}}:
						<input type="text" name="limit_rating_from" maxlength="10" size="5" value="{{if $smarty.post.limit_rating_from>0}}{{$smarty.post.limit_rating_from}}{{/if}}"/>
					</span>
					<span>
						{{$lang.videos.feed_field_limit_rating_to}}:
						<input type="text" name="limit_rating_to" maxlength="10" size="5" value="{{if $smarty.post.limit_rating_to>0}}{{$smarty.post.limit_rating_to}}{{/if}}"/>
					</span>
					<span class="de_hint">{{$lang.videos.feed_field_limit_rating_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_limit_views}}</td>
				<td class="de_control">
					<span>
						{{$lang.videos.feed_field_limit_views_from}}:
						<input type="text" name="limit_views_from" maxlength="10" size="5" value="{{if $smarty.post.limit_views_from>0}}{{$smarty.post.limit_views_from}}{{/if}}"/>
					</span>
					<span>
						{{$lang.videos.feed_field_limit_views_to}}:
						<input type="text" name="limit_views_to" maxlength="10" size="5" value="{{if $smarty.post.limit_views_to>0}}{{$smarty.post.limit_views_to}}{{/if}}"/>
					</span>
					<span class="de_hint">{{$lang.videos.feed_field_limit_views_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_limit_terminology}}</td>
				<td class="de_control">
					<textarea name="limit_terminology" cols="30" rows="3">{{$smarty.post.limit_terminology}}</textarea>
					<span class="de_hint">{{$lang.videos.feed_field_limit_terminology_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.videos.feed_divider_videos}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/17-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different/">What video types are supported in KVS and how they are different</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/56-different-ways-to-upload-video-files-into-kvs/">Different ways to upload video files into KVS</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_users}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_autocreate_users" value="1" {{if $smarty.post.is_autocreate_users==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_users_autogenerate}}</label></span>
					<span class="de_hint">{{$lang.videos.feed_field_users_autogenerate_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_categorization}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_categorization_categories}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_categorization_models}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_categorization_content_sources}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_skip_new_dvds" value="1" {{if $smarty.post.is_skip_new_dvds==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_categorization_dvds}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="is_categories_as_tags" value="1" {{if $smarty.post.is_categories_as_tags==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_categorization_categories_as_tags}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_limit_title}}</td>
				<td class="de_control">
					<input type="text" name="title_limit" value="{{$smarty.post.title_limit}}" maxlength="10" size="5"/>
					<select name="title_limit_type_id">
						<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected{{/if}}>{{$lang.videos.feed_field_limit_title_words}}</option>
						<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected{{/if}}>{{$lang.videos.feed_field_limit_title_characters}}</option>
					</select>
					<span class="de_hint">{{$lang.videos.import_field_limit_title_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_status}}</td>
				<td class="de_control">
					<select name="videos_status_id">
						<option value="0" {{if $smarty.post.videos_status_id==0}}selected{{/if}}>{{$lang.videos.feed_field_videos_status_disabled}}</option>
						<option value="1" {{if $smarty.post.videos_status_id==1}}selected{{/if}}>{{$lang.videos.feed_field_videos_status_active}}</option>
					</select>
					<span class="de_lv_pair"><input type="checkbox" name="videos_is_review_needed" value="1" {{if $smarty.post.videos_is_review_needed==1}}checked{{/if}}/><label>{{$lang.videos.feed_field_videos_need_review}}</label></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_admin_flag}}</td>
				<td class="de_control">
					<select name="videos_admin_flag_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$list_flags_admins|smarty:nodefaults}}
							<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.videos_admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
					<span class="de_hint">{{$lang.videos.feed_field_videos_admin_flag_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_type}}</td>
				<td class="de_control">
					<select name="videos_is_private">
						<option value="0" {{if $smarty.post.videos_is_private==0}}selected{{/if}}>{{$lang.videos.feed_field_videos_type_public}}</option>
						<option value="1" {{if $smarty.post.videos_is_private==1}}selected{{/if}}>{{$lang.videos.feed_field_videos_type_private}}</option>
						<option value="2" {{if $smarty.post.videos_is_private==2}}selected{{/if}}>{{$lang.videos.feed_field_videos_type_premium}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_content_source}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=content_sources</span>
							{{if in_array('content_sources|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<input type="text" name="videos_content_source" maxlength="255" value="{{$smarty.post.videos_content_source.title}}"/>
					</div>
					<span class="de_hint">{{$lang.videos.feed_field_videos_content_source_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_dvd}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvds</span>
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<input type="text" name="videos_dvd" maxlength="255" value="{{$smarty.post.videos_dvd.title}}"/>
					</div>
					<span class="de_hint">{{$lang.videos.feed_field_videos_dvd_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_videos_mode}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="videos_adding_mode_id">
						<option value="1" {{if $smarty.post.videos_adding_mode_id==1}}selected{{/if}}>{{$lang.videos.feed_field_videos_mode_embed}}</option>
						<option value="2" {{if $smarty.post.videos_adding_mode_id==2}}selected{{/if}}>{{$lang.videos.feed_field_videos_mode_pseudo_video}}</option>
						<option value="3" {{if $smarty.post.videos_adding_mode_id==3}}selected{{/if}}>{{$lang.videos.feed_field_videos_mode_hotlink}}</option>
						<option value="4" {{if $smarty.post.videos_adding_mode_id==4}}selected{{/if}}>{{$lang.videos.feed_field_videos_mode_download}}</option>
						<option value="6" {{if $smarty.post.videos_adding_mode_id==6}}selected{{/if}}>{{$lang.videos.feed_field_videos_mode_grabbers}}</option>
					</select>
					<span class="de_hint videos_adding_mode_id_1">{{$lang.videos.feed_field_videos_mode_embed_hint}}</span>
					<span class="de_hint videos_adding_mode_id_2">{{$lang.videos.feed_field_videos_mode_pseudo_video_hint}}</span>
					<span class="de_hint videos_adding_mode_id_3">{{$lang.videos.feed_field_videos_mode_hotlink_hint}}</span>
					<span class="de_hint videos_adding_mode_id_4">{{$lang.videos.feed_field_videos_mode_download_hint}}</span>
					<span class="de_hint videos_adding_mode_id_6">{{$lang.videos.feed_field_videos_mode_grabbers_hint}}</span>
				</td>
			</tr>
			<tr class="videos_adding_mode_id_4">
				<td class="de_label de_dependent">{{$lang.videos.feed_field_format}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="format_video_id">
						<option value="0">{{$lang.videos.feed_field_format_source}}</option>
						<option value="9999999" {{if $smarty.post.format_video_id==9999999}}selected{{/if}}>{{$lang.videos.feed_field_format_multiple}}</option>
						{{foreach item="group" from=$list_formats_videos_groups|smarty:nodefaults}}
							<optgroup label="{{$group.title}}">
								{{foreach item="item" from=$group.formats|smarty:nodefaults}}
									<option value="{{$item.format_video_id}}" {{if $smarty.post.format_video_id==$item.format_video_id}}selected{{/if}}>{{$lang.videos.feed_field_format_format|replace:"%1%":$item.title}}</option>
								{{/foreach}}
							</optgroup>
						{{/foreach}}
					</select>
					{{if count($list_formats_videos_groups)>1}}
						<span class="format_video_id_0">
							{{$lang.videos.feed_field_format_source_group}}:
							<select name="format_video_group_id">
								<option value="0">{{$lang.videos.feed_field_format_source_group_auto}}</option>
								{{foreach from=$list_formats_videos_groups item="group"}}
									<option value="{{$group.format_video_group_id}}" {{if $group.format_video_group_id==$smarty.post.format_video_group_id}}selected{{/if}}>{{$group.title}}</option>
								{{/foreach}}
							</select>
						</span>
					{{/if}}
					<span class="de_hint">{{$lang.videos.feed_field_format_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_screenshots_mode}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="screenshots_mode_id">
						<option value="1" {{if $smarty.post.screenshots_mode_id==1}}selected{{/if}}>{{$lang.videos.feed_field_screenshots_mode_feed}}</option>
						<option value="2" {{if $smarty.post.screenshots_mode_id==2}}selected{{/if}}>{{$lang.videos.feed_field_screenshots_mode_create}}</option>
						<option value="3" {{if $smarty.post.screenshots_mode_id==3}}selected{{/if}}>{{$lang.videos.feed_field_screenshots_mode_feed_main}}</option>
					</select>
					<span class="de_hint screenshots_mode_id_1">{{$lang.videos.feed_field_screenshots_mode_feed_hint}}</span>
					<span class="de_hint screenshots_mode_id_2">{{$lang.videos.feed_field_screenshots_mode_create_hint}}</span>
					<span class="de_hint screenshots_mode_id_3">{{$lang.videos.feed_field_screenshots_mode_feed_main_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_post_date_mode}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="post_date_mode_id">
						<option value="1" {{if $smarty.post.post_date_mode_id==1}}selected{{/if}}>{{$lang.videos.feed_field_post_date_mode_current}}</option>
						<option value="2" {{if $smarty.post.post_date_mode_id==2}}selected{{/if}}>{{$lang.videos.feed_field_post_date_mode_feed}}</option>
						<option value="3" {{if $smarty.post.post_date_mode_id==3}}selected{{/if}}>{{$lang.videos.feed_field_post_date_mode_uniform}}</option>
						<option value="4" {{if $smarty.post.post_date_mode_id==4}}selected{{/if}}>{{$lang.videos.feed_field_post_date_mode_random}}</option>
					</select>
					<span class="de_hint post_date_mode_id_1">{{$lang.videos.feed_field_post_date_mode_current_hint}}</span>
					<span class="de_hint post_date_mode_id_2">{{$lang.videos.feed_field_post_date_mode_feed_hint}}</span>
					<span class="de_hint post_date_mode_id_3">{{$lang.videos.feed_field_post_date_mode_uniform_hint}}</span>
					<span class="de_hint post_date_mode_id_4">{{$lang.videos.feed_field_post_date_mode_random_hint}}</span>
				</td>
			</tr>
			<tr class="post_date_mode_id_3">
				<td class="de_label de_dependent de_required">{{$lang.videos.feed_field_future_interval}}</td>
				<td class="de_control">
					<input type="text" name="end_date_offset" maxlength="10" size="5" value="{{if $smarty.post.end_date_offset>0}}{{$smarty.post.end_date_offset}}{{/if}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_future_interval_hint}}</span>
				</td>
			</tr>
			<tr class="post_date_mode_id_4">
				<td class="de_label de_dependent de_required">{{$lang.videos.feed_field_date_interval}}</td>
				<td class="de_control">
					<span>
						{{$lang.videos.feed_field_date_interval_from}}:
						<span class="calendar">
							<input type="text" name="start_date_interval" value="{{$smarty.post.start_date_interval}}" placeholder="{{$lang.common.select_default_option}}">
						</span>
					</span>
					<span>
						{{$lang.videos.feed_field_date_interval_to}}:
						<span class="calendar">
							<input type="text" name="end_date_interval" value="{{$smarty.post.end_date_interval}}" placeholder="{{$lang.common.select_default_option}}">
						</span>
					</span>
				</td>
			</tr>
			<tr class="post_date_mode_id_3 post_date_mode_id_4">
				<td class="de_label de_dependent post_date_mode_id_3">{{$lang.videos.feed_field_max_videos_per_day}}</td>
				<td class="de_control">
					<input type="text" name="max_videos_per_day" maxlength="10" size="5" value="{{if $smarty.post.max_videos_per_day>0}}{{$smarty.post.max_videos_per_day}}{{/if}}"/>
					<span class="de_hint">{{$lang.videos.feed_field_max_videos_per_day_hint}}</span>
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

{{assign var="can_delete" value=1}}
{{assign var="can_edit" value=1}}
{{assign var="can_add" value=1}}
{{assign var="can_execute" value=1}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
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
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
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
										{{if $item.status_id==1}}
											<span class="js_param">activate_hide=true</span>
										{{else}}
											<span class="js_param">deactivate_hide=true</span>
										{{/if}}
										{{if $item.is_running==1}}
											<span class="js_param">run_disable=true</span>
										{{else}}
											<span class="js_param">stop_hide=true</span>
										{{/if}}
										{{if $item.is_debug_enabled==1}}
											<span class="js_param">enable_debug_hide=true</span>
											<span class="js_param">log_type=2</span>
										{{else}}
											<span class="js_param">disable_debug_hide=true</span>
											<span class="js_param">log_type=0</span>
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
						<span class="js_param">hide=${deactivate_hide}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
				{{/if}}
				{{if $can_add==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=duplicate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.videos.feed_action_duplicate}}</span>
						<span class="js_param">confirm={{$lang.videos.feed_action_duplicate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-copy</span>
					</li>
				{{/if}}
				{{if $can_execute==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=execute&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.videos.feed_action_run_feed}}</span>
						<span class="js_param">disable=${run_disable}</span>
						<span class="js_param">icon=action-pump</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=stop&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.videos.feed_action_stop_feed}}</span>
						<span class="js_param">confirm={{$lang.videos.feed_action_stop_feed_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">hide=${stop_hide}</span>
						<span class="js_param">icon=action-ban</span>
					</li>
				{{/if}}
				{{if $can_edit==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=enable_debug&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
						<span class="js_param">hide=${enable_debug_hide}</span>
						<span class="js_param">icon=action-log</span>
						<span class="js_param">subicon=action-add</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=disable_debug&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
						<span class="js_param">hide=${disable_debug_hide}</span>
						<span class="js_param">icon=action-log</span>
						<span class="js_param">subicon=action-delete</span>
					</li>
				{{/if}}
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_feeds.php?no_filter=true&amp;se_feed_id=${id}</span>
						<span class="js_param">title={{$lang.videos.feed_action_view_log}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=action-log</span>
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
					{{/if}}
					{{if $can_execute==1}}
						<option value="execute">{{$lang.videos.feed_batch_action_run_selected|replace:"%1%":'${count}'}}</option>
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
			</ul>
		</div>
	</form>
</div>

{{/if}}