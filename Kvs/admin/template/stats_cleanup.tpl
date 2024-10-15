{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="stats_cleanup">
	<div class="de_main">
		<div class="de_header"><h1>{{$lang.stats.cleanup_header}}</h1></div>
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
				<td class="de_label de_required">{{$lang.stats.cleanup_field_to_date}}</td>
				<td class="de_control">
					<span class="calendar">
						<input type="text" name="to_date" value="{{$smarty.now|date_format:$smarty.session.userdata.short_date_format}}" placeholder="{{$lang.common.select_default_option}}">
					</span>
					<span class="de_hint">{{$lang.stats.cleanup_field_to_date_hint}}</span>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.stats.cleanup_field_stats_to_cleanup}}</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="traffic" value="1" {{if $smarty.session.save.stats_cleanup.traffic==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_traffic}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="embed" value="1" {{if $smarty.session.save.stats_cleanup.embed==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_embed}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="videos" value="1" {{if $smarty.session.save.stats_cleanup.videos==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_videos}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="albums" value="1" {{if $smarty.session.save.stats_cleanup.albums==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_albums}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="player" value="1" {{if $smarty.session.save.stats_cleanup.player==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_player}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="search" value="1" {{if $smarty.session.save.stats_cleanup.search==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_search}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="memberzone" value="1" {{if $smarty.session.save.stats_cleanup.memberzone==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_memberzone}}</label></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="de_lv_pair"><input type="checkbox" name="overload" value="1" {{if $smarty.session.save.stats_cleanup.overload==1}}checked{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_overload}}</label></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="de_action_group">
		<input type="hidden" name="action" value="cleanup_complete"/>
		<input type="submit" name="save_default" value="{{$lang.stats.cleanup_btn_cleanup}}"/>
	</div>
</form>