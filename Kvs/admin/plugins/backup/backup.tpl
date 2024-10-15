{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.backup.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.backup.long_desc}}
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
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.backup.divider_parameters_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.backup.divider_parameters}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.backup.field_backup_folder}}</td>
				<td class="de_control">
					<input type="text" name="backup_folder" value="{{$smarty.post.backup_folder}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_folder_hint}}</span>
					{{if $smarty.post.open_basedir!=''}}
						<span class="de_hint">{{$lang.plugins.backup.field_backup_folder_hint2|replace:"%1%":$smarty.post.open_basedir}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.backup.remote_storage_type}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="remote_storage_type">
						<option value="">{{$lang.plugins.backup.remote_storage_type_na}}</option>
						<option value="ftp" {{if $smarty.post.remote_storage_type=='ftp'}}selected{{/if}}>{{$lang.plugins.backup.remote_storage_type_ftp}}</option>
						<option value="s3" {{if $smarty.post.remote_storage_type=='s3'}}selected{{/if}}>{{$lang.plugins.backup.remote_storage_type_s3}}</option>
					</select>
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_ftp_host}}</td>
				<td class="de_control">
					<input type="text" name="ftp_host" value="{{$smarty.post.ftp_host}}"/>
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_ftp_port}}</td>
				<td class="de_control">
					<input type="text" name="ftp_port" value="{{$smarty.post.ftp_port}}"/>
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_ftp_user}}</td>
				<td class="de_control">
					<input type="text" name="ftp_user" value="{{$smarty.post.ftp_user}}"/>
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_ftp_pass}}</td>
				<td class="de_control {{if $smarty.post.ftp_pass!=''}}de_passw{{/if}}">
					{{if $smarty.post.ftp_pass!=''}}
						<input type="text" data-name="ftp_pass" value="{{$lang.common.password_hidden}}"/>
					{{else}}
						<input type="password" name="ftp_pass"/>
					{{/if}}
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_dependent">{{$lang.plugins.backup.field_backup_ftp_folder}}</td>
				<td class="de_control">
					<input type="text" name="ftp_folder" value="{{$smarty.post.ftp_folder}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_ftp_folder_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_ftp">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_ftp_timeout}}</td>
				<td class="de_control">
					<input type="text" name="ftp_timeout" maxlength="10" size="10" value="{{$smarty.post.ftp_timeout|default:'20'}}"/>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_s3_region}}</td>
				<td class="de_control">
					<input type="text" name="s3_region" maxlength="150" value="{{$smarty.post.s3_region}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_region_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_dependent">{{$lang.plugins.backup.field_backup_s3_endpoint}}</td>
				<td class="de_control">
					<input type="text" name="s3_endpoint" maxlength="150" value="{{$smarty.post.s3_endpoint}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_endpoint_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_s3_bucket}}</td>
				<td class="de_control">
					<input type="text" name="s3_bucket" maxlength="150" value="{{$smarty.post.s3_bucket}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_bucket_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_dependent">{{$lang.plugins.backup.field_backup_s3_prefix}}</td>
				<td class="de_control">
					<input type="text" name="s3_prefix" maxlength="150" value="{{$smarty.post.s3_prefix}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_prefix_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_required de_dependent">{{$lang.plugins.backup.field_backup_s3_api_key}}</td>
				<td class="de_control">
					<input type="text" name="s3_api_key" maxlength="150" value="{{$smarty.post.s3_api_key}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_api_key_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label {{if $smarty.post.connection_type_id!=3}}de_required{{/if}} de_dependent">{{$lang.plugins.backup.field_backup_s3_api_secret}}</td>
				<td class="de_control {{if $smarty.post.s3_api_secret!=''}}de_passw{{/if}}">
					{{if $smarty.post.s3_api_secret!=''}}
						<input type="text" data-name="s3_api_secret" value="{{$lang.common.password_hidden}}" maxlength="150"/>
					{{else}}
						<input type="password" name="s3_api_secret" maxlength="150"/>
					{{/if}}
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_api_secret_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_dependent">{{$lang.plugins.backup.field_backup_s3_upload_chunk_size_mb}}</td>
				<td class="de_control">
					<input type="text" name="s3_upload_chunk_size_mb" maxlength="10" size="10" value="{{if $smarty.post.s3_upload_chunk_size_mb>0}}{{$smarty.post.s3_upload_chunk_size_mb}}{{/if}}"/>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_upload_chunk_size_mb_hint}}</span>
				</td>
			</tr>
			<tr class="remote_storage_type_s3">
				<td class="de_label de_dependent">{{$lang.plugins.backup.field_backup_s3_is_endpoint_subdirectory}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="s3_is_endpoint_subdirectory" value="1" {{if $smarty.post.s3_is_endpoint_subdirectory==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_s3_is_endpoint_subdirectory_yes}}</label></span>
					<span class="de_hint">{{$lang.plugins.backup.field_backup_s3_is_endpoint_subdirectory_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.backup.field_backup_auto}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="auto_backup_daily" value="1" {{if $smarty.post.auto_backup_daily==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_daily}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="auto_backup_weekly" value="1" {{if $smarty.post.auto_backup_weekly==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_weekly}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="auto_backup_monthly" value="1" {{if $smarty.post.auto_backup_monthly==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_monthly}}</label></span>
							</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="auto_skip_database" value="1" {{if $smarty.post.auto_skip_database==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_skip_database}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="auto_skip_content_auxiliary" value="1" {{if $smarty.post.auto_skip_content_auxiliary==1}}checked{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_skip_content_auxiliary}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.backup.field_schedule}}</td>
				<td class="de_control">
					<span>
						{{$lang.plugins.backup.field_schedule_interval}}:
						<input type="text" name="interval" maxlength="10" size="4" value="24" readonly/>
					</span>
					<span>
						{{$lang.plugins.backup.field_schedule_tod}}:
						<select name="tod">
							<option value="0" {{if $smarty.post.tod==0}}selected{{/if}}>{{$lang.plugins.backup.field_schedule_tod_any}}</option>
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
					<span class="de_hint">{{$lang.plugins.backup.field_schedule_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.backup.field_last_exec}}</td>
				<td class="de_control">
					<span>
						{{if !$smarty.post.last_exec_date || $smarty.post.last_exec_date==0}}
							{{$lang.plugins.backup.field_last_exec_none}}
						{{else}}
							{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							{{if $smarty.post.is_running!=1}}
								(<a href="{{$page_name}}?plugin_id=backup&amp;action=get_log" rel="log">{{$lang.plugins.backup.field_last_exec_data|replace:"%1%":$smarty.post.duration}}</a>)
							{{else}}
								({{$lang.plugins.backup.field_last_exec_data|replace:"%1%":$smarty.post.duration}})
							{{/if}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.backup.field_next_exec}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.is_running==1}}
							<a href="{{$page_name}}?plugin_id=backup&amp;action=get_log" rel="log">{{$lang.plugins.backup.field_next_exec_running}}</a>
						{{else}}
							{{if !$smarty.post.next_exec_date || $smarty.post.next_exec_date==0}}
								{{$lang.plugins.backup.field_next_exec_none}}
							{{else}}
								{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							{{/if}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.backup.divider_manual_backup}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.backup.field_backup_options}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="backup_mysql" value="1" {{if $smarty.post.has_mysqldump_error==1}}disabled{{/if}}/><label>{{$lang.plugins.backup.field_backup_options_mysql}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_mysql_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="backup_website" value="1"/><label>{{$lang.plugins.backup.field_backup_options_website}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_website_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="backup_player" value="1"/><label>{{$lang.plugins.backup.field_backup_options_player}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_player_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="backup_kvs" value="1"/><label>{{$lang.plugins.backup.field_backup_options_kvs}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_kvs_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="backup_content_auxiliary" value="1"/><label>{{$lang.plugins.backup.field_backup_options_content_auxiliary}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_content_auxiliary_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" disabled/><label>{{$lang.plugins.backup.field_backup_options_content_main}}</label></span>
								<span class="de_hint">{{$lang.plugins.backup.field_backup_options_content_main_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.backup.divider_backups}}</h2></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					{{if count($smarty.post.backups)>0}}
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td class="eg_selector"><input type="checkbox"/><label>{{$lang.plugins.backup.dg_backups_col_delete}}</label></td>
								<td>{{$lang.plugins.backup.dg_backups_col_filename}}</td>
								<td>{{$lang.plugins.backup.dg_backups_col_filedate}}</td>
								<td>{{$lang.plugins.backup.dg_backups_col_filesize}}</td>
								<td>{{$lang.plugins.backup.dg_backups_col_backup_type}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.backups|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="eg_selector"><input type="checkbox" name="delete[]" value="{{$item.filename}}" {{if $item.is_deletable!=1}}disabled{{/if}}/></td>
									<td>
										{{if $item.url && $item.filesize!='DIR'}}
											<a href="{{$item.url}}" rel="file">{{$item.filename}}</a>
										{{else}}
											{{$item.filename}}
										{{/if}}
									</td>
									<td>{{$item.filedate|date_format:$smarty.session.userdata.full_date_format}}</td>
									<td>{{$item.filesize}}</td>
									<td>
										{{foreach item="item2" name="data2" from=$item.contents|smarty:nodefaults}}
											{{$item2}}{{if !$smarty.foreach.data2.last}}, {{/if}}
										{{/foreach}}
									</td>
								</tr>
							{{/foreach}}
							<tr class="eg_total">
								<td></td>
								<td>{{$lang.plugins.backup.dg_backups_col_total}}</td>
								<td></td>
								<td>{{$smarty.post.backups_summary_size}}</td>
								<td></td>
							</tr>
						</table>
					{{else}}
						{{$lang.plugins.backup.divider_backups_none}}
					{{/if}}
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="save_backup"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.backup.btn_save}}"/>
	</div>
</form>