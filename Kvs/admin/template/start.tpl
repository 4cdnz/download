{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}
<div id="start_page">
	<div id="general_info">
		<div id="welcome_mes">
			<h1>{{$lang.common.welcome|replace:"%1%":$smarty.session.userdata.login}}</h1>
			{{if $smarty.session.userdata.last_login.login_date!=''}}
				<p>
					{{assign var="date_formatted" value=$smarty.session.userdata.last_login.login_date|date_format:$smarty.session.userdata.full_date_format}}
					{{assign var="last_login_ip" value=$smarty.session.userdata.last_login.ip}}
					{{if $smarty.session.userdata.last_login.country_code}}
						{{query_kvs select="single" table="list_countries" where_language_code=$lang.system.language_code where_country_code=$smarty.session.userdata.last_login.country_code assign="country_record"}}
						{{assign var="last_login_ip" value="`$last_login_ip` (`$country_record.title`)"}}
						{{if $last_login_warning==1}}
							{{assign var="last_login_ip" value="`$last_login_ip` <span class=\"highlighted_text\"><i class=\"icon icon-type-alert\"></i></span>"|smarty:nodefaults}}
						{{/if}}
					{{/if}}
					{{$lang.start.last_logon_details|replace:"%1%":$date_formatted}} <strong>{{$last_login_ip|smarty:nodefaults}}</strong>
					[{{strip}}
					{{if $smarty.session.userdata.last_login.duration>60}}
						{{assign var="duration" value=$smarty.session.userdata.last_login.duration/60}}
						{{if $duration>60}}
							{{assign var="duration" value=$duration/60}}
							{{if $duration>24}}
									{{assign var="duration" value=$duration/24}}
									{{$duration|string_format:"%.1f"}} {{$lang.common.day_truncated}}
							{{else}}
									{{$duration|string_format:"%.1f"}} {{$lang.common.hour_truncated}}
							{{/if}}
						{{else}}
								{{$duration|string_format:"%.1f"}} {{$lang.common.minute_truncated}}
						{{/if}}
					{{else}}
							{{assign var="duration" value=$smarty.session.userdata.last_login.duration}}
							{{$duration|string_format:"%.1f"}} {{$lang.common.second_truncated}}
					{{/if}}
					{{/strip}}]
					{{if in_array('start|view',$smarty.session.permissions)}}
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/80-how-to-hide-access-to-admin-panel/">How to hide access to your admin panel</a></span>
						<span class="de_hint de_forum_link">Forum: <a href="https://forum.kernel-video-sharing.com/topic/156-how-to-build-your-tube-empire-with-kvs/">How to build your tube empire with KVS</a></span>
					{{/if}}
				</p>
			{{/if}}
			{{if in_array('system|administration',$smarty.session.permissions)}}
				<form method="post" action="start.php" class="de">
					<p id="kvs_support">
						<label>{{$lang.start.kvs_support_state}}:</label>
						{{if $options.ENABLE_KVS_SUPPORT_ACCESS==1}}
							<span class="enabled">{{$lang.start.kvs_support_state_enabled}}</span>
							<input type="hidden" name="action" value="disable_kvs_support"/>
							<input type="submit" value="{{$lang.start.kvs_support_state_btn_disable}}"/>
						{{else}}
							<span class="disabled">{{$lang.start.kvs_support_state_disabled}}</span>
							<input type="hidden" name="action" value="enable_kvs_support"/>
							<input type="submit" value="{{$lang.start.kvs_support_state_btn_enable}}"/>
						{{/if}}
					</p>
				</form>
			{{/if}}
		</div>
		<div id="system_info">
			<h2>
				<a href="{{if $config.player_lrc!=''}}{{$config.project_url}}{{else}}https://www.kernel-video-sharing.com{{/if}}">{{$lang.start.software|replace:"%1%":$config.project_title|replace:"%2%":$config.project_version}}</a>
			</h2>
			<p>
				{{if $config.installation_type==1}}
					{{$lang.start.license_domain_basic}}
				{{elseif $config.installation_type==2}}
					{{$lang.start.license_domain_advanced}}
				{{elseif $config.installation_type==3}}
					{{$lang.start.license_domain_premium}}
				{{elseif $config.installation_type==4}}
					{{$lang.start.license_domain_ultimate}}
				{{/if}}
			</p>
			{{if $config.is_clone_db=="true"}}
				<p>
					{{$lang.start.satellite_domain|replace:"%1%":$options.SYSTEM_DOMAIN}}
				</p>
			{{/if}}
			{{if in_array('start|view',$smarty.session.permissions)}}
				{{if $license_payment_date>0}}
					<p>
						{{if $license_payment_date<$smarty.now+1*86400}}
							{{assign var="license_payment_date" value=$license_payment_date|date_format:$smarty.session.userdata.short_date_format}}
							<span class="highlighted_text">{{$lang.start.license_payment_date|replace:"%1%":$license_payment_date}}</span>
						{{elseif $license_payment_date<$smarty.now+3*86400}}
							{{assign var="license_payment_date" value=$license_payment_date|date_format:$smarty.session.userdata.short_date_format}}
							<span class="warning_text">{{$lang.start.license_payment_date|replace:"%1%":$license_payment_date}}</span>
						{{else}}
							{{assign var="license_payment_date" value=$license_payment_date|date_format:$smarty.session.userdata.short_date_format}}
							{{$lang.start.license_payment_date|replace:"%1%":$license_payment_date}}
						{{/if}}
					</p>
				{{/if}}
				<p>
					{{$lang.start.update}}:
					{{if $new_version!=''}}
						{{if in_array('plugins|kvs_update',$smarty.session.permissions)}}
							<a href="plugins.php?plugin_id=kvs_update"><b>{{$lang.start.update_version|replace:"%1%":$new_version}}</b></a>
						{{else}}
							<b>{{$lang.start.update_version|replace:"%1%":$new_version}}</b>
						{{/if}}
					{{else}}
						<span class="disabled">{{$lang.start.update_na}}</span>
					{{/if}}
				</p>
			{{/if}}
		</div>
	</div>
	{{if in_array('start|view',$smarty.session.permissions)}}
		{{if count($news)>0}}
			<div id="news">
				<h1 data-accordeon="start_news">
					{{assign var="last_update" value=$lang.common.undefined}}
					{{assign var="has_new_news" value=0}}
					{{if count($news)>0}}
						{{assign var="last_update" value=$news[0].post_date|date_format:$smarty.session.userdata.short_date_format}}
						{{assign var="last_update_time" value=$news[0].post_date|strtotime}}
						{{if $smarty.now-$last_update_time<5*86400}}
							{{assign var="has_new_news" value=1}}
						{{/if}}
					{{/if}}
					<i class="icon icon-type-feed"></i> {{$lang.start.news|replace:"%1%":$last_update}} {{if $has_new_news==1}}<span class="highlighted_text">NEW!</span>{{/if}}
				</h1>
				<table id="start_news">
					{{foreach item="item" from=$news|smarty:nodefaults name="news"}}
						{{if $smarty.foreach.news.iteration<=5}}
							<tr>
								<td class="nowrap">
									<span>{{$item.post_date|date_format:$smarty.session.userdata.short_date_format}}</span>
									{{if $item.is_new==1}}
										<br/><span class="highlighted_text">NEW!</span>
									{{/if}}
								</td>
								<td>
									<p>
										{{$item.$news_text_key}} <a class="small_link" href="https://www.kernel-video-sharing.com/{{$lang.system.language_code}}/news/{{$item.news_id}}/" target="_blank">{{$lang.start.news_more}}</a>
									</p>
								</td>
							</tr>
						{{/if}}
					{{/foreach}}
				</table>
			</div>
		{{/if}}
		<div id="stats">
			<h1 data-accordeon="start_summary">
				<i class="icon icon-type-traffic"></i>{{$lang.start.stats}}
				{{if count($stats.satellites)>0}}
					<div class="drop">
						{{if $stats.displaying_satellite.multi_prefix}}
							<span class="filtered">{{$stats.displaying_satellite.project_url}}</span>
						{{else}}
							<span>{{$lang.start.stats_satellite}}</span>
						{{/if}}
						<ul>
							<li><a href="start.php">{{$lang.start.stats_satellite_main}}</a></li>
							{{foreach from=$stats.satellites item="satellite"}}
								<li><a href="start.php?satellite_prefix={{$satellite.multi_prefix}}">{{$satellite.project_url}}</a></li>
							{{/foreach}}
						</ul>
					</div>
				{{/if}}
			</h1>
			<div id="start_summary">
				<div id="group1">
					{{if is_array($stats.daily_updates)}}
						<div class="stats_group">
							<h2 data-accordeon="start_daily_updates">{{$lang.start.stats_content_added}}</h2>
							{{if count($stats.daily_updates)>0}}
								<table id="start_daily_updates">
									{{foreach item="item" key="key" from=$stats.daily_updates|smarty:nodefaults}}
									<tr>
										<td>{{$key|date_format:$smarty.session.userdata.short_date_format}}</td>
										<td>{{$item.videos|default:0}} / {{$item.albums|default:0}} / {{$item.posts|default:0}}</td>
									</tr>
									{{/foreach}}
								</table>
							{{/if}}
						</div>
					{{/if}}
					<div class="stats_group">
						<h2 data-accordeon="start_content">{{$lang.start.stats_content}}</h2>
						<table id="start_content" >
							<tr>
								<td>
									{{if in_array('videos|view',$smarty.session.permissions) && $stats.total_premium_videos>0}}
										<a href="videos.php?no_filter=true&amp;se_is_private=2">{{$lang.start.stats_content_premium_videos}}</a>
									{{else}}
										{{$lang.start.stats_content_premium_videos}}
									{{/if}}
								</td>
								<td>{{$stats.total_premium_videos}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('videos|view',$smarty.session.permissions) && $stats.total_active_videos>0}}
										<a href="videos.php?no_filter=true&amp;se_status_id=1">{{$lang.start.stats_content_active_videos}}</a>
									{{else}}
										{{$lang.start.stats_content_active_videos}}
									{{/if}}
								</td>
								<td>{{$stats.total_active_videos}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('videos|view',$smarty.session.permissions) && $stats.total_disabled_videos>0}}
										<a href="videos.php?no_filter=true&amp;se_status_id=0">{{$lang.start.stats_content_disabled_videos}}</a>
									{{else}}
										{{$lang.start.stats_content_disabled_videos}}
									{{/if}}
								</td>
								<td>{{$stats.total_disabled_videos}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('videos|view',$smarty.session.permissions) && $stats.total_deleted_videos>0}}
										<a href="videos.php?no_filter=true&amp;se_status_id=5">{{$lang.start.stats_content_deleted_videos}}</a>
									{{else}}
										{{$lang.start.stats_content_deleted_videos}}
									{{/if}}
								</td>
								<td>{{$stats.total_deleted_videos}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('videos|view',$smarty.session.permissions) && $stats.total_error_videos>0}}
										<a href="videos.php?no_filter=true&amp;se_status_id=2">{{$lang.start.stats_content_error_videos}}</a>
									{{else}}
										{{$lang.start.stats_content_error_videos}}
									{{/if}}
								</td>
								<td {{if $stats.total_error_videos>0}}class="highlighted_text"{{/if}}>{{$stats.total_error_videos}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('albums|view',$smarty.session.permissions) && $stats.total_premium_albums>0}}
										<a href="albums.php?no_filter=true&amp;se_is_private=2">{{$lang.start.stats_content_premium_albums}}</a>
									{{else}}
										{{$lang.start.stats_content_premium_albums}}
									{{/if}}
								</td>
								<td>{{$stats.total_premium_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('albums|view',$smarty.session.permissions) && $stats.total_active_albums>0}}
										<a href="albums.php?no_filter=true&amp;se_status_id=1">{{$lang.start.stats_content_active_albums}}</a>
									{{else}}
										{{$lang.start.stats_content_active_albums}}
									{{/if}}
								</td>
								<td>{{$stats.total_active_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('albums|view',$smarty.session.permissions) && $stats.total_disabled_albums>0}}
										<a href="albums.php?no_filter=true&amp;se_status_id=0">{{$lang.start.stats_content_disabled_albums}}</a>
									{{else}}
										{{$lang.start.stats_content_disabled_albums}}
									{{/if}}
								</td>
								<td>{{$stats.total_disabled_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('albums|view',$smarty.session.permissions) && $stats.total_deleted_albums>0}}
										<a href="albums.php?no_filter=true&amp;se_status_id=5">{{$lang.start.stats_content_deleted_albums}}</a>
									{{else}}
										{{$lang.start.stats_content_deleted_albums}}
									{{/if}}
								</td>
								<td>{{$stats.total_deleted_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('albums|view',$smarty.session.permissions) && $stats.total_error_albums>0}}
										<a href="albums.php?no_filter=true&amp;se_status_id=2">{{$lang.start.stats_content_error_albums}}</a>
									{{else}}
										{{$lang.start.stats_content_error_albums}}
									{{/if}}
								</td>
								<td {{if $stats.total_error_albums>0}}class="highlighted_text"{{/if}}>{{$stats.total_error_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('posts|view',$smarty.session.permissions) && $stats.total_active_posts>0}}
										<a href="posts.php?no_filter=true&amp;se_status_id=1">{{$lang.start.stats_content_active_posts}}</a>
									{{else}}
										{{$lang.start.stats_content_active_posts}}
									{{/if}}
								</td>
								<td>{{$stats.total_active_posts}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('posts|view',$smarty.session.permissions) && $stats.total_disabled_posts>0}}
										<a href="posts.php?no_filter=true&amp;se_status_id=0">{{$lang.start.stats_content_disabled_posts}}</a>
									{{else}}
										{{$lang.start.stats_content_disabled_posts}}
									{{/if}}
								</td>
								<td>{{$stats.total_disabled_posts}}</td>
							</tr>
						</table>
						<h2 data-accordeon="start_categorization_stats">{{$lang.start.stats_categorization}}</h2>
						<table id="start_categorization_stats" >
							<tr>
								<td>
									{{if in_array('content_sources|view',$smarty.session.permissions) && $stats.total_content_sources>0}}
										<a href="content_sources.php?no_filter=true">{{$lang.start.stats_categorization_content_sources}}</a>
									{{else}}
										{{$lang.start.stats_categorization_content_sources}}
									{{/if}}
								</td>
								<td>{{$stats.total_content_sources}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('categories|view',$smarty.session.permissions) && $stats.total_categories>0}}
										<a href="categories.php?no_filter=true">{{$lang.start.stats_categorization_categories}}</a>
									{{else}}
										{{$lang.start.stats_categorization_categories}}
									{{/if}}
									/
									{{if in_array('tags|view',$smarty.session.permissions) && $stats.total_tags>0}}
										<a href="tags.php?no_filter=true">{{$lang.start.stats_categorization_tags}}</a>
									{{else}}
										{{$lang.start.stats_categorization_tags}}
									{{/if}}
								</td>
								<td>{{$stats.total_categories}} / {{$stats.total_tags}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('models|view',$smarty.session.permissions) && $stats.total_models>0}}
										<a href="models.php?no_filter=true">{{$lang.start.stats_categorization_models}}</a>
									{{else}}
										{{$lang.start.stats_categorization_models}}
									{{/if}}
								</td>
								<td>{{$stats.total_models}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('dvds|view',$smarty.session.permissions) && $stats.total_dvds>0}}
										<a href="dvds.php?no_filter=true">{{$lang.start.stats_categorization_dvds}}</a>
									{{else}}
										{{$lang.start.stats_categorization_dvds}}
									{{/if}}
								</td>
								<td>{{$stats.total_dvds}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('dvds_groups|view',$smarty.session.permissions) && $stats.total_dvds_groups>0}}
										<a href="dvds_groups.php?no_filter=true">{{$lang.start.stats_categorization_dvds_groups}}</a>
									{{else}}
										{{$lang.start.stats_categorization_dvds_groups}}
									{{/if}}
								</td>
								<td>{{$stats.total_dvds_groups}}</td>
							</tr>
						</table>
						{{if count($stats.storage_servers)>0}}
							<h2 data-accordeon="start_storage_system">{{$lang.start.stats_storage_servers}}</h2>
							<table id="start_storage_system">
								<tr>
									<td>{{$lang.start.stats_storage_servers_primary}}</td>
									<td>{{$smarty.session.server_free_space}} ({{$smarty.session.server_free_space_pc|intval}}%)</td>
								</tr>
								{{foreach item="item" from=$stats.storage_servers|smarty:nodefaults}}
									<tr>
										<td>{{$item.title}}</td>
										<td>{{$item.free_space}} ({{$item.total_space_pc|intval}}%)</td>
									</tr>
								{{/foreach}}
							</table>
						{{/if}}
					</div>
				</div>
				<div id="group2">
					<div class="stats_group">
						<h2 data-accordeon="start_members_totals">{{$lang.start.stats_members_totals}}</h2>
						<table id="start_members_totals">
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_users>0}}
										<a href="users.php?no_filter=true">{{$lang.start.stats_members_totals_users}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_users}}
									{{/if}}
								</td>
								<td>{{$stats.total_users}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_generated_users>0}}
										<a href="users.php?no_filter=true&amp;se_status_id=5">{{$lang.start.stats_members_totals_generated_users}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_generated_users}}
									{{/if}}
								</td>
								<td>{{$stats.total_generated_users}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_nonconfirmed_users>0}}
										<a href="users.php?no_filter=true&amp;se_status_id=1">{{$lang.start.stats_members_totals_nonconfirmed_users}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_nonconfirmed_users}}
									{{/if}}
								</td>
								<td>{{$stats.total_nonconfirmed_users}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_disabled_users>0}}
										<a href="users.php?no_filter=true&amp;se_status_id=0">{{$lang.start.stats_members_totals_disabled_users}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_disabled_users}}
									{{/if}}
								</td>
								<td>{{$stats.total_disabled_users}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_premium_users>0}}
										<a href="users.php?no_filter=true&amp;se_status_id=3">{{$lang.start.stats_members_totals_premium_users}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_premium_users}}
									{{/if}}
								</td>
								<td>{{$stats.total_premium_users}}</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_members_totals_videos_bookmarks}}</td>
								<td>{{$stats.total_bookmarks_videos}}</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_members_totals_albums_bookmarks}}</td>
								<td>{{$stats.total_bookmarks_albums}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_comments>0}}
										<a href="comments.php?no_filter=true">{{$lang.start.stats_members_totals_comments}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_comments}}
									{{/if}}
								</td>
								<td>
									{{$stats.total_comments}}
									{{if $stats.total_comments_for_review>0}}
										/
										{{if in_array('users|view',$smarty.session.permissions) && in_array('videos|view',$smarty.session.permissions)}}
											<a href="comments.php?no_filter=true&amp;se_status_id=1">+{{$stats.total_comments_for_review}}</a>
										{{else}}
											+{{$stats.total_comments_for_review}}
										{{/if}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									{{if in_array('playlists|view',$smarty.session.permissions) && $stats.total_playlists>0}}
										<a href="playlists.php?no_filter=true">{{$lang.start.stats_members_totals_playlists}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_playlists}}
									{{/if}}
								</td>
								<td>
									{{$stats.total_playlists}}
									{{if $stats.total_playlists_for_review>0}}
										/
										{{if in_array('playlists|view',$smarty.session.permissions)}}
											<a href="playlists.php?no_filter=true&amp;se_review_flag=1">+{{$stats.total_playlists_for_review}}</a>
										{{else}}
											+{{$stats.total_playlists_for_review}}
										{{/if}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_members_totals_friends}}</td>
								<td>{{$stats.total_friends}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_users_blogs>0}}
										<a href="users_blogs.php?no_filter=true">{{$lang.start.stats_members_totals_blog_entries}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_blog_entries}}
									{{/if}}
								</td>
								<td>
									{{$stats.total_users_blogs}}
									{{if $stats.total_users_blogs_for_review>0}}
										/
										{{if in_array('users|view',$smarty.session.permissions)}}
											<a href="users_blogs.php?no_filter=true&amp;se_status_id=1">+{{$stats.total_users_blogs_for_review}}</a>
										{{else}}
											+{{$stats.total_users_blogs_for_review}}
										{{/if}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									{{if in_array('messages|view',$smarty.session.permissions) && $stats.total_messages>0}}
										<a href="messages.php?no_filter=true">{{$lang.start.stats_members_totals_internal_messages}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_internal_messages}}
									{{/if}}
								</td>
								<td>{{$stats.total_messages}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('messages|view',$smarty.session.permissions) && $stats.spam_messages>0}}
										<a href="messages.php?no_filter=true&amp;se_is_spam=1">{{$lang.start.stats_members_totals_spam_messages}}</a>
									{{else}}
										{{$lang.start.stats_members_totals_spam_messages}}
									{{/if}}
								</td>
								<td {{if $stats.spam_messages>0}}class="highlighted_text"{{/if}}>{{$stats.spam_messages}}</td>
							</tr>
							{{if $stats.user_sess_avg_duration!=''}}
								<tr>
									<td>
										{{$lang.start.stats_members_totals_avg_sess_duration}}
									</td>
									<td>{{$stats.user_sess_avg_duration}}</td>
								</tr>
							{{/if}}
						</table>
						<h2 data-accordeon="start_members_activity">{{$lang.start.stats_activity_indicator}}</h2>
						<table id="start_members_activity">
							<tr>
								<td>{{$lang.start.stats_activity_indicator_new}}</td>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_new_users_week>0}}
										<a href="users.php?no_filter=true&amp;se_activity=new_week">{{$stats.total_new_users_week}}</a>
									{{else}}
										{{$stats.total_new_users_week}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_new_users_month>0}}
										<a href="users.php?no_filter=true&amp;se_activity=new_month">{{$stats.total_new_users_month}}</a>
									{{else}}
										{{$stats.total_new_users_month}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_new_users_year>0}}
										<a href="users.php?no_filter=true&amp;se_activity=new_year">{{$stats.total_new_users_year}}</a>
									{{else}}
										{{$stats.total_new_users_year}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_activity_indicator_active}}</td>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_active_users_week>0}}
										<a href="users.php?no_filter=true&amp;se_activity=have/logins_week">{{$stats.total_active_users_week}}</a>
									{{else}}
										{{$stats.total_active_users_week}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_active_users_month>0}}
										<a href="users.php?no_filter=true&amp;se_activity=have/logins_month">{{$stats.total_active_users_month}}</a>
									{{else}}
										{{$stats.total_active_users_month}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_active_users_year>0}}
										<a href="users.php?no_filter=true&amp;se_activity=have/logins_year">{{$stats.total_active_users_year}}</a>
									{{else}}
										{{$stats.total_active_users_year}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_activity_indicator_inactive}}</td>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_not_active_users_week>0}}
										<a href="users.php?no_filter=true&amp;se_activity=no/logins_week">{{$stats.total_not_active_users_week}}</a>
									{{else}}
										{{$stats.total_not_active_users_week}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_not_active_users_month>0}}
										<a href="users.php?no_filter=true&amp;se_activity=no/logins_month">{{$stats.total_not_active_users_month}}</a>
									{{else}}
										{{$stats.total_not_active_users_month}}
									{{/if}}
									/
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_not_active_users_year>0}}
										<a href="users.php?no_filter=true&amp;se_activity=no/logins_year">{{$stats.total_not_active_users_year}}</a>
									{{else}}
										{{$stats.total_not_active_users_year}}
									{{/if}}
								</td>
							</tr>
						</table>
						<h2 data-accordeon="start_members_bans">{{$lang.start.stats_banned_users}}</h2>
						<table id="start_members_bans">
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_temporary_banned_users>0}}
										<a href="users.php?no_filter=true&amp;se_banned_status=1">{{$lang.start.stats_banned_users_temporary}}</a>
									{{else}}
										{{$lang.start.stats_banned_users_temporary}}
									{{/if}}
								</td>
								<td>{{$stats.total_temporary_banned_users}}</td>
							</tr>
							<tr>
								<td>
									{{if in_array('users|view',$smarty.session.permissions) && $stats.total_forever_banned_users>0}}
										<a href="users.php?no_filter=true&amp;se_banned_status=2">{{$lang.start.stats_banned_users_forever}}</a>
									{{else}}
										{{$lang.start.stats_banned_users_forever}}
									{{/if}}
								</td>
								<td>{{$stats.total_forever_banned_users}}</td>
							</tr>
						</table>
					</div>
					<div class="stats_group">
						<h2 data-accordeon="start_stats_traffic">{{$lang.start.stats_traffic}}</h2>
						<table id="start_stats_traffic">
							<tr>
								<td>{{$lang.start.stats_today}}</td>
								<td>
									{{$stats.in_today_uniq|traffic_format}} /
									{{$stats.in_today_total|traffic_format}} /
									{{$stats.in_today_content|traffic_format}} /
									{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) && $stats.in_today_errors>0}}
										<a href="stats_overload.php?no_filter=true">{{$stats.in_today_errors|traffic_format}}</a>
									{{else}}
										{{$stats.in_today_errors|traffic_format}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_yesterday}}</td>
								<td>
									{{$stats.in_yesterday_uniq|traffic_format}} /
									{{$stats.in_yesterday_total|traffic_format}} /
									{{$stats.in_yesterday_content|traffic_format}} /
									{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) && $stats.in_yesterday_errors>0}}
										<a href="stats_overload.php?no_filter=true">{{$stats.in_yesterday_errors|traffic_format}}</a>
									{{else}}
										{{$stats.in_yesterday_errors|traffic_format}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_7_days}}</td>
								<td>
									{{$stats.in_week_uniq|traffic_format}} /
									{{$stats.in_week_total|traffic_format}} /
									{{$stats.in_week_content|traffic_format}} /
									{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) && $stats.in_week_errors>0}}
										<a href="stats_overload.php?no_filter=true">{{$stats.in_week_errors|traffic_format}}</a>
									{{else}}
										{{$stats.in_week_errors|traffic_format}}
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_30_days}}</td>
								<td>
									{{$stats.in_month_uniq|traffic_format}} /
									{{$stats.in_month_total|traffic_format}} /
									{{$stats.in_month_content|traffic_format}} /
									{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) && $stats.in_month_errors>0}}
										<a href="stats_overload.php?no_filter=true">{{$stats.in_month_errors|traffic_format}}</a>
									{{else}}
										{{$stats.in_month_errors|traffic_format}}
									{{/if}}
								</td>
							</tr>
						</table>
						{{if count($stats.satellites)>0}}
							<h2 data-accordeon="start_stats_satellites">{{$lang.start.stats_satellites}}</h2>
							<table id="start_stats_satellites">
								<tr>
									<td>{{$lang.start.stats_today}}</td>
									<td>
										{{$stats.satellites_today_uniq|traffic_format}} /
										{{$stats.satellites_today_total|traffic_format}} /
										{{$stats.satellites_today_content|traffic_format}} /
										{{$stats.satellites_today_errors|traffic_format}}
									</td>
								</tr>
								<tr>
									<td>{{$lang.start.stats_yesterday}}</td>
									<td>
										{{$stats.satellites_yesterday_uniq|traffic_format}} /
										{{$stats.satellites_yesterday_total|traffic_format}} /
										{{$stats.satellites_yesterday_content|traffic_format}} /
										{{$stats.satellites_yesterday_errors|traffic_format}}
									</td>
								</tr>
								<tr>
									<td>{{$lang.start.stats_last_7_days}}</td>
									<td>
										{{$stats.satellites_week_uniq|traffic_format}} /
										{{$stats.satellites_week_total|traffic_format}} /
										{{$stats.satellites_week_content|traffic_format}} /
										{{$stats.satellites_week_errors|traffic_format}}
									</td>
								</tr>
								<tr>
									<td>{{$lang.start.stats_last_30_days}}</td>
									<td>
										{{$stats.satellites_month_uniq|traffic_format}} /
										{{$stats.satellites_month_total|traffic_format}} /
										{{$stats.satellites_month_content|traffic_format}} /
										{{$stats.satellites_month_errors|traffic_format}}
									</td>
								</tr>
							</table>
						{{/if}}
						<h2 data-accordeon="start_stats_bookmarks">{{$lang.start.stats_bookmarks}}</h2>
						<table id="start_stats_bookmarks">
							<tr>
								<td>{{$lang.start.stats_today}}</td>
								<td>
									{{$stats.bookmarks_today|traffic_format}} / {{$stats.bookmarks_today_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_yesterday}}</td>
								<td>
									{{$stats.bookmarks_yesterday|traffic_format}} / {{$stats.bookmarks_yesterday_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_7_days}}</td>
								<td>
									{{$stats.bookmarks_week|traffic_format}} / {{$stats.bookmarks_week_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_30_days}}</td>
								<td>
									{{$stats.bookmarks_month|traffic_format}} / {{$stats.bookmarks_month_pc}}%
								</td>
							</tr>
						</table>
						<h2 data-accordeon="start_stats_outs">{{$lang.start.stats_outs}}</h2>
						<table id="start_stats_outs">
							<tr>
								<td>{{$lang.start.stats_today}}</td>
								<td>
									{{$stats.out_today|traffic_format}} / {{$stats.out_today_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_yesterday}}</td>
								<td>
									{{$stats.out_yesterday|traffic_format}} / {{$stats.out_yesterday_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_7_days}}</td>
								<td>
									{{$stats.out_week|traffic_format}} / {{$stats.out_week_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_30_days}}</td>
								<td>
									{{$stats.out_month|traffic_format}} / {{$stats.out_month_pc}}%
								</td>
							</tr>
						</table>
						<h2 data-accordeon="start_stats_embeds">{{$lang.start.stats_embeds}}</h2>
						<table id="start_stats_embeds">
							<tr>
								<td>{{$lang.start.stats_today}}</td>
								<td>
									{{$stats.embed_today|traffic_format}} / {{$stats.embed_today_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_yesterday}}</td>
								<td>
									{{$stats.embed_yesterday|traffic_format}} / {{$stats.embed_yesterday_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_7_days}}</td>
								<td>
									{{$stats.embed_week|traffic_format}} / {{$stats.embed_week_pc}}%
								</td>
							</tr>
							<tr>
								<td>{{$lang.start.stats_last_30_days}}</td>
								<td>
									{{$stats.embed_month|traffic_format}} / {{$stats.embed_month_pc}}%
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div id="important">
			{{if $errors|@count>0}}
				<div id="errors">
					<h1 data-accordeon="start_errors"><i class="icon icon-type-alert"></i>{{$lang.start.errors}} ({{$errors|@count}})</h1>
					<div id="start_errors">
						{{foreach item="item" from=$errors|smarty:nodefaults}}
							<p>
								{{if $item.href && (!$item.permission || in_array($item.permission, $smarty.session.permissions))}}
									<a href="{{$item.href}}">{{$item.message}}</a>
								{{else}}
									{{$item.message}}
								{{/if}}
							</p>
						{{/foreach}}
					</div>
				</div>
			{{/if}}
			<div id="alerts">
				<h1 data-accordeon="start_alerts"><i class="icon icon-type-signal"></i>{{$lang.start.alerts}} ({{$stats.total_alerts}})</h1>
				<div id="start_alerts">
					{{if count($admin_panel_alerts)>0}}
						<h2 data-accordeon="start_alerts_admin_panel">{{$lang.start.alerts_group_admin_panel}} ({{$admin_panel_alerts|@count}})</h2>
						<div id="start_alerts_admin_panel">
							{{foreach item="item" from=$admin_panel_alerts|smarty:nodefaults}}
								<p>
									{{if $item.href}}
										<a href="{{$item.href}}">{{$item.message}}</a>
									{{else}}
										{{$item.message}}
									{{/if}}
								</p>
							{{/foreach}}
						</div>
					{{/if}}
					{{if $stats.total_feedback_alerts>0}}
						<h2 data-accordeon="start_alerts_feedback">{{$lang.start.alerts_group_feedback}} ({{$stats.total_feedback_alerts}})</h2>
						<div id="start_alerts_feedback">
							{{if $stats.flags_messages>0}}
								<p>
									{{if in_array('feedbacks|view',$smarty.session.permissions)}}
										<a href="flags_messages.php?no_filter=true">{{$lang.start.alerts_flags_messages|replace:"%1%":$stats.flags_messages}}</a>
									{{else}}
										{{$lang.start.alerts_flags_messages|replace:"%1%":$stats.flags_messages}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_new_feedbacks>0}}
								<p>
									{{if in_array('feedbacks|view',$smarty.session.permissions)}}
										<a href="feedbacks.php?no_filter=true&amp;se_status_id=1">{{$lang.start.alerts_new_feedbacks|replace:"%1%":$stats.total_new_feedbacks}}</a>
									{{else}}
										{{$lang.start.alerts_new_feedbacks|replace:"%1%":$stats.total_new_feedbacks}}
									{{/if}}
								</p>
							{{/if}}
						</div>
					{{/if}}
					{{if count($flagged_alerts)>0}}
						{{assign var="total_flagged_alerts" value="0"}}
						{{foreach item="item" from=$flagged_alerts|smarty:nodefaults}}
							{{assign var="total_flagged_alerts" value=$total_flagged_alerts+$item.count}}
						{{/foreach}}
						<h2 data-accordeon="start_alerts_flags">{{$lang.start.alerts_group_flagged_objects}} ({{$total_flagged_alerts}})</h2>
						<div id="start_alerts_flags">
							{{foreach item="item" from=$flagged_alerts|smarty:nodefaults}}
								<p>
									{{if $item.flag_group_id==1}}
										{{if in_array('videos|view',$smarty.session.permissions)}}
											<a href="videos.php?no_filter=true&amp;se_flag_id={{$item.flag_id}}&amp;se_flag_values_amount={{$item.alert_min_count}}">{{$lang.start.alerts_flagged_videos|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}</a>
										{{else}}
											{{$lang.start.alerts_flagged_videos|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}
										{{/if}}
									{{elseif $item.flag_group_id==2}}
										{{if in_array('albums|view',$smarty.session.permissions)}}
											<a href="albums.php?no_filter=true&amp;se_flag_id={{$item.flag_id}}&amp;se_flag_values_amount={{$item.alert_min_count}}">{{$lang.start.alerts_flagged_albums|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}</a>
										{{else}}
											{{$lang.start.alerts_flagged_albums|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}
										{{/if}}
									{{elseif $item.flag_group_id==3}}
										{{if in_array('dvds|view',$smarty.session.permissions)}}
											<a href="dvds.php?no_filter=true&amp;se_flag_id={{$item.flag_id}}&amp;se_flag_values_amount={{$item.alert_min_count}}">{{$lang.start.alerts_flagged_dvds|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}</a>
										{{else}}
											{{$lang.start.alerts_flagged_dvds|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}
										{{/if}}
									{{elseif $item.flag_group_id==4}}
										{{if in_array('posts|view',$smarty.session.permissions)}}
											<a href="posts.php?no_filter=true&amp;se_flag_id={{$item.flag_id}}&amp;se_flag_values_amount={{$item.alert_min_count}}">{{$lang.start.alerts_flagged_posts|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}</a>
										{{else}}
											{{$lang.start.alerts_flagged_posts|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}
										{{/if}}
									{{elseif $item.flag_group_id==5}}
										{{if in_array('playlists|view',$smarty.session.permissions)}}
											<a href="playlists.php?no_filter=true&amp;se_flag_id={{$item.flag_id}}&amp;se_flag_values_amount={{$item.alert_min_count}}">{{$lang.start.alerts_flagged_playlists|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}</a>
										{{else}}
											{{$lang.start.alerts_flagged_playlists|replace:"%1%":$item.count|replace:"%2%":$item.flag_title}}
										{{/if}}
									{{/if}}
								</p>
							{{/foreach}}
						</div>
					{{/if}}
					{{if $stats.total_memberzone_alerts>0}}
						<h2 data-accordeon="start_alerts_memberzone">{{$lang.start.alerts_group_memberzone}} ({{$stats.total_memberzone_alerts}})</h2>
						<div id="start_alerts_memberzone">
							{{if $stats.profile_removal_requests>0}}
								<p class="memberzone_expander">
									{{if in_array('users|view',$smarty.session.permissions)}}
										<a href="users.php?no_filter=true&amp;se_is_removal_requested=1">{{$lang.start.alerts_account_removal_requests|replace:"%1%":$stats.profile_removal_requests}}</a>
									{{else}}
										{{$lang.start.alerts_account_removal_requests|replace:"%1%":$stats.profile_removal_requests}}
									{{/if}}
								</p>
							{{/if}}
						</div>
					{{/if}}
					{{if $stats.total_review_alerts>0}}
						<h2 data-accordeon="start_alerts_review">{{$lang.start.alerts_group_review}} ({{$stats.total_review_alerts}})</h2>
						<div id="start_alerts_review">
							{{if $stats.total_comments_for_review>0}}
								<p>
									{{if in_array('users|view',$smarty.session.permissions)}}
										<a href="comments.php?no_filter=true&amp;se_status_id=1">{{$lang.start.alerts_new_comments|replace:"%1%":$stats.total_comments_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_new_comments|replace:"%1%":$stats.total_comments_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_users_blogs_for_review>0}}
								<p>
									{{if in_array('users|view',$smarty.session.permissions)}}
										<a href="users_blogs.php?no_filter=true&amp;se_status_id=1">{{$lang.start.alerts_new_blog_entries|replace:"%1%":$stats.total_users_blogs_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_new_blog_entries|replace:"%1%":$stats.total_users_blogs_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_videos_for_review>0}}
								<p>
									{{if in_array('videos|view',$smarty.session.permissions)}}
										<a href="videos.php?no_filter=true&amp;se_review_flag=1">{{$lang.start.alerts_videos_for_review|replace:"%1%":$stats.total_videos_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_videos_for_review|replace:"%1%":$stats.total_videos_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_albums_for_review>0}}
								<p>
									{{if in_array('albums|view',$smarty.session.permissions)}}
										<a href="albums.php?no_filter=true&amp;se_review_flag=1">{{$lang.start.alerts_albums_for_review|replace:"%1%":$stats.total_albums_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_albums_for_review|replace:"%1%":$stats.total_albums_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_posts_for_review>0}}
								<p>
									{{if in_array('posts|view',$smarty.session.permissions)}}
										<a href="posts.php?no_filter=true&amp;se_review_flag=1">{{$lang.start.alerts_posts_for_review|replace:"%1%":$stats.total_posts_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_posts_for_review|replace:"%1%":$stats.total_posts_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_dvds_for_review>0}}
								<p>
									{{if in_array('dvds|view',$smarty.session.permissions)}}
										<a href="dvds.php?no_filter=true&amp;se_review_flag=1">{{$lang.start.alerts_dvds_for_review|replace:"%1%":$stats.total_dvds_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_dvds_for_review|replace:"%1%":$stats.total_dvds_for_review}}
									{{/if}}
								</p>
							{{/if}}
							{{if $stats.total_playlists_for_review>0}}
								<p>
									{{if in_array('playlists|view',$smarty.session.permissions)}}
										<a href="playlists.php?no_filter=true&amp;se_review_flag=1">{{$lang.start.alerts_playlists_for_review|replace:"%1%":$stats.total_playlists_for_review}}</a>
									{{else}}
										{{$lang.start.alerts_playlists_for_review|replace:"%1%":$stats.total_playlists_for_review}}
									{{/if}}
								</p>
							{{/if}}
						</div>
					{{/if}}
					{{if $stats.total_alerts==0}}
						<p>
							{{$lang.start.alerts_none}}
						</p>
					{{/if}}
				</div>
			</div>
		</div>
	{{/if}}
</div>