{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.external_search.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.external_search.long_desc}}
				</td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/4004-how-to-use-sphinxsearch-with-kvs-for-better-search-and-related-videos-performance/">How to use SphinxSearch with KVS for better search and related videos performance</a>
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.external_search.divider_videos}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.external_search.field_enable_external_search_videos}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="enable_external_search">
						<option value="0" {{if $smarty.post.enable_external_search==0}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_never}}</option>
						<option value="1" {{if $smarty.post.enable_external_search==1}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_always}}</option>
						<option value="2" {{if $smarty.post.enable_external_search==2}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_condition}}</option>
					</select>
					<span class="enable_external_search_2">
						<input type="text" name="enable_external_search_condition" size="4" maxlength="10" value="{{$smarty.post.enable_external_search_condition}}"/>
					</span>
					<span class="de_hint">{{$lang.plugins.external_search.field_enable_external_search_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_1 enable_external_search_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_display_results}}</td>
				<td class="de_control">
					<select name="display_results">
						<option value="0" {{if $smarty.post.display_results==0}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_replace}}</option>
						<option value="1" {{if $smarty.post.display_results==1}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_beginning}}</option>
						<option value="2" {{if $smarty.post.display_results==2}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_end}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.external_search.field_display_results_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_1 enable_external_search_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_api_call}}</td>
				<td class="de_control">
					<input type="text" name="api_call" value="{{$smarty.post.api_call}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_api_call_hint_videos|replace:"%1%":$config.project_url}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_1 enable_external_search_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_outgoing_url}}</td>
				<td class="de_control">
					<input type="text" name="outgoing_url" value="{{$smarty.post.outgoing_url}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_outgoing_url_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_1 enable_external_search_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_query_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.query_time>0}}
							{{$smarty.post.performance.query_time}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="enable_external_search_1 enable_external_search_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_parse_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.parse_time>0}}
							{{$smarty.post.performance.parse_time}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.external_search.divider_albums}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.external_search.field_enable_external_search_albums}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="enable_external_search_albums">
						<option value="0" {{if $smarty.post.enable_external_search_albums==0}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_never}}</option>
						<option value="1" {{if $smarty.post.enable_external_search_albums==1}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_always}}</option>
						<option value="2" {{if $smarty.post.enable_external_search_albums==2}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_condition}}</option>
					</select>
					<span class="enable_external_search_albums_2">
						<input type="text" name="enable_external_search_albums_condition" size="4" maxlength="10" value="{{$smarty.post.enable_external_search_albums_condition}}"/>
					</span>
					<span class="de_hint">{{$lang.plugins.external_search.field_enable_external_search_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_albums_1 enable_external_search_albums_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_display_results}}</td>
				<td class="de_control">
					<select name="display_results_albums">
						<option value="0" {{if $smarty.post.display_results_albums==0}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_replace}}</option>
						<option value="1" {{if $smarty.post.display_results_albums==1}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_beginning}}</option>
						<option value="2" {{if $smarty.post.display_results_albums==2}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_end}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.external_search.field_display_results_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_albums_1 enable_external_search_albums_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_api_call}}</td>
				<td class="de_control">
					<input type="text" name="api_call_albums" value="{{$smarty.post.api_call_albums}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_api_call_hint_albums|replace:"%1%":$config.project_url}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_albums_1 enable_external_search_albums_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_outgoing_url}}</td>
				<td class="de_control">
					<input type="text" name="outgoing_url_albums" value="{{$smarty.post.outgoing_url_albums}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_outgoing_url_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_albums_1 enable_external_search_albums_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_query_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.query_time_albums>0}}
							{{$smarty.post.performance.query_time_albums}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="enable_external_search_albums_1 enable_external_search_albums_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_parse_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.parse_time_albums>0}}
							{{$smarty.post.performance.parse_time_albums}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.external_search.divider_searches}}</h2></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.external_search.field_enable_external_search_searches}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="enable_external_search_searches">
						<option value="0" {{if $smarty.post.enable_external_search_searches==0}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_never}}</option>
						<option value="1" {{if $smarty.post.enable_external_search_searches==1}}selected{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_always}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.external_search.field_enable_external_search_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_searches_1 enable_external_search_searches_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_display_results}}</td>
				<td class="de_control">
					<select name="display_results_searches">
						<option value="0" {{if $smarty.post.display_results_searches==0}}selected{{/if}}>{{$lang.plugins.external_search.field_display_results_replace}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.external_search.field_display_results_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_searches_1 enable_external_search_searches_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_api_call}}</td>
				<td class="de_control">
					<input type="text" name="api_call_searches" value="{{$smarty.post.api_call_searches}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_api_call_hint_searches|replace:"%1%":$config.project_url}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_searches_1 enable_external_search_searches_2">
				<td class="de_label de_required de_dependent">{{$lang.plugins.external_search.field_outgoing_url}}</td>
				<td class="de_control">
					<input type="text" name="outgoing_url_searches" value="{{$smarty.post.outgoing_url_searches}}">
					<span class="de_hint">{{$lang.plugins.external_search.field_outgoing_url_hint}}</span>
				</td>
			</tr>
			<tr class="enable_external_search_searches_1 enable_external_search_searches_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_query_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.query_time_searches>0}}
							{{$smarty.post.performance.query_time_searches}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
			<tr class="enable_external_search_searches_1 enable_external_search_searches_2">
				<td class="de_label de_dependent">{{$lang.plugins.external_search.field_avg_parse_time}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.performance.parse_time_searches>0}}
							{{$smarty.post.performance.parse_time_searches}} {{$lang.common.second_truncated}}
						{{else}}
							{{$lang.common.undefined}}
						{{/if}}
					</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.external_search.btn_save}}"/>
	</div>
</form>