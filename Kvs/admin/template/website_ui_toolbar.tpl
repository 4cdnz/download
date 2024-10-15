<style>
	#kvs_toolbar,
	#kvs_toolbar *,
	#kvs_toolbar *:after,
	#kvs_toolbar *:before {
		font: 14px/16px Verdana, sans-serif;
		text-align: left;
		box-sizing: border-box;
		text-decoration: none;
		font-weight: normal;
		color: #ffffff;
		z-index: 10000;
		padding: 0;
		margin: 0;
		border: 0;
	}

	#kvs_toolbar {
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: fixed;
		z-index: 10000;
		padding: 5px;
		bottom: 0;
		left: 0;
		right: 0;
		background: #2c434e;
		background: linear-gradient(to right, rgba(44, 67, 78, 0.95) 0%, rgba(45, 55, 71, 0.95) 100%);
		border-top: 1px solid #cccccc;
	}

	@media screen and (max-width: 700px) {
		#kvs_toolbar {
			display: none;
		}
	}

	#kvs_toolbar svg {
		display: block;
	}


	#kvs_toolbar_title {
		padding: 0 0 0 10px;
		min-width: 90px;
	}

	#kvs_toolbar_page_info {
		position: relative;
		margin: 0 0 0 20px;
		border: 1px #ffffff solid;
		border-radius: 5px;
		background-color: transparent;
		background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 255 255' stroke='white' fill='white' width='10' height='10' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='0,191.25 127.5,63.75 255,191.25'%3E%3C/polygon%3E%3C/svg%3E");
		background-repeat: no-repeat;
		background-position: right 10px top 50%;
		background-size: auto;
		cursor: pointer;
	}

	#kvs_toolbar_page_info_title {
		padding: 5px 30px 5px 5px;
		white-space: nowrap;
	}

	#kvs_toolbar_page_info:hover {
		border-color: #888888;
		background-color: #303D44;
	}

	#kvs_toolbar_page_info.kvs-toolbar-open {
		border-color: #888888;
		background-color: #303D44;
		border-top-left-radius: 0;
		border-top-right-radius: 0;
		background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 255 255' stroke='white' fill='white' width='10' height='10' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='0,63.75 127.5,191.25 255,63.75'%3E%3C/polygon%3E%3C/svg%3E");
	}

	#kvs_toolbar_page_info strong {
		font-weight: bold;
	}

	#kvs_toolbar_page_info_contents {
		display: none;
		position: absolute;
		background: #303D44;
		border: 1px #888888 solid;
		left: -1px;
		bottom: 26px;
		list-style: none;
		padding: 0;
		border-radius: 5px 5px 5px 0;
		white-space: nowrap;
		max-height: 350px;
		overflow-y: scroll;
		cursor: default;
	}

	#kvs_toolbar_page_info.kvs-toolbar-open #kvs_toolbar_page_info_contents {
		display: block;
	}

	#kvs_toolbar_page_info_contents li {
		position: relative;
		padding: 8px 40px 8px 8px;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-2,
	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-3,
	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-4,
	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-5,
	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-6,
	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-7 {
		margin-left: 12px;
		border-left: 1px #888888 solid;
		padding-left: 20px;
		background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAABCAIAAABc7mk1AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAARSURBVBhXY+zo6GAgFTAwAABU5gGaTf+kagAAAABJRU5ErkJggg==') no-repeat left 50%;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-3 {
		margin-left: 39px;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-4 {
		margin-left: 66px;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-5 {
		margin-left: 93px;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-6 {
		margin-left: 120px;
	}

	#kvs_toolbar_page_info_contents li.kvs-toolbar-level-7 {
		margin-left: 120px;
	}

	#kvs_toolbar_page_info_contents li[data-hidden] {
		display: none;
	}

	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable {
		content: '';
		display: block;
		position: absolute;
		left: -8px;
		top: 9px;
		width: 15px;
		height: 15px;
		border: 1px solid #888888;
		background: #303D44;
		cursor: pointer;
	}

	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable:after {
		content: '';
		display: none;
		position: absolute;
		left: 6px;
		top: 3px;
		width: 1px;
		height: 7px;
		background: #888888;
	}

	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable:before {
		content: '';
		display: block;
		position: absolute;
		top: 6px;
		left: 3px;
		height: 1px;
		width: 7px;
		background: #888888;
	}

	#kvs_toolbar_page_info_contents [data-collapsed] em.kvs-toolbar-expandable:after {
		display: block;
	}

	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable:hover {
		border-color: #dddddd;
	}

	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable:hover:before,
	#kvs_toolbar_page_info_contents em.kvs-toolbar-expandable:hover:after {
		background: #dddddd;
	}

	#kvs_toolbar_page_info_contents li a.kvs-toolbar-ico {
		display: inline-block;
		width: 20px;
		height: 20px;
		padding: 2px;
		border: 1px solid #dddddd;
		vertical-align: middle;
		margin-left: 10px;
	}

	#kvs_toolbar_page_info_contents li a.kvs-toolbar-ico + a.kvs-toolbar-ico {
		margin-left: 5px;
	}

	#kvs_toolbar_page_info_contents li a.kvs-toolbar-ico:hover {
		border-color: #888888;
	}

	#kvs_toolbar_page_info_contents li a.kvs-toolbar-ico svg {
		fill: #dddddd;
		width: 14px;
		height: 14px;
	}

	#kvs_toolbar_page_info_contents li a.kvs-toolbar-ico:hover svg {
		fill: #888888;
	}

	#kvs_toolbar_page_info_contents li strong {
		font-weight: bold;
	}

	#kvs_toolbar_object_highlighter {
		position: absolute;
		z-index: 9999;
		background: rgba(255, 0, 0, 0.3);
	}

	#kvs_toolbar_object_highlighter a {
		display: block;
		position: absolute;
		padding: 5px;
		text-decoration: underline;
		background: rgba(255, 255, 255, 0.6);
		color: #ff0000;
		font: 14px/16px Verdana, sans-serif;
		font-weight: bold;
	}

	#kvs_toolbar_object_highlighter a:hover {
		text-decoration: none;
		background: rgba(255, 255, 255, 0.9);
	}

	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-edit {
		right: 26px;
		top: 0;
		padding: 5px;
	}

	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-close {
		width: 26px;
		height: 26px;
		right: 0;
		top: 0;
		border-left: 1px solid rgba(177, 60, 60, 0.6);
	}

	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-close:before,
	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-close:after {
		position: absolute;
		left: 12px;
		top: 6px;
		content: ' ';
		height: 15px;
		width: 2px;
		background-color: #ff0000;
	}

	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-close:before {
		transform: rotate(45deg);
	}

	#kvs_toolbar_object_highlighter a.kvs-toolbar-highlighter-close:after {
		transform: rotate(-45deg);
	}

	#kvs_toolbar_caching_switch {
		position: relative;
		margin: 0 0 0 20px;
		padding: 0 0 0 20px;
		cursor: pointer;
	}

	#kvs_toolbar_caching_switch:before {
		display: block;
		position: absolute;
		content: '';
		border: 1px #ffffff solid;
		border-radius: 3px;
		width: 16px;
		height: 16px;
		left: 0;
		top: calc(50% - 8px);
	}

	#kvs_toolbar_caching_switch[data-checked]:after {
		display: block;
		position: absolute;
		content: '';
		background: #ffffff;
		width: 10px;
		height: 10px;
		left: 3px;
		top: 3px;
	}

	#kvs_toolbar_caching_switch:hover:before {
		border-color: #888888;
		background-color: #303D44;
	}

	#kvs_toolbar .kvs-toolbar-separator {
		display: block;
		width: 1px;
		height: 32px;
		margin: 0 0 0 20px;
		background: rgba(160, 160, 160, 0.3);
	}

	#kvs_toolbar .kvs-toolbar-context-object {
		margin: 0 0 0 20px;
	}

	#kvs_toolbar .kvs-toolbar-context-object a {
		text-decoration: underline;
	}

	#kvs_toolbar .kvs-toolbar-context-object a:hover {
		text-decoration: none;
	}

	#kvs_toolbar .kvs-toolbar-context-object a svg {
		display: inline-block;
		vertical-align: top;
		margin-right: 5px;
		fill: #dddddd;
	}

	@media screen and (max-width: 1000px) {
		.kvs-toolbar-context-object {
			display: none;
		}
	}

	#kvs_toolbar_button_close {
		position: relative;
		min-width: 26px;
		width: 26px;
		height: 26px;
		cursor: pointer;
	}

	#kvs_toolbar_button_close:before,
	#kvs_toolbar_button_close:after {
		position: absolute;
		left: 12px;
		top: 3px;
		content: ' ';
		height: 18px;
		width: 2px;
		background-color: #dddddd;
	}

	#kvs_toolbar_button_close:before {
		transform: rotate(45deg);
	}

	#kvs_toolbar_button_close:after {
		transform: rotate(-45deg);
	}

	#kvs_toolbar_button_close:hover:before,
	#kvs_toolbar_button_close:hover:after {
		background-color: #888888;
	}

	#kvs_toolbar_spacer {
		flex-grow: 1;
	}
