<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{$lang.system.language_code}}" {{if $smarty.session.userdata.night_mode==1}}class="dark"{{/if}}>
<head>
	<title>{{$page_title}} / {{$config.project_version}}</title>
	<link type="text/css" rel="stylesheet" href="styles/{{$smarty.session.userdata.skin}}.css?v={{$version_hash}}"/>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>

	<script type="text/javascript" data-cfasync="false" src="js/config.php?v={{$version_hash}}"></script>
	<script type="text/javascript" data-cfasync="false" src="js/admin.js?v={{$version_hash}}"></script>
</head>
<body class="{{if $smarty.session.userdata.is_expert_mode==1}}expert-mode{{/if}} {{if $smarty.session.userdata.is_hide_forum_hints==1}}hide-forum-links{{/if}}">
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
									<span class="value">{{$smarty.session.server_time|date_format:$smarty.session.userdata.full_date_format}}</span>
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
				<div id="license_page" class="highlighted_text">
					{{if $curl_error==1}}
						{{$lang.validation.rental_curl_error}}
						<br/>
						{{$curl_details|replace:"\n":"<br/>"}}
					{{elseif $exec_error==1}}
						{{$lang.validation.rental_exec_error}}
					{{elseif $folder_error==1}}
						{{$lang.validation.rental_folder_error}}
					{{elseif $locking_error==1}}
						{{$lang.validation.rental_locking_error}}
					{{else}}
						{{$lang.validation.rental_stopped|smarty:nodefaults}}
					{{/if}}
				</div>
			</div>
		</div>
	</div>
</body>
</html>