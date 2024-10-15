{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="installation_info">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.installation_header}}</h1></div>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_system}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">PHP WWW</td>
				<td class="de_control">
					<a href="installation.php?action=get_info" rel="log">PHP {{$phpversion}}</a>
				</td>
			</tr>
			{{foreach item="item" from=$system|smarty:nodefaults}}
				<tr>
					<td class="de_label">{{$item.name}}</td>
					<td class="de_control">
						{{if $item.type=='multiline'}}
							<textarea cols="20" rows="3">{{$item.value}}</textarea>
						{{else}}
							<input type="text" value="{{$item.value}}"/>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td class="de_label">{{$lang.settings.installation_processes}}</td>
				<td class="de_table_control">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.installation_processes_pid}}</td>
							<td>{{$lang.settings.installation_processes_exec_interval}}</td>
							<td>{{$lang.settings.installation_processes_last_exec_date}}</td>
							<td>{{$lang.settings.installation_processes_last_message}}</td>
						</tr>
						{{foreach item="item" from=$processes|smarty:nodefaults}}
							<tr class="eg_data">
								<td class="nowrap {{if $item.level>1}}eg_padding{{/if}}">
									{{if $item.link && in_array($item.permission, $smarty.session.permissions)}}
										<a href="{{$item.link}}">{{$item.pid}}</a>
									{{else}}
										{{$item.pid}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.exec_interval==0}}
										{{$lang.common.undefined}}
									{{else}}
										{{if $item.exec_interval/60 > 60 && $item.exec_interval%3600==0}}
											{{$item.exec_interval/3600}} {{$lang.common.hours}}
										{{else}}
											{{$item.exec_interval/60}} {{$lang.common.minutes}}
										{{/if}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.is_running==1}}
										{{$lang.settings.installation_processes_last_exec_date_now}}
										{{if $item.osid>0}}<br/><a href="?action=kill&pid={{$item.osid}}">{{$lang.settings.installation_processes_last_exec_date_kill}}</a>{{/if}}
									{{elseif $item.last_exec_date=='0000-00-00 00:00:00'}}
										{{$lang.common.undefined}}
									{{else}}
										{{$item.last_exec_date|date_format:$smarty.session.userdata.full_date_format}} ({{$item.last_exec_duration}} {{$lang.common.seconds}})
									{{/if}}
								</td>
								<td>
									<a href="{{if $item.log_link}}{{$item.log_link}}{{else}}?action=get_log&amp;log_file={{$item.log_filename}}{{/if}}" rel="log">{{$item.last_message}}</a>
								</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
			{{if count($memcache_stats)}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_memcache}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.installation_memcache_utilization}}</td>
					<td class="de_control">
						<input type="text" value="{{$memcache_stats.memcache_used_memory}} / {{$memcache_stats.memcache_total_memory}} ({{$memcache_stats.memcache_usage_percent}}%)"/>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.installation_memcache_success}}</td>
					<td class="de_control">
						<input type="text" value="{{$memcache_stats.memcache_success_hits}} / {{$memcache_stats.memcache_total_hits}} ({{$memcache_stats.memcache_success_percent}}%)"/>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_configuration}}</h2></td>
			</tr>
			{{foreach item="item" from=$data|smarty:nodefaults}}
				<tr>
					<td class="de_label">$config.{{$item.key}}</td>
					<td class="de_control">
						{{if is_array($item.value)}}
							{{assign var="value" value="Array["}}
							{{foreach item="item2" name="data" from=$item.value|smarty:nodefaults}}
								{{if $smarty.foreach.data.last}}
									{{assign var="value" value="`$value``$item2`"}}
								{{else}}
									{{assign var="value" value="`$value``$item2`, "}}
								{{/if}}
							{{/foreach}}
							{{assign var="value" value="`$value`]"}}
							<input type="text" value="{{$value}}"/>
						{{else}}
							<input type="text" value="{{$item.value}}"/>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_php_options}}</h2></td>
			</tr>
			{{foreach item="item" key="key" from=$ini_vars|smarty:nodefaults}}
				<tr>
					<td class="de_label">{{$key}}</td>
					<td class="de_control">
						{{if is_array($item)}}
							{{assign var="global_value" value="`$item.global_value`"}}
							{{if $global_value=='0'}}
								{{assign var="global_value" value="Off"}}
							{{elseif $global_value=='1'}}
								{{assign var="global_value" value="On"}}
							{{elseif $global_value==''}}
								{{assign var="global_value" value="N/A"}}
							{{/if}}
							{{assign var="local_value" value="`$item.local_value`"}}
							{{if $local_value=='0'}}
								{{assign var="local_value" value="Off"}}
							{{elseif $local_value=='1'}}
								{{assign var="local_value" value="On"}}
							{{elseif $local_value==''}}
								{{assign var="local_value" value="N/A"}}
							{{/if}}
							<input type="text" size="30" value="{{$global_value}}"/>
							/
							<input type="text" size="30" value="{{$local_value}}"/>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_environment}}</h2></td>
			</tr>
			{{foreach key="key" item="item" from=$smarty.server|smarty:nodefaults}}
				<tr>
					<td class="de_label">{{$key}}</td>
					<td class="de_control">
						<input type="text" value="{{$item}}"/>
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_logs}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.installation_files_log_file}}</td>
							<td>{{$lang.settings.installation_files_filesize}}</td>
							<td>{{$lang.settings.installation_files_modified}}</td>
						</tr>
						{{foreach item="item" from=$logs|smarty:nodefaults}}
							<tr class="eg_data">
								<td><a href="?action=get_log&amp;log_file={{$item.file_name}}" rel="log">{{$item.file_name}}</a></td>
								<td>{{$item.file_size}}</td>
								<td>{{$item.file_time|date_format:$smarty.session.userdata.full_date_format}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.installation_divider_engine_customizations}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.installation_files_engine_file}}</td>
							<td>{{$lang.settings.installation_files_filesize}}</td>
							<td>{{$lang.settings.installation_files_modified}}</td>
						</tr>
						{{foreach item="item" from=$engine_customizations|smarty:nodefaults}}
							<tr class="eg_data">
								<td><a href="?action=get_customization_file&amp;customization_file={{$item.file_name|replace:".htaccess":"htaccess"}}" rel="log">{{$item.file_name}}</a></td>
								<td>{{$item.file_size}}</td>
								<td>{{$item.file_time|date_format:$smarty.session.userdata.full_date_format}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>