</style>
<script>
	function kvs_toolbar_highlight(highlightId, editorLink, title) {
		var markerStart,
			markerEnd,
			iterator = document.createNodeIterator(document.body, NodeFilter.SHOW_COMMENT),
			node,
			highlighter,
			editor,
			closer,
			position = function (element) {
				if (!element) return null;
				var rect = element.getBoundingClientRect(),
					scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
					scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				return {top: rect.top + scrollTop, left: rect.left + scrollLeft, width: element.offsetWidth, height: element.offsetHeight};
			};

		while ((node = iterator.nextNode())) {
			if (node.nodeValue == 'start/' + highlightId) {
				markerStart = position(node.nextElementSibling);
			} else if (node.nodeValue == 'end/' + highlightId) {
				markerEnd = position(node.previousElementSibling);
			}
		}
		if (markerStart && markerEnd) {
			highlighter = document.getElementById('kvs_toolbar_object_highlighter');
			if (!highlighter) {
				highlighter = document.createElement('DIV');
				highlighter.id = 'kvs_toolbar_object_highlighter';
				document.body.appendChild(highlighter);
			}
			highlighter.innerHTML = '';

			if (editorLink) {
				editor = document.createElement('A');
				editor.className = 'kvs-toolbar-highlighter-edit';
				editor.target = '_blank';
				editor.href = editorLink;
				editor.innerHTML = '{{$lang.website_ui.toolbar_link_edit|replace:"'":"\'"}}';
				highlighter.appendChild(editor);
			}

			closer = document.createElement('A');
			closer.className = 'kvs-toolbar-highlighter-close';
			closer.onclick = function() {
				if (highlighter && highlighter.parentNode) {
					highlighter.parentNode.removeChild(highlighter);
				}
				if (toolbarTitle) {
					toolbarTitle.innerHTML = '{{$lang.website_ui.toolbar_page|replace:"%1%":$page_definition.title|replace:"%2%":$page_definition.display_mode|replace:"%3%":$page_definition.exec_time|replace:"'":"\'"}}';
				}
			};
			highlighter.appendChild(closer);

			var left = Math.min(markerStart.left, markerEnd.left),
				top = Math.min(markerStart.top, markerEnd.top),
				width = Math.max(markerStart.left + markerStart.width, markerEnd.left + markerEnd.width) - left,
				height = Math.max(markerStart.top + markerStart.height, markerEnd.top + markerEnd.height) - top,
				toolbar = document.getElementById('kvs_toolbar_page_info'),
				toolbarTitle = document.getElementById('kvs_toolbar_page_info_title')
			;
			if (width == 0 || height == 0) {
				alert('{{$lang.website_ui.toolbar_link_show_empty_css|replace:"'":"\'"}}');
			} else {
				highlighter.style.left = left + 'px';
				highlighter.style.top = top + 'px';
				highlighter.style.width = width + 'px';
				highlighter.style.height = height + 'px';
				if (toolbar) {
					setTimeout(function () {
						toolbar.classList.remove('kvs-toolbar-open');
					}, 1);
				}
				window.scrollTo(0, Math.max(0, top - 30));
				if (title && toolbarTitle) {
					toolbarTitle.innerHTML = title;
				}
			}
		} else {
			alert('{{$lang.website_ui.toolbar_link_show_empty_template|replace:"'":"\'"}}');
		}
	}

	function kvs_toolbar_toggle_item(item, state) {
		if (state) {
			item.removeAttribute('data-collapsed');
		} else {
			item.setAttribute('data-collapsed', '');
		}

		document.querySelectorAll('#kvs_toolbar_page_info_contents [data-parent-id="' + item.getAttribute('data-id') + '"]').forEach(function(item) {
			if (state) {
				item.removeAttribute('data-hidden');
			} else {
				item.setAttribute('data-hidden', '');
			}
			kvs_toolbar_toggle_item(item, state ? !item.hasAttribute('data-collapsed') : false);
		});
	}

	function kvs_toolbar_click(event) {
		if (event.type != 'click') {
			return;
		}

		var obj = event.target;
		var contents = document.getElementById('kvs_toolbar_page_info');
		if (contents) {
			var isParent = false, parentNode = obj;
			while (parentNode) {
				if (parentNode == contents) {
					isParent = true;
					break;
				}
				parentNode = parentNode.parentNode;
			}
			if (contents.classList.contains('kvs-toolbar-open')) {
				if (!isParent || obj.id == 'kvs_toolbar_page_info_title') {
					contents.classList.remove('kvs-toolbar-open');
				}
			} else {
				if (isParent) {
					contents.classList.add('kvs-toolbar-open');
				}
			}
		}

		if (obj.hasAttribute('data-expand-id')) {
			var line = document.querySelector('#kvs_toolbar_page_info_contents [data-id="' + obj.getAttribute('data-expand-id') + '"]');
			if (line) {
				kvs_toolbar_toggle_item(line, line.hasAttribute('data-collapsed'));
			}
		}

		var cookieDomain = location.host;
		if (cookieDomain.indexOf('www.') >= 0) {
			cookieDomain = cookieDomain.replace('www.', '');
		}

		if (obj.id == 'kvs_toolbar_caching_switch') {
			var selected = !obj.hasAttribute('data-checked');
			if (selected) {
				obj.setAttribute('data-checked', '');
				document.cookie = 'kt_admin_action=enable_caching; samesite=lax; path=/; domain=.' + cookieDomain;
			} else {
				obj.removeAttribute('data-checked');
				document.cookie = 'kt_admin_action=disable_caching; samesite=lax; path=/; domain=.' + cookieDomain;
			}
			window.location.reload();
		} else if (obj.id == 'kvs_toolbar_button_close') {
			document.cookie = 'kt_admin_action=disable_toolbar; samesite=lax; path=/; domain=.' + cookieDomain;
			alert('{{$lang.website_ui.toolbar_button_close_hint|replace:"'":"\'"}}');
			window.location.reload();
		}
	}

	document.addEventListener('click', kvs_toolbar_click);
