<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_posts messages
// =====================================================================================================================

$lang['list_posts']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_posts']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_posts']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_posts']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_posts']['groups']['search']             = "Search posts by text";
$lang['list_posts']['groups']['related']            = "Related posts";
$lang['list_posts']['groups']['display_modes']      = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_posts']['groups']['connected_videos']   = "Posts-to-videos connection";
$lang['list_posts']['groups']['subselects']         = "Select additional data for each post";

$lang['list_posts']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_posts']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_posts']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['list_posts']['params']['var_items_per_page']             = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_posts']['params']['sort_by']                        = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_posts']['params']['var_sort_by']                    = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_posts']['params']['post_type']                      = "Specify post type external ID to display only posts of this type.";
$lang['list_posts']['params']['show_only_with_description']     = "If enabled, only posts with non-empty description are displayed in result.";
$lang['list_posts']['params']['skip_categories']                = "If specified, posts with these categories will not be displayed (comma separated list of category IDs).";
$lang['list_posts']['params']['show_categories']                = "If specified, only posts with these categories will be displayed (comma separated list of category IDs).";
$lang['list_posts']['params']['skip_category_groups']           = "If specified, posts with these category groups will not be displayed (comma separated list of category group IDs).";
$lang['list_posts']['params']['show_category_groups']           = "If specified, only posts with these category groups will be displayed (comma separated list of category group IDs).";
$lang['list_posts']['params']['skip_tags']                      = "If specified, posts with these tags will not be displayed (comma separated list of tag IDs).";
$lang['list_posts']['params']['show_tags']                      = "If specified, only posts with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_posts']['params']['skip_models']                    = "If specified, posts with these models will not be displayed (comma separated list of model IDs).";
$lang['list_posts']['params']['show_models']                    = "If specified, only posts with these models will be displayed (comma separated list of model IDs).";
$lang['list_posts']['params']['skip_model_groups']              = "If specified, posts with these model groups will not be displayed (comma separated list of model group IDs).";
$lang['list_posts']['params']['show_model_groups']              = "If specified, only posts with these model groups will be displayed (comma separated list of model group IDs).";
$lang['list_posts']['params']['skip_users']                     = "If specified, posts from these users will not be displayed (comma separated list of user IDs).";
$lang['list_posts']['params']['show_users']                     = "If specified, only posts from these users will be displayed (comma separated list of user IDs).";
$lang['list_posts']['params']['days_passed_from']               = "Allows filtering by publishing date, e.g. posts added today, yesterday and etc. Specifies the upper limit in number of days passed from today.";
$lang['list_posts']['params']['days_passed_to']                 = "Allows filtering by publishing date, e.g. posts added today, yesterday and etc. Specifies the lower limit in number of days passed from today. Value should be greater than value specified in [kt|b]days_passed_from[/kt|b] block parameter.";
$lang['list_posts']['params']['var_post_type']                  = "URL parameter, which provides post type external ID. If specified, only posts of the type passed in this parameter will be displayed.";
$lang['list_posts']['params']['var_title_section']              = "URL parameter, which provides title first characters to filter the list.";
$lang['list_posts']['params']['var_category_dir']               = "URL parameter, which provides category directory. If specified, only posts from category with this directory will be displayed.";
$lang['list_posts']['params']['var_category_id']                = "URL parameter, which provides category ID. If specified, only posts from category with this ID will be displayed.";
$lang['list_posts']['params']['var_category_ids']               = "URL parameter, which provides comma-separated list of category ID. If specified, only posts from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display posts that belong to all these categories at the same time.";
$lang['list_posts']['params']['var_category_group_dir']         = "URL parameter, which provides category group directory. If specified, only posts from category group with this directory will be displayed.";
$lang['list_posts']['params']['var_category_group_id']          = "URL parameter, which provides category group ID. If specified, only posts from category group with this ID will be displayed.";
$lang['list_posts']['params']['var_category_group_ids']         = "URL parameter, which provides comma-separated list of category group ID. If specified, only posts from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display posts that belong to all these category groups at the same time.";
$lang['list_posts']['params']['var_tag_dir']                    = "URL parameter, which provides tag directory. If specified, only posts that have tag with this directory will be displayed.";
$lang['list_posts']['params']['var_tag_id']                     = "URL parameter, which provides tag ID. If specified, only posts that have tag with this ID will be displayed.";
$lang['list_posts']['params']['var_tag_ids']                    = "URL parameter, which provides comma-separated list of tag ID. If specified, only posts that have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display posts that belong to all these tags at the same time.";
$lang['list_posts']['params']['var_model_dir']                  = "URL parameter, which provides model directory. If specified, only posts that have model with this directory will be displayed.";
$lang['list_posts']['params']['var_model_id']                   = "URL parameter, which provides model ID. If specified, only posts that have model with this ID will be displayed.";
$lang['list_posts']['params']['var_model_ids']                  = "URL parameter, which provides comma-separated list of model ID. If specified, only posts that have models with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display posts that belong to all these models at the same time.";
$lang['list_posts']['params']['var_model_group_dir']            = "URL parameter, which provides model group directory. If specified, only posts from model group with this directory will be displayed.";
$lang['list_posts']['params']['var_model_group_id']             = "URL parameter, which provides model group ID. If specified, only posts from model group with this ID will be displayed.";
$lang['list_posts']['params']['var_model_group_ids']            = "URL parameter, which provides comma-separated list of model group ID. If specified, only posts from model groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display posts that belong to all these model groups at the same time.";
$lang['list_posts']['params']['var_post_date_from']             = "URL parameter, which provides publishing date interval beginning (YYYY-MM-DD or number of days in the past). If specified, only posts published within the given interval will be displayed.";
$lang['list_posts']['params']['var_post_date_to']               = "URL parameter, which provides publishing date interval end (YYYY-MM-DD or number of days in the past). If specified, only posts published within the given interval will be displayed.";
$lang['list_posts']['params']['var_custom1']                    = "URL parameter, which provides value for custom field 1 dynamic filtering.";
$lang['list_posts']['params']['var_custom2']                    = "URL parameter, which provides value for custom field 2 dynamic filtering.";
$lang['list_posts']['params']['var_custom3']                    = "URL parameter, which provides value for custom field 3 dynamic filtering.";
$lang['list_posts']['params']['var_custom4']                    = "URL parameter, which provides value for custom field 4 dynamic filtering.";
$lang['list_posts']['params']['var_custom5']                    = "URL parameter, which provides value for custom field 5 dynamic filtering.";
$lang['list_posts']['params']['var_custom6']                    = "URL parameter, which provides value for custom field 6 dynamic filtering.";
$lang['list_posts']['params']['var_custom7']                    = "URL parameter, which provides value for custom field 7 dynamic filtering.";
$lang['list_posts']['params']['var_custom8']                    = "URL parameter, which provides value for custom field 8 dynamic filtering.";
$lang['list_posts']['params']['var_custom9']                    = "URL parameter, which provides value for custom field 9 dynamic filtering.";
$lang['list_posts']['params']['var_custom10']                   = "URL parameter, which provides value for custom field 10 dynamic filtering.";
$lang['list_posts']['params']['var_custom_flag1']               = "URL parameter, which provides value for custom flag 1 dynamic filtering.";
$lang['list_posts']['params']['var_custom_flag2']               = "URL parameter, which provides value for custom flag 2 dynamic filtering.";
$lang['list_posts']['params']['var_custom_flag3']               = "URL parameter, which provides value for custom flag 3 dynamic filtering.";
$lang['list_posts']['params']['var_search']                     = "URL parameter, which provides search string. If specified, only posts that match search string will be displayed.";
$lang['list_posts']['params']['search_method']                  = "Search method that affects how the actual data is being searched. Full-text methods will default to sorting by relevance, so that more relevant results are on top ignoring [kt|b]sort_by[/kt|b] parameter setting.";
$lang['list_posts']['params']['search_scope']                   = "Configures which data should be searched.";
$lang['list_posts']['params']['search_redirect_enabled']        = "Forces redirect to a post display page if search result returns exactly 1 post.";
$lang['list_posts']['params']['search_redirect_pattern']        = "Post display page pattern to build redirect URL together with [kt|b]search_redirect_enabled[/kt|b] parameter. This pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b]. If not specified, KVS will use default pattern for posts.";
$lang['list_posts']['params']['search_empty_404']               = "Forces block to show 404 error on empty search results.";
$lang['list_posts']['params']['search_empty_redirect_to']       = "Forces block to redirect to the given URL on empty search results. You can use [kt|b]%QUERY%[/kt|b] token that will be replaced with query string.";
$lang['list_posts']['params']['search_blocked_404']             = "Configures block to show 404 error on search queries that contain blocked words. The list of blocked words is defined globally in [kt|b]Settings -> Website settings[/kt|b] area of admin panel.";
$lang['list_posts']['params']['search_blocked_redirect_to']     = "Configures block to redirect to the given URL on search queries that contain blocked words. You can use [kt|b]%QUERY%[/kt|b] token here, which will be replaced with query string. The list of blocked words is defined globally in [kt|b]Settings -> Website settings[/kt|b] area of admin panel.";
$lang['list_posts']['params']['search_disabled_404']            = "Configures block to show 404 error on search queries that are inactive in your database. You can manage search queries in [kt|b]Stats -> Search queries[/kt|b] area of admin panel.";
$lang['list_posts']['params']['search_disabled_redirect_to']    = "Configures block to redirect to the given URL on search queries that are inactive in your database. You can use [kt|b]%QUERY%[/kt|b] token here, which will be replaced with query string. You can manage search queries in [kt|b]Stats -> Search queries[/kt|b] area of admin panel.";
$lang['list_posts']['params']['search_caching_words']           = "Specifies the maximum number of search query words (separated by space or hyphen) when block should be actively caching results. It makes no much sense to cache multi-words queries, as chance they will be repeated within the same caching time is low, and thus caching won't make any benefit. By default the block will cache all single-word queries. Specify [kt|b]2[/kt|b] if you want KVS to cache all 2-worded queries as well.";
$lang['list_posts']['params']['enable_search_on_tags']          = "Enables additional search in tags. If the whole search query matches any of tags or any of their synonyms, all posts with these tags will also be included in search results. Using this parameter will decrease overall search performance.";
$lang['list_posts']['params']['enable_search_on_categories']    = "Enables additional search in categories. If the whole search query matches any of categories or any of their synonyms, all posts with these categories will also be included in search results. Using this parameter will decrease overall search performance.";
$lang['list_posts']['params']['enable_search_on_models']        = "Enables additional search in models. If the whole search query matches any of models or any of their pseudonyms, all posts with these models will also be included in search results. Using this parameter will decrease overall search performance.";
$lang['list_posts']['params']['enable_search_on_users']         = "Enables additional search in users. If the whole search query matches any of users, all posts from these users will also be included in search results. Using this parameter will decrease overall search performance.";
$lang['list_posts']['params']['enable_search_on_custom_fields'] = "Enables additional search in custom fields. Using this parameter will decrease overall search performance.";
$lang['list_posts']['params']['mode_related']                   = "Enables related posts display mode.";
$lang['list_posts']['params']['var_post_dir']                   = "URL parameter, which provides post directory to display its related posts.";
$lang['list_posts']['params']['var_post_id']                    = "URL parameter, which provides post ID to display its related posts.";
$lang['list_posts']['params']['mode_related_category_group_id'] = "Can be used with related by categories only. Specify category group ID / external ID to restrict only related posts from this category group.";
$lang['list_posts']['params']['mode_related_model_group_id']    = "Can be used with related by models only. Specify model group ID / external ID to restrict only related posts from this model group.";
$lang['list_posts']['params']['var_mode_related']               = "Allows dynamically switch related posts display mode by passing one of the following values in URL parameter: [kt|b]1[/kt|b] - by tags, [kt|b]2[/kt|b] - by categories, [kt|b]3[/kt|b] - by models, [kt|b]4[/kt|b] and [kt|b]5[/kt|b] - by title.";
$lang['list_posts']['params']['mode_created']                   = "Enables member's created posts display mode. You can also enable [kt|b]var_user_id[/kt|b] parameter to show created posts of the given user, otherwise created posts of the currently logged user will be displayed.";
$lang['list_posts']['params']['var_user_id']                    = "URL parameter, which provides user ID for the selected display mode.";
$lang['list_posts']['params']['redirect_unknown_user_to']       = "Specifies redirect URL for the visitors that are not logged in and are attempting to access display mode available for members only.";
$lang['list_posts']['params']['allow_delete_created_posts']     = "Allows members to remove their created posts in [kt|b]mode_created[/kt|b] display mode.";
$lang['list_posts']['params']['mode_connected_video']           = "Shows connected posts for the given video.";
$lang['list_posts']['params']['var_connected_video_dir']        = "URL parameter, which provides video directory to display its connected posts.";
$lang['list_posts']['params']['var_connected_video_id']         = "URL parameter, which provides video ID to display its connected posts.";
$lang['list_posts']['params']['show_categories_info']           = "Enables categories data loading for every post. Using this parameter will decrease overall block performance.";
$lang['list_posts']['params']['show_tags_info']                 = "Enables tags data loading for every post. Using this parameter will decrease overall block performance.";
$lang['list_posts']['params']['show_models_info']               = "Enables models data loading for every post. Using this parameter will decrease overall block performance.";
$lang['list_posts']['params']['show_user_info']                 = "Enables user data loading for every post. Using this parameter will decrease overall block performance.";
$lang['list_posts']['params']['show_connected_info']            = "Enables connected video data loading for every post. Using this parameter will decrease overall block performance.";

