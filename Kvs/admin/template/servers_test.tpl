{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="storage_server_test">
	<div class="de_main">
		<div class="de_header"><h1><a href="servers.php">{{$lang.settings.submenu_option_storage_servers_list}}</a> / <a href="servers.php?action=change&amp;item_id={{$server.server_id}}">{{$server.title}}</a> / {{$lang.settings.server_test|replace:"%1%":$server.title}}</h1></div>
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
				<td class="de_table_control">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.server_dg_result_col_check}}</td>
							<td>{{$lang.settings.server_dg_result_col_status}}</td>
							<td>{{$lang.settings.server_dg_result_col_url}}</td>
							<td>{{$lang.settings.server_dg_result_col_details}}</td>
						</tr>
						{{foreach item="item" from=$data|smarty:nodefaults}}
							<tr class="eg_group_header">
								<td colspan="4">
									{{if $item.is_sources==1}}
										{{$lang.settings.server_dg_result_format_sources}}
									{{else}}
										{{$lang.settings.server_dg_result_format|replace:"%1%":$item.format}}
									{{/if}}
								</td>
							</tr>
							{{foreach item="item_check" from=$item.checks|smarty:nodefaults}}
								<tr class="eg_data">
									<td class="nowrap {{if $item_check.disabled>0}}disabled{{elseif $item_check.is_error==1}}highlighted_text{{/if}}">
										{{assign var="check_name" value=""}}
										{{if $item_check.type=='direct_link'}}
											{{assign var="check_name" value=$lang.settings.server_dg_result_col_check_direct_link}}
										{{elseif $item_check.type=='direct_link2'}}
											{{assign var="check_name" value=$lang.settings.server_dg_result_col_check_direct_link2}}
										{{elseif $item_check.type=='protected_link'}}
											{{assign var="check_name" value=$lang.settings.server_dg_result_col_check_protected_link}}
										{{/if}}
										{{$check_name}}
									</td>
									<td>
										{{if $item_check.is_error==1}}
											<span class="highlighted_text">{{$lang.settings.server_dg_result_col_status_failure}}</span>
										{{elseif $item_check.disabled>0}}
											<span class="disabled">{{$lang.settings.server_dg_result_col_status_na}}</span>
										{{else}}
											{{$lang.settings.server_dg_result_col_status_ok}}
										{{/if}}
									</td>
									<td>
										<a href="{{$item_check.url}}">{{$item_check.url}}</a>
									</td>
									<td>
										{{if $item_check.details!=''}}
											<a class="details_link" title="{{$check_name}}">
												<span class="info">
													{{$item_check.details}}
												</span>
											</a>
										{{/if}}
									</td>
								</tr>
							{{foreachelse}}
								<tr class="eg_data_text">
									<td colspan="4">{{$lang.settings.server_dg_result_no_content|replace:"%1%":$server.title}}</td>
								</tr>
							{{/foreach}}
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>