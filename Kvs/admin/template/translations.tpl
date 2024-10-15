{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='change'}}

{{assign var="can_edit_all" value="0"}}
{{foreach item="item" from=$list_languages|smarty:nodefaults}}
	{{assign var="permission_id" value="localization|`$item.code`"}}
	{{if in_array($permission_id,$smarty.session.permissions)}}
		{{assign var="can_edit_all" value="1"}}
	{{/if}}
{{/foreach}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="translation_edit">
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.settings.submenu_option_translations_list}}</a> /
				{{if $item_type==1}}
					{{if in_array('videos|view',$smarty.session.permissions)}}
						<a href="videos.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==2}}
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==3}}
					{{if in_array('content_sources|view',$smarty.session.permissions)}}
						<a href="content_sources.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==4}}
					{{if in_array('models|view',$smarty.session.permissions)}}
						<a href="models.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==5}}
					{{if in_array('dvds|view',$smarty.session.permissions)}}
						<a href="dvds.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==6}}
					{{if in_array('categories|view',$smarty.session.permissions)}}
						<a href="categories.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==7}}
					{{if in_array('category_groups|view',$smarty.session.permissions)}}
						<a href="categories_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==8}}
					{{if in_array('content_sources_groups|view',$smarty.session.permissions)}}
						<a href="content_sources_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==9}}
					{{if in_array('tags|view',$smarty.session.permissions)}}
						<a href="tags.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==10}}
					{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
						<a href="dvds_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==14}}
					{{if in_array('models_groups|view',$smarty.session.permissions)}}
						<a href="models_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{/if}}
				/
				{{$lang.settings.translation_edit}}
			</h1>
		</div>
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
			{{if $item_type==1}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_video_info}}</h2></td>
				</tr>
				{{if count($smarty.post.screenshots)>0}}
					<tr>
						<td class="de_control" colspan="2">
							<div class="de_img_list">
								<div class="de_img_list_main">
									{{foreach item="item" from=$smarty.post.screenshots}}
										<div class="de_img_list_item">
											<div class="de_img_list_thumb">
												<img src="{{$item}}?rnd={{$smarty.now}}" alt=""/>
											</div>
										</div>
									{{/foreach}}
								</div>
							</div>
						</td>
					</tr>
				{{else}}
					<tr>
						<td class="de_control" colspan="2">
							<span class="de_hint">{{$lang.settings.translation_edit_object_type_video_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_models}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.models}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_content_source}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.content_source|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==2}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_album_info}}</h2></td>
				</tr>
				{{if count($smarty.post.images)>0}}
					<tr>
						<td class="de_control" colspan="4">
							<div class="de_img_list">
								<div class="de_img_list_main">
									{{foreach item="item" from=$smarty.post.images}}
										<div class="de_img_list_item">
											<div class="de_img_list_thumb">
												<img src="{{$item}}?rnd={{$smarty.now}}" alt=""/>
											</div>
										</div>
									{{/foreach}}
								</div>
							</div>
						</td>
					</tr>
				{{else}}
					<tr>
						<td class="de_control" colspan="2">
							<span class="de_hint">{{$lang.settings.translation_edit_object_type_album_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_models}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.models}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_content_source}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.content_source|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==3}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_content_source_info}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_group}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.group|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==4}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_model_info}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_group}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.group|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==5}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_dvd_info}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_group}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.group|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_models}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.models}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==6}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_category_info}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_group}}</td>
					<td class="de_control">
						<span>
							{{$smarty.post.group|default:$lang.common.undefined}}
						</span>
					</td>
				</tr>
			{{elseif $item_type==10}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_dvd_group_info}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_tags}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.tags}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_categories}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.categories}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_object_models}}</td>
					<td class="de_control">
						<span>
							{{foreach name="data" item="item" from=$smarty.post.models}}
								{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{foreachelse}}
								{{$lang.common.undefined}}
							{{/foreach}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_title_translation}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_original_title}}</td>
				<td class="de_control">
					<input type="text" name="{{$title_selector}}" maxlength="255" value="{{$smarty.post.$title_selector}}" readonly/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_original_directory}}</td>
				<td class="de_control">
					<input type="text" name="{{$dir_selector}}" maxlength="255" value="{{$smarty.post.$dir_selector}}" readonly/>
				</td>
			</tr>
			{{foreach item="item" from=$list_languages|smarty:nodefaults}}
				{{assign var="permission_id" value="localization|`$item.code`"}}
				{{assign var="can_edit_language" value="0"}}
				{{if in_array($permission_id,$smarty.session.permissions)}}
					{{assign var="can_edit_language" value="1"}}
				{{/if}}
				{{assign var="language_selector" value="`$title_selector`_`$item.code`"}}
				{{assign var="column_ok_id" value="ok_`$item.code`"}}
				{{assign var="column_title_id" value="title_`$item.code`"}}
				{{if $can_edit_language==1}}
					<tr>
						<td class="de_label">{{$lang.settings.translation_field_language_title|replace:"%1%":$item.title}}</td>
						<td class="de_control">
							<input type="text" name="{{$language_selector}}" maxlength="255" class="{{if $can_edit_language==1}}preserve_editing{{/if}}" value="{{$smarty.post.$language_selector}}" {{if $can_edit_language==0}}readonly{{/if}}/>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
					{{if $item.is_directories_localize==1}}
						{{assign var="language_selector" value="`$dir_selector`_`$item.code`"}}
						<tr>
							<td class="de_label">{{$lang.settings.translation_field_language_directory|replace:"%1%":$item.title}}</td>
							<td class="de_control">
								<input type="text" name="{{$language_selector}}" maxlength="255" class="{{if $can_edit_language==1}}preserve_editing{{/if}}" value="{{$smarty.post.$language_selector}}" {{if $can_edit_language==0}}readonly{{/if}}/>
								<span class="de_hint">{{$lang.settings.translation_field_language_directory_hint|replace:"%1%":$item.title|replace:"%2%":$lang.settings.translation_field_language_title|replace:"%1%":$item.title}}</span>
							</td>
						</tr>
					{{/if}}
				{{/if}}
			{{/foreach}}
			{{if $desc_selector!=''}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.translation_divider_description_translation}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.translation_field_original_description}}</td>
					<td class="de_control">
						<textarea name="{{$desc_selector}}" class="{{if $smarty.session.userdata[$tiny_mce_key]=='1'}}tinymce{{/if}}" cols="40" rows="3" readonly>{{$smarty.post.$desc_selector}}</textarea>
						<span class="de_hint"><span class="de_str_len_value"></span></span>
					</td>
				</tr>
				{{foreach item="item" from=$list_languages|smarty:nodefaults}}
					{{assign var="permission_id" value="localization|`$item.code`"}}
					{{assign var="can_edit_language" value="0"}}
					{{if in_array($permission_id,$smarty.session.permissions)}}
						{{assign var="can_edit_language" value="1"}}
					{{/if}}
					{{assign var="language_selector" value="`$desc_selector`_`$item.code`"}}
					{{assign var="column_ok_id" value="ok_`$item.code`"}}
					{{assign var="column_desc_id" value="description_`$item.code`"}}
					{{if $can_edit_language==1}}
						{{if $item.translation_scope==0}}
							<tr>
								<td class="de_label">{{$lang.settings.translation_field_language_description|replace:"%1%":$item.title}}</td>
								<td class="de_control">
									<textarea name="{{$language_selector}}" class="{{if $can_edit_language==1}}preserve_editing{{/if}} {{if $smarty.session.userdata[$tiny_mce_key]=='1'}}tinymce{{/if}}" cols="40" rows="3" {{if $can_edit_language==0}}readonly{{/if}}>{{$smarty.post.$language_selector}}</textarea>
									<span class="de_hint"><span class="de_str_len_value"></span></span>
								</td>
							</tr>
						{{/if}}
					{{/if}}
				{{/foreach}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		<input type="hidden" name="item_type" value="{{$smarty.get.item_type}}"/>
		{{if $smarty.session.save.options.default_save_button==1}}
			<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{else}}
			<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
			<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
			<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
		{{/if}}
	</div>