$lang['list_posts']['values']['search_method']['1']                             = "Whole expression match";
$lang['list_posts']['values']['search_method']['2']                             = "Any expression part match";
$lang['list_posts']['values']['search_method']['3']                             = "Full-text index (natural mode)";
$lang['list_posts']['values']['search_method']['4']                             = "Full-text index (boolean mode)";
$lang['list_posts']['values']['search_method']['5']                             = "Full-text index (with query expansion)";
$lang['list_posts']['values']['search_scope']['0']                              = "Title, description and content";
$lang['list_posts']['values']['search_scope']['1']                              = "Title only";
$lang['list_posts']['values']['search_scope']['2']                              = "Nothing";
$lang['list_posts']['values']['mode_related']['1']                              = "Related by tags";
$lang['list_posts']['values']['mode_related']['2']                              = "Related by categories";
$lang['list_posts']['values']['mode_related']['3']                              = "Related by models";
$lang['list_posts']['values']['mode_related']['4']                              = "Related by title (natural)";
$lang['list_posts']['values']['mode_related']['5']                              = "Related by title (with query expansion)";
$lang['list_posts']['values']['sort_by']['post_id']                             = "Post ID";
$lang['list_posts']['values']['sort_by']['title']                               = "Title";
$lang['list_posts']['values']['sort_by']['dir']                                 = "Directory";
$lang['list_posts']['values']['sort_by']['post_date']                           = "Published on";
$lang['list_posts']['values']['sort_by']['post_date_and_popularity']            = "Published on (by popularity)";
$lang['list_posts']['values']['sort_by']['post_date_and_rating']                = "Published on (by rating)";
$lang['list_posts']['values']['sort_by']['last_time_view_date']                 = "Last viewed";
$lang['list_posts']['values']['sort_by']['last_time_view_date_and_popularity']  = "Last viewed (by popularity)";
$lang['list_posts']['values']['sort_by']['last_time_view_date_and_rating']      = "Last viewed (by rating)";
$lang['list_posts']['values']['sort_by']['rating']                              = "Rating";
$lang['list_posts']['values']['sort_by']['post_viewed']                         = "Popularity";
$lang['list_posts']['values']['sort_by']['most_commented']                      = "Most commented";
$lang['list_posts']['values']['sort_by']['rand()']                              = "Random";

