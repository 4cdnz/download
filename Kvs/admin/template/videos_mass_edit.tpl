{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="video_mass_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="videos.php">{{$lang.videos.submenu_option_videos_list}}</a> / {{$lang.videos.mass_edit_videos_header}}</h1></div>
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
			<tr data-field-name="selected_items">
				<td class="de_label">{{$lang.videos.mass_edit_videos_field_selected_videos}}</td>
				<td class="de_control">
					{{if $videos_count_all==1}}
						<span class="highlighted_text"><b>{{$lang.videos.mass_edit_videos_field_selected_videos_all}} ({{$videos_count}})</b></span>
					{{else}}
						<span>{{$videos_count}}</span>
					{{/if}}
				</td>
			</tr>
			{{if in_array('videos|edit_dir',$smarty.session.permissions) || in_array('videos|edit_status',$smarty.session.permissions) || in_array('videos|edit_type',$smarty.session.permissions) ||
				in_array('videos|edit_access_level',$smarty.session.permissions) || in_array('videos|edit_tokens',$smarty.session.permissions) || in_array('videos|edit_release_year',$smarty.session.permissions) ||
				in_array('videos|edit_user',$smarty.session.permissions) || in_array('videos|edit_admin_user',$smarty.session.permissions) || in_array('videos|edit_content_source',$smarty.session.permissions) ||
				in_array('videos|edit_dvd',$smarty.session.permissions) || in_array('videos|edit_post_date',$smarty.session.permissions) || in_array('videos|edit_is_locked',$smarty.session.permissions) ||
				in_array('videos|edit_admin_flag',$smarty.session.permissions) || in_array('videos|edit_all',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_general}}</h2></td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_dir',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_directory}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="regenerate_directories" value="1" {{if $disallow_directory_change==1}}disabled{{/if}}/><label>{{$lang.videos.mass_edit_videos_field_directory_regenerate}}</label></span>
						{{if count($list_languages)>0}}
							<select name="regenerate_directories_language" class="regenerate_directories_on">
								<option value="">{{$lang.videos.mass_edit_videos_field_directory_default_language}}</option>
								{{foreach item="item" from=$list_languages|smarty:nodefaults}}
									{{if $item.is_directories_localize==1}}
										<option value="{{$item.code}}">{{$item.title}}</option>
									{{/if}}
								{{/foreach}}
							</select>
						{{/if}}
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_directory_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_status',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_status}}</td>
					<td class="de_control">
						<select name="status_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							<option value="1">{{$lang.videos.mass_edit_videos_field_status_active}}</option>
							<option value="0">{{$lang.videos.mass_edit_videos_field_status_disabled}}</option>
						</select>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_type',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_type}}</td>
					<td class="de_control">
						<select name="is_private">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							<option value="0">{{$lang.videos.mass_edit_videos_field_type_public}}</option>
							<option value="1">{{$lang.videos.mass_edit_videos_field_type_private}}</option>
							<option value="2">{{$lang.videos.mass_edit_videos_field_type_premium}}</option>
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_type_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=2}}
				{{if in_array('videos|edit_access_level',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.videos.mass_edit_videos_field_access_level}}</td>
						<td class="de_control">
							<select name="access_level_id">
								<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
								<option value="0">{{$lang.videos.mass_edit_videos_field_access_level_inherit}}</option>
								<option value="1">{{$lang.videos.mass_edit_videos_field_access_level_all}}</option>
								<option value="2">{{$lang.videos.mass_edit_videos_field_access_level_members}}</option>
								<option value="3">{{$lang.videos.mass_edit_videos_field_access_level_premium}}</option>
							</select>
						</td>
					</tr>
				{{/if}}
				{{if in_array('videos|edit_tokens',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.videos.mass_edit_videos_field_tokens_cost}}</td>
						<td class="de_control">
							<input type="text" name="tokens_required" size="10" maxlength="10"/>
							<span class="de_hint">{{$lang.videos.mass_edit_videos_field_tokens_cost_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if in_array('videos|edit_release_year',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_release_year}}</td>
					<td class="de_control">
						<input type="text" name="release_year" size="10" maxlength="10"/>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_user',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_users}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_insight_list">
										<div class="js_params">
											<span class="js_param">title={{$lang.videos.mass_edit_videos_field_users}}</span>
											<span class="js_param">url=async/insight.php?type=users</span>
											<span class="js_param">submit_mode=compound</span>
											<span class="js_param">submit_name=user_ids[]</span>
											<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_users_empty}}</span>
											{{if in_array('users|add',$smarty.session.permissions)}}
												<span class="js_param">allow_creation=true</span>
											{{/if}}
											{{if in_array('users|view',$smarty.session.permissions)}}
												<span class="js_param">allow_view=true</span>
											{{/if}}
										</div>
										<div class="list"></div>
										<div class="controls">
											<input type="text" name="new_user"/>
											<input type="button" class="add" value="{{$lang.common.add}}"/>
											<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_users_all}}"/>
										</div>
									</div>
								</td>
							</tr>
							{{if in_array('users|add',$smarty.session.permissions)}}
								<tr>
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_username_randomization" value="1"/><label>{{$lang.videos.mass_edit_videos_field_users_generate}}</label></span>
									</td>
								</tr>
							{{/if}}
						</table>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_users_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_admin_user',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_admins}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_admins}}</span>
								<span class="js_param">url=async/insight.php?type=admins</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=admin_user_ids[]</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_admins_empty}}</span>
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_admin"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_admins_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_admins_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_content_source',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_content_source}}</td>
					<td class="de_control">
						<span class="insight">
							<span class="js_params">
								<span class="js_param">url=async/insight.php?type=content_sources</span>
								{{if in_array('content_sources|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('content_sources|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</span>
							<input type="text" name="content_source" maxlength="255" class="content_source_set_empty_off"/>
						</span>
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="content_source_set_empty" value="1"/><label>{{$lang.videos.mass_edit_videos_field_content_source_reset}}</label></span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_dvd',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_dvd}}</td>
					<td class="de_control">
						<span class="insight">
							<span class="js_params">
								<span class="js_param">url=async/insight.php?type=dvds</span>
								{{if in_array('dvds|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('dvds|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</span>
							<input type="text" name="dvd" maxlength="255" class="dvd_set_empty_off"/>
						</span>
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="dvd_set_empty" value="1"/><label>{{$lang.videos.mass_edit_videos_field_dvd_reset}}</label></span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_post_date',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_post_date}}</td>
					<td class="de_control">
						{{if $config.relative_post_dates=='true'}}
							<table class="control_group de_vis_sw_radio">
								<tr>
									<td>
										<span class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" checked/><label>{{$lang.videos.mass_edit_videos_field_post_date_option_fixed}}</label></span>
										<span>
											{{$lang.videos.mass_edit_videos_field_post_date_from}}:
											<span class="calendar">
												<input type="text" name="post_date_from" class="post_date_option_fixed" placeholder="{{$lang.common.select_default_option}}">
											</span>
										</span>
										<span>
											{{$lang.videos.mass_edit_videos_field_post_date_to}}:
											<span class="calendar">
												<input type="text" name="post_date_to" class="post_date_option_fixed" placeholder="{{$lang.common.select_default_option}}">
											</span>
										</span>
										<span class="de_hint">{{$lang.videos.mass_edit_videos_field_post_date_hint}}</span>
									</td>
								</tr>
								<tr>
									<td>
										<span class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1"/><label>{{$lang.videos.mass_edit_videos_field_post_date_option_relative}}</label></span>
										<span>
											{{$lang.videos.mass_edit_videos_field_post_date_from}}:
											<input type="text" name="relative_post_date_from" class="post_date_option_relative" maxlength="5" size="10"/>
										</span>
										<span>
											{{$lang.videos.mass_edit_videos_field_post_date_to}}:
											<input type="text" name="relative_post_date_to" class="post_date_option_relative" maxlength="5" size="10"/>
										</span>
										<span class="de_hint">{{$lang.videos.mass_edit_videos_field_post_date_hint2}}</span>
									</td>
								</tr>
							</table>
						{{else}}
							<span>
								{{$lang.videos.mass_edit_videos_field_post_date_from}}:
								<span class="calendar">
									<input type="text" name="post_date_from" placeholder="{{$lang.common.select_default_option}}">
								</span>
							</span>
							<span>
								{{$lang.videos.mass_edit_videos_field_post_date_to}}:
								<span class="calendar">
									<input type="text" name="post_date_to" placeholder="{{$lang.common.select_default_option}}">
								</span>
							</span>
							<span class="de_hint">{{$lang.videos.mass_edit_videos_field_post_date_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_post_time}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="post_time_change" value="1"/><label>{{$lang.videos.mass_edit_videos_field_post_time_change}}</label></span>
						<span>
							{{$lang.videos.mass_edit_videos_field_post_time_from}}:
							<input type="text" name="post_time_from" maxlength="5" class="post_time_change_on" size="5" value="00:00"/>
						</span>
						<span>
							{{$lang.videos.mass_edit_videos_field_post_time_to}}:
							<input type="text" name="post_time_to" maxlength="5" class="post_time_change_on" size="5" value="23:59"/>
						</span>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_post_time_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_all',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_rating}}</td>
					<td class="de_control">
						<span>
							{{$lang.videos.mass_edit_videos_field_rating_min}}:
							<input type="text" name="rating_min" size="10"/>
						</span>
						<span>
							{{$lang.videos.mass_edit_videos_field_rating_max}}:
							<input type="text" name="rating_max" size="10"/>
						</span>
						<span>
							{{$lang.videos.mass_edit_videos_field_rating_votes_from}}:
							<input type="text" name="rating_amount_min" size="5" value="1"/>
						</span>
						<span>
							{{$lang.videos.mass_edit_videos_field_rating_votes_to}}:
							<input type="text" name="rating_amount_max" size="5" value="1"/>
						</span>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_rating_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_visits}}</td>
					<td class="de_control">
						<span>
							{{$lang.videos.mass_edit_videos_field_visits_min}}:
							<input type="text" name="visits_min" size="10"/>
						</span>
						<span>
							{{$lang.videos.mass_edit_videos_field_visits_max}}:
							<input type="text" name="visits_max" size="10"/>
						</span>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_visits_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_is_locked',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_lock_website}}</td>
					<td class="de_control">
						<select name="is_locked">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							<option value="1">{{$lang.videos.mass_edit_videos_field_lock_website_locked}}</option>
							<option value="0">{{$lang.videos.mass_edit_videos_field_lock_website_unlocked}}</option>
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_lock_website_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_status',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_review_flag}}</td>
					<td class="de_control">
						<select name="is_review_needed">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							<option value="1">{{$lang.videos.mass_edit_videos_field_review_flag_set}}</option>
							<option value="0">{{$lang.videos.mass_edit_videos_field_review_flag_unset}}</option>
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_review_flag_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_admin_flag',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_admin_flag}}</td>
					<td class="de_control">
						<select name="admin_flag_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							<option value="-1">{{$lang.videos.mass_edit_videos_field_admin_flag_reset}}</option>
							{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
								<option value="{{$item.flag_id}}">{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_categories',$smarty.session.permissions) || in_array('videos|edit_tags',$smarty.session.permissions) || in_array('videos|edit_models',$smarty.session.permissions) ||
				in_array('videos|edit_flags',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_categorization}}</h2></td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_tags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_tags_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_tags_add}}</span>
								<span class="js_param">url=async/insight.php?type=tags</span>
								<span class="js_param">submit_mode=simple</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_tags_empty}}</span>
								{{if in_array('tags|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('tags|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<input type="hidden" name="tags_add"/>
							<div class="controls">
								<input type="text" name="new_tag_add"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_tags_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_categories',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_categories_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_categories_add}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=category_ids_add[]</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_categories_empty}}</span>
								{{if in_array('categories|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('categories|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_category_add"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_categories_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_models',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_models_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_models_add}}</span>
								<span class="js_param">url=async/insight.php?type=models</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=model_ids_add[]</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_models_empty}}</span>
								{{if in_array('models|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('models|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_model_add"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_models_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_tags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_tags_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_tags_delete}}</span>
								<span class="js_param">url=async/insight.php?type=tags</span>
								<span class="js_param">submit_mode=simple</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_tags_empty}}</span>
								{{if in_array('tags|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<input type="hidden" name="tags_delete"/>
							<div class="controls">
								<input type="text" name="new_tag_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_tags_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_categories',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_categories_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_categories_delete}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=category_ids_delete[]</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_categories_empty}}</span>
								{{if in_array('categories|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_category_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_categories_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_models',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_models_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_models_delete}}</span>
								<span class="js_param">url=async/insight.php?type=models</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=model_ids_delete[]</span>
								<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_models_empty}}</span>
								{{if in_array('models|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_model_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_models_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_flags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_flags_reset}}</td>
					<td class="de_control">
						{{foreach item="item" from=$list_flags_videos|smarty:nodefaults}}
							<span class="de_lv_pair"><input type="checkbox" name="flag_ids_delete[]" value="{{$item.flag_id}}"/><label>{{$item.title}}</label></span>
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=2}}
				{{if in_array('playlists|edit_all',$smarty.session.permissions)}}
					<tr>
						<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_playlists}}</h2></td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.videos.mass_edit_videos_field_playlists_add}}</td>
						<td class="de_control">
							<div class="de_insight_list">
								<div class="js_params">
									<span class="js_param">title={{$lang.videos.mass_edit_videos_field_playlists_add}}</span>
									<span class="js_param">url=async/insight.php?type=playlists</span>
									<span class="js_param">submit_mode=compound</span>
									<span class="js_param">submit_name=playlist_ids_add[]</span>
									<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_playlists_empty}}</span>
									{{if in_array('playlists|view',$smarty.session.permissions)}}
										<span class="js_param">allow_view=true</span>
									{{/if}}
									{{if in_array('playlists|add',$smarty.session.permissions)}}
										<span class="js_param">allow_creation=true</span>
									{{/if}}
								</div>
								<div class="list"></div>
								<div class="controls">
									<input type="text" name="new_playlist_add"/>
									<input type="button" class="add" value="{{$lang.common.add}}"/>
									<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_playlists_all}}"/>
								</div>
							</div>
							<span class="de_hint">{{$lang.videos.mass_edit_videos_field_playlists_add_hint}}</span>
						</td>
					</tr>
					<tr>
						<td class="de_label">{{$lang.videos.mass_edit_videos_field_playlists_delete}}</td>
						<td class="de_control">
							<div class="de_insight_list">
								<div class="js_params">
									<span class="js_param">title={{$lang.videos.mass_edit_videos_field_playlists_delete}}</span>
									<span class="js_param">url=async/insight.php?type=playlists</span>
									<span class="js_param">submit_mode=compound</span>
									<span class="js_param">submit_name=playlist_ids_delete[]</span>
									<span class="js_param">empty_message={{$lang.videos.mass_edit_videos_field_playlists_empty}}</span>
									{{if in_array('playlists|view',$smarty.session.permissions)}}
										<span class="js_param">allow_view=true</span>
									{{/if}}
								</div>
								<div class="list"></div>
								<div class="controls">
									<input type="text" name="new_playlist_delete"/>
									<input type="button" class="add" value="{{$lang.common.add}}"/>
									<input type="button" class="all" value="{{$lang.videos.mass_edit_videos_field_playlists_all}}"/>
								</div>
							</div>
							<span class="de_hint">{{$lang.videos.mass_edit_videos_field_playlists_delete_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if in_array('videos|edit_video_files',$smarty.session.permissions) || in_array('videos|edit_storage',$smarty.session.permissions) || in_array('videos|manage_screenshots',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_content}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.videos.mass_edit_videos_divider_content_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_video_files',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_sources}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="delete_source_files" value="1"/><label>{{$lang.videos.mass_edit_videos_field_sources_delete}}</label></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_format_video_create}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									{{assign var="any_has_watermark" value="false"}}
									{{foreach item="item_group" key="group_id" from=$list_formats_videos_create|smarty:nodefaults}}
										{{if count($list_formats_videos_create)>1}}<span class="de_inline_group">{{$list_formats_videos_groups.$group_id}}:</span>{{/if}}
										{{foreach item="item" from=$item_group|smarty:nodefaults}}
											<span class="de_lv_pair">
												<input type="checkbox" name="video_format_create_ids[]" value="{{$item.postfix}}"/>
												<label>
													{{$item.title}}
													{{if $item.has_watermark==1}}
														{{assign var="any_has_watermark" value="true"}}
														({{$lang.videos.mass_edit_videos_field_format_video_create_watermarks_flag}})
													{{/if}}
												</label>
											</span>
										{{/foreach}}
									{{/foreach}}
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_format_video_create_hint}}</span>
								</td>
							</tr>
							{{if $any_has_watermark=='true'}}
								<tr>
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="video_format_create_disable_wm" value="1"/><label>{{$lang.videos.mass_edit_videos_field_format_video_create_watermarks}}</label></span>
										<span class="de_hint">{{$lang.videos.mass_edit_videos_field_format_video_create_watermarks_hint}}</span>
									</td>
								</tr>
							{{/if}}
						</table>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_format_video_delete}}</td>
					<td class="de_control">
						{{foreach item="item_group" key="group_id" from=$list_formats_videos_delete|smarty:nodefaults}}
							{{if count($list_formats_videos_create)>1}}<span class="de_inline_group">{{$list_formats_videos_groups.$group_id}}:</span>{{/if}}
							{{foreach item="item" from=$item_group|smarty:nodefaults}}
								<span class="de_lv_pair"><input type="checkbox" name="video_format_delete_ids[]" value="{{$item.postfix}}"/><label>{{$item.title}}</label></span>
							{{/foreach}}
						{{/foreach}}
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_format_video_delete_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_format_video_upload}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="video_format_upload_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							{{assign var="enable_upload_class" value=""}}
							{{foreach item="item_group" key="group_id" from=$list_formats_videos_create|smarty:nodefaults}}
								<optgroup label="{{$list_formats_videos_groups.$group_id}}">
									{{foreach item="item" from=$item_group|smarty:nodefaults}}
										<option value="{{$item.postfix}}">{{$item.title}}</option>
										{{assign var="enable_upload_class" value="`$enable_upload_class` video_format_upload_id_`$item.postfix`"}}
									{{/foreach}}
								</optgroup>
							{{/foreach}}
						</select>
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.mass_edit_videos_field_format_video_upload}}</span>
								<span class="js_param">accept={{$config.video_allowed_ext}}</span>
							</div>
							<input type="text" name="video_format_upload_file" class="{{$enable_upload_class}}" maxlength="100"/>
							<input type="hidden" name="video_format_upload_file_hash"/>
							<input type="button" class="de_fu_upload {{$enable_upload_class}}" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove {{$enable_upload_class}}" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_format_video_upload_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_duration_truncate}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="truncate_duration_enabled" value="1"/><label>{{$lang.videos.mass_edit_videos_field_duration_truncate_duration}}</label></span>
									<span>
										<input type="text" name="truncate_duration_value" class="truncate_duration_enabled_on" size="10"/>
										{{$lang.common.second_truncated}}
									</span>
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_duration_truncate_duration_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="truncate_offset_start_enabled" value="1"/><label>{{$lang.videos.mass_edit_videos_field_duration_truncate_offset_start}}</label></span>
									<span>
										<input type="text" name="truncate_duration_offset_start" class="truncate_offset_start_enabled_on" size="10"/>
										{{$lang.common.second_truncated}}
									</span>
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_duration_truncate_offset_start_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="truncate_offset_end_enabled" value="1"/><label>{{$lang.videos.mass_edit_videos_field_duration_truncate_offset_end}}</label></span>
									<span>
										<input type="text" name="truncate_duration_offset_end" class="truncate_offset_end_enabled_on" size="10"/>
										{{$lang.common.second_truncated}}
									</span>
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_duration_truncate_offset_end_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="hidden">
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_format_video_duration_update}}</td>
					<td class="de_control">
						<select name="video_format_duration_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							{{foreach item="item_group" key="group_id" from=$list_formats_videos_duration|smarty:nodefaults}}
								<optgroup label="{{$list_formats_videos_groups.$group_id}}">
									{{foreach item="item" from=$item_group|smarty:nodefaults}}
										<option value="{{$item.postfix}}">{{$item.title}}</option>
									{{/foreach}}
								</optgroup>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_format_video_duration_update_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_change_format_group}}</td>
					<td class="de_control">
						<select name="new_format_video_group_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							{{foreach item="item" key="key" from=$list_formats_videos_groups|smarty:nodefaults}}
								<option value="{{$key}}">{{$item}}</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_change_format_group_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_invalidate_cdn}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="invalidate_cdn" value="1"/><label>{{$lang.videos.mass_edit_videos_field_invalidate_cdn_invalidate}}</label></span>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_invalidate_cdn_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_storage',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_change_storage_group}}</td>
					<td class="de_control">
						<select name="new_storage_group_id">
							<option value="">{{$lang.videos.mass_edit_videos_do_not_change}}</option>
							{{foreach item="item" from=$list_server_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}">{{$item.title}} ({{$lang.videos.mass_edit_videos_field_change_storage_group_free|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
							{{/foreach}}
						</select>
						<span class="de_hint">{{$lang.videos.mass_edit_videos_field_change_storage_group_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|manage_screenshots',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_overview_screenshots}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="recreate_overview_screenshots" value="1"/><label>{{$lang.videos.mass_edit_videos_field_overview_screenshots_recreate}}</label></span>
									<span class="de_lv_pair recreate_overview_screenshots_on"><input type="checkbox" name="recreate_overview_screenshots_main" value="1"/><label>{{$lang.videos.mass_edit_videos_field_overview_screenshots_recreate_main}}</label></span>
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_overview_screenshots_recreate_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input type="checkbox" name="delete_overview_screenshots" value="1"/><label>{{$lang.videos.mass_edit_videos_field_overview_screenshots_delete}}</label></span>
									<span class="de_hint">{{$lang.videos.mass_edit_videos_field_overview_screenshots_delete_hint}}</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				{{if count($list_formats_videos_timelines)>0}}
					<tr>
						<td class="de_label">{{$lang.videos.mass_edit_videos_field_timeline_screenshots_recreate}}</td>
						<td class="de_control">
							{{foreach item="item_group" key="group_id" from=$list_formats_videos_timelines|smarty:nodefaults}}
								{{if count($list_formats_videos_timelines)>1}}<span class="de_inline_group">{{$list_formats_videos_groups.$group_id}}:</span>{{/if}}
								{{foreach item="item" from=$item_group|smarty:nodefaults}}
									<span class="de_lv_pair"><input type="checkbox" name="video_format_recreate_timelines_ids[]" value="{{$item.postfix}}"/><label>{{$item.title}}</label></span>
								{{/foreach}}
							{{/foreach}}
							<span class="de_hint">{{$lang.videos.mass_edit_videos_field_timeline_screenshots_recreate_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_format_screenshot_create}}</td>
					<td class="de_control">
						<table class="control_group">
							<colgroup>
								<col class="nowrap"/>
								<col/>
							</colgroup>
							<tr>
								<td class="nowrap">{{$lang.videos.mass_edit_videos_field_format_screenshot_create_overview}}</td>
								<td>
									{{foreach item="item" from=$list_formats_screenshots_overview|smarty:nodefaults}}
										<span class="de_lv_pair"><input type="checkbox" name="screenshot_format_recreate_ids[]" value="{{$item.format_screenshot_id}}"/><label>{{$item.title}}</label></span>
									{{/foreach}}
								</td>
							</tr>
							{{if count($list_formats_screenshots_timeline)>0}}
								<tr>
									<td class="nowrap">{{$lang.videos.mass_edit_videos_field_format_screenshot_create_timeline}}</td>
									<td>
										{{foreach item="item" from=$list_formats_screenshots_timeline|smarty:nodefaults}}
											<span class="de_lv_pair"><input type="checkbox" name="screenshot_format_recreate_ids[]" value="{{$item.format_screenshot_id}}"/><label>{{$item.title}}</label></span>
										{{/foreach}}
									</td>
								</tr>
							{{/if}}
							{{if count($list_formats_screenshots_posters)>0}}
								<tr>
									<td class="nowrap">{{$lang.videos.mass_edit_videos_field_format_screenshot_create_posters}}</td>
									<td>
										{{foreach item="item" from=$list_formats_screenshots_posters|smarty:nodefaults}}
											<span class="de_lv_pair"><input type="checkbox" name="screenshot_format_recreate_ids[]" value="{{$item.format_screenshot_id}}"/><label>{{$item.title}}</label></span>
										{{/foreach}}
									</td>
								</tr>
							{{/if}}
						</table>
					</td>
				</tr>
			{{/if}}
			{{if in_array('videos|edit_all',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_rotator}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_main_stats}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="rotator_reset_main_stats" value="1"/><label>{{$lang.videos.mass_edit_videos_field_main_stats_reset}}</label></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_screenshots_stats}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="rotator_reset_screenshots_stats" value="1"/><label>{{$lang.videos.mass_edit_videos_field_screenshots_stats_reset}}</label></span>
					</td>
				</tr>
			{{/if}}
			{{if count($list_post_process_plugins)>0}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.videos.mass_edit_videos_divider_plugins}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.mass_edit_videos_field_plugins}}</td>
					<td class="de_control">
						<table class="control_group">
							{{foreach item="item" from=$list_post_process_plugins|smarty:nodefaults}}
								<tr><td>
									<span class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}"/><label>{{$lang.videos.mass_edit_videos_field_plugins_execute|replace:"%1%":$item.title}}</label></span>
								</td></tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="edit_id" value="{{$smarty.get.edit_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.videos.mass_edit_videos_btn_apply}}"/>
	</div>
</form>