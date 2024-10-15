<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<title>{{$lang.login.page_tilte}}{{if $show_version==1}} / {{$config.project_version}}{{/if}}</title>
	<link type="text/css" rel="stylesheet" href="styles/default.css?v={{$version_hash}}"/>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>

	<script type="text/javascript" data-cfasync="false" src="js/config.php?v={{$version_hash}}"></script>
	<script type="text/javascript" data-cfasync="false" src="js/admin.js?v={{$version_hash}}"></script>
</head>
<body>
	<div id="content">
		<div id="layout_root">
			<div id="layout_main_login">
				<form id="login" action="log_in.php" method="post" class="de" name="{{$smarty.now}}" data-editor-name="login">
					<table class="de_editor">
						<tr class="err_list {{if $session_error==''}}hidden{{/if}}">
							<td colspan="2">
								<div class="err_header">{{if $session_error!=''}}{{$lang.validation.common_header}}{{/if}}</div>
								<div class="err_content">
									{{if $session_error!=''}}
										<ul>
											<li>{{$session_error}}</li>
										</ul>
									{{/if}}
								</div>
							</td>
						</tr>
						<tr>
							<td class="de_label">{{$lang.login.field_username}}</td>
							<td class="de_control"><input type="text" name="username"/></td>
						</tr>
						<tr>
							<td class="de_label">{{$lang.login.field_password}}</td>
							<td class="de_control"><input type="password" name="password"/></td>
						</tr>
						<tr>
							<td class="de_label">{{$lang.login.field_ip}}</td>
							<td class="de_control"><span>{{$ip_address}}</span></td>
						</tr>
					</table>
					<div class="de_action_group">
						<input type="hidden" name="action" value="login"/>
						<input type="submit" value="{{$lang.login.btn_log_on}}"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>