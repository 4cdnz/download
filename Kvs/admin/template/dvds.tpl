{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('dvds|edit_all',$smarty.session.permissions) || (in_array('dvds|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var="can_edit_all" value=1}}
{{else}}
	{{assign var="can_edit_all" value=0}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de {{if $can_edit_all==0}}de_readonly{{/if}}" name="{{$smarty.now}}" data-editor-name="dvd_edit">
	<div>
		{{if $options.DVD_COVER_OPTION==0}}
			<input type="hidden" name="cover2_front" value="{{$smarty.post.cover2_front}}"/>
			<input type="hidden" name="cover2_front_hash"/>
			<input type="hidden" name="cover2_back" value="{{$smarty.post.cover2_back}}"/>
			<input type="hidden" name="cover2_back_hash"/>
		{{/if}}
		{{if $config.dvds_mode!='channels'}}
			<input type="hidden" name="user" value="{{$smarty.post.user}}"/>
			<input type="hidden" name="is_video_upload_allowed" value="{{$smarty.post.is_video_upload_allowed}}"/>
		{{/if}}
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
		<input type="hidden" name="custom4" value="{{$smarty.post.custom4}}"/>
		<input type="hidden" name="custom5" value="{{$smarty.post.custom5}}"/>
		<input type="hidden" name="custom6" value="{{$smarty.post.custom6}}"/>
		<input type="hidden" name="custom7" value="{{$smarty.post.custom7}}"/>
		<input type="hidden" name="custom8" value="{{$smarty.post.custom8}}"/>
		<input type="hidden" name="custom9" value="{{$smarty.post.custom9}}"/>
		<input type="hidden" name="custom10" value="{{$smarty.post.custom10}}"/>
		<input type="hidden" name="custom_file1" value="{{$smarty.post.custom_file1}}"/>
		<input type="hidden" name="custom_file1_hash"/>
		<input type="hidden" name="custom_file2" value="{{$smarty.post.custom_file2}}"/>
		<input type="hidden" name="custom_file2_hash"/>
		<input type="hidden" name="custom_file3" value="{{$smarty.post.custom_file3}}"/>
		<input type="hidden" name="custom_file3_hash"/>
		<input type="hidden" name="custom_file4" value="{{$smarty.post.custom_file4}}"/>
		<input type="hidden" name="custom_file4_hash"/>
		<input type="hidden" name="custom_file5" value="{{$smarty.post.custom_file5}}"/>
		<input type="hidden" name="custom_file5_hash"/>
	</div>
	<div class="de_main">
		<div class="de_header">
			<h1>
				<a href="{{$page_name}}">{{$lang.videos.submenu_option_dvds_list}}</a>
				/
				{{if $smarty.get.action=='add_new'}}
					{{$lang.videos.dvd_add}}
				{{else}}
					{{if $smarty.post.dvd_group_id>0}}
						{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
							<a href="dvds_groups.php?action=change&amp;item_id={{$smarty.post.dvd_group_id}}">{{$smarty.post.dvd_group}}</a>
						{{else}}
							{{$smarty.post.dvd_group}}
						{{/if}}
						/
					{{/if}}
					{{$lang.videos.dvd_edit|replace:"%1%":$smarty.post.title}}
				{{/if}}
			</h1>
		</div>
		<table class="de_editor">
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="3">
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
			{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_review}}</h2></td>
				</tr>
				<tr>
					<td class="de_simple_text" colspan="3">
						<span class="de_hint">{{$lang.videos.dvd_divider_review_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_reviewed}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="is_reviewed" value="1"/><label>{{$lang.videos.dvd_field_reviewed_yes}}</label></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.videos.dvd_field_title}}</td>
				<td class="de_control">
					<input type="text" name="title" maxlength="255" value="{{$smarty.post.title}}"/>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
				{{if is_array($sidebar_fields)}}
					{{assign var="sidebar_rowspan" value="8"}}
					{{if $smarty.post.website_link!=''}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
					{{/if}}
					{{if $options.DVD_COVER_OPTION>0}}
						{{assign var="sidebar_rowspan" value=$sidebar_rowspan+2}}
					{{/if}}

					{{assign var="image_field" value=""}}
					{{if $smarty.post.cover1_front!=''}}
						{{assign var="image_field" value="cover1_front"}}
					{{/if}}
					{{if $options.DVD_COVER_OPTION>0}}
						{{assign var="image_size1" value="x"|explode:$options.DVD_COVER_1_SIZE}}
						{{assign var="image_size2" value="x"|explode:$options.DVD_COVER_2_SIZE}}
						{{if ($image_size1[0]>$image_size2[0] || $smarty.post.cover1_front=='') && $smarty.post.cover2_front!=''}}
							{{assign var="image_field" value="cover2_front"}}
						{{/if}}
					{{/if}}
					{{if $image_field!=''}}
						{{assign var="sidebar_image_url" value="`$config.content_url_dvds`/`$smarty.post.dvd_id`/`$smarty.post.$image_field`"}}
					{{/if}}

					{{include file="editor_sidebar_inc.tpl"}}
				{{/if}}
			</tr>
			{{if $smarty.get.action=='change'}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_directory}}</td>
					<td class="de_control">
						<input type="text" name="dir" maxlength="255" value="{{$smarty.post.dir}}"/>
						<span class="de_hint">{{$lang.videos.dvd_field_directory_hint|replace:"%1%":$lang.videos.dvd_field_title}}</span>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.website_link!=''}}
				<tr data-field-name="website_link">
					<td class="de_label">{{$lang.videos.dvd_field_website_link}}</td>
					<td class="de_control">
						<a href="{{$smarty.post.website_link}}">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_description}}</td>
				<td class="de_control">
					<textarea name="description" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					<span class="de_hint"><span class="de_str_len_value"></span></span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_synonyms}}</td>
				<td class="de_control">
					<textarea name="synonyms" cols="40" rows="4">{{$smarty.post.synonyms}}</textarea>
					<span class="de_hint">{{$lang.videos.dvd_field_synonyms_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_status}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked{{/if}}/><label>{{$lang.videos.dvd_field_status_active}}</label></span>
					<span class="de_hint">{{$lang.videos.dvd_field_status_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_group}}</td>
				<td class="de_control" colspan="2">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvd_groups</span>
							{{if in_array('dvds_groups|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.post.dvd_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="dvd_group" maxlength="255" value="{{$smarty.post.dvd_group}}"/>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_cover1_front}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_cover1_front}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover1_front!='' && in_array(end(explode(".",$smarty.post.cover1_front)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover1_front}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover1_front" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover1_front!=''}}value="{{$smarty.post.cover1_front}}"{{/if}}/>
						<input type="hidden" name="cover1_front_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.videos.dvd_field_cover1_front_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_1_SIZE}}</a>)</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_cover1_back}}</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_cover1_back}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover1_back!='' && in_array(end(explode(".",$smarty.post.cover1_back)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover1_back}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover1_back" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover1_back!=''}}value="{{$smarty.post.cover1_back}}"{{/if}}/>
						<input type="hidden" name="cover1_back_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.videos.dvd_field_cover1_back_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_1_SIZE}}</a>)</span>
				</td>
			</tr>
			{{if $options.DVD_COVER_OPTION>0}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_cover2_front}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.dvd_field_cover2_front}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.cover2_front!='' && in_array(end(explode(".",$smarty.post.cover2_front)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover2_front}}</span>
								{{/if}}
							</div>
							<input type="text" name="cover2_front" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover2_front!=''}}value="{{$smarty.post.cover2_front}}"{{/if}}/>
							<input type="hidden" name="cover2_front_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.dvd_field_cover2_front_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_2_SIZE}}</a>){{if $options.DVD_COVER_OPTION==1}}; {{$lang.videos.dvd_field_cover2_front_hint2|replace:"%1%":$lang.videos.dvd_field_cover1_front}}{{/if}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_cover2_back}}</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.dvd_field_cover2_back}}</span>
								<span class="js_param">accept={{$config.image_allowed_ext}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.cover2_back!='' && in_array(end(explode(".",$smarty.post.cover2_back)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover2_back}}</span>
								{{/if}}
							</div>
							<input type="text" name="cover2_back" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover2_back!=''}}value="{{$smarty.post.cover2_back}}"{{/if}}/>
							<input type="hidden" name="cover2_back_hash"/>
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.dvd_field_cover2_back_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_2_SIZE}}</a>){{if $options.DVD_COVER_OPTION==1}}; {{$lang.videos.dvd_field_cover2_back_hint2|replace:"%1%":$lang.videos.dvd_field_cover1_back}}{{/if}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.videos.dvd_field_rating}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="avg_rating" size="10" value="{{$smarty.post.rating|replace:",":"."|floatval|round:1}}"/>
					</span>
					<span>
						{{$lang.videos.dvd_field_rating_votes}}:
						<input type="text" name="rating_amount" size="5" value="{{$smarty.post.rating_amount}}"/>
					</span>
					<span class="de_hint">{{$lang.videos.dvd_field_rating_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_release_year}}</td>
				<td class="de_control">
					<input type="text" name="release_year" size="10" value="{{$smarty.post.release_year}}"/>
				</td>
			</tr>
			{{if $config.dvds_mode=='channels'}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_community}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_user}}</td>
					<td class="de_control" colspan="2">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|add',$smarty.session.permissions)}}
									<span class="js_param">allow_creation=true</span>
								{{/if}}
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$smarty.post.user_id}}</span>
								{{/if}}
							</div>
							<input type="text" name="user" maxlength="255" value="{{$smarty.post.user}}"/>
						</div>
						<span class="de_hint">{{$lang.videos.dvd_field_user_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_video_upload_allowed}}</td>
					<td class="de_control" colspan="2">
						<select name="is_video_upload_allowed">
							<option value="0" {{if $smarty.post.is_video_upload_allowed=='0'}}selected{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_public}}</option>
							<option value="1" {{if $smarty.post.is_video_upload_allowed=='1'}}selected{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_friends}}</option>
							<option value="2" {{if $smarty.post.is_video_upload_allowed=='2'}}selected{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_owner}}</option>
						</select>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_tokens_required}}</td>
					<td class="de_control" colspan="2">
						<input type="text" name="tokens_required" maxlength="10" size="10" value="{{$smarty.post.tokens_required}}"/>
						<span class="de_hint">{{$lang.videos.dvd_field_tokens_required_hint|replace:"%1%":$options.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_categorization}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_tags}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_tags}}</span>
							<span class="js_param">url=async/insight.php?type=tags</span>
							<span class="js_param">submit_mode=simple</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
							{{if in_array('tags|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_field_tags_empty}}</span>
						</div>
						<div class="list"></div>
						<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_tag"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_field_tags_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_categories}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_categories}}</span>
							<span class="js_param">url=async/insight.php?type=categories</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=category_ids[]</span>
							{{if in_array('categories|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_field_categories_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
							<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_category"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_field_categories_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_models}}</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_models}}</span>
							<span class="js_param">url=async/insight.php?type=models</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=model_ids[]</span>
							{{if in_array('models|add',$smarty.session.permissions)}}
								<span class="js_param">allow_creation=true</span>
							{{/if}}
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
							{{/if}}
							<span class="js_param">empty_message={{$lang.videos.dvd_field_models_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
							<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_model"/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
								<input type="button" class="all" value="{{$lang.videos.dvd_field_models_all}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_flags}}</td>
					<td class="de_control" colspan="2">
						<div class="de_deletable_list">
							<div class="js_params">
								<span class="js_param">submit_name=delete_flags[]</span>
								<span class="js_param">empty_message={{$lang.videos.dvd_field_flags_empty}}</span>
								{{if $can_edit_all!=1}}
									<span class="js_param">forbid_delete=true</span>
								{{/if}}
							</div>
							<div class="list">
								{{if count($smarty.post.flags)>0}}
									{{foreach name="data" item="item" from=$smarty.post.flags|smarty:nodefaults}}
										<span class="item"><a data-item-id="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}})</a>{{if !$smarty.foreach.data.last}}<span class="separator">,</span>{{/if}}</span>
									{{/foreach}}
								{{else}}
									{{$lang.videos.dvd_field_flags_empty}}
								{{/if}}
							</div>
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FIELD_1==1 || $options.ENABLE_DVD_FIELD_2==1 || $options.ENABLE_DVD_FIELD_3==1 || $options.ENABLE_DVD_FIELD_4==1 || $options.ENABLE_DVD_FIELD_5==1 || $options.ENABLE_DVD_FIELD_6==1 || $options.ENABLE_DVD_FIELD_7==1 || $options.ENABLE_DVD_FIELD_8==1 || $options.ENABLE_DVD_FIELD_9==1 || $options.ENABLE_DVD_FIELD_10==1
				|| $options.ENABLE_DVD_FILE_FIELD_1==1 || $options.ENABLE_DVD_FILE_FIELD_2==1 || $options.ENABLE_DVD_FILE_FIELD_3==1 || $options.ENABLE_DVD_FILE_FIELD_4==1 || $options.ENABLE_DVD_FILE_FIELD_5==1}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_customization}}</h2></td>
				</tr>
				{{if $options.ENABLE_DVD_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom1" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom2" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom3" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_4_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom4" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom4}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_5_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom5" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom5}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_6==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_6_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom6" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom6}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_7==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_7_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom7" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom7}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_8==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_8_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom8" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom8}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_9==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_9_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom9" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom9}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FIELD_10==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FIELD_10_NAME}}</td>
						<td class="de_control" colspan="2">
							<textarea name="custom10" class="{{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom10}}</textarea>
							<span class="de_hint"><span class="de_str_len_value"></span></span>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FILE_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FILE_FIELD_1_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.DVD_FILE_FIELD_1_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file1)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file1}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file1}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file1" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}value="{{$smarty.post.custom_file1}}"{{/if}}/>
								<input type="hidden" name="custom_file1_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FILE_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FILE_FIELD_2_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.DVD_FILE_FIELD_2_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file2)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file2}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file2}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file2" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}value="{{$smarty.post.custom_file2}}"{{/if}}/>
								<input type="hidden" name="custom_file2_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FILE_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FILE_FIELD_3_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.DVD_FILE_FIELD_3_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file3)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file3}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file3}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file3" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}value="{{$smarty.post.custom_file3}}"{{/if}}/>
								<input type="hidden" name="custom_file3_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FILE_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FILE_FIELD_4_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.DVD_FILE_FIELD_4_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file4)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file4}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file4}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file4" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}value="{{$smarty.post.custom_file4}}"{{/if}}/>
								<input type="hidden" name="custom_file4_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_DVD_FILE_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.DVD_FILE_FIELD_5_NAME}}</td>
						<td class="de_control" colspan="2">
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$options.DVD_FILE_FIELD_5_NAME}}</span>
									{{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}
										{{if in_array(end(explode(".",$smarty.post.custom_file5)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file5}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file5}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="custom_file5" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}value="{{$smarty.post.custom_file5}}"{{/if}}/>
								<input type="hidden" name="custom_file5_hash"/>
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove" value="{{$lang.common.attachment_btn_remove}}"/>
							</div>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $config.dvds_mode!='channels'}}
				<tr>
					<td class="de_separator" colspan="3"><h2>{{$lang.videos.dvd_divider_videos}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.videos.dvd_field_add_videos}}</td>
					<td class="de_control" colspan="2">
						<div class="de_insight_list">
							<div class="js_params">
								<span class="js_param">title={{$lang.videos.dvd_field_add_videos}}</span>
								<span class="js_param">url=async/insight.php?type=videos</span>
								<span class="js_param">submit_mode=compound</span>
								<span class="js_param">submit_name=add_video_ids[]</span>
								<span class="js_param">empty_message={{$lang.videos.dvd_field_add_videos_empty}}</span>
							</div>
							<div class="list"></div>
							{{if $can_edit_all==1}}
								<div class="controls">
									<input type="text" name="new_video"/>
									<input type="button" class="add" value="{{$lang.common.add}}"/>
									<input type="button" class="all" value="{{$lang.videos.dvd_field_add_videos_all}}"/>
								</div>
							{{/if}}
						</div>
					</td>
				</tr>
				{{if count($smarty.post.videos)>0}}
					<tr>
						<td class="de_simple_text" colspan="3">
							<span class="de_hint">{{$lang.videos.dvd_divider_videos_hint}}</span>
						</td>
					</tr>
					<tr>
						<td class="de_table_control" colspan="3">
							<table class="de_edit_grid">
								<tr class="eg_header">
									<td class="eg_selector"><input type="checkbox"/><label>{{$lang.common.dg_actions_detach}}</label></td>
									<td>{{$lang.videos.video_field_id}}</td>
									<td>{{$lang.videos.video_field_title}}</td>
									<td>{{$lang.videos.video_field_duration}}</td>
									<td>{{$lang.videos.video_field_status}}</td>
									<td>{{$lang.videos.video_field_visits}}</td>
									<td>{{$lang.videos.video_field_rating}}</td>
									<td>{{$lang.videos.video_field_order}}</td>
								</tr>
								{{foreach item="item" from=$smarty.post.videos|smarty:nodefaults}}
									<tr class="eg_data {{if $item.status_id==0}}disabled{{/if}}">
										<td class="eg_selector"><input type="checkbox" name="delete_video_ids[]" value="{{$item.video_id}}"/></td>
										<td class="nowrap">
											{{if in_array('videos|view',$smarty.session.permissions)}}
												<a href="videos.php?action=change&amp;item_id={{$item.video_id}}">{{$item.video_id}}</a>
											{{else}}
												{{$item.video_id}}
											{{/if}}
										</td>
										<td>{{$item.title}}</td>
										<td class="nowrap">{{$item.duration}}</td>
										<td class="nowrap">{{if $item.status_id==1}}{{$lang.videos.video_field_status_active}}{{elseif $item.status_id==2}}<span class="highlighted_text">{{$lang.videos.video_field_status_error}}</span>{{elseif $item.status_id==3}}{{$lang.videos.video_field_status_in_process}}{{elseif $item.status_id==4}}{{$lang.videos.video_field_status_deleting}}{{elseif $item.status_id==5}}{{$lang.videos.video_field_status_deleted}}{{else}}{{$lang.videos.video_field_status_disabled}}{{/if}}</td>
										<td class="nowrap {{if $item.video_viewed==0}}disabled{{/if}}">{{if $item.video_viewed>999}}{{$item.video_viewed/1000|number_format:1:".":""}}{{$lang.common.traffic_k}}{{else}}{{$item.video_viewed}}{{/if}}</td>
										<td class="nowrap {{if $item.rating==0}}disabled{{/if}}">{{$item.rating|number_format:1:".":" "}}</td>
										<td>
											<input type="text" name="video_sorting_{{$item.video_id}}" maxlength="32" value="{{$item.sort_id}}" size="3" autocomplete="off"/>
										</td>
									</tr>
								{{/foreach}}
							</table>
						</td>
					</tr>
				{{/if}}
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			{{else}}
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
			{{/if}}
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			{{if $smarty.session.save.options.default_save_button==1}}
				<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
				<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
			{{else}}
				<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
				<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
				<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
			{{/if}}
		{{/if}}
	</div>
