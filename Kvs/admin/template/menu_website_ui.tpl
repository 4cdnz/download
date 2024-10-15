{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('website_ui|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="website_theme"><i class="icon icon-type-theme"></i>{{$lang.website_ui.submenu_group_theme}}</h1>
	<ul id="website_theme">
		{{if $has_empty_theme!=1}}
			{{if $supports_theme==1}}
				{{if $page_name=='project_theme.php'}}
					<li><span><i class="icon icon-type-theme"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.website_ui.submenu_option_theme_settings}}</span></li>
				{{else}}
					<li><a href="project_theme.php"><i class="icon icon-type-theme"><i class="icon icon-bottom icon-action-settings"></i></i>{{$lang.website_ui.submenu_option_theme_settings}}</a></li>
				{{/if}}
			{{/if}}

			{{if $page_name=='project_pages_history.php'}}
				<li><span><i class="icon icon-type-history"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_theme_history}}</span></li>
			{{else}}
				<li><a href="project_pages_history.php"><i class="icon icon-type-history"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_theme_history}}</a></li>
			{{/if}}

			{{if $page_name=='templates_search.php' && $smarty.get.action!='htaccess'}}
				<li><span><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-search"></i></i>{{$lang.website_ui.submenu_option_template_search}}</span></li>
			{{else}}
				<li><a href="templates_search.php"><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-search"></i></i>{{$lang.website_ui.submenu_option_template_search}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('website_ui|delete',$smarty.session.permissions)}}
			{{if $page_name=='project_theme_install.php'}}
				<li><span><i class="icon icon-type-theme-install"></i>{{$lang.website_ui.submenu_option_theme_install}}</span>{{if $smarty.session.admin_notifications.badges.theme_install.count>0}} <em title="{{$smarty.session.admin_notifications.badges.theme_install.title}}">{{$smarty.session.admin_notifications.badges.theme_install.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="project_theme_install.php"><i class="icon icon-type-theme-install"></i>{{$lang.website_ui.submenu_option_theme_install}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
	{{if $has_empty_theme!=1}}
		<h1 data-submenu-group="website_pages"><i class="icon icon-type-page"></i>{{$lang.website_ui.submenu_group_pages}}</h1>
		<ul id="website_pages">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='restore_pages' && $smarty.get.action!='restore_blocks' && $smarty.get.action!='change' && $smarty.get.action!='change_block' && $page_name=='project_pages.php'}}
				<li><span><i class="icon icon-type-page"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_pages_list}}</span></li>
			{{else}}
				<li><a href="project_pages.php"><i class="icon icon-type-page"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_pages_list}}</a></li>
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new'  && $page_name=='project_pages.php'}}
					<li><span><i class="icon icon-type-page"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_page}}</span></li>
				{{else}}
					<li><a href="project_pages.php?action=add_new"><i class="icon icon-type-page"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_page}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
				{{if $deleted_pages_count>0}}
					{{if $smarty.get.action=='restore_pages' && $page_name=='project_pages.php'}}
						<li><span><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_pages|replace:"%1%":$deleted_pages_count}}</span></li>
					{{else}}
						<li><a href="project_pages.php?action=restore_pages"><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_pages|replace:"%1%":$deleted_pages_count}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}

			{{if in_array('website_ui|edit_all',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
				{{if $deleted_blocks_count>0}}
					{{if $smarty.get.action=='restore_blocks' && $page_name=='project_pages.php'}}
						<li><span><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_blocks|replace:"%1%":$deleted_blocks_count}}</span></li>
					{{else}}
						<li><a href="project_pages.php?action=restore_blocks"><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_blocks|replace:"%1%":$deleted_blocks_count}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}
		</ul>
		<h1 data-submenu-group="website_infrastructure"><i class="icon icon-type-component"></i>{{$lang.website_ui.submenu_group_page_infrastructure}}</h1>
		<ul id="website_infrastructure">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_components.php'}}
				<li><span><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_page_components}}</span></li>
			{{else}}
				<li><a href="project_pages_components.php"><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_page_components}}</a></li>
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='project_pages_components.php'}}
					<li><span><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_page_component}}</span></li>
				{{else}}
					<li><a href="project_pages_components.php?action=add_new"><i class="icon icon-type-component"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_page_component}}</a></li>
				{{/if}}
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='restore_blocks' && $page_name=='project_pages_global.php'}}
				<li><span><i class="icon icon-type-block"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_global_blocks}}</span></li>
			{{else}}
				<li><a href="project_pages_global.php"><i class="icon icon-type-block"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_global_blocks}}</a></li>
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='project_pages_global.php'}}
					<li><span><i class="icon icon-type-block"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_global_block}}</span></li>
				{{else}}
					<li><a href="project_pages_global.php?action=add_new"><i class="icon icon-type-block"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_global_block}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('website_ui|edit_all',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
				{{if $deleted_global_blocks_count>0}}
					{{if $smarty.get.action=='restore_blocks' && $page_name=='project_pages_global.php'}}
						<li><span><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_global_blocks|replace:"%1%":$deleted_global_blocks_count}}</span></li>
					{{else}}
						<li><a href="project_pages_global.php?action=restore_blocks"><i class="icon icon-type-trash"><i class="icon icon-bottom icon-action-undo"></i></i>{{$lang.website_ui.submenu_option_restore_global_blocks|replace:"%1%":$deleted_global_blocks_count}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}

			{{if $smarty.get.action!='show_long_desc' && $page_name=='project_blocks.php'}}
				<li><span><i class="icon icon-type-repository"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_blocks_list}}</span></li>
			{{else}}
				<li><a href="project_blocks.php"><i class="icon icon-type-repository"></i>{{$lang.website_ui.submenu_option_blocks_list}}</a>{{if $smarty.session.admin_notifications.badges.theme_building_blocks.count>0}} <em title="{{$smarty.session.admin_notifications.badges.theme_building_blocks.title}}">{{$smarty.session.admin_notifications.badges.theme_building_blocks.count}}</em>{{/if}}</li>
			{{/if}}
		</ul>
	{{/if}}
{{/if}}
{{if $has_empty_theme!=1}}
	{{if in_array('advertising|view',$smarty.session.permissions)}}
		<h1 data-submenu-group="website_advertising"><i class="icon icon-type-ad"></i>{{$lang.website_ui.submenu_group_advertising}}</h1>
		<ul id="website_advertising">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $smarty.get.action!='add_new_spot' && $smarty.get.action!='change_spot' && $page_name=='project_spots.php'}}
				<li><span><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_advertisements_list}}</span>{{if $smarty.session.admin_notifications.badges.theme_advertising.count>0}} <em title="{{$smarty.session.admin_notifications.badges.theme_advertising.title}}">{{$smarty.session.admin_notifications.badges.theme_advertising.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="project_spots.php"><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_advertisements_list}}</a>{{if $smarty.session.admin_notifications.badges.theme_advertising.count>0}} <em title="{{$smarty.session.admin_notifications.badges.theme_advertising.title}}">{{$smarty.session.admin_notifications.badges.theme_advertising.count}}</em>{{/if}}</li>
			{{/if}}

			{{if in_array('advertising|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='project_spots.php'}}
					<li><span><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_advertisement}}</span></li>
				{{else}}
					<li><a href="project_spots.php?action=add_new"><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_advertisement}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('advertising|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new_spot' && $page_name=='project_spots.php'}}
					<li><span><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-group"></i></i>{{$lang.website_ui.submenu_option_add_spot}}</span></li>
				{{else}}
					<li><a href="project_spots.php?action=add_new_spot"><i class="icon icon-type-ad"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-group"></i></i>{{$lang.website_ui.submenu_option_add_spot}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('website_ui|view',$smarty.session.permissions) && $supports_langs==1}}
		<h1 data-submenu-group="website_texts"><i class="icon icon-type-language"></i>{{$lang.website_ui.submenu_group_page_texts}}</h1>
		<ul id="website_texts">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_lang_files.php'}}
				<li><span><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_lang_files}}</span></li>
			{{else}}
				<li><a href="project_pages_lang_files.php"><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_lang_files}}</a></li>
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='project_pages_lang_files.php'}}
					<li><span><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_lang_file}}</span></li>
				{{else}}
					<li><a href="project_pages_lang_files.php?action=add_new"><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_lang_file}}</a></li>
				{{/if}}
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_lang_texts.php'}}
				<li><span><i class="icon icon-type-text"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_text_items}}</span></li>
			{{else}}
				<li><a href="project_pages_lang_texts.php"><i class="icon icon-type-text"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.website_ui.submenu_option_text_items}}</a></li>
			{{/if}}

			{{if in_array('website_ui|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='project_pages_lang_texts.php'}}
					<li><span><i class="icon icon-type-text"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_text_item}}</span></li>
				{{else}}
					<li><a href="project_pages_lang_texts.php?action=add_new"><i class="icon icon-type-text"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.website_ui.submenu_option_add_text_item}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|administration',$smarty.session.permissions)}}
		<h1 data-submenu-group="website_cache"><i class="icon icon-type-cache"></i>{{$lang.website_ui.submenu_group_cache}}</h1>
		<ul id="website_cache">
			<li><a href="project_pages.php?action=reset_file_cache" data-confirm="{{$lang.website_ui.submenu_option_reset_file_cache_confirm}}"><i class="icon icon-type-disk"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.website_ui.submenu_option_reset_file_cache}}</a></li>
			{{if $config.memcache_server && class_exists('Memcached')}}
				<li><a href="project_pages.php?action=reset_mem_cache" data-confirm="{{$lang.website_ui.submenu_option_reset_mem_cache_confirm}}"><i class="icon icon-type-memory"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.website_ui.submenu_option_reset_mem_cache}}</a></li>
			{{/if}}
			{{if $collect_performance_stats==1}}
				<li><a href="project_pages.php?action=reset_perf_stats" data-confirm="{{$lang.website_ui.submenu_option_reset_performance_stats_confirm}}"><i class="icon icon-type-load"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.website_ui.submenu_option_reset_performance_stats}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
{{/if}}
</div>