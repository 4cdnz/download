{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
		<h1 data-submenu-group="stats_traffic"><i class="icon icon-type-traffic"></i>{{$lang.stats.submenu_group_stats}}</h1>
		<ul id="stats_traffic">
			{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
				{{if $page_name=='stats_in.php'}}
					<li><span><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_in}}</span></li>
				{{else}}
					<li><a href="stats_in.php"><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_in}}</a></li>
				{{/if}}
				{{if $page_name=='stats_country.php'}}
					<li><span><i class="icon icon-type-country"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_country}}</span></li>
				{{else}}
					<li><a href="stats_country.php"><i class="icon icon-type-country"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_country}}</a></li>
				{{/if}}
				{{if $page_name=='stats_referer.php'}}
					<li><span><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_referer}}</span></li>
				{{else}}
					<li><a href="stats_referer.php"><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_referer}}</a></li>
				{{/if}}
				{{if $page_name=='stats_out.php'}}
					<li><span><i class="icon icon-type-out"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_out}}</span></li>
				{{else}}
					<li><a href="stats_out.php"><i class="icon icon-type-out"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_out}}</a></li>
				{{/if}}
				{{if $page_name=='stats_player.php'}}
					<li><span><i class="icon icon-type-player"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_player}}</span></li>
				{{else}}
					<li><a href="stats_player.php"><i class="icon icon-type-player"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_player}}</a></li>
				{{/if}}
				{{if $page_name=='stats_embed.php'}}
					<li><span><i class="icon icon-type-embed"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_embed}}</span></li>
				{{else}}
					<li><a href="stats_embed.php"><i class="icon icon-type-embed"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_embed}}</a></li>
				{{/if}}
				{{if $page_name=='stats_overload.php'}}
					<li><span><i class="icon icon-type-load"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_overload}}</span></li>
				{{else}}
					<li><a href="stats_overload.php"><i class="icon icon-type-load"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_overload}}</a></li>
				{{/if}}
			{{/if}}
			{{if in_array('system|administration',$smarty.session.permissions)}}
				{{if $page_name=='stats_cleanup.php'}}
					<li><span><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.stats.submenu_option_stats_cleanup}}</span></li>
				{{else}}
					<li><a href="stats_cleanup.php"><i class="icon icon-type-traffic"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.stats.submenu_option_stats_cleanup}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
		<h1 data-submenu-group="stats_search"><i class="icon icon-type-search"></i>{{$lang.stats.submenu_group_stats_search}}</h1>
		<ul id="stats_search">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='stats_search.php'}}
				<li><span><i class="icon icon-type-search"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_search}}</span></li>
			{{else}}
				<li><a href="stats_search.php"><i class="icon icon-type-search"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_search}}</a></li>
			{{/if}}
			{{if in_array('stats|manage_search_queries',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='stats_search.php'}}
					<li><span><i class="icon icon-type-search"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.stats.submenu_option_add_searches}}</span></li>
				{{else}}
					<li><a href="stats_search.php?action=add_new"><i class="icon icon-type-search"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.stats.submenu_option_add_searches}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
		<h1 data-submenu-group="stats_content"><i class="icon icon-type-content"></i>{{$lang.stats.submenu_group_stats_content}}</h1>
		<ul id="stats_content">
			{{if $page_name=='stats_videos.php'}}
				<li><span><i class="icon icon-type-video"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_videos}}</span></li>
			{{else}}
				<li><a href="stats_videos.php"><i class="icon icon-type-video"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_videos}}</a></li>
			{{/if}}
			{{if $config.installation_type==4}}
				{{if $page_name=='stats_albums.php'}}
					<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_albums}}</span></li>
				{{else}}
					<li><a href="stats_albums.php"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_albums}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_user_stats',$smarty.session.permissions)}}
		<h1 data-submenu-group="stats_users"><i class="icon icon-type-memberzone"></i>{{$lang.stats.submenu_group_stats_users}}</h1>
		<ul id="stats_users">
			{{if $page_name=='stats_transactions.php'}}
				<li><span><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_transactions}}</span></li>
			{{else}}
				<li><a href="stats_transactions.php"><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_transactions}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users.php'}}
				<li><span><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users}}</span></li>
			{{else}}
				<li><a href="stats_users.php"><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_logins.php'}}
				<li><span><i class="icon icon-type-login"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_logins}}</span></li>
			{{else}}
				<li><a href="stats_users_logins.php"><i class="icon icon-type-login"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_logins}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_content.php'}}
				<li><span><i class="icon icon-type-visit"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_content}}</span></li>
			{{else}}
				<li><a href="stats_users_content.php"><i class="icon icon-type-visit"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_content}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_purchases.php'}}
				<li><span><i class="icon icon-type-purchase"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_purchases}}</span></li>
			{{else}}
				<li><a href="stats_users_purchases.php"><i class="icon icon-type-purchase"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_purchases}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_sellings.php'}}
				<li><span><i class="icon icon-type-selling"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_sellings}}</span></li>
			{{else}}
				<li><a href="stats_users_sellings.php"><i class="icon icon-type-selling"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_sellings}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_donations.php'}}
				<li><span><i class="icon icon-type-donation"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_donations}}</span></li>
			{{else}}
				<li><a href="stats_users_donations.php"><i class="icon icon-type-donation"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_donations}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_awards.php'}}
				<li><span><i class="icon icon-type-award"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_awards}}</span></li>
			{{else}}
				<li><a href="stats_users_awards.php"><i class="icon icon-type-award"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_awards}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_initial_transactions.php'}}
				<li><span><i class="icon icon-type-ip"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_initial_transactions}}</span></li>
			{{else}}
				<li><a href="stats_users_initial_transactions.php"><i class="icon icon-type-ip"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_stats_users_initial_transactions}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|manage_referers',$smarty.session.permissions)}}
		<h1 data-submenu-group="stats_referers"><i class="icon icon-type-referer"></i>{{$lang.stats.submenu_group_referers}}</h1>
		<ul id="stats_referers">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='stats_referers_list.php'}}
				<li><span><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_referers_list}}</span></li>
			{{else}}
				<li><a href="stats_referers_list.php"><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.stats.submenu_option_referers_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='stats_referers_list.php'}}
				<li><span><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.stats.submenu_option_add_referer}}</span></li>
			{{else}}
				<li><a href="stats_referers_list.php?action=add_new"><i class="icon icon-type-referer"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.stats.submenu_option_add_referer}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>