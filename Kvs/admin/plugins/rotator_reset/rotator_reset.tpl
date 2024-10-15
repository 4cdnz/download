{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.rotator_reset.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.rotator_reset.long_desc}}
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
				<td class="de_control" colspan="2">
					<span class="de_lv_pair"><input type="checkbox" name="reset_videos" value="1"/><label>{{$lang.plugins.rotator_reset.field_reset_videos}}</label></span>
					<span class="de_hint">{{$lang.plugins.rotator_reset.field_reset_videos_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<span class="de_lv_pair"><input type="checkbox" name="reset_screenshots" value="1"/><label>{{$lang.plugins.rotator_reset.field_reset_screenshots}}</label></span>
					<span class="de_hint">{{$lang.plugins.rotator_reset.field_reset_screenshots_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="reset"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.rotator_reset.btn_reset}}"/>
	</div>
</form>