<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{$lang.system.language_code}}" lang="{{$lang.system.language_code}}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" href="{{$config.project_url}}/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="{{$config.project_url}}/favicon.ico" type="image/x-icon"/>

	<title>{{$lang.website_ui.debug_page_title}}</title>
	<style type="text/css">
		body {
			font-family: Verdana, sans-serif;
			font-size: 14px;
		}

		h1 {
			font-size: 18px;
		}

		h2 {
			font-size: 14px;
		}

		a {
			color: #00d;
			text-decoration: underline;
		}

		a:hover {
			text-decoration: none;
		}

		@font-face {
			font-family: 'default';
			src: url('{{$admin_url}}/styles/default/default.ttf?zbf9r9') format('truetype'),
			url('{{$admin_url}}/styles/default/default.woff?zbf9r9') format('woff'),
			url('{{$admin_url}}/styles/default/default.svg?zbf9r9#default') format('svg');
			font-weight: normal;
			font-style: normal;
			font-display: block;
		}

		a.expand,
		a.collapse {
			display: inline-block;
			color: #000;
			text-decoration: none;
			border-bottom: 1px solid #000;
			cursor: pointer;
		}

		a.expand:hover,
		a.collapse:hover {
			border-bottom-color: transparent;
		}

		a.expand:before,
		a.collapse:before {
			font-family: 'default' !important;
			speak: never;
			font-style: normal;
			font-weight: normal;
			font-variant: normal;
			text-transform: none;
			line-height: 1;

			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;

			padding-right: 5px;
			content: "\e938";
		}

		a.collapse:before {
			content: "\e939";
		}

		td {
			padding: 5px;
		}

		td:first-child {
			width: 15%;
		}

		td.label {
			background: #ddd;
		}

		td.error {
			color: #f00;
		}

		td.value {
			font-weight: bold;
		}

		td.empty {
			color: #777;
			font-weight: normal;
		}

		#expand_all.expand .on-collapse {
			display: none;
		}

		#expand_all.collapse .on-expand {
			display: none;
		}
	</style>
	<script type="text/javascript" data-cfasync="false">
		let visibleNodes = {};

		function toggleAllRows(button) {
			if (button.className.indexOf('expand') >= 0) {
				let nodes = document.querySelectorAll('.expand');
				for (let i = 0; i < nodes.length; i++) {
					if (nodes[i] != button) {
						nodes[i].dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true}));
					}
				}
				button.className = 'collapse';
			} else {
				let nodes = document.querySelectorAll('.collapse');
				for (let i = 0; i < nodes.length; i++) {
					if (nodes[i] != button) {
						nodes[i].dispatchEvent(new MouseEvent('click', {bubbles: true, cancelable: true}));
					}
				}
				button.className = 'expand';
			}
		}

		function toggleRow(prefix) {
			let button = document.getElementById(prefix + '-toggler');
			if (button.className.indexOf('expand') >= 0) {
				visibleNodes[prefix] = true;
				toggleRowRecursive(prefix, true);
				button.className = 'collapse';
			} else {
				visibleNodes[prefix] = null;
				toggleRowRecursive(prefix, false);
				button.className = 'expand';
			}
		}

		function toggleRowRecursive(prefix, visible) {
			let i = 0;
			if (visible && !visibleNodes[prefix]) {
				visible = false;
			}
			while (true) {
				if (document.getElementById(prefix + '-' + i)) {
					toggleRowRecursive(prefix + '-' + i, visible);
					if (visible) {
						document.getElementById(prefix + '-' + i).style.display = 'table-row';
					} else {
						document.getElementById(prefix + '-' + i).style.display = 'none';
					}
					i++;
				} else {
					break;
				}
			}
		}

		document.addEventListener('DOMContentLoaded', function() {
			let search = new URLSearchParams(window.location.search);
			if (search.get('scroll')) {
				let object = document.querySelector('[data-scroll-id="' + search.get('scroll') + '"]');
				if (object) {
					let rect = object.getBoundingClientRect(),
						scrollTop = window.pageYOffset || document.documentElement.scrollTop;
					window.scrollTo(0, Math.max(0, rect.top + scrollTop - 30));
				}
			}
		});
	</script>
</head>
<body>
<h1>{{$lang.website_ui.debug_header_page_info}} | <a id="expand_all" class="expand" onclick="toggleAllRows(this)"><span class="on-expand">{{$lang.website_ui.debug_action_expand_all}}</span><span class="on-collapse">{{$lang.website_ui.debug_action_collapse_all}}</span></a></h1>
	<table>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_page_status}}:</td>
			<td {{if $page_status=='404'}}class="error"{{/if}}>{{$page_status}}</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_page_id}}:</td>
			<td>{{$page_external_id}}</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_page_name}}:</td>
			<td>
				<a href="{{$admin_url}}/project_pages.php?action=change&amp;item_id={{$page_external_id}}" target="_blank">{{$page_name}}</a>
			</td>
		</tr>
		{{if $config.locale!=''}}
			<tr>
				<td class="label">{{$lang.website_ui.debug_field_locale}}:</td>
				<td>{{$config.locale}}</td>
			</tr>
		{{elseif $config.theme_locale!=''}}
			<tr>
				<td class="label">{{$lang.website_ui.debug_field_locale}}:</td>
				<td>{{$config.theme_locale}}</td>
			</tr>
		{{/if}}
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_page_components}}:</td>
			<td>
				{{if count($page_includes)>0}}
					{{foreach name="data2" from=$page_includes|smarty:nodefaults key="key2" item="item2"}}
						<a href="{{$admin_url}}/project_pages_components.php?action=change&amp;item_id={{$key2}}" target="_blank">{{$key2}}</a>{{if !$smarty.foreach.data2.last}},{{/if}}
					{{/foreach}}
				{{else}}
					{{$lang.website_ui.debug_no_components}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_request_uri}}:</td>
			<td>{{$page_request_uri}}</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_http_params}}:</td>
			<td>
				{{if count($page_http_params)>0}}
					<table>
						{{foreach name="data2" from=$page_http_params|smarty:nodefaults item="item2"}}
							<tr>
								<td class="label">{{$item2.name}}</td>
								<td class="value">
									{{$item2.value}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.website_ui.debug_no_parameters}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_htaccess_rules}}:</td>
			<td>
				{{if count($htaccess_rules)>0}}
					<table>
						{{foreach name="data2" from=$htaccess_rules|smarty:nodefaults item="item2"}}
							<tr>
								<td class="label {{if $item2.is_current==1}}value{{/if}}">{{$item2.rule}}</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.website_ui.debug_no_data}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_session_values}}:</td>
			<td>
				{{if count($session_values)>0}}
					<table>
						{{foreach name="data2" from=$session_values|smarty:nodefaults item="item2"}}
							{{math assign=padding_left equation="5+20*x" x=$item2.level}}
							<tr id="{{$item2.row_id}}" {{if $item2.level>0}}style="display: none"{{/if}}>
								<td class="label" style="padding-left: {{$padding_left}}px;">
									{{$item2.key}}
								</td>
								<td class="value {{if $item2.value==''}}empty{{/if}}" style="padding-left: {{$padding_left}}px;">
									{{if $item2.is_expandable==1}}
										<a id="{{$item2.row_id}}-toggler" class="expand" onclick="toggleRow('{{$item2.row_id}}')">{{$item2.value}}</a>
									{{else}}
										{{$item2.value|default:$lang.website_ui.debug_no_value}}
									{{/if}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.website_ui.debug_no_data}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_runtime_params}}:</td>
			<td>
				{{if count($runtime_params)>0}}
					<table>
						{{foreach name="data2" from=$runtime_params|smarty:nodefaults item="item2" key="key2"}}
							<tr>
								<td class="label">{{$key2}}</td>
								<td class="value">
									{{$item2}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.website_ui.debug_no_data}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="label">{{$lang.website_ui.debug_field_lang}}:</td>
			<td>
				{{if count($localization)>0}}
					<table>
						{{foreach name="data2" from=$localization|smarty:nodefaults item="item2"}}
							{{math assign=padding_left equation="5+20*x" x=$item2.level}}
							<tr id="{{$item2.row_id}}" {{if $item2.level>0}}style="display: none"{{/if}}>
								<td class="label" style="padding-left: {{$padding_left}}px;">
									{{$item2.key}}
								</td>
								<td class="value {{if $item2.value==''}}empty{{/if}}" style="padding-left: {{$padding_left}}px;">
									{{if $item2.is_expandable==1}}
										<a id="{{$item2.row_id}}-toggler" class="expand" onclick="toggleRow('{{$item2.row_id}}')">{{$item2.value}}</a>
									{{else}}
										{{$item2.value|default:$lang.website_ui.debug_no_value}}
									{{/if}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.website_ui.debug_no_data}}
				{{/if}}
			</td>
		</tr>
	</table>
	<h1>{{$lang.website_ui.debug_header_page_contents}}</h1>
	{{if count($blocks)>0}}
		{{foreach from=$blocks|smarty:nodefaults item="item"}}
			<h2 data-scroll-id="{{if $item.is_global==1}}$global{{else}}{{$page_external_id}}{{/if}}/{{$item.block_id}}_{{$item.block_name_mod}}">
				<a href="{{$admin_url}}/project_pages.php?action=change_block&amp;item_id={{if $item.is_global==1}}$global{{else}}{{$page_external_id}}{{/if}}||{{$item.block_id}}||{{$item.block_name_mod}}&amp;item_name={{$item.block_name|escape}}" target="_blank">{{$item.block_name}}{{if $item.is_global==1}} ({{$lang.website_ui.debug_global_block}}){{/if}}</a>
			</h2>
			<table>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_name}}:</td>
					<td>
						<a href="{{$admin_url}}/project_pages.php?action=change_block&amp;item_id={{if $item.is_global==1}}$global{{else}}{{$page_external_id}}{{/if}}||{{$item.block_id}}||{{$item.block_name_mod}}&amp;item_name={{$item.block_name|escape}}" target="_blank">{{$item.block_name}}{{if $item.is_global==1}} ({{$lang.website_ui.debug_global_block}}){{/if}}</a>
					</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_type}}:</td>
					<td>
						<a href="{{$admin_url}}/project_blocks.php?action=show_long_desc&amp;block_id={{$item.block_id}}" target="_blank">{{$item.block_id}}{{if $item.is_global==1}} [G]{{/if}}</a>
					</td>
				</tr>
				{{if $item.is_global==1}}
					<tr>
						<td class="label">{{$lang.website_ui.debug_field_storage_key}}:</td>
						<td>$global_storage.{{$item.block_uid}}</td>
					</tr>
				{{else}}
					<tr>
						<td class="label">{{$lang.website_ui.debug_field_storage_key}}:</td>
						<td>$storage.{{$item.block_uid}}</td>
					</tr>
				{{/if}}
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_memory_usage}}:</td>
					<td>{{$item.memory_usage|number_format:0:".":" "}}</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_time_usage}}:</td>
					<td>{{$item.time_usage|number_format:4:".":" "}}</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_parameters}}:</td>
					<td>
						{{if count($item.params)>0}}
							<table>
								{{foreach name="data2" from=$item.params|smarty:nodefaults key="key2" item="item2"}}
									<tr>
										<td class="label">{{$key2}}</td>
										<td class="value">
											{{if $item2==''}}
												{{$lang.website_ui.debug_field_block_parameters_enabled}}
											{{else}}
												{{$item2}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							</table>
						{{else}}
							{{$lang.website_ui.debug_no_parameters}}
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_components}}:</td>
					<td>
						{{if count($item.block_includes)>0}}
							{{foreach name="data2" from=$item.block_includes|smarty:nodefaults key="key2" item="item2"}}
								<a href="{{$admin_url}}/project_pages_components.php?action=change&amp;item_id={{$key2}}" target="_blank">{{$key2}}</a>{{if !$smarty.foreach.data2.last}},{{/if}}
							{{/foreach}}
						{{else}}
							{{$lang.website_ui.debug_no_components}}
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_storage}}:</td>
					<td>
						{{if count($item.storage)>0}}
							<table>
								{{foreach name="data2" from=$item.storage|smarty:nodefaults item="item2"}}
									{{math assign=padding_left equation="5+20*x" x=$item2.level}}
									<tr id="{{$item2.row_id}}" {{if $item2.level>0}}style="display: none"{{/if}}>
										<td class="label" style="padding-left: {{$padding_left}}px;">
											{{if $item.is_global==1}}
												$global_storage.{{$item2.key}}
											{{else}}
												$storage.{{$item2.key}}
											{{/if}}
										</td>
										<td class="value {{if $item2.value==''}}empty{{/if}}" style="padding-left: {{$padding_left}}px;">
											{{if $item2.is_expandable==1}}
												<a id="{{$item2.row_id}}-toggler" class="expand" onclick="toggleRow('{{$item2.row_id}}')">{{$item2.value}}</a>
											{{else}}
												{{$item2.value|default:$lang.website_ui.debug_no_value}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							</table>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="label">{{$lang.website_ui.debug_field_block_variables}}:</td>
					<td>
						{{if count($item.template_vars)>0}}
							<table>
								{{foreach name="data2" from=$item.template_vars|smarty:nodefaults item="item2"}}
									{{math assign=padding_left equation="5+20*x" x=$item2.level}}
									<tr id="{{$item2.row_id}}" {{if $item2.level>0}}style="display: none"{{/if}}>
										<td class="label" style="padding-left: {{$padding_left}}px;">${{$item2.key}}</td>
										<td class="value {{if $item2.value==''}}empty{{/if}}" style="padding-left: {{$padding_left}}px;">
											{{if $item2.is_expandable==1}}
												<a id="{{$item2.row_id}}-toggler" class="expand" onclick="toggleRow('{{$item2.row_id}}')">{{$item2.value}}</a>
											{{else}}
												{{$item2.value|default:$lang.website_ui.debug_no_value}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							</table>
						{{/if}}
					</td>
				</tr>
				{{if $item.status_code!=''}}
					<tr>
						<td class="label">{{$lang.website_ui.debug_field_block_status}}:</td>
						<td {{if $item.status_code=='404'}}class="error"{{/if}}>
							{{$item.status_code}}
						</td>
					</tr>
				{{/if}}
			</table>
		{{/foreach}}
	{{else}}
		{{$lang.website_ui.debug_no_blocks}}
	{{/if}}
</body>
</html>