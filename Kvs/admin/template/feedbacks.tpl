{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

{{if in_array('feedbacks|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="feedback_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_feedbacks}}</a> / {{$lang.users.feedback_edit|replace:"%1%":$smarty.post.feedback_id}}</h1></div>
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
			{{if $smarty.post.status_id==1 && $smarty.post.email!=''}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.feedback_divider_user_entry}}</h2></td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_status}}</td>
				<td class="de_control">
					<select name="status_id">
						<option value="1" {{if $smarty.post.status_id==1}}selected{{/if}}>{{$lang.users.feedback_field_status_new}}</option>
						<option value="2" {{if $smarty.post.status_id==2}}selected{{/if}}>{{if $smarty.post.status_id==2 && $smarty.post.response!=''}}{{$lang.users.feedback_field_status_replied}}{{else}}{{$lang.users.feedback_field_status_closed}}{{/if}}</option>
					</select>
				</td>
			</tr>
			{{if $smarty.post.user_id>0}}
				<tr>
					<td class="de_label">{{$lang.users.feedback_field_user}}</td>
					<td class="de_control">
						{{if $smarty.post.user!=''}}
							{{if in_array('users|view',$smarty.session.permissions)}}
								<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
							{{else}}
								<span>{{$smarty.post.user}}</span>
							{{/if}}
						{{else}}
							<span>{{$lang.common.user_deleted|replace:"%1%":$smarty.post.user_id}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_ip}}</td>
				<td class="de_control">
					<span>{{$smarty.post.ip}}{{if $smarty.post.country!=''}} ({{$smarty.post.country}}){{/if}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_user_agent}}</td>
				<td class="de_control">
					<span>{{$smarty.post.user_agent}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_referer}}</td>
				<td class="de_control">
					{{if $smarty.post.referer!=''}}
						<a href="{{$smarty.post.referer}}">{{$smarty.post.referer}}</a>
					{{/if}}
				</td>
			</tr>
			{{if $options.ENABLE_FEEDBACK_FIELD_1==1}}
				<tr>
					<td class="de_label">{{$options.FEEDBACK_FIELD_1_NAME}}</td>
					<td class="de_control">
						<span>{{$smarty.post.custom1}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_FEEDBACK_FIELD_2==1}}
				<tr>
					<td class="de_label">{{$options.FEEDBACK_FIELD_2_NAME}}</td>
					<td class="de_control">
						<span>{{$smarty.post.custom2}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_FEEDBACK_FIELD_3==1}}
				<tr>
					<td class="de_label">{{$options.FEEDBACK_FIELD_3_NAME}}</td>
					<td class="de_control">
						<span>{{$smarty.post.custom3}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_FEEDBACK_FIELD_4==1}}
				<tr>
					<td class="de_label">{{$options.FEEDBACK_FIELD_4_NAME}}</td>
					<td class="de_control">
						<span>{{$smarty.post.custom4}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_FEEDBACK_FIELD_5==1}}
				<tr>
					<td class="de_label">{{$options.FEEDBACK_FIELD_5_NAME}}</td>
					<td class="de_control">
						<span>{{$smarty.post.custom5}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.email!=''}}
				<tr>
					<td class="de_label">{{$lang.users.feedback_field_email}}</td>
					<td class="de_control">
						<a href="mailto:{{$smarty.post.email}}">{{$smarty.post.email}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_added_date}}</td>
				<td class="de_control">
					<span>{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_closed_date}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.closed_date!='0000-00-00 00:00:00'}}
							{{$smarty.post.closed_date|date_format:$smarty.session.userdata.full_date_format}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_subject}}</td>
				<td class="de_control">
					<input type="text" value="{{$smarty.post.subject}}" readonly/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_message}}</td>
				<td class="de_control">
					<textarea rows="5" cols="40" readonly>{{$smarty.post.message}}</textarea>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_mentioned_videos}}</td>
				<td class="de_control">
					{{if count($smarty.post.mentioned_videos)>0}}
						<table class="control_group">
							{{if $smarty.post.mentioned_videos_select_id>0}}
								<tr>
									<td><a href="videos.php?no_filter=true&amp;se_file_ids={{$smarty.post.mentioned_videos_select_id}}">{{$lang.users.feedback_field_mentioned_videos_select}}</a></td>
								</tr>
							{{/if}}
							<tr>
								<td>
									<div class="de_img_list" data-viewer-control="iframe">
										<div class="de_img_list_main">
											{{assign var="max_thumb_size" value=$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}
											{{assign var="max_thumb_size" value="x"|explode:$max_thumb_size}}
											{{foreach item="mentioned_video" from=$smarty.post.mentioned_videos|smarty:nodefaults}}
												<div class="de_img_list_item" style="{{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0+20}}px{{/if}};">
													<a class="de_img_list_thumb" href="preview_video.php?video_id={{$mentioned_video.video_id}}" style="{{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0+10}}px{{/if}};">
														<img src="{{$mentioned_video.preview_url}}?rnd={{$smarty.now}}" style="object-fit: contain; {{if $max_thumb_size.0>0}}width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.0>0}}height: {{$max_thumb_size.0*9/16}}px{{/if}}" alt="{{$mentioned_video.title}}"/>
														<i>
															{{$mentioned_video.duration|durationToHumanString}}
															<em class="separator"></em>
															<em class="icon icon-type-visit"></em> {{$mentioned_video.video_viewed|traffic_format}}
														</i>
														{{if $mentioned_video.status_id==4 || $mentioned_video.status_id==5}}
															<span class="banner">{{$lang.videos.video_field_status_deleted}}</span>
														{{/if}}
													</a>
													<div class="de_img_list_options">
														{{if in_array('videos|view', $smarty.session.permissions)}}
															<a href="videos.php?action=change&amp;item_id={{$mentioned_video.video_id}}" title="{{$mentioned_video.title}}">{{$mentioned_video.title}}</a>
														{{else}}
															{{$mentioned_video.title}}
														{{/if}}
													</div>
												</div>
											{{/foreach}}
										</div>
									</div>
								</td>
							</tr>
						</table>
					{{else}}
						<span>{{$lang.users.feedback_field_mentioned_videos_none}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.status_id==1 && $smarty.post.email!=''}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.users.feedback_divider_response}}</h2></td>
				</tr>
				<tr>
					<td></td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="do_reply" value="1"/><label>{{$lang.users.feedback_field_response_do_reply}}</label></span>
					</td>
				</tr>
				<tr>
					<td class="de_label do_reply_on">{{$lang.users.feedback_field_response_subject}}</td>
					<td class="de_control">
						<input type="text" name="response_subject" class="do_reply_on" value="{{if $smarty.post.subject!=''}}RE: {{$smarty.post.subject}}{{else}}{{$smarty.session.save.$page_name.subject}}{{/if}}"/>
					</td>
				</tr>
				<tr>
					<td class="de_label do_reply_on">{{$lang.users.feedback_field_response_headers}}</td>
					<td class="de_control">
						<textarea name="response_headers" class="do_reply_on" rows="5" cols="40">{{$smarty.session.save.$page_name.headers|default:$config.default_email_headers}}</textarea>
					</td>
				</tr>
				<tr>
					<td class="de_label do_reply_on">{{$lang.users.feedback_field_response_body}}</td>
					<td class="de_control">
						<textarea name="response_body" class="do_reply_on" rows="10" cols="40">{{$smarty.post.response}}</textarea>
					</td>
				</tr>
				{{if $smarty.post.previous_response!=''}}
					<tr>
						<td class="de_label">{{$lang.users.feedback_field_response_previous}}</td>
						<td class="de_control">
							<textarea rows="10" cols="40" readonly>{{$smarty.post.previous_response}}</textarea>
						</td>
					</tr>
				{{/if}}
			{{elseif $smarty.post.response!=''}}
				<tr>
					<td class="de_label">{{$lang.users.feedback_field_response_body}}</td>
					<td class="de_control">
						<textarea rows="10" cols="40" readonly>{{$smarty.post.response}}</textarea>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{if $smarty.post.email!=''}}
			<input type="hidden" name="response_email" value="{{$smarty.post.email}}"/>
		{{/if}}
		<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
		<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
	</div>
</form>

{{else}}

{{if in_array('feedbacks|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('feedbacks|edit_all',$smarty.session.permissions)}}
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
						<option value="">{{$lang.users.feedback_field_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id==1}}selected{{/if}}>{{$lang.users.feedback_field_status_new}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_status_id==2}}selected{{/if}}>{{$lang.users.feedback_field_status_closed}}</option>
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
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.users.feedback_field_ip}}..."/>
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.$table_key_name}}</span>
										{{if $item.status_id==2 || $item.status_id==21}}
											<span class="js_param">close_hide=true</span>
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
						<span class="js_param">desructive=true</span>
					</li>
				{{/if}}
				{{if $can_edit==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=close&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.users.feedback_action_mark_closed}}</span>
						<span class="js_param">confirm={{$lang.users.feedback_action_mark_closed_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">hide=${close_hide}</span>
						<span class="js_param">icon=action-approve</span>
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
						<option value="close">{{$lang.users.feedback_batch_action_mark_closed|replace:"%1%":'${count}'}}</option>
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
					<span class="js_param">value=close</span>
					<span class="js_param">confirm={{$lang.users.feedback_batch_action_mark_closed_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}