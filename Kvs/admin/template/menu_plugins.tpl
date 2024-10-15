{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	<h1><i class="icon icon-type-plugin"></i>{{$lang.main_menu.plugins}}</h1>
	<ul>
		<li>
			{{if $smarty.request.plugin_id==''}}
				<span><i class="icon icon-type-plugin"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.plugins.submenu_plugins_home}}</span>
			{{else}}
				<a href="plugins.php"><i class="icon icon-type-plugin"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.plugins.submenu_plugins_home}}</a>
			{{/if}}
		</li>
		{{foreach item="item" from=$plugins|smarty:nodefaults}}
			<li>
				{{assign var="badge_id" value="plugins_`$item.id`"}}
				{{if $item.id==$smarty.request.plugin_id}}
					<span>
						{{if $item.icon}}
							{{$item.icon|smarty:nodefaults}}
						{{else}}
							<i class="icon icon-type-plugin"></i>
						{{/if}}
						{{$item.title}}{{if $smarty.session.admin_notifications.badges.$badge_id.count>0}} <em title="{{$smarty.session.admin_notifications.badges.$badge_id.title}}">{{$smarty.session.admin_notifications.badges.$badge_id.count}}</em>{{/if}}
					</span>
				{{else}}
					<a href="plugins.php?plugin_id={{$item.id}}">
						{{if $item.icon}}
							{{$item.icon|smarty:nodefaults}}
						{{else}}
							<i class="icon icon-type-plugin"></i>
						{{/if}}
						{{$item.title}}{{if $smarty.session.admin_notifications.badges.$badge_id.count>0}} <em title="{{$smarty.session.admin_notifications.badges.$badge_id.title}}">{{$smarty.session.admin_notifications.badges.$badge_id.count}}</em>{{/if}}
					</a>
				{{/if}}
			</li>
		{{/foreach}}
	</ul>
</div>