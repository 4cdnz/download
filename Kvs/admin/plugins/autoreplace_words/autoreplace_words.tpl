{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.autoreplace_words.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.autoreplace_words.long_desc}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.autoreplace_words.divider_settings}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.autoreplace_words.field_replace_videos}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="replace_videos_title" value="1" {{if $smarty.post.replace_videos_title==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_title}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="replace_videos_description" value="1" {{if $smarty.post.replace_videos_description==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_description}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.plugins.autoreplace_words.field_replace_albums}}</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" name="replace_albums_title" value="1" {{if $smarty.post.replace_albums_title==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_title}}</label></span></td>
							</tr>
							<tr>
								<td><span class="de_lv_pair"><input type="checkbox" name="replace_albums_description" value="1" {{if $smarty.post.replace_albums_description==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_description}}</label></span></td>
							</tr>
						</table>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.autoreplace_words.field_limit}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="limit_feeds" value="1" {{if $smarty.post.limit_feeds==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_limit_feeds}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="limit_grabbers" value="1" {{if $smarty.post.limit_grabbers==1}}checked{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_limit_grabbers}}</label></span></td>
						</tr>
					</table>
					<span class="de_hint">{{$lang.plugins.autoreplace_words.field_limit_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.autoreplace_words.divider_vocabulary}}</h2></td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<textarea name="vocabulary" rows="20" cols="40" placeholder="{{$lang.plugins.autoreplace_words.field_vocabulary_example}}">{{$smarty.post.vocabulary}}</textarea>
					<span class="de_hint">{{$lang.plugins.autoreplace_words.divider_vocabulary_hint}}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.autoreplace_words.btn_save}}"/>
	</div>
</form>