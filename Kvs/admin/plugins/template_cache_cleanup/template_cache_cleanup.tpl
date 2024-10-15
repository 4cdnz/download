{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.template_cache_cleanup.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.template_cache_cleanup.long_desc}}
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
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_cache_folder}}</td>
				<td class="de_control">
					<span>
						{{$smarty.post.cache_dir}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_cache_size}}</td>
				<td class="de_control">
					<table class="control_group">
						{{if $smarty.post.cache_size>0}}
							<tr><td>{{$smarty.post.cache_size|sizeToHumanString}} ({{$smarty.post.cache_count|traffic_format}}):</td></tr>
							{{foreach key="template" item="cache_item" from=$smarty.post.cache_details}}
								<tr><td>
								- <a href="?plugin_id=template_cache_cleanup&amp;action=example&amp;result_id={{$smarty.get.result_id}}&amp;template={{$template|urlencode}}" rel="log">{{$template}}</a>: <b>{{$cache_item.count}} ({{$cache_item.size|sizeToHumanString}})</b>
								</td></tr>
							{{/foreach}}
						{{else}}
							{{$lang.plugins.template_cache_cleanup.field_size_check}}
						{{/if}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_storage_folder}}</td>
				<td class="de_control">
					<span>
						{{$smarty.post.storage_dir}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_storage_size}}</td>
				<td class="de_control">
					<table class="control_group">
						{{if $smarty.post.storage_size>0}}
						<tr><td>{{$smarty.post.storage_size|sizeToHumanString}} ({{$smarty.post.storage_count|traffic_format}}):</td></tr>
							{{foreach key="template" item="storage_item" from=$smarty.post.storage_details}}
								<tr><td>
								- {{$template}}: <b>{{$storage_item.count}} ({{$storage_item.size|sizeToHumanString}})</b>
								</td></tr>
							{{/foreach}}
						{{else}}
							{{$lang.plugins.template_cache_cleanup.field_size_check}}
						{{/if}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_speed}}</td>
				<td class="de_control">
					<select name="speed">
						<option value="veryslow" {{if $smarty.post.speed=='veryslow'}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_speed_veryslow}}</option>
						<option value="slow" {{if $smarty.post.speed=='slow'}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_speed_slow}}</option>
						<option value="normal" {{if $smarty.post.speed=='normal'}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_speed_normal}}</option>
						<option value="fast" {{if $smarty.post.speed=='fast'}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_speed_fast}}</option>
						<option value="ultrafast" {{if $smarty.post.speed=='ultrafast'}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_speed_ultrafast}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.template_cache_cleanup.field_speed_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.template_cache_cleanup.field_enable}}</td>
				<td class="de_control"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_enabled" value="1" {{if $smarty.post.is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.template_cache_cleanup.field_enable_enabled}}</label></span></td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_required de_dependent">{{$lang.plugins.template_cache_cleanup.field_schedule}}</td>
				<td class="de_control">
					<span>
						{{$lang.plugins.template_cache_cleanup.field_schedule_interval}}:
						<input type="text" name="interval" maxlength="10" size="4" value="{{if $smarty.post.interval>0}}{{$smarty.post.interval}}{{/if}}"/>
					</span>
					<span>
						{{$lang.plugins.template_cache_cleanup.field_schedule_tod}}:
						<select name="tod">
							<option value="0" {{if $smarty.post.tod==0}}selected{{/if}}>{{$lang.plugins.template_cache_cleanup.field_schedule_tod_any}}</option>
							<option value="1" {{if $smarty.post.tod==1}}selected{{/if}}>00:00-01:00</option>
							<option value="2" {{if $smarty.post.tod==2}}selected{{/if}}>01:00-02:00</option>
							<option value="3" {{if $smarty.post.tod==3}}selected{{/if}}>02:00-03:00</option>
							<option value="4" {{if $smarty.post.tod==4}}selected{{/if}}>03:00-04:00</option>
							<option value="5" {{if $smarty.post.tod==5}}selected{{/if}}>04:00-05:00</option>
							<option value="6" {{if $smarty.post.tod==6}}selected{{/if}}>05:00-06:00</option>
							<option value="7" {{if $smarty.post.tod==7}}selected{{/if}}>06:00-07:00</option>
							<option value="8" {{if $smarty.post.tod==8}}selected{{/if}}>07:00-08:00</option>
							<option value="9" {{if $smarty.post.tod==9}}selected{{/if}}>08:00-09:00</option>
							<option value="10" {{if $smarty.post.tod==10}}selected{{/if}}>09:00-10:00</option>
							<option value="11" {{if $smarty.post.tod==11}}selected{{/if}}>10:00-11:00</option>
							<option value="12" {{if $smarty.post.tod==12}}selected{{/if}}>11:00-12:00</option>
							<option value="13" {{if $smarty.post.tod==13}}selected{{/if}}>12:00-13:00</option>
							<option value="14" {{if $smarty.post.tod==14}}selected{{/if}}>13:00-14:00</option>
							<option value="15" {{if $smarty.post.tod==15}}selected{{/if}}>14:00-15:00</option>
							<option value="16" {{if $smarty.post.tod==16}}selected{{/if}}>15:00-16:00</option>
							<option value="17" {{if $smarty.post.tod==17}}selected{{/if}}>16:00-17:00</option>
							<option value="18" {{if $smarty.post.tod==18}}selected{{/if}}>17:00-18:00</option>
							<option value="19" {{if $smarty.post.tod==19}}selected{{/if}}>18:00-19:00</option>
							<option value="20" {{if $smarty.post.tod==20}}selected{{/if}}>19:00-20:00</option>
							<option value="21" {{if $smarty.post.tod==21}}selected{{/if}}>20:00-21:00</option>
							<option value="22" {{if $smarty.post.tod==22}}selected{{/if}}>21:00-22:00</option>
							<option value="23" {{if $smarty.post.tod==23}}selected{{/if}}>22:00-23:00</option>
							<option value="24" {{if $smarty.post.tod==24}}selected{{/if}}>23:00-00:00</option>
						</select>
					</span>
					<span class="de_hint">{{$lang.plugins.template_cache_cleanup.field_schedule_hint}}</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.template_cache_cleanup.field_last_exec}}</td>
				<td class="de_control">
					<span>
						{{if !$smarty.post.last_exec_date || $smarty.post.last_exec_date==0}}
							{{$lang.plugins.template_cache_cleanup.field_last_exec_none}}
						{{else}}
							{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							{{if $smarty.post.is_running!=1}}
								(<a href="{{$page_name}}?plugin_id=template_cache_cleanup&amp;action=get_log" rel="log">{{$lang.plugins.template_cache_cleanup.field_last_exec_data|replace:"%1%":$smarty.post.duration|replace:"%2%":$smarty.post.deleted_files}}</a>)
							{{else}}
								({{$lang.plugins.template_cache_cleanup.field_last_exec_data|replace:"%1%":$smarty.post.duration|replace:"%2%":$smarty.post.deleted_files}})
							{{/if}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="is_enabled_on">
				<td class="de_label de_dependent">{{$lang.plugins.template_cache_cleanup.field_next_exec}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.is_running==1}}
							<a href="{{$page_name}}?plugin_id=template_cache_cleanup&amp;action=get_log" rel="log">{{$lang.plugins.template_cache_cleanup.field_next_exec_running}}</a>
						{{else}}
							{{if !$smarty.post.next_exec_date || $smarty.post.next_exec_date==0}}
								{{$lang.plugins.template_cache_cleanup.field_next_exec_none}}
							{{else}}
								{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							{{/if}}
						{{/if}}
					</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.template_cache_cleanup.btn_save}}"/>
		<input type="submit" name="calculate_stats" value="{{$lang.plugins.template_cache_cleanup.btn_calculate_stats}}"/>
		<input type="submit" name="start_now" value="{{$lang.plugins.template_cache_cleanup.btn_start_now}}" {{if $smarty.post.is_running==1}}disabled{{/if}}/>
	</div>
</form>