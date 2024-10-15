{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_mass_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.mass_edit_albums_header}}</h1></div>
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
				<td class="de_label">{{$lang.albums.mass_edit_albums_field_selected_albums}}</td>
				<td class="de_control">
					{{if $albums_count_all==1}}
						<span class="highlighted_text"><b>{{$lang.albums.mass_edit_albums_field_selected_albums_all}} ({{$albums_count}})</b></span>
					{{else}}
						<span>{{$albums_count}}</span>
					{{/if}}
				</td>
			</tr>
			{{if in_array('albums|edit_dir',$smarty.session.permissions) || in_array('albums|edit_status',$smarty.session.permissions) || in_array('albums|edit_type',$smarty.session.permissions) ||
				in_array('albums|edit_access_level',$smarty.session.permissions) || in_array('albums|edit_tokens',$smarty.session.permissions) || in_array('albums|edit_user',$smarty.session.permissions) ||
				in_array('albums|edit_admin_user',$smarty.session.permissions) || in_array('albums|edit_content_source',$smarty.session.permissions) || in_array('albums|edit_post_date',$smarty.session.permissions) ||
				in_array('albums|edit_is_locked',$smarty.session.permissions) || in_array('albums|edit_admin_flag',$smarty.session.permissions) || in_array('albums|edit_all',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.albums.mass_edit_albums_divider_general}}</h2></td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_dir',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_directory}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="regenerate_directories" value="1" {{if $disallow_directory_change==1}}disabled{{/if}}/><label>{{$lang.albums.mass_edit_albums_field_directory_regenerate}}</label></span>
						{{if count($list_languages)>0}}
							<select name="regenerate_directories_language" class="regenerate_directories_on">
								<option value="">{{$lang.albums.mass_edit_albums_field_directory_default_language}}</option>
								{{foreach item="item" from=$list_languages|smarty:nodefaults}}
									{{if $item.is_directories_localize==1}}
										<option value="{{$item.code}}">{{$item.title}}</option>
									{{/if}}
								{{/foreach}}
							</select>
						{{/if}}
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_directory_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_status',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_status}}</td>
					<td class="de_control">
						<select name="status_id">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="1">{{$lang.albums.mass_edit_albums_field_status_active}}</option>
							<option value="0">{{$lang.albums.mass_edit_albums_field_status_disabled}}</option>
						</select>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_type',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_type}}</td>
					<td class="de_control">
						<select name="is_private">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="0">{{$lang.albums.mass_edit_albums_field_type_public}}</option>
							<option value="1">{{$lang.albums.mass_edit_albums_field_type_private}}</option>
							<option value="2">{{$lang.albums.mass_edit_albums_field_type_premium}}</option>
						</select>
					</td>
				</tr>
			{{/if}}
			{{if $config.installation_type>=2}}
				{{if in_array('albums|edit_access_level',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.albums.mass_edit_albums_field_access_level}}</td>
						<td class="de_control">
							<select name="access_level_id">
								<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
								<option value="0">{{$lang.albums.mass_edit_albums_field_access_level_inherit}}</option>
								<option value="1">{{$lang.albums.mass_edit_albums_field_access_level_all}}</option>
								<option value="2">{{$lang.albums.mass_edit_albums_field_access_level_members}}</option>
								<option value="3">{{$lang.albums.mass_edit_albums_field_access_level_premium}}</option>
							</select>
						</td>
					</tr>
				{{/if}}
				{{if in_array('albums|edit_tokens',$smarty.session.permissions)}}
					<tr>
						<td class="de_label">{{$lang.albums.mass_edit_albums_field_tokens_cost}}</td>
						<td class="de_control">
							<input type="text" name="tokens_required" size="10" maxlength="10"/>
							<span class="de_hint">{{$lang.albums.mass_edit_albums_field_tokens_cost_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if in_array('albums|edit_user',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_users}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_insight_list">
										<div class="js_params">
											<span class="js_param">title={{$lang.albums.mass_edit_albums_field_users}}</span>
											<span class="js_param">url=async/insight.php?type=users</span>
											<span class="js_param">submit_mode=compound</span>
											<span class="js_param">submit_name=user_ids[]</span>
											<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_users_empty}}</span>
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
											<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_users_all}}"/>
										</div>
									</div>
								</td>
							</tr>
							{{if in_array('users|add',$smarty.session.permissions)}}
								<tr>
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_username_randomization" value="1"/><label>{{$lang.albums.mass_edit_albums_field_users_generate}}</label></span>
									</td>
								</tr>
							{{/if}}
						</table>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_users_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_admin_user',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_admins}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_admins}}</span>
								<span class="js_param">url=async/insight.php?type=admins</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=admin_user_ids[]</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_admins_empty}}</span>
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_admin"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_admins_all}}"/>
							</div>
						</div>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_admins_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_content_source',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_content_source}}</td>
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
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="content_source_set_empty" value="1"/><label>{{$lang.albums.mass_edit_albums_field_content_source_reset}}</label></span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_post_date',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_post_date}}</td>
					<td class="de_control">
						{{if $config.relative_post_dates=='true'}}
							<table class="control_group de_vis_sw_radio">
								<tr>
									<td>
										<span class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" checked/><label>{{$lang.albums.mass_edit_albums_field_post_date_option_fixed}}</label></span>
										<span>
											{{$lang.albums.mass_edit_albums_field_post_date_from}}:
											<span class="calendar">
												<input type="text" name="post_date_from" class="post_date_option_fixed" placeholder="{{$lang.common.select_default_option}}">
											</span>
										</span>
										<span>
											{{$lang.albums.mass_edit_albums_field_post_date_to}}:
											<span class="calendar">
												<input type="text" name="post_date_to" class="post_date_option_fixed" placeholder="{{$lang.common.select_default_option}}">
											</span>
										</span>
										<span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint}}</span>
									</td>
								</tr>
								<tr>
									<td>
										<span class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1"/><label>{{$lang.albums.mass_edit_albums_field_post_date_option_relative}}</label></span>
										<span>
											{{$lang.albums.mass_edit_albums_field_post_date_from}}:
											<input type="text" name="relative_post_date_from" class="post_date_option_relative" maxlength="5" size="10"/>
										</span>
										<span>
											{{$lang.albums.mass_edit_albums_field_post_date_to}}:
											<input type="text" name="relative_post_date_to" class="post_date_option_relative" maxlength="5" size="10"/>
										</span>
										<span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint2}}</span>
									</td>
								</tr>
							</table>
						{{else}}
							<span>
								{{$lang.albums.mass_edit_albums_field_post_date_from}}:
								<span class="calendar">
									<input type="text" name="post_date_from" placeholder="{{$lang.common.select_default_option}}">
								</span>
							</span>
							<span>
								{{$lang.albums.mass_edit_albums_field_post_date_to}}:
								<span class="calendar">
									<input type="text" name="post_date_to" placeholder="{{$lang.common.select_default_option}}">
								</span>
							</span>
							<span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_post_time}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="post_time_change" value="1"/><label>{{$lang.albums.mass_edit_albums_field_post_time_change}}</label></span>
						<span>
							{{$lang.albums.mass_edit_albums_field_post_time_from}}:
							<input type="text" name="post_time_from" maxlength="5" class="post_time_change_on" size="5" value="00:00"/>
						</span>
						<span>
							{{$lang.albums.mass_edit_albums_field_post_time_to}}:
							<input type="text" name="post_time_to" maxlength="5" class="post_time_change_on" size="5" value="23:59"/>
						</span>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_time_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_all',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_rating}}</td>
					<td class="de_control">
						<span>
							{{$lang.albums.mass_edit_albums_field_rating_min}}:
							<input type="text" name="rating_min" size="10"/>
						</span>
						<span>
							{{$lang.albums.mass_edit_albums_field_rating_max}}:
							<input type="text" name="rating_max" size="10"/>
						</span>
						<span>
							{{$lang.albums.mass_edit_albums_field_rating_votes_from}}:
							<input type="text" name="rating_amount_min" size="5" value="1"/>
						</span>
						<span>
							{{$lang.albums.mass_edit_albums_field_rating_votes_to}}:
							<input type="text" name="rating_amount_max" size="5" value="1"/>
						</span>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_rating_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_visits}}</td>
					<td class="de_control">
						<span>
							{{$lang.albums.mass_edit_albums_field_visits_min}}:
							<input type="text" name="visits_min" size="10"/>
						</span>
						<span>
							{{$lang.albums.mass_edit_albums_field_visits_max}}:
							<input type="text" name="visits_max" size="10"/>
						</span>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_visits_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_is_locked',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_lock_website}}</td>
					<td class="de_control">
						<select name="is_locked">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="1">{{$lang.albums.mass_edit_albums_field_lock_website_locked}}</option>
							<option value="0">{{$lang.albums.mass_edit_albums_field_lock_website_unlocked}}</option>
						</select>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_lock_website_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_status',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_review_flag}}</td>
					<td class="de_control">
						<select name="is_review_needed">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="1">{{$lang.albums.mass_edit_albums_field_review_flag_set}}</option>
							<option value="0">{{$lang.albums.mass_edit_albums_field_review_flag_unset}}</option>
						</select>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_review_flag_hint}}</span>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_admin_flag',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_admin_flag}}</td>
					<td class="de_control">
						<select name="admin_flag_id">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="-1">{{$lang.albums.mass_edit_albums_field_admin_flag_reset}}</option>
							{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
								<option value="{{$item.flag_id}}">{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_categories',$smarty.session.permissions) || in_array('albums|edit_tags',$smarty.session.permissions) || in_array('albums|edit_models',$smarty.session.permissions) ||
				in_array('albums|edit_flags',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.albums.mass_edit_albums_divider_categorization}}</h2></td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_tags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_tags_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_tags_add}}</span>
								<span class="js_param">url=async/insight.php?type=tags</span>
								<span class="js_param">submit_mode=simple</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_tags_empty}}</span>
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
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_tags_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_categories',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_categories_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_categories_add}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=category_ids_add[]</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_categories_empty}}</span>
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
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_categories_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_models',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_models_add}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_models_add}}</span>
								<span class="js_param">url=async/insight.php?type=models</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=model_ids_add[]</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_models_empty}}</span>
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
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_models_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_tags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_tags_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_tags_delete}}</span>
								<span class="js_param">url=async/insight.php?type=tags</span>
								<span class="js_param">submit_mode=simple</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_tags_empty}}</span>
								{{if in_array('tags|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<input type="hidden" name="tags_delete"/>
							<div class="controls">
								<input type="text" name="new_tag_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_tags_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_categories',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_categories_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_categories_delete}}</span>
								<span class="js_param">url=async/insight.php?type=categories</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=category_ids_delete[]</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_categories_empty}}</span>
								{{if in_array('categories|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_category_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_categories_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_models',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_models_delete}}</td>
					<td class="de_control">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.albums.mass_edit_albums_field_models_delete}}</span>
								<span class="js_param">url=async/insight.php?type=models</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=model_ids_delete[]</span>
								<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_models_empty}}</span>
								{{if in_array('models|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
								{{/if}}
							</div>
							<div class="list"></div>
							<div class="controls">
								<input type="text" name="new_model_delete"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_models_all}}"/>
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_flags',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_flags_reset}}</td>
					<td class="de_control">
						{{foreach item="item" from=$list_flags_albums|smarty:nodefaults}}
							<span class="de_lv_pair"><input type="checkbox" name="flag_ids_delete[]" value="{{$item.flag_id}}"/><label>{{$item.title}}</label></span>
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_storage',$smarty.session.permissions) || in_array('albums|manage_images',$smarty.session.permissions)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.albums.mass_edit_albums_divider_content}}</h2></td>
				</tr>
			{{/if}}
			{{if in_array('albums|edit_storage',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_change_storage_group}}</td>
					<td class="de_control">
						<select name="new_storage_group_id">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							{{foreach item="item" from=$list_server_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}">{{$item.title}} ({{$lang.albums.mass_edit_albums_field_change_storage_group_free|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			{{/if}}
			{{if in_array('albums|manage_images',$smarty.session.permissions)}}
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_invalidate_cdn}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="invalidate_cdn" value="1"/><label>{{$lang.albums.mass_edit_albums_field_invalidate_cdn_invalidate}}</label></span>
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_invalidate_cdn_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_format_album_create}}</td>
					<td class="de_control">
						<table class="control_group">
							<colgroup>
								<col class="nowrap"/>
								<col/>
							</colgroup>
							<tr>
								<td class="nowrap">{{$lang.albums.mass_edit_albums_field_format_album_create_main}}</td>
								<td>
									{{foreach item="item" from=$list_formats_albums_main|smarty:nodefaults}}
										<span class="de_lv_pair"><input type="checkbox" name="album_format_recreate_ids[]" value="{{$item.format_album_id}}"/><label>{{$item.title}}</label></span>
									{{/foreach}}
								</td>
							</tr>
							<tr>
								<td class="nowrap">{{$lang.albums.mass_edit_albums_field_format_album_create_preview}}</td>
								<td>
									{{foreach item="item" from=$list_formats_albums_preview|smarty:nodefaults}}
										<span class="de_lv_pair"><input type="checkbox" name="album_format_recreate_ids[]" value="{{$item.format_album_id}}"/><label>{{$item.title}}</label></span>
									{{/foreach}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{{/if}}
			{{if count($list_post_process_plugins)>0}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.albums.mass_edit_albums_divider_plugins}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.albums.mass_edit_albums_field_plugins}}</td>
					<td class="de_control">
						<table class="control_group">
							{{foreach item="item" from=$list_post_process_plugins|smarty:nodefaults}}
								<tr><td>
									<span class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}"/><label>{{$lang.albums.mass_edit_albums_field_plugins_execute|replace:"%1%":$item.title}}</label></span>
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
		<input type="submit" name="save_default" value="{{$lang.albums.mass_edit_albums_btn_apply}}"/>
	</div>
</form>