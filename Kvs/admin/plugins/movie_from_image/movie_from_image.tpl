{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.movie_from_image.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.movie_from_image.long_desc}}
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
				<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_image}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.plugins.movie_from_image.field_image}}</span>
							<span class="js_param">accept=jpg</span>
						</div>
						<input type="text" name="source_image" maxlength="100"/>
						<input type="hidden" name="source_image_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.plugins.movie_from_image.field_image_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_duration}}</td>
				<td class="de_control">
					<input type="text" name="duration" maxlength="10" value="10"/>
					<span class="de_hint">{{$lang.plugins.movie_from_image.field_duration_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_quality}}</td>
				<td class="de_control">
					<input type="text" name="quality" maxlength="1000" value="-vcodec libx264 -threads 0 -crf 28 -vf &quot;fps=25,format=yuv420p&quot;"/>
					<span class="de_hint">{{$lang.plugins.movie_from_image.field_quality_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="create"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.movie_from_image.btn_create}}"/>
	</div>
</form>