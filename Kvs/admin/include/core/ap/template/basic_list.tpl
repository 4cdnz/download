<div class="dg_wrapper">
	<form action="?/{{$controller_path}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			{{if count($data.type.searchable_scopes)>0}}
				<div class="dgf_search">
					<i class="icon icon-action-search"></i>
					<input type="text" name="se_text" autocomplete="off" value="{{$data.search_text}}" placeholder="{{$localization.ap.grid_filter_search}}"/>
					<i class="icon icon-action-forward dgf_search_apply"></i>
					{{if count($data.type.searchable_scopes)>1}}
						<div class="dgf_search_layer">
							<span>{{$localization.ap.grid_filter_search_in}}:</span>
							<ul>
								{{assign var="search_everywhere" value="true"}}
								{{foreach from=$data.type.searchable_scopes|smarty:nodefaults item="scope"}}
									<li>
										<span class="de_lv_pair"><input type="checkbox" name="se_text_scopes[]" value="{{$scope.name}}" {{if $scope.is_selected==1}}checked{{/if}}/><label>{{$scope.title}}</label></span>
										{{if $scope.is_selected!=1}}
											{{assign var="search_everywhere" value="false"}}
										{{/if}}
									</li>
								{{/foreach}}
								<li class="dgf_everywhere">
									<span class="de_lv_pair"><input type="checkbox" name="se_text_scopes[]" value="*" {{if $search_everywhere=='true'}}checked{{/if}} class="dgf_everywhere"/><label>{{$localization.ap.grid_filter_search_everywhere}}</label></span>
								</li>
							</ul>
						</div>
					{{/if}}
				</div>
			{{/if}}
			<div class="dgf_reset">
				<input type="reset" value="{{$localization.ap.grid_filter_list_reset}}" {{if $data.is_filtered==0}}disabled{{/if}}/>
			</div>
			<div class="dgf_options">
				<div class="drop">
					<i class="icon icon-action-list"></i><span>{{$localization.ap.grid_filter_list_view}}</span>
					<ul>
						<li><a href="?/{{$controller_path}}&amp;se_grid_preset=">{{$localization.ap.grid_filter_list_view_default}}</a></li>
						{{foreach from=$data.grid_presets|smarty:nodefaults item="preset"}}
							<li {{if $preset.is_selected==1}}{{assign var="selected_grid_preset" value=$preset.title}}class="selected"{{/if}}><a href="?/{{$controller_path}}&amp;se_grid_preset={{$preset.title}}">{{$preset.title}}</a></li>
						{{/foreach}}
					</ul>
				</div>
				<div class="drop dgf_advanced_link"><i class="icon icon-action-settings"></i><span>{{$localization.ap.grid_filter_list_customize}}</span></div>
			</div>
		</div>
		<div class="dgf_advanced">
			<div class="dgf_advanced_control">
				<a class="dgf_filters"><i class="icon icon-action-filter"></i>{{$localization.ap.grid_filter_list_customize_filters}}</a>
				<a class="dgf_columns"><i class="icon icon-action-columnchooser"></i>{{$localization.ap.grid_filter_list_customize_columns}}</a>
				<div class="dgf_submit">
					<div class="dgf_preset_name">
						<input type="text" name="save_grid_preset" value="{{$selected_grid_preset}}" maxlength="100" placeholder="{{$localization.ap.grid_filter_list_customize_save}}"/>
					</div>
					<input type="submit" name="save_filter" value="{{$localization.ap.grid_filter_list_customize_apply}}"/>
				</div>
			</div>
			<div class="dgf_advanced_filters">
				{{foreach item="filter" from=$data.type.filters|smarty:nodefaults}}
					<div class="dgf_filter">
						{{if $filter.type=='enum' || $filter.type=='choice' || $filter.type=='objecttype'}}
							{{if is_array($filter.value)}}
								{{foreach item="filter_value" from=$filter.value|smarty:nodefaults}}
									<select name="{{$filter.name}}[]">
										<option value="">{{$filter.title}}...</option>
										{{assign var="found_value" value="false"}}
										{{foreach item="title" key="value" from=$filter.values|smarty:nodefaults}}
											<option value="{{$value}}" {{if $value==$filter_value}}{{assign var="found_value" value="true"}}selected{{/if}}>{{$title}}</option>
										{{/foreach}}
										{{if $found_value!='true'}}
											<option value="{{$filter_value}}" selected>{{$localization.ap.grid_filter_invalid_enum|replace:"%1%":$filter_value}}</option>
										{{/if}}
									</select>
								{{/foreach}}
							{{/if}}
							<select name="{{$filter.name}}[]">
								<option value="">{{$filter.title}}...</option>
								{{foreach item="title" key="value" from=$filter.values|smarty:nodefaults}}
									<option value="{{$value}}">{{$title}}</option>
								{{/foreach}}
							</select>
						{{elseif $filter.type=='country'}}
							{{if is_array($filter.value)}}
								{{foreach item="filter_value" from=$filter.value|smarty:nodefaults}}
									<div class="insight">
										<div class="js_params">
											<span class="js_param">url=async/insight.php?type=countries</span>
										</div>
										<input type="text" name="{{$filter.name}}[]" value="{{$filter_value}}" placeholder="{{$filter.title}}..."/>
									</div>
								{{/foreach}}
							{{/if}}
							<div class="insight">
								<div class="js_params">
									<span class="js_param">url=async/insight.php?type=countries</span>
								</div>
								<input type="text" name="{{$filter.name}}[]" value="" placeholder="{{$filter.title}}..."/>
							</div>
						{{elseif ($filter.type=='ref' && $filter.ref_type!='multiple') || $filter.type=='ref_list'}}
							{{if is_array($filter.value)}}
								{{foreach item="filter_value" from=$filter.value|smarty:nodefaults}}
									<div class="insight">
										<div class="js_params">
											<span class="js_param">url=async/insight.php?type={{$filter.ref_type}}</span>
											{{if $filter.can_view==1 && $filter_value.id>0}}
												<span class="js_param">allow_view=true</span>
												<span class="js_param">view_id={{$filter_value.id}}</span>
												{{if $filter_value.is_inactive==1}}
													<span class="js_param">inactive=true</span>
												{{/if}}
											{{/if}}
										</div>
										<input type="text" name="{{$filter.name}}[]" value="{{$filter_value.title}}" placeholder="{{$filter.title}}..."/>
									</div>
								{{/foreach}}
							{{/if}}
							<div class="insight">
								<div class="js_params">
									<span class="js_param">url=async/insight.php?type={{$filter.ref_type}}</span>
								</div>
								<input type="text" name="{{$filter.name}}[]" value="" placeholder="{{$filter.title}}..."/>
							</div>
						{{/if}}
					</div>
				{{/foreach}}
			</div>
			<div class="dgf_advanced_columns">
				{{foreach item="field" from=$data.type.fields|smarty:nodefaults}}
					{{if $field.type!='id'}}
						<span class="de_lv_pair"><em class="dg_move_handle"></em><input type="checkbox" name="grid_columns[]" value="{{$field.name}}" {{if $field.is_visible==1}}checked{{/if}}/><label>{{$field.title|smarty:nodefaults|replace:"[kt|br]":""}}</label></span>
					{{/if}}
				{{/foreach}}
			</div>
		</div>
	</form>
	<form action="?/{{$controller_path}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					{{assign var="table_columns_visible" value=0}}
					{{if $data.type.identifier}}
						<col width="1%"/>
						{{assign var="table_columns_visible" value=$table_columns_visible+1}}
					{{/if}}
					{{foreach item="field" from=$data.type.fields|smarty:nodefaults}}
						{{if $field.is_visible==1}}
							{{assign var="table_columns_visible" value=$table_columns_visible+1}}
							<col/>
						{{/if}}
					{{/foreach}}
					<col width="1%"/>
					{{assign var="table_columns_visible" value=$table_columns_visible+1}}
				</colgroup>
				<thead>
					<tr class="dg_header">
						{{if $data.type.identifier}}
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/><span></span></td>
						{{/if}}
						{{foreach item="field" from=$data.type.fields|smarty:nodefaults}}
							{{if $field.is_visible==1}}
								<td class="{{if $data.sort_by==$field.name}}dg_sorted{{/if}} dg_type_{{$field.type}}">
									{{if $data.sort_by==$field.name}}<i class="icon icon-action-{{if $data.sort_direction=='desc'}}down{{else}}up{{/if}}"></i>{{/if}}
									<a href="?/{{$controller_path}}&amp;sort_by={{$field.name}}&amp;sort_direction={{if $data.sort_by!=$field.name}}{{if $data.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $data.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}">{{$field.title|default:$field.name}}</a>
								</td>
							{{/if}}
						{{/foreach}}
						<td>{{$localization.ap.grid_column_actions}}</td>
					</tr>
				</thead>
				<tbody>
					<tr class="err_list {{if count($data.errors)==0}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{$localization.ap.validation_common_header}}</div>
							<div class="err_content">
								{{if is_array($data.errors)}}
									<ul>
										{{foreach item="error" from=$data.errors|smarty:nodefaults}}
											<li>{{$error}}</li>
										{{/foreach}}
									</ul>
								{{/if}}
							</div>
						</td>
					</tr>
					{{if count($data.items)==0 && $data.total_num==0 && count($data.errors)==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$localization.ap.grid_message_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data" item="item" from=$data.items|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_inactive==1}}inactive{{/if}}">
							{{if $data.type.identifier}}
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
							{{/if}}
							{{foreach item="field" from=$data.type.fields|smarty:nodefaults}}
								{{if $field.is_visible==1}}
									<td class="dg_type_{{$field.type}}">
										{{assign var="value" value=$item[$field.name]|smarty:nodefaults}}
										{{if $field.type=='id' && $editor_controller_path && $item[$item_identifier]}}
											<a href="index.php?/{{$editor_controller_path|replace:"%id%":$item[$item_identifier]}}">{{$value}}</a>
										{{elseif $field.type=='ref'}}
											{{if $value.id>0}}
												<span {{if $value.is_inactive==1}}class="inactive"{{/if}}>
													{{if $field.can_view==1}}
														<a href="index.php?/{{$field.ref_editor_path|replace:"%id%":$value.id}}" {{if $field.filter_name}}class="dg_link_menu"{{/if}}>
															{{if $field.filter_name}}
																<span class="js_params">
																	{{assign var="link_value" value=$value.title|urlencode|smarty:nodefaults}}
																	<span class="js_param">add_filter=?/{{$controller_path}}&amp;merge_parameter={{$field.filter_name}}&amp;from=0&amp;{{$field.filter_name}}[]={{$link_value}}</span>
																	<span class="js_param">replace_filter=?/{{$controller_path}}&amp;se_grid_preset=&amp;no_filter=true&amp;from=0&amp;{{$field.filter_name}}[]={{$link_value}}</span>
																</span>
															{{/if}}{{$value.title}}
														</a>
													{{else}}
														{{$value.title}}
													{{/if}}
												</span>
											{{/if}}
										{{elseif $field.type=='ref_list'}}
											{{foreach name="ref_list" from=$value item="ref_list_item"}}
												<span {{if $ref_list_item.is_inactive==1}}class="inactive"{{/if}}>
													{{if $field.can_view==1}}
														<a href="index.php?/{{$field.ref_editor_path|replace:"%id%":$ref_list_item.id}}" {{if $field.filter_name}}class="dg_link_menu"{{/if}}>
															{{if $field.filter_name}}
																<span class="js_params">
																	{{assign var="link_value" value=$ref_list_item.title|urlencode|smarty:nodefaults}}
																	<span class="js_param">add_filter=?/{{$controller_path}}&amp;merge_parameter={{$field.filter_name}}&amp;from=0&amp;{{$field.filter_name}}[]={{$link_value}}</span>
																	<span class="js_param">replace_filter=?/{{$controller_path}}&amp;se_grid_preset=&amp;no_filter=true&amp;from=0&amp;{{$field.filter_name}}[]={{$link_value}}</span>
																</span>
															{{/if}}
														{{$ref_list_item.title}}</a>
													{{else}}
														{{$ref_list_item.title}}
													{{/if}}
												</span>{{if !$smarty.foreach.ref_list.last}}, {{/if}}
											{{/foreach}}
										{{elseif $field.type=='enum' || $field.type=='objecttype' || $field.type=='country'}}
											{{if $field.filter_name}}
												<a class="dg_link_menu">
													<span class="js_params">
														<span class="js_param">add_filter=?/{{$controller_path}}&amp;merge_parameter={{$field.filter_name}}&amp;from=0&amp;{{$field.filter_name}}[]={{$value}}</span>
														<span class="js_param">replace_filter=?/{{$controller_path}}&amp;se_grid_preset=&amp;no_filter=true&amp;from=0&amp;{{$field.filter_name}}[]={{$value}}</span>
													</span>
													{{$field.values[$value]|default:$value}}
												</a>
											{{else}}
												{{$field.values[$value]|default:$value}}
											{{/if}}
										{{elseif $field.type=='choice'}}
											{{if $value>0}}
												{{if $field.filter_name}}
													<a class="dg_link_menu">
														<span class="js_params">
															<span class="js_param">add_filter=?/{{$controller_path}}&amp;from=0&amp;{{$field.filter_name}}[]={{$value}}</span>
															<span class="js_param">replace_filter=?/{{$controller_path}}&amp;no_filter=true&amp;from=0&amp;{{$field.filter_name}}[]={{$value}}</span>
														</span>
														{{$field.values[$value]|default:$value}}
													</a>
												{{else}}
													{{$field.values[$value]|default:$value}}
												{{/if}}
											{{/if}}
										{{else}}
											{{$value}}
										{{/if}}
									</td>
								{{/if}}
							{{/foreach}}
							<td>
								<a {{if $editor_controller_path && $item[$item_identifier]}}href="index.php?/{{$editor_controller_path|replace:"%id%":$item[$item_identifier]}}" class="edit" {{else}}class="disabled"{{/if}} title="{{$localization.ap.common.dg_actions_edit}}"><i class="icon icon-action-edit"></i></a>
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
				<select name="batch_action">
					<option value="">{{$localization.ap.grid_action_batch}}</option>
				</select>
				<input type="submit" value="{{$localization.ap.grid_action_execute}}" disabled/>
			</div>

			{{if is_array($data.paginator.pages)}}
				<div class="paging">
					{{if $data.paginator.first_displayed_page>1}}
						<a class="first" href="index.php?/{{$controller_path}}&from=1">1</a>
						{{if $data.paginator.first_displayed_page>2}}
							<a class="jump" >...</a>
						{{/if}}
					{{/if}}

					{{foreach from=$data.paginator.pages item="page"}}
						{{if $page==$data.paginator.current_page}}
							<span>{{$page}}</span>
						{{else}}
							<a class="page" href="index.php?/{{$controller_path}}&from={{$page}}">{{$page}}</a>
						{{/if}}
					{{/foreach}}

					{{if $data.paginator.last_displayed_page<$data.paginator.total_pages}}
						<a class="jump" href="{{$data.paginator.pages_right_jump}}">...</a>
						<a class="last" href="index.php?/{{$controller_path}}&from={{$data.paginator.total_pages}}">{{$data.paginator.total_pages}}</a>
					{{/if}}
				</div>
			{{/if}}

			<div class="dgb_info">
				{{$localization.ap.grid_items_window|smarty:nodefaults|replace:"%1%":$data.total_num|replace:"%2%":$data.num_on_page}}
			</div>
		</div>
	</form>
</div>