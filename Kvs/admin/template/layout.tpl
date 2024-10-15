<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{$lang.system.language_code}}" {{if $smarty.session.userdata.night_mode==1}}class="dark"{{/if}}>
<head>
	<title>{{$page_title}} / {{$config.project_version}}</title>
	<link type="text/css" rel="stylesheet" href="styles/{{$smarty.session.userdata.skin}}.css?v={{$version_hash}}"/>
	{{if $smarty.session.userdata.custom_css}}
		<style>
			{{$smarty.session.userdata.custom_css|replace:'&#34;':'"'|replace:'&gt;':'>'}}
		</style>
	{{/if}}

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>

	{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1' || $smarty.session.userdata.is_wysiwyg_enabled_albums=='1' || $smarty.session.userdata.is_wysiwyg_enabled_posts=='1' || $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}
		<script type="text/javascript" data-cfasync="false" src="tinymce/tinymce.min.js"></script>
		<script type="text/javascript" data-cfasync="false" src="js/TinyMCEConfig.js"></script>
	{{/if}}
	<script type="text/javascript" data-cfasync="false" src="js/config.php?v={{$version_hash}}"></script>
	<script type="text/javascript" data-cfasync="false" src="js/admin.js?v={{$version_hash}}"></script>
</head>
<body class="{{if $smarty.session.save.options.scrolling_mode=='old'}}old-scrolling{{/if}} {{if $smarty.session.userdata.is_expert_mode==1}}expert-mode{{/if}} {{if $smarty.session.userdata.is_hide_forum_hints==1}}hide-forum-links{{/if}} {{if $smarty.session.userdata.is_superadmin==2 || $smarty.session.save.options.is_syntax_highlight_disabled==1}}disable-syntax-highlight{{/if}} {{if $smarty.get.tab=='true'}}editor-tabs{{/if}}">
	<div id="content">
		<div id="layout_root">
			<div id="layout_main_main">
				<div id="header"><div id="header_inner">
					<div id="server_info">
						<span class="inner">
							<span class="values">
								<span class="elem sitename">
									<label>{{$lang.common.website}}:</label>
									<a class="value" href="{{$smarty.session.admin_panel_project_url|default:$config.project_url}}">{{$config.project_url}}</a>
								</span>
								<span class="elem">
									<i class="icon icon-clock"></i>
									<label>{{$lang.common.server_time}}:</label>
									<span class="value" data-time-now="{{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}">{{$smarty.session.server_time|date_format:$smarty.session.userdata.full_date_format}}</span>
								</span>
								{{if in_array('system|administration',$smarty.session.permissions)}}
									<span class="elem {{if $smarty.session.server_la>10}}has-issues{{/if}}">
										<i class="icon icon-load"></i>
										<label>{{$lang.common.server_la}}:</label>
										<span class="value">{{$smarty.session.server_la|number_format:2}}</span>
									</span>
									<span class="elem {{if $smarty.session.server_free_space_alert==1}}has-issues{{/if}}">
										<i class="icon icon-disk"></i>
										<label>{{$lang.common.server_free_space}}:</label>
										<span class="value">{{$smarty.session.server_free_space}} ({{$smarty.session.server_free_space_pc|intval}}%)</span>
									</span>
								{{/if}}
								{{if in_array('system|background_tasks',$smarty.session.permissions)}}
									<span class="elem {{if $smarty.session.server_processes_error>0}}has-issues{{elseif $smarty.session.server_processes>0}}has-tasks{{/if}}">
										<i class="icon icon-tasks"></i>
										<label>{{$lang.common.server_processes}}:</label>
										{{if $smarty.session.server_processes>0}}
											<a class="value" href="background_tasks.php?no_filter=true">{{$smarty.session.server_processes}}</a>
										{{else}}
											<span class="value">{{$smarty.session.server_processes}}</span>
										{{/if}}
										{{if $smarty.session.server_processes_error>0}}
											(<a class="value" href="background_tasks.php?no_filter=true&amp;se_status_id=2">{{$smarty.session.server_processes_error}}</a>)
										{{/if}}
										{{if $smarty.session.server_processes_paused==1}}
											{{$lang.common.server_processes_paused}}
										{{/if}}
									</span>
								{{/if}}
							</span>
						</span>
						<span class="inner links">
							<a class="link-btn" href="documentation.php"><i class="icon icon-documentation"></i><span>{{$lang.common.documentation}}</span></a>
							<a class="link-btn" href="https://www.kernel-video-sharing.com/forum/"><i class="icon icon-forum"></i><span>{{$lang.common.forum}}</span></a>
							<a class="link-btn" href="https://www.kernel-scripts.com/support/"><i class="icon icon-support"></i><span>{{$lang.common.support}}</span></a>
						</span>
					</div>
					{{include file="ap_custom_header.tpl"}}
					<div id="user_info">
						<a href="options.php" class="admin">{{$smarty.session.userdata.login}}</a>
						<a href="logout.php" class="logout">
							<i class="icon icon-logout"></i>
							{{$lang.common.log_off}}
						</a>
					</div>
				</div></div>

				{{assign var="number_of_menu_items" value=2}}
				{{if in_array('videos|view',$smarty.session.permissions) || in_array('dvds|view',$smarty.session.permissions) || in_array('dvds_groups|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('albums|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('posts|view',$smarty.session.permissions) || in_array('posts_types|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('users|view',$smarty.session.permissions) || in_array('feedbacks|view',$smarty.session.permissions) || in_array('messages|view',$smarty.session.permissions) || in_array('playlists|view',$smarty.session.permissions) || in_array('billing|view',$smarty.session.permissions) || in_array('payouts|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) || in_array('stats|view_content_stats',$smarty.session.permissions) || in_array('stats|view_user_stats',$smarty.session.permissions) || in_array('stats|manage_referers',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('categories|view',$smarty.session.permissions) || in_array('category_groups|view',$smarty.session.permissions) || in_array('models|view',$smarty.session.permissions) || in_array('models_groups|view',$smarty.session.permissions) || in_array('tags|view',$smarty.session.permissions) || in_array('content_sources|view',$smarty.session.permissions) || in_array('content_sources_groups|view',$smarty.session.permissions) || in_array('flags|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('website_ui|view',$smarty.session.permissions) || in_array('advertising|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('plugins|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}
				{{if in_array('system|background_tasks',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions) || in_array('localization|view',$smarty.session.permissions)}}
					{{assign var="number_of_menu_items" value=$number_of_menu_items+1}}
				{{/if}}

				{{assign var="main_menu" value=""}}
				{{assign var="side_menu" value="no"}}
				{{if $page_name=='index.php'}}
					{{assign var="side_menu" value=""}}
				{{/if}}
				{{if $page_name=='start.php'}}
					{{assign var="main_menu" value="start"}}
				{{/if}}
				{{if $page_name=='videos.php' || $page_name=='videos_screenshots.php' || $page_name=='videos_screenshots_grabbing.php' || $page_name=='dvds.php' || $page_name=='dvds_groups.php' || $page_name=='videos_import.php' || $page_name=='videos_export.php' || $page_name=='videos_select.php' || $page_name=='videos_mass_edit.php' || $page_name=='videos_feeds_import.php' || $page_name=='videos_feeds_export.php'}}
					{{assign var="main_menu" value="videos"}}
					{{assign var="side_menu" value="videos"}}
				{{/if}}
				{{if $page_name=='albums.php' || $page_name=='albums_import.php' || $page_name=='albums_export.php' || $page_name=='albums_select.php' || $page_name=='albums_mass_edit.php'}}
					{{assign var="main_menu" value="albums"}}
					{{assign var="side_menu" value="albums"}}
				{{/if}}
				{{if $page_name=='posts.php' || $page_name=='posts_types.php' || $locked_post_type_id>0}}
					{{assign var="main_menu" value="posts"}}
					{{assign var="side_menu" value="posts"}}
				{{/if}}
				{{if $page_name=='users.php' || $page_name=='emailing.php' || $page_name=='comments.php' || $page_name=='feedbacks.php' || $page_name=='flags_messages.php' || $page_name=='users_blogs.php' || $page_name=='messages.php' || $page_name=='playlists.php' || $page_name=='card_bill_configurations.php' || $page_name=='bill_transactions.php' || $page_name=='payouts.php'}}
					{{assign var="main_menu" value="users"}}
					{{assign var="side_menu" value="users"}}
				{{/if}}
				{{if $page_name=='stats_in.php' || $page_name=='stats_country.php' || $page_name=='stats_out.php' || $page_name=='stats_player.php' || $page_name=='stats_referer.php' || $page_name=='stats_referers_list.php' || $page_name=='stats_search.php' || $page_name=='stats_embed.php' || $page_name=='stats_overload.php' || $page_name=='stats_videos.php' || $page_name=='stats_albums.php' || $page_name=='stats_transactions.php' || $page_name=='stats_users.php' || $page_name=='stats_users_logins.php' || $page_name=='stats_users_content.php' || $page_name=='stats_users_purchases.php' || $page_name=='stats_users_sellings.php' || $page_name=='stats_users_donations.php' || $page_name=='stats_users_awards.php' || $page_name=='stats_users_initial_transactions.php' || $page_name=='stats_cleanup.php'}}
					{{assign var="main_menu" value="stats"}}
					{{assign var="side_menu" value="stats"}}
				{{/if}}
				{{if $page_name=='categories.php' || $page_name=='categories_groups.php' || $page_name=='models.php' || $page_name=='models_groups.php' || $page_name=='tags.php' || $page_name=='content_sources.php' || $page_name=='content_sources_groups.php' || $page_name=='flags.php'}}
					{{assign var="main_menu" value="categorization"}}
					{{assign var="side_menu" value="categorization"}}
				{{/if}}
				{{if $page_name=='project_spots.php' || $page_name=='project_blocks.php' || $page_name=='project_pages_components.php' || $page_name=='project_pages_lang_files.php' || $page_name=='project_pages_lang_texts.php' || $page_name=='project_pages.php' || $page_name=='templates_search.php' || $page_name=='project_pages_global.php' || $page_name=='project_pages_history.php' || $page_name=='project_theme.php' || $page_name=='project_theme_install.php'}}
					{{assign var="main_menu" value="website_ui"}}
					{{assign var="side_menu" value="website_ui"}}
				{{/if}}
				{{if $page_name=='plugins.php'}}
					{{assign var="main_menu" value="plugins"}}
					{{assign var="side_menu" value="plugins"}}
				{{/if}}
				{{if $page_name=='admin_users.php' || $page_name=='admin_users_groups.php' || $page_name=='log_system.php' || $page_name=='log_logins.php' || $page_name=='log_audit.php' || $page_name=='log_bill.php' || $page_name=='log_feeds.php' || $page_name=='log_imports.php' || $page_name=='log_background_tasks.php' || $page_name=='background_tasks.php' || $page_name=='installation.php' || $page_name=='file_changes.php' || $page_name=='translations.php' || $page_name=='translations_summary.php'}}
					{{assign var="main_menu" value="administration"}}
					{{assign var="side_menu" value="administration"}}
				{{/if}}
				{{if $page_name=='formats_videos_basic.php' || $page_name=='formats_videos.php' || $page_name=='formats_screenshots.php' || $page_name=='formats_albums.php' || $page_name=='options.php' || $page_name=='player.php' || $page_name=='vast_profiles.php' || $page_name=='servers.php' || $page_name=='servers_test.php' || $page_name=='servers_conversion.php' || $page_name=='servers_conversion_basic.php' || $page_name=='languages.php'}}
					{{assign var="main_menu" value="options"}}
					{{assign var="side_menu" value="options"}}
				{{/if}}

				<div id="main_menu" {{if $smarty.session.save.options.main_menu_memory==1}}data-memory="true"{{/if}} class="{{if $smarty.session.save.options.main_menu_mode=='only_icons'}}no-text{{elseif $smarty.session.save.options.main_menu_mode=='only_text'}}no-icons{{/if}} number-{{$number_of_menu_items}}">
					<a href="start.php" data-menu="start" {{if $main_menu=='start'}}class="active"{{/if}}>
						<i class="icon icon-dashboard"></i>
						<span>{{$lang.main_menu.home}}</span>
					</a>

					{{if in_array('videos|view',$smarty.session.permissions) || in_array('dvds|view',$smarty.session.permissions) || in_array('dvds_groups|view',$smarty.session.permissions)}}
						{{if in_array('videos|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="videos.php"}}
						{{elseif in_array('dvds|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="dvds.php"}}
						{{elseif in_array('dvds_groups|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="dvds_groups.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="videos" {{if $main_menu=='videos'}}class="active"{{/if}}>
							<i class="icon icon-videos"></i>
							<span>{{$lang.main_menu.videos}}</span>
							{{if $smarty.session.admin_notifications.badges.videos.count>0}}<em title="{{$smarty.session.admin_notifications.badges.videos.title|trim}}">{{$smarty.session.admin_notifications.badges.videos.count}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php" data-menu="albums" {{if $main_menu=='albums'}}class="active"{{/if}}>
							<i class="icon icon-albums"></i>
							<span>{{$lang.main_menu.albums}}</span>
							{{if $smarty.session.admin_notifications.badges.albums>0}}<em>{{$smarty.session.admin_notifications.badges.albums}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('posts|view',$smarty.session.permissions) || in_array('posts_types|view',$smarty.session.permissions)}}
						{{if in_array('posts|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="posts.php"}}
						{{else}}
							{{assign var="menu_url" value="posts_types.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="posts" {{if $main_menu=='posts'}}class="active"{{/if}}>
							<i class="icon icon-posts"></i>
							<span>{{$lang.main_menu.posts}}</span>
							{{if $smarty.session.admin_notifications.badges.posts>0}}<em>{{$smarty.session.admin_notifications.badges.posts}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('users|view',$smarty.session.permissions) || in_array('feedbacks|view',$smarty.session.permissions) || in_array('messages|view',$smarty.session.permissions) || in_array('playlists|view',$smarty.session.permissions) || in_array('billing|view',$smarty.session.permissions) || in_array('payouts|view',$smarty.session.permissions)}}
						{{if in_array('users|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="users.php"}}
						{{elseif in_array('feedbacks|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="feedbacks.php"}}
						{{elseif in_array('messages|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="messages.php"}}
						{{elseif in_array('playlists|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="playlists.php"}}
						{{elseif in_array('payouts|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="payouts.php"}}
						{{else}}
							{{assign var="menu_url" value="card_bill_configurations.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="users" {{if $main_menu=='users'}}class="active"{{/if}}>
							<i class="icon icon-memberzone"></i>
							<span>{{$lang.main_menu.memberzone}}</span>
							{{if $smarty.session.admin_notifications.badges.memberzone.count>0}}<em title="{{$smarty.session.admin_notifications.badges.memberzone.title|trim}}">{{$smarty.session.admin_notifications.badges.memberzone.count}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) || in_array('stats|view_content_stats',$smarty.session.permissions) || in_array('stats|view_user_stats',$smarty.session.permissions) || in_array('stats|manage_referers',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
						{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
							{{assign var="menu_url" value="stats_in.php"}}
						{{elseif in_array('stats|view_content_stats',$smarty.session.permissions)}}
							{{assign var="menu_url" value="stats_videos.php"}}
						{{elseif in_array('stats|view_user_stats',$smarty.session.permissions)}}
							{{assign var="menu_url" value="stats_transactions.php"}}
						{{elseif in_array('stats|manage_referers',$smarty.session.permissions)}}
							{{assign var="menu_url" value="stats_referers_list.php"}}
						{{elseif in_array('system|administration',$smarty.session.permissions)}}
							{{assign var="menu_url" value="stats_cleanup.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="stats" {{if $main_menu=='stats'}}class="active"{{/if}}>
							<i class="icon icon-stats"></i>
							<span>{{$lang.main_menu.stats}}</span>
						</a>
					{{/if}}

					{{if in_array('categories|view',$smarty.session.permissions) || in_array('category_groups|view',$smarty.session.permissions) || in_array('models|view',$smarty.session.permissions) || in_array('models_groups|view',$smarty.session.permissions) || in_array('tags|view',$smarty.session.permissions) || in_array('content_sources|view',$smarty.session.permissions) || in_array('content_sources_groups|view',$smarty.session.permissions) || in_array('flags|view',$smarty.session.permissions)}}
						{{if in_array('categories|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="categories.php"}}
						{{elseif in_array('category_groups|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="categories_groups.php"}}
						{{elseif in_array('models|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="models.php"}}
						{{elseif in_array('models_groups|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="models_groups.php"}}
						{{elseif in_array('content_sources|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="content_sources.php"}}
						{{elseif in_array('content_sources_groups|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="content_sources_groups.php"}}
						{{elseif in_array('tags|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="tags.php"}}
						{{else}}
							{{assign var="menu_url" value="flags.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="categorization" {{if $main_menu=='categorization'}}class="active"{{/if}}>
							<i class="icon icon-categorization"></i>
							<span>{{$lang.main_menu.categorization}}</span>
						</a>
					{{/if}}

					{{if in_array('website_ui|view',$smarty.session.permissions) || in_array('advertising|view',$smarty.session.permissions)}}
						{{if in_array('website_ui|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="project_pages.php"}}
						{{else}}
							{{assign var="menu_url" value="project_spots.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="website_ui" {{if $main_menu=='website_ui'}}class="active"{{/if}}>
							<i class="icon icon-website"></i>
							<span>{{$lang.main_menu.website_ui}}</span>
							{{if $smarty.session.admin_notifications.badges.theme.count>0}}<em title="{{$smarty.session.admin_notifications.badges.theme.title}}">{{$smarty.session.admin_notifications.badges.theme.count}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('plugins|view',$smarty.session.permissions)}}
						<a href="plugins.php" data-menu="plugins" {{if $main_menu=='plugins'}}class="active"{{/if}}>
							<i class="icon icon-plugins"></i>
							<span>{{$lang.main_menu.plugins}}</span>
							{{if $smarty.session.admin_notifications.badges.plugins.count>0}}<em title="{{$smarty.session.admin_notifications.badges.plugins.title}}">{{$smarty.session.admin_notifications.badges.plugins.count}}</em>{{/if}}
						</a>
					{{/if}}

					{{if in_array('system|background_tasks',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions) || in_array('localization|view',$smarty.session.permissions)}}
						{{if in_array('system|background_tasks',$smarty.session.permissions)}}
							{{assign var="menu_url" value="background_tasks.php"}}
						{{elseif in_array('localization|view',$smarty.session.permissions)}}
							{{assign var="menu_url" value="translations_summary.php"}}
						{{else}}
							{{assign var="menu_url" value="installation.php"}}
						{{/if}}
						<a href="{{$menu_url}}" data-menu="administration" {{if $main_menu=='administration'}}class="active"{{/if}}>
							<i class="icon icon-administration"></i>
							<span>{{$lang.main_menu.administration}}</span>
							{{if $smarty.session.admin_notifications.badges.administration.count>0}}<em title="{{$smarty.session.admin_notifications.badges.administration.title}}">{{$smarty.session.admin_notifications.badges.administration.count}}</em>{{/if}}
						</a>
					{{/if}}

					<a href="options.php" data-menu="options" {{if $main_menu=='options'}}class="active"{{/if}}>
						<i class="icon icon-settings"></i>
						<span>{{$lang.main_menu.settings}}</span>
						{{if $smarty.session.admin_notifications.badges.settings.count>0}}<em title="{{$smarty.session.admin_notifications.badges.settings.title}}">{{$smarty.session.admin_notifications.badges.settings.count}}</em>{{/if}}
					</a>

					{{include file="ap_custom_menu.tpl"}}
				</div>
				<div id="main_menu_margin"></div>
				{{if $left_menu=='no' || $side_menu=='no'}}
					{{include file="ap_custom_main.tpl"}}
					{{include file=$template}}
					{{include file="ap_custom_footer.tpl"}}
				{{else}}
					<div id="main_pane">
						<div id="left_pane" class="{{if $smarty.session.save.options.side_menu_mode=='only_text'}}no-icons{{/if}}" data-submenu="{{$main_menu}}">
							{{if $left_menu!=''}}{{include file=$left_menu}}{{elseif $side_menu!=''}}{{include file="menu_`$side_menu`.tpl"}}{{elseif $template!=''}}{{include file="menu_`$template`"}}{{/if}}
						</div>
						<div id="left_pane_margin"></div>
						<div id="center_pane">
							{{if is_array($list_messages)}}
								<div class="message">
								{{foreach item="item" from=$list_messages|smarty:nodefaults}}
									{{if $item|strpos:$lang.notifications.warning_prefix===0}}
										<p class="warning"><i class="icon icon-type-alert"></i> {{$item}}</p>
									{{else}}
										<p><i class="icon icon-type-success"></i> {{$item}}</p>
									{{/if}}
								{{/foreach}}
								</div>
							{{/if}}
							{{include file="ap_custom_main.tpl"}}
							{{include file=$template}}
							{{include file="ap_custom_footer.tpl"}}
						</div>
					</div>
				{{/if}}
			</div>
			<div id="layout_bottom_main">
				{{assign var="admin_page_generation_time_end" value=1|microtime}}
				{{assign var="admin_page_generation_memory_end" value=0|memory_get_peak_usage}}
				{{assign var="admin_page_generation_time" value=$admin_page_generation_time_end-$smarty.session.admin_page_generation_time_start}}
				{{assign var="admin_page_generation_time" value=$admin_page_generation_time|number_format:2:".":""}}
				{{assign var="admin_page_generation_memory" value=$admin_page_generation_memory_end-$smarty.session.admin_page_generation_memory_start}}
				{{assign var="admin_page_generation_memory" value=$admin_page_generation_memory/1024/1024|number_format:2:".":""}}
				<div id="layout_bottom_info">{{$lang.common.generated_message|replace:"%1%":$admin_page_generation_time|replace:"%2%":$admin_page_generation_memory}}</div>
			</div>
		</div>
	</div>
</body>
</html>