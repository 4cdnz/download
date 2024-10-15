{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('posts|view',$smarty.session.permissions)}}
		<h1 data-submenu-group="posts_main"><i class="icon icon-type-post"></i>{{$lang.posts.submenu_group_posts}}</h1>
		<ul id="posts_main">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='posts.php'}}
				<li><span><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.posts.submenu_option_posts_list}}</span>{{if $smarty.session.admin_notifications.badges.posts_main.count>0}} <em title="{{$smarty.session.admin_notifications.badges.posts_main.title}}">{{$smarty.session.admin_notifications.badges.posts_main.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="posts.php"><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.posts.submenu_option_posts_list}}</a>{{if $smarty.session.admin_notifications.badges.posts_main.count>0}} <em title="{{$smarty.session.admin_notifications.badges.posts_main.title}}">{{$smarty.session.admin_notifications.badges.posts_main.count}}</em>{{/if}}</li>
			{{/if}}

			{{if $locked_post_type_support!=1}}
				{{if in_array('posts|add',$smarty.session.permissions)}}
					{{if $smarty.get.action=='add_new' && $page_name=='posts.php'}}
						<li><span><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post}}</span></li>
					{{else}}
						<li><a href="posts.php?action=add_new"><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}
		</ul>
		{{if $locked_post_type_support==1}}
			{{foreach item="item" from=$list_types|smarty:nodefaults}}
				<h1 data-submenu-group="posts_{{$item.external_id}}"><i class="icon icon-type-post"></i>{{$item.title}}</h1>
				<ul id="posts_{{$item.external_id}}">
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=="posts_for_`$item.external_id`.php"}}
						<li><span><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-list"></i></i>{{$item.title}}</span></li>
					{{else}}
						<li><a href="posts_for_{{$item.external_id}}.php"><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-list"></i></i>{{$item.title}}</a></li>
					{{/if}}

					{{if in_array('posts|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=="posts_for_`$item.external_id`.php"}}
							<li><span><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post}}</span></li>
						{{else}}
							<li><a href="posts_for_{{$item.external_id}}.php?action=add_new"><i class="icon icon-type-post"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post}}</a></li>
						{{/if}}
					{{/if}}
				</ul>
			{{/foreach}}
		{{/if}}
	{{/if}}
	{{if in_array('posts_types|view',$smarty.session.permissions)}}
		<h1 data-submenu-group="posts_post_types"><i class="icon icon-type-post-type"></i>{{$lang.posts.submenu_group_post_types}}</h1>
		<ul id="posts_post_types">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='posts_types.php'}}
				<li><span><i class="icon icon-type-post-type"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.posts.submenu_option_post_types_list}}</span></li>
			{{else}}
				<li><a href="posts_types.php"><i class="icon icon-type-post-type"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.posts.submenu_option_post_types_list}}</a></li>
			{{/if}}

			{{if in_array('posts_types|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='posts_types.php'}}
					<li><span><i class="icon icon-type-post-type"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post_type}}</span></li>
				{{else}}
					<li><a href="posts_types.php?action=add_new"><i class="icon icon-type-post-type"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.posts.submenu_option_add_post_type}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
</div>