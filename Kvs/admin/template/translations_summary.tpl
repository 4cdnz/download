{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf" name="{{$smarty.now}}">
		<div class="dgf">
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg" name="{{$smarty.now}}">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col/>
					<col/>
					{{foreach item="item" from=$list_languages|smarty:nodefaults}}
						<col/>
					{{/foreach}}
				</colgroup>
				<thead>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled/><span></span></td>
						<td>{{$lang.common.object_type}}</td>
						<td>{{$lang.common.total}}</td>
						{{foreach item="item" from=$list_languages|smarty:nodefaults}}
							<td>{{$item.title}}</td>
						{{/foreach}}
					</tr>
				</thead>
				<tbody>
					{{assign var="table_columns_visible" value=1}}
					{{foreach item="item" from=$list_languages|smarty:nodefaults}}
						{{assign var="table_columns_visible" value=$table_columns_visible+1}}
					{{/foreach}}
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
					{{foreach key="key" name="data" item="item" from=$data|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled/></td>
							<td>
								<a href="translations.php?no_filter=true&amp;se_object_type={{$item.object_type}}">
									{{if $key=='videos'}}
										{{$lang.common.object_type_videos}}
									{{elseif $key=='albums'}}
										{{$lang.common.object_type_albums}}
									{{elseif $key=='content_sources'}}
										{{$lang.common.object_type_content_sources}}
									{{elseif $key=='models'}}
										{{$lang.common.object_type_models}}
									{{elseif $key=='dvds'}}
										{{$lang.common.object_type_dvds}}
									{{elseif $key=='categories'}}
										{{$lang.common.object_type_categories}}
									{{elseif $key=='categories_groups'}}
										{{$lang.common.object_type_category_groups}}
									{{elseif $key=='content_sources_groups'}}
										{{$lang.common.object_type_content_source_groups}}
									{{elseif $key=='tags'}}
										{{$lang.common.object_type_tags}}
									{{elseif $key=='dvds_groups'}}
										{{$lang.common.object_type_dvd_groups}}
									{{elseif $key=='models_groups'}}
										{{$lang.common.object_type_model_groups}}
									{{/if}}
								</a>
							</td>
							<td>{{$item.total}}</td>
							{{foreach item="item_lang" from=$list_languages|smarty:nodefaults}}
								{{assign var="pc_key" value="`$item_lang.code`_pc"}}
								<td>
									{{if $item[$pc_key]<100}}
										<a href="translations.php?no_filter=true&amp;se_object_type={{$item.object_type}}&amp;se_translation_missing_for={{$item_lang.code}}">{{$item[$pc_key]}}%</a>
									{{else}}
										{{$item[$pc_key]}}%
									{{/if}}
								</td>
							{{/foreach}}
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</div>
		<div class="dgb">
			<div class="dgb_actions"></div>
			<div class="dgb_info">
				{{$lang.common.dg_list_stats|count_format:"%1%":$total_num}}
			</div>
		</div>
	</form>
</div>