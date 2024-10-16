{{foreach name="data_custom" item="item" from=$custom_text_fields|smarty:nodefaults}}
	<tr>
		<td class="de_label">{{$item.name}}</td>
		<td class="de_control" {{if $custom_colspan>0}}colspan="{{$custom_colspan}}"{{/if}}>
			<textarea name="{{$item.field_name}}" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$item.value}}</textarea>
			<span class="de_hint"><span class="de_str_len_value"></span></span>
		</td>
	</tr>
{{/foreach}}
{{foreach name="data_custom" item="item" from=$custom_file_fields|smarty:nodefaults}}
	<tr>
		<td class="de_label">{{$item.name}}</td>
		<td class="de_control" {{if $custom_colspan>0}}colspan="{{$custom_colspan}}"{{/if}}>
			<div class="de_fu">
				<div class="js_params">
					<span class="js_param">title={{$item.name}}</span>
					{{if $smarty.get.action=='change' && $item.value!=''}}
						{{if in_array(end(explode(".",$item.value)),explode(",",$config.image_allowed_ext))}}
							<span class="js_param">preview_url={{$custom_file_base_url}}/{{$item.value}}</span>
						{{else}}
							<span class="js_param">download_url={{$custom_file_base_url}}/{{$item.value}}</span>
						{{/if}}
					{{/if}}
				</div>
				<input type="text" name="{{$item.field_name}}" maxlength="100" {{if $smarty.get.action=='change' && $item.value!=''}}value="{{$item.value}}"{{/if}}/>
				<input type="hidden" name="{{$item.field_name}}_hash"/>
				{{if $can_edit_all==1}}
					<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
					<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
				{{/if}}
			</div>
		</td>
	</tr>
{{/foreach}}