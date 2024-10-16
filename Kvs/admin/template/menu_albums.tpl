{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	<h1 data-submenu-group="albums_main"><i class="icon icon-type-album"></i>{{$lang.albums.submenu_group_albums}}</h1>
	<ul id="albums_main">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change_images' && $smarty.get.action!='change' && $smarty.get.action!='mark_deleted' && $smarty.get.action!='change_deleted' && $page_name=='albums.php'}}
			<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.albums.submenu_option_albums_list}}</span>{{if $smarty.session.admin_notifications.badges.albums_main.count>0}} <em title="{{$smarty.session.admin_notifications.badges.albums_main.title}}">{{$smarty.session.admin_notifications.badges.albums_main.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="albums.php"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.albums.submenu_option_albums_list}}</a>{{if $smarty.session.admin_notifications.badges.albums_main.count>0}} <em title="{{$smarty.session.admin_notifications.badges.albums_main.title}}">{{$smarty.session.admin_notifications.badges.albums_main.count}}</em>{{/if}}</li>
		{{/if}}

		{{if in_array('albums|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='albums.php'}}
				<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.albums.submenu_option_add_album}}</span></li>
			{{else}}
				<li><a href="albums.php?action=add_new"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.albums.submenu_option_add_album}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('albums|import',$smarty.session.permissions)}}
			{{if $page_name=='albums_import.php'}}
				<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-import"></i></i>{{$lang.albums.submenu_option_import_albums}}</span></li>
			{{else}}
				<li><a href="albums_import.php"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-import"></i></i>{{$lang.albums.submenu_option_import_albums}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('albums|export',$smarty.session.permissions)}}
			{{if $page_name=='albums_export.php'}}
				<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-export"></i></i>{{$lang.albums.submenu_option_export_albums}}</span></li>
			{{else}}
				<li><a href="albums_export.php"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-export"></i></i>{{$lang.albums.submenu_option_export_albums}}</a></li>
			{{/if}}
		{{/if}}

		{{if $page_name=='albums_select.php'}}
			<li><span><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-select"></i></i>{{$lang.albums.submenu_option_select_albums}}</span></li>
		{{else}}
			<li><a href="albums_select.php"><i class="icon icon-type-album"><i class="icon icon-bottom icon-action-select"></i></i>{{$lang.albums.submenu_option_select_albums}}</a></li>
		{{/if}}
	</ul>
	{{if count($list_updates)>0}}
		<div class="left_dt">
			<h1 data-submenu-group="albums_updates"><i class="icon icon-type-calendar"></i>{{$lang.albums.submenu_group_albums_by_date}}</h1>
			<table id="albums_updates">
				{{foreach item="item" from=$list_updates}}
					<tr>
						<td>{{$item.post_date|date_format:$smarty.session.userdata.short_date_format}}</td>
						<td>{{$item.updates}}</td>
					</tr>
				{{/foreach}}
			</table>
		</div>
	{{/if}}
</div>