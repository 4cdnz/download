{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="album_select">
	<div class="de_main">
		<div class="de_header"><h1><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.submenu_option_select_albums}}</h1></div>
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
				<td class="de_label de_required">{{$lang.albums.select_field_list_items}}</td>
				<td class="de_control">
					<textarea name="selector" cols="30" rows="5">{{$smarty.post.selector}}</textarea>
					<span class="de_hint">{{$lang.albums.select_field_list_items_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.albums.select_field_operation}}</td>
				<td class="de_control">
					<table class="control_group de_vis_sw_radio">
						<tr>
							<td>
								<span class="de_lv_pair"><input id="operation_list" type="radio" name="operation" value="list"/><label>{{$lang.albums.select_field_operation_list}}</label></span>
								<span class="de_hint">{{$lang.albums.select_field_operation_list_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="operation_massedit" type="radio" name="operation" value="mass_edit" {{if !in_array('albums|mass_edit',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.select_field_operation_mass_edit}}</label></span>
								<span class="de_hint">{{$lang.albums.select_field_operation_mass_edit_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="operation_export" type="radio" name="operation" value="export" {{if !in_array('albums|export',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.select_field_operation_export}}</label></span>
								<span class="de_hint">{{$lang.albums.select_field_operation_export_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="operation_mark_deleted" type="radio" name="operation" value="mark_deleted" {{if !in_array('albums|delete',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.select_field_operation_mark_deleted}}</label></span>
								<span class="de_hint">{{$lang.albums.select_field_operation_mark_deleted_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input id="operation_delete" type="radio" name="operation" value="delete" {{if !in_array('albums|delete',$smarty.session.permissions)}}disabled{{/if}}/><label>{{$lang.albums.select_field_operation_delete}}</label></span>
								<span class="de_hint">{{$lang.albums.select_field_operation_delete_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="operation_delete operation_mark_deleted">
				<td class="de_label de_required">{{$lang.albums.select_field_operation_confirm}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="confirm" value="1"/><label>{{$lang.albums.select_field_operation_confirm_value}}</label></span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="select_complete"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>