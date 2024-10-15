<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members_events messages
// =====================================================================================================================

$lang['list_members_events']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members_events']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_members_events']['groups']['display_modes']     = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_members_events']['groups']['pull_content']      = "Выборка контентного объекта для каждого события";

$lang['list_members_events']['params']['items_per_page']            = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members_events']['params']['links_per_page']            = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members_events']['params']['var_from']                  = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members_events']['params']['var_items_per_page']        = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members_events']['params']['event_type']                = "Позволяет выводить только события определенного типа.";
$lang['list_members_events']['params']['skip_users']                = "Используется при включенном параметре [kt|b]mode_global[/kt|b]. Позволяет не выводить события указанных пользователей (список ID пользователей разделенных через запятую).";
$lang['list_members_events']['params']['show_users']                = "Используется при включенном параметре [kt|b]mode_global[/kt|b]. Позволяет выводить события только указанных пользователей (список ID пользователей разделенных через запятую).";
$lang['list_members_events']['params']['var_event_types']           = "Параметр URL-а, в котором передается список айди типов событий, разделенных запятыми, чтобы вывести только события определенного типа(-ов). Перекрывает параметр [kt|b]event_type[/kt|b].";
$lang['list_members_events']['params']['var_user_id']               = "Параметр URL-а, в котором передается ID пользователя, чей список событий / список событий друзей должен быть выведен. Если не задан, то выводится список событий / список событий друзей текущего пользователя.";
$lang['list_members_events']['params']['mode_global']               = "Включает режим отображения глобальных событий.";
$lang['list_members_events']['params']['mode_friends']              = "Включает режим отображения событий друзей пользователя.";
$lang['list_members_events']['params']['mode_subscriptions']        = "Включает режим отображения событий из подписок пользователя.";
$lang['list_members_events']['params']['include_my_events']         = "Включает события текущего пользователя в выборку.";
$lang['list_members_events']['params']['redirect_unknown_user_to']  = "Указывает URL, на который будет перенаправлен незалогиненный пользователь при попытке доступа к режиму отображения, доступному только для залогиненных пользователей.";
$lang['list_members_events']['params']['pull_content']              = "Включает возможность выборки объекта, по отношению к которому произошло событие, для каждого события в списке. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_members_events']['params']['match_locale']              = "При включенном параметре блок выведет только события, произошедшие на текущей локали KVS.";

$lang['list_members_events']['values']['event_type']['1']  = "Добавлено видео";
$lang['list_members_events']['values']['event_type']['2']  = "Добавлен альбом";
$lang['list_members_events']['values']['event_type']['6']  = "Видео стало личным";
$lang['list_members_events']['values']['event_type']['7']  = "Видео стало публичным";
$lang['list_members_events']['values']['event_type']['8']  = "Альбом стал личным";
$lang['list_members_events']['values']['event_type']['9']  = "Альбом стал публичным";
$lang['list_members_events']['values']['event_type']['10'] = "Началась дружба";
$lang['list_members_events']['values']['event_type']['11'] = "Закончилась дружба";
$lang['list_members_events']['values']['event_type']['12'] = "Добавлено сообщение на свою стену";
$lang['list_members_events']['values']['event_type']['13'] = "Добавлено сообщение на чужую стену";
$lang['list_members_events']['values']['event_type']['17'] = "Изменился аватар";
$lang['list_members_events']['values']['event_type']['18'] = "Изменилось сообщение статуса";
$lang['list_members_events']['values']['event_type']['19'] = "Был использован флаг";
$lang['list_members_events']['values']['event_type']['4']  = "Добавлен комментарий к видео";
$lang['list_members_events']['values']['event_type']['5']  = "Добавлен комментарий к альбому";
$lang['list_members_events']['values']['event_type']['14'] = "Добавлен комментарий к модели";
$lang['list_members_events']['values']['event_type']['15'] = "Добавлен комментарий к контент провайдеру";
$lang['list_members_events']['values']['event_type']['16'] = "Добавлен комментарий к DVD";
$lang['list_members_events']['values']['event_type']['20'] = "Добавлен комментарий к плэйлисту";
$lang['list_members_events']['values']['event_type']['21'] = "Добавлен комментарий к записи";

$lang['list_members_events']['block_short_desc'] = "Выводит список событий пользователей с заданными опциями";

$lang['list_members_events']['block_desc'] = "
	Блок предназначен для отображения списка событий с различными опциями фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Существует 3 различных типа листинга событий:[kt|br]
	1) Список глобальных событий. Для получения данного списка должен быть включен параметр блока
	   [kt|b]mode_global[/kt|b].[kt|br]
	2) Список событий пользователя. Если указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит список
	   событий пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список событий текущего пользователя ('мои
	   события'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	3) Список событий личных друзей (лента активности друзей). В этом случае должен быть включен параметр блока
	   [kt|b]mode_friends[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
	   отобразит список событий друзей пользователя, ID которого передается в соответствующем HTTP параметре. Если
	   параметр блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список событий друзей текущего
	   пользователя ('события моих друзей'), а если он не залогинен, то перенаправит его по пути, указанному в
	   параметре блока [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	4) Список событий подписок (лента активности подписок). В этом случае должен быть включен параметр блока
	   [kt|b]mode_subscriptions[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
	   отобразит список событий подписок пользователя, ID которого передается в соответствующем HTTP параметре. Если
	   параметр блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список событий подписок текущего
	   пользователя ('события моих подписок'), а если он не залогинен, то перенаправит его по пути, указанному в
	   параметре блока [kt|b]redirect_unknown_user_to[/kt|b].
	[kt|br][kt|br]

	В списке глобальных событий вы можете исключить события отдельных пользователей используя параметр блока
	[kt|b]skip_users[/kt|b]. Если вы хотите показать глобальный список событий только конкретных пользователей,
	воспользуйтесь параметром блока [kt|b]show_users[/kt|b].
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и
	та же версия кэша. Блок не кэшируется, когда отображает список событий / событий друзей текущего пользователя.
";

$lang['list_members_events']['block_examples'] = "
	[kt|b]Показать события пользователя с ID '87' по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=87
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 последних событий на сайте[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- mode_global[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 последних событий своих друзей[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";

?>