$lang['list_posts']['block_short_desc'] = "Displays list of posts with the given options";

$lang['list_posts']['block_desc'] = "
	Block displays list of posts with different sorting and filtering options. This block is a standard list block
	with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]Display modes[/kt|b]
	[kt|br][kt|br]

	1) [kt|b]Default[/kt|b]. This is default mode to display abtract list of posts. No specific configuration is needed.
	[kt|br][kt|br]

	2) [kt|b]Created posts[/kt|b]. This mode displays posts that were created by a user. When displaying created posts
	for the user currently logged in, this mode will also provide ability to delete any created posts from your site,
	if this operation is permitted by [kt|b]allow_delete_created_posts[/kt|b] parameter.
	[kt|br][kt|br]

	3) [kt|b]Related posts[/kt|b]. You can configure this block to show posts that are similar to some other given post
	by using a wide range of criteria. This is so-called 'related' behavior. You should enable [kt|b]mode_related[/kt|b]
	parameter and additionally you should enable one of the [kt|b]var_post_dir[/kt|b] or [kt|b]var_post_id[/kt|b]
	parameters. In order this functionality to start working, either post ID or post directory should be passed in the
	URL, so that this block knows which post should it base from:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=post-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	4) [kt|b]Posts connected to a video[/kt|b]. In order to use this mode you should enable
	[kt|b]mode_connected_video[/kt|b] parameter and additionally you should enable one of the
	[kt|b]var_connected_video_dir[/kt|b] or [kt|b]var_connected_video_id[/kt|b] parameters. In order this functionality
	to start working, either video ID or video directory should be passed in the URL, so that this block knows which
	video should it base from:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=video-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_list_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_mode_specific']}
";

$lang['list_posts']['block_examples'] = "
	[kt|b]Display 20 news per page sorted by publishing date[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 most popular news[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = post_viewed[kt|br]
	- post_type = news[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all news added today[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	- days_passed_from = 0[kt|br]
	- days_passed_to = 1[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all yesterday's news[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	- days_passed_from = 1[kt|br]
	- days_passed_to = 2[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 15 top rated news in category with directory 'my_category'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rating[kt|br]
	- post_type = news[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display news that have tag with directory 'my_tag', 20 per page and sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- post_type = news[kt|br]
	- var_tag_dir = tag[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?tag=my_tag
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 5 related news for the post with ID '23'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_related[kt|br]
	- var_post_id = post_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?post_id=23
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all connected news for a video with directory 'my-video'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_connected_video[kt|br]
	- var_connected_video_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my-video
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all news created by me (user that is logged in)[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_created[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	- allow_delete_created_posts[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all news created by user with ID 12[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_created[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=12
	[/kt|code]
";
