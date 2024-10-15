{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="general_error">
	<div class="message">{{$page_title}}</div>
	{{if $exception_text}}
		<div class="details">
			{{$exception_text}}{{if $exception_details}}: {{$exception_details}}{{/if}}
			<ul>
				#0 {{$exception_file}}({{$exception_line}})
				{{foreach from=$exception_trace|smarty:nodefaults item="item" name="trace"}}
					<li>
						#{{$smarty.foreach.trace.index+1}} {{$item.file}}({{$item.line}}): {{$item.class}}{{$item.type}}{{$item.function|smarty:nodefaults}}
					</li>
				{{/foreach}}
			</ul>
		</div>
	{{/if}}
</div>