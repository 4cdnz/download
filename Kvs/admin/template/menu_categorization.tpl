{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('categories|view',$smarty.session.permissions) || in_array('category_groups|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="categorization_categories"><i class="icon icon-type-category"></i>{{$lang.categorization.submenu_group_categories}}</h1>
	<ul id="categorization_categories">
		{{if in_array('categories|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='categories.php'}}
				<li><span><i class="icon icon-type-category"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_categories_list}}</span></li>
			{{else}}
				<li><a href="categories.php"><i class="icon icon-type-category"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_categories_list}}</a></li>
			{{/if}}

			{{if in_array('categories|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='categories.php'}}
					<li><span><i class="icon icon-type-category"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_category}}</span></li>
				{{else}}
					<li><a href="categories.php?action=add_new"><i class="icon icon-type-category"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_category}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
		{{if in_array('category_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='categories_groups.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-category"></i></i>{{$lang.categorization.submenu_option_category_groups_list}}</span></li>
			{{else}}
				<li><a href="categories_groups.php"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-category"></i></i>{{$lang.categorization.submenu_option_category_groups_list}}</a></li>
			{{/if}}

			{{if in_array('category_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='categories_groups.php'}}
					<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-category"></i></i>{{$lang.categorization.submenu_option_add_category_group}}</span></li>
				{{else}}
					<li><a href="categories_groups.php?action=add_new"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-category"></i></i>{{$lang.categorization.submenu_option_add_category_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('models|view',$smarty.session.permissions) || in_array('models_groups|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="categorization_models"><i class="icon icon-type-model"></i>{{$lang.categorization.submenu_group_models}}</h1>
	<ul id="categorization_models">
		{{if in_array('models|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='models.php'}}
				<li><span><i class="icon icon-type-model"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_models_list}}</span></li>
			{{else}}
				<li><a href="models.php"><i class="icon icon-type-model"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_models_list}}</a></li>
			{{/if}}

			{{if in_array('models|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='models.php'}}
					<li><span><i class="icon icon-type-model"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_model}}</span></li>
				{{else}}
					<li><a href="models.php?action=add_new"><i class="icon icon-type-model"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_model}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if in_array('models_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='models_groups.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-model"></i></i>{{$lang.categorization.submenu_option_model_groups_list}}</span></li>
			{{else}}
				<li><a href="models_groups.php"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-model"></i></i>{{$lang.categorization.submenu_option_model_groups_list}}</a></li>
			{{/if}}

			{{if in_array('models_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='models_groups.php'}}
					<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-model"></i></i>{{$lang.categorization.submenu_option_add_model_group}}</span></li>
				{{else}}
					<li><a href="models_groups.php?action=add_new"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-model"></i></i>{{$lang.categorization.submenu_option_add_model_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('content_sources|view',$smarty.session.permissions) || in_array('content_sources_groups|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="categorization_content_sources"><i class="icon icon-type-content-source"></i>{{$lang.categorization.submenu_group_content_sources}}</h1>
	<ul id="categorization_content_sources">
		{{if in_array('content_sources|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='content_sources.php'}}
				<li><span><i class="icon icon-type-content-source"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_content_sources_list}}</span></li>
			{{else}}
				<li><a href="content_sources.php"><i class="icon icon-type-content-source"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_content_sources_list}}</a></li>
			{{/if}}

			{{if in_array('content_sources|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='content_sources.php'}}
					<li><span><i class="icon icon-type-content-source"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_content_source}}</span></li>
				{{else}}
					<li><a href="content_sources.php?action=add_new"><i class="icon icon-type-content-source"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_content_source}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if in_array('content_sources_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='content_sources_groups.php'}}
				<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-content-source"></i></i>{{$lang.categorization.submenu_option_content_source_groups_list}}</span></li>
			{{else}}
				<li><a href="content_sources_groups.php"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-list"></i><i class="icon icon-top icon-type-content-source"></i></i>{{$lang.categorization.submenu_option_content_source_groups_list}}</a></li>
			{{/if}}

			{{if in_array('content_sources_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='content_sources_groups.php'}}
					<li><span><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-content-source"></i></i>{{$lang.categorization.submenu_option_add_content_source_group}}</span></li>
				{{else}}
					<li><a href="content_sources_groups.php?action=add_new"><i class="icon icon-type-group"><i class="icon icon-bottom icon-action-add"></i><i class="icon icon-top icon-type-content-source"></i></i>{{$lang.categorization.submenu_option_add_content_source_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('tags|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="categorization_tags"><i class="icon icon-type-tag"></i>{{$lang.categorization.submenu_group_tags}}</h1>
	<ul id="categorization_tags">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='tags.php'}}
			<li><span><i class="icon icon-type-tag"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_tags_list}}</span></li>
		{{else}}
			<li><a href="tags.php"><i class="icon icon-type-tag"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_tags_list}}</a></li>
		{{/if}}

		{{if in_array('tags|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='tags.php'}}
				<li><span><i class="icon icon-type-tag"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_tags}}</span></li>
			{{else}}
				<li><a href="tags.php?action=add_new"><i class="icon icon-type-tag"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_tags}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('flags|view',$smarty.session.permissions)}}
	<h1 data-submenu-group="categorization_flags"><i class="icon icon-type-flag"></i>{{$lang.categorization.submenu_group_flags}}</h1>
	<ul id="categorization_flags">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='flags.php'}}
			<li><span><i class="icon icon-type-flag"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_flags_list}}</span></li>
		{{else}}
			<li><a href="flags.php"><i class="icon icon-type-flag"><i class="icon icon-bottom icon-action-list"></i></i>{{$lang.categorization.submenu_option_flags_list}}</a></li>
		{{/if}}

		{{if in_array('flags|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='flags.php'}}
				<li><span><i class="icon icon-type-flag"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_flag}}</span></li>
			{{else}}
				<li><a href="flags.php?action=add_new"><i class="icon icon-type-flag"><i class="icon icon-bottom icon-action-add"></i></i>{{$lang.categorization.submenu_option_add_flag}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
</div>