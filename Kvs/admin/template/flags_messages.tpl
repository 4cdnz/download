{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="flag_message_edit">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.users.submenu_option_flags_messages}}</a> / {{$lang.users.flag_message_edit|replace:"%1%":$smarty.post.flag_message_id}}</h1></div>
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
				<td class="de_label">{{$lang.users.flag_message_field_flag}}</td>
				<td class="de_control">
					<span>{{$smarty.post.flag}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_object}}</td>
				<td class="de_control">
					{{if $smarty.post.video_id>0}}
						{{if in_array('videos|view',$smarty.session.permissions)}}
							<a href="videos.php?action=change&amp;item_id={{$smarty.post.video_id}}">{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.album_id>0}}
						{{if in_array('albums|view',$smarty.session.permissions)}}
							<a href="albums.php?action=change&amp;item_id={{$smarty.post.album_id}}">{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.dvd_id>0}}
						{{if in_array('dvds|view',$smarty.session.permissions)}}
							<a href="dvds.php?action=change&amp;item_id={{$smarty.post.dvd_id}}">{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.post_id>0}}
						{{if in_array('posts|view',$smarty.session.permissions)}}
							<a href="posts.php?action=change&amp;item_id={{$smarty.post.post_id}}">{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{elseif $smarty.post.playlist_id>0}}
						{{if in_array('playlists|view',$smarty.session.permissions)}}
							<a href="playlists.php?action=change&amp;item_id={{$smarty.post.playlist_id}}">{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</a>
						{{else}}
							<span>{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_ip}}</td>
				<td class="de_control">
					<span>{{$smarty.post.ip}}{{if $smarty.post.country!=''}} ({{$smarty.post.country}}){{/if}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_user_agent}}</td>
				<td class="de_control">
					<span>{{$smarty.post.user_agent}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_referer}}</td>
				<td class="de_control">
					{{if $smarty.post.referer!=''}}
						<span><a href="{{$smarty.post.referer}}">{{$smarty.post.referer}}</a></span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_added_date}}</td>
				<td class="de_control">
					<span>{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.flag_message_field_message}}</td>
				<td class="de_control">
					<span>{{$smarty.post.message}}</span>
				</td>
			</tr>
		</table>
	</div>
</form>

{{else}}

{{if in_array('feedbacks|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
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
					<select name="se_flag_id">
						<option value="">{{$lang.users.flag_message_field_flag}}...</option>
						{{foreach from=$list_flags_grouped|smarty:nodefaults item="list_flags" key="group_id"}}
							{{if $group_id==1}}
								{{assign var="group_title" value=$lang.common.object_type_videos}}
							{{elseif $group_id==2}}
								{{assign var="group_title" value=$lang.common.object_type_albums}}
							{{elseif $group_id==3}}
								{{assign var="group_title" value=$lang.common.object_type_dvds}}
							{{elseif $group_id==4}}
								{{assign var="group_title" value=$lang.common.object_type_posts}}
							{{elseif $group_id==5}}
								{{assign var="group_title" value=$lang.common.object_type_playlists}}
							{{/if}}
							<optgroup label="{{$group_title}}">
								{{foreach from=$list_flags|smarty:nodefaults item="flag"}}
									<option value="{{$flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$flag.flag_id}}selected{{/if}}>{{$flag.title}}</option>
								{{/foreach}}
							</optgroup>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_ip" value="{{$smarty.session.save.$page_name.se_ip}}" placeholder="{{$lang.users.flag_message_field_ip}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_object_type_id">
						<option value="">{{$lang.common.object_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected{{/if}}>{{$lang.common.object_type_videos}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected{{/if}}>{{$lang.common.object_type_albums}}</option>
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
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
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