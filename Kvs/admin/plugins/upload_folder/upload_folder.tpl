{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / <a href="{{$page_name}}?plugin_id=upload_folder">{{$lang.plugins.upload_folder.title}}</a> /
				{{if $smarty.get.action=='confirm'}}
					{{$lang.plugins.upload_folder.divider_validation_results}}
				{{elseif $smarty.get.action=='complete'}}
					{{$lang.plugins.upload_folder.divider_import_results}}
				{{/if}}
				&nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]
			</h1>
		</div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.upload_folder.long_desc}}
				</td>
			</tr>
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
			{{if $smarty.get.action!='confirm' && $smarty.get.action!='complete'}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/57-6-ways-to-add-videos-into-kvs/">6 ways to add videos into KVS</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/56-different-ways-to-upload-video-files-into-kvs/">Different ways to upload video files into KVS</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_folder_standard_videos}}</td>
					<td class="de_control">
						<input type="text" name="folder_standard_videos" maxlength="400" value="{{$smarty.post.folder_standard_videos}}"/>
						<span class="de_hint">{{$lang.plugins.upload_folder.field_folder_standard_videos_hint}}</span>
					</td>
				</tr>
				{{if $config.installation_type==4}}
					<tr>
						<td class="de_label">{{$lang.plugins.upload_folder.field_folder_albums}}</td>
						<td class="de_control">
							<input type="text" name="folder_albums" maxlength="400" value="{{$smarty.post.folder_albums}}"/>
							<span class="de_hint">{{$lang.plugins.upload_folder.field_folder_albums_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_video_formats}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="video_formats">
							<option value="2" {{if $smarty.post.video_formats==2}}selected{{/if}}>{{$lang.plugins.upload_folder.field_video_formats_ignore}}</option>
							<option value="1" {{if $smarty.post.video_formats==1}}selected{{/if}}>{{$lang.plugins.upload_folder.field_video_formats_analyze}}</option>
						</select>
						{{if count($smarty.post.list_formats_videos_groups)>1}}
							<span class="video_formats_2">
								{{$lang.plugins.upload_folder.field_video_formats_group}}:
								<select name="format_video_group_id">
									<option value="0">{{$lang.plugins.upload_folder.field_video_formats_group_auto}}</option>
									{{foreach from=$smarty.post.list_formats_videos_groups item="group"}}
										<option value="{{$group.format_video_group_id}}" {{if $group.format_video_group_id==$smarty.post.format_video_group_id}}selected{{/if}}>{{$group.title}}</option>
									{{/foreach}}
								</select>
							</span>
						{{/if}}
						<span class="de_hint">{{$lang.plugins.upload_folder.field_video_formats_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_video_screenshots}}</td>
					<td class="de_control">
						<select name="video_screenshots">
							<option value="0" {{if $smarty.post.video_screenshots==0}}selected{{/if}}>{{$lang.plugins.upload_folder.field_video_screenshots_ignore}}</option>
							<option value="1" {{if $smarty.post.video_screenshots==1}}selected{{/if}}>{{$lang.plugins.upload_folder.field_video_screenshots_overview}}</option>
							<option value="2" {{if $smarty.post.video_screenshots==2}}selected{{/if}}>{{$lang.plugins.upload_folder.field_video_screenshots_posters}}</option>
						</select>
						<span class="de_hint">{{$lang.plugins.upload_folder.field_video_screenshots_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_filenames_encoding}}</td>
					<td class="de_control">
						<input type="text" name="charset" maxlength="400" value="{{$smarty.post.charset}}"/>
						<span class="de_hint">{{$lang.plugins.upload_folder.field_filenames_encoding_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_delete_files}}</td>
					<td class="de_control" colspan="2">
						<span class="de_lv_pair"><input type="checkbox" name="delete_files" value="1" {{if $smarty.post.delete_files==1}}checked{{/if}}/><label>{{$lang.plugins.upload_folder.field_delete_files_yes}}</label></span>
						<span class="de_hint">{{$lang.plugins.upload_folder.field_delete_files_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_randomize}}</td>
					<td class="de_control" colspan="2">
						<span class="de_lv_pair"><input type="checkbox" name="randomize" value="1" {{if $smarty.post.randomize==1}}checked{{/if}}/><label>{{$lang.plugins.upload_folder.field_randomize_yes}}</label></span>
						<span class="de_hint">{{$lang.plugins.upload_folder.field_randomize_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_content_status}}</td>
					<td class="de_control">
						<select name="content_status">
							<option value="0" {{if $smarty.post.content_status==0}}selected{{/if}}>{{$lang.plugins.upload_folder.field_content_status_disabled}}</option>
							<option value="1" {{if $smarty.post.content_status==1}}selected{{/if}}>{{$lang.plugins.upload_folder.field_content_status_active}}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_admin_flag}}</td>
					<td class="de_control">
						<select name="admin_flag_id_videos">
							<option value="">{{$lang.plugins.upload_folder.field_admin_flag_videos}}</option>
							{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_videos|smarty:nodefaults}}
								<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id_videos}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
						{{if $config.installation_type>=4}}
							<select name="admin_flag_id_albums">
								<option value="">{{$lang.plugins.upload_folder.field_admin_flag_albums}}</option>
								{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_albums|smarty:nodefaults}}
									<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id_albums}}selected{{/if}}>{{$item.title}}</option>
								{{/foreach}}
							</select>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='confirm'}}
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_analyze_result}}</td>
					<td class="de_control">
						<span>
							{{$lang.plugins.upload_folder.field_analyze_result_found_objects|replace:"%1%":$smarty.post.found_objects}},
							{{$lang.plugins.upload_folder.field_analyze_result_existing_objects|replace:"%1%":$smarty.post.existing_objects}},
							{{$lang.plugins.upload_folder.field_analyze_result_errors|replace:"%1%":$smarty.post.errors}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col class="eg_column_small"/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td class="eg_selector"><input type="checkbox" checked/> <label>{{$lang.plugins.upload_folder.dg_contents_col_import}}</label></td>
								<td>{{$lang.plugins.upload_folder.dg_contents_col_object_type}}</td>
								<td>{{$lang.plugins.upload_folder.dg_contents_col_file_name}}</td>
								<td>{{$lang.plugins.upload_folder.dg_contents_col_file_usage}}</td>
							</tr>
							{{if count($smarty.post.content)>0}}
								{{foreach item="item" key="key" from=$smarty.post.content|smarty:nodefaults}}
									<tr class="eg_data {{if $key % 2==0}}eg_even{{/if}}">
										<td class="eg_selector" rowspan="{{$item.files|@count}}"><input type="checkbox" name="import_items[]" value="{{$item.external_key}}" {{if $item.has_error==1}}disabled{{else}}checked{{/if}}/></td>
										<td rowspan="{{$item.files|@count}}" {{if $item.has_error==1}}class="highlighted_text"{{/if}}>
											{{if $item.type==1}}
												{{$lang.plugins.upload_folder.dg_contents_col_object_type_video}}
											{{elseif $item.type==3}}
												{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
											{{/if}}
											{{if $item.error!=''}}({{$lang.plugins.upload_folder[$item.error]}}){{/if}}
											{{if $item.folder_title!=''}}
												/ {{$item.folder_title}}
											{{/if}}
										</td>
										<td class="nowrap {{if $item.files[0].file_type==0}}disabled{{/if}} {{if count($item.files)>1}}eg_row_group_upper{{/if}}">{{$item.files[0].file_title}}</td>
										<td class="{{if $item.files[0].file_type==-1}}highlighted_text{{elseif $item.files[0].file_type==0}}disabled{{/if}} {{if count($item.files)>1}}eg_row_group_upper{{/if}}" {{if $item.files[0].error!=''}}{{assign var="error_key" value=$item.files[0].error}}title="{{$lang.plugins.upload_folder[$error_key]|replace:"%1%":$item.files[0].file_title}}"{{/if}}>
											{{if $item.files[0].file_type==-1}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_error|replace:"%1%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==0}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_ignored|replace:"%1%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==1}}
												{{assign var="format_video_group_title" value=$item.files[0].format_video_group.title|default:$lang.plugins.upload_folder.field_video_formats_group_auto}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_file|replace:"%1%":$item.files[0].file_duration|replace:"%2%":$item.files[0].file_size|replace:"%3%":$format_video_group_title}}
											{{elseif $item.files[0].file_type==2}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_format_file|replace:"%1%":$item.files[0].format_title|replace:"%2%":$item.files[0].file_duration|replace:"%3%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==3}}
												{{if $smarty.post.video_screenshots==2}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
												{{else}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
												{{/if}}
											{{elseif $item.files[0].file_type==4}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_main_screenshot|replace:"%1%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==5}}
												{{if $smarty.post.video_screenshots==2}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
												{{else}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
												{{/if}}
											{{elseif $item.files[0].file_type==6}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==7}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{elseif $item.files[0].file_type==8}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_description|replace:"%1%":$item.files[0].file_size}}
											{{/if}}
										</td>
									</tr>
									{{foreach item="item_file" key="key_file" from=$item.files|smarty:nodefaults}}
										{{if $key_file>0}}
											<tr class="eg_data {{if $key % 2==0}}eg_even{{/if}}">
												<td class="nowrap {{if $item_file.file_type==0}}disabled{{/if}} eg_row_group_lower {{if count($item.files)>1 && $key_file!=$item.files|@count-1}}eg_row_group_upper{{/if}}">{{$item_file.file_title}}</td>
												<td class="{{if $item_file.file_type==-1}}highlighted_text{{elseif $item_file.file_type==0}}disabled{{/if}} eg_row_group_lower {{if count($item.files)>1 && $key_file!=$item.files|@count-1}}eg_row_group_upper{{/if}}" {{if $item_file.error!=''}}title="{{$lang.plugins.upload_folder[$item_file.error]|replace:"%1%":$item_file.file_title}}"{{/if}}>
													{{if $item_file.file_type==-1}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_error|replace:"%1%":$item_file.file_size}}
													{{elseif $item_file.file_type==0}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_ignored|replace:"%1%":$item_file.file_size}}
													{{elseif $item_file.file_type==1}}
														{{assign var="format_video_group_title" value=$item_file.format_video_group.title|default:$lang.plugins.upload_folder.field_video_formats_group_auto}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_file|replace:"%1%":$item_file.file_duration|replace:"%2%":$item_file.file_size|replace:"%3%":$format_video_group_title}}
													{{elseif $item_file.file_type==2}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_format_file|replace:"%1%":$item_file.format_title|replace:"%2%":$item_file.file_duration|replace:"%3%":$item_file.file_size}}
													{{elseif $item_file.file_type==3}}
														{{if $smarty.post.video_screenshots==2}}
															{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
														{{else}}
															{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
														{{/if}}
													{{elseif $item_file.file_type==4}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_main_screenshot|replace:"%1%":$item_file.file_size}}
													{{elseif $item_file.file_type==5}}
														{{if $smarty.post.video_screenshots==2}}
															{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
														{{else}}
															{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
														{{/if}}
													{{elseif $item_file.file_type==6}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{elseif $item_file.file_type==7}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{elseif $item_file.file_type==8}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_description|replace:"%1%":$item_file.file_size}}
													{{/if}}
												</td>
											</tr>
										{{/if}}
									{{/foreach}}
								{{/foreach}}
							{{/if}}
							{{foreach item="item" key="key" from=$smarty.post.duplicates|smarty:nodefaults}}
								<tr class="eg_data">
									<td><input type="checkbox" disabled/></td>
									<td class="disabled">
										{{if $item.type==1}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_video}}
										{{elseif $item.type==3}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
										{{/if}}
									</td>
									<td class="disabled">{{$item.title}}</td>
									<td class="disabled">{{$lang.plugins.upload_folder.dg_contents_col_file_usage_duplicate|replace:"%1%":$item.duplicate_id}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='complete'}}
				<tr>
					<td class="de_table_control" colspan="2">
						{{if count($smarty.post.processed_content)>0}}
							<table class="de_edit_grid">
								<colgroup>
									<col/>
									<col/>
									<col/>
								</colgroup>
								<tr class="eg_header">
									<td>{{$lang.plugins.upload_folder.dg_contents_col_object_type}}</td>
									<td>{{$lang.plugins.upload_folder.dg_contents_col_object_id}}</td>
									<td>{{$lang.plugins.upload_folder.dg_contents_col_title}}</td>
								</tr>
								{{foreach item="item" key="key" from=$smarty.post.processed_content|smarty:nodefaults}}
									<tr class="eg_data {{if $key % 2==0}}eg_even{{/if}}">
										<td>
											{{if $item.type==1}}
												{{$lang.plugins.upload_folder.dg_contents_col_object_type_video}}
											{{elseif $item.type==3}}
												{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
											{{/if}}
										</td>
										<td>{{$item.item_id}}</td>
										<td>{{$item.title}}</td>
									</tr>
								{{/foreach}}
							</table>
						{{else}}
							{{$lang.plugins.upload_folder.divider_import_results_none}}
						{{/if}}
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="task_id" value="{{$smarty.request.task_id}}"/>
		{{if $smarty.get.action!='confirm' && $smarty.get.action!='complete'}}
			<input type="hidden" name="action" value="validate"/>
			<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_analyze}}"/>
		{{elseif $smarty.get.action=='confirm'}}
			<input type="hidden" name="action" value="import"/>
			<input type="submit" name="action_back" value="{{$lang.plugins.upload_folder.btn_back}}"/>
			<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_import}}" {{if count($smarty.post.content)==0}}disabled{{/if}}/>
		{{elseif $smarty.get.action=='complete'}}
			<input type="hidden" name="action" value="close"/>
			<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_close}}"/>
		{{/if}}
	</div>
</form>