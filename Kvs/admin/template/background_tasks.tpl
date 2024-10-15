{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

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
						<option value="">{{$lang.settings.background_task_field_status}}...</option>
						{{foreach from=$list_status_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_status_id!='' && $smarty.session.save.$page_name.se_status_id=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_type_id">
						<option value="">{{$lang.settings.background_task_field_type}}...</option>
						{{foreach from=$list_type_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_type_id>0 && $smarty.session.save.$page_name.se_type_id=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_server_id">
						<option value="">{{$lang.settings.background_task_field_server}}...</option>
						{{foreach from=$list_conversion_servers|smarty:nodefaults item="item"}}
							<option value="{{$item.server_id}}" {{if $smarty.session.save.$page_name.se_server_id==$item.server_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_error_code">
						<option value="">{{$lang.settings.background_task_field_error_code}}...</option>
						{{foreach from=$list_error_code_values|smarty:nodefaults key="id" item="value"}}
							<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_error_code>0 && $smarty.session.save.$page_name.se_error_code=="`$id`"}}selected{{/if}}>{{$value}}</option>
						{{/foreach}}
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
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a class="edit disabled" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										{{if $item.status_id!=2}}
											<span class="js_param">restart_hide=true</span>
										{{/if}}
										{{if $item.status_id!=0}}
											<span class="js_param">inc_priority_hide=true</span>
										{{/if}}
										{{if $item.video_id>0}}
											<span class="js_param">video_id={{$item.video_id}}</span>
										{{else}}
											<span class="js_param">video_log_hide=true</span>
										{{/if}}
										{{if $item.album_id>0}}
											<span class="js_param">album_id={{$item.album_id}}</span>
										{{else}}
											<span class="js_param">album_log_hide=true</span>
										{{/if}}
										{{if $item.server_id<1}}
											<span class="js_param">conversion_log_disable=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.background_task_action_delete_confirm|replace:"%1%":'${id}'}}</span>
					<span class="js_param">icon=action-delete</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_restart}}</span>
					<span class="js_param">hide=${restart_hide}</span>
					<span class="js_param">icon=action-redo</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=inc_priority&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_inc_priority}}</span>
					<span class="js_param">hide=${inc_priority_hide}</span>
					<span class="js_param">icon=action-pump</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=task_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_task}}</span>
					<span class="js_param">disable=${task_log_disable}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=action-log</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=videos.php?action=video_log&amp;item_id=${video_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_video}}</span>
					<span class="js_param">hide=${video_log_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-video</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=albums.php?action=album_log&amp;item_id=${album_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_album}}</span>
					<span class="js_param">hide=${album_log_hide}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-album</span>
					<span class="js_param">subicon=action-search</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=conversion_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_conversion}}</span>
					<span class="js_param">disable=${conversion_log_disable}</span>
					<span class="js_param">popup=true</span>
					<span class="js_param">icon=type-conversion</span>
					<span class="js_param">subicon=action-search</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
						<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
						{{if $total_num>0}}
							<option value="delete_all">{{$lang.settings.background_task_batch_delete_all|replace:"%1%":$total_num}}</option>
						{{/if}}
						{{if $failed_num>0}}
							<option value="delete_failed">{{$lang.settings.background_task_batch_delete_failed|replace:"%1%":$failed_num}}</option>
						{{/if}}
						<option value="inc_priority">{{$lang.settings.background_task_batch_inc_priority|replace:"%1%":'${count}'}}</option>
						<option value="restart">{{$lang.settings.background_task_batch_restart_selected|replace:"%1%":'${count}'}}</option>
						{{if $failed_num>0}}
							<option value="restart_failed">{{$lang.settings.background_task_batch_restart_failed|replace:"%1%":$failed_num}}</option>
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
					<span class="js_param">value=delete_all</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_delete_all_confirm|count_format:"%1%":$total_num}}</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_failed</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_delete_failed_confirm|count_format:"%1%":$failed_num}}</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=restart</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_restart_selected_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=restart_failed</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_restart_failed_confirm|count_format:"%1%":$failed_num}}</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">requires_selection=false</span>
				</li>
			</ul>
		</div>
	</form>
</div>