</form>

{{else}}

{{if in_array('dvds|delete',$smarty.session.permissions)}}
	{{assign var="can_delete" value=1}}
{{else}}
	{{assign var="can_delete" value=0}}
{{/if}}
{{if in_array('dvds|edit_all',$smarty.session.permissions)}}
	{{assign var="can_edit" value=1}}
{{else}}
	{{assign var="can_edit" value=0}}
{{/if}}

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
					<select name="se_status_id">
						<option value="">{{$lang.videos.dvd_field_status}}...</option>
						<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected{{/if}}>{{$lang.videos.dvd_field_status_active}}</option>
						<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected{{/if}}>{{$lang.videos.dvd_field_status_disabled}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=dvd_groups</span>
							{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_dvd_group_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_dvd_group" value="{{$smarty.session.save.$page_name.se_dvd_group}}" placeholder="{{$lang.videos.dvd_field_group}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=tags</span>
							{{if in_array('tags|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_tag_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_tag" value="{{$smarty.session.save.$page_name.se_tag}}" placeholder="{{$lang.videos.dvd_field_tag}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=categories</span>
							{{if in_array('categories|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_category_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_category" value="{{$smarty.session.save.$page_name.se_category}}" placeholder="{{$lang.videos.dvd_field_category}}..."/>
					</div>
				</div>
				<div class="dgf_filter">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight.php?type=models</span>
							{{if in_array('models|view',$smarty.session.permissions)}}
								<span class="js_param">allow_view=true</span>
								<span class="js_param">view_id={{$smarty.session.save.$page_name.se_model_id}}</span>
							{{/if}}
						</div>
						<input type="text" name="se_model" value="{{$smarty.session.save.$page_name.se_model}}" placeholder="{{$lang.videos.dvd_field_model}}..."/>
					</div>
				</div>
				{{if $config.dvds_mode=='channels'}}
					<div class="dgf_filter">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight.php?type=users</span>
								{{if in_array('users|view',$smarty.session.permissions)}}
									<span class="js_param">allow_view=true</span>
									<span class="js_param">view_id={{$smarty.session.save.$page_name.se_user_id}}</span>
								{{/if}}
							</div>
							<input type="text" name="se_user" value="{{$smarty.session.save.$page_name.se_user}}" placeholder="{{$lang.videos.dvd_field_user}}..."/>
						</div>
					</div>
				{{/if}}
				<div class="dgf_filter">
					<select name="se_field">
						<option value="">{{$lang.common.dg_filter_field}}...</option>
						<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_description}}</option>
						<option value="empty/synonyms" {{if $smarty.session.save.$page_name.se_field=="empty/synonyms"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_synonyms}}</option>
						<option value="empty/group" {{if $smarty.session.save.$page_name.se_field=="empty/group"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_group}}</option>
						{{if $config.dvds_mode=='channels'}}
							<option value="empty/user" {{if $smarty.session.save.$page_name.se_field=="empty/user"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_user}}</option>
						{{/if}}
						<option value="empty/cover1_front" {{if $smarty.session.save.$page_name.se_field=="empty/cover1_front"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_front}}</option>
						<option value="empty/cover1_back" {{if $smarty.session.save.$page_name.se_field=="empty/cover1_back"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_back}}</option>
						<option value="empty/cover2_front" {{if $smarty.session.save.$page_name.se_field=="empty/cover2_front"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_front}}</option>
						<option value="empty/cover2_back" {{if $smarty.session.save.$page_name.se_field=="empty/cover2_back"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_back}}</option>
						<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_rating}}</option>
						<option value="empty/dvd_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/dvd_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_visits}}</option>
						{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
							<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_tokens_required}}</option>
						{{/if}}
						<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_tags}}</option>
						<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_categories}}</option>
						<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_models}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_FILE_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_description}}</option>
						<option value="filled/synonyms" {{if $smarty.session.save.$page_name.se_field=="filled/synonyms"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_synonyms}}</option>
						<option value="filled/group" {{if $smarty.session.save.$page_name.se_field=="filled/group"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_group}}</option>
						{{if $config.dvds_mode=='channels'}}
							<option value="filled/user" {{if $smarty.session.save.$page_name.se_field=="filled/user"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_user}}</option>
						{{/if}}
						<option value="filled/cover1_front" {{if $smarty.session.save.$page_name.se_field=="filled/cover1_front"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_front}}</option>
						<option value="filled/cover1_back" {{if $smarty.session.save.$page_name.se_field=="filled/cover1_back"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_back}}</option>
						<option value="filled/cover2_front" {{if $smarty.session.save.$page_name.se_field=="filled/cover2_front"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_front}}</option>
						<option value="filled/cover2_back" {{if $smarty.session.save.$page_name.se_field=="filled/cover2_back"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_back}}</option>
						<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_rating}}</option>
						<option value="filled/dvd_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/dvd_viewed"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_visits}}</option>
						{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
							<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_tokens_required}}</option>
						{{/if}}
						<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_tags}}</option>
						<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_categories}}</option>
						<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_models}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
						{{section name="data" start="1" loop="6"}}
							{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
							{{assign var="custom_field_name_id" value="DVD_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							{{assign var="custom_field_enable_id" value="ENABLE_DVD_FILE_FIELD_`$smarty.section.data.index`"}}
							{{if $options[$custom_field_enable_id]==1}}
								<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
							{{/if}}
						{{/section}}
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_usage">
						<option value="">{{$lang.common.dg_filter_usage}}...</option>
						<option value="used/videos" {{if $smarty.session.save.$page_name.se_usage=="used/videos"}}selected{{/if}}>{{$lang.common.dg_filter_usage_videos}}</option>
						<option value="notused/videos" {{if $smarty.session.save.$page_name.se_usage=="notused/videos"}}selected{{/if}}>{{$lang.common.dg_filter_usage_no_videos}}</option>
					</select>
				</div>
				<div class="dgf_filter">
					<select name="se_flag_id">
						<option value="">{{$lang.common.dg_filter_flag}}...</option>
						{{foreach item="item_flag" from=$list_flags_dvds|smarty:nodefaults}}
							<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected{{/if}}>{{$item_flag.title}}</option>
						{{/foreach}}
					</select>
				</div>
				{{if $config.dvds_mode=='channels'}}
					<div class="dgf_filter">
						<select name="se_review_flag">
							<option value="">{{$lang.common.dg_filter_review_flag}}...</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
						</select>
					</div>
				{{/if}}
			</div>
			<div class="dgf_advanced_columns">
				{{assign var="table_columns_display_mode" value="selector"}}
				{{include file="table_columns_inc.tpl"}}
			</div>
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		{{assign var="fields_other_than_thumb" value="0"}}
		{{foreach from=$table_fields|smarty:nodefaults item="field"}}
			{{if $field.is_enabled==1}}
				{{if $field.type!='thumb' && $field.type!='id'}}
					{{assign var="fields_other_than_thumb" value=$fields_other_than_thumb+1}}
				{{/if}}
			{{/if}}
		{{/foreach}}
		<div class="dg {{if $fields_other_than_thumb==0}}thumbs{{/if}}">
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
					{{if count($data)==0 && $total_num==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td class="nowrap">
								<a {{if $item.is_editing_forbidden!=1}}href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}"{{/if}} class="edit {{if $item.is_editing_forbidden==1}}disabled{{/if}}" title="{{$lang.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
								<a class="additional" title="{{$lang.common.dg_actions_additional}}">
									<i class="icon icon-action-settings"></i>
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.title}}</span>
										{{if $item.website_link==''}}
											<span class="js_param">website_link_disable=true</span>
										{{else}}
											<span class="js_param">website_link={{$item.website_link}}</span>
										{{/if}}
										{{if $item.is_review_needed!=1}}
											<span class="js_param">mark_reviewed_hide=true</span>
										{{/if}}
										{{if $item.videos_amount==0}}
											<span class="js_param">delete_with_videos_disable=true</span>
										{{/if}}
										{{if $item.status_id==0}}
											<span class="js_param">deactivate_hide=true</span>
										{{else}}
											<span class="js_param">activate_hide=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">icon=action-delete</span>
						<span class="js_param">destructive=true</span>
					</li>
					{{if in_array('videos|delete',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete_with_videos&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.videos.dvd_action_delete_with_videos}}</span>
							<span class="js_param">confirm={{$lang.videos.dvd_action_delete_with_videos_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">disable=${delete_with_videos_disable}</span>
							<span class="js_param">prompt_value=yes</span>
							<span class="js_param">icon=action-delete</span>
							<span class="js_param">destructive=true</span>
						</li>
					{{/if}}
				{{/if}}
				{{if $can_edit==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
						<span class="js_param">hide=${activate_hide}</span>
						<span class="js_param">icon=action-activate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">hide=${deactivate_hide}</span>
						<span class="js_param">icon=action-deactivate</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
						<span class="js_param">hide=${mark_reviewed_hide}</span>
						<span class="js_param">icon=action-approve</span>
					</li>
				{{/if}}
				{{if in_array('users|manage_comments',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=5&amp;object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-comment</span>
						<span class="js_param">subicon=action-add</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=${website_link}</span>
					<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
					<span class="js_param">disable=${website_link_disable}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">icon=action-open</span>
				</li>
				{{if in_array('system|administration',$smarty.session.permissions)}}
					<li class="js_params">
						<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=5&amp;se_object_id=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">icon=type-audit</span>
						<span class="js_param">subicon=action-search</span>
					</li>
				{{/if}}
			</ul>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$lang.common.dg_batch_actions}}</option>
					{{if $can_delete==1}}
						<option value="delete">{{$lang.common.dg_batch_actions_delete|replace:"%1%":'${count}'}}</option>
						{{if in_array('videos|delete',$smarty.session.permissions)}}
							<option value="delete_with_videos">{{$lang.videos.dvd_batch_action_delete_with_videos|replace:"%1%":'${count}'}}</option>
						{{/if}}
					{{/if}}
					{{if $can_edit==1}}
						<option value="activate">{{$lang.common.dg_batch_actions_activate|replace:"%1%":'${count}'}}</option>
						<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate|replace:"%1%":'${count}'}}</option>
						<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed|replace:"%1%":'${count}'}}</option>
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.type=='sorting' && $field.is_enabled==1}}
								<option value="reorder">{{$lang.common.dg_batch_actions_reorder|replace:"%1%":'${all}'}}</option>
							{{/if}}
						{{/foreach}}
					{{/if}}
				</select>
				<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled/>
			</div>

			{{include file="navigation.tpl"}}

			<div class="dgb_info">
				{{$lang.common.dg_list_window|smarty:nodefaults|replace:"%1%":$total_num|replace:"%2%":$num_on_page}}
			</div>

			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_with_videos</span>
					<span class="js_param">confirm={{$lang.videos.dvd_batch_action_delete_with_videos_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">prompt_value=yes</span>
					<span class="js_param">destructive=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=reorder</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_reorder_confirm|replace:"%1%":'${all}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}