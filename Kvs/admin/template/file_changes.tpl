{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de {{if count($data)==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="file_changes_review">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.settings.file_changes_header}}</h1></div>
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
				<td class="de_simple_text">{{$lang.settings.file_changes_header_hint}}</td>
			</tr>
			<tr>
				<td class="de_table_control">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td class="eg_selector"><input type="checkbox" checked/></td>
							<td>{{$lang.settings.file_changes_col_path}}</td>
							<td>{{$lang.settings.file_changes_col_description}}</td>
							<td>{{$lang.settings.file_changes_col_modified_date}}</td>
						</tr>
						{{foreach item="item" from=$data|smarty:nodefaults}}
							<tr class="eg_data">
								<td class="eg_selector"><input type="checkbox" name="approve[]" value="{{$item.change_id}}" checked/></td>
								<td><a href="project_pages_history.php?action=change&amp;item_id={{$item.change_id}}">{{$item.path}}</a> {{if $item.is_deleted==1}}{{$lang.settings.file_changes_col_path_deleted}}{{/if}}</td>
								<td>{{$item.description}}</td>
								<td>{{$item.added_date|date_format:$smarty.session.userdata.full_date_format}}</td>
							</tr>
						{{foreachelse}}
							<tr class="eg_data_text">
								<td colspan="4">{{$lang.settings.file_changes_no_changes}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="approve"/>
		<input type="submit" value="{{$lang.settings.file_changes_btn_approve}}"/>
	</div>
</form>