{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('users|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_main"><i class="icon icon-type-memberzone"></i>{{$lang.users.submenu_group_community}}</h1>
	<ul id="users_main">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='users.php'}}
			<li><span><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_users_list}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_users.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_users.title}}">{{$smarty.session.admin_notifications.badges.memberzone_users.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="users.php"><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_users_list}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_users.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_users.title}}">{{$smarty.session.admin_notifications.badges.memberzone_users.count}}</em>{{/if}}</li>
		{{/if}}

		{{if in_array('users|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='users.php'}}
				<li><span><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_user}}</span></li>
			{{else}}
				<li><a href="users.php?action=add_new"><i class="icon icon-type-user"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_user}}</a></li>
			{{/if}}
		{{/if}}
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='comments.php'}}
			<li><span><i class="icon icon-type-comment"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_comments_list}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_comments.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_comments.title}}">{{$smarty.session.admin_notifications.badges.memberzone_comments.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="comments.php"><i class="icon icon-type-comment"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_comments_list}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_comments.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_comments.title}}">{{$smarty.session.admin_notifications.badges.memberzone_comments.count}}</em>{{/if}}</li>
		{{/if}}
		{{if $config.installation_type==4}}
			{{if $smarty.get.action!='change' && $page_name=='users_blogs.php'}}
				<li><span><i class="icon icon-type-blog"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_blog_entries_list}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_blogs.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_blogs.title}}">{{$smarty.session.admin_notifications.badges.memberzone_blogs.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="users_blogs.php"><i class="icon icon-type-blog"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_blog_entries_list}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_blogs.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_blogs.title}}">{{$smarty.session.admin_notifications.badges.memberzone_blogs.count}}</em>{{/if}}</li>
			{{/if}}
		{{/if}}
		{{if in_array('users|emailings',$smarty.session.permissions)}}
			{{if $page_name=='emailing.php'}}
				<li><span><i class="icon icon-type-emailing"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_create_emailing}}</span></li>
			{{else}}
				<li><a href="emailing.php"><i class="icon icon-type-emailing"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_create_emailing}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}

{{if in_array('feedbacks|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_feedback"><i class="icon icon-type-feedback"></i>{{$lang.users.submenu_group_feedback}}</h1>
	<ul id="users_feedback">
		{{if $smarty.get.action!='change' && $page_name=='feedbacks.php'}}
			<li><span><i class="icon icon-type-feedback"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-site"></i></i>{{$lang.users.submenu_option_feedbacks}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_feedbacks.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_feedbacks.title}}">{{$smarty.session.admin_notifications.badges.memberzone_feedbacks.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="feedbacks.php"><i class="icon icon-type-feedback"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-site"></i></i>{{$lang.users.submenu_option_feedbacks}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_feedbacks.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_feedbacks.title}}">{{$smarty.session.admin_notifications.badges.memberzone_feedbacks.count}}</em>{{/if}}</li>
		{{/if}}
		{{if $smarty.get.action!='change' && $page_name=='flags_messages.php'}}
			<li><span><i class="icon icon-type-feedback"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-content"></i></i>{{$lang.users.submenu_option_flags_messages}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_flag_messages.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_flag_messages.title}}">{{$smarty.session.admin_notifications.badges.memberzone_flag_messages.count}}</em>{{/if}}</li>
		{{else}}
			<li><a href="flags_messages.php"><i class="icon icon-type-feedback"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-content"></i></i>{{$lang.users.submenu_option_flags_messages}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_flag_messages.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_flag_messages.title}}">{{$smarty.session.admin_notifications.badges.memberzone_flag_messages.count}}</em>{{/if}}</li>
		{{/if}}
	</ul>
{{/if}}

{{if in_array('messages|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_messages"><i class="icon icon-type-message"></i>{{$lang.users.submenu_group_messages}}</h1>
	<ul id="users_messages">
		{{if in_array('messages|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='messages.php'}}
				<li><span><i class="icon icon-type-message"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_messages_list}}</span></li>
			{{else}}
				<li><a href="messages.php"><i class="icon icon-type-message"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_messages_list}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('messages|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='messages.php'}}
				<li><span><i class="icon icon-type-message"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_message}}</span></li>
			{{else}}
				<li><a href="messages.php?action=add_new"><i class="icon icon-type-message"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_message}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('playlists|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_playlists"><i class="icon icon-type-playlist"></i>{{$lang.users.submenu_group_playlists}}</h1>
	<ul id="users_playlists">
		{{if in_array('playlists|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='playlists.php'}}
				<li><span><i class="icon icon-type-playlist"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_playlists_list}}</span>{{if $smarty.session.admin_notifications.badges.memberzone_playlists.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_playlists.title}}">{{$smarty.session.admin_notifications.badges.memberzone_playlists.count}}</em>{{/if}}</li>
			{{else}}
				<li><a href="playlists.php"><i class="icon icon-type-playlist"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_playlists_list}}</a>{{if $smarty.session.admin_notifications.badges.memberzone_playlists.count>0}} <em title="{{$smarty.session.admin_notifications.badges.memberzone_playlists.title}}">{{$smarty.session.admin_notifications.badges.memberzone_playlists.count}}</em>{{/if}}</li>
			{{/if}}
		{{/if}}

		{{if in_array('playlists|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='playlists.php'}}
				<li><span><i class="icon icon-type-playlist"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_playlist}}</span></li>
			{{else}}
				<li><a href="playlists.php?action=add_new"><i class="icon icon-type-playlist"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_playlist}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('billing|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_billing"><i class="icon icon-type-billing"></i>{{$lang.users.submenu_group_paid_access}}</h1>
	<ul id="users_billing">
		{{if $smarty.get.action!='change' && $smarty.get.action!='change_provider' && $smarty.get.action!='add_new' && $page_name=='card_bill_configurations.php'}}
			<li><span><i class="icon icon-type-billing"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_card_billing}}</span></li>
		{{else}}
			<li><a href="card_bill_configurations.php"><i class="icon icon-type-billing"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_card_billing}}</a></li>
		{{/if}}

		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='bill_transactions.php'}}
			<li><span><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_billing_transactions}}</span></li>
		{{else}}
			<li><a href="bill_transactions.php"><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_billing_transactions}}</a></li>
		{{/if}}

		{{if in_array('billing|edit_all',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='bill_transactions.php'}}
				<li><span><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_billing_transaction}}</span></li>
			{{else}}
				<li><a href="bill_transactions.php?action=add_new"><i class="icon icon-type-transaction"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_billing_transaction}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('payouts|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="users_payouts"><i class="icon icon-type-payout"></i>{{$lang.users.submenu_group_payouts}}</h1>
	<ul id="users_payouts">
		{{if $smarty.get.action!='change' && $smarty.get.action!='add_new' && $page_name=='payouts.php'}}
			<li><span><i class="icon icon-type-payout"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_payouts_list}}</span></li>
		{{else}}
			<li><a href="payouts.php"><i class="icon icon-type-payout"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.users.submenu_option_payouts_list}}</a></li>
		{{/if}}

		{{if in_array('payouts|edit_all',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='payouts.php'}}
				<li><span><i class="icon icon-type-payout"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_payout}}</span></li>
			{{else}}
				<li><a href="payouts.php?action=add_new"><i class="icon icon-type-payout"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.users.submenu_option_add_payout}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
</div>