</script>
<div id="kvs_toolbar">
	<a href="{{$admin_url}}/start.php" target="_blank">
		<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="28" height="32" viewBox="0 0 28 32">
			<title>KVS</title>
			<path fill="#ffffff" d="M13.82 31.98l-6.958-4.019v-23.805l6.958-4.017 9.539 5.507-15.888 9.946v0.060l16.376 10.54zM0.032 24.019v-15.92l6.035-3.485v22.889z"></path>
			<path fill="#227ccd" d="M17.055 10.415l7.064-4.394 3.453 2.039v16.013l-2.982 1.727-7.535-4.867z"></path>
		</svg>
	</a>
	<a id="kvs_toolbar_title" href="{{$admin_url}}/start.php" target="_blank">KVS Admin<br/>Toolbar</a>
	<div class="kvs-toolbar-separator"></div>
	<div id="kvs_toolbar_page_info">
		<div id="kvs_toolbar_page_info_title">{{$lang.website_ui.toolbar_page|replace:"%1%":$page_definition.title|replace:"%2%":$page_definition.display_mode|replace:"%3%":$page_definition.exec_time}}</div>
		<ul id="kvs_toolbar_page_info_contents">
			<li>
				{{if $smarty.session.user_id>0}}
					{{$lang.website_ui.toolbar_user|replace:"%1%":$smarty.session.display_name}}
					{{if in_array('users|view',$smarty.session.permissions)}}
						<a class="kvs-toolbar-ico" href="{{$admin_url}}/users.php?action=change&amp;item_id={{$smarty.session.user_id}}" target="_blank">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
								<title>{{$lang.website_ui.toolbar_link_edit}}</title>
								<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"></polygon>
								<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"></path>
							</svg>
						</a>
					{{/if}}
					<a class="kvs-toolbar-ico" href="{{$smarty.server.REQUEST_URI}}{{if $smarty.server.REQUEST_URI|strpos:'?'!==false}}&amp;{{else}}?{{/if}}debug=true&amp;scroll=session" target="_blank">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
							<title>{{$lang.website_ui.toolbar_link_structure}}</title>
							<path d="m180.667 30.333h150.667v150.667h-150.667z"/>
							<path d="m0 331h150.667v150.667h-150.667z"/>
							<path d="m180.667 331h150.667v150.667h-150.667z"/>
							<path d="m90.333 271h150.667v30h30v-30h150.667v30h30v-60h-180.667v-30h-30v30h-180.667v60h30z"/>
							<path d="m361.333 331h150.667v150.667h-150.667z"/>
						</svg>
					</a>
				{{else}}
					{{$lang.website_ui.toolbar_guest}}
				{{/if}}
			</li>
			<li>
				{{$lang.website_ui.toolbar_page|replace:"%1%":$page_definition.title|replace:"%2%":$page_definition.display_mode|replace:"%3%":$page_definition.exec_time}}
				{{if $page_definition.external_id}}
					{{if in_array('website_ui|view',$smarty.session.permissions)}}
						<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_pages.php?action=change&amp;item_id={{$page_definition.external_id}}" target="_blank">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
								<title>{{$lang.website_ui.toolbar_link_edit}}</title>
								<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"></polygon>
								<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"></path>
							</svg>
						</a>
					{{/if}}
					<a class="kvs-toolbar-ico" href="{{$smarty.server.REQUEST_URI}}{{if $smarty.server.REQUEST_URI|strpos:'?'!==false}}&amp;{{else}}?{{/if}}debug=true" target="_blank">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
							<title>{{$lang.website_ui.toolbar_link_structure}}</title>
							<path d="m180.667 30.333h150.667v150.667h-150.667z"/>
							<path d="m0 331h150.667v150.667h-150.667z"/>
							<path d="m180.667 331h150.667v150.667h-150.667z"/>
							<path d="m90.333 271h150.667v30h30v-30h150.667v30h30v-60h-180.667v-30h-30v30h-180.667v60h30z"/>
							<path d="m361.333 331h150.667v150.667h-150.667z"/>
						</svg>
					</a>
				{{/if}}
			</li>
			{{foreach item="item" from=$page_definition.structure|smarty:nodefaults}}
				<li class="kvs-toolbar-level-{{$item.level}}" {{if $item.parent_id}}data-parent-id="{{$item.parent_id}}"{{/if}} {{if $item.level>2}}data-hidden{{/if}} data-id="{{$item.id}}" {{if $item.has_structure==1 && $item.level>=2}}data-collapsed{{/if}}>
					{{if $item.has_structure==1}}<em class="kvs-toolbar-expandable" data-expand-id="{{$item.id}}"></em>{{/if}}
					{{if $item.page_component_id}}
						{{if $item.page_component_id=='include_header_general.tpl'}}
							{{$lang.website_ui.toolbar_component_header}}
						{{elseif $item.page_component_id=='include_footer_general.tpl'}}
							{{$lang.website_ui.toolbar_component_footer}}
						{{else}}
							{{$lang.website_ui.toolbar_component|replace:"%1%":$item.page_component_id}}
						{{/if}}
						{{if in_array('website_ui|view',$smarty.session.permissions)}}
							<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_pages_components.php?action=change&amp;item_id={{$item.page_component_id}}" target="_blank">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
									<title>{{$lang.website_ui.toolbar_link_edit}}</title>
									<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"/>
									<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/>
								</svg>
							</a>
						{{/if}}
					{{elseif $item.spot_id}}
						{{assign var="title" value=$lang.website_ui.toolbar_spot|replace:"%1%":$item.title|smarty:nodefaults}}
						{{$title}}
						{{if in_array('advertising|view',$smarty.session.permissions)}}
							<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_spots.php?action=change_spot&amp;item_id={{$item.spot_id}}" target="_blank">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
									<title>{{$lang.website_ui.toolbar_link_edit}}</title>
									<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"/>
									<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/>
								</svg>
							</a>
						{{/if}}
						<a class="kvs-toolbar-ico" onclick="kvs_toolbar_highlight('spot/{{$item.spot_id}}', '{{if in_array('advertising|view',$smarty.session.permissions)}}{{$admin_url}}/project_spots.php?action=change_spot&amp;item_id={{$item.spot_id}}{{/if}}', '{{$title}}')">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 408 408">
								<title>{{$lang.website_ui.toolbar_link_show}}</title>
								<path d="M397.9,83c5.5,0,10-4.5,10.1-10V10.1C408,4.5,403.5,0,397.9,0H335c-5.5,0-10,4.5-10,10.1V32H83V10.1c0-5.5-4.5-10-10-10.1 H10.1C4.6,0,0.1,4.5,0,10.1V73c0,5.5,4.5,10,10.1,10H32v242H10.1c-5.5,0-10,4.5-10.1,10v62.9c0,5.5,4.5,10,10.1,10.1H73 c5.5,0,10-4.5,10-10.1V376h242v21.9c0,5.5,4.5,10,10,10.1h62.9c5.5,0,10-4.5,10.1-10.1V335c0-5.5-4.5-10-10.1-10H376V83H397.9z M20,63V20h43v43H20z M63,388H20v-43h43V388z M325,335v21H83v-21c0-5.5-4.5-10-10-10H52V83h21c5.5,0,10-4.5,10-10V52h242v21 c0,5.5,4.5,10,10,10h21v242h-21C329.5,325,325,329.5,325,335z M388,345v43h-43v-43H388z M345,63V20h43v43H345z"/>
							</svg>
						</a>
					{{elseif $item.ad_id}}
						{{$lang.website_ui.toolbar_advertising|replace:"%1%":$item.title}}
						{{if in_array('advertising|view',$smarty.session.permissions)}}
							<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_spots.php?action=change&amp;item_id={{$item.ad_id}}" target="_blank">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
									<title>{{$lang.website_ui.toolbar_link_edit}}</title>
									<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"/>
									<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/>
								</svg>
							</a>
						{{/if}}
					{{elseif $item.block_uid}}
						{{assign var="title" value=$lang.website_ui.toolbar_page_block|replace:"%1%":$item.title|replace:"%2%":$item.display_mode|replace:"%3%":$item.exec_time|smarty:nodefaults}}
						{{$title}}
						{{if in_array('website_ui|view',$smarty.session.permissions)}}
							<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_pages.php?action=change_block&amp;item_id={{$page_definition.external_id}}||{{$item.block_id}}||{{$item.block_name}}&amp;item_name={{$item.title}}" target="_blank">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
									<title>{{$lang.website_ui.toolbar_link_edit}}</title>
									<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"/>
									<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/>
								</svg>
							</a>
						{{/if}}
						<a class="kvs-toolbar-ico" href="{{$smarty.server.REQUEST_URI}}{{if $smarty.server.REQUEST_URI|strpos:'?'!==false}}&amp;{{else}}?{{/if}}debug=true&amp;scroll={{$page_definition.external_id}}/{{$item.block_uid}}" target="_blank">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								<title>{{$lang.website_ui.toolbar_link_structure}}</title>
								<path d="m180.667 30.333h150.667v150.667h-150.667z"/>
								<path d="m0 331h150.667v150.667h-150.667z"/>
								<path d="m180.667 331h150.667v150.667h-150.667z"/>
								<path d="m90.333 271h150.667v30h30v-30h150.667v30h30v-60h-180.667v-30h-30v30h-180.667v60h30z"/>
								<path d="m361.333 331h150.667v150.667h-150.667z"/>
							</svg>
						</a>
						<a class="kvs-toolbar-ico" onclick="kvs_toolbar_highlight('{{$page_definition.external_id}}/{{$item.block_uid}}', '{{if in_array('website_ui|view',$smarty.session.permissions)}}{{$admin_url}}/project_pages.php?action=change_block&amp;item_id={{$page_definition.external_id}}||{{$item.block_id}}||{{$item.block_name}}&amp;item_name={{$item.title}}{{/if}}', '{{$title}}')">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 408 408">
								<title>{{$lang.website_ui.toolbar_link_show}}</title>
								<path d="M397.9,83c5.5,0,10-4.5,10.1-10V10.1C408,4.5,403.5,0,397.9,0H335c-5.5,0-10,4.5-10,10.1V32H83V10.1c0-5.5-4.5-10-10-10.1 H10.1C4.6,0,0.1,4.5,0,10.1V73c0,5.5,4.5,10,10.1,10H32v242H10.1c-5.5,0-10,4.5-10.1,10v62.9c0,5.5,4.5,10,10.1,10.1H73 c5.5,0,10-4.5,10-10.1V376h242v21.9c0,5.5,4.5,10,10,10.1h62.9c5.5,0,10-4.5,10.1-10.1V335c0-5.5-4.5-10-10.1-10H376V83H397.9z M20,63V20h43v43H20z M63,388H20v-43h43V388z M325,335v21H83v-21c0-5.5-4.5-10-10-10H52V83h21c5.5,0,10-4.5,10-10V52h242v21 c0,5.5,4.5,10,10,10h21v242h-21C329.5,325,325,329.5,325,335z M388,345v43h-43v-43H388z M345,63V20h43v43H345z"/>
							</svg>
						</a>
					{{elseif $item.global_uid}}
						{{assign var="title" value=$lang.website_ui.toolbar_global_block|replace:"%1%":$item.title|replace:"%2%":$item.display_mode|replace:"%3%":$item.exec_time|smarty:nodefaults}}
						{{$title}}
						{{if in_array('website_ui|view',$smarty.session.permissions)}}
							<a class="kvs-toolbar-ico" href="{{$admin_url}}/project_pages.php?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_name}}&amp;item_name={{$item.title}}" target="_blank">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 383.947 383.947">
									<title>{{$lang.website_ui.toolbar_link_edit}}</title>
									<polygon points="0,303.947 0,383.947 80,383.947 316.053,147.893 236.053,67.893"/>
									<path d="M377.707,56.053L327.893,6.24c-8.32-8.32-21.867-8.32-30.187,0l-39.04,39.04l80,80l39.04-39.04 C386.027,77.92,386.027,64.373,377.707,56.053z"/>
								</svg>
							</a>
						{{/if}}
						<a class="kvs-toolbar-ico" href="{{$smarty.server.REQUEST_URI}}{{if $smarty.server.REQUEST_URI|strpos:'?'!==false}}&amp;{{else}}?{{/if}}debug=true&amp;scroll=$global/{{$item.global_uid}}" target="_blank">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								<title>{{$lang.website_ui.toolbar_link_structure}}</title>
								<path d="m180.667 30.333h150.667v150.667h-150.667z"/>
								<path d="m0 331h150.667v150.667h-150.667z"/>
								<path d="m180.667 331h150.667v150.667h-150.667z"/>
								<path d="m90.333 271h150.667v30h30v-30h150.667v30h30v-60h-180.667v-30h-30v30h-180.667v60h30z"/>
								<path d="m361.333 331h150.667v150.667h-150.667z"/>
							</svg>
						</a>
						<a class="kvs-toolbar-ico" onclick="kvs_toolbar_highlight('$global/{{$item.global_uid}}', '{{if in_array('website_ui|view',$smarty.session.permissions)}}{{$admin_url}}/project_pages.php?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_name}}&amp;item_name={{$item.title}}{{/if}}', '{{$title}}')">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 408 408">
								<title>{{$lang.website_ui.toolbar_link_show}}</title>
								<path d="M397.9,83c5.5,0,10-4.5,10.1-10V10.1C408,4.5,403.5,0,397.9,0H335c-5.5,0-10,4.5-10,10.1V32H83V10.1c0-5.5-4.5-10-10-10.1 H10.1C4.6,0,0.1,4.5,0,10.1V73c0,5.5,4.5,10,10.1,10H32v242H10.1c-5.5,0-10,4.5-10.1,10v62.9c0,5.5,4.5,10,10.1,10.1H73 c5.5,0,10-4.5,10-10.1V376h242v21.9c0,5.5,4.5,10,10,10.1h62.9c5.5,0,10-4.5,10.1-10.1V335c0-5.5-4.5-10-10.1-10H376V83H397.9z M20,63V20h43v43H20z M63,388H20v-43h43V388z M325,335v21H83v-21c0-5.5-4.5-10-10-10H52V83h21c5.5,0,10-4.5,10-10V52h242v21 c0,5.5,4.5,10,10,10h21v242h-21C329.5,325,325,329.5,325,335z M388,345v43h-43v-43H388z M345,63V20h43v43H345z"/>
							</svg>
						</a>
					{{/if}}
				</li>
			{{/foreach}}
			<li></li>
		</ul>
	</div>
	<div class="kvs-toolbar-separator"></div>
	<div id="kvs_toolbar_caching_switch" {{if $smarty.session.save.options.enable_site_caching==1}}data-checked{{/if}} title="{{$lang.website_ui.toolbar_checkbox_caching_hint}}">{{$lang.website_ui.toolbar_checkbox_caching}}</div>
	<div class="kvs-toolbar-separator"></div>
	{{foreach item="item" from=$context_objects|smarty:nodefaults}}
		{{if $item.editor && (!$item.permission || in_array($item.permission,$smarty.session.permissions))}}
			<div class="kvs-toolbar-context-object">
				<a href="{{$admin_url}}/{{$item.editor}}?action=change&amp;item_id={{$item.id}}" target="_blank">{{$item.name}}</a>
			</div>
		{{/if}}
	{{/foreach}}
	<div id="kvs_toolbar_spacer"></div>
	<a id="kvs_toolbar_button_close" title="{{$lang.website_ui.toolbar_button_close}}"></a>
</div>