{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('playlists|edit_all',$smarty.session.permissions) || (in_array('playlists|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="playlist_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_playlists_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.playlist_add}}{{else}}{{$lang.users.playlist_edit|replace:"%1%":$smarty.post.title}}{{/if}}</h1></div>
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
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/113-theme-customization-how-to-build-embed-code-for-video-playlist/">How to build embed code for video playlist</a></span>
				</td>
			</tr>
			{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.playlist_divider_review}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.users.playlist_divider_review_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.playlist_field_reviewed}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_reviewed" value="1"/><label>{{$lang.users.playlist_field_reviewed_yes}}</label></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.playlist_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.playlist_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.users.playlist_field_directory}}</td>
					<td class="de_control">
						<input type="text" name="dir" maxlength="255" value="{{$smarty.post.dir}}"/>
						<span class="de_hint">{{$lang.users.playlist_field_directory_hint|replace:"%1%":$lang.users.playlist_field_title}}</span>
					</td>
				</tr>
				{{if $smarty.post.website_link!=''}}
					<tr data-field-name="website_link">
						<td class="de_label">{{$lang.users.playlist_field_website_link}}</td>
						<td class="de_control">
							<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_description}}</td>
				<td class="de_control">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.playlist_field_user}}</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="user" maxlength="255" value="{{$smarty.post.user}}"/>
					</div>
					<span class="de_hint">{{$lang.users.playlist_field_user_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="is_private">
						<option value="0" {{if $smarty.post.is_private=='0'}}selected{{/if}}>{{$lang.users.playlist_field_type_public}}</option>
						<option value="1" {{if $smarty.post.is_private=='1'}}selected{{/if}}>{{$lang.users.playlist_field_type_private}}</option>
					</select>
					<span class="de_hint">{{$lang.users.playlist_field_type_hint}}</span>
				</td>
			</tr>
			<tr class="is_private_0">
				<td class="de_label">{{$lang.users.playlist_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}}/><label>{{$lang.users.playlist_field_status_active}}</label></span>
					<span class="de_hint">{{$lang.users.playlist_field_status_hint}}</span>
				</td>
			</tr>
			<tr class="is_private_0">
				<td class="de_label">{{$lang.users.playlist_field_lock_website}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked{{/if}}/><label>{{$lang.users.playlist_field_lock_website_locked}}</label></span>
					<span class="de_hint">{{$lang.users.playlist_field_lock_website_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.playlist_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_tags}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.playlist_field_tags}}</span>
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
							<span class="js_param">empty_message={{$lang.users.playlist_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_tag"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.users.playlist_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_categories}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.playlist_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.users.playlist_field_categories_empty}}</span>
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
								<input type="button" class="all" value="{{$lang.users.playlist_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">
						<div>{{$lang.users.playlist_field_flags}}</div>
					</td>
					<td class="de_control">
						<div class="de_deletable_list">
							<div class="js_params">
								<span class="js_param">submit_name=delete_flags[]</span>
								<span class="js_param">empty_message={{$lang.users.playlist_field_flags_empty}}</span>
								{{if $can_edit_all!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
							</div>
							<div class="list">
								{{if count($smarty.post.flags)>0}}
									{{foreach name="data" item="item" from=$smarty.post.flags|smarty:nodefaults}}
										<span class="item"><a data-item-id="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}})</a>{{if !$smarty.foreach.data.last}}<span class="separator">,</span>{{/if}}</span>
									{{/foreach}}
								{{else}}
									{{$lang.users.playlist_field_flags_empty}}
								{{/if}}
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.users.playlist_divider_videos}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_add_videos}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.playlist_field_add_videos}}</span>
							<span class="js_param">url=async/insight.php?type=videos</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=add_video_ids[]</span>
							<span class="js_param">empty_message={{$lang.users.playlist_field_add_videos_empty}}</span>
						</div>
						<div class="list"></div>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_video"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.users.playlist_field_add_videos_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if count($smarty.post.videos)>0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.users.playlist_divider_videos_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td class="eg_selector"><input type="checkbox"/><label>{{$lang.common.dg_actions_detach}}</label></td>
								<td>{{$lang.videos.video_field_id}}</td>
								<td>{{$lang.videos.video_field_title}}</td>
								<td>{{$lang.videos.video_field_duration}}</td>
								<td>{{$lang.videos.video_field_status}}</td>
								<td>{{$lang.videos.video_field_type}}</td>
								<td>{{$lang.videos.video_field_visits}}</td>
								<td>{{$lang.videos.video_field_rating}}</td>
								<td>{{$lang.videos.video_field_order}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.videos|smarty:nodefaults}}
								<tr class="eg_data {{if $item.status_id==0}}disabled{{/if}}">
									<td class="eg_selector"><input type="checkbox" name="delete_video_ids[]" value="{{$item.video_id}}"/></td>
									<td class="nowrap">
										{{if in_array('videos|view',$smarty.session.permissions)}}
											<a href="videos.php?action=change&amp;item_id={{$item.video_id}}">{{$item.video_id}}</a>
										{{else}}
											{{$item.video_id}}
										{{/if}}
									</td>
									<td>{{$item.title}}</td>
									<td class="nowrap">{{$item.duration}}</td>
									<td class="nowrap">{{if $item.status_id==0}}{{$lang.videos.video_field_status_disabled}}{{elseif $item.status_id==1}}{{$lang.videos.video_field_status_active}}{{elseif $item.status_id==2}}<span class="highlighted_text">{{$lang.videos.video_field_status_error}}</span>{{elseif $item.status_id==3}}{{$lang.videos.video_field_status_in_process}}{{elseif $item.status_id==4}}{{$lang.videos.video_field_status_deleting}}{{elseif $item.status_id==5}}{{$lang.videos.video_field_status_deleted}}{{/if}}</td>
									<td class="nowrap">{{if $item.is_private==2}}{{$lang.videos.video_field_type_premium}}{{elseif $item.is_private==1}}{{$lang.videos.video_field_type_private}}{{elseif $item.is_private==0}}{{$lang.videos.video_field_type_public}}{{/if}}</td>
									<td class="nowrap {{if $item.video_viewed==0}}disabled{{/if}}">{{if $item.video_viewed>999}}{{$item.video_viewed/1000|number_format:1:".":""}}{{$lang.common.traffic_k}}{{else}}{{$item.video_viewed}}{{/if}}</td>
									<td class="nowrap {{if $item.rating==0}}disabled{{/if}}">{{$item.rating|number_format:1:".":" "}}</td>
									<td>
										<input type="text" name="video_sorting_{{$item.video_id}}" maxlength="32" value="{{$item.sort_id}}" size="3" autocomplete="off"/>
									</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
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
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('playlists|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('playlists|edit_all',$smarty.session.permissions)}}
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
						<option value="">{{$lang.users.playlist_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.users.playlist_field_status_disabled}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.users.playlist_field_status_active}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_is_private">
						<option value="">{{$lang.users.playlist_field_type}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_is_private=='0'}}selected{{/if}}>{{$lang.users.playlist_field_type_public}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_is_private=='1'}}selected{{/if}}>{{$lang.users.playlist_field_type_private}}</option>
					</select>
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
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.users.playlist_field_tag}}..."/>
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
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.users.playlist_field_category}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.users.playlist_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_description}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_rating}}</option>
						<option value="empty/playlist_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/playlist_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_visits}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_categories}}</option>
						<option value="empty/videos" {{if $smarty.session.save.$page_name.se_field=="empty/videos"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_videos_count}}</option>
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_description}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_rating}}</option>
						<option value="filled/playlist_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/playlist_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_visits}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_categories}}</option>
						<option value="filled/videos" {{if $smarty.session.save.$page_name.se_field=="filled/videos"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_videos_count}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_flag_id">
						<option value="">{{$lang.common.dg_filter_flag}}...</option>
						{{foreach item="item_flag" from=$list_flags_playlists|smarty:nodefaults}}
							<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected{{/if}}>{{$item_flag.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_review_flag">
						<option value="">{{$lang.common.dg_filter_review_flag}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_locked">
						<option value="">{{$lang.users.playlist_field_lock_website}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_locked=='yes'}}selected{{/if}}>{{$lang.users.playlist_field_lock_website_locked}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_locked=='no'}}selected{{/if}}>{{$lang.users.playlist_field_lock_website_unlocked}}</option>
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
							<td class="dg_selector">
								<input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/>
								<input type="hidden" name="row_all[]" value="{{$item.$table_key_name}}"/>
								<span class="js_params">
									{{if $item.status_id==0}}
										<span class="js_param">inactive=true</span>
									{{/if}}
								</span>
							</td>
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
										{{if $item.is_private==1}}
											<span class="js_param">deactivate_disable=true</span>
										{{/if}}
										{{if $item.is_review_needed!=1}}
											<span class="js_param">mark_reviewed_hide=true</span>
										{{/if}}
										{{if $item.website_link==''}}
											<span class="js_param">website_link_disable=true</span>
										{{else}}
											<span class="js_param">website_link={{$item.website_link}}</span>
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
						<span class="js_param">disable=${deactivate_disable}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
						<span class="js_param">hide=${mark_reviewed_hide}</span>
						<span class="js_param">icon=action-approve</span>
					</li>
				{{/if}}
				{{if in_array('users|manage_comments',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=13&amp;object_id=${id}</span>
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
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=13&amp;se_object_id=${id}</span>
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
						<optgroup label="{{$lang.common.dg_batch_groups_delete}}">
							<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
							{{if $can_edit==1}}
								<option value="delete_and_activate">{{$lang.users.playlist_batch_delete_and_activate|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_edit==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_status}}">
							<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
							<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
							<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed|replace:"%1%":'${count}'}}</option>
							{{if $can_delete==1}}
								<option value="activate_and_delete">{{$lang.users.playlist_batch_activate_and_delete|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</option>
							{{/if}}
						</optgroup>
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
					<span class="js_param">value=activate_and_delete</span>
					<span class="js_param">confirm={{$lang.users.playlist_batch_activate_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_activate</span>
					<span class="js_param">confirm={{$lang.users.playlist_batch_delete_and_activate_confirm|replace:"%1%":'${count}'|replace:"%2%":'${inactive_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}