</form>

{{else}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_search">
				<i class="icon icon-action-search"></i>
				<input type="text" name="se_text" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}" placeholder="{{$lang.common.dg_filter_search}}"/>
				<i class="icon icon-action-forward dgf_search_apply"></i>
				{{if count($search_fields)>0}}
					<div class="dgf_search_layer">
						<span>{{$lang.common.dg_filter_search_in}}:</span>
						<ul>
							{{assign var="search_everywhere" value="true"}}
							{{foreach from=$search_fields|smarty:nodefaults item="field"}}
								<li>
									{{assign var="option_id" value="se_text_`$field.id`"}}
									<input type="hidden" name="{{$option_id}}" value="0"/>
									<span class="de_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked{{/if}}/><label>{{$field.title}}</label></span>
									{{if $smarty.session.save.$page_name[$option_id]!=1}}
										{{assign var="search_everywhere" value="false"}}
									{{/if}}
								</li>
							{{/foreach}}
							<li class="dgf_everywhere">
								<span class="de_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></span>
							</li>
						</ul>
					</div>
				{{/if}}
			</div>
			<div class="dgf_filter">
				<div class="drop">
					<i class="icon icon-action-groupby"></i><span>{{$lang.settings.translation_field_object_type}}</span>
					<ul>
						<li {{if $smarty.session.save.$page_name.se_object_type=='1'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=1">{{$lang.common.object_type_videos}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='2'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=2">{{$lang.common.object_type_albums}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='6'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=6">{{$lang.common.object_type_categories}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='7'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=7">{{$lang.common.object_type_category_groups}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='4'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=4">{{$lang.common.object_type_models}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='14'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=14">{{$lang.common.object_type_model_groups}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='3'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=3">{{$lang.common.object_type_content_sources}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='8'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=8">{{$lang.common.object_type_content_source_groups}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='9'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=9">{{$lang.common.object_type_tags}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='5'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=5">{{$lang.common.object_type_dvds}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_object_type=='10'}}class="selected"{{/if}}><a href="{{$page_name}}?se_object_type=10">{{$lang.common.object_type_dvd_groups}}</a></li>
					</ul>
				</div>
			</div>
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
				<div class="drop">
					<i class="icon icon-action-list"></i><span>{{$lang.common.dg_list_view}}</span>
					<ul>
						<li><a href="{{$page_name}}?se_grid_preset=">{{$lang.common.dg_list_view_default}}</a></li>
						{{foreach from=$grid_presets item="preset"}}
							<li {{if $smarty.session.save.$page_name.grid_preset==$preset.title}}class="selected"{{/if}}><a href="{{$page_name}}?se_grid_preset={{$preset.title}}">{{$preset.title}}</a></li>
						{{/foreach}}
					</ul>
				</div>
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$lang.common.dg_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_filters"><i class="icon icon-action-filter"></i>{{$lang.common.dg_filter_filters}}</a>
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$lang.common.dg_filter_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$smarty.session.save.$page_name.grid_preset}}" maxlength="100" placeholder="{{$lang.common.dg_filter_save_view}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$lang.common.dg_filter_btn_submit}}"/>
				</div>
			</div>
			<div class="dgf_advanced_filters">
				<div class="dgf_filter">
					<select name="se_translation_missing_for">
						<option value="">{{$lang.settings.translation_filter_translation_required}}...</option>
						{{foreach item="item" from=$list_languages|smarty:nodefaults}}
							<option value="{{$item.code}}" {{if $smarty.session.save.$page_name.se_translation_missing_for==$item.code}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_translation_having_for">
						<option value="">{{$lang.settings.translation_filter_translation_done}}...</option>
						{{foreach item="item" from=$list_languages|smarty:nodefaults}}
							<option value="{{$item.code}}" {{if $smarty.session.save.$page_name.se_translation_having_for==$item.code}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_translated_date_from}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_from}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_translated_date_from" value="{{$smarty.session.save.$page_name.se_translated_date_from}}" placeholder="{{$lang.settings.translation_filter_translated_date_from}}...">
					</div>
				</div>
				<div class="dgf_filter">
					<div class="calendar">
						{{if $smarty.session.save.$page_name.se_translated_date_to}}
							<div class="js_params">
								<span class="js_param">prefix={{$lang.common.dg_filter_range_to}}</span>
							</div>
						{{/if}}
						<input type="text" name="se_translated_date_to" value="{{$smarty.session.save.$page_name.se_translated_date_to}}" placeholder="{{$lang.settings.translation_filter_translated_date_to}}...">
					</div>
				</div>
			</div>
			<div class="dgf_advanced_columns">
				{{assign var="table_columns_display_mode" value="selector"}}
				{{include file="table_columns_inc.tpl"}}
			</div>
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
					<col width="1%"/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						{{assign var="table_columns_display_mode" value="header"}}
						{{include file="table_columns_inc.tpl"}}
						<td>{{$lang.common.dg_actions}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=2}}
					{{foreach from=$table_fields|smarty:nodefaults item="field"}}
						{{if $field.is_enabled==1}}
							{{assign var="table_columns_visible" value=$table_columns_visible+1}}
						{{/if}}
					{{/foreach}}
					<tr class="err_list {{if (count($data)>0 || $total_num==0) && !is_array($smarty.post.errors)}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{if count($data)==0 && $total_num>0}}{{$lang.common.dg_list_error}}{{else}}{{$lang.validation.common_header}}{{/if}}</div>
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
					{{if $smarty.session.save.$page_name.se_object_type>0}}
						{{if count($data)==0 && $total_num==0}}
							<tr class="dg_empty">
								<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
							</tr>
						{{/if}}
						{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td class="nowrap">
									<a {{if $item.is_editing_forbidden!=1}}href="{{$item.editor_url}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
									<a class="additional" title="{{$lang.common.dg_actions_additional}}">
										<i class="icon icon-action-settings"></i>
										<span class="js_params">
										</span>
									</a>
								</td>
							</tr>
						{{/foreach}}
					{{else}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.settings.translation_field_object_type_hint_list}}</td>
						</tr>
					{{/if}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>

		</div>
	</form>
</div>

{{/if}}