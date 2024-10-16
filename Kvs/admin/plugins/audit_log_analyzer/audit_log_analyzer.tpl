{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.audit_log_analyzer.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.audit_log_analyzer.long_desc}}
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
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.audit_log_analyzer.divider_parameters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_period}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="1" {{if $smarty.post.period_type==1}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_today}} ({{$smarty.post.today|date_format:$smarty.session.userdata.short_date_format}})</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="2" {{if $smarty.post.period_type==2}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_yesterday}} ({{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="3" {{if $smarty.post.period_type==3}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_last_7days}} ({{$smarty.post.week|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="4" {{if $smarty.post.period_type==4}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_last_30days}} ({{$smarty.post.month|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="6" {{if $smarty.post.period_type==6}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_prev_month}} ({{$smarty.post.month_start|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.month_end|date_format:$smarty.session.userdata.short_date_format}})</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="radio" name="period_type" value="5" {{if $smarty.post.period_type==5}}checked{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_custom}}</label></span></td>
						</tr>
						<tr>
							<td class="de_dependent">
								<span>
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_from}}:
									<span class="calendar">
										<input class="period_type_5" type="text" name="period_custom_date_from" value="{{$smarty.post.period_custom_date_from}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
								<span>
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_to}}:
									<span class="calendar">
										<input class="period_type_5" type="text" name="period_custom_date_to" value="{{$smarty.post.period_custom_date_to}}" placeholder="{{$lang.common.select_default_option}}">
									</span>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_admins}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.plugins.audit_log_analyzer.field_admins}}</span>
							<span class="js_param">url=async/insight.php?type=admins</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=admin_ids[]</span>
							<span class="js_param">empty_message={{$lang.plugins.audit_log_analyzer.field_admins_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.admins|smarty:nodefaults}}
							<input type="hidden" name="admin_ids[]" value="{{$item.user_id}}" alt="{{$item.login}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_user" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.plugins.audit_log_analyzer.field_admins_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_users}}</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.plugins.audit_log_analyzer.field_users}}</span>
							<span class="js_param">url=async/insight.php?type=users</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=user_ids[]</span>
							<span class="js_param">empty_message={{$lang.plugins.audit_log_analyzer.field_users_empty}}</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.users|smarty:nodefaults}}
							<input type="hidden" name="user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_user" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.plugins.audit_log_analyzer.field_users_all}}"/>
						</div>
					</div>
				</td>
			</tr>
			{{if $smarty.get.action=='results'}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.audit_log_analyzer.divider_summary}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td>{{$lang.plugins.audit_log_analyzer.field_results_period}}: {{$smarty.post.period_start|date_format:$smarty.session.userdata.full_date_format}} - {{$smarty.post.period_end|date_format:$smarty.session.userdata.full_date_format}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.username}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_added}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.videos_added}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_added}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.albums_added}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_added}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.posts_added}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_added}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.other_added}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.videos_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.albums_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.posts_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.other_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_vs_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.vs_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_ai_modified}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.ai_modified}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_deleted}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.videos_deleted}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_deleted}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.albums_deleted}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_deleted}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.posts_deleted}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_deleted}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.other_deleted}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_translated}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.videos_translated}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_translated}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.albums_translated}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_translated}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.other_translated}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_text_symbols}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.text_symbols}}</td>
								{{/foreach}}
							</tr>
							<tr class="eg_data">
								<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_translation_symbols}}</td>
								{{foreach item="item" from=$smarty.post.result|smarty:nodefaults}}
									<td>{{$item.translation_symbols}}</td>
								{{/foreach}}
							</tr>
						</table>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="calculate"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.audit_log_analyzer.btn_calculate}}"/>
	</div>
</form>