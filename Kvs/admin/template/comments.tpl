{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('users|manage_comments',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="comment_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_comments_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.comment_add}}{{else}}{{$lang.users.comment_edit|replace:"%1%":$smarty.post.comment_id}}{{/if}}</h1></div>
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
				<td class="de_label">{{$lang.users.comment_field_object}}</td>
				<td class="de_control">
					{{if $smarty.post.object_type_id==1}}
						{{if in_array('videos|view',$smarty.session.permissions)}}
							<a href="videos.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==2}}
						{{if in_array('albums|view',$smarty.session.permissions)}}
							<a href="albums.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==3}}
						{{if in_array('content_sources|view',$smarty.session.permissions)}}
							<a href="content_sources.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_content_source}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_content_source}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==4}}
						{{if in_array('models|view',$smarty.session.permissions)}}
							<a href="models.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_model}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_model}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==5}}
						{{if in_array('dvds|view',$smarty.session.permissions)}}
							<a href="dvds.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==12}}
						{{if in_array('posts|view',$smarty.session.permissions)}}
							<a href="posts.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.object_type_id==13}}
						{{if in_array('playlists|view',$smarty.session.permissions)}}
							<a href="playlists.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.website_link!=''}}
				<tr data-field-name="website_link">
					<td class="de_label">{{$lang.users.comment_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.comment_field_user}}</td>
				<td class="de_control">
					{{if $smarty.get.action=='add_new'}}
						<table class="control_group de_vis_sw_radio">
							<tr>
								<td>
									<span class="de_lv_pair"><input id="user_type_registered" type="radio" name="user_type" value="1" checked/><label>{{$lang.users.comment_field_user_registered}}:</label></span>
									<span class="insight">
										<span class="js_params">
											<span class="js_param">url=async/insight.php?type=users</span>
										</span>
										<input type="text" name="user" maxlength="255" size="30" class="user_type_registered"/>
									</span>
									<span class="de_hint">{{$lang.users.comment_field_user_hint1}}</span>
								</td>
							</tr>
							<tr>
								<td>
									<span class="de_lv_pair"><input id="user_type_anonymous" type="radio" name="user_type" value="2"/><label>{{$lang.users.comment_field_user_anonymous}}:</label></span>
									<input type="text" name="anonymous_username" maxlength="255" size="30" class="user_type_anonymous"/>
									<span class="de_hint">{{$lang.users.comment_field_user_hint2}}</span>
								</td>
							</tr>
						</table>
					{{else}}
						{{if $smarty.post.user_status_id==4}}
							<span>{{$smarty.post.user}}</span>
						{{else}}
							{{if in_array('users|view',$smarty.session.permissions)}}
								<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
							{{else}}
								<span>{{$smarty.post.user}}</span>
							{{/if}}
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.get.action!='add_new' && $config.safe_mode!='true'}}
				<tr>
					<td class="de_label">{{$lang.users.comment_field_ip}}</td>
					<td class="de_control">
						<span>{{$smarty.post.ip}} {{if $smarty.post.country!=''}}({{$smarty.post.country}}){{/if}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.users.comment_field_rating}}</td>
					<td class="de_control">
						<span>{{$lang.users.comment_field_rating_value|replace:"%1%":$smarty.post.rating|replace:"%2%":$smarty.post.likes|replace:"%3%":$smarty.post.dislikes}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.comment_field_added_date}}</td>
				<td class="de_control">
					{{if $smarty.get.action=='add_new'}}
						<span class="calendar">
							<span class="js_params">
								<span class="js_param">type=datetime</span>
							</span>
							<input type="text" name="added_date" placeholder="{{$lang.common.select_default_option}}" value="{{$smarty.post.added_date}}">
						</span>
						<span class="de_hint">
							{{if $smarty.post.object_post_date==''}}
								{{$lang.users.comment_field_added_date_hint1}}
							{{else}}
								{{assign var="object_post_date" value=$smarty.post.object_post_date|date_format:$smarty.session.userdata.full_date_format}}
								{{$lang.users.comment_field_added_date_hint2|replace:"%1%":$object_post_date}}
							{{/if}}
						</span>
					{{else}}
						<span>{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.users.comment_field_comment}}</td>
				<td class="de_control">
					<textarea name="comment" rows="10" cols="40">{{$smarty.post.comment}}</textarea>
					{{if $smarty.post.comment_id>0 && $smarty.post.comment==''}}
						<span class="de_hint">{{$lang.users.comment_field_comment_deleted}}</span>
					{{/if}}
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
			<input type="hidden" name="object_id" value="{{$smarty.get.object_id}}"/>
			<input type="hidden" name="object_type_id" value="{{$smarty.get.object_type_id}}"/>
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

