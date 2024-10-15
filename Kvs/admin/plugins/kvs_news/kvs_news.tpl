{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.kvs_news.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.kvs_news.long_desc}}
				</td>
			</tr>
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td colspan="2">
					<div class="err_header">{{$lang.validation.common_header}}</div>
					<div class="err_content">
						{{if is_array($smarty.post.errors)}}
							<ul>
								{{foreach item="error" from=$smarty.post.errors|smarty:nodefaults}}
									<li>{{$error}}</li>
								{{/foreach}}
							</ul>
						{{/if}}
					</div>
				</td>
			</tr>
			{{foreach item="item" from=$smarty.post.news|smarty:nodefaults}}
				<tr>
					<td class="de_label">{{$item.post_date|date_format:$smarty.session.userdata.short_date_format}}</td>
					<td class="de_control">
						<span>
							{{assign var="news_short_text_key" value="short_text_`$smarty.session.userdata.lang`"}}
							{{assign var="url_language" value="en"}}
							{{if $smarty.session.userdata.lang=='russian'}}
								{{assign var="url_language" value="ru"}}
							{{/if}}
							<a href="https://www.kernel-video-sharing.com/{{$url_language}}/news/{{$item.news_id}}/">{{$item.$news_short_text_key}}</a>
						</span>
					</td>
				</tr>
			{{/foreach}}
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_news.field_last_exec}}</td>
				<td class="de_control">
					<span>
						{{if $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
							{{$lang.plugins.kvs_news.field_last_exec_none}}
						{{else}}
							{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
							(<a href="{{$page_name}}?plugin_id=kvs_news&amp;action=get_log" rel="log">{{$smarty.post.duration|default:0}} {{$lang.plugins.kvs_news.field_last_exec_seconds}}</a>)
						{{/if}}
					</span>
				</td>
			</tr>
		</table>
	</div>
</form>