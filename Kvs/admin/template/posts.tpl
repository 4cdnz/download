{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('posts|edit_all',$smarty.session.permissions) || (in_array('posts|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}
{{if in_array('posts|edit_title',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_title" value=1}}
{{else}}
	{{assign var="can_edit_title" value=0}}
{{/if}}
{{if in_array('posts|edit_dir',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_dir" value=1}}
{{else}}
	{{assign var="can_edit_dir" value=0}}
{{/if}}
{{if in_array('posts|edit_description',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_description" value=1}}
{{else}}
	{{assign var="can_edit_description" value=0}}
{{/if}}
{{if in_array('posts|edit_content',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_content" value=1}}
{{else}}
	{{assign var="can_edit_content" value=0}}
{{/if}}
{{if in_array('posts|edit_post_date',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_post_date" value=1}}
{{else}}
	{{assign var="can_edit_post_date" value=0}}
{{/if}}
{{if in_array('posts|edit_user',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_user" value=1}}
{{else}}
	{{assign var="can_edit_user" value=0}}
{{/if}}
{{if in_array('posts|edit_status',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
{{/if}}
{{if in_array('posts|edit_type',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_type" value=1}}
{{else}}
	{{assign var="can_edit_type" value=0}}
{{/if}}
{{if in_array('posts|edit_categories',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_categories" value=1}}
{{else}}
	{{assign var="can_edit_categories" value=0}}
{{/if}}
{{if in_array('posts|edit_tags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_tags" value=1}}
{{else}}
	{{assign var="can_edit_tags" value=0}}
{{/if}}
{{if in_array('posts|edit_models',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_models" value=1}}
{{else}}
	{{assign var="can_edit_models" value=0}}
{{/if}}
{{if in_array('posts|edit_flags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_flags" value=1}}
{{else}}
	{{assign var="can_edit_flags" value=0}}
{{/if}}
{{if in_array('posts|edit_custom',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_custom" value=1}}
{{else}}
	{{assign var="can_edit_custom" value=0}}
{{/if}}
{{if in_array('posts|edit_is_locked',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var="can_edit_is_locked" value=1}}
{{else}}
	{{assign var="can_edit_is_locked" value=0}}
{{/if}}
{{if in_array('posts|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="post_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{if $locked_post_type_id>0}}{{$locked_post_type.title}}{{else}}{{$lang.posts.submenu_option_posts_list}}{{/if}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.posts.post_add}}
				{{else}}
					{{if $smarty.post.title!=''}}
						{{$lang.posts.post_edit|replace:"%1%":$smarty.post.title}}
					{{else}}
						{{$lang.posts.post_edit|replace:"%1%":$smarty.post.post_id}}
					{{/if}}
				{{/if}}
			</h1>
		</div>
		<table class="de_editor">
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="4">
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
				<td class="de_simple_text" colspan="4">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/138-theme-customization-how-to-display-text-posts-in-kvs/">How to display text posts in KVS</a></span>
				</td>
			</tr>
			{{if $smarty.post.is_review_needed==1 && $can_edit_status==1}}
				<tr>
					<td class="de_separator" colspan="4"><h2>{{$lang.posts.post_divider_review}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.posts.post_divider_review_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.posts.post_field_review_action}}</td>
					<td class="de_control" colspan="3">
						<table class="control_group">
							<tr>
								<td class="de_vis_sw_select">
									<select name="is_reviewed" class="preserve_editing">
										<option value="0">{{$lang.posts.post_field_review_action_none}}</option>
										<option value="1">{{$lang.posts.post_field_review_action_approve}}</option>
										<option value="2" {{if $can_delete==0}}disabled{{/if}}>{{$lang.posts.post_field_review_action_delete}}</option>
									</select>
								</td>
							</tr>
							{{if $smarty.post.status_id==0}}
								<tr class="is_reviewed_1">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_activate" value="1" class="preserve_editing"/><label>{{$lang.posts.post_field_review_action_activate}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_disable_user" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('users|edit_all',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.posts.post_field_review_action_disable_user|replace:"%1%":$smarty.post.user}}</label></span>
									</td>
								</tr>
								{{if $smarty.post.user_domain!='' && $smarty.post.user_domain_blocked!=1}}
									<tr class="is_reviewed_2">
										<td>
											<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_domain" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.posts.post_field_review_action_block_domain|replace:"%1%":$smarty.post.user_domain}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
							{{if $smarty.post.ip_mask!='0.0.0.*' && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_mask" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.posts.post_field_review_action_block_mask|replace:"%1%":$smarty.post.ip_mask}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.ip!='0.0.0.0' && $smarty.post.ip_blocked!=1 && $smarty.post.ip_mask_blocked!=1}}
								<tr class="is_reviewed_2">
									<td>
										<span class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_ip" value="1" class="is_reviewed_delete preserve_editing" {{if !in_array('system|antispam_settings',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.posts.post_field_review_action_block_ip|replace:"%1%":$smarty.post.ip}}</label></span>
									</td>
								</tr>
							{{/if}}
							{{if $smarty.post.user_status_id!=4}}
								{{if $smarty.post.other_posts_need_review>0}}
									<tr class="is_reviewed_2">
										<td>
											{{assign var="max_delete_on_review" value=$config.max_delete_on_review|intval}}
											{{if $max_delete_on_review==0}}
												{{assign var="max_delete_on_review" value=30}}
											{{/if}}
											<span class="de_lv_pair"><input type="checkbox" name="is_delete_all_posts_from_user" value="1" class="is_reviewed_delete preserve_editing" {{if $can_delete!=1 || $smarty.post.other_posts_need_review>$max_delete_on_review}}disabled{{/if}}/><label>{{$lang.posts.post_field_review_action_delete_other|replace:"%1%":$smarty.post.other_posts_need_review}}</label></span>
										</td>
									</tr>
								{{/if}}
							{{/if}}
						</table>
					</td>
				</tr>
				{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
					<tr>
						<td class="de_label">{{$lang.posts.post_field_af_upload_zone}}</td>
						<td class="de_control">
							<select name="af_upload_zone">
								<option value="0" {{if $smarty.post.af_upload_zone==0}}selected{{/if}}>{{$lang.posts.post_field_af_upload_zone_site}}</option>
								<option value="1" {{if $smarty.post.af_upload_zone==1}}selected{{/if}}>{{$lang.posts.post_field_af_upload_zone_memberarea}}</option>
							</select>
							<span class="de_hint">{{$lang.posts.post_field_af_upload_zone_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.posts.post_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label status_id_on">{{$lang.posts.post_field_title}}</td>
				<td class="de_control" colspan="3">
					<input type="text" name="title" maxlength="255" class="{{if $can_edit_title==1}}preserve_editing{{/if}}" value="{{$smarty.post.title}}"/>
					<span class="de_hint">{{$lang.posts.post_field_title_hint}}, <span class="de_str_len_value"></span></span>
				</td>
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_directory}}</td>
					<td class="de_control" colspan="3">
						<input type="text" name="dir" maxlength="255" class="{{if $can_edit_dir==1}}preserve_editing{{/if}}" value="{{$smarty.post.dir}}" {{if $options.POST_REGENERATE_DIRECTORIES==1}}readonly{{/if}}/>
						{{if $options.POST_REGENERATE_DIRECTORIES==1}}
							<span class="de_hint">{{$lang.posts.post_field_directory_hint2|replace:"%1%":$lang.posts.post_field_title}}</span>
						{{else}}
							<span class="de_hint">{{$lang.posts.post_field_directory_hint|replace:"%1%":$lang.posts.post_field_title}}</span>
						{{/if}}
					</td>
				</tr>
				{{if $smarty.post.website_link!=''}}
					<tr data-field-name="website_link">
						<td class="de_label">{{$lang.posts.post_field_website_link}}</td>
						<td class="de_control" colspan="3">
							<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.posts.post_field_description}}</td>
				<td class="de_control" colspan="3">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_description==1}}preserve_editing{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.posts.post_field_content}}</td>
				<td class="de_control" colspan="3">
					<textarea name="content" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_content==1}}preserve_editing{{/if}}" cols="40" rows="6">{{$smarty.post.content}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.posts.post_field_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="post_type_id" {{if $can_edit_type==1}}class="preserve_editing"{{/if}}>
						{{foreach from=$list_types|smarty:nodefaults item="item"}}
							<option value="{{$item.post_type_id}}" {{if $smarty.post.post_type_id==$item.post_type_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
				<td class="de_label de_required">{{$lang.posts.post_field_user}}</td>
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
						<input type="text" name="user" maxlength="255" class="{{if $can_edit_user==1}}preserve_editing{{/if}}" value="{{$smarty.post.user}}"/>
					</div>
					<span class="de_hint">{{$lang.posts.post_field_user_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.posts.post_field_post_date}}</td>
				<td class="de_control">
					{{if $config.relative_post_dates=='true'}}
						<table class="control_group de_vis_sw_radio">
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option!='1'}}checked{{/if}}/><label>{{$lang.posts.post_field_post_date_fixed}}</label></span>
									<span class="calendar">
										<span class="js_params">
											<span class="js_param">type=datetime</span>
											{{if $can_edit_post_date!=1}}
												<span class="js_param">forbid_edit=true</span>
											{{/if}}
										</span>
										<input class="post_date_option_fixed" type="text" name="post_date" value="{{$smarty.post.post_date}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
									<span class="de_hint">{{$lang.posts.post_field_post_date_hint}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} {{if $smarty.post.post_date_option=='1'}}checked{{/if}}/><label>{{$lang.posts.post_field_post_date_relative}}</label></span>
									<span>
										<input type="text" name="relative_post_date" size="4" maxlength="5" class="post_date_option_relative {{if $can_edit_post_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.relative_post_date}}"/>
										{{$lang.posts.post_field_post_date_relative_days}}
									</span>
									<span class="de_hint">{{$lang.posts.post_field_post_date_hint2}}</span>
								</td>
							</tr>
						</table>
					{{else}}
						<span class="calendar">
							<span class="js_params">
								<span class="js_param">type=datetime</span>
								{{if $can_edit_post_date!=1}}
									<span class="js_param">forbid_edit=true</span>
								{{/if}}
							</span>
							<input type="text" name="post_date" value="{{$smarty.post.post_date}}" {{if $can_edit_post_date==1}}class="preserve_editing"{{/if}} placeholder="{{$lang.common.select_default_option}}">
						</span>
						<span class="de_hint">{{$lang.posts.post_field_post_date_hint}}</span>
					{{/if}}
				</td>
				<td class="de_label">{{$lang.posts.post_field_status}}</td>
				<td class="de_control de_vis_sw_checkbox">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}} {{if $can_edit_status==1}}class="preserve_editing"{{/if}}/><label>{{$lang.posts.post_field_status_active}}</label></span>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_lock_website}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked{{/if}} {{if $can_edit_is_locked==1}}class="preserve_editing"{{/if}}/><label>{{$lang.posts.post_field_lock_website_locked}}</label></span>
						<span class="de_hint">{{$lang.posts.post_field_lock_website_hint}}</span>
					</td>
					<td class="de_label">{{$lang.posts.post_field_ip}}</td>
					<td class="de_control">
						<span>
							{{if $config.safe_mode!='true'}}
								{{$smarty.post.ip}}
							{{else}}
								0.0.0.0
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.connected_video_title!=''}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_connected_video}}</td>
					<td class="de_control" colspan="3">
						<a href="videos.php?action=change&amp;item_id={{$smarty.post.connected_video_id}}">{{$smarty.post.connected_video_title}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="4"><h2>{{$lang.posts.post_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.posts.post_field_tags}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.posts.post_field_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=simple</span>
							{{if $can_edit_tags!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
							{{if in_array('tags|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.posts.post_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_tags==1}}
							<div class="controls">
								<input type="text" name="new_tag" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.posts.post_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.posts.post_field_categories}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.posts.post_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.posts.post_field_categories_empty}}</span>
							{{if $can_edit_categories!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_categories==1}}
							<div class="controls">
								<input type="text" name="new_category" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.posts.post_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.posts.post_field_models}}</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.posts.post_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=model_ids[]</span>
							{{if in_array('models|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.posts.post_field_models_empty}}</span>
							{{if $can_edit_models!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
							<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_models==1}}
							<div class="controls">
								<input type="text" name="new_model" class="preserve_editing"/>
								<input type="button" class="add preserve_editing" value="{{$lang.common.add}}"/>
								<input type="button" class="all preserve_editing" value="{{$lang.posts.post_field_models_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_flags}}</td>
					<td class="de_control" colspan="3">
						<div class="de_deletable_list">
							<div class="js_params">
								<span class="js_param">submit_name=delete_flags[]</span>
								<span class="js_param">empty_message={{$lang.posts.post_field_flags_empty}}</span>
								{{if $can_edit_flags!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
							</div>
							<div class="list">
								{{if count($smarty.post.flags)>0}}
									{{foreach name="data" item="item" from=$smarty.post.flags|smarty:nodefaults}}
										<span class="item"><a data-item-id="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}})</a>{{if !$smarty.foreach.data.last}}<span class="separator">,</span>{{/if}}</span>
									{{/foreach}}
								{{else}}
									{{$lang.posts.post_field_flags_empty}}
								{{/if}}
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if count($list_custom_fields) > 0}}
				<tr class="{{foreach item="item" from=$post_types_with_custom_fields|smarty:nodefaults}}post_type_id_{{$item}} {{/foreach}}">
					<td class="de_separator" colspan="4"><h2>{{$lang.posts.post_divider_customization}}</h2></td>
				</tr>
				{{foreach name="data" item="item" from=$list_custom_fields|smarty:nodefaults}}
					<tr class="{{foreach name="data_enabled" key="key_enabled" item="item_enabled" from=$item.enabled|smarty:nodefaults}}post_type_id_{{$key_enabled}} {{/foreach}}">
						<td class="de_label">
							{{foreach name="data_titles" key="key_titles" item="item_titles" from=$item.titles|smarty:nodefaults}}
								<div class="post_type_id_{{$key_titles}}">{{$item_titles}}</div>
							{{/foreach}}
						</td>
						<td class="de_control" colspan="3">
							{{if $item.is_text==1}}
								{{assign var="field_name" value=$item.field_name}}
								<textarea name="{{$field_name}}" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.$field_name}}</textarea>
								<span class="de_hint"><span class="de_str_len_value"></span></span>
							{{else}}
								<div class="de_fu">
									<div class="js_params">
										{{foreach name="data_titles" key="key_titles" item="item_titles" from=$item.titles|smarty:nodefaults}}
											<span class="js_param post_type_id_{{$key_titles}}">title={{$item_titles}}:</span>
										{{/foreach}}
										{{assign var="field_name" value=$item.field_name}}
										{{if $smarty.get.action=='change' && $smarty.post.$field_name!=''}}
											{{if in_array(end(explode(".",$smarty.post.$field_name)),explode(",",$config.image_allowed_ext))}}
												<span class="js_param">preview_url={{$config.content_url_posts}}/{{$smarty.post.dir_path}}/{{$smarty.post.post_id}}/{{$smarty.post.$field_name}}</span>
											{{else}}
												<span class="js_param">download_url={{$config.content_url_posts}}/{{$smarty.post.dir_path}}/{{$smarty.post.post_id}}/{{$smarty.post.$field_name}}</span>
											{{/if}}
										{{/if}}
									</div>
									<input type="text" name="{{$field_name}}" maxlength="100" {{if $smarty.get.action=='change'}}value="{{$smarty.post.$field_name}}"{{/if}}/>
									<input type="hidden" name="{{$field_name}}_hash"/>
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
								</div>
							{{/if}}
						</td>
					</tr>
				{{/foreach}}
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
			{{if $can_delete}}
				<span class="de_separated_group">
					<input type="submit" name="delete_and_edit" class="destructive" value="{{$lang.common.btn_delete_and_edit_next}}" data-confirm="{{$lang.common.btn_delete_and_edit_next_confirm}}"/>
				</span>
			{{/if}}
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('posts|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('posts|edit_status',$smarty.session.permissions) || in_array('posts|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_status" value=1}}
{{else}}
	{{assign var="can_edit_status" value=0}}
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
					{{if $locked_post_type_id>0}}
						<label>{{$lang.posts.post_field_type}}: {{foreach from=$list_types|smarty:nodefaults item="item"}}{{if $locked_post_type_id==$item.post_type_id}}{{$item.title}}{{/if}}{{/foreach}}</label>
					{{else}}
						<select name="se_post_type_id">
							<option value="">{{$lang.posts.post_field_type}}...</option>
							{{foreach from=$list_types|smarty:nodefaults item="item"}}
								<option value="{{$item.post_type_id}}" {{if $smarty.session.save.$page_name.se_post_type_id==$item.post_type_id}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					{{/if}}
				</div>
				<div class="dgf_filter">
					<select name="se_status_id">
						<option value="">{{$lang.posts.post_field_status}}...</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.posts.post_field_status_disabled}}</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.posts.post_field_status_active}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_admin_user_id">
						<option value="">{{$lang.posts.post_field_admin}}...</option>
						{{foreach from=$list_admin_users|smarty:nodefaults item="item"}}
							<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_user_id==$item.user_id}}selected{{/if}}>{{$item.login}}</option>
						{{/foreach}}
					</select>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.posts.post_field_user}}..."/>
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
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.posts.post_field_category}}..."/>
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
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.posts.post_field_tag}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=models</span>
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_model_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_model" value="{{$smarty.session.save.$page_name.se_model}}" placeholder="{{$lang.posts.post_field_model}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.posts.post_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_flag_id">
						<option value="">{{$lang.common.dg_filter_flag}}...</option>
						{{foreach item="item_flag" from=$list_flags_posts|smarty:nodefaults}}
							<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected{{/if}}>{{$item_flag.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/title" {{if $smarty.session.save.$page_name.se_field=="empty/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_title}}</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_description}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_rating}}</option>
						<option value="empty/post_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/post_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_visits}}</option>
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_categories}}</option>
						<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_models}}</option>
						{{if $locked_post_type_id>0}}
							{{foreach from=$list_custom_fields|smarty:nodefaults item="custom_field"}}
								{{if $custom_field.enabled[$locked_post_type_id]==1}}
									<option value="empty/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field.field_name`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$custom_field.titles[$locked_post_type_id]}}</option>
								{{/if}}
							{{/foreach}}
						{{/if}}
						<option value="filled/title" {{if $smarty.session.save.$page_name.se_field=="filled/title"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_title}}</option>
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_description}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_rating}}</option>
						<option value="filled/post_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/post_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_visits}}</option>
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_categories}}</option>
						<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_models}}</option>
						{{if $locked_post_type_id>0}}
							{{foreach from=$list_custom_fields|smarty:nodefaults item="custom_field"}}
								{{if $custom_field.enabled[$locked_post_type_id]==1}}
									<option value="filled/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field.field_name`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$custom_field.titles[$locked_post_type_id]}}</option>
								{{/if}}
							{{/foreach}}
						{{/if}}
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
					<select name="se_posted">
						<option value="">{{$lang.common.dg_filter_posted}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_posted=="yes"}}selected{{/if}}>{{$lang.common.dg_filter_posted_yes}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_posted=="no"}}selected{{/if}}>{{$lang.common.dg_filter_posted_no}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_post_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_post_date_from" value="{{$smarty.session.save.$page_name.se_post_date_from}}" placeholder="{{$lang.common.dg_filter_post_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_post_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_post_date_to" value="{{$smarty.session.save.$page_name.se_post_date_to}}" placeholder="{{$lang.common.dg_filter_post_date_to}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_locked">
						<option value="">{{$lang.posts.post_field_lock_website}}...</option>
						<option value="yes" {{if $smarty.session.save.$page_name.se_locked=='yes'}}selected{{/if}}>{{$lang.posts.post_field_lock_website_locked}}</option>
						<option value="no" {{if $smarty.session.save.$page_name.se_locked=='no'}}selected{{/if}}>{{$lang.posts.post_field_lock_website_unlocked}}</option>
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
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{if $item.title!=''}}{{$item.title}}{{else}}{{$item.$table_key_name}}{{/if}}</span>
										{{if $item.website_link==''}}
											<span class="js_param">website_link_disable=true</span>
										{{else}}
											<span class="js_param">website_link={{$item.website_link}}</span>
										{{/if}}
										{{if $item.is_review_needed!=1}}
											<span class="js_param">mark_reviewed_hide=true</span>
										{{/if}}
										{{if $item.status_id==1}}
											<span class="js_param">activate_hide=true</span>
										{{else}}
											<span class="js_param">deactivate_hide=true</span>
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
				{{if $can_edit_status==1}}
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
					<li class="js_params">
						<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
						<span class="js_param">hide=${mark_reviewed_hide}</span>
						<span class="js_param">icon=action-approve</span>
					</li>
				{{/if}}
				{{if in_array('users|manage_comments',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=12&amp;object_id=${id}</span>
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
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=12&amp;se_object_id=${id}</span>
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
					{{if $can_edit_status==1}}
						<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
						<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
						<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed|replace:"%1%":'${count}'}}</option>
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
			</ul>
		</div>
	</form>
</div>

{{/if}}