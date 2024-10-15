{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="theme_main">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.website_ui.theme_settings_title}}</h1></div>
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
				<td class="de_control" colspan="2">
					<span class="de_hint">{{$lang.website_ui.theme_settings_title_hint}}</span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/177-theme-customization-basic-things/">Theme customization: basic things</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/59-working-with-seo-texts-and-urls-in-kvs-themes/">Working with SEO, texts and URLs in KVS themes</a></span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/tags/theme/">Different articles on theme customization</a></span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_settings_divider_info}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_name}}</td>
				<td class="de_control">
					<span>
						{{$theme.name}} {{if $theme.version!=''}}({{$theme.version}}){{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_developer}}</td>
				<td class="de_control">
					{{if $theme.developer_url!=''}}
						<a href="{{$theme.developer_url}}">{{$theme.developer|default:$theme.developer_url}}</a>
					{{else}}
						<span>{{$theme.developer|default:$lang.common.undefined}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $theme.forum!=''}}
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_settings_field_forum}}</td>
					<td class="de_control">
						<a href="https://forum.kernel-video-sharing.com/forum/5-themes-amp-templates/">{{$lang.website_ui.theme_settings_field_forum_open}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.website_ui.theme_settings_divider_global}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_texts}}</td>
				<td class="de_control">
					<a href="project_pages_lang_texts.php?no_filter=true">{{$lang.website_ui.theme_settings_field_texts_value}}</a>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_urls}}</td>
				<td class="de_control">
					<span>
						{{if in_array('system|website_settings',$smarty.session.permissions)}}
							<a href="options.php?page=website_settings">{{$lang.website_ui.theme_settings_field_urls_value_objects}}</a>
						{{else}}
							{{$lang.website_ui.theme_settings_field_urls_value_objects}}
						{{/if}}
						&nbsp;/&nbsp;
						<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=urls">{{$lang.website_ui.theme_settings_field_urls_value_design}}</a>
					</span>
					<span class="de_hint">{{$lang.website_ui.theme_settings_field_urls_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_seo}}</td>
				<td class="de_control">
					<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=html">{{$lang.website_ui.theme_settings_field_seo_value}}</a>
				</td>
			</tr>
			{{if $theme.header!=''}}
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_settings_field_header}}</td>
					<td class="de_control">
						<a href="project_pages_components.php?action=change&amp;item_id={{$theme.header}}">{{$lang.website_ui.theme_settings_field_header_value}}</a>
					</td>
				</tr>
			{{/if}}
			{{if $theme.footer!=''}}
				<tr>
					<td class="de_label">{{$lang.website_ui.theme_settings_field_footer}}</td>
					<td class="de_control">
						<a href="project_pages_components.php?action=change&amp;item_id={{$theme.footer}}">{{$lang.website_ui.theme_settings_field_footer_value}}</a>
					</td>
				</tr>
			{{/if}}
			{{foreach from=$theme.sections|smarty:nodefaults item="section"}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$section.title}}</h2></td>
				</tr>
				{{foreach from=$section.fields|smarty:nodefaults item="field"}}
					{{if $field.hidden!=1 && $field.unsupported!=1}}
						<tr>
							<td class="de_label {{if $field.required==1}}de_required{{/if}} {{if $field.unsupported==1}}disabled{{/if}}">{{$field.title}}</td>
							<td class="de_control">
								{{if $field.type=='checkbox'}}
									<span class="de_lv_pair"><input type="checkbox" name="{{$field.id}}" value="true" {{if $field.value=='true'}}checked{{/if}} {{if $field.unsupported==1}}disabled{{/if}}/><label>{{$field.label|default:$lang.website_ui.theme_settings_field_label_enable}}</label></span>
								{{elseif $field.type=='text'}}
									<input type="text" name="{{$field.id}}" value="{{$field.value}}" {{if $field.unsupported==1}}disabled{{/if}}/>
								{{elseif $field.type=='select'}}
									<select name="{{$field.id}}" {{if $field.unsupported==1}}disabled{{/if}}>
										{{assign var="select_has_valid_option" value="false"}}
										{{foreach from=$field.options|smarty:nodefaults item="option"}}
											<option value="{{$option.id}}" {{if $option.id==$field.value}}selected{{/if}}>{{$option.title}}</option>
											{{if $option.id==$field.value}}
												{{assign var="select_has_valid_option" value="true"}}
											{{/if}}
										{{/foreach}}
										{{if $select_has_valid_option=='false'}}
											<option value="__INVALID__" selected>{{$lang.website_ui.theme_settings_field_label_missing}}</option>
										{{/if}}
									</select>
								{{elseif $field.type=='multiselect'}}
									{{if count($field.options)>0}}
										{{foreach from=$field.options|smarty:nodefaults item="option"}}
											<span class="de_lv_pair"><input type="checkbox" name="{{$field.id}}[]" value="{{$option.id}}" {{if in_array($option.id, $field.value)}}checked{{/if}} {{if $field.unsupported==1}}disabled{{/if}}/><label>{{$option.title}}</label></span>
										{{/foreach}}
									{{else}}
										{{$lang.website_ui.theme_settings_field_label_none}}
									{{/if}}
								{{elseif $field.type=='multiformat'}}
									{{if count($field.options)>0}}
										{{assign var="number_of_valid_options" value=0}}
										{{foreach from=$field.options|smarty:nodefaults item="group"}}
											<span>
												{{$group.title}}:
												<select name="{{$field.id}}[]">
													<option value=""></option>
													{{foreach from=$group.formats|smarty:nodefaults item="format"}}
														<option value="{{$format.id}}" {{if in_array($format.id, $field.value)}}selected{{assign var="number_of_valid_options" value=$number_of_valid_options+1}}{{/if}}>{{$format.title}}</option>
													{{/foreach}}
												</select>
											</span>
										{{/foreach}}
										{{if $number_of_valid_options!=count($field.value)}}
											<span>
												<select name="{{$field.id}}[]">
													<option value=""></option>
													<option value="__INVALID__" selected>{{$lang.website_ui.theme_settings_field_label_missing}}</option>
												</select>
											</span>
										{{/if}}
									{{else}}
										{{$lang.website_ui.theme_settings_field_label_none}}
									{{/if}}
								{{elseif $field.type=='image'}}
									<div class="de_fu">
										<div class="js_params">
											<span class="js_param">title={{$field.title}}</span>
											<span class="js_param">accept={{$config.image_allowed_ext}}</span>
											{{if $field.url!=''}}
												<span class="js_param">preview_url={{$field.url}}</span>
											{{/if}}
										</div>
										<input type="text" name="{{$field.id}}" maxlength="100" {{if $field.value!=''}}value="{{$field.value}}"{{/if}}/>
										<input type="hidden" name="{{$field.id}}_hash" {{if $field.is_invalid==1}}value="__INVALID__"{{/if}}/>
										<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
										<input type="button" class="de_fu_remove {{if $field.value==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
									</div>
								{{elseif $field.type=='group'}}
									<table class="control_group">
										{{foreach from=$field.group|smarty:nodefaults item="field_inner"}}
											{{if $field.hidden!=1}}
												<tr><td>
													{{if $field_inner.type=='checkbox'}}
														<span class="de_lv_pair"><input type="checkbox" name="{{$field_inner.id}}" value="true" {{if $field_inner.value=='true'}}checked{{/if}} {{if $field_inner.unsupported==1}}disabled{{/if}}/><label>{{$field_inner.title}}</label></span>
													{{/if}}
												</td></tr>
											{{/if}}
										{{/foreach}}
									</table>
								{{elseif $field.type=='block'}}
									<span>
										{{foreach from=$field.blocks|smarty:nodefaults item="block" name="blocks"}}
											{{if $field.unsupported==1 || $block.unsupported==1 || $block.unused==1}}
												<span class="disabled">{{$block.title}}</span>
											{{else}}
												<a href="{{$block.link|smarty:nodefaults}}">{{$block.title}}</a>
											{{/if}}
											{{if !$smarty.foreach.blocks.last}}&nbsp;/&nbsp;{{/if}}
										{{/foreach}}
									</span>
								{{/if}}
								{{if $field.hint!=''}}
									<span class="de_hint">{{$field.hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
				{{/foreach}}
			{{/foreach}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
	</div>
</form>