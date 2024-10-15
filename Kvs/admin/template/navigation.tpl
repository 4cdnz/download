{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $nav.show==1}}
	<div class="paging">
		{{if ($nav.page_str_left_jump!='')}}
			<a class="first" href="{{$nav.first}}">1</a>
			<a class="jump" href="{{$nav.page_str_left_jump}}">...</a>
		{{/if}}

		{{section name="index" start=0 step=1 loop=$nav.page_str}}
			{{if $nav.page_str[index]!=''}}
				<a class="page" href="{{$nav.page_str[index]}}">{{$nav.page_num[index]|intval}}</a>
			{{else}}
				{{if !$nav.is_first}}
					{{assign var="prev_index" value=$smarty.section.index.index-1}}
					<a class="prev" href="{{$nav.page_str[$prev_index]}}"><<</a>
				{{/if}}
				<span>{{$nav.page_num[index]|intval}}</span>
				{{if !$nav.is_last}}
					{{assign var="next_index" value=$smarty.section.index.index+1}}
					<a class="next" href="{{$nav.page_str[$next_index]}}">>></a>
				{{/if}}
			{{/if}}
		{{/section}}

		{{if ($nav.page_str_right_jump!='')}}
			<a class="jump" href="{{$nav.page_str_right_jump}}">...</a>
			<a class="last" href="{{$nav.last}}">{{$nav.last_from}}</a>
		{{/if}}
	</div>
{{/if}}