{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a>
				/
				{{if $smarty.post.grabber_info.grabber_id!=''}}
					<a href="{{$page_name}}?plugin_id=grabbers">{{$lang.plugins.grabbers.title}}</a>
					/
					{{$smarty.post.grabber_info.grabber_name}}
				{{elseif $smarty.get.action=='upload' || $smarty.get.action=='upload_confirm' || $smarty.get.action=='back_upload'}}
					<a href="{{$page_name}}?plugin_id=grabbers">{{$lang.plugins.grabbers.title}}</a>
					/
					{{$lang.plugins.grabbers.upload}}
				{{else}}
					{{$lang.plugins.grabbers.title}}
				{{/if}}
				&nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]
			</h1>
		</div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.grabbers.long_desc}}
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
			{{if $smarty.post.grabber_info.grabber_id!=''}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_grabber_settings|replace:"%1%":$smarty.post.grabber_info.grabber_name}}</h2></td>
				</tr>
				{{if $smarty.post.grabber_info.is_default==1}}
					<tr>
						<td class="de_simple_text" colspan="2">
							<span class="de_hint">{{$lang.plugins.grabbers.divider_grabber_settings_default}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/17-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different/">What video types are supported in KVS and how they are different</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/56-different-ways-to-upload-video-files-into-kvs/">Different ways to upload video files into KVS</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.grabbers.field_mode}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="mode">
							{{if $smarty.post.grabber_info.settings.mode==''}}
								<option value=""></option>
							{{/if}}
							{{foreach from=$smarty.post.grabber_info.supported_modes item="mode"}}
								{{assign var="mode_title_key" value="field_mode_`$mode`"}}
								<option value="{{$mode}}" {{if $smarty.post.grabber_info.settings.mode==$mode}}selected{{/if}}>{{$lang.plugins.grabbers[$mode_title_key]}}</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.plugins.grabbers.field_mode_hint|replace:"%1%":$lang.plugins.grabbers.field_mode_download|replace:"%2%":$lang.plugins.grabbers.field_mode_embed|replace:"%3%":$lang.plugins.grabbers.field_mode_pseudo}}</span>
					</td>
				</tr>
				{{if $smarty.post.grabber_info.grabber_type=='videos'}}
					<tr class="mode_embed mode_pseudo">
						<td class="de_label">{{$lang.plugins.grabbers.field_url_postfix}}</td>
						<td class="de_control">
							<input type="text" name="url_postfix" value="{{$smarty.post.grabber_info.settings.url_postfix}}"/>
							<span class="de_hint">{{$lang.plugins.grabbers.field_url_postfix_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_data}}</td>
					<td class="de_control">
						<table class="control_group">
							{{foreach from=$smarty.post.grabber_info.supported_data item="data"}}
								<tr>
									{{assign var="data_title_key" value="field_data_`$data`"}}
									<td><span class="de_lv_pair"><input type="checkbox" name="data[]" value="{{$data}}" {{if in_array($data,$smarty.post.grabber_info.settings.data)}}checked{{/if}}/><label>{{$lang.plugins.grabbers[$data_title_key]}}</label></span></td>
								</tr>
							{{/foreach}}
						</table>
						<span class="de_hint">{{$lang.plugins.grabbers.field_data_hint}}</span>
					</td>
				</tr>
				{{if in_array('categories',$smarty.post.grabber_info.supported_data)}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_import_categories_as_tags}}</td>
						<td class="de_control">
							<span class="de_lv_pair"><input type="checkbox" name="is_import_categories_as_tags" value="1" {{if $smarty.post.grabber_info.settings.is_import_categories_as_tags==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_import_categories_as_tags_enabled}}</label></span>
							<span class="de_hint">{{$lang.plugins.grabbers.field_import_categories_as_tags_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_autogenerate_users}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_autocreate_users" value="1" {{if $smarty.post.grabber_info.settings.is_autocreate_users==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_autogenerate_users_enabled}}</label></span>
						<span class="de_hint">{{$lang.plugins.grabbers.field_autogenerate_users_hint}}</span>
					</td>
				</tr>
				{{if $smarty.post.grabber_info.grabber_type!='models'}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_content_source}}</td>
						<td class="de_control">
							<div class="insight">
								<div class="js_params">
									<span class="js_param">url=async/insight.php?type=content_sources</span>
								</div>
								<input type="text" name="content_source" maxlength="255" value="{{$smarty.post.grabber_info.settings.content_source}}"/>
							</div>
							<span class="de_hint">{{$lang.plugins.grabbers.field_content_source_hint}}</span>
						</td>
					</tr>
				{{/if}}
				{{if $smarty.post.grabber_info.grabber_type=='videos'}}
					<tr class="mode_download">
						<td class="de_label">{{$lang.plugins.grabbers.field_quality}}</td>
						<td class="de_control de_vis_sw_select">
							{{assign var="quality_vis_selector" value="quality_"}}
							<select name="quality">
								<option value="">{{$lang.plugins.grabbers.field_quality_none}}</option>
								{{if count($smarty.post.grabber_info.supported_qualities)>1}}
									{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
										{{assign var="quality_vis_selector" value="`$quality_vis_selector` quality_`$item`"}}
										<option value="{{$item}}" {{if $smarty.post.grabber_info.settings.quality==$item}}selected{{/if}}>{{$item}}</option>
									{{/foreach}}
								{{/if}}
								{{if is_array($smarty.post.grabber_info.supported_video_formats) && count($smarty.post.grabber_info.supported_qualities)>1}}
									<option value="*" {{if $smarty.post.grabber_info.settings.quality=='*'}}selected{{/if}}>{{$lang.plugins.grabbers.field_quality_multiple}}</option>
								{{/if}}
							</select>
							<span class="{{$quality_vis_selector}}">
								{{$lang.plugins.grabbers.field_quality_missing}}:
								<select name="quality_missing" class="{{$quality_vis_selector}}">
									<option value="error" {{if $smarty.post.grabber_info.settings.quality_missing=='error'}}selected{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_error}}</option>
									<option value="lower" {{if $smarty.post.grabber_info.settings.quality_missing=='lower'}}selected{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_lower}}</option>
									<option value="higher" {{if $smarty.post.grabber_info.settings.quality_missing=='higher'}}selected{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_higher}}</option>
								</select>
							</span>
							{{if is_array($smarty.post.grabber_info.supported_video_formats)>0}}
								<span class="{{$quality_vis_selector}} de_vis_sw_select">
									{{$lang.plugins.grabbers.field_download_format}}:
									<select name="download_format" class="{{$quality_vis_selector}}">
										<option value="">{{$lang.plugins.grabbers.field_download_format_source}}</option>
										{{foreach item="group" from=$smarty.post.grabber_info.supported_video_formats}}
											<optgroup label="{{$group.title}}">
												{{foreach item="item" from=$group.formats}}
													<option value="{{$item.postfix}}" {{if $smarty.post.grabber_info.settings.download_format==$item.postfix}}selected{{/if}}>{{$lang.plugins.grabbers.field_download_format_format|replace:"%1%":$item.title}}</option>
												{{/foreach}}
											</optgroup>
										{{/foreach}}
									</select>
								</span>
								{{if count($smarty.post.grabber_info.supported_video_formats)>1}}
									<span class="{{$quality_vis_selector}} download_format_">
										{{$lang.plugins.grabbers.field_download_format_group}}:
										<select name="download_format_source_group_id">
											<option value="0">{{$lang.plugins.grabbers.field_download_format_group_auto}}</option>
											{{foreach from=$smarty.post.grabber_info.supported_video_formats item="group"}}
												<option value="{{$group.format_video_group_id}}" {{if $group.format_video_group_id==$smarty.post.grabber_info.settings.download_format_source_group_id}}selected{{/if}}>{{$group.title}}</option>
											{{/foreach}}
										</select>
									</span>
								{{/if}}
							{{/if}}
							{{if is_array($smarty.post.grabber_info.supported_video_formats)>0 && count($smarty.post.grabber_info.supported_qualities)>1}}
								{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
									<span class="quality_*">
										{{$item}}:
										<select name="download_format_{{$item}}" class="quality_*">
											<option value="">{{$lang.plugins.grabbers.field_download_format_skip}}</option>
											{{foreach item="group" from=$smarty.post.grabber_info.supported_video_formats}}
												<optgroup label="{{$group.title}}">
													{{foreach item="item2" from=$group.formats}}
														<option value="{{$item2.postfix}}" {{if $smarty.post.grabber_info.settings.download_formats_mapping[$item]==$item2.postfix}}selected{{/if}}>{{$item2.title}}</option>
													{{/foreach}}
												</optgroup>
											{{/foreach}}
										</select>
									</span>
								{{/foreach}}
							{{/if}}
							<span class="de_hint">{{$lang.plugins.grabbers.field_quality_hint}}</span>
						</td>
					</tr>
					<tr class="mode_download {{$quality_vis_selector}} download_format_">
						<td class="de_label">{{$lang.plugins.grabbers.field_truncate_duration}}</td>
						<td class="de_control">
							<span>
								{{$lang.plugins.grabbers.field_truncate_duration_start}}:
								<input type="text" name="offset_from_start" size="4" value="{{if $smarty.post.grabber_info.settings.offset_from_start>0}}{{$smarty.post.grabber_info.settings.offset_from_start}}{{/if}}"/>
								{{$lang.common.second_truncated}}
							</span>
							<span>
								{{$lang.plugins.grabbers.field_truncate_duration_end}}:
								<input type="text" name="offset_from_end" size="4" value="{{if $smarty.post.grabber_info.settings.offset_from_end>0}}{{$smarty.post.grabber_info.settings.offset_from_end}}{{/if}}"/>
								{{$lang.common.second_truncated}}
							</span>
							<span class="de_hint">{{$lang.plugins.grabbers.field_truncate_duration_hint}}</span>
						</td>
					</tr>
				{{/if}}
				{{if $smarty.post.grabber_info.grabber_type!='models'}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_replacements}}</td>
						<td class="de_control">
							<textarea name="replacements" rows="3">{{$smarty.post.grabber_info.settings.replacements}}</textarea>
							<span class="de_hint">{{$lang.plugins.grabbers.field_replacements_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_timeout}}</td>
					<td class="de_control">
						<input type="text" name="timeout" size="4" value="{{$smarty.post.grabber_info.settings.timeout}}"/>
						<span class="de_hint">{{$lang.plugins.grabbers.field_timeout_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_customize_user_agent}}</td>
					<td class="de_control">
						<input type="text" name="customize_user_agent" value="{{$smarty.post.grabber_info.settings.customize_user_agent}}"/>
						<span class="de_hint">{{$lang.plugins.grabbers.field_customize_user_agent_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_proxies}}</td>
					<td class="de_control">
						<textarea name="proxies" rows="3">{{$smarty.post.grabber_info.settings.proxies}}</textarea>
						<span class="de_hint">{{$lang.plugins.grabbers.field_proxies_hint}}</span>
					</td>
				</tr>
				{{if $smarty.post.grabber_info.is_ydl==1}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_account}}</td>
						<td class="de_control">
							<input type="text" name="account" value="{{$smarty.post.grabber_info.settings.account}}"/>
							<span class="de_hint">{{$lang.plugins.grabbers.field_account_hint}}</span>
						</td>
					</tr>
				{{/if}}
				{{if $smarty.post.grabber_info.is_autodelete_supported==1}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_autodelete}}</td>
						<td class="de_control">
							<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_autodelete" value="1" {{if $smarty.post.grabber_info.settings.is_autodelete=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_autodelete_enabled}}</label></span>
							<span class="de_hint">{{$lang.plugins.grabbers.field_autodelete_hint}}</span>
						</td>
					</tr>
					<tr class="is_autodelete_on">
						<td class="de_label de_dependent">{{$lang.plugins.grabbers.field_autodelete_mode}}</td>
						<td class="de_control de_vis_sw_select">
							<select name="autodelete_mode">
								<option value="0" {{if $smarty.post.grabber_info.settings.autodelete_mode=='0'}}selected{{/if}}>{{$lang.plugins.grabbers.field_autodelete_mode_delete}}</option>
								<option value="1" {{if $smarty.post.grabber_info.settings.autodelete_mode=='1'}}selected{{/if}}>{{$lang.plugins.grabbers.field_autodelete_mode_mark}}</option>
							</select>
							<span class="de_hint">{{$lang.plugins.grabbers.field_autodelete_mode_hint}}</span>
						</td>
					</tr>
					<tr class="is_autodelete_on autodelete_mode_1">
						<td class="de_label de_dependent">{{$lang.plugins.grabbers.field_autodelete_reason}}</td>
						<td class="de_control">
							<textarea name="autodelete_reason" cols="30" rows="3">{{$smarty.post.grabber_info.settings.autodelete_reason}}</textarea>
							<span class="de_hint">{{$lang.plugins.grabbers.field_autodelete_reason_hint}}</span>
						</td>
					</tr>
					<tr class="is_autodelete_on">
						<td class="de_label de_dependent">{{$lang.plugins.grabbers.field_autodelete_last_exec}}</td>
						<td class="de_control">
							<span>
								{{if $smarty.post.grabber_info.settings.autodelete_last_exec_time==0}}
									{{$lang.common.undefined}}
								{{else}}
									{{$smarty.post.grabber_info.settings.autodelete_last_exec_time|date_format:$smarty.session.userdata.full_date_format}}
									(<a href="?plugin_id=grabbers&amp;action=log&amp;grabber_id={{$smarty.post.grabber_info.grabber_id}}" rel="log">{{$lang.plugins.grabbers.field_autodelete_last_exec_info|replace:"%1%":$smarty.post.grabber_info.settings.autodelete_last_exec_duration|replace:"%2%":$smarty.post.grabber_info.settings.autodelete_last_exec_deleted}}</a>)
								{{/if}}
							</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_import_deleted_content}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_import_deleted_content" value="1" {{if $smarty.post.grabber_info.settings.is_import_deleted_content=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_import_deleted_content_enabled}}</label></span>
						<span class="de_hint">{{$lang.plugins.grabbers.field_import_deleted_content_hint}}</span>
					</td>
				</tr>
				{{if $smarty.post.grabber_info.grabber_type!='models'}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_filters}}</h2></td>
					</tr>
					{{if $smarty.post.grabber_info.grabber_type=='videos'}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_quantity_filter_videos}}</td>
							<td class="de_control">
								<span>
									{{$lang.plugins.grabbers.field_quantity_filter_from}}:
									<input type="text" name="filter_quantity_from" value="{{if $smarty.post.grabber_info.settings.filter_quantity_from!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_from}}{{/if}}" size="4"/>
								</span>
								<span>
									{{$lang.plugins.grabbers.field_quantity_filter_to}}:
									<input type="text" name="filter_quantity_to" value="{{if $smarty.post.grabber_info.settings.filter_quantity_to!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_to}}{{/if}}" size="4"/>
								</span>
								<span class="de_hint">{{$lang.plugins.grabbers.field_quantity_filter_videos_hint}}</span>
							</td>
						</tr>
					{{elseif $smarty.post.grabber_info.grabber_type=='albums'}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_quantity_filter_albums}}</td>
							<td class="de_control">
								<span>
									{{$lang.plugins.grabbers.field_quantity_filter_from}}:
									<input type="text" name="filter_quantity_from" value="{{if $smarty.post.grabber_info.settings.filter_quantity_from!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_from}}{{/if}}" size="4"/>
								</span>
								<span>
									{{$lang.plugins.grabbers.field_quantity_filter_to}}:
									<input type="text" name="filter_quantity_to" value="{{if $smarty.post.grabber_info.settings.filter_quantity_to!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_to}}{{/if}}" size="4"/>
								</span>
								<span class="de_hint">{{$lang.plugins.grabbers.field_quantity_filter_albums_hint}}</span>
							</td>
						</tr>
					{{/if}}
					{{if in_array('rating',$smarty.post.grabber_info.supported_data)}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_rating_filter}}</td>
							<td class="de_control">
								<span>
									{{$lang.plugins.grabbers.field_rating_filter_from}}:
									<input type="text" name="filter_rating_from" value="{{if $smarty.post.grabber_info.settings.filter_rating_from!='0'}}{{$smarty.post.grabber_info.settings.filter_rating_from}}{{/if}}" size="4"/>
								</span>
								<span>
									{{$lang.plugins.grabbers.field_rating_filter_to}}:
									<input type="text" name="filter_rating_to" value="{{if $smarty.post.grabber_info.settings.filter_rating_to!='0'}}{{$smarty.post.grabber_info.settings.filter_rating_to}}{{/if}}" size="4"/>
								</span>
								<span class="de_hint">{{$lang.plugins.grabbers.field_rating_filter_hint}}</span>
							</td>
						</tr>
					{{/if}}
					{{if in_array('views',$smarty.post.grabber_info.supported_data)}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_views_filter}}</td>
							<td class="de_control">
								<span>
									{{$lang.plugins.grabbers.field_views_filter_from}}:
									<input type="text" name="filter_views_from" value="{{if $smarty.post.grabber_info.settings.filter_views_from!='0'}}{{$smarty.post.grabber_info.settings.filter_views_from}}{{/if}}" size="4"/>
								</span>
								<span>
									{{$lang.plugins.grabbers.field_views_filter_to}}:
									<input type="text" name="filter_views_to" value="{{if $smarty.post.grabber_info.settings.filter_views_to!='0'}}{{$smarty.post.grabber_info.settings.filter_views_to}}{{/if}}" size="4"/>
								</span>
								<span class="de_hint">{{$lang.plugins.grabbers.field_views_filter_hint}}</span>
							</td>
						</tr>
					{{/if}}
					{{if in_array('date',$smarty.post.grabber_info.supported_data)}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_date_filter}}</td>
							<td class="de_control">
								<span>
									{{$lang.plugins.grabbers.field_date_filter_from}}:
									<input type="text" name="filter_date_from" value="{{if $smarty.post.grabber_info.settings.filter_date_from!='0'}}{{$smarty.post.grabber_info.settings.filter_date_from}}{{/if}}" size="4"/>
								</span>
								<span>
									{{$lang.plugins.grabbers.field_date_filter_to}}:
									<input type="text" name="filter_date_to" value="{{if $smarty.post.grabber_info.settings.filter_date_to!='0'}}{{$smarty.post.grabber_info.settings.filter_date_to}}{{/if}}" size="4"/>
								</span>
								<span class="de_hint">{{$lang.plugins.grabbers.field_date_filter_hint}}</span>
							</td>
						</tr>
					{{/if}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_terminology_filter}}</td>
						<td class="de_control">
							<textarea name="filter_terminology" cols="30" rows="3">{{$smarty.post.grabber_info.settings.filter_terminology}}</textarea>
							<span class="de_hint">{{$lang.plugins.grabbers.field_terminology_filter_hint}}</span>
						</td>
					</tr>
					{{if $smarty.post.grabber_info.grabber_type=='videos' && count($smarty.post.grabber_info.supported_qualities)>1}}
						<tr>
							<td class="de_label">{{$lang.plugins.grabbers.field_quality_from_filter}}</td>
							<td class="de_control">
								<select name="filter_quality_from">
									<option value="">{{$lang.common.select_default_option}}</option>
									{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
										<option value="{{$item}}" {{if $smarty.post.grabber_info.settings.filter_quality_from==$item}}selected{{/if}}>{{$item}}</option>
									{{/foreach}}
								</select>
								<span class="de_hint">{{$lang.plugins.grabbers.field_quality_from_filter_hint}}</span>
							</td>
						</tr>
					{{/if}}
				{{/if}}
				{{if $smarty.post.grabber_info.is_autopilot_supported==1}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_autopilot}}</h2></td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_autopilot}}</td>
						<td class="de_control">
							<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_autopilot" value="1" {{if $smarty.post.grabber_info.settings.is_autopilot=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_autopilot_enabled}}</label></span>
							<span class="de_hint">{{$lang.plugins.grabbers.field_autopilot_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label de_required">{{$lang.plugins.grabbers.field_autopilot_interval}}</td>
						<td class="de_control">
							<input type="text" name="autopilot_interval" value="{{$smarty.post.grabber_info.settings.autopilot_interval}}" maxlength="100" size="20"/>
							<span class="de_hint">{{$lang.plugins.grabbers.field_autopilot_interval_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_threads}}</td>
						<td class="de_control">
							<select name="threads">
								{{section name="threads" start="1" loop="21"}}
									<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.grabber_info.settings.threads}}selected{{/if}}>{{$smarty.section.threads.iteration}}</option>
								{{/section}}
							</select>
							<span class="de_hint">{{$lang.plugins.grabbers.field_threads_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_limit_title}}</td>
						<td class="de_control">
							<input type="text" name="title_limit" value="{{if $smarty.post.grabber_info.settings.title_limit>0}}{{$smarty.post.grabber_info.settings.title_limit}}{{/if}}" maxlength="10" size="4"/>
							<select name="title_limit_type_id">
								<option value="1" {{if $smarty.post.grabber_info.settings.title_limit_type_id=="1"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_title_words}}</option>
								<option value="2" {{if $smarty.post.grabber_info.settings.title_limit_type_id=="2"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_title_characters}}</option>
							</select>
							<span class="de_hint">{{$lang.plugins.grabbers.field_limit_title_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_limit_description}}</td>
						<td class="de_control">
							<input type="text" name="description_limit" value="{{if $smarty.post.grabber_info.settings.description_limit>0}}{{$smarty.post.grabber_info.settings.description_limit}}{{/if}}" maxlength="10" size="4"/>
							<select name="description_limit_type_id">
								<option value="1" {{if $smarty.post.grabber_info.settings.description_limit_type_id=="1"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_description_words}}</option>
								<option value="2" {{if $smarty.post.grabber_info.settings.description_limit_type_id=="2"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_description_characters}}</option>
							</select>
							<span class="de_hint">{{$lang.plugins.grabbers.field_limit_description_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_status_after_import}}</td>
						<td class="de_control">
							<select name="status_after_import_id">
								<option value="0" {{if $smarty.post.grabber_info.settings.status_after_import_id=='0'}}selected{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_active}}</option>
								<option value="1" {{if $smarty.post.grabber_info.settings.status_after_import_id=='1'}}selected{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_disabled}}</option>
							</select>
							<span class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.grabber_info.settings.is_review_needed==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_need_review}}</label></span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_admin_flag}}</td>
						<td class="de_control">
							<select name="admin_flag_id">
								<option value="">{{$lang.common.select_default_option}}</option>
								{{if $smarty.post.grabber_info.grabber_type=='videos'}}
									{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_videos|smarty:nodefaults}}
										<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.grabber_info.settings.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								{{elseif $smarty.post.grabber_info.grabber_type=='albums'}}
									{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_albums|smarty:nodefaults}}
										<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.grabber_info.settings.admin_flag_id}}selected{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								{{/if}}
							</select>
							<span class="de_hint">{{$lang.plugins.grabbers.field_admin_flag_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_options_categorization}}</td>
						<td class="de_control">
							<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_categories=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_categories}}</label></span>
							<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_models=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_models}}</label></span>
							<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_content_sources=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_cs}}</label></span>
							{{if $smarty.post.grabber_info.grabber_type=='videos'}}
								<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_channels" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_channels=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_channels}}</label></span>
							{{/if}}
							<span class="de_hint">{{$lang.plugins.grabbers.field_options_categorization_hint}}</span>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label">{{$lang.plugins.grabbers.field_options_other}}</td>
						<td class="de_control">
							<table class="control_group">
								<tr>
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.grabber_info.settings.is_skip_duplicate_titles=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_duplicates}}</label></span>
										<span class="de_hint">{{$lang.plugins.grabbers.field_options_other_duplicates_hint}}</span>
									</td>
								</tr>
								<tr>
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_randomize_time" value="1" {{if $smarty.post.grabber_info.settings.is_randomize_time==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_randomize_time}}</label></span>
										<span class="de_hint">{{$lang.plugins.grabbers.field_options_other_randomize_time_hint}}</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="is_autopilot_on">
						<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_list}}</td>
						<td class="de_control">
							<textarea name="upload_list" rows="5" cols="30">{{$smarty.post.grabber_info.settings.upload_list}}</textarea>
							<span class="de_hint">{{$lang.plugins.grabbers.field_upload_list_hint_autopilot}}</span>
						</td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_last_exec}}</td>
						<td class="de_control">
							<span>
								{{if $smarty.post.grabber_info.settings.autopilot_last_exec_time==0}}
									{{$lang.plugins.grabbers.field_last_exec_none}}
								{{else}}
									{{$smarty.post.grabber_info.settings.autopilot_last_exec_time|date_format:$smarty.session.userdata.full_date_format}}
									(<a href="?plugin_id=grabbers&amp;action=log&amp;grabber_id={{$smarty.post.grabber_info.grabber_id}}" rel="log">{{$lang.plugins.grabbers.field_last_exec_info|replace:"%1%":$smarty.post.grabber_info.settings.autopilot_last_exec_duration|replace:"%2%":$smarty.post.grabber_info.settings.autopilot_last_exec_added|replace:"%3%":$smarty.post.grabber_info.settings.autopilot_last_exec_duplicates}}</a>)
								{{/if}}
							</span>
						</td>
					</tr>
				{{/if}}
			{{elseif $smarty.get.action=='upload' || $smarty.get.action=='back_upload'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_upload}}</h2></td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_type}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="upload_type">
							<option value="videos" {{if $smarty.post.upload_type=='videos'}}selected{{/if}}>{{$lang.plugins.grabbers.field_upload_type_videos}}</option>
							{{if $config.installation_type>=4}}
								<option value="albums" {{if $smarty.post.upload_type=='albums'}}selected{{/if}}>{{$lang.plugins.grabbers.field_upload_type_albums}}</option>
							{{/if}}
						</select>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_list}}</td>
					<td class="de_control">
						<textarea name="upload_list" rows="5" cols="30">{{$smarty.post.upload_list}}</textarea>
						<span class="de_hint">{{$lang.plugins.grabbers.field_upload_list_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_upload_log}}</td>
					<td class="de_control">
						<span>
							<a href="?plugin_id=grabbers&amp;action=get_import_log" rel="log">import.txt</a>
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_upload_options}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_threads}}</td>
					<td class="de_control">
						<select name="threads">
							{{section name="threads" start="1" loop="21"}}
								<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.threads}}selected{{/if}}>{{$smarty.section.threads.iteration}}</option>
							{{/section}}
						</select>
						<span class="de_hint">{{$lang.plugins.grabbers.field_threads_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_limit_title}}</td>
					<td class="de_control">
						<input type="text" name="title_limit" value="{{if $smarty.post.title_limit>0}}{{$smarty.post.title_limit}}{{/if}}" maxlength="10" size="4"/>
						<select name="title_limit_type_id">
							<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_title_words}}</option>
							<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_title_characters}}</option>
						</select>
						<span class="de_hint">{{$lang.plugins.grabbers.field_limit_title_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_limit_description}}</td>
					<td class="de_control">
						<input type="text" name="description_limit" value="{{if $smarty.post.description_limit>0}}{{$smarty.post.description_limit}}{{/if}}" maxlength="10" size="4"/>
						<select name="description_limit_type_id">
							<option value="1" {{if $smarty.post.description_limit_type_id=="1"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_description_words}}</option>
							<option value="2" {{if $smarty.post.description_limit_type_id=="2"}}selected{{/if}}>{{$lang.plugins.grabbers.field_limit_description_characters}}</option>
						</select>
						<span class="de_hint">{{$lang.plugins.grabbers.field_limit_description_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_status_after_import}}</td>
					<td class="de_control">
						<select name="status_after_import_id">
							<option value="0" {{if $smarty.post.status_after_import_id=='0'}}selected{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_active}}</option>
							<option value="1" {{if $smarty.post.status_after_import_id=='1'}}selected{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_disabled}}</option>
						</select>
						<span class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.is_review_needed==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_need_review}}</label></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_admin_flag}}</td>
					<td class="de_control">
						<span class="upload_type_videos">
							<select name="admin_flag_id_videos">
								<option value="">{{$lang.common.select_default_option}}</option>
								{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_videos|smarty:nodefaults}}
									<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id_videos}}selected{{/if}}>{{$item.title}}</option>
								{{/foreach}}
							</select>
						</span>
						<span class="upload_type_albums">
							<select name="admin_flag_id_albums">
								<option value="">{{$lang.common.select_default_option}}</option>
								{{foreach name="data" item="item" from=$smarty.post.list_admin_flags_albums|smarty:nodefaults}}
									<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id_albums}}selected{{/if}}>{{$item.title}}</option>
								{{/foreach}}
							</select>
						</span>
						<span class="de_hint">{{$lang.plugins.grabbers.field_admin_flag_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_options_categorization}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_categories}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_models}}</label></span>
						<span class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_cs}}</label></span>
						<span class="de_lv_pair upload_type_videos"><input type="checkbox" name="is_skip_new_channels" value="1" {{if $smarty.post.is_skip_new_channels=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_channels}}</label></span>
						<span class="de_hint">{{$lang.plugins.grabbers.field_options_categorization_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_options_other}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles=='1'}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_duplicates}}</label></span>
									<span class="de_hint">{{$lang.plugins.grabbers.field_options_other_duplicates_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="is_randomize_time" value="1" {{if $smarty.post.is_randomize_time==1}}checked{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_randomize_time}}</label></span>
									<span class="de_hint">{{$lang.plugins.grabbers.field_options_other_randomize_time_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{{elseif $smarty.get.action=='upload_confirm'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_upload_confirm}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.plugins.grabbers.field_name}}</td>
								<td>{{$lang.plugins.grabbers.field_mode}}</td>
								<td>
									{{if $smarty.post.upload_type=='videos'}}
										{{$lang.plugins.grabbers.field_videos_amount}}
									{{elseif $smarty.post.upload_type=='albums'}}
										{{$lang.plugins.grabbers.field_albums_amount}}
									{{/if}}
								</td>
							</tr>
							{{assign var="total_download" value="0"}}
							{{assign var="total_embed" value="0"}}
							{{assign var="total_pseudo" value="0"}}
							{{foreach item="item" key="key" from=$smarty.post.grabbers_usage|smarty:nodefaults}}
								{{assign var="expander_id" value="expander_`$key`"}}
								<tr class="eg_data">
									<td>
										{{if $item.type=='valid'}}
											{{$item.name}}
											{{assign var="total_inc" value=$item.urls|@count}}
											{{if $item.mode=='download'}}
												{{assign var="total_download" value=$total_download+$total_inc}}
											{{elseif $item.mode=='embed'}}
												{{assign var="total_embed" value=$total_embed+$total_inc}}
											{{elseif $item.mode=='pseudo'}}
												{{assign var="total_pseudo" value=$total_pseudo+$total_inc}}
											{{/if}}
										{{elseif $item.type=='missing'}}
											{{$lang.plugins.grabbers.field_name_missing_grabber}}
										{{elseif $item.type=='error'}}
											{{$lang.plugins.grabbers.field_name_error_grabber}}
										{{elseif $item.type=='duplicates'}}
											{{$lang.plugins.grabbers.field_name_duplicates}}
										{{/if}}
									</td>
									<td>
										{{if $item.mode=='' || $item.name==''}}
											{{$lang.plugins.grabbers.field_mode_skip}}
										{{else}}
											{{assign var="mode_title_key" value="field_mode_`$item.mode`"}}
											{{$lang.plugins.grabbers[$mode_title_key]}}
										{{/if}}
									</td>
									<td>
										<span data-accordeon="{{$expander_id}}">{{$item.urls|@count}}</span>
									</td>
								</tr>
								{{if count($item.urls)>0}}
									<tr class="eg_data {{$expander_id}} hidden">
										<td colspan="3" class="eg_padding">
											{{foreach item="url" from=$item.urls|smarty:nodefaults}}
												{{$url}}<br/>
											{{/foreach}}
										</td>
									</tr>
								{{/if}}
								{{if count($item.errors)>0}}
									<tr class="eg_data {{$expander_id}} hidden">
										<td colspan="3" class="eg_padding">
											{{foreach item="errors" from=$item.errors|smarty:nodefaults}}
												{{$errors}}<br/>
											{{/foreach}}
										</td>
									</tr>
								{{/if}}
							{{/foreach}}
							<tr class="eg_header">
								<td rowspan="3">{{$lang.plugins.grabbers.field_total}}</td>
								<td>{{$lang.plugins.grabbers.field_mode_download}}</td>
								<td>
									{{$total_download}}
								</td>
							</tr>
							<tr class="eg_header">
								<td>{{$lang.plugins.grabbers.field_mode_embed}}</td>
								<td>
									{{$total_embed}}
								</td>
							</tr>
							<tr class="eg_header">
								<td>{{$lang.plugins.grabbers.field_mode_pseudo}}</td>
								<td>
									{{$total_pseudo}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{{else}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/57-6-ways-to-add-videos-into-kvs/">6 ways to add videos into KVS</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_upload}}</h2></td>
				</tr>
				<tr>
					<td class="de_control" colspan="2">
						<span><a href="plugins.php?plugin_id=grabbers&amp;action=upload">{{$lang.plugins.grabbers.field_upload}}</a></span>
						<span class="de_hint">{{$lang.plugins.grabbers.field_upload_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_grabbers}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_ydl_path}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.ydl_path!=='' && $smarty.post.ydl_version!==''}}✔️{{else}}❌{{/if}}
							{{$smarty.post.ydl_path|default:$lang.common.undefined}}
						</span>
						{{if $smarty.post.ydl_path!==''}}
							<span>
								({{$lang.plugins.grabbers.field_version}}: {{$smarty.post.ydl_version|default:$lang.common.undefined}})
							</span>
						{{/if}}
						{{if $smarty.post.ydl_output!==''}}
							<br/><br/>&gt;&gt; {{$smarty.post.ydl_output}}<br/><br/>
						{{/if}}
						<span class="de_hint">{{$lang.plugins.grabbers.field_ydl_path_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_last_exec}}</td>
					<td class="de_control">
						<span>
							{{if !$smarty.post.last_exec_date || $smarty.post.last_exec_date==0}}
								{{$lang.plugins.grabbers.field_last_exec_none}}
							{{else}}
								{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
								{{if $smarty.post.is_running!=1}}
									(<a href="{{$page_name}}?plugin_id=grabbers&amp;action=get_log" rel="log">{{$lang.plugins.grabbers.field_last_exec_data|replace:"%1%":$smarty.post.duration}}</a>)
								{{else}}
									({{$lang.plugins.grabbers.field_last_exec_data|replace:"%1%":$smarty.post.duration}})
								{{/if}}
							{{/if}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_next_exec}}</td>
					<td class="de_control">
						<span>
							{{if $smarty.post.is_running==1}}
								<a href="{{$page_name}}?plugin_id=grabbers&amp;action=get_log" rel="log">{{$lang.plugins.grabbers.field_next_exec_running}}</a>
							{{else}}
								{{if !$smarty.post.next_exec_date || $smarty.post.next_exec_date==0}}
									{{$lang.plugins.grabbers.field_next_exec_none}}
								{{else}}
									{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
								{{/if}}
							{{/if}}
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
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td class="eg_selector"><input type="checkbox"/> <label>{{$lang.plugins.grabbers.field_delete}}</label></td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_name}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_version}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_mode}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_data}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_filters}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_quality}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_autopilot}}</td>
								<td class="nowrap">{{$lang.plugins.grabbers.field_autodelete}}</td>
							</tr>
							{{foreach item="grabbers" key="grabber_type" from=$smarty.post.grabbers|smarty:nodefaults}}
								<tr class="eg_header">
									{{assign var="grabber_type_title_key" value="divider_grabbers_`$grabber_type`"}}
									<td colspan="9">{{$lang.plugins.grabbers.$grabber_type_title_key}}</td>
								</tr>
								{{foreach item="item" from=$grabbers|smarty:nodefaults}}
									<tr class="eg_data">
										<td class="eg_selector"><input type="checkbox" name="delete[]" value="{{$item.grabber_id}}"/></td>
										<td><a href="?plugin_id=grabbers&amp;grabber_id={{$item.grabber_id}}" class="nowrap {{if $item.settings.mode=='' || $item.settings.is_broken=='1' || $item.is_broken=='1'}}highlighted_text{{/if}}">{{if $item.settings.is_autopilot==1}}<b>{{$item.grabber_name}}</b>{{else}}{{$item.grabber_name}}{{/if}}</a></td>
										<td>
											{{$item.grabber_version}}
										</td>
										<td {{if $item.settings.mode==''}}class="highlighted_text"{{/if}}>
											{{if $item.settings.mode==''}}
												{{$lang.plugins.grabbers.field_mode_none}}
											{{else}}
												{{assign var="mode_title_key" value="field_mode_`$item.settings.mode`"}}
												{{$lang.plugins.grabbers[$mode_title_key]}}
											{{/if}}
										</td>
										<td>
											{{if count($item.settings.data)>0}}
												{{foreach name="data" item="item_data" from=$item.settings.data|smarty:nodefaults}}
													{{assign var="data_title_key" value="field_data_`$item_data`"}}
													{{$lang.plugins.grabbers[$data_title_key]}}{{if !$smarty.foreach.data.last}},{{/if}}
												{{/foreach}}
											{{else}}
												{{$lang.plugins.grabbers.field_data_none}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{assign var="needs_break" value="false"}}
											{{if $item.settings.filter_quantity_from>0 && $item.settings.filter_quantity_to>0}}
												{{$item.settings.filter_quantity_from}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}} - {{$item.settings.filter_quantity_to}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_quantity_from>0}}
												{{$item.settings.filter_quantity_from}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_quantity_to>0}}
												{{$item.settings.filter_quantity_to}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_rating_from>0 && $item.settings.filter_rating_to>0}}
												{{$item.settings.filter_rating_from}}% - {{$item.settings.filter_rating_to}}%
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_rating_from>0}}
												{{$item.settings.filter_rating_from}}%+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_rating_to>0}}
												{{$item.settings.filter_rating_to}}%-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_views_from>0 && $item.settings.filter_views_to>0}}
												{{$item.settings.filter_views_from}} - {{$item.settings.filter_views_to}}
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_views_from>0}}
												{{$item.settings.filter_views_from}}+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_views_to>0}}
												{{$item.settings.filter_views_to}}-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_date_from>0 && $item.settings.filter_date_to>0}}
												{{$item.settings.filter_date_from}}d - {{$item.settings.filter_date_to}}d
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_date_from>0}}
												{{$item.settings.filter_date_from}}d+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_date_to>0}}
												{{$item.settings.filter_date_to}}d-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_quality_from}}
												{{$item.settings.filter_quality_from}}+
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.mode=='download' && $item.settings.quality!=''}}
												{{if $item.settings.quality=='*'}}
													{{foreach name="quality" item="item_quality" key="key_quality" from=$item.settings.download_formats_mapping}}
														{{$key_quality}}{{if !$smarty.foreach.quality.last}},{{/if}}
													{{/foreach}}
												{{else}}
													{{$item.settings.quality}}
													{{if $item.settings.quality_missing=='lower'}}-{{elseif $item.settings.quality_missing=='higher'}}+{{/if}}
												{{/if}}
											{{else}}
												{{$lang.plugins.grabbers.field_quality_none}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.is_autopilot=='1'}}
												{{$item.settings.autopilot_interval}}{{if is_numeric($item.settings.autopilot_interval) && $item.settings.autopilot_interval>0}}{{$lang.common.hour_truncated}}{{/if}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.is_autodelete=='1'}}
												{{$lang.common.term_yes}}
											{{/if}}
										</td>
									</tr>
								{{foreachelse}}
									<tr class="eg_data_text">
										<td colspan="9">
											{{$lang.plugins.grabbers.divider_grabbers_none}}
										</td>
									</tr>
								{{/foreach}}
							{{/foreach}}
						</table>
					</td>
				</tr>
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.grabbers.divider_install}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/24-how-to-create-custom-tube-video-grabber-for-kvs/">How to create custom tube video grabber for KVS</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_kvs_repository}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.plugins.grabbers.field_kvs_repository}}</span>
								<span class="js_param">url={{$page_name}}?plugin_id=grabbers&amp;action=grabbers_list</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=grabber_ids[]</span>
								<span class="js_param">empty_message={{$lang.plugins.grabbers.field_kvs_repository_empty}}</span>
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_grabber" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.plugins.grabbers.field_kvs_repository_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.plugins.grabbers.field_kvs_repository_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_custom_grabber}}</td>
					<td class="de_control">
						{{if $config.allow_custom_grabbers=='true'}}
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$lang.plugins.grabbers.field_custom_grabber}}</span>
									<span class="js_param">accept=php</span>
								</div>
								<input type="text" name="custom_grabber" maxlength="100"/>
								<input type="hidden" name="custom_grabber_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
							<span class="de_hint">{{$lang.plugins.grabbers.field_custom_grabber_hint}}</span>
						{{else}}
							<span>
								<span>{{$lang.plugins.grabbers.field_custom_grabber_security}}</span>
							</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		{{if $smarty.post.grabber_info.grabber_id!=''}}
			<input type="hidden" name="action" value="save_grabber"/>
			<input type="hidden" name="grabber_id" value="{{$smarty.post.grabber_info.grabber_id}}"/>
		{{elseif $smarty.get.action=='upload' || $smarty.get.action=='back_upload'}}
			<input type="hidden" name="action" value="mass_import"/>
		{{elseif $smarty.get.action=='upload_confirm'}}
			<input type="hidden" name="action" value="mass_import_confirm"/>
			<input type="hidden" name="task_id" value="{{$smarty.post.task_id}}"/>
			<input type="submit" name="back_mass_import" value="{{$lang.plugins.grabbers.btn_back}}"/>
		{{else}}
			<input type="hidden" name="action" value="manage_grabbers"/>
		{{/if}}
		<input type="submit" name="save_default" value="{{$lang.plugins.grabbers.btn_save}}"/>
	</div>
</form>