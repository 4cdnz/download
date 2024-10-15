{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $smarty.get.action=='show_long_desc'}}

<form action="{{$page_name}}" method="post" class="de de_readonly" name="{{$smarty.now}}" data-editor-name="theme_block_overview">
	<div class="de_main">
		<div class="de_header"><h1><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_blocks_list}}</a> / {{$lang.website_ui.block_view|replace:"%1%":$smarty.post.external_id}}</h1></div>
		<table class="de_editor">
			<tr class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
				<td>
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
				<td class="de_separator"><h2>{{$lang.website_ui.block_divider_description}}</h2></td>
			</tr>
			<tr>
				<td class="de_control"><div>{{$smarty.post.desc}}</div></td>
			</tr>
			<tr>
				<td class="de_separator"><h2>{{$lang.website_ui.block_divider_template}}</h2></td>
			</tr>
			<tr>
				<td class="de_control">
					<div class="code_editor" data-syntax="smarty">
						<textarea name="template_example" rows="10" cols="40">{{$smarty.post.template}}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_separator"><h2>{{$lang.website_ui.block_divider_params}}</h2></td>
			</tr>
			{{if count($smarty.post.params)>0}}
				<tr>
					<td class="de_table_control">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td>{{$lang.website_ui.block_params_col_name}}</td>
								<td>{{$lang.website_ui.block_params_col_type}}</td>
								<td>{{$lang.website_ui.block_params_col_required}}</td>
								<td>{{$lang.website_ui.block_params_col_description}}</td>
							</tr>
							{{assign var="last_group" value=""}}
							{{foreach item="item" from=$smarty.post.params|smarty:nodefaults}}
								{{if ($last_group=='' || $last_group!=$item.group) && $item.group!=''}}
									{{assign var="last_group" value=$item.group}}
									<tr class="eg_group_header">
										<td colspan="4">{{$item.group_desc}}</td>
									</tr>
								{{/if}}
								<tr class="eg_data">
									<td {{if $item.is_deprecated==1}}class="deprecated_text"{{/if}}>{{if $item.is_required==1}}<b>{{$item.name}}</b>{{else}}{{$item.name}}{{/if}}</td>
									<td>{{$item.type}}</td>
									<td><input type="checkbox" {{if $item.is_required==1}}checked{{/if}}/></td>
									<td>{{$item.desc}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_control">{{$lang.website_ui.block_divider_params_nothing}}</td>
				</tr>
			{{/if}}
			{{if $smarty.post.examples!=''}}
				<tr>
					<td class="de_separator"><h2>{{$lang.website_ui.block_divider_examples}}</h2></td>
				</tr>
				<tr>
					<td class="de_control">{{$smarty.post.examples}}</td>
				</tr>
			{{/if}}
		</table>
	</div>
</form>

{{else}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
			<div class="dgf_filter">
				<div class="drop">
					<i class="icon icon-action-groupby"></i><span>{{$lang.website_ui.block_repostory_filter_group_by}}</span>
					<ul>
						<li {{if $smarty.session.save.$page_name.se_group_by=='functionality'}}class="selected"{{/if}}><a href="{{$page_name}}?se_group_by=functionality">{{$lang.website_ui.block_repostory_filter_group_by_functionality}}</a></li>
						<li {{if $smarty.session.save.$page_name.se_group_by=='type'}}class="selected"{{/if}}><a href="{{$page_name}}?se_group_by=type">{{$lang.website_ui.block_repostory_filter_group_by_type}}</a></li>
					</ul>
				</div>
			</div>
			<div class="dgf_reset">
				<input type="reset" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled{{/if}}/>
			</div>
		</div>
	</form>
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
					<col/>
					<col/>
					<col/>
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/></td>
						<td>{{$lang.website_ui.block_repostory_field_block_id}}</td>
						<td>{{$lang.website_ui.block_repostory_field_description}}</td>
						<td>{{$lang.website_ui.block_repostory_field_author}}</td>
						<td>{{$lang.website_ui.block_repostory_field_version}}</td>
						<td>{{$lang.website_ui.block_repostory_field_type}}</td>
						<td>{{$lang.website_ui.block_repostory_field_functionality}}</td>
						<td>{{$lang.website_ui.block_repostory_field_package}}</td>
						<td>{{$lang.website_ui.block_repostory_field_state}}</td>
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=9}}
					<tr class="err_list {{if (count($data)>0 || $total_num==0) && !is_array($smarty.post.errors)}}hidden{{/if}}">
						<td colspan="{{$table_columns_visible}}">
							<div class="err_header">{{if count($data)==0 && $total_num>0}}{{$lang.common.dg_list_error}}{{else}}{{$lang.validation.common_header}}{{/if}}</div>
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
					{{if count($data)==0 && $total_num==0}}
						<tr class="dg_empty">
							<td colspan="{{$table_columns_visible}}">{{$lang.common.dg_list_empty}}</td>
						</tr>
					{{/if}}
					{{foreach name="data_blocks" key="blocks_grouping" item="item_blocks" from=$data|smarty:nodefaults}}
						<tr class="dg_group_header">
							<td colspan="9">
								{{if $blocks_grouping=='type:list'}}
									{{$lang.website_ui.block_repostory_field_type_list}}
								{{elseif $blocks_grouping=='type:context'}}
									{{$lang.website_ui.block_repostory_field_type_context}}
								{{elseif $blocks_grouping=='type:form'}}
									{{$lang.website_ui.block_repostory_field_type_form}}
								{{elseif $blocks_grouping=='type:misc'}}
									{{$lang.website_ui.block_repostory_field_type_misc}}
								{{elseif $blocks_grouping=='type:custom'}}
									{{$lang.website_ui.block_repostory_field_type_custom}}
								{{elseif $blocks_grouping=='functionality:videos'}}
									{{$lang.website_ui.block_repostory_field_functionality_videos}}
								{{elseif $blocks_grouping=='functionality:albums'}}
									{{$lang.website_ui.block_repostory_field_functionality_albums}}
								{{elseif $blocks_grouping=='functionality:posts'}}
									{{$lang.website_ui.block_repostory_field_functionality_posts}}
								{{elseif $blocks_grouping=='functionality:categorization'}}
									{{$lang.website_ui.block_repostory_field_functionality_categorization}}
								{{elseif $blocks_grouping=='functionality:memberzone'}}
									{{$lang.website_ui.block_repostory_field_functionality_memberzone}}
								{{elseif $blocks_grouping=='functionality:community'}}
									{{$lang.website_ui.block_repostory_field_functionality_community}}
								{{elseif $blocks_grouping=='functionality:misc'}}
									{{$lang.website_ui.block_repostory_field_functionality_misc}}
								{{else}}
									{{$blocks_grouping|substr:14}}
								{{/if}}
							</td>
						</tr>
						{{foreach name="data" item="item" from=$item_blocks|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}" disabled/></td>
								<td>
									{{if $item.is_invalid==1}}
										<span class="highlighted_text">{{$item.external_id}}</span>
									{{else}}
										{{if $item.package>$config.installation_type}}
											{{$item.external_id}}
										{{else}}
											<a href="{{$page_name}}?action=show_long_desc&amp;block_id={{$item.external_id}}">{{$item.external_id}}</a>
										{{/if}}
									{{/if}}
								</td>
								<td>{{$item.short_desc}}</td>
								<td class="nowrap">{{$item.author}}</td>
								<td class="nowrap">{{$item.version}}</td>
								<td class="nowrap">
									{{foreach name="data_types" item="item_type" from=$item.types}}
										{{if $item_type=='list'}}{{$lang.website_ui.block_repostory_field_type_list}}{{elseif $item_type=='context'}}{{$lang.website_ui.block_repostory_field_type_context}}{{elseif $item_type=='form'}}{{$lang.website_ui.block_repostory_field_type_form}}{{elseif $item_type=='custom'}}{{$lang.website_ui.block_repostory_field_type_custom}}{{else}}{{$lang.website_ui.block_repostory_field_type_misc}}{{/if}}{{if !$smarty.foreach.data_types.last}}, {{/if}}
									{{/foreach}}
								</td>
								<td class="nowrap">
									{{foreach name="data_functionalities" item="item_functionality" from=$item.functionalities}}{{if $item_functionality=='videos'}}{{$lang.website_ui.block_repostory_field_functionality_videos}}{{elseif $item_functionality=='albums'}}{{$lang.website_ui.block_repostory_field_functionality_albums}}{{elseif $item_functionality=='posts'}}{{$lang.website_ui.block_repostory_field_functionality_posts}}{{elseif $item_functionality=='categorization'}}{{$lang.website_ui.block_repostory_field_functionality_categorization}}{{elseif $item_functionality=='memberzone'}}{{$lang.website_ui.block_repostory_field_functionality_memberzone}}{{elseif $item_functionality=='community'}}{{$lang.website_ui.block_repostory_field_functionality_community}}{{else}}{{$lang.website_ui.block_repostory_field_functionality_misc}}{{/if}}{{if !$smarty.foreach.data_functionalities.last}}, {{/if}}
									{{/foreach}}
								</td>
								<td class="nowrap">
									{{if $item.package==4}}
										{{$lang.website_ui.block_repostory_field_package_ultimate}}
									{{elseif $item.package==3}}
										{{$lang.website_ui.block_repostory_field_package_premium}}
									{{elseif $item.package==2}}
										{{$lang.website_ui.block_repostory_field_package_advanced}}
									{{else}}
										{{$lang.website_ui.block_repostory_field_package_basic}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.is_invalid==1}}
										<span class="highlighted_text">{{$lang.website_ui.block_repostory_field_state_invalid}}</span>
									{{elseif $item.package>$config.installation_type}}
										{{$lang.website_ui.block_repostory_field_state_disabled}}
									{{else}}
										{{$lang.website_ui.block_repostory_field_state_valid}}
									{{/if}}
								</td>
							</tr>
						{{/foreach}}
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions">
			</div>
			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>
		</div>
	</form>
</div>

{{/if}}