{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<style>
.de_control table {
	border-collapse: collapse;
	width: 100%;
}
.de_control table + table {
	margin-top: 10px;
}
.de_control table th,
.de_control table td {
	border: 1px solid #aaaaaa;
	padding: 5px;
	width: 10%;
	min-width: 70px;
	max-width: 100px;
	text-align: left;
}
.de_control table th {
	background: #dddddd;
	font-weight: 700;
}
</style>
<form action="{{$page_name}}" method="post" class="de" name="{{$smarty.now}}" data-editor-name="plugin_{{$smarty.request.plugin_id}}">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.rotator_clicks_matrix.title}} &nbsp;[ <span data-accordeon="doc_expander_{{$smarty.request.plugin_id}}">{{$lang.plugins.plugin_divider_description}}</span> ]</h1></div>
		<table class="de_editor">
			<tr class="doc_expander_{{$smarty.request.plugin_id}} hidden">
				<td class="de_control" colspan="2">
					{{$lang.plugins.rotator_clicks_matrix.long_desc}}
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
			<tr>
				<td class="de_label de_required">{{$lang.plugins.rotator_clicks_matrix.field_page}}</td>
				<td class="de_control">
					<select name="page_external_id" class="de_switcher" data-switcher-params="plugin_id=rotator_clicks_matrix;items_in_row={{$smarty.post.items_in_row}}">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach name="data" item="item" from=$smarty.post.pages|smarty:nodefaults}}
							<option value="{{$item.external_id}}" {{if $item.external_id==$smarty.post.page_external_id}}selected{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.rotator_clicks_matrix.field_items_in_row}}</td>
				<td class="de_control">
					<select name="items_in_row" class="de_switcher" data-switcher-params="plugin_id=rotator_clicks_matrix;page_external_id={{$smarty.post.page_external_id}}">
						{{section name="items_in_row" start=1 loop=11}}
							<option value="{{$smarty.section.items_in_row.iteration}}" {{if $smarty.post.items_in_row==$smarty.section.items_in_row.iteration}}selected{{/if}}>{{$smarty.section.items_in_row.iteration}}</option>
						{{/section}}
					</select>
					<span class="de_hint">{{$lang.plugins.rotator_clicks_matrix.field_items_in_row_hint}}</span>
				</td>
			</tr>
			{{if count($smarty.post.displayed_data)>0}}
				<tr>
					<td class="de_separator" colspan="2"><h2>{{$lang.plugins.rotator_clicks_matrix.divider_matrix}}</h2></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.rotator_clicks_matrix.field_by_page_number}}</td>
					<td class="de_control">
						{{foreach item="item" from=$smarty.post.displayed_data|smarty:nodefaults}}
							{{if is_array($item.page_matrix)}}
								<table>
									<colgroup>
										{{section name="data" start=0 step=1 loop=10}}
											<col/>
										{{/section}}
									</colgroup>
									<tr>
										<th colspan="10">
											<span class="de_lv_pair"><input type="checkbox" name="reset_page[]" value="{{$item.id}}"/><label>{{$item.id}}</label></span>
										</th>
									</tr>
									<tr>
										{{assign var="pos" value=1}}
										{{section name="data" start=0 step=1 loop=10}}
											<th>
												{{$lang.plugins.rotator_clicks_matrix.field_by_page_number_page|replace:"%1%":$pos}}{{if $pos==10}}+{{/if}}
											</th>
											{{assign var="pos" value=$pos+1}}
										{{/section}}
									</tr>
									<tr>
										{{assign var="pos" value=1}}
										{{section name="elements_data" start=0 step=1 loop=10}}
											<td>
												{{if $item.page_matrix[$pos]!=''}}
													{{$item.page_matrix[$pos]}}
													<br/>
													({{$item.page_matrix_pc[$pos]}}%)
												{{else}}
													0
												{{/if}}
											</td>
											{{assign var="pos" value=$pos+1}}
										{{/section}}
									</tr>
								</table>
							{{/if}}
						{{/foreach}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.rotator_clicks_matrix.field_by_page_position}}</td>
					<td class="de_control">
						{{foreach item="item" from=$smarty.post.displayed_data|smarty:nodefaults}}
							{{if is_array($item.matrix)}}
								<table>
									<colgroup>
										{{section name="data" start=0 step=1 loop=$smarty.post.items_in_row}}
											<col/>
										{{/section}}
									</colgroup>
									{{assign var="elements_amount" value=$item.places_count/$smarty.post.items_in_row|ceil}}
									{{assign var="pos" value=1}}
									<tr>
										<th colspan="{{$smarty.post.items_in_row}}">
											<span class="de_lv_pair"><input type="checkbox" name="reset_place[]" value="{{$item.id}}"/><label>{{$item.id}}</label></span>
										</th>
									</tr>
									{{section name="elements_data" start=0 step=1 loop=$elements_amount}}
										<tr>
											{{section name="data" start=0 step=1 loop=$smarty.post.items_in_row}}
												<td>
													{{if $item.matrix[$pos]!=''}}
														{{$item.matrix[$pos]}} ({{$item.matrix_pc[$pos]}}%)
													{{else}}
														0
													{{/if}}
												</td>
												{{assign var="pos" value=$pos+1}}
											{{/section}}
										</tr>
									{{/section}}
								</table>
							{{/if}}
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
		</table>
	</div>
	{{if count($smarty.post.displayed_data)>0}}
		<div class="de_action_group">
			<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
			<input type="hidden" name="action" value="reset"/>
			<input type="submit" value="{{$lang.plugins.rotator_clicks_matrix.btn_reset}}" data-confirm="{{$lang.plugins.rotator_clicks_matrix.btn_reset_confirm}}"/>
		</div>
	{{/if}}
</form>
