{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a>
				/
				{{if $smarty.post.details_table}}
					<a href="{{$page_name}}?plugin_id=database_repair">{{$lang.plugins.database_repair.title}}</a>
					/
					{{$smarty.post.details_table}}
				{{else}}
					{{$lang.plugins.database_repair.title}}
				{{/if}}
				&nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]
			</h1>
		</div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.database_repair.long_desc}}
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
				<td class="de_label">{{$lang.plugins.database_repair.field_database_version}}</td>
				<td class="de_control">
					<span>
						{{$smarty.post.database_version|default:$lang.common.undefined}}
					</span>
				</td>
			</tr>
			{{if count($smarty.post.queries)>0}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.database_repair.divider_queries}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td class="eg_selector">{{$lang.plugins.database_repair.dg_queries_col_kill}}</td>
								<td>{{$lang.plugins.database_repair.dg_queries_col_id}}</td>
								<td>{{$lang.plugins.database_repair.dg_queries_col_command}}</td>
								<td>{{$lang.plugins.database_repair.dg_queries_col_time}}</td>
								<td>{{$lang.plugins.database_repair.dg_queries_col_state}}</td>
								<td>{{$lang.plugins.database_repair.dg_queries_col_info}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.queries|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="eg_selector"><input type="checkbox" name="kill_queries[]" value="{{$item.Id}}"/></td>
									<td class="nowrap">{{$item.Id}}</td>
									<td class="nowrap">{{$item.Command}}</td>
									<td class="nowrap">{{$item.Time}}</td>
									<td class="nowrap">{{$item.State}}</td>
									<td>{{$item.Info}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.details_table}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.database_repair.divider_table_details|replace:"%1%":$smarty.post.details_table}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.plugins.database_repair.dg_details_col_field_name}}</td>
								<td>{{$lang.plugins.database_repair.dg_details_col_field_type}}</td>
								<td>{{$lang.plugins.database_repair.dg_details_col_field_key}}</td>
								<td>{{$lang.plugins.database_repair.dg_details_col_field_extra}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.details_fields|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="nowrap">{{$item.Field}}</td>
									<td class="nowrap">{{$item.Type}}</td>
									<td class="nowrap">{{$item.Key}}</td>
									<td class="nowrap">{{$item.Extra}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
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
								<td>{{$lang.plugins.database_repair.dg_details_col_index_name}}</td>
								<td>{{$lang.plugins.database_repair.dg_details_col_field_name}}</td>
								<td>{{$lang.plugins.database_repair.dg_details_col_index_power}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.details_indexes|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="nowrap">{{$item.Key_name}}</td>
									<td class="nowrap">{{$item.Column_name}}</td>
									<td class="nowrap">{{$item.Cardinality}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.database_repair.divider_tables}}</h2></td>
				</tr>
				<tr>
					<td class="de_table_control" colspan="2">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header">
								<td>{{$lang.plugins.database_repair.dg_data_col_table_name}}</td>
								<td>{{$lang.plugins.database_repair.dg_data_col_engine}}</td>
								<td>{{$lang.plugins.database_repair.dg_data_col_rows}}</td>
								<td>{{$lang.plugins.database_repair.dg_data_col_size}}</td>
								<td>{{$lang.plugins.database_repair.dg_data_col_status}}</td>
								<td>{{$lang.plugins.database_repair.dg_data_col_message}}</td>
							</tr>
							{{foreach item="item" from=$smarty.post.data|smarty:nodefaults}}
								{{foreach item="item2" from=$item.status|smarty:nodefaults}}
									<tr class="eg_data">
										<td class="nowrap"><a href="?plugin_id=database_repair&amp;action=table_details&amp;table={{$item.table}}">{{$item.table}}</a></td>
										<td class="nowrap">{{$item.engine}}</td>
										<td class="nowrap">{{$item.rows|number_format:0:".":" "}}</td>
										<td class="nowrap">{{$item.size}}</td>
										<td class="nowrap">
											{{if $item2.Msg_type=='status'}}
												{{$item2.Msg_text}}
											{{elseif $item2.Msg_type|strtolower=='error'}}
												<span class="highlighted_text">{{$item2.Msg_type}}</span>
											{{elseif $item2.Msg_type|strtolower=='warning'}}
												<span class="warning_text">{{$item2.Msg_type}}</span>
											{{else}}
												<span>{{$item2.Msg_type}}</span>
											{{/if}}
										</td>
										<td>{{if $item2.Msg_type!='status'}}{{$item2.Msg_text}}{{/if}}</td>
									</tr>
								{{/foreach}}
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="repair"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		{{if count($smarty.post.queries)>0}}
			<input type="submit" name="save_default" value="{{$lang.plugins.database_repair.btn_kill}}"/>
		{{/if}}
		<input type="submit" name="analyze" value="{{$lang.plugins.database_repair.btn_check_tables}}"/>
		{{if $smarty.post.has_errors==1}}
			<input type="submit" name="repair" value="{{$lang.plugins.database_repair.btn_repair}}"/>
		{{/if}}
	</div>
</form>