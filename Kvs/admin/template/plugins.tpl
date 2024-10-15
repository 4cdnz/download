{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/></td>
						<td>{{$lang.plugins.plugin_field_title}}</td>
						<td>{{$lang.plugins.plugin_field_description}}</td>
						<td>{{$lang.plugins.plugin_field_type}}</td>
						<td>{{$lang.plugins.plugin_field_version}}</td>
						<td>{{$lang.plugins.plugin_field_kvs_version}}</td>
					</tr>
				</thead>
				<tbody>
					{{foreach name="data" item="item" from=$plugins|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$id}}" disabled {{if $item.is_enabled==1}}checked{{/if}}/></td>
							<td>
								<a href="{{$page_name}}?plugin_id={{$item.id}}" class="{{if $item.is_invalid==1}}highlighted_text{{/if}}">{{if $item.is_enabled==1}}<b>{{/if}}{{$item.title}}{{if $item.is_enabled==1}}</b>{{/if}}</a>
							</td>
							<td>{{$item.description}}</td>
							<td>
								{{foreach name="data_type" item="item_type" from=$item.plugin_types|smarty:nodefaults}}
									{{if $item_type=='manual'}}
										{{$lang.plugins.plugin_field_type_manual}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='cron'}}
										{{$lang.plugins.plugin_field_type_cron}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='api'}}
										{{$lang.plugins.plugin_field_type_api}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='process_object'}}
										{{$lang.plugins.plugin_field_type_object_callback}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{/if}}
								{{/foreach}}
							</td>
							<td>{{$item.version}}</td>
							<td>{{$item.kvs_version}}</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>
			<div class="dgb_info"></div>
		</div>
	</form>
</div>