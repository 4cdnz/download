{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('system|background_tasks',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
		<h1 data-submenu-group="administration_main"><i class="icon icon-type-administrative"></i>{{$lang.settings.submenu_group_administration}}</h1>
		<ul id="administration_main">
			{{if in_array('system|background_tasks',$smarty.session.permissions)}}
				{{if $page_name=='background_tasks.php'}}
					<li><span><i class="icon icon-type-task"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_background_tasks}}</span>{{if $smarty.session.admin_notifications.badges.administration_background_tasks.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_background_tasks.title}}">{{$smarty.session.admin_notifications.badges.administration_background_tasks.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="background_tasks.php"><i class="icon icon-type-task"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_background_tasks}}</a>{{if $smarty.session.admin_notifications.badges.administration_background_tasks.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_background_tasks.title}}">{{$smarty.session.admin_notifications.badges.administration_background_tasks.count}}</em>{{/if}}</li>
				{{/if}}
			{{/if}}

			{{if in_array('system|administration',$smarty.session.permissions)}}
				{{if $page_name=='installation.php'}}
					<li><span><i class="icon icon-type-info"></i>{{$lang.settings.submenu_option_installation}}</span>{{if $smarty.session.admin_notifications.badges.administration_installation.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_installation.title}}">{{$smarty.session.admin_notifications.badges.administration_installation.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="installation.php"><i class="icon icon-type-info"></i>{{$lang.settings.submenu_option_installation}}</a>{{if $smarty.session.admin_notifications.badges.administration_installation.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_installation.title}}">{{$smarty.session.admin_notifications.badges.administration_installation.count}}</em>{{/if}}</li>
				{{/if}}

				{{if $page_name=='log_system.php'}}
					<li><span><i class="icon icon-type-system"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_system_log}}</span></li>
				{{else}}
					<li><a href="log_system.php"><i class="icon icon-type-system"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_system_log}}</a></li>
				{{/if}}

				{{if $page_name=='log_logins.php'}}
					<li><span><i class="icon icon-type-login"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_activity_log}}</span></li>
				{{else}}
					<li><a href="log_logins.php"><i class="icon icon-type-login"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_activity_log}}</a></li>
				{{/if}}

				{{if $config.is_clone_db!="true"}}
					{{if $page_name=='log_audit.php'}}
						<li><span><i class="icon icon-type-audit"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_audit_log}}</span></li>
					{{else}}
						<li><a href="log_audit.php"><i class="icon icon-type-audit"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_audit_log}}</a></li>
					{{/if}}

					{{if $config.installation_type>=2}}
						{{if $page_name=='log_bill.php' && $smarty.get.action!='change'}}
							<li><span><i class="icon icon-type-billing"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_bill_log}}</span>{{if $smarty.session.admin_notifications.badges.administration_log_billing.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_log_billing.title}}">{{$smarty.session.admin_notifications.badges.administration_log_billing.count}}</em>{{/if}}</li>
						{{else}}
							<li><a href="log_bill.php"><i class="icon icon-type-billing"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_bill_log}}</a>{{if $smarty.session.admin_notifications.badges.administration_log_billing.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_log_billing.title}}">{{$smarty.session.admin_notifications.badges.administration_log_billing.count}}</em>{{/if}}</li>
						{{/if}}
					{{/if}}

					{{if $page_name=='log_feeds.php'}}
						<li><span><i class="icon icon-type-feed"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_feeds_log}}</span></li>
					{{else}}
						<li><a href="log_feeds.php"><i class="icon icon-type-feed"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_feeds_log}}</a></li>
					{{/if}}

					{{if $page_name=='log_imports.php' && $smarty.get.action!='change'}}
						<li><span><i class="icon icon-type-import"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_imports_log}}</span></li>
					{{else}}
						<li><a href="log_imports.php"><i class="icon icon-type-import"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_imports_log}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}

			{{if in_array('system|background_tasks',$smarty.session.permissions)}}
				{{if $page_name=='log_background_tasks.php' && $smarty.get.action!='change'}}
					<li><span><i class="icon icon-type-task"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_background_tasks_log}}</span></li>
				{{else}}
					<li><a href="log_background_tasks.php"><i class="icon icon-type-task"><i class="icon icon-bottom icon-action-log"></i></i>{{$lang.settings.submenu_option_background_tasks_log}}</a></li>
				{{/if}}
			{{/if}}

			{{assign var="notification_file_changes_key" value="administration.file_changes.unexpected_changes"}}
			{{if in_array('system|administration',$smarty.session.permissions) && is_array($smarty.session.admin_notifications.list.$notification_file_changes_key)}}
				{{if $page_name=='file_changes.php'}}
					<li><span><i class="icon icon-type-surprise"><i class="icon icon-bottom icon-action-warning"></i></i>{{$lang.settings.submenu_option_file_changes}}</span>{{if $smarty.session.admin_notifications.badges.administration_file_changes.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_file_changes.title}}">{{$smarty.session.admin_notifications.badges.administration_file_changes.count}}</em>{{/if}}</li>
				{{else}}
					<li><a href="file_changes.php"><i class="icon icon-type-surprise"><i class="icon icon-bottom icon-action-warning"></i></i>{{$lang.settings.submenu_option_file_changes}}</a>{{if $smarty.session.admin_notifications.badges.administration_file_changes.count>0}} <em title="{{$smarty.session.admin_notifications.badges.administration_file_changes.title}}">{{$smarty.session.admin_notifications.badges.administration_file_changes.count}}</em>{{/if}}</li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if $smarty.session.userdata.is_superadmin>0}}
		<h1 data-submenu-group="administration_admins"><i class="icon icon-type-admin"></i>{{$lang.settings.submenu_group_admin_access}}</h1>
		<ul id="administration_admins">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='admin_users.php'}}
				<li><span><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_admins_list}}</span></li>
			{{else}}
				<li><a href="admin_users.php"><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_admins_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='admin_users.php'}}
				<li><span><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_admin}}</span></li>
			{{else}}
				<li><a href="admin_users.php?action=add_new"><i class="icon icon-type-admin"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.settings.submenu_option_add_admin}}</a></li>
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='admin_users_groups.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-admin"></i></i>{{$lang.settings.submenu_option_groups_list}}</span></li>
			{{else}}
				<li><a href="admin_users_groups.php"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-admin"></i></i>{{$lang.settings.submenu_option_groups_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='admin_users_groups.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-admin"></i></i>{{$lang.settings.submenu_option_add_group}}</span></li>
			{{else}}
				<li><a href="admin_users_groups.php?action=add_new"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-admin"></i></i>{{$lang.settings.submenu_option_add_group}}</a></li>
			{{/if}}

			<li><a href="admin_users.php?action=reset_admin_cache"><i class="icon icon-type-disk"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.settings.submenu_option_reset_admin_cache}}</a></li>
			{{if $smarty.session.userdata.is_superadmin==2}}
				<li><a href="admin_users.php?action=reset_lock_files" data-confirm="{{$lang.settings.submenu_option_reset_lock_files_confirm}}"><i class="icon icon-type-disk"><i class="icon icon-bottom icon-action-cleanup"></i></i>{{$lang.settings.submenu_option_reset_lock_files}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('localization|view',$smarty.session.permissions)}}
		<h1 data-submenu-group="administration_localization"><i class="icon icon-type-language"></i>{{$lang.settings.submenu_group_localization}}</h1>
		<ul id="administration_localization">
			{{if $page_name=='translations_summary.php'}}
				<li><span><i class="icon icon-type-language"></i>{{$lang.settings.submenu_option_translations_summary}}</span></li>
			{{else}}
				<li><a href="translations_summary.php"><i class="icon icon-type-language"></i>{{$lang.settings.submenu_option_translations_summary}}</a></li>
			{{/if}}
			{{if $smarty.get.action!='change' && $page_name=='translations.php'}}
				<li><span><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_translations_list}}</span></li>
			{{else}}
				<li><a href="translations.php"><i class="icon icon-type-language"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.settings.submenu_option_translations_list}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>