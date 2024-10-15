{{*
	© Kernel Video Sharing
	https://kernel-video-sharing.com
*}}
<div class="insight_list_table">
	{{if $is_grouped==1}}
		{{foreach item="item_group" from=$data|smarty:nodefaults}}
			{{if count($item_group.items)>0}}
				<h2>{{$item_group.title}} ({{$item_group.items|@count}})</h2>
				<ul>
					{{foreach item="item" from=$item_group.items|smarty:nodefaults}}
						<li><span class="de_lv_pair"><input type="checkbox" name="data[]" value="{{$item.id}}" alt="{{$item.title}}" {{if $item.selected==1}}disabled checked{{/if}}/><label {{if $item.error}}class="error" {{elseif $item.inactive}}class="inactive"{{/if}}>{{$item.title}}</label></span></li>
					{{/foreach}}
				</ul>
			{{/if}}
		{{/foreach}}
	{{else}}
		<ul>
			{{foreach item="item" from=$data|smarty:nodefaults}}
				<li><span class="de_lv_pair"><input type="checkbox" name="data[]" value="{{$item.id}}" alt="{{$item.title}}" {{if $item.selected==1}}disabled checked{{/if}}/><label {{if $item.error}}class="error" {{elseif $item.inactive}}class="inactive"{{/if}}>{{$item.title}}</label></span></li>
			{{/foreach}}
		</ul>
	{{/if}}
</div>
<div class="insight_list_buttons">
	<button>{{$lang.insight_action_add_selected_items}}</button>
	<div class="insight_list_options">
		<input type="text" placeholder="{{$lang.insight_label_search}}">
		{{if count($sortings)>1}}
			<select name="sort_by">
				{{foreach item="item" from=$sortings|smarty:nodefaults}}
					<option value="{{$item.id}}" {{if $item.selected}}selected{{/if}}>{{$item.title}}</option>
				{{/foreach}}
			</select>
		{{/if}}
		{{if count($groupings)>1}}
			<select name="group_by">
				{{foreach item="item" from=$groupings|smarty:nodefaults}}
					<option value="{{$item.id}}" {{if $item.selected}}selected{{/if}}>{{$item.title}}</option>
				{{/foreach}}
			</select>
		{{/if}}
		{{if count($statuses)>1}}
			<select name="status">
				{{foreach item="item" from=$statuses|smarty:nodefaults}}
					<option value="{{$item.id}}" {{if $item.selected}}selected{{/if}}>{{$item.title}}</option>
				{{/foreach}}
			</select>
		{{/if}}
	</div>
</div>