{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	<h1 data-submenu-group="settings_main"><i class="icon icon-type-settings"></i>{{$lang.settings.submenu_group_settings}}</h1>
	<ul id="settings_main">
		{{if $page_name=='options.php' && $smarty.request.page==''}}
			<li><span><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_personal_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_personal.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_personal.title}}">{{$smarty.session.admin_notifications.badges.settings_personal.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="options.php"><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_personal_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_personal.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_personal.title}}">{{$smarty.session.admin_notifications.badges.settings_personal.count}}</em>{{/if}}</li>
		{{/if}}

		{{if in_array('system|system_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='general_settings'}}
				<li><span><i class="icon icon-type-content"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_system_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_general.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_general.title}}">{{$smarty.session.admin_notifications.badges.settings_general.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="options.php?page=general_settings"><i class="icon icon-type-content"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_system_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_general.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_general.title}}">{{$smarty.session.admin_notifications.badges.settings_general.count}}</em>{{/if}}</li>
			{{/if}}
		{{/if}}

		{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='memberzone_settings'}}
				<li><span><i class="icon icon-type-memberzone"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_memberzone_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=memberzone_settings"><i class="icon icon-type-memberzone"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_memberzone_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|antispam_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='antispam_settings'}}
				<li><span><i class="icon icon-type-spam"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_antispam_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=antispam_settings"><i class="icon icon-type-spam"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_antispam_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|website_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='website_settings'}}
				<li><span><i class="icon icon-type-site"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_website_ui_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_website.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_website.title}}">{{$smarty.session.admin_notifications.badges.settings_website.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="options.php?page=website_settings"><i class="icon icon-type-site"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_website_ui_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_website.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_website.title}}">{{$smarty.session.admin_notifications.badges.settings_website.count}}</em>{{/if}}</li>
			{{/if}}
		{{/if}}

		{{if in_array('system|stats_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='stats_settings'}}
				<li><span><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_stats_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_stats.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_stats.title}}">{{$smarty.session.admin_notifications.badges.settings_stats.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="options.php?page=stats_settings"><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_stats_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_stats.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_stats.title}}">{{$smarty.session.admin_notifications.badges.settings_stats.count}}</em>{{/if}}</li>
			{{/if}}
		{{/if}}

		{{if in_array('system|customization',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='customization'}}
				<li><span><i class="icon icon-type-plugin"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_customization}}</span></li>
			{{else}}
				<li><a href="options.php?page=customization"><i class="icon icon-type-plugin"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_customization}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
	{{if in_array('system|player_settings',$smarty.session.permissions) || in_array('system|vast_profiles',$smarty.session.permissions)}}
		<h1 data-submenu-group="settings_player"><i class="icon icon-type-player"></i>{{$lang.settings.submenu_group_player}}</h1>
		<ul id="settings_player">
			{{if in_array('system|player_settings',$smarty.session.permissions)}}
				{{if $page_name=='player.php' && $smarty.request.page!='embed'}}
					<li><span><i class="icon icon-type-player"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_player_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_player.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_player.title}}">{{$smarty.session.admin_notifications.badges.settings_player.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="player.php"><i class="icon icon-type-player"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_player_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_player.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_player.title}}">{{$smarty.session.admin_notifications.badges.settings_player.count}}</em>{{/if}}</li>
				{{/if}}

				{{if $page_name=='player.php' && $smarty.request.page=='embed'}}
					<li><span><i class="icon icon-type-embed"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_embed_settings}}</span>{{if $smarty.session.admin_notifications.badges.settings_embed.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_embed.title}}">{{$smarty.session.admin_notifications.badges.settings_embed.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="player.php?page=embed"><i class="icon icon-type-embed"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_embed_settings}}</a>{{if $smarty.session.admin_notifications.badges.settings_embed.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_embed.title}}">{{$smarty.session.admin_notifications.badges.settings_embed.count}}</em>{{/if}}</li>
				{{/if}}
			{{/if}}

			{{if in_array('system|vast_profiles',$smarty.session.permissions)}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='vast_profiles.php'}}
					<li><span><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-player"></i></i>{{$lang.settings.submenu_option_vast_profiles_list}}</span>{{if $smarty.session.admin_notifications.badges.settings_vast_profiles.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_vast_profiles.title}}">{{$smarty.session.admin_notifications.badges.settings_vast_profiles.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="vast_profiles.php"><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-player"></i></i>{{$lang.settings.submenu_option_vast_profiles_list}}</a>{{if $smarty.session.admin_notifications.badges.settings_vast_profiles.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_vast_profiles.title}}">{{$smarty.session.admin_notifications.badges.settings_vast_profiles.count}}</em>{{/if}}</li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='vast_profiles.php'}}
					<li><span><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-player"></i></i>{{$lang.settings.submenu_option_add_vast_profile}}</span></li>
				{{else}}
					<li><a href="vast_profiles.php?action=add_new"><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-player"></i></i>{{$lang.settings.submenu_option_add_vast_profile}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|formats',$smarty.session.permissions)}}
		<h1 data-submenu-group="settings_formats"><i class="icon icon-type-format"></i>{{$lang.settings.submenu_group_formats}}</h1>
		<ul id="settings_formats">
			{{if $config.installation_type>=2}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='add_new_group' && $smarty.get.action!='change' && $smarty.get.action!='change_group' && $page_name=='formats_videos.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_formats_videos_list}}</span></li>
				{{else}}
					<li><a href="formats_videos.php"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_formats_videos_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='formats_videos.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_add_format_video}}</span></li>
				{{else}}
					<li><a href="formats_videos.php?action=add_new"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_add_format_video}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new_group' && $page_name=='formats_videos.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_add_format_video_group}}</span></li>
				{{else}}
					<li><a href="formats_videos.php?action=add_new_group"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_add_format_video_group}}</a></li>
				{{/if}}
			{{else}}
				{{if $page_name=='formats_videos_basic.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-settings"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_main_format_video}}</span></li>
				{{else}}
					<li><a href="formats_videos_basic.php"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-settings"></i><i class="icon icon-top icon-type-video"></i></i>{{$lang.settings.submenu_option_main_format_video}}</a></li>
				{{/if}}
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='formats_screenshots.php'}}
				<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-screenshot"></i></i>{{$lang.settings.submenu_option_formats_screenshots_list}}</span></li>
			{{else}}
				<li><a href="formats_screenshots.php"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-screenshot"></i></i>{{$lang.settings.submenu_option_formats_screenshots_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='formats_screenshots.php'}}
				<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-screenshot"></i></i>{{$lang.settings.submenu_option_add_format_screenshot}}</span></li>
			{{else}}
				<li><a href="formats_screenshots.php?action=add_new"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-screenshot"></i></i>{{$lang.settings.submenu_option_add_format_screenshot}}</a></li>
			{{/if}}

			{{if $config.installation_type==4}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='formats_albums.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-album"></i></i>{{$lang.settings.submenu_option_formats_albums_list}}</span></li>
				{{else}}
					<li><a href="formats_albums.php"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-album"></i></i>{{$lang.settings.submenu_option_formats_albums_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='formats_albums.php'}}
					<li><span><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-album"></i></i>{{$lang.settings.submenu_option_add_format_album}}</span></li>
				{{else}}
					<li><a href="formats_albums.php?action=add_new"><i class="icon icon-type-format"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-album"></i></i>{{$lang.settings.submenu_option_add_format_album}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|servers',$smarty.session.permissions)}}
		<h1 data-submenu-group="settings_storage_servers"><i class="icon icon-type-storage"></i>{{$lang.settings.submenu_group_storage_servers}}</h1>
		<ul id="settings_storage_servers">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='add_new_group' && $smarty.get.action!='change' && $smarty.get.action!='change_group' && $page_name=='servers.php'}}
				<li><span><i class="icon icon-type-storage"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_storage_servers_list}}</span>{{if $smarty.session.admin_notifications.badges.settings_storage_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_storage_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_storage_servers.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="servers.php"><i class="icon icon-type-storage"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_storage_servers_list}}</a>{{if $smarty.session.admin_notifications.badges.settings_storage_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_storage_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_storage_servers.count}}</em>{{/if}}</li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='servers.php'}}
				<li><span><i class="icon icon-type-storage"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_storage_server}}</span></li>
			{{else}}
				<li><a href="servers.php?action=add_new"><i class="icon icon-type-storage"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_storage_server}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new_group' && $page_name=='servers.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_storage_server_group}}</span></li>
			{{else}}
				<li><a href="servers.php?action=add_new_group"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-storage"></i></i>{{$lang.settings.submenu_option_add_storage_server_group}}</a></li>
			{{/if}}
		</ul>
		<h1 data-submenu-group="settings_conversion_servers"><i class="icon icon-type-conversion"></i>{{$lang.settings.submenu_group_conversion_servers}}</h1>
		<ul id="settings_conversion_servers">
			{{if $config.installation_type>=3}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='servers_conversion.php'}}
					<li><span><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_conversion_servers_list}}</span>{{if $smarty.session.admin_notifications.badges.settings_conversion_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_conversion_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_conversion_servers.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="servers_conversion.php"><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_conversion_servers_list}}</a>{{if $smarty.session.admin_notifications.badges.settings_conversion_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_conversion_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_conversion_servers.count}}</em>{{/if}}</li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='servers_conversion.php'}}
					<li><span><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_conversion_server}}</span></li>
				{{else}}
					<li><a href="servers_conversion.php?action=add_new"><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_conversion_server}}</a></li>
				{{/if}}
			{{else}}
				{{if $page_name=='servers_conversion_basic.php'}}
					<li><span><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_main_conversion_server}}</span>{{if $smarty.session.admin_notifications.badges.settings_conversion_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_conversion_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_conversion_servers.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="servers_conversion_basic.php"><i class="icon icon-type-conversion"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.settings.submenu_option_main_conversion_server}}</a>{{if $smarty.session.admin_notifications.badges.settings_conversion_servers.count>0}} <em title="{{$smarty.session.admin_notifications.badges.settings_conversion_servers.title}}">{{$smarty.session.admin_notifications.badges.settings_conversion_servers.count}}</em>{{/if}}</li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|localization',$smarty.session.permissions)}}
		<h1 data-submenu-group="settings_localization"><i class="icon icon-type-language"></i>{{$lang.settings.submenu_group_localization}}</h1>
		<ul id="settings_localization">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='languages.php'}}
				<li><span><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_languages_list}}</span></li>
			{{else}}
				<li><a href="languages.php"><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_languages_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='languages.php'}}
				<li><span><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_language}}</span></li>
			{{else}}
				<li><a href="languages.php?action=add_new"><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_language}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>