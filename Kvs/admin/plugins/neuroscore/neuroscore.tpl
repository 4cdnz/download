{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.neuroscore.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.neuroscore.long_desc}}
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
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.neuroscore.divider_general}}</h2></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.neuroscore.field_api_key}}</td>
				<td class="de_control">
					<input type="text" name="api_key" value="{{$smarty.post.api_key}}"/>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_api_key_hint|smarty:nodefaults}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_balance}}</td>
				<td class="de_control">
					<span>
						${{$smarty.post.balance_usd|default:'0.00'}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_on_empty_balance}}</td>
				<td class="de_control">
					<select name="on_empty_balance">
						<option value="0" {{if $smarty.post.on_empty_balance==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_on_empty_balance_schedule}}</option>
						<option value="1" {{if $smarty.post.on_empty_balance==1}}selected{{/if}}>{{$lang.plugins.neuroscore.field_on_empty_balance_ignore}}</option>
					</select>
					{{if $smarty.post.on_empty_balance==0}}
						<span>
							{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$smarty.post.total_stats_postponed}}
						</span>
					{{/if}}
					<span class="de_hint">{{$lang.plugins.neuroscore.field_on_empty_balance_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_enable_debug}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_enable_debug_enabled}}</label></span>
					{{if $smarty.post.is_debug_enabled==1}}
						<span>(<a href="{{$page_name}}?plugin_id=neuroscore&amp;action=get_debug_log" rel="log">{{$lang.plugins.neuroscore.field_enable_debug_log}}</a>)</span>
					{{/if}}
					<span class="de_hint">{{$lang.plugins.neuroscore.field_enable_debug_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_stats}}</td>
				<td class="de_control">
					<span>
						{{foreach from=$smarty.post.total_stats|smarty:nodefaults item="stat" name="stats"}}
							{{if $stat.operation_status_id==0}}
								{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}
							{{elseif $stat.operation_status_id==1}}
								{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}
							{{elseif $stat.operation_status_id==2}}
								{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}
							{{elseif $stat.operation_status_id==3}}
								{{$lang.plugins.neuroscore.field_stats_deleted|replace:"%1%":$stat.tasks}}
							{{/if}}
							{{if !$smarty.foreach.stats.last}},{{/if}}
						{{foreachelse}}
							{{$lang.plugins.neuroscore.field_stats_none}}
						{{/foreach}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.neuroscore.divider_score}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.neuroscore.divider_score_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_score_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="score_is_enabled" value="1" {{if $smarty.post.score_is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_score_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_score_enable_hint}}</span>
				</td>
			</tr>
			<tr class="score_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_score_screenshot_type}}</td>
				<td class="de_control">
					<select name="score_screenshot_type">
						<option value="0" {{if $smarty.post.score_screenshot_type==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_score_screenshot_type_all}}</option>
						<option value="1" {{if $smarty.post.score_screenshot_type==1}}selected{{/if}}>{{$lang.plugins.neuroscore.field_score_screenshot_type_auto}}</option>
					</select>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_score_screenshot_type_hint}}</span>
				</td>
			</tr>
			<tr class="score_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_score_screenshot_max_count}}</td>
				<td class="de_control">
					<input type="text" name="score_screenshot_max_count" size="4" value="{{if $smarty.post.score_screenshot_max_count>0}}{{$smarty.post.score_screenshot_max_count}}{{/if}}"/>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_score_screenshot_max_count_hint}}</span>
				</td>
			</tr>
			<tr class="score_is_enabled_on">
				<td class="de_label score_screenshot_retain_option_1">{{$lang.plugins.neuroscore.field_score_screenshot_retain}}</td>
				<td class="de_control de_vis_sw_select">
					<select name="score_screenshot_retain_option">
						<option value="0" {{if $smarty.post.score_screenshot_retain_count==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_score_screenshot_retain_all}}</option>
						<option value="1" {{if $smarty.post.score_screenshot_retain_count>0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_score_screenshot_retain_count}}</option>
					</select>
					<span class="score_screenshot_retain_option_1">
						<input type="text" name="score_screenshot_retain_count" size="4" value="{{if $smarty.post.score_screenshot_retain_count>0}}{{$smarty.post.score_screenshot_retain_count}}{{/if}}"/>
					</span>
					<span class="de_hint score_screenshot_retain_option_0">{{$lang.plugins.neuroscore.field_score_screenshot_retain_all_hint}}</span>
					<span class="de_hint score_screenshot_retain_option_1">{{$lang.plugins.neuroscore.field_score_screenshot_retain_count_hint}}</span>
				</td>
			</tr>
			<tr class="score_is_enabled_on">
				<td class="de_label de_required">{{$lang.plugins.neuroscore.field_apply_to}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="values" value="admins,site,import,feeds,grabbers,ftp,manual"}}
						{{foreach from=","|explode:$values item="item"}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="score_apply_to_{{$item}}" name="score_apply_to[]" value="{{$item}}" {{if in_array($item, $smarty.post.score_apply_to)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.$item}}</label></span>
									{{assign var="item_hint" value="`$item`_hint"}}
									{{if $lang.plugins.neuroscore.field_apply_to_list.$item_hint}}
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.$item_hint}}</span>
									{{/if}}
								</td>
							</tr>
							{{if $item=='feeds'}}
								<tr class="score_apply_to_{{$item}}_on">
									<td class="de_dependent de_vis_sw_select">
										<select name="score_apply_to_feeds_type">
											<option value="0" {{if count($smarty.post.score_apply_to_feeds)==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_all}}</option>
											<option value="1" {{if count($smarty.post.score_apply_to_feeds)>0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_selected}}</option>
										</select>
									</td>
								</tr>
								{{foreach from=$smarty.post.feeds|smarty:nodefaults item="feed"}}
									<tr class="score_apply_to_{{$item}}_on score_apply_to_feeds_type_1">
										<td class="de_dependent">
											<span class="de_lv_pair"><input type="checkbox" name="score_apply_to_feeds[]" value="{{$feed.feed_id}}" {{if in_array($feed.feed_id, $smarty.post.score_apply_to_feeds)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_feeds_feed|replace:"%1%":$feed.title}}</label></span>
										</td>
									</tr>
								{{/foreach}}
							{{/if}}
							{{if $item=='manual'}}
								<tr class="score_apply_to_{{$item}}_on">
									<td class="de_dependent">
										<span class="de_lv_pair"><input type="checkbox" name="score_apply_to_manual_repeat" value="1" {{if $smarty.post.score_apply_to_manual_repeat==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat}}</label></span>
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat_hint}}</span>
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr class="score_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_stats}}</td>
				<td class="de_control">
					<span>
						{{foreach from=$smarty.post.score_stats|smarty:nodefaults item="stat" name="stats"}}
							{{if $stat.operation_status_id==0}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=score_postponed">{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==1}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=score_processing">{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==2}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=score_finished">{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==3}}
								{{$lang.plugins.neuroscore.field_stats_deleted|replace:"%1%":$stat.tasks}}
							{{/if}}
							{{if !$smarty.foreach.stats.last}},{{/if}}
						{{foreachelse}}
							{{$lang.plugins.neuroscore.field_stats_none}}
						{{/foreach}}
					</span>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.neuroscore.divider_title}}</h2></td>
			</tr>
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.neuroscore.divider_title_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.neuroscore.field_title_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="title_is_enabled" value="1" {{if $smarty.post.title_is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_title_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_title_enable_hint}}</span>
				</td>
			</tr>
			<tr class="title_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_title_rewrite_directories}}</td>
				<td class="de_control">
					<span class="de_lv_pair"><input type="checkbox" name="title_is_rewrite_directories" value="1" {{if $smarty.post.title_is_rewrite_directories==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_title_rewrite_directories_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_title_rewrite_directories_hint}}</span>
				</td>
			</tr>
			<tr class="title_is_enabled_on">
				<td class="de_label de_required">{{$lang.plugins.neuroscore.field_apply_to}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="values" value="admins,site,import,feeds,grabbers,ftp,manual"}}
						{{foreach from=","|explode:$values item="item"}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="title_apply_to_{{$item}}" name="title_apply_to[]" value="{{$item}}" {{if in_array($item, $smarty.post.title_apply_to)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.$item}}</label></span>
									{{assign var="item_hint" value="`$item`_hint"}}
									{{if $lang.plugins.neuroscore.field_apply_to_list.$item_hint}}
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.$item_hint}}</span>
									{{/if}}
								</td>
							</tr>
							{{if $item=='feeds'}}
								<tr class="title_apply_to_{{$item}}_on">
									<td class="de_dependent de_vis_sw_select">
										<select name="title_apply_to_feeds_type">
											<option value="0" {{if count($smarty.post.title_apply_to_feeds)==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_all}}</option>
											<option value="1" {{if count($smarty.post.title_apply_to_feeds)>0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_selected}}</option>
										</select>
									</td>
								</tr>
								{{foreach from=$smarty.post.feeds|smarty:nodefaults item="feed"}}
									<tr class="title_apply_to_{{$item}}_on title_apply_to_feeds_type_1">
										<td class="de_dependent">
											<span class="de_lv_pair"><input type="checkbox" name="title_apply_to_feeds[]" value="{{$feed.feed_id}}" {{if in_array($feed.feed_id, $smarty.post.title_apply_to_feeds)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_feeds_feed|replace:"%1%":$feed.title}}</label></span>
										</td>
									</tr>
								{{/foreach}}
							{{/if}}
							{{if $item=='manual'}}
								<tr class="title_apply_to_{{$item}}_on">
									<td class="de_dependent">
										<span class="de_lv_pair"><input type="checkbox" name="title_apply_to_manual_repeat" value="1" {{if $smarty.post.title_apply_to_manual_repeat==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat}}</label></span>
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat_hint}}</span>
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr class="title_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_stats}}</td>
				<td class="de_control">
					<span>
						{{foreach from=$smarty.post.title_stats|smarty:nodefaults item="stat" name="stats"}}
							{{if $stat.operation_status_id==0}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=title_postponed">{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==1}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=title_processing">{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==2}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=title_finished">{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==3}}
								{{$lang.plugins.neuroscore.field_stats_deleted|replace:"%1%":$stat.tasks}}
							{{/if}}
							{{if !$smarty.foreach.stats.last}},{{/if}}
						{{foreachelse}}
							{{$lang.plugins.neuroscore.field_stats_none}}
						{{/foreach}}
					</span>
				</td>
			</tr>
			<tr class="hidden">
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.neuroscore.divider_categories}}</h2></td>
			</tr>
			<tr class="hidden">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.neuroscore.divider_categories_hint}}</span>
				</td>
			</tr>
			<tr class="hidden">
				<td class="de_label">{{$lang.plugins.neuroscore.field_categories_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="categories_is_enabled" value="1" {{if $smarty.post.categories_is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_categories_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_categories_enable_hint}}</span>
				</td>
			</tr>
			<tr class="categories_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_categories_type}}</td>
				<td class="de_control">
					<select name="categories_type">
						<option value="categories" {{if $smarty.post.categories_type=='categories'}}selected{{/if}}>{{$lang.plugins.neuroscore.field_categories_type_categories}}</option>
						<option value="tags" {{if $smarty.post.categories_type=='tags'}}selected{{/if}}>{{$lang.plugins.neuroscore.field_categories_type_tags}}</option>
					</select>
				</td>
			</tr>
			<tr class="categories_is_enabled_on">
				<td class="de_label de_required">{{$lang.plugins.neuroscore.field_apply_to}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="values" value="admins,site,import,feeds,grabbers,ftp,manual"}}
						{{foreach from=","|explode:$values item="item"}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="categories_apply_to_{{$item}}" name="categories_apply_to[]" value="{{$item}}" {{if in_array($item, $smarty.post.categories_apply_to)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.$item}}</label></span>
									{{assign var="item_hint" value="`$item`_hint"}}
									{{if $lang.plugins.neuroscore.field_apply_to_list.$item_hint}}
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.$item_hint}}</span>
									{{/if}}
								</td>
							</tr>
							{{if $item=='feeds'}}
								<tr class="categories_apply_to_{{$item}}_on">
									<td class="de_dependent de_vis_sw_select">
										<select name="categories_apply_to_feeds_type">
											<option value="0" {{if count($smarty.post.categories_apply_to_feeds)==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_all}}</option>
											<option value="1" {{if count($smarty.post.categories_apply_to_feeds)>0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_selected}}</option>
										</select>
									</td>
								</tr>
								{{foreach from=$smarty.post.feeds|smarty:nodefaults item="feed"}}
									<tr class="categories_apply_to_{{$item}}_on categories_apply_to_feeds_type_1">
										<td class="de_dependent">
											<span class="de_lv_pair"><input type="checkbox" name="categories_apply_to_feeds[]" value="{{$feed.feed_id}}" {{if in_array($feed.feed_id, $smarty.post.categories_apply_to_feeds)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_feeds_feed|replace:"%1%":$feed.title}}</label></span>
										</td>
									</tr>
								{{/foreach}}
							{{/if}}
							{{if $item=='manual'}}
								<tr class="categories_apply_to_{{$item}}_on">
									<td class="de_dependent">
										<span class="de_lv_pair"><input type="checkbox" name="categories_apply_to_manual_repeat" value="1" {{if $smarty.post.categories_apply_to_manual_repeat==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat}}</label></span>
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat_hint}}</span>
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="categories_apply_to_empty" value="1" {{if $smarty.post.categories_apply_to_empty==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_empty_categories}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="categories_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_stats}}</td>
				<td class="de_control">
					<span>
						{{foreach from=$smarty.post.categories_stats|smarty:nodefaults item="stat" name="stats"}}
							{{if $stat.operation_status_id==0}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=categories_postponed">{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==1}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=categories_processing">{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==2}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=categories_finished">{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==3}}
								{{$lang.plugins.neuroscore.field_stats_deleted|replace:"%1%":$stat.tasks}}
							{{/if}}
							{{if !$smarty.foreach.stats.last}},{{/if}}
						{{foreachelse}}
							{{$lang.plugins.neuroscore.field_stats_none}}
						{{/foreach}}
					</span>
				</td>
			</tr>
			<tr class="hidden">
				<td class="de_separator" colspan="2"><h2>{{$lang.plugins.neuroscore.divider_models}}</h2></td>
			</tr>
			<tr class="hidden">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.neuroscore.divider_models_hint}}</span>
				</td>
			</tr>
			<tr class="hidden">
				<td class="de_label">{{$lang.plugins.neuroscore.field_models_enable}}</td>
				<td class="de_control">
					<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="models_is_enabled" value="1" {{if $smarty.post.models_is_enabled==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_models_enable_enabled}}</label></span>
					<span class="de_hint">{{$lang.plugins.neuroscore.field_models_enable_hint}}</span>
				</td>
			</tr>
			<tr class="models_is_enabled_on">
				<td class="de_label de_required">{{$lang.plugins.neuroscore.field_apply_to}}</td>
				<td class="de_control">
					<table class="control_group">
						{{assign var="values" value="admins,site,import,feeds,grabbers,ftp,manual"}}
						{{foreach from=","|explode:$values item="item"}}
							<tr>
								<td>
									<span class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="models_apply_to_{{$item}}" name="models_apply_to[]" value="{{$item}}" {{if in_array($item, $smarty.post.models_apply_to)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.$item}}</label></span>
									{{assign var="item_hint" value="`$item`_hint"}}
									{{if $lang.plugins.neuroscore.field_apply_to_list.$item_hint}}
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.$item_hint}}</span>
									{{/if}}
								</td>
							</tr>
							{{if $item=='feeds'}}
								<tr class="models_apply_to_{{$item}}_on">
									<td class="de_dependent de_vis_sw_select">
										<select name="models_apply_to_feeds_type">
											<option value="0" {{if count($smarty.post.models_apply_to_feeds)==0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_all}}</option>
											<option value="1" {{if count($smarty.post.models_apply_to_feeds)>0}}selected{{/if}}>{{$lang.plugins.neuroscore.field_apply_to_feeds_selected}}</option>
										</select>
									</td>
								</tr>
								{{foreach from=$smarty.post.feeds|smarty:nodefaults item="feed"}}
									<tr class="models_apply_to_{{$item}}_on models_apply_to_feeds_type_1">
										<td class="de_dependent">
											<span class="de_lv_pair"><input type="checkbox" name="models_apply_to_feeds[]" value="{{$feed.feed_id}}" {{if in_array($feed.feed_id, $smarty.post.models_apply_to_feeds)}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_feeds_feed|replace:"%1%":$feed.title}}</label></span>
										</td>
									</tr>
								{{/foreach}}
							{{/if}}
							{{if $item=='manual'}}
								<tr class="models_apply_to_{{$item}}_on">
									<td class="de_dependent">
										<span class="de_lv_pair"><input type="checkbox" name="models_apply_to_manual_repeat" value="1" {{if $smarty.post.models_apply_to_manual_repeat==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat}}</label></span>
										<span class="de_hint">{{$lang.plugins.neuroscore.field_apply_to_list.manual_repeat_hint}}</span>
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="models_apply_to_empty" value="1" {{if $smarty.post.models_apply_to_empty==1}}checked{{/if}}/><label>{{$lang.plugins.neuroscore.field_apply_to_empty_models}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="models_is_enabled_on">
				<td class="de_label">{{$lang.plugins.neuroscore.field_stats}}</td>
				<td class="de_control">
					<span>
						{{foreach from=$smarty.post.models_stats|smarty:nodefaults item="stat" name="stats"}}
							{{if $stat.operation_status_id==0}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=models_postponed">{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_postponed|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==1}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=models_processing">{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_processing|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==2}}
								{{if in_array('videos|view',$smarty.session.permissions)}}
									<a href="videos.php?no_filter=true&amp;se_neuroscore=models_finished">{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}</a>
								{{else}}
									{{$lang.plugins.neuroscore.field_stats_finished|replace:"%1%":$stat.tasks}}
								{{/if}}
							{{elseif $stat.operation_status_id==3}}
								{{$lang.plugins.neuroscore.field_stats_deleted|replace:"%1%":$stat.tasks}}
							{{/if}}
							{{if !$smarty.foreach.stats.last}},{{/if}}
						{{foreachelse}}
							{{$lang.plugins.neuroscore.field_stats_none}}
						{{/foreach}}
					</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="submit" name="save_default" value="{{$lang.plugins.neuroscore.btn_save}}"/>
	</div>
</form>