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
				{{if count($list_satellite_values)>0}}
					<div class="dgf_filter">
						<select name="se_satellite_prefix">
							<option value="">{{$lang.settings.system_log_field_satellite}}...</option>
							<option value="_" {{if $smarty.session.save.$page_name.se_satellite_prefix=='_'}}selected{{/if}}>{{$lang.settings.system_log_field_satellite_none}}</option>
							{{foreach from=$list_satellite_values key="value" item="title"}}
								<option value="{{$value}}" {{if $smarty.session.save.$page_name.se_satellite_prefix==$value}}selected{{/if}}>{{$title}}</option>
							{{/foreach}}
						</select>
					</div>
				{{/if}}
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
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/><span></span></td>
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
							<td class="dg_selector"><input type="checkbox" name="row_select[]" disabled/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>
		</div>
	</form>
</div>