{{if in_array('users|manage_comments',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('users|manage_comments',$smarty.session.permissions)}}
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
						<option value="">{{$lang.users.comment_filter_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id==1}}selected{{/if}}>{{$lang.users.comment_filter_status_new}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id==2}}selected{{/if}}>{{$lang.users.comment_filter_status_approved}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_status_id==3}}selected{{/if}}>{{$lang.users.comment_filter_status_not_approved}}</option>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.users.comment_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.users.comment_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_object_type_id">
						<option value="">{{$lang.common.object_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected{{/if}}>{{$lang.common.object_type_videos}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected{{/if}}>{{$lang.common.object_type_albums}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_object_type_id==3}}selected{{/if}}>{{$lang.common.object_type_content_sources}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_object_type_id==4}}selected{{/if}}>{{$lang.common.object_type_models}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected{{/if}}>{{$lang.common.object_type_dvds}}</option>
						<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected{{/if}}>{{$lang.common.object_type_posts}}</option>
						<option value="13" {{if $smarty.session.save.$page_name.se_object_type_id==13}}selected{{/if}}>{{$lang.common.object_type_playlists}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_object_id" value="{{$smarty.session.save.$page_name.se_object_id}}" placeholder="{{$lang.common.object_id}}..."/>
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_approved==0}}disabled{{/if}}">
							<td class="dg_selector">
								<input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/>
								<input type="hidden" name="row_all[]" value="{{$item.$table_key_name}}"/>
								<span class="js_params">
									{{if $item.is_review_needed==1}}
										<span class="js_param">new=true</span>
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
										<span class="js_param">name={{$item.$table_key_name}}</span>
										<span class="js_param">object_id={{$item.object_id}}</span>
										<span class="js_param">object_type_id={{$item.object_type_id}}</span>
										{{if $item.is_review_needed==0}}
											<span class="js_param">approve_hide=true</span>
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
						<span class="js_param">href=?batch_action=approve&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.users.comment_action_approve}}</span>
						<span class="js_param">hide=${approve_hide}</span>
						<span class="js_param">icon=action-approve</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=?action=add_new&amp;object_type_id=${object_type_id}&amp;object_id=${object_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=type-comment</span>
					<span class="js_param">subicon=action-add</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=${website_link}</span>
					<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
					<span class="js_param">disable=${website_link_disable}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=action-open</span>
				</li>
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=15&amp;se_object_id=${id}</span>
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
							{{if $new_comments_count>0}}
								<option value="delete_new">{{$lang.users.comment_batch_action_delete_new|replace:"%1%":$new_comments_count}}</option>
							{{/if}}
							{{if $can_edit==1}}
								<option value="delete_and_approve">{{$lang.users.comment_batch_action_delete_and_approve|replace:"%1%":'${count}'|replace:"%2%":'${new_inverted}'}}</option>
							{{/if}}
						</optgroup>
					{{/if}}
					{{if $can_edit==1}}
						<optgroup label="{{$lang.common.dg_batch_groups_status}}">
							<option value="approve">{{$lang.users.comment_batch_action_approve|replace:"%1%":'${count}'}}</option>
							{{if $can_delete==1}}
								<option value="approve_and_delete">{{$lang.users.comment_batch_action_approve_and_delete|replace:"%1%":'${count}'|replace:"%2%":'${new_inverted}'}}</option>
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
					<span class="js_param">value=delete_new</span>
					<span class="js_param">confirm={{$lang.users.comment_batch_action_delete_new_confirm|count_format:"%1%":$new_comments_count}}</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=approve_and_delete</span>
					<span class="js_param">confirm={{$lang.users.comment_batch_action_approve_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":'${new_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_approve</span>
					<span class="js_param">confirm={{$lang.users.comment_batch_action_delete_and_approve_confirm|replace:"%1%":'${count}'|replace:"%2%":'${new_inverted}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}