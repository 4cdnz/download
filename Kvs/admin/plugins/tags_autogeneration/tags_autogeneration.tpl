{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.tags_autogeneration.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.tags_autogeneration.long_desc}}
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
				<td class="de_label">{{$lang.plugins.tags_autogeneration.field_enable_for_videos}}</td>
				<td class="de_control">
					<select name="enable_for_videos">
						<option value="0" {{if $smarty.post.enable_for_videos==0}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_videos_disabled}}</option>
						<option value="1" {{if $smarty.post.enable_for_videos==1}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_videos_always}}</option>
						<option value="2" {{if $smarty.post.enable_for_videos==2}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_videos_empty}}</option>
					</select>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.plugins.tags_autogeneration.field_enable_for_albums}}</td>
					<td class="de_control">
						<select name="enable_for_albums">
							<option value="0" {{if $smarty.post.enable_for_albums==0}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_albums_disabled}}</option>
							<option value="1" {{if $smarty.post.enable_for_albums==1}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_albums_always}}</option>
							<option value="2" {{if $smarty.post.enable_for_albums==2}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_enable_for_albums_empty}}</option>
						</select>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label lenient_2">{{$lang.plugins.tags_autogeneration.field_lenient}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="lenient">
									<option value="0">{{$lang.plugins.tags_autogeneration.field_lenient_off}}</option>
									<option value="1" {{if $smarty.post.lenient==1}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_lenient_all}}</option>
									<option value="2" {{if $smarty.post.lenient==2}}selected{{/if}}>{{$lang.plugins.tags_autogeneration.field_lenient_specific}}</option>
								</select>
								<span class="de_hint">{{$lang.plugins.tags_autogeneration.field_lenient_hint1}}</span>
							</td>
						</tr>
						<tr class="lenient_2">
							<td>
								<textarea name="lenient_list" rows="3" cols="40">{{$smarty.post.lenient_list}}</textarea>
								<span class="de_hint">{{$lang.plugins.tags_autogeneration.field_lenient_hint2}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.tags_autogeneration.btn_save}}"/>
	</div>
</form>