{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
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
					<select name="se_object_type_id">
						<option value="">{{$lang.common.object_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected{{/if}}>{{$lang.common.object_type_videos}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected{{/if}}>{{$lang.common.object_type_albums}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_object_type_id==3}}selected{{/if}}>{{$lang.common.object_type_content_sources}}</option>
						<option value="8" {{if $smarty.session.save.$page_name.se_object_type_id==8}}selected{{/if}}>{{$lang.common.object_type_content_source_groups}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_object_type_id==4}}selected{{/if}}>{{$lang.common.object_type_models}}</option>
						<option value="14" {{if $smarty.session.save.$page_name.se_object_type_id==14}}selected{{/if}}>{{$lang.common.object_type_model_groups}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected{{/if}}>{{$lang.common.object_type_dvds}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_object_type_id==10}}selected{{/if}}>{{$lang.common.object_type_dvd_groups}}</option>
						<option value="6" {{if $smarty.session.save.$page_name.se_object_type_id==6}}selected{{/if}}>{{$lang.common.object_type_categories}}</option>
						<option value="7" {{if $smarty.session.save.$page_name.se_object_type_id==7}}selected{{/if}}>{{$lang.common.object_type_category_groups}}</option>
						<option value="9" {{if $smarty.session.save.$page_name.se_object_type_id==9}}selected{{/if}}>{{$lang.common.object_type_tags}}</option>
						<option value="11" {{if $smarty.session.save.$page_name.se_object_type_id==11}}selected{{/if}}>{{$lang.common.object_type_post_types}}</option>
						<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected{{/if}}>{{$lang.common.object_type_posts}}</option>
						<option value="13" {{if $smarty.session.save.$page_name.se_object_type_id==13}}selected{{/if}}>{{$lang.common.object_type_playlists}}</option>
						<option value="15" {{if $smarty.session.save.$page_name.se_object_type_id==15}}selected{{/if}}>{{$lang.common.object_type_comments}}</option>
						<option value="30" {{if $smarty.session.save.$page_name.se_object_type_id==30}}selected{{/if}}>{{$lang.common.object_type_settings}}</option>
						<option value="31" {{if $smarty.session.save.$page_name.se_object_type_id==31}}selected{{/if}}>{{$lang.common.object_type_lookups}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_object_id" value="{{if $smarty.session.save.$page_name.se_object_id>0}}{{$smarty.session.save.$page_name.se_object_id}}{{/if}}" placeholder="{{$lang.common.object_id}}..."/>
				</div>
				<div class="dgf_filter">
					<select name="se_action_type_id">
						<option value="">{{$lang.settings.audit_log_field_action}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_action_type_id==1}}selected{{/if}}>{{$lang.settings.audit_log_field_action_added_object_manually}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_action_type_id==2}}selected{{/if}}>{{$lang.settings.audit_log_field_action_added_object_import}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_action_type_id==3}}selected{{/if}}>{{$lang.settings.audit_log_field_action_added_object_feed}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_action_type_id==4}}selected{{/if}}>{{$lang.settings.audit_log_field_action_added_object_plugin}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_action_type_id==5}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_object}}</option>
						<option value="6" {{if $smarty.session.save.$page_name.se_action_type_id==6}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_video_screenshots}}</option>
						<option value="7" {{if $smarty.session.save.$page_name.se_action_type_id==7}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_album_images}}</option>
						<option value="8" {{if $smarty.session.save.$page_name.se_action_type_id==8}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_object_massedit}}</option>
						<option value="165" {{if $smarty.session.save.$page_name.se_action_type_id==165}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_object_import}}</option>
						<option value="168" {{if $smarty.session.save.$page_name.se_action_type_id==168}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_object_plugin}}</option>
						<option value="9" {{if $smarty.session.save.$page_name.se_action_type_id==9}}selected{{/if}}>{{$lang.settings.audit_log_field_action_deleted_object}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_action_type_id==10}}selected{{/if}}>{{$lang.settings.audit_log_field_action_translated_object}}</option>
						<option value="220" {{if $smarty.session.save.$page_name.se_action_type_id==220}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_content_settings}}</option>
						<option value="221" {{if $smarty.session.save.$page_name.se_action_type_id==221}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_website_settings}}</option>
						<option value="222" {{if $smarty.session.save.$page_name.se_action_type_id==222}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_memberzone_settings}}</option>
						<option value="223" {{if $smarty.session.save.$page_name.se_action_type_id==223}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_stats_settings}}</option>
						<option value="224" {{if $smarty.session.save.$page_name.se_action_type_id==224}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_customization_settings}}</option>
						<option value="225" {{if $smarty.session.save.$page_name.se_action_type_id==225}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_player_settings}}</option>
						<option value="226" {{if $smarty.session.save.$page_name.se_action_type_id==226}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_embed_settings}}</option>
						<option value="227" {{if $smarty.session.save.$page_name.se_action_type_id==227}}selected{{/if}}>{{$lang.settings.audit_log_field_action_modified_antispam_settings}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_admin_id">
						<option value="">{{$lang.settings.audit_log_filter_admin}}...</option>
						<option value="system" {{if $smarty.session.save.$page_name.se_admin_id=='system'}}selected{{/if}}>{{$lang.settings.audit_log_filter_admin_system}}</option>
                        {{foreach item="item" from=$list_admins|smarty:nodefaults}}
							<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_id==$item.user_id}}selected{{/if}}>{{$item.login}}</option>
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
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.settings.audit_log_filter_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter de_vis_sw_select">
					<select name="se_period_id">
						<option value="">{{$lang.stats.common_period}}...</option>
						<option value="today" {{if $smarty.session.save.$page_name.se_period_id=='today'}}selected{{/if}}>{{$lang.stats.common_period_today}}</option>
						<option value="yesterday" {{if $smarty.session.save.$page_name.se_period_id=='yesterday'}}selected{{/if}}>{{$lang.stats.common_period_yesterday}}</option>
						<option value="days7" {{if $smarty.session.save.$page_name.se_period_id=='days7'}}selected{{/if}}>{{$lang.stats.common_period_days7}}</option>
						<option value="days30" {{if $smarty.session.save.$page_name.se_period_id=='days30'}}selected{{/if}}>{{$lang.stats.common_period_days30}}</option>
						<option value="current_month" {{if $smarty.session.save.$page_name.se_period_id=='current_month'}}selected{{/if}}>{{$lang.stats.common_period_current_month}}</option>
						<option value="prev_month" {{if $smarty.session.save.$page_name.se_period_id=='prev_month'}}selected{{/if}}>{{$lang.stats.common_period_prev_month}}</option>
						<option value="custom" {{if $smarty.session.save.$page_name.se_period_id=='custom'}}selected{{/if}}>{{$lang.stats.common_period_custom}}</option>
					</select>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_from" value="{{$smarty.session.save.$page_name.se_date_from}}" placeholder="{{$lang.stats.common_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter se_period_id_custom">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_date_to" value="{{$smarty.session.save.$page_name.se_date_to}}" placeholder="{{$lang.stats.common_date_to}}...">
					</div>
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
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						{{assign var="table_columns_display_mode" value="header"}}
						{{include file="table_columns_inc.tpl"}}
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=1}}
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
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
			</div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>
		</div>
	</form>
</div>