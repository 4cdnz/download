{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_filter">
				<div class="drop">
					<i class="icon icon-action-groupby"></i><span>{{$lang.stats.users_awards_filter_group_by}}</span>
					<ul>
						<li {{if $smarty.session.save.$page_name.se_group_by=='user'}}class="selected"{{/if}}><a href="{{$page_name}}?se_group_by=user">{{$lang.stats.users_awards_filter_group_by_user}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_group_by=='type'}}class="selected"{{/if}}><a href="{{$page_name}}?se_group_by=type">{{$lang.stats.users_awards_filter_group_by_type}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_group_by=='log'}}class="selected"{{/if}}><a href="{{$page_name}}?se_group_by=log">{{$lang.stats.users_awards_filter_group_by_full}}</a></li>
					</ul>
				</div>
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
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=users</span>
							{{if in_array('users|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.stats.users_awards_field_user}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<select name="se_object_type_id">
						<option value="">{{$lang.common.object_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected{{/if}}>{{$lang.common.object_type_videos}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected{{/if}}>{{$lang.common.object_type_albums}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected{{/if}}>{{$lang.common.object_type_dvds}}</option>
						<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected{{/if}}>{{$lang.common.object_type_posts}}</option>
						<option value="15" {{if $smarty.session.save.$page_name.se_object_type_id==15}}selected{{/if}}>{{$lang.common.object_type_comments}}</option>
						<option value="20" {{if $smarty.session.save.$page_name.se_object_type_id==20}}selected{{/if}}>{{$lang.common.object_type_profiles}}</option>
						<option value="21" {{if $smarty.session.save.$page_name.se_object_type_id==21}}selected{{/if}}>{{$lang.common.object_type_messages}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<input type="text" name="se_object_id" value="{{$smarty.session.save.$page_name.se_object_id}}" placeholder="{{$lang.common.object_id}}"/>
				</div>
				<div class="dgf_filter">
					<select name="se_award_type">
						<option value="">{{$lang.stats.users_awards_field_award_type}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_award_type==1}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_signup}}</option>
						<option value="15" {{if $smarty.session.save.$page_name.se_award_type==15}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_login}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_award_type==2}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_avatar}}</option>
						<option value="16" {{if $smarty.session.save.$page_name.se_award_type==16}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_cover}}</option>
						<option value="3" {{if $smarty.session.save.$page_name.se_award_type==3}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_comment}}</option>
						<option value="4" {{if $smarty.session.save.$page_name.se_award_type==4}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_video_upload}}</option>
						<option value="5" {{if $smarty.session.save.$page_name.se_award_type==5}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_album_upload}}</option>
						<option value="9" {{if $smarty.session.save.$page_name.se_award_type==9}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_post_upload}}</option>
						<option value="6" {{if $smarty.session.save.$page_name.se_award_type==6}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_video_sale}}</option>
						<option value="7" {{if $smarty.session.save.$page_name.se_award_type==7}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_album_sale}}</option>
						<option value="13" {{if $smarty.session.save.$page_name.se_award_type==13}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_profile_sale}}</option>
						<option value="14" {{if $smarty.session.save.$page_name.se_award_type==14}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_dvd_sale}}</option>
						<option value="8" {{if $smarty.session.save.$page_name.se_award_type==8}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_referral}}</option>
						<option value="10" {{if $smarty.session.save.$page_name.se_award_type==10}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_donate}}</option>
						<option value="18" {{if $smarty.session.save.$page_name.se_award_type==18}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_messages}}</option>
						<option value="11" {{if $smarty.session.save.$page_name.se_award_type==11}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_video_views}}</option>
						<option value="12" {{if $smarty.session.save.$page_name.se_award_type==12}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_album_views}}</option>
						<option value="17" {{if $smarty.session.save.$page_name.se_award_type==17}}selected{{/if}}>{{$lang.stats.users_awards_field_award_type_embed_views}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_payout">
						<option value="">{{$lang.stats.users_awards_filter_payout}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_payout==1}}selected{{/if}}>{{$lang.stats.users_awards_filter_payout_done}}</option>
						<option value="2" {{if $smarty.session.save.$page_name.se_payout==2}}selected{{/if}}>{{$lang.stats.users_awards_filter_payout_not_done}}</option>
					</select>
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
					{{if $smarty.session.save.$page_name.se_group_by=='log'}}
						<col width="1%"/>
					{{/if}}
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $smarty.session.save.$page_name.se_group_by!='log'}}disabled{{/if}}/><span></span></td>
						{{assign var="table_columns_display_mode" value="header"}}
						{{include file="table_columns_inc.tpl"}}
						{{if $smarty.session.save.$page_name.se_group_by=='log'}}
							<td>{{$lang.common.dg_actions}}</td>
						{{/if}}
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=1}}
					{{if $smarty.session.save.$page_name.se_group_by=='log'}}
						{{assign var="table_columns_visible" value=2}}
					{{/if}}
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
					{{assign var="table_columns_average" value=$average}}
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $smarty.session.save.$page_name.se_group_by!='log' || $item.payout_id!=0 || $item.tokens_granted==0}}disabled{{/if}}/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							{{if $smarty.session.save.$page_name.se_group_by=='log'}}
								<td class="nowrap">
									<a class="edit disabled" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									{{if $item.payout_id==0 && $item.tokens_granted>0}}
										<a class="additional" title="{{$lang.common.dg_actions_additional}}">
											<i class="icon icon-action-settings"></i>
											<span class="js_params">
												<span class="js_param">id={{$item.$table_key_name}}</span>
												<span class="js_param">tokens={{$item.tokens_granted}}</span>
												<span class="js_param">user={{$item.user|default:$item.user_id}}</span>
											</span>
										</a>
									{{/if}}
								</td>
							{{/if}}
						</tr>
					{{/foreach}}
				</tbody>
				<tfoot>
					{{foreach name="data" item="item" from=$total|smarty:nodefaults}}
						<tr class="dg_total">
							<td class="dg_selector"></td>
							{{assign var="table_columns_display_mode" value="summary"}}
							{{assign var="table_columns_summary_field_name" value=$table_summary_field_name}}
							{{include file="table_columns_inc.tpl"}}
							{{if $smarty.session.save.$page_name.se_group_by=='log'}}
								<td></td>
							{{/if}}
						</tr>
					{{/foreach}}
				</tfoot>
			</table>
			{{if $smarty.session.save.$page_name.se_group_by=='log'}}
				<ul class="dg_additional_menu_template">
					{{if in_array('users|manage_awards',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=revert&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.stats.users_awards_action_revert}}</span>
							<span class="js_param">confirm={{$lang.stats.users_awards_action_revert_confirm|replace:"%1%":'${tokens}'|replace:"%2%":'${user}'}}</span>
							<span class="js_param">icon=action-delete</span>
							<span class="js_param">destructive=true</span>
						</li>
					{{/if}}
				</ul>
			{{/if}}
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				{{if $smarty.session.save.$page_name.se_group_by=='log'}}
					<select name="batch_action">
						<option value="">{{$lang.common.dg_batch_actions}}</option>
						{{if in_array('users|manage_awards',$smarty.session.permissions)}}
							<option value="revert">{{$lang.stats.users_awards_batch_action_revert|replace:"%1%":'${count}'}}</option>
						{{/if}}
					</select>
					<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
				{{/if}}
			</div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=revert</span>
					<span class="js_param">confirm={{$lang.stats.users_awards_batch_action_revert_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
			</ul>
		</div>
	</form>
</div>