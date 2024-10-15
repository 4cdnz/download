{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.request.page=='embed'}}
	{{assign var="player_path" value="`$config.content_url_other`/player/embed"}}
	{{if $smarty.request.embed_profile_id!=''}}
		{{if $smarty.request.embed_profile_id!='new'}}
			{{assign var="player_path" value=$smarty.request.embed_profile_id|md5}}
			{{assign var="player_path" value="`$config.content_url_other`/player/embed/`$player_path`"}}
		{{/if}}
	{{/if}}
{{else}}
	{{if $player_data.access_level==0}}
		{{assign var="player_path" value="`$config.content_url_other`/player"}}
	{{elseif $player_data.access_level==2}}
		{{assign var="player_path" value="`$config.content_url_other`/player/active"}}
	{{elseif $player_data.access_level==3}}
		{{assign var="player_path" value="`$config.content_url_other`/player/premium"}}
	{{/if}}
{{/if}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="{{if $smarty.request.page=='embed'}}settings_embed_player{{else}}settings_player{{/if}}">
	<div class="de_main">
		<div class="de_header"><h1>{{if $smarty.request.page=='embed'}}{{$lang.settings.player_embed_header}}{{else}}{{$lang.settings.player_header}}{{/if}}</h1></div>
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
			{{if $smarty.request.page=='embed'}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/42-how-to-disable-embedding-your-content-on-other-sites-and-redirect-embeds-to-your-site/">How to disable embedding your content on other sites and redirect embeds to your site</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/146-how-to-allow-embedding-your-videos-from-a-whitelisted-set-of-sites/">How to allow embedding your videos from a whitelisted set of sites</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/113-theme-customization-how-to-build-embed-code-for-video-playlist/">How to build embed code for video playlist</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/9-how-to-save-bandwidth-with-kvs-tube-script/">How to save bandwidth from embed codes</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/83-how-to-maximize-your-tube-revenue-with-kvs-advertising-system/">How to maximize your tube revenue with KVS advertising system</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_embed_profile}}</td>
					<td class="de_control">
						<select name="embed_profile_id" class="de_switcher">
							<option value="">{{$lang.settings.player_field_embed_profile_default}}</option>
							{{foreach key="key" item="item" from=$list_embed_profiles|smarty:nodefaults}}
								<option value="{{$key}}" {{if $smarty.get.embed_profile_id==$key}}selected{{/if}}>{{$item.embed_profile_name}}</option>
							{{/foreach}}
							<option value="new" {{if $smarty.get.embed_profile_id=='new'}}selected{{/if}}>{{$lang.settings.player_field_embed_profile_new}}</option>
						</select>
						{{if $smarty.get.embed_profile_id!='' && $smarty.get.embed_profile_id!='new'}}
							<input type="submit" name="delete_profile" value="{{$lang.settings.player_btn_delete_profile}}" data-confirm="{{$lang.settings.player_btn_delete_profile_confirm}}"/>
						{{/if}}
						<span class="de_hint">{{$lang.settings.player_field_embed_profile_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_settings_applied_for}}</td>
					<td class="de_control">
						<span>
							{{if count($list_embed_profiles)>0}}
								{{if $smarty.get.embed_profile_id==''}}
									{{$lang.settings.player_field_embed_profile_default}},
								{{else}}
									<span class="disabled"><s>{{$lang.settings.player_field_embed_profile_default}}</s></span>,
								{{/if}}
								{{foreach key="key" item="item" name="data" from=$list_embed_profiles|smarty:nodefaults}}
									{{if $smarty.get.embed_profile_id==$key}}
										{{$item.embed_profile_name}}{{if !$smarty.foreach.data.last}},{{/if}}
									{{else}}
										<span class="disabled"><s>{{$item.embed_profile_name}}</s></span>{{if !$smarty.foreach.data.last}},{{/if}}
									{{/if}}
								{{/foreach}}
							{{else}}
								{{$lang.common.undefined}}
							{{/if}}
						</span>
					</td>
				</tr>
				{{if $smarty.get.embed_profile_id!=''}}
					<tr>
						<td class="de_label de_required">{{$lang.settings.player_field_embed_profile_name}}</td>
						<td class="de_control">
							<input type="text" name="embed_profile_name" maxlength="100" value="{{$player_data.embed_profile_name}}"/>
							<span class="de_hint">{{$lang.settings.player_field_embed_profile_name_hint}}</span>
						</td>
					</tr>
					<tr>
						<td class="de_label de_required">{{$lang.settings.player_field_embed_profile_domains}}</td>
						<td class="de_control">
							<textarea name="embed_profile_domains" cols="40" rows="3">{{$player_data.embed_profile_domains}}</textarea>
							<span class="de_hint">{{$lang.settings.player_field_embed_profile_domains_hint}}</span>
						</td>
					</tr>
				{{/if}}
			{{elseif $config.installation_type>=2}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/83-how-to-maximize-your-tube-revenue-with-kvs-advertising-system/">How to maximize your tube revenue with KVS advertising system</a></span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_access_level}}</td>
					<td class="de_control">
						<select name="access_level" class="de_switcher">
							<option value="0" {{if $player_data.access_level==0}}selected{{/if}}>{{$lang.settings.player_field_access_level_default}}</option>
							<option value="2" {{if $player_data.access_level==2}}selected{{/if}}>{{$lang.settings.player_field_access_level_member}}</option>
							<option value="3" {{if $player_data.access_level==3}}selected{{/if}}>{{$lang.settings.player_field_access_level_premium}}</option>
						</select>
						<span class="de_hint">{{$lang.settings.player_field_access_level_hint}}</span>
					</td>
				</tr>
				{{if $player_data.access_level!=0}}
					<tr>
						<td class="de_label">{{$lang.settings.player_field_overwrite_settings}}</td>
						<td class="de_control">
							<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="overwrite_settings" value="1" {{if $player_data.no_settings!=1}}checked{{/if}}/><label>{{$lang.settings.player_field_overwrite_settings_yes}}</label></span>
						</td>
					</tr>
				{{/if}}
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_settings_applied_for}}</td>
					<td class="de_control">
						<span>
							{{if in_array(1,$applied)}}
								{{$lang.settings.player_field_access_level_unknown}},
							{{else}}
								<span class="disabled"><s>{{$lang.settings.player_field_access_level_unknown}}</s></span>,
							{{/if}}
							{{if in_array(2,$applied)}}
								{{$lang.settings.player_field_access_level_member}},
							{{else}}
								<span class="disabled"><s>{{$lang.settings.player_field_access_level_member}}</s></span>,
							{{/if}}
							{{if in_array(3,$applied)}}
								{{$lang.settings.player_field_access_level_premium}}
							{{else}}
								<span class="disabled"><s>{{$lang.settings.player_field_access_level_premium}}</s></span>
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_general_settings}}</h2></td>
			</tr>
			{{if $smarty.request.page=='embed'}}
				<tr class="overwrite_settings_on">
					<td class="de_label de_required">{{$lang.settings.player_field_embed_template}}</td>
					<td class="de_control">
						<div class="code_editor" data-syntax="smarty">
							<textarea name="embed_template" rows="10" cols="40">{{$player_data.embed_template}}</textarea>
							<span class="de_hint">{{$lang.settings.player_field_embed_template_hint}}</span>
							<div class="toolbar">
								{{if $smarty.request.embed_profile_id=='' || $smarty.request.embed_profile_id!='new'}}
									<input type="button" value="{{$lang.website_ui.common_action_quick_save}}" data-quicksave-action="quick_save"/>
								{{/if}}
								<div class="separated-group">
									<div class="drop up">
										<i class="icon icon-action-add-code"></i><span>{{$lang.website_ui.common_action_insert_smarty}}</span>
										<ul>
											<li data-insert="{{$smarty.ldelim}}${selection}$variable${selection}{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_variable}}</li>
											<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if}}</li>
											<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}else{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if_else}}</li>
											<li data-insert="{{$smarty.ldelim}}foreach item=&quot;item&quot; from=${selection}$variable${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$item{{$smarty.rdelim}}\n{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_foreach}}</li>
											<li data-insert="{{$smarty.ldelim}}section name=&quot;name&quot; start=0 step=1 loop=${selection}10${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$smarty.section.name.iteration{{$smarty.rdelim}}\n{{$smarty.ldelim}}/section{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_section}}</li>
											<li data-insert="{{$smarty.ldelim}}assign var=&quot;${selection}name${selection}&quot; value=&quot;value&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_assign}}</li>
										</ul>
									</div>
									<div class="push" data-fullscreen-action>
										<i class="icon icon-action-extend"></i>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_error_template}}</td>
					<td class="de_control">
						<div class="code_editor" data-syntax="smarty">
							<textarea name="error_template" rows="10" cols="40">{{$player_data.error_template}}</textarea>
							<span class="de_hint">{{$lang.settings.player_field_error_template_hint}}</span>
							<div class="toolbar">
								{{if $smarty.request.embed_profile_id=='' || $smarty.request.embed_profile_id!='new'}}
									<input type="button" value="{{$lang.website_ui.common_action_quick_save}}" data-quicksave-action="quick_save"/>
								{{/if}}
								<div class="separated-group">
									<div class="drop up">
										<i class="icon icon-action-add-code"></i><span>{{$lang.website_ui.common_action_insert_smarty}}</span>
										<ul>
											<li data-insert="{{$smarty.ldelim}}${selection}$variable${selection}{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_variable}}</li>
											<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if}}</li>
											<li data-insert="{{$smarty.ldelim}}if ${selection}$variable${selection}{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}else{{$smarty.rdelim}}\n\t\n{{$smarty.ldelim}}/if{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_if_else}}</li>
											<li data-insert="{{$smarty.ldelim}}foreach item=&quot;item&quot; from=${selection}$variable${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$item{{$smarty.rdelim}}\n{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_foreach}}</li>
											<li data-insert="{{$smarty.ldelim}}section name=&quot;name&quot; start=0 step=1 loop=${selection}10${selection}{{$smarty.rdelim}}\n\t{{$smarty.ldelim}}$smarty.section.name.iteration{{$smarty.rdelim}}\n{{$smarty.ldelim}}/section{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_section}}</li>
											<li data-insert="{{$smarty.ldelim}}assign var=&quot;${selection}name${selection}&quot; value=&quot;value&quot;{{$smarty.rdelim}}">{{$lang.website_ui.common_action_insert_smarty_assign}}</li>
										</ul>
									</div>
									<div class="push" data-fullscreen-action>
										<i class="icon icon-action-extend"></i>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
				<tr class="overwrite_settings_on">
					<td class="de_label de_required">{{$lang.settings.player_field_embed_cache_time}}</td>
					<td class="de_control">
						<input type="text" name="embed_cache_time" maxlength="10" size="10" value="{{$player_data.embed_cache_time|default:"86400"}}"/>
						<span class="de_hint">{{$lang.settings.player_field_embed_cache_time_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_label de_required">{{$lang.settings.player_field_size}}</td>
				<td class="de_control de_vis_sw_select">
					{{if $smarty.request.page=='embed'}}
						<select name="embed_size_option">
							<option value="0" {{if $player_data.embed_size_option==0}}selected{{/if}}>{{$lang.settings.player_field_size_embed_as_video}}</option>
							<option value="1" {{if $player_data.embed_size_option==1}}selected{{/if}}>{{$lang.settings.player_field_size_embed_as_options}}</option>
						</select>
					{{/if}}
					<span>
						<input type="text" name="width" maxlength="5" size="5" class="embed_size_option_1" value="{{$player_data.width}}"/>
						x
						<input type="text" name="height" maxlength="5" size="5" class="embed_size_option_1" value="{{$player_data.height}}"/>
					</span>
					<select name="height_option" class="embed_size_option_1">
						<option value="0" {{if $player_data.height_option==0}}selected{{/if}}>{{$lang.settings.player_field_size_height_dynamic1}}</option>
						<option value="2" {{if $player_data.height_option==2}}selected{{/if}}>{{$lang.settings.player_field_size_height_dynamic2}}</option>
						<option value="1" {{if $player_data.height_option==1}}selected{{/if}}>{{$lang.settings.player_field_size_height_fixed}}</option>
					</select>
					{{if $smarty.request.page=='embed'}}
						<span class="de_hint">{{$lang.settings.player_field_size_hint_embed}}</span>
					{{else}}
						<span class="de_hint">{{$lang.settings.player_field_size_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.request.page!='embed' && $player_data.access_level==0}}
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_adjust_embed_codes}}</td>
					<td class="de_control">
						<span class="de_lv_pair"><input type="checkbox" name="adjust_embed_codes" value="1" {{if $player_data.adjust_embed_codes==1}}checked{{/if}}/><label>{{$lang.settings.player_field_adjust_embed_codes_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.player_field_adjust_embed_codes_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_skin}}</td>
				<td class="de_control">
					<select name="skin">
						{{assign var="found_skin" value="false"}}
						{{foreach item="item" from=$list_skins|smarty:nodefaults}}
							<option value="{{$item}}" {{if $player_data.skin==$item}}{{assign var="found_skin" value="true"}}selected{{/if}}>{{$item}}</option>
						{{/foreach}}
						<option value="disable" {{if $player_data.skin=='disable'}}{{assign var="found_skin" value="true"}}selected{{/if}}>{{$lang.settings.player_field_skin_disable}}</option>
						{{if $found_skin=='false'}}
							<option value="{{$player_data.skin}}" selected>{{$lang.settings.player_field_skin_missing}}</option>
						{{/if}}
					</select>
					<span class="de_hint">{{$lang.settings.player_field_skin_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_controlbar_mode}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="controlbar">
						<option value="0" {{if $player_data.controlbar==0}}selected{{/if}}>{{$lang.settings.player_field_controlbar_mode_show_always}}</option>
						<option value="1" {{if $player_data.controlbar==1}}selected{{/if}}>{{$lang.settings.player_field_controlbar_mode_autohide}}</option>
						<option value="2" {{if $player_data.controlbar==2}}selected{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_always}}</option>
					</select>
					<select name="controlbar_hide_style" class="controlbar_1">
						<option value="0" {{if $player_data.controlbar_hide_style==0}}selected{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_move}}</option>
						<option value="1" {{if $player_data.controlbar_hide_style==1}}selected{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_fade}}</option>
					</select>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_preload}}</td>
				<td class="de_control">
					<select name="preload_metadata">
						<option value="0" {{if $player_data.preload_metadata==0}}selected{{/if}}>{{$lang.settings.player_field_preload_none}}</option>
						<option value="1" {{if $player_data.preload_metadata==1}}selected{{/if}}>{{$lang.settings.player_field_preload_metadata}}</option>
						<option value="2" {{if $player_data.preload_metadata==2}}selected{{/if}}>{{$lang.settings.player_field_preload_auto}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.player_field_preload_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_volume}}</td>
				<td class="de_control">
					<select name="volume">
						<option value="muted" {{if $player_data.volume=='muted'}}selected{{/if}}>{{$lang.settings.player_field_volume_muted}}</option>
						<option value="0.1" {{if $player_data.volume=='0.1'}}selected{{/if}}>10%</option>
						<option value="0.2" {{if $player_data.volume=='0.2'}}selected{{/if}}>20%</option>
						<option value="0.3" {{if $player_data.volume=='0.3'}}selected{{/if}}>30%</option>
						<option value="0.4" {{if $player_data.volume=='0.4'}}selected{{/if}}>40%</option>
						<option value="0.5" {{if $player_data.volume=='0.5'}}selected{{/if}}>50%</option>
						<option value="0.6" {{if $player_data.volume=='0.6'}}selected{{/if}}>60%</option>
						<option value="0.7" {{if $player_data.volume=='0.7'}}selected{{/if}}>70%</option>
						<option value="0.8" {{if $player_data.volume=='0.8'}}selected{{/if}}>80%</option>
						<option value="0.9" {{if $player_data.volume=='0.9'}}selected{{/if}}>90%</option>
						<option value="1" {{if $player_data.volume=='1' || $player_data.volume==''}}selected{{/if}}>100%</option>
					</select>
					<span class="de_hint">{{$lang.settings.player_field_volume_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_loop}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="loop">
						<option value="0">{{$lang.settings.player_field_loop_disabled}}</option>
						<option value="1" {{if $player_data.loop=='1'}}selected{{/if}}>{{$lang.settings.player_field_loop_all_videos}}</option>
						<option value="2" {{if $player_data.loop=='2'}}selected{{/if}}>{{$lang.settings.player_field_loop_duration}}</option>
					</select>
					<span>
						<input type="text" name="loop_duration" class="loop_2" maxlength="5" size="5" value="{{$player_data.loop_duration}}"/>
						{{$lang.settings.player_field_loop_duration_seconds}}
					</span>
					<span class="de_hint">{{$lang.settings.player_field_loop_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_timeline_screenshots}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								{{assign var="vis_sw_timeline" value=""}}
								<select name="timeline_screenshots_size">
									{{if $player_data.timeline_screenshots_size==''}}
										{{assign var="found_format" value="true"}}
									{{else}}
										{{assign var="found_format" value="false"}}
									{{/if}}
									<option value="">{{$lang.settings.player_field_timeline_screenshots_no}}</option>
									{{foreach item="item" from=$list_formats_timeline_screenshots|smarty:nodefaults}}
										<option value="{{$item.size}}" {{if $player_data.timeline_screenshots_size==$item.size}}{{assign var="found_format" value="true"}}selected{{/if}}>{{$item.title}}</option>
										{{assign var="vis_sw_timeline" value="`$vis_sw_timeline` timeline_screenshots_size_`$item.size`"}}
									{{/foreach}}
									{{if $found_format=='false'}}
										<option value="{{$player_data.timeline_screenshots_size}}" selected>{{$lang.settings.player_field_timeline_screenshots_missing}}</option>
									{{/if}}
								</select>
							</td>
						</tr>
						<tr class="{{$vis_sw_timeline}}">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="timeline_screenshots_cuepoints" value="1" {{if $player_data.timeline_screenshots_cuepoints==1}}checked{{/if}}/><label>{{$lang.settings.player_field_timeline_screenshots_cue}}</label></span>
							</td>
						</tr>
						<tr class="{{$vis_sw_timeline}}">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="timeline_screenshots_preload" value="1" {{if $player_data.timeline_screenshots_preload==1}}checked{{/if}}/><label>{{$lang.settings.player_field_timeline_screenshots_preload}}</label></span>
							</td>
						</tr>
					</table>
					<span class="de_hint">{{$lang.settings.player_field_timeline_screenshots_hint}}</span>
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/148-theme-customization-how-to-show-player-timeline-screenshots-outside-player-and-make-them-clickable/">How to show player timeline screenshots outside player and make them clickable</a></span>
				</td>
			</tr>
			{{if $smarty.request.page=='embed'}}
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_affiliate_param_name}}</td>
					<td class="de_control">
						<input type="text" name="affiliate_param_name" maxlength="100" value="{{$player_data.affiliate_param_name}}"/>
						<span class="de_hint">{{$lang.settings.player_field_affiliate_param_name_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_label enable_adblock_protection_on">{{$lang.settings.player_field_adblock_protection}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_adblock_protection" value="1" {{if $player_data.enable_adblock_protection==1}}checked{{/if}}/><label>{{$lang.settings.player_field_adblock_protection_enabled}}</label></span>
								<span>
									<input type="text" name="adblock_protection_html_after" size="5" class="enable_adblock_protection_on" value="{{$player_data.adblock_protection_html_after|default:"10"}}"/>
									{{$lang.settings.player_field_adblock_protection_enabled2}}
								</span>
								<span class="de_hint">{{$lang.settings.player_field_adblock_protection_hint}}</span>
							</td>
						</tr>
						<tr>
							<td>
								<div class="code_editor" data-syntax="html">
									<textarea name="adblock_protection_html" class="enable_adblock_protection_on" rows="4">{{$player_data.adblock_protection_html}}</textarea>
								</div>
								<span class="de_hint">{{$lang.settings.player_field_adblock_protection_hint2}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_poster}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="disable_preview_resize" value="1" {{if $player_data.disable_preview_resize==1}}checked{{/if}}/><label>{{$lang.settings.player_field_poster_disable_preview_resize}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="use_uploaded_poster" value="1" {{if $player_data.use_uploaded_poster==1}}checked{{/if}}/><label>{{$lang.settings.player_field_poster_use_uploaded_poster}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="use_preview_source" value="1" {{if $player_data.use_preview_source==1}}checked{{/if}}/><label>{{$lang.settings.player_field_poster_use_preview_source}}</label></span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_options}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="show_speed" value="1" {{if $player_data.show_speed==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_speed}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="enable_stream" value="1" {{if $player_data.enable_stream==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_stream}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="enable_autoplay" value="1" {{if $player_data.enable_autoplay==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_autoplay}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_related_videos" value="1" {{if $player_data.enable_related_videos==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_related_videos}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="enable_related_videos_on_pause" value="1" {{if $player_data.enable_related_videos_on_pause==1}}checked{{/if}} class="enable_related_videos_on"/><label>{{$lang.settings.player_field_options_related_videos_pause}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="enable_urls_in_same_window" value="1" {{if $player_data.enable_urls_in_same_window==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_urls_same_window}}</label></span></td>
						</tr>
						<tr>
							<td><span class="de_lv_pair"><input type="checkbox" name="disable_embed_code" value="1" {{if $player_data.disable_embed_code==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_disable_embed_code}}</label></span></td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="error_logging" value="1" {{if $player_data.error_logging==1}}checked{{/if}}/><label>{{$lang.settings.player_field_options_error_logging}}</label></span>
								<span class="de_hint">{{$lang.settings.player_field_options_error_logging_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_vast}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_vast_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_advertising_vast_key}}</td>
				<td class="de_control">
					<span>
						<input type="text" size="32" value="{{$primary_vast_key|default:$lang.common.undefined}}" readonly/>
					</span>
					{{if $primary_vast_key_invalid==1}}
						<span class="highlighted_text">{{$lang.settings.player_field_advertising_vast_key_invalid}}</span>
					{{elseif $primary_vast_key_valid>0}}
						{{if $primary_vast_key_valid<=3}}
							<span class="warning_text">{{$lang.settings.player_field_advertising_vast_key_valid|replace:"%1%":$primary_vast_key_valid}}</span>
						{{else}}
							<span>{{$lang.settings.player_field_advertising_vast_key_valid|replace:"%1%":$primary_vast_key_valid}}</span>
						{{/if}}
					{{/if}}
					<span class="de_hint">{{$lang.settings.player_field_advertising_vast_key_hint}}</span>
				</td>
			</tr>
			{{if count($vast_aliases)>0}}
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_advertising_vast_aliases}}</td>
					<td class="de_control">
						<span>
							{{assign var="display_limit" value=10}}
							{{foreach from=$vast_aliases item="alias" name="aliases"}}
								{{if $smarty.foreach.aliases.iteration<=$display_limit}}{{$alias.domain}}{{if ($smarty.foreach.aliases.iteration<=$display_limit-1 || count($vast_aliases)>$display_limit) && !$smarty.foreach.aliases.last}}, {{/if}}{{/if}}
							{{/foreach}}
							{{if count($vast_aliases)>$display_limit}}
								{{assign var="display_left" value=$vast_aliases|@count}}
								{{assign var="display_left" value=$display_left-$display_limit}}
								<span data-accordeon="aliases_expander" class="after">{{$lang.settings.player_field_advertising_vast_aliases_more|replace:"%1%":$display_left}}</span>
								<div class="aliases_expander hidden">
									{{foreach from=$vast_aliases item="alias" name="aliases"}}
										{{if $smarty.foreach.aliases.iteration>$display_limit}}{{$alias.domain}}{{if !$smarty.foreach.aliases.last}}, {{/if}}{{/if}}
									{{/foreach}}
								</div>
							{{/if}}
						</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_advertising_vast_timeout}}</td>
				<td class="de_control">
					<span>
						<input type="text" name="pre_roll_vast_timeout" size="5" maxlength="10" value="{{$player_data.pre_roll_vast_timeout|default:"10"}}"/>
						{{$lang.settings.player_field_advertising_vast_timeout_s}}
					</span>
					<span class="de_hint">{{$lang.settings.player_field_advertising_vast_timeout_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_branding_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_branding_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_logo}}</td>
				<td class="de_control">
					<select name="logo_source">
						<option value="0" {{if $player_data.logo_source==0}}selected{{/if}}>{{$lang.settings.common_advertising_source_global_image}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index}}" {{if $player_data.logo_source==$smarty.section.data.index}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_logo}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $player_data.logo!=''}}
								<span class="js_param">preview_url={{$player_path}}/{{$player_data.logo}}</span>
							{{/if}}
						</div>
						<input type="text" name="logo" maxlength="100" {{if $player_data.logo!=''}}value="{{$player_data.logo}}"{{/if}}/>
						<input type="hidden" name="logo_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $player_data.logo==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.player_field_logo_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_logo_text}}</td>
				<td class="de_control">
					<select name="logo_text_source">
						<option value="0" {{if $player_data.logo_text_source==0}}selected{{/if}}>{{$lang.settings.common_advertising_source_global_text}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="20" {{if $player_data.logo_text_source==20}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$lang.categorization.content_source_field_title}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index}}" {{if $player_data.logo_text_source==$smarty.section.data.index}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="logo_text" maxlength="255" size="40" value="{{$player_data.logo_text}}"/>
					<span class="de_hint">{{$lang.settings.player_field_logo_text_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_logo_url}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="logo_url_source">
						<option value="1" {{if $player_data.logo_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.logo_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="3" {{if $player_data.logo_url_source==3}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+3}}" {{if $player_data.logo_url_source==$smarty.section.data.index+3}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="logo_url" maxlength="255" size="40" class="logo_url_source_1 logo_url_source_3 logo_url_source_4 logo_url_source_5 logo_url_source_6 logo_url_source_7 logo_url_source_8 logo_url_source_9 logo_url_source_10 logo_url_source_11 logo_url_source_12 logo_url_source_13" value="{{$player_data.logo_url}}"/>
					<span class="de_hint">
						{{if $smarty.request.page=='embed'}}
							{{$lang.settings.player_field_logo_url_embed_hint}}
						{{else}}
							{{$lang.settings.player_field_logo_url_hint}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_logo_position}}</td>
				<td class="de_control">
					<select name="logo_anchor">
						<option value="topleft" {{if $player_data.logo_anchor=='topleft'}}selected{{/if}}>{{$lang.settings.player_field_logo_position_topleft}}</option>
						<option value="topright" {{if $player_data.logo_anchor=='topright'}}selected{{/if}}>{{$lang.settings.player_field_logo_position_topright}}</option>
						<option value="bottomright" {{if $player_data.logo_anchor=='bottomright'}}selected{{/if}}>{{$lang.settings.player_field_logo_position_bottomright}}</option>
						<option value="bottomleft" {{if $player_data.logo_anchor=='bottomleft'}}selected{{/if}}>{{$lang.settings.player_field_logo_position_bottomleft}}</option>
					</select>
					<span>
						{{$lang.settings.player_field_logo_position_offset_x}}:
						<input type="text" name="logo_position_x" maxlength="5" size="5" value="{{$player_data.logo_position_x}}"/>
					</span>
					<span>
						{{$lang.settings.player_field_logo_position_offset_y}}:
						<input type="text" name="logo_position_y" maxlength="5" size="5" value="{{$player_data.logo_position_y}}"/>
					</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_logo_autohide}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="logo_hide" value="1" {{if $player_data.logo_hide==1}}checked{{/if}}/><label>{{$lang.settings.player_field_logo_autohide_enable}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_logo_autohide_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_controlbar_ad_text}}</td>
				<td class="de_control">
					<select name="controlbar_ad_text_source">
						<option value="0" {{if $player_data.controlbar_ad_text_source==0}}selected{{/if}}>{{$lang.settings.common_advertising_source_global_text}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="20" {{if $player_data.controlbar_ad_text_source==20}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$lang.categorization.content_source_field_title}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index}}" {{if $player_data.controlbar_ad_text_source==$smarty.section.data.index}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="controlbar_ad_text" maxlength="255" size="40" value="{{$player_data.controlbar_ad_text}}"/>
					<span class="de_hint">{{$lang.settings.player_field_controlbar_ad_text_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_controlbar_ad_url}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="controlbar_ad_url_source">
						<option value="1" {{if $player_data.controlbar_ad_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.controlbar_ad_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="3" {{if $player_data.controlbar_ad_url_source==3}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+3}}" {{if $player_data.controlbar_ad_url_source==$smarty.section.data.index+3}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="controlbar_ad_url" maxlength="255" size="40" class="controlbar_ad_url_source_1 controlbar_ad_url_source_3 controlbar_ad_url_source_4 controlbar_ad_url_source_5 controlbar_ad_url_source_6 controlbar_ad_url_source_7 controlbar_ad_url_source_8 controlbar_ad_url_source_9 controlbar_ad_url_source_10 controlbar_ad_url_source_11 controlbar_ad_url_source_12 controlbar_ad_url_source_13" value="{{$player_data.controlbar_ad_url}}"/>
					<span class="de_hint">
						{{if $smarty.request.page=='embed'}}
							{{$lang.settings.player_field_controlbar_ad_url_embed_hint}}
						{{else}}
							{{$lang.settings.player_field_controlbar_ad_url_hint}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_formats_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_formats_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label format_redirect_url_source_1 format_redirect_url_source_3 format_redirect_url_source_4 format_redirect_url_source_5 format_redirect_url_source_6 format_redirect_url_source_7 format_redirect_url_source_8 format_redirect_url_source_9 format_redirect_url_source_10 format_redirect_url_source_11 format_redirect_url_source_12 format_redirect_url_source_13">{{$lang.settings.player_field_format_redirect}}<span class="format_redirect_url_source_3 format_redirect_url_source_4 format_redirect_url_source_5 format_redirect_url_source_6 format_redirect_url_source_7 format_redirect_url_source_8 format_redirect_url_source_9 format_redirect_url_source_10 format_redirect_url_source_11 format_redirect_url_source_12 format_redirect_url_source_13"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="format_redirect_url_source">
						<option value="1" {{if $player_data.format_redirect_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.format_redirect_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="3" {{if $player_data.format_redirect_url_source==3}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+3}}" {{if $player_data.format_redirect_url_source==$smarty.section.data.index+3}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="format_redirect_url" maxlength="255" size="40" class="format_redirect_url_source_1 format_redirect_url_source_3 format_redirect_url_source_4 format_redirect_url_source_5 format_redirect_url_source_6 format_redirect_url_source_7 format_redirect_url_source_8 format_redirect_url_source_9 format_redirect_url_source_10 format_redirect_url_source_11 format_redirect_url_source_12 format_redirect_url_source_13" value="{{$player_data.format_redirect_url}}"/>
					<span class="de_hint">
						{{if $smarty.request.page=='embed'}}
							{{$lang.settings.player_field_format_redirect_embed_hint}}
						{{else}}
							{{$lang.settings.player_field_format_redirect_hint}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_restoring_selected_slot}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="disable_selected_slot_restoring" value="1" {{if $player_data.disable_selected_slot_restoring==1}}checked{{/if}}/><label>{{$lang.settings.player_field_restoring_selected_slot_no}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_restoring_selected_slot_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_global_duration}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="show_global_duration" value="1" {{if $player_data.show_global_duration==1}}checked{{/if}}/><label>{{$lang.settings.player_field_global_duration_enable}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_global_duration_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col class="eg_column_small"/>
							<col class="eg_column_medium"/>
							<col class="eg_column_medium"/>
							<col class="eg_column_medium"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.settings.player_formats_col_order}}</td>
							<td>{{$lang.settings.player_formats_col_format}}</td>
							<td>{{$lang.settings.player_formats_col_player_title}}</td>
							<td>{{$lang.settings.player_formats_col_default}}</td>
							<td>{{$lang.settings.player_formats_col_options}}</td>
						</tr>
						{{foreach from=$formats_groups item="group"}}
							{{assign var="group_id" value=$group.format_video_group_id}}
							{{if count($formats[$group_id])>0}}
								<tr class="eg_header">
									<td colspan="5">{{$group.title}}</td>
								</tr>
								{{assign var="slot_id" value=1}}
								{{assign var="global_vis_sw" value=""}}
								{{section name="formats" start=0 step=1 loop=7}}
									<tr class="eg_data {{$global_vis_sw}}">
										<td>{{$slot_id}}</td>
										<td class="de_vis_sw_select">
											{{assign var="option_id" value="group`$group_id`_slot`$slot_id`"}}
											<select name="{{$option_id}}">
												{{if $selected_formats[$option_id]=='' || $selected_formats[$option_id]=='redirect'}}
													{{assign var="found_format" value="true"}}
												{{else}}
													{{assign var="found_format" value="false"}}
												{{/if}}
												<option value="">{{$lang.settings.player_formats_col_format_slot|replace:"%1%":$slot_id}}</option>
												{{assign var="vis_sw" value=""}}
												{{foreach item="item" from=$formats[$group_id]|smarty:nodefaults}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_`$item.postfix`"}}
													<option value="{{$item.postfix}}" {{if $selected_formats[$option_id]==$item.postfix}}{{assign var="found_format" value="true"}}selected{{/if}}>{{$item.title}}</option>
												{{/foreach}}
												{{if $slot_id>=2}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_redirect"}}
													<option value="redirect" {{if $selected_formats[$option_id]=='redirect'}}selected{{/if}}>{{$lang.settings.player_formats_col_format_redirect}}</option>
												{{/if}}
												{{if $found_format=='false'}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_`$selected_formats[$option_id]`"}}
													<option value="{{$selected_formats[$option_id]}}" selected>{{$lang.settings.player_formats_col_format_missing}}</option>
												{{/if}}
												{{assign var="global_vis_sw" value="`$global_vis_sw` `$vis_sw`"}}
											</select>
										</td>
										<td>
											{{assign var="option_id" value="group`$group_id`_slot_title`$slot_id`"}}
											<input type="text" name="{{$option_id}}" value="{{$selected_formats[$option_id]}}" class="{{$vis_sw}}"/>
										</td>
										<td>
											{{assign var="vis_sw_default" value=""}}
											{{foreach item="item" from=$formats[$group_id]|smarty:nodefaults}}
												{{assign var="vis_sw_default" value="`$vis_sw_default` group`$group_id`_slot`$slot_id`_`$item.postfix`"}}
											{{/foreach}}
											{{assign var="option_id" value="group`$group_id`_default"}}
											<input type="radio" class="{{$vis_sw_default}}" name="{{$option_id}}" value="{{$slot_id}}" {{if $selected_formats[$option_id]==$slot_id}}checked{{/if}}/>
										</td>
										<td>
											{{foreach item="item" from=$formats[$group_id]|smarty:nodefaults}}
												<div class="group{{$group_id}}_slot{{$slot_id}}_{{$item.postfix}}">
													{{if $item.access_level_id==0}}
														{{$lang.settings.player_formats_col_options_video}}:
														{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
															{{if $item==1}}
																{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
															{{elseif $item==2}}
																{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
															{{elseif $item==3}}
																{{$lang.settings.player_formats_col_options_user_premium}}
															{{/if}}
														{{/foreach}}
													{{elseif $item.access_level_id==1}}
														{{if in_array(2,$applied) || in_array(3,$applied)}}
															{{$lang.settings.player_formats_col_options_video}}:
															{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
																{{if $item==2}}
																	{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
																{{elseif $item==3}}
																	{{$lang.settings.player_formats_col_options_user_premium}}
																{{/if}}
															{{/foreach}}
															&nbsp;&nbsp;
														{{/if}}
														{{if in_array(1,$applied)}}
															{{$lang.settings.player_formats_col_options_redirect}}:
															{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
																{{if $item==1}}
																	{{$lang.settings.player_formats_col_options_user_unknown}}
																{{/if}}
															{{/foreach}}
														{{/if}}
													{{elseif $item.access_level_id==2}}
														{{if in_array(3,$applied)}}
															{{$lang.settings.player_formats_col_options_video}}:
															{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
																{{if $item==3}}
																	{{$lang.settings.player_formats_col_options_user_premium}}
																{{/if}}
															{{/foreach}}
															&nbsp;&nbsp;
														{{/if}}
														{{if in_array(1,$applied) || in_array(2,$applied)}}
															{{$lang.settings.player_formats_col_options_redirect}}:
															{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
																{{if $item==1}}
																	{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
																{{elseif $item==2}}
																	{{$lang.settings.player_formats_col_options_user_active}}
																{{/if}}
															{{/foreach}}
														{{/if}}
													{{/if}}
												</div>
											{{/foreach}}
											{{if $slot_id>=2}}
												<div class="group{{$group_id}}_slot{{$slot_id}}_redirect">
													{{$lang.settings.player_formats_col_options_redirect}}:
													{{foreach name="data" item="item" from=$applied|smarty:nodefaults}}
														{{if $item==1}}
															{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
														{{elseif $item==2}}
															{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
														{{elseif $item==3}}
															{{$lang.settings.player_formats_col_options_user_premium}}
														{{/if}}
													{{/foreach}}
												</div>
											{{/if}}
										</td>
									</tr>
									{{assign var="slot_id" value=$slot_id+1}}
								{{/section}}
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_click_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_click_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_video_click_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_video_click" value="1" {{if $player_data.enable_video_click==1}}checked{{/if}}/><label>{{$lang.settings.player_field_video_click_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_video_click_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_video_click_on">
				<td class="de_label de_required de_dependent video_click_url_source_1">{{$lang.settings.player_field_video_click_url}}<span class="video_click_url_source_3 video_click_url_source_4 video_click_url_source_5 video_click_url_source_6 video_click_url_source_7 video_click_url_source_8 video_click_url_source_9 video_click_url_source_10 video_click_url_source_11 video_click_url_source_12 video_click_url_source_13"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="video_click_url_source">
						<option value="1" {{if $player_data.video_click_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.video_click_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="3" {{if $player_data.video_click_url_source==3}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+3}}" {{if $player_data.video_click_url_source==$smarty.section.data.index+3}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
						<optgroup label="{{$lang.settings.common_advertising_source_profile_grouping}}">
							{{assign var="found_profile" value="false"}}
							{{foreach from=$vast_profiles item="vast_profile"}}
								<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.video_click_url_source=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_profile|replace:"%1%":$vast_profile.title}}</option>
							{{/foreach}}
							{{if $found_profile=='false' && $player_data.video_click_url_source|strpos:"vast_profile_"!==false}}
								<option value="{{$player_data.video_click_url_source}}" selected>{{$lang.settings.common_advertising_source_profile_missing}}</option>
							{{/if}}
						</optgroup>
					</select>
					<input type="text" name="video_click_url" maxlength="255" size="40" class="video_click_url_source_1 video_click_url_source_3 video_click_url_source_4 video_click_url_source_5 video_click_url_source_6 video_click_url_source_7 video_click_url_source_8 video_click_url_source_9 video_click_url_source_10 video_click_url_source_11 video_click_url_source_12 video_click_url_source_13" value="{{$player_data.video_click_url}}"/>
					<span class="de_hint">
						{{if $smarty.request.page=='embed'}}
							{{$lang.settings.player_field_video_click_url_embed_hint}}
						{{else}}
							{{$lang.settings.player_field_video_click_url_hint}}
						{{/if}}
					</span>
				</td>
			</tr>
			{{if $smarty.request.page!='embed'}}
				<tr class="overwrite_settings_on">
					<td class="de_label">{{$lang.settings.player_field_popunder_enable}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_popunder" value="1" {{if $player_data.enable_popunder==1}}checked{{/if}}/><label>{{$lang.settings.player_field_popunder_enable_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.player_field_popunder_enable_hint}}</span>
					</td>
				</tr>
				<tr class="overwrite_settings_on enable_popunder_on">
					<td class="de_label de_required de_dependent popunder_url_source_1">{{$lang.settings.player_field_popunder_url}}<span class="popunder_url_source_3 popunder_url_source_4 popunder_url_source_5 popunder_url_source_6 popunder_url_source_7 popunder_url_source_8 popunder_url_source_9 popunder_url_source_10 popunder_url_source_11 popunder_url_source_12 popunder_url_source_13"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
					<td class="de_control de_vis_sw_select">
						<select name="popunder_url_source">
							<option value="1" {{if $player_data.popunder_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
							<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
								<option value="3" {{if $player_data.popunder_url_source==3}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
								{{section name="data" start="1" loop="11"}}
									{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
									<option value="{{$smarty.section.data.index+3}}" {{if $player_data.popunder_url_source==$smarty.section.data.index+3}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
								{{/section}}
							</optgroup>
							<optgroup label="{{$lang.settings.common_advertising_source_profile_grouping}}">
								{{assign var="found_profile" value="false"}}
								{{foreach from=$vast_profiles item="vast_profile"}}
									<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.popunder_url_source=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_profile|replace:"%1%":$vast_profile.title}}</option>
								{{/foreach}}
								{{if $found_profile=='false' && $player_data.popunder_url_source|strpos:"vast_profile_"!==false}}
									<option value="{{$player_data.popunder_url_source}}" selected>{{$lang.settings.common_advertising_source_profile_missing}}</option>
								{{/if}}
							</optgroup>
						</select>
						<input type="text" name="popunder_url" maxlength="255" size="40" class="popunder_url_source_1 popunder_url_source_3 popunder_url_source_4 popunder_url_source_5 popunder_url_source_6 popunder_url_source_7 popunder_url_source_8 popunder_url_source_9 popunder_url_source_10 popunder_url_source_11 popunder_url_source_12 popunder_url_source_13" value="{{$player_data.popunder_url}}"/>
						<span class="de_hint">{{$lang.settings.player_field_popunder_url_hint}}</span>
					</td>
				</tr>
				<tr class="overwrite_settings_on enable_popunder_on">
					<td class="de_label de_dependent">{{$lang.settings.player_field_popunder_duration}}</td>
					<td class="de_control">
						<span>
							<input type="text" name="popunder_duration" maxlength="10" size="5" value="{{$player_data.popunder_duration}}"/>
							{{$lang.settings.player_field_popunder_duration_minutes}}
						</span>
						<span class="de_hint">{{$lang.settings.player_field_popunder_duration_hint}}</span>
					</td>
				</tr>
				<tr class="overwrite_settings_on enable_popunder_on">
					<td class="de_label de_dependent popunder_start_option_1">{{$lang.settings.player_field_popunder_start}}</td>
					<td class="de_control de_vis_sw_select">
						<select name="popunder_start_option">
							<option value="0">{{$lang.settings.player_field_popunder_start_immediately}}</option>
							<option value="1" {{if $player_data.popunder_start_after>0}}selected{{/if}}>{{$lang.settings.player_field_popunder_start_nth_video}}</option>
						</select>
						<span class="popunder_start_option_1">
							<input type="text" name="popunder_start_after" maxlength="5" size="5" value="{{$player_data.popunder_start_after|default:''}}"/>
						</span>
						<span class="de_hint">{{$lang.settings.player_field_popunder_start_hint}}</span>
					</td>
				</tr>
				<tr class="overwrite_settings_on enable_popunder_on">
					<td class="de_label de_dependent">{{$lang.settings.player_field_popunder_autoplay_only}}</td>
					<td class="de_control">
						<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="popunder_autoplay_only" value="1" {{if $player_data.popunder_autoplay_only==1}}checked{{/if}}/><label>{{$lang.settings.player_field_popunder_autoplay_only_enabled}}</label></span>
						<span class="de_hint">{{$lang.settings.player_field_popunder_autoplay_only_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_start_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_start_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_start_html_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_start_html" value="1" {{if $player_data.enable_start_html==1}}checked{{/if}}/><label>{{$lang.settings.player_field_start_html_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_start_html_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_start_html_on">
				<td class="de_label de_dependent start_html_source_1">{{$lang.settings.player_field_start_html_code}}<span class="start_html_source_2 start_html_source_3 start_html_source_4 start_html_source_5 start_html_source_6 start_html_source_7 start_html_source_8 start_html_source_9 start_html_source_10 start_html_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="start_html_source">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.start_html_source==1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.start_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.start_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.start_html_source!=''}}
											<option value="{{$player_data.start_html_source}}" selected>{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								<span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
							</td>
						</tr>
						<tr class="start_html_source_1 start_html_source_2 start_html_source_3 start_html_source_4 start_html_source_5 start_html_source_6 start_html_source_7 start_html_source_8 start_html_source_9 start_html_source_10 start_html_source_11">
							<td>
								<div class="code_editor" data-syntax="html">
									<textarea name="start_html_code" rows="4">{{$player_data.start_html_code}}</textarea>
								</div>
								<span class="de_hint">{{$lang.settings.player_field_start_html_code_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_start_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}</td>
				<td class="de_control">
					<input type="text" name="start_html_bg" maxlength="20" value="{{$player_data.start_html_bg|default:"#000000"}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_start_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="start_html_adaptive" value="1" {{if $player_data.start_html_adaptive==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></span>
					<span>
						<input type="text" name="start_html_adaptive_width" maxlength="3" size="3" class="start_html_adaptive_on" value="{{$player_data.start_html_adaptive_width}}"/>
						%
						x
						<input type="text" name="start_html_adaptive_height" maxlength="3" size="3" class="start_html_adaptive_on" value="{{$player_data.start_html_adaptive_height}}"/>
						%
					</span>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_pre_roll_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_pre_roll_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pre_roll_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pre_roll" value="1" {{if $player_data.enable_pre_roll==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pre_roll_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_on">
				<td class="de_label de_dependent pre_roll_file_source_1">{{$lang.settings.player_field_pre_roll_file}}<span class="pre_roll_file_source_2 pre_roll_file_source_3 pre_roll_file_source_4 pre_roll_file_source_5 pre_roll_file_source_6 pre_roll_file_source_7 pre_roll_file_source_8 pre_roll_file_source_9 pre_roll_file_source_10 pre_roll_file_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="pre_roll_file_source">
						<option value="1" {{if $player_data.pre_roll_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pre_roll_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_pre_roll_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}},mp4</span>
							{{if $player_data.pre_roll_file!=''}}
								{{if in_array(end(explode(".",$player_data.pre_roll_file)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$player_path}}/{{$player_data.pre_roll_file}}</span>
								{{else}}
									<span class="js_param">download_url={{$player_path}}/{{$player_data.pre_roll_file}}</span>
								{{/if}}
							{{/if}}
						</div>
						<input type="text" name="pre_roll_file" maxlength="100" {{if $player_data.pre_roll_file!=''}}value="{{$player_data.pre_roll_file}}"{{/if}}/>
						<input type="hidden" name="pre_roll_file_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $player_data.pre_roll_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_file_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_on">
				<td class="de_label de_dependent de_required">{{$lang.settings.player_field_pre_roll_url}}<span class="pre_roll_url_source_2 pre_roll_url_source_3 pre_roll_url_source_4 pre_roll_url_source_5 pre_roll_url_source_6 pre_roll_url_source_7 pre_roll_url_source_8 pre_roll_url_source_9 pre_roll_url_source_10 pre_roll_url_source_11 pre_roll_url_source_12"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="pre_roll_url_source">
						<option value="1" {{if $player_data.pre_roll_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="2" {{if $player_data.pre_roll_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+2}}" {{if $player_data.pre_roll_url_source==$smarty.section.data.index+2}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="pre_roll_url" maxlength="255" size="40" value="{{$player_data.pre_roll_url}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pre_roll_html_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pre_roll_html" value="1" {{if $player_data.enable_pre_roll_html==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pre_roll_html_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_html_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_html_on">
				<td class="de_label de_dependent pre_roll_html_source_1">{{$lang.settings.player_field_pre_roll_html_code}}<spane class="pre_roll_html_source_2 pre_roll_html_source_3 pre_roll_html_source_4 pre_roll_html_source_5 pre_roll_html_source_6 pre_roll_html_source_7 pre_roll_html_source_8 pre_roll_html_source_9 pre_roll_html_source_10 pre_roll_html_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</spane></td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="pre_roll_html_source">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.pre_roll_html_source==1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pre_roll_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.pre_roll_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.pre_roll_html_source!=''}}
											<option value="{{$player_data.pre_roll_html_source}}" selected>{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								<span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
							</td>
						</tr>
						<tr class="pre_roll_html_source_1 pre_roll_html_source_2 pre_roll_html_source_3 pre_roll_html_source_4 pre_roll_html_source_5 pre_roll_html_source_6 pre_roll_html_source_7 pre_roll_html_source_8 pre_roll_html_source_9 pre_roll_html_source_10 pre_roll_html_source_11">
							<td>
								<div class="code_editor" data-syntax="html">
									<textarea name="pre_roll_html_code" rows="4">{{$player_data.pre_roll_html_code}}</textarea>
								</div>
								<span class="de_hint">{{$lang.settings.player_field_pre_roll_html_code_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}</td>
				<td class="de_control">
					<input type="text" name="pre_roll_html_bg" maxlength="20" value="{{$player_data.pre_roll_html_bg|default:"#000000"}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="pre_roll_html_adaptive" value="1" {{if $player_data.pre_roll_html_adaptive==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></span>
					<span>
						<input type="text" name="pre_roll_html_adaptive_width" maxlength="3" size="3" class="pre_roll_html_adaptive_on" value="{{$player_data.pre_roll_html_adaptive_width}}"/>
						%
						x
						<input type="text" name="pre_roll_html_adaptive_height" maxlength="3" size="3" class="pre_roll_html_adaptive_on" value="{{$player_data.pre_roll_html_adaptive_height}}"/>
						%
					</span>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pre_roll_vast_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pre_roll_vast" value="1" {{if $player_data.enable_pre_roll_vast==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pre_roll_vast_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_vast_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_vast_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_provider}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="pre_roll_vast_provider">
						<option value="c" {{if $player_data.pre_roll_vast_provider=='c'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_c}}</option>
						<option value="1" {{if $player_data.pre_roll_vast_provider=='1'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_1}}</option>
						<option value="2" {{if $player_data.pre_roll_vast_provider=='2'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_2}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_profile_grouping}}">
							{{assign var="found_profile" value="false"}}
							{{foreach from=$vast_profiles item="vast_profile"}}
								<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.pre_roll_vast_provider=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_profile|replace:"%1%":$vast_profile.title}}</option>
							{{/foreach}}
							{{if $found_profile=='false' && $player_data.pre_roll_vast_provider|strpos:"vast_profile_"!==false}}
								<option value="{{$player_data.pre_roll_vast_provider}}" selected>{{$lang.settings.common_advertising_source_profile_missing}}</option>
							{{/if}}
						</optgroup>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="cs_custom_{{$smarty.section.data.index}}" {{if $player_data.pre_roll_vast_provider=="cs_custom_`$smarty.section.data.index`"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_1_url}}" class="pre_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_2_url}}" class="pre_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_url2}}</a>
					<span class="de_hint pre_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_hint}}</span>
					<span class="de_hint pre_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_hint}}</span>
					<span class="de_hint pre_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_provider_c_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_vast_on pre_roll_vast_provider_1 pre_roll_vast_provider_2 pre_roll_vast_provider_c pre_roll_vast_provider_cs_custom_1 pre_roll_vast_provider_cs_custom_2 pre_roll_vast_provider_cs_custom_3 pre_roll_vast_provider_cs_custom_4 pre_roll_vast_provider_cs_custom_5 pre_roll_vast_provider_cs_custom_6 pre_roll_vast_provider_cs_custom_7 pre_roll_vast_provider_cs_custom_8 pre_roll_vast_provider_cs_custom_9 pre_roll_vast_provider_cs_custom_10">
				<td class="de_label de_dependent pre_roll_vast_provider_1 pre_roll_vast_provider_2 pre_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_url}}<span class="pre_roll_vast_provider_cs_custom_1 pre_roll_vast_provider_cs_custom_2 pre_roll_vast_provider_cs_custom_3 pre_roll_vast_provider_cs_custom_4 pre_roll_vast_provider_cs_custom_5 pre_roll_vast_provider_cs_custom_6 pre_roll_vast_provider_cs_custom_7 pre_roll_vast_provider_cs_custom_8 pre_roll_vast_provider_cs_custom_9 pre_roll_vast_provider_cs_custom_10"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control">
					<input type="text" name="pre_roll_vast_url" value="{{$player_data.pre_roll_vast_url}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_vast_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_vast_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_alt_url}}</td>
				<td class="de_control">
					<textarea name="pre_roll_vast_alt_url" rows="3" cols="40">{{$player_data.pre_roll_vast_alt_url}}</textarea>
					<span class="de_hint">{{$lang.settings.common_field_advertising_vast_alt_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_vast_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_logo}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="pre_roll_vast_logo" value="1" {{if $player_data.pre_roll_vast_logo==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_vast_logo_enabled}}</label></span>
								<span class="de_hint">{{$lang.settings.common_field_advertising_vast_logo_hint}}</span>
							</td>
						</tr>
						<tr class="pre_roll_vast_logo_on">
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="pre_roll_vast_logo_click" value="1" {{if $player_data.pre_roll_vast_logo_click==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_vast_logo_enabled2}}</label></span>
								<span class="de_hint">{{$lang.settings.common_field_advertising_vast_logo_hint2}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label enable_pre_roll_on enable_pre_roll_html_on enable_pre_roll_on_or_enable_pre_roll_html_on">{{$lang.settings.player_field_pre_roll_duration}}</td>
				<td class="de_control">
					<input type="text" name="pre_roll_duration" maxlength="5" size="5" value="{{$player_data.pre_roll_duration}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_duration_hint}}</span>
					<span class="de_hint enable_pre_roll_vast_on">{{$lang.settings.player_field_pre_roll_duration_hint2}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pre_roll_duration_text}}</td>
				<td class="de_control">
					<input type="text" name="pre_roll_duration_text" maxlength="100" value="{{$player_data.pre_roll_duration_text}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_duration_text_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label pre_roll_start_option_1">{{$lang.settings.player_field_pre_roll_start}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="pre_roll_start_option">
						<option value="0">{{$lang.settings.player_field_pre_roll_start_immediately}}</option>
						<option value="1" {{if $player_data.pre_roll_start_after>0}}selected{{/if}}>{{$lang.settings.player_field_pre_roll_start_nth_video}}</option>
					</select>
					<span class="pre_roll_start_option_1">
						<input type="text" name="pre_roll_start_after" maxlength="5" size="5" value="{{$player_data.pre_roll_start_after|default:''}}"/>
					</span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_start_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label pre_roll_replay_option_1">{{$lang.settings.player_field_pre_roll_frequency}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="pre_roll_replay_option">
						<option value="0">{{$lang.settings.player_field_pre_roll_frequency_each}}</option>
						<option value="1" {{if $player_data.pre_roll_replay_after>0}}selected{{/if}}>{{$lang.settings.player_field_pre_roll_frequency_interval}}</option>
					</select>
					<span class="pre_roll_replay_option_1">
						<input type="text" name="pre_roll_replay_after" maxlength="5" size="5" value="{{$player_data.pre_roll_replay_after|default:''}}"/>
						<select name="pre_roll_replay_after_type">
							<option value="0">{{$lang.settings.player_field_pre_roll_frequency_videos}}</option>
							<option value="1" {{if $player_data.pre_roll_replay_after_type>0}}selected{{/if}}>{{$lang.settings.player_field_pre_roll_frequency_minutes}}</option>
						</select>
					</span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_frequency_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pre_roll_skip}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pre_roll_skip" value="1" {{if $player_data.enable_pre_roll_skip==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pre_roll_skip_after}}</label></span>
					<span>
						<input type="text" name="pre_roll_skip_duration" maxlength="5" size="5" class="enable_pre_roll_skip_on" value="{{$player_data.pre_roll_skip_duration}}"/>
						{{$lang.settings.player_field_pre_roll_skip_after_seconds}}
					</span>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_hint}}</span>
					<span class="de_hint enable_pre_roll_vast_on">{{$lang.settings.player_field_pre_roll_skip_hint2}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_skip_on">
				<td class="de_label de_dependent">{{$lang.settings.player_field_pre_roll_skip_text1}}</td>
				<td class="de_control">
					<input type="text" name="pre_roll_skip_text1" maxlength="100" value="{{$player_data.pre_roll_skip_text1}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_text1_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pre_roll_skip_on">
				<td class="de_label de_required de_dependent">{{$lang.settings.player_field_pre_roll_skip_text2}}</td>
				<td class="de_control">
					<input type="text" name="pre_roll_skip_text2" maxlength="100" value="{{$player_data.pre_roll_skip_text2}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_text2_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_post_roll_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_post_roll_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_mode}}</td>
				<td class="de_control">
					<select name="post_roll_mode">
						<option value="0" {{if $player_data.post_roll_mode=='0'}}selected{{/if}}>{{$lang.settings.player_field_post_roll_mode_finish}}</option>
						<option value="1" {{if $player_data.post_roll_mode=='1'}}selected{{/if}}>{{$lang.settings.player_field_post_roll_mode_pause}}</option>
					</select>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_mode_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_post_roll" value="1" {{if $player_data.enable_post_roll==1}}checked{{/if}}/><label>{{$lang.settings.player_field_post_roll_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_on">
				<td class="de_label de_dependent post_roll_file_source_1">{{$lang.settings.player_field_post_roll_file}}<span class="post_roll_file_source_2 post_roll_file_source_3 post_roll_file_source_4 post_roll_file_source_5 post_roll_file_source_6 post_roll_file_source_7 post_roll_file_source_8 post_roll_file_source_9 post_roll_file_source_10 post_roll_file_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="post_roll_file_source">
						<option value="1" {{if $player_data.post_roll_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+1}}" {{if $player_data.post_roll_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_post_roll_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}},mp4</span>
							{{if $player_data.post_roll_file!=''}}
								{{if in_array(end(explode(".",$player_data.post_roll_file)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$player_path}}/{{$player_data.post_roll_file}}</span>
								{{else}}
									<span class="js_param">download_url={{$player_path}}/{{$player_data.post_roll_file}}</span>
								{{/if}}
							{{/if}}
						</div>
						<input type="text" name="post_roll_file" maxlength="100" {{if $player_data.post_roll_file!=''}}value="{{$player_data.post_roll_file}}"{{/if}}/>
						<input type="hidden" name="post_roll_file_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $player_data.post_roll_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_file_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_on">
				<td class="de_label de_dependent post_roll_url_source_1 post_roll_url_source_2 post_roll_url_source_3 post_roll_url_source_4 post_roll_url_source_5 post_roll_url_source_6 post_roll_url_source_7 post_roll_url_source_8 post_roll_url_source_9 post_roll_url_source_10 post_roll_url_source_11 post_roll_url_source_12">{{$lang.settings.player_field_post_roll_url}}<span class="post_roll_url_source_2 post_roll_url_source_3 post_roll_url_source_4 post_roll_url_source_5 post_roll_url_source_6 post_roll_url_source_7 post_roll_url_source_8 post_roll_url_source_9 post_roll_url_source_10 post_roll_url_source_11 post_roll_url_source_12"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="post_roll_url_source">
						<option value="1" {{if $player_data.post_roll_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="2" {{if $player_data.post_roll_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+2}}" {{if $player_data.post_roll_url_source==$smarty.section.data.index+2}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="post_roll_url" maxlength="255" size="40" value="{{$player_data.post_roll_url}}"/>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_html_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_post_roll_html" value="1" {{if $player_data.enable_post_roll_html==1}}checked{{/if}}/><label>{{$lang.settings.player_field_post_roll_html_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_html_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_html_on">
				<td class="de_label de_dependent post_roll_html_source_1">{{$lang.settings.player_field_post_roll_html_code}}<span class="post_roll_html_source_2 post_roll_html_source_3 post_roll_html_source_4 post_roll_html_source_5 post_roll_html_source_6 post_roll_html_source_7 post_roll_html_source_8 post_roll_html_source_9 post_roll_html_source_10 post_roll_html_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="post_roll_html_source">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.post_roll_html_source==1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.post_roll_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.post_roll_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.post_roll_html_source!=''}}
											<option value="{{$player_data.post_roll_html_source}}" selected>{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								<span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
							</td>
						</tr>
						<tr class="post_roll_html_source_1 post_roll_html_source_2 post_roll_html_source_3 post_roll_html_source_4 post_roll_html_source_5 post_roll_html_source_6 post_roll_html_source_7 post_roll_html_source_8 post_roll_html_source_9 post_roll_html_source_10 post_roll_html_source_11">
							<td>
								<div class="code_editor" data-syntax="html">
									<textarea name="post_roll_html_code" rows="4">{{$player_data.post_roll_html_code}}</textarea>
								</div>
								<span class="de_hint">{{$lang.settings.player_field_post_roll_html_code_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}</td>
				<td class="de_control">
					<input type="text" name="post_roll_html_bg" maxlength="20" value="{{$player_data.post_roll_html_bg|default:"#000000"}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="post_roll_html_adaptive" value="1" {{if $player_data.post_roll_html_adaptive==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></span>
					<span>
						<input type="text" name="post_roll_html_adaptive_width" maxlength="3" size="3" class="post_roll_html_adaptive_on" value="{{$player_data.post_roll_html_adaptive_width}}"/>
						%
						x
						<input type="text" name="post_roll_html_adaptive_height" maxlength="3" size="3" class="post_roll_html_adaptive_on" value="{{$player_data.post_roll_html_adaptive_height}}"/>
						%
					</span>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_vast_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_post_roll_vast" value="1" {{if $player_data.enable_post_roll_vast==1}}checked{{/if}}/><label>{{$lang.settings.player_field_post_roll_vast_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_vast_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_vast_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_provider}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="post_roll_vast_provider">
						<option value="c" {{if $player_data.post_roll_vast_provider=='c'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_c}}</option>
						<option value="1" {{if $player_data.post_roll_vast_provider=='1'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_1}}</option>
						<option value="2" {{if $player_data.post_roll_vast_provider=='2'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_2}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_profile_grouping}}">
							{{assign var="found_profile" value="false"}}
							{{foreach from=$vast_profiles item="vast_profile"}}
								<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.post_roll_vast_provider=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_profile|replace:"%1%":$vast_profile.title}}</option>
							{{/foreach}}
							{{if $found_profile=='false' && $player_data.post_roll_vast_provider|strpos:"vast_profile_"!==false}}
								<option value="{{$player_data.post_roll_vast_provider}}" selected>{{$lang.settings.common_advertising_source_profile_missing}}</option>
							{{/if}}
						</optgroup>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="cs_custom_{{$smarty.section.data.index}}" {{if $player_data.post_roll_vast_provider=="cs_custom_`$smarty.section.data.index`"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_1_url}}" class="post_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_2_url}}" class="post_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_url2}}</a>
					<span class="de_hint post_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_hint}}</span>
					<span class="de_hint post_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_hint}}</span>
					<span class="de_hint post_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_provider_c_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_vast_on post_roll_vast_provider_1 post_roll_vast_provider_2 post_roll_vast_provider_c post_roll_vast_provider_cs_custom_1 post_roll_vast_provider_cs_custom_2 post_roll_vast_provider_cs_custom_3 post_roll_vast_provider_cs_custom_4 post_roll_vast_provider_cs_custom_5 post_roll_vast_provider_cs_custom_6 post_roll_vast_provider_cs_custom_7 post_roll_vast_provider_cs_custom_8 post_roll_vast_provider_cs_custom_9 post_roll_vast_provider_cs_custom_10">
				<td class="de_label de_dependent post_roll_vast_provider_1 post_roll_vast_provider_2 post_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_url}}<span class="post_roll_vast_provider_cs_custom_1 post_roll_vast_provider_cs_custom_2 post_roll_vast_provider_cs_custom_3 post_roll_vast_provider_cs_custom_4 post_roll_vast_provider_cs_custom_5 post_roll_vast_provider_cs_custom_6 post_roll_vast_provider_cs_custom_7 post_roll_vast_provider_cs_custom_8 post_roll_vast_provider_cs_custom_9 post_roll_vast_provider_cs_custom_10"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control">
					<input type="text" name="post_roll_vast_url" value="{{$player_data.post_roll_vast_url}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_vast_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_vast_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_alt_url}}</td>
				<td class="de_control">
					<textarea name="post_roll_vast_alt_url" rows="3" cols="40">{{$player_data.post_roll_vast_alt_url}}</textarea>
					<span class="de_hint">{{$lang.settings.common_field_advertising_vast_alt_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label enable_post_roll_on enable_post_roll_html_on enable_post_roll_on_or_enable_post_roll_html_on">{{$lang.settings.player_field_post_roll_duration}}</td>
				<td class="de_control">
					<input type="text" name="post_roll_duration" maxlength="5" size="5" value="{{$player_data.post_roll_duration}}"/>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_duration_hint}}</span>
					<span class="de_hint enable_post_roll_vast_on">{{$lang.settings.player_field_post_roll_duration_hint2}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_duration_text}}</td>
				<td class="de_control">
					<input type="text" name="post_roll_duration_text" maxlength="100" value="{{$player_data.post_roll_duration_text}}"/>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_duration_text_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_post_roll_skip}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_post_roll_skip" value="1" {{if $player_data.enable_post_roll_skip==1}}checked{{/if}}/><label>{{$lang.settings.player_field_post_roll_skip_after}}</label></span>
					<span>
						<input type="text" name="post_roll_skip_duration" maxlength="5" size="5" class="enable_post_roll_skip_on" value="{{$player_data.post_roll_skip_duration}}"/>
						{{$lang.settings.player_field_post_roll_skip_after_seconds}}
					</span>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_skip_hint}}</span>
					<span class="de_hint enable_post_roll_vast_on">{{$lang.settings.player_field_post_roll_skip_hint2}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_skip_on">
				<td class="de_label de_dependent">{{$lang.settings.player_field_post_roll_skip_text1}}</td>
				<td class="de_control">
					<input type="text" name="post_roll_skip_text1" maxlength="100" value="{{$player_data.post_roll_skip_text1}}"/>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_skip_text1_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_post_roll_skip_on">
				<td class="de_label de_required de_dependent">{{$lang.settings.player_field_post_roll_skip_text2}}</td>
				<td class="de_control">
					<input type="text" name="post_roll_skip_text2" maxlength="100" value="{{$player_data.post_roll_skip_text2}}"/>
					<span class="de_hint">{{$lang.settings.player_field_post_roll_skip_text2_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_pause_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_pause_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pause_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pause" value="1" {{if $player_data.enable_pause==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pause_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_pause_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pause_on">
				<td class="de_label de_dependent pause_file_source_1">{{$lang.settings.player_field_pause_file}}<span class="pause_file_source_2 pause_file_source_3 pause_file_source_4 pause_file_source_5 pause_file_source_6 pause_file_source_7 pause_file_source_8 pause_file_source_9 pause_file_source_10 pause_file_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span>
				</td>
				<td class="de_control de_vis_sw_select">
					<select name="pause_file_source">
						<option value="1" {{if $player_data.pause_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pause_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_pause_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $player_data.pause_file!=''}}
								<span class="js_param">preview_url={{$player_path}}/{{$player_data.pause_file}}</span>
							{{/if}}
						</div>
						<input type="text" name="pause_file" maxlength="100" {{if $player_data.pause_file!=''}}value="{{$player_data.pause_file}}"{{/if}}/>
						<input type="hidden" name="pause_file_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $player_data.pause_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					<span class="de_hint">{{$lang.settings.player_field_pause_file_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pause_on">
				<td class="de_label de_dependent pause_url_source_1 pause_url_source_2 pause_url_source_3 pause_url_source_4 pause_url_source_5 pause_url_source_6 pause_url_source_7 pause_url_source_8 pause_url_source_9 pause_url_source_10 pause_url_source_11 pause_url_source_12">{{$lang.settings.player_field_pause_url}}<span class="pause_url_source_2 pause_url_source_3 pause_url_source_4 pause_url_source_5 pause_url_source_6 pause_url_source_7 pause_url_source_8 pause_url_source_9 pause_url_source_10 pause_url_source_11 pause_url_source_12"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control de_vis_sw_select">
					<select name="pause_url_source">
						<option value="1" {{if $player_data.pause_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
							<option value="2" {{if $player_data.pause_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
								<option value="{{$smarty.section.data.index+2}}" {{if $player_data.pause_url_source==$smarty.section.data.index+2}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
							{{/section}}
						</optgroup>
					</select>
					<input type="text" name="pause_url" maxlength="255" size="40" value="{{$player_data.pause_url}}"/>
					<span class="de_hint">{{$lang.settings.player_field_pause_url_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_pause_html_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_pause_html" value="1" {{if $player_data.enable_pause_html==1}}checked{{/if}}/><label>{{$lang.settings.player_field_pause_html_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.settings.player_field_pause_html_enable_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pause_html_on">
				<td class="de_label de_dependent pause_html_source_1">{{$lang.settings.player_field_pause_html_code}}<span class="pause_html_source_2 pause_html_source_3 pause_html_source_4 pause_html_source_5 pause_html_source_6 pause_html_source_7 pause_html_source_8 pause_html_source_9 pause_html_source_10 pause_html_source_11"> {{$lang.settings.common_field_advertising_default_mark}}</span></td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td class="de_vis_sw_select">
								<select name="pause_html_source">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.pause_html_source==1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pause_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.pause_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.pause_html_source!=''}}
											<option value="{{$player_data.pause_html_source}}" selected>{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								<span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
							</td>
						</tr>
						<tr class="pause_html_source_1 pause_html_source_2 pause_html_source_3 pause_html_source_4 pause_html_source_5 pause_html_source_6 pause_html_source_7 pause_html_source_8 pause_html_source_9 pause_html_source_10 pause_html_source_11">
							<td>
								<div class="code_editor" data-syntax="html">
									<textarea name="pause_html_code" rows="4">{{$player_data.pause_html_code}}</textarea>
								</div>
								<span class="de_hint">{{$lang.settings.player_field_pause_html_code_hint}}</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pause_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}</td>
				<td class="de_control">
					<input type="text" name="pause_html_bg" maxlength="20" value="{{$player_data.pause_html_bg|default:"#000000"}}"/>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on enable_pause_html_on">
				<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="pause_html_adaptive" value="1" {{if $player_data.pause_html_adaptive==1}}checked{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></span>
					<span>
						<input type="text" name="pause_html_adaptive_width" maxlength="3" size="3" class="pause_html_adaptive_on" value="{{$player_data.pause_html_adaptive_width}}"/>
						%
						x
						<input type="text" name="pause_html_adaptive_height" maxlength="3" size="3" class="pause_html_adaptive_on" value="{{$player_data.pause_html_adaptive_height}}"/>
						%
					</span>
					<span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_float_settings}}</h2></td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.player_divider_float_settings_hint}}</span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_float_options}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="enable_float_replay" value="1" {{if $player_data.enable_float_replay==1}}checked{{/if}}/><label>{{$lang.settings.player_field_float_options_replay}}</label></span>
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td colspan="2"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_float1" value="1" {{if $player_data.enable_float1==1}}checked{{/if}}/><label>{{$lang.settings.player_field_float_enable|replace:"%1%":"1"}}</label></span></td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_time}}</td>
							<td class="de_control">
								<input type="text" name="float1_time" maxlength="5" size="5" value="{{$player_data.float1_time}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_duration}}</td>
							<td class="de_control">
								<input type="text" name="float1_duration" maxlength="5" size="5" value="{{$player_data.float1_duration}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_location}}</td>
							<td class="de_control">
								<select name="float1_location">
									<option value="bottom" {{if $player_data.float1_location=='bottom'}}selected{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
									<option value="top" {{if $player_data.float1_location=='top'}}selected{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
								</select>
							</td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_size}}</td>
							<td class="de_control de_vis_sw_select">
								<select name="float1_size">
									<option value="0" {{if $player_data.float1_size==0}}selected{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float1_size==1}}selected{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								<span class="float1_size_1">
									<input type="text" name="float1_size_width" maxlength="5" size="5" class="float1_size_1" value="{{$player_data.float1_size_width}}"/>
									x
									<input type="text" name="float1_size_height" maxlength="5" size="5" class="float1_size_1" value="{{$player_data.float1_size_height}}"/>
								</span>
							</td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_file}}</td>
							<td class="de_control">
								<select name="float1_file_source">
									<option value="1" {{if $player_data.float1_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float1_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
								</select>
								<div class="de_fu">
									<div class="js_params">
										<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
										<span class="js_param">accept={{$config.image_allowed_ext}}</span>
										{{if $player_data.float1_file!=''}}
											{{if in_array(end(explode(".",$player_data.float1_file)),explode(",",$config.image_allowed_ext))}}
												<span class="js_param">preview_url={{$player_path}}/{{$player_data.float1_file}}</span>
											{{else}}
												<span class="js_param">download_url={{$player_path}}/{{$player_data.float1_file}}</span>
											{{/if}}
										{{/if}}
									</div>
									<input type="text" name="float1_file" maxlength="100" {{if $player_data.float1_file!=''}}value="{{$player_data.float1_file}}"{{/if}}/>
									<input type="hidden" name="float1_file_hash"/>
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove {{if $player_data.float1_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								</div>
							</td>
						</tr>
						<tr class="eg_data enable_float1_on">
							<td class="de_label">{{$lang.settings.player_field_float_url}}</td>
							<td class="de_control">
								<select name="float1_url_source">
									<option value="1" {{if $player_data.float1_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										<option value="2" {{if $player_data.float1_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
									</optgroup>
								</select>
								<input type="text" name="float1_url" maxlength="255" size="40" value="{{$player_data.float1_url}}"/>
							</td>
						</tr>
						<tr class="eg_header">
							<td colspan="2"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_float2" value="1" {{if $player_data.enable_float2==1}}checked{{/if}}/><label>{{$lang.settings.player_field_float_enable|replace:"%1%":"2"}}</label></span></td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_time}}</td>
							<td class="de_control">
								<input type="text" name="float2_time" maxlength="5" size="5" value="{{$player_data.float2_time}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_duration}}</td>
							<td class="de_control">
								<input type="text" name="float2_duration" maxlength="5" size="5" value="{{$player_data.float2_duration}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_location}}</td>
							<td class="de_control">
								<select name="float2_location">
									<option value="bottom" {{if $player_data.float2_location=='bottom'}}selected{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
									<option value="top" {{if $player_data.float2_location=='top'}}selected{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
								</select>
							</td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_size}}</td>
							<td class="de_control de_vis_sw_select">
								<select name="float2_size">
									<option value="0" {{if $player_data.float2_size==0}}selected{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float2_size==1}}selected{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								<span class="float2_size_1">
									<input type="text" name="float2_size_width" maxlength="5" size="5" class="float2_size_1" value="{{$player_data.float2_size_width}}"/>
									x
									<input type="text" name="float2_size_height" maxlength="5" size="5" class="float2_size_1" value="{{$player_data.float2_size_height}}"/>
								</span>
							</td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_file}}</td>
							<td class="de_control">
								<select name="float2_file_source">
									<option value="1" {{if $player_data.float2_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float2_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
								</select>
								<div class="de_fu">
									<div class="js_params">
										<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
										<span class="js_param">accept={{$config.image_allowed_ext}}</span>
										{{if $player_data.float2_file!=''}}
											{{if in_array(end(explode(".",$player_data.float2_file)),explode(",",$config.image_allowed_ext))}}
												<span class="js_param">preview_url={{$player_path}}/{{$player_data.float2_file}}</span>
											{{else}}
												<span class="js_param">download_url={{$player_path}}/{{$player_data.float2_file}}</span>
											{{/if}}
										{{/if}}
									</div>
									<input type="text" name="float2_file" maxlength="100" {{if $player_data.float2_file!=''}}value="{{$player_data.float2_file}}"{{/if}}/>
									<input type="hidden" name="float2_file_hash"/>
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove {{if $player_data.float2_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								</div>
							</td>
						</tr>
						<tr class="eg_data enable_float2_on">
							<td class="de_label">{{$lang.settings.player_field_float_url}}</td>
							<td class="de_control">
								<select name="float2_url_source">
									<option value="1" {{if $player_data.float2_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										<option value="2" {{if $player_data.float2_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
									</optgroup>
								</select>
								<input type="text" name="float2_url" maxlength="255" size="40" value="{{$player_data.float2_url}}"/>
							</td>
						</tr>
						<tr class="eg_header">
							<td colspan="2"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_float3" value="1" {{if $player_data.enable_float3==1}}checked{{/if}}/><label>{{$lang.settings.player_field_float_enable|replace:"%1%":"3"}}</label></span></td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_time}}</td>
							<td class="de_control">
								<input type="text" name="float3_time" maxlength="5" size="5" value="{{$player_data.float3_time}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_duration}}</td>
							<td class="de_control">
								<input type="text" name="float3_duration" maxlength="5" size="5" value="{{$player_data.float3_duration}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_location}}</td>
							<td class="de_control">
								<select name="float3_location">
									<option value="bottom" {{if $player_data.float3_location=='bottom'}}selected{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
									<option value="top" {{if $player_data.float3_location=='top'}}selected{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
								</select>
							</td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_size}}</td>
							<td class="de_control de_vis_sw_select">
								<select name="float3_size">
									<option value="0" {{if $player_data.float3_size==0}}selected{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float3_size==1}}selected{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								<span class="float3_size_1">
									<input type="text" name="float3_size_width" maxlength="5" size="5" class="float3_size_1" value="{{$player_data.float3_size_width}}"/>
									x
									<input type="text" name="float3_size_height" maxlength="5" size="5" class="float3_size_1" value="{{$player_data.float3_size_height}}"/>
								</span>
							</td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_file}}</td>
							<td class="de_control">
								<select name="float3_file_source">
									<option value="1" {{if $player_data.float3_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float3_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
								</select>
								<div class="de_fu">
									<div class="js_params">
										<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
										<span class="js_param">accept={{$config.image_allowed_ext}}</span>
										{{if $player_data.float3_file!=''}}
											{{if in_array(end(explode(".",$player_data.float3_file)),explode(",",$config.image_allowed_ext))}}
												<span class="js_param">preview_url={{$player_path}}/{{$player_data.float3_file}}</span>
											{{else}}
												<span class="js_param">download_url={{$player_path}}/{{$player_data.float3_file}}</span>
											{{/if}}
										{{/if}}
									</div>
									<input type="text" name="float3_file" maxlength="100" {{if $player_data.float3_file!=''}}value="{{$player_data.float3_file}}"{{/if}}/>
									<input type="hidden" name="float3_file_hash"/>
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove {{if $player_data.float3_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								</div>
							</td>
						</tr>
						<tr class="eg_data enable_float3_on">
							<td class="de_label">{{$lang.settings.player_field_float_url}}</td>
							<td class="de_control">
								<select name="float3_url_source">
									<option value="1" {{if $player_data.float3_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										<option value="2" {{if $player_data.float3_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
									</optgroup>
								</select>
								<input type="text" name="float3_url" maxlength="255" size="40" value="{{$player_data.float3_url}}"/>
							</td>
						</tr>
						<tr class="eg_header">
							<td colspan="2"><span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="enable_float4" value="1" {{if $player_data.enable_float4==1}}checked{{/if}}/><label>{{$lang.settings.player_field_float_enable|replace:"%1%":"4"}}</label></span></td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_time}}</td>
							<td class="de_control">
								<input type="text" name="float4_time" maxlength="5" size="5" value="{{$player_data.float4_time}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_duration}}</td>
							<td class="de_control">
								<input type="text" name="float4_duration" maxlength="5" size="5" value="{{$player_data.float4_duration}}"/>
							</td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_location}}</td>
							<td class="de_control">
								<select name="float4_location">
									<option value="bottom" {{if $player_data.float4_location=='bottom'}}selected{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
									<option value="top" {{if $player_data.float4_location=='top'}}selected{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
								</select>
							</td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_size}}</td>
							<td class="de_control de_vis_sw_select">
								<select name="float4_size">
									<option value="0" {{if $player_data.float4_size==0}}selected{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float4_size==1}}selected{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								<span class="float4_size_1">
									<input type="text" name="float4_size_width" maxlength="5" size="5" class="float4_size_1" value="{{$player_data.float4_size_width}}"/>
									x
									<input type="text" name="float4_size_height" maxlength="5" size="5" class="float4_size_1" value="{{$player_data.float4_size_height}}"/>
								</span>
							</td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_file}}</td>
							<td class="de_control">
								<select name="float4_file_source">
									<option value="1" {{if $player_data.float4_file_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float4_file_source==$smarty.section.data.index+1}}selected{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
								</select>
								<div class="de_fu">
									<div class="js_params">
										<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
										<span class="js_param">accept={{$config.image_allowed_ext}}</span>
										{{if $player_data.float4_file!=''}}
											{{if in_array(end(explode(".",$player_data.float4_file)),explode(",",$config.image_allowed_ext))}}
												<span class="js_param">preview_url={{$player_path}}/{{$player_data.float4_file}}</span>
											{{else}}
												<span class="js_param">download_url={{$player_path}}/{{$player_data.float4_file}}</span>
											{{/if}}
										{{/if}}
									</div>
									<input type="text" name="float4_file" maxlength="100" {{if $player_data.float4_file!=''}}value="{{$player_data.float4_file}}"{{/if}}/>
									<input type="hidden" name="float4_file_hash"/>
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove {{if $player_data.float4_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								</div>
							</td>
						</tr>
						<tr class="eg_data enable_float4_on">
							<td class="de_label">{{$lang.settings.player_field_float_url}}</td>
							<td class="de_control">
								<select name="float4_url_source">
									<option value="1" {{if $player_data.float4_url_source==1}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										<option value="2" {{if $player_data.float4_url_source==2}}selected{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
									</optgroup>
								</select>
								<input type="text" name="float4_url" maxlength="255" size="40" value="{{$player_data.float4_url}}"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $smarty.request.page=='embed' && $smarty.get.embed_profile_id==''}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.settings.player_divider_embed_access_control}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_black_list_countries}}</td>
					<td class="de_control">
						<textarea name="black_list_countries" cols="40" rows="3">{{$player_data.black_list_countries}}</textarea>
						<span class="de_hint">{{$lang.settings.player_field_black_list_countries_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_black_list_domains}}</td>
					<td class="de_control">
						<textarea name="black_list_domains" cols="40" rows="3">{{$player_data.black_list_domains}}</textarea>
						<span class="de_hint">{{$lang.settings.player_field_black_list_domains_hint}}</span>
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.settings.player_field_player_replacement_html}}</td>
					<td class="de_control">
						<div class="code_editor" data-syntax="html">
							<textarea name="player_replacement_html" cols="40" rows="10">{{$player_data.player_replacement_html}}</textarea>
						</div>
						<span class="de_hint">{{$lang.settings.player_field_player_replacement_html_hint}}</span>
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="change_complete"/>
		{{if $smarty.request.page=='embed'}}
			<input type="hidden" name="is_embed" value="1"/>
			{{if $smarty.request.embed_profile_id!=''}}
				<input type="hidden" name="embed_profile_id" value="{{$smarty.request.embed_profile_id}}"/>
			{{/if}}
		{{/if}}
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
		<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
	</div>
</form>