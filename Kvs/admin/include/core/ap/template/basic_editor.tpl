{{*todo: can_edit_all *}}
{{assign var="controller_type" value="add"}}
{{if $data_type.identifier!="" && $data[$data_type.identifier]>0}}
	{{assign var="controller_type" value="edit"}}
{{/if}}
<form action="?/{{$controller_path}}" method="post" class="de {{if $controller_type=="edit"}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="{{$data_type.name}}_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="?/{{$data_type.module}}/{{$data_type.names}}/list">{{$data_type.titles}}</a>
				/
				{{$page_title}}
			</h1>
		</div>
		<fieldset class="de_contents">
			{{assign var="last_section" value=""}}
			{{foreach from=$data_type.fields|smarty:nodefaults item="field"}}
				{{if !$last_section || $last_section!=$field.group.id}}
					<section data-section="{{$field.group.id}}">{{$field.group.title}}</section>
					{{assign var="last_section" value=$field.group.id}}
				{{/if}}
				{{if $field.type!="id" && ($controller_type=="edit" || $field.name!=$data_type.directory_identifier) && ($controller_type=="edit" || $field.is_calculated!=1)}}
					<label {{if $field.is_required}}class="required" {{/if}}>{{$field.title}}</label>
					<value>
						{{if $field.is_calculated==1}}
							<span>
								{{if $field.type=='date'}}
									{{$data[$field.name]|date_format:$smarty.session.userdata.short_date_format}}
								{{elseif $field.type=='datetime'}}
									{{$data[$field.name]|date_format:$smarty.session.userdata.full_date_format}}
								{{else}}
									{{$data[$field.name]}}
								{{/if}}
							</span>
						{{elseif $field.name=="status_id"}}
							<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $data.status_id=='1'}}checked{{/if}}/><label>{{$field.values.1}}</label></span>
						{{elseif $field.type=="shorttext"}}
							<input type="text" name="{{$field.name}}" value="{{$data[$field.name]}}"/>
						{{elseif $field.type=="longtext" || $field.type=="bigtext"}}
							<textarea name="{{$field.name}}" cols="40" rows="4">{{$data[$field.name]}}</textarea>
						{{elseif $field.type=="choice"}}
							<select name="{{$field.name}}">
								<option value="0">{{$lang.common.select_default_option}}</option>
								{{foreach from=$field.values|smarty:nodefaults key="value" item="option"}}
									<option value="{{$value}}" {{if $data[$field.name]==$value}}selected{{/if}}>{{$option}}</option>
								{{/foreach}}
							</select>
						{{elseif $field.type=="enum"}}
							<select name="{{$field.name}}">
								{{foreach from=$field.values|smarty:nodefaults key="value" item="option"}}
									<option value="{{$value}}" {{if $data[$field.name]==$value}}selected{{/if}}>{{$option}}</option>
								{{/foreach}}
							</select>
						{{elseif $field.type=="ref"}}
							<div class="insight">
								<span class="js_params">
									<span class="js_param">url=async/insight.php?type={{$field.target.names}}</span>
									{{if $field.target.can_create==1}}
										<span class="js_param">allow_creation=true</span>
									{{/if}}
									{{if $field.target.can_view==1}}
										<span class="js_param">allow_view=true</span>
										{{if $data[$field.name].id>0}}
											<span class="js_param">view_id={{$data[$field.name].id}}</span>
											{{if $data[$field.name].is_inactive==1}}
												<span class="js_param">inactive=true</span>
											{{/if}}
										{{/if}}
									{{/if}}
								</span>
								<input type="text" name="{{$field.name}}" maxlength="255" value="{{$data[$field.name].title}}"/>
							</div>
						{{elseif $field.type=="country"}}
							<div class="insight">
								<span class="js_params">
									<span class="js_param">url=async/insight.php?type=countries</span>
								</span>
								<input type="text" name="{{$field.name}}" maxlength="255" value="{{$data[$field.name]}}"/>
							</div>
						{{/if}}
						{{if $field.hint}}
							<span class="de_hint">{{$field.hint}}</span>
						{{/if}}
						{{if $field.name==$data_type.title_identifier || $field.name==$data_type.description_identifier}}
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						{{/if}}
					</value>
				{{/if}}
			{{/foreach}}
		</fieldset>
	</div>
	<div class="de_action_group">
		{{if $controller_type=="edit"}}
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_edit" value="{{$localization.ap.controller_action_save_and_edit}}"/>
				<input type="submit" name="save_and_stay" value="{{$localization.ap.controller_action_save}}"/>
				<input type="submit" name="save_and_close" value="{{$localization.ap.controller_action_save_and_close}}"/>
			{{else}}
				<input type="submit" name="save_and_stay" value="{{$localization.ap.controller_action_save}}"/>
				<input type="submit" name="save_and_edit" value="{{$localization.ap.controller_action_save_and_edit}}"/>
				<input type="submit" name="save_and_close" value="{{$localization.ap.controller_action_save_and_close}}"/>
			{{/if}}
			{{if $data_type.can_delete==1}}
				<span class="de_separated_group">
					<input type="submit" name="delete_and_edit" class="destructive" value="{{$localization.ap.controller_action_delete_and_edit}}" data-confirm="{{$localization.ap.controller_action_delete_and_edit_confirm}}"/>
				</span>
			{{/if}}
		{{else}}
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_add" value="{{$localization.ap.controller_action_save_and_add}}"/>
				<input type="submit" name="save_default" value="{{$localization.ap.controller_action_save}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$localization.ap.controller_action_save}}"/>
				<input type="submit" name="save_and_add" value="{{$localization.ap.controller_action_save_and_add}}"/>
			{{/if}}
		{{/if}}
	</div>
</form>