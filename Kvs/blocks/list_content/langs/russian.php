<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content messages
// =====================================================================================================================

$lang['list_content']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_content']['groups']['search']           = "Текстовый поиск контента";
$lang['list_content']['groups']['display_modes']    = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_content']['groups']['subselects']       = "Выборка дополнительных данных для каждого элемента";
$lang['list_content']['groups']['access']           = "Ограничение доступа к контенту";

$lang['list_content']['params']['items_per_page']               = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content']['params']['links_per_page']               = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content']['params']['var_from']                     = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content']['params']['var_items_per_page']           = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content']['params']['sort_by']                      = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content']['params']['var_sort_by']                  = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content']['params']['skip_categories']              = "Запрещает выводить контент с данными категориями (список ID категорий разделенных через запятую).";
$lang['list_content']['params']['show_categories']              = "Позволяет выводить только контент с данными категориями (список ID категорий разделенных через запятую).";
$lang['list_content']['params']['skip_category_groups']         = "Запрещает выводить контент с данными группами категорий (список ID групп категорий разделенных через запятую).";
$lang['list_content']['params']['show_category_groups']         = "Позволяет выводить только контент с данными группами категорий (список ID групп категорий разделенных через запятую).";
$lang['list_content']['params']['skip_tags']                    = "Запрещает выводить контент с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_content']['params']['show_tags']                    = "Позволяет выводить только контент с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_content']['params']['skip_models']                  = "Запрещает выводить контент с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_content']['params']['show_models']                  = "Позволяет выводить только контент с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_content']['params']['skip_model_groups']            = "Запрещает выводить контент с данными группами моделей (список ID групп моделей разделенных через запятую).";
$lang['list_content']['params']['show_model_groups']            = "Позволяет выводить только контент с данными группами моделей (список ID групп моделей разделенных через запятую).";
$lang['list_content']['params']['skip_content_sources']         = "Запрещает выводить контент с данными контент провайдерами (список ID контент провайдеров разделенных через запятую).";
$lang['list_content']['params']['show_content_sources']         = "Позволяет выводить только контент с данными контент провайдерами (список ID контент провайдеров разделенных через запятую).";
$lang['list_content']['params']['skip_content_source_groups']   = "Запрещает выводить контент с данными группами контент провайдеров (список ID групп контент провайдеров разделенных через запятую).";
$lang['list_content']['params']['show_content_source_groups']   = "Позволяет выводить только контент с данными группами контент провайдеров (список ID групп контент провайдеров разделенных через запятую).";
$lang['list_content']['params']['skip_users']                   = "Запрещает выводить контент от данных пользователей (список ID пользователей разделенных через запятую).";
$lang['list_content']['params']['show_users']                   = "Позволяет выводить только контент от данных пользователей (список ID пользователей разделенных через запятую).";
$lang['list_content']['params']['show_only_with_description']   = "Позволяет выводить только контент, у которого задано не пустое описание.";
$lang['list_content']['params']['show_only_from_same_country']  = "Включите эту опцию для отображения только того контента, который был загружен пользователями из такой же страны, как и текущий пользователь.";
$lang['list_content']['params']['days_passed_from']             = "Позволяет фильтровать список контента по дате публикации, например, контент добавленный сегодня, вчера, за неделю и т.д. Указывает верхнюю границу даты публикации в кол-ве дней, прошедших с текущего дня.";
$lang['list_content']['params']['days_passed_to']               = "Позволяет фильтровать список контента по дате публикации, например, контент добавленный сегодня, вчера, за неделю и т.д. Указывает нижнюю границу даты публикации в кол-ве дней, прошедших с текущего дня. Значение должно быть больше, чем значение в параметре блока [kt|b]days_passed_from[/kt|b].";
$lang['list_content']['params']['is_private']                   = "Позволяет выводить контент различных типов.";
$lang['list_content']['params']['access_level_id']              = "Позволяет выводить контент различных уровней доступа.";
$lang['list_content']['params']['var_category_dir']             = "HTTP параметр, в котором передается директория категории. Позволяет выводить только контент из категории с заданной директорией.";
$lang['list_content']['params']['var_category_id']              = "HTTP параметр, в котором передается ID категории. Позволяет выводить только контент из категории с заданным ID.";
$lang['list_content']['params']['var_tag_dir']                  = "HTTP параметр, в котором передается директория тэга. Позволяет выводить только контент, у которых есть тэг с заданной директорией.";
$lang['list_content']['params']['var_tag_id']                   = "HTTP параметр, в котором передается ID тэга. Позволяет выводить только контент, у которых есть тэг с заданным ID.";
$lang['list_content']['params']['var_model_dir']                = "HTTP параметр, в котором передается директория модели. Позволяет выводить только контент, у которых есть модель с заданной директорией.";
$lang['list_content']['params']['var_model_id']                 = "HTTP параметр, в котором передается ID модели. Позволяет выводить только контент, у которых есть модель с заданным ID.";
$lang['list_content']['params']['var_content_source_dir']       = "HTTP параметр, в котором передается директория контент провайдера. Позволяет выводить контент по контент провайдеру с заданной директорией.";
$lang['list_content']['params']['var_content_source_id']        = "HTTP параметр, в котором передается ID контент провайдера. Позволяет выводить контент по контент провайдеру с заданным ID.";
$lang['list_content']['params']['var_content_source_group_dir'] = "HTTP параметр, в котором передается директория группы контент провайдеров. Позволяет выводить только контент из группы контент провайдеров с заданной директорией.";
$lang['list_content']['params']['var_content_source_group_id']  = "HTTP параметр, в котором передается ID группы контент провайдеров. Позволяет выводить только контент из группы контент провайдеров с заданным ID.";
$lang['list_content']['params']['var_is_private']               = "HTTP параметр, который позволяет выводить контент с различной доступностью базируясь на переданных в параметре значениях (список ID доступов разделенных через запятую, где 2 - премиум контент, 1 - личный контент и 0 - публичный контент). Перекрывает параметр [kt|b]is_private[/kt|b].";
$lang['list_content']['params']['var_post_date_from']           = "HTTP параметр, в котором передается дата начала интервала публикации (YYYY-MM-DD или кол-во дней в прошлом). Позволяет выводить только контент, опубликованный в данном интервале.";
$lang['list_content']['params']['var_post_date_to']             = "HTTP параметр, в котором передается дата конца интервала публикации (YYYY-MM-DD или кол-во дней в прошлом). Позволяет выводить только контент, опубликованный в данном интервале.";
$lang['list_content']['params']['var_search']                   = "HTTP параметр, в котором передается поисковая строка. Позволяет выводить только контент, который соответствуют поисковой строке.";
$lang['list_content']['params']['search_method']                = "Устанавливает метод поиска.";
$lang['list_content']['params']['search_scope']                 = "Указывает, по каким полям должен идти поиск.";
$lang['list_content']['params']['search_empty_404']             = "Заставляет блок выдавать 404 ошибку при пустых результатах поиска.";
$lang['list_content']['params']['search_empty_redirect_to']     = "Заставляет блок делать редирект на указанный URL при пустых результатах поиска. В можете использовать токен [kt|b]%QUERY%[/kt|b], который заменится на поисковую строку.";
$lang['list_content']['params']['enable_search_on_tags']        = "Включает поиск по названию тэга и, если тэг с таким названием находится, то контент с этим тэгом попадает в результат поиска. Может ухудшить производительность поиска.";
$lang['list_content']['params']['enable_search_on_categories']  = "Включает поиск по названию категории и, если категория с таким названием находится, то контент из этой категории попадает в результат поиска. Может ухудшить производительность поиска.";
$lang['list_content']['params']['enable_search_on_models']      = "Включает поиск по названию модели и, если модель с таким названием находится, то контент по этой модели попадает в результат поиска. Может ухудшить производительность поиска.";
$lang['list_content']['params']['enable_search_on_cs']          = "Включает поиск по названию контент провайдера и, если контент провайдер с таким названием находится, то контент этого контент провайдера попадает в результат поиска. Может ухудшить производительность поиска.";
$lang['list_content']['params']['enable_search_on_users']       = "Включает поиск по имени пользователя и, если пользователь с таким именем находится, то контент от этого пользователя попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['list_content']['params']['mode_favourites']              = "Включает режим отображения закладок видео и альбомов пользователя.";
$lang['list_content']['params']['mode_uploaded']                = "Включает режим отображения видео и альбомов, загруженных пользователем.";
$lang['list_content']['params']['mode_purchased']               = "Включает режим отображения купленных пользователем видео и альбомов.";
$lang['list_content']['params']['mode_subscribed']              = "Включает режим отображения видео и альбомов из подписок пользователя.";
$lang['list_content']['params']['mode_futures']                 = "Включает режим отображения будущего контента.";
$lang['list_content']['params']['fav_type']                     = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. Указывает тип закладок: 0 - основной список закладок, 10 - плэйлист, ID которого передается в параметре блока [kt|b]var_playlist_id[/kt|b], 1-9 - доп. списки закладок, которые вы можете использовать по своему усмотрению.";
$lang['list_content']['params']['var_fav_type']                 = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. HTTP параметр, в котором передается тип закладок: 0 - основной список закладок, 10 - плэйлист, ID которого передается в параметре блока [kt|b]var_playlist_id[/kt|b], 1-9 - доп. списки закладок, которые вы можете использовать по своему усмотрению.";
$lang['list_content']['params']['var_playlist_id']              = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. HTTP параметр, в котором передается ID плэйлиста.";
$lang['list_content']['params']['var_user_id']                  = "Используется при включенных параметрах [kt|b]mode_favourites[/kt|b], [kt|b]mode_uploaded[/kt|b], [kt|b]mode_purchased[/kt|b] и [kt|b]mode_subscribed[/kt|b]. HTTP параметр, в котором передается ID пользователя, чьи закладки / загруженный контент должны быть выведены. Если не задан, то выводятся закладки (загруженный контент) текущего пользователя.";
$lang['list_content']['params']['redirect_unknown_user_to']     = "Используется при включенных параметрах [kt|b]mode_favourites[/kt|b], [kt|b]mode_uploaded[/kt|b], [kt|b]mode_purchased[/kt|b] и [kt|b]mode_subscribed[/kt|b]. Указывает путь, на который будет перенаправлен незалогиненный пользователь при попытке доступа к своим личным закладкам / личному загруженному контенту (в большинстве случаев это путь на страницу с формой логина).";
$lang['list_content']['params']['allow_delete_uploaded_content']= "Используется при включенном параметре [kt|b]mode_uploaded[/kt|b]. Разрешает пользователям удалять свой загруженный контент.";
$lang['list_content']['params']['show_content_source_info']     = "Включает выборку данных о контент провайдере для каждого элемента контента (работает медленнее).";
$lang['list_content']['params']['show_categories_info']         = "Включает выборку данных о категориях для каждого элемента контента (работает медленнее).";
$lang['list_content']['params']['show_tags_info']               = "Включает выборку данных о тэгах для каждого элемента контента (работает медленнее).";
$lang['list_content']['params']['show_models_info']             = "Включает выборку данных о моделях для каждого элемента контента (работает медленнее).";
$lang['list_content']['params']['show_user_info']               = "Включает выборку данных о пользователе для каждого элемента контента (работает медленнее).";
$lang['list_content']['params']['show_comments']                = "Включает возможность выборки списка комментариев для каждого элемента контента. Количество комментариев настраивается отдельным параметром блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content']['params']['show_comments_count']          = "Может использоваться при включенном параметре блока [kt|b]show_comments[/kt|b]. Указывает кол-во комментариев, которое выбирается для каждого элемента контента.";
$lang['list_content']['params']['show_private']                 = "Устанавливает для пользователей каких статусов следует показывать личный контент.";
$lang['list_content']['params']['show_premium']                 = "Устанавливает для пользователей каких статусов следует показывать премиум контент.";
$lang['list_content']['params']['limit_access_level']           = "Отображает только видео и альбомы, которые доступны для просмотра пользователю исходя из настроек доступа к ним.";

$lang['list_content']['values']['is_private']['0']                  = "Публичный";
$lang['list_content']['values']['is_private']['1']                  = "Личный";
$lang['list_content']['values']['is_private']['2']                  = "Премиум";
$lang['list_content']['values']['is_private']['0|1']                = "Публичный и личный";
$lang['list_content']['values']['is_private']['0|2']                = "Публичный и премиум";
$lang['list_content']['values']['is_private']['1|2']                = "Личный и премиум";
$lang['list_content']['values']['access_level_id']['1']             = "Публичные";
$lang['list_content']['values']['access_level_id']['2']             = "Для залогиненных";
$lang['list_content']['values']['access_level_id']['3']             = "Для премиум";
$lang['list_content']['values']['search_method']['1']               = "Полное совпадение с запросом";
$lang['list_content']['values']['search_method']['2']               = "Совпадение с элементами запроса";
$lang['list_content']['values']['search_method']['3']               = "Полнотекстовый индекс (натуральный)";
$lang['list_content']['values']['search_method']['4']               = "Полнотекстовый индекс (булевый режим)";
$lang['list_content']['values']['search_method']['5']               = "Полнотекстовый индекс (с расширенным подзапросом)";
$lang['list_content']['values']['search_scope']['0']                = "Название и описание";
$lang['list_content']['values']['search_scope']['1']                = "Только название";
$lang['list_content']['values']['search_scope']['2']                = "Ничего";
$lang['list_content']['values']['show_private']['1']                = "Только для зарег. пользователей";
$lang['list_content']['values']['show_private']['2']                = "Только для премиум пользователей";
$lang['list_content']['values']['show_premium']['1']                = "Только для зарег. пользователей";
$lang['list_content']['values']['show_premium']['2']                = "Только для премиум пользователей";
$lang['list_content']['values']['sort_by']['object_id']             = "ID объекта";
$lang['list_content']['values']['sort_by']['title']                 = "Название";
$lang['list_content']['values']['sort_by']['dir']                   = "Директория";
$lang['list_content']['values']['sort_by']['post_date']             = "Дата публикации";
$lang['list_content']['values']['sort_by']['last_time_view_date']   = "Просмотрен последний раз";
$lang['list_content']['values']['sort_by']['rating']                = "Рейтинг";
$lang['list_content']['values']['sort_by']['object_viewed']         = "Популярность";
$lang['list_content']['values']['sort_by']['most_favourited']       = "Добавление в закладки";
$lang['list_content']['values']['sort_by']['most_commented']        = "Кол-во комментариев";
$lang['list_content']['values']['sort_by']['most_purchased']        = "Кол-во покупок";
$lang['list_content']['values']['sort_by']['rand()']                = "Случайно";

$lang['list_content']['block_short_desc'] = "Выводит смешанный список видео и альбомов с заданными опциями";


$lang['list_content']['block_desc'] = "
	Блок предназначен для отображения смешанного списка видео и альбомов с различными опциями сортировки и
	фильтрации. Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Существует 6 различных типов листинга смешанного контента:[kt|br]
	1) Список контента, добавленного в личные закладки пользователя. В этом случае должен быть включен параметр блока
	   [kt|b]mode_favourites[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
	   отобразит список закладок пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр
	   блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список закладок текущего пользователя
	   ('мои закладки'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b]. В данном режиме списка существует также возможность удаления контента из
	   своих закладок. Список закладок может быть разделен на несколько типов списков. За вывод списка определенного
	   типа отвечает параметр блока [kt|b]var_fav_type[/kt|b], который ссылается на HTTP параметр, в котором вы должны
	   передать тип списка закладок. Для того, чтобы вывести закладки из какого-либо плэйлиста, необходимо передать ID
	   этого плэйлиста в HTTP параметре, который задается параметром блока [kt|b]var_playlist_id[/kt|b].[kt|br]
	2) Список контента, загруженного пользователем. В этом случае должен быть включен параметр блока
	   [kt|b]mode_uploaded[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит
	   список контента пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список контента текущего пользователя
	   ('мой контент'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b]. В данном режиме списка существует также возможность удаления контента из
	   своих загрузок.[kt|br]
	3) Список контента, купленного пользователем. В этом случае должен быть включен параметр блока
	   [kt|b]mode_purchased[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
	   отобразит список купленного контента пользователем, ID которого передается в соответствующем HTTP параметре. Если
	   параметр блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список купленного контента текущего
	   пользователя ('мой купленный контент'), а если он не залогинен, то перенаправит его по пути, указанному в
	   параметре блока [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	4) Контент из подписок пользователя. В этом случае должен быть включен параметр блока [kt|b]mode_subscribed[/kt|b].
	   Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит контент из подписок
	   пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести контент из подписок текущего пользователя ('мой
	   контент из подписок'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	5) Список будущего контента. Для отображения данного списка следует использовать параметр блока
	   [kt|b]mode_futures[/kt|b].[kt|br]
	6) Обычный список контента для просмотра. Для данного списка работают все параметры фильтрации и сортировки.
	[kt|br][kt|br]

	Если вам нужно исключить из результата контент из каких-либо категорий или содержащие какие-либо тэги, вам следует
	воспользоваться параметрами блока [kt|b]skip_categories[/kt|b] или [kt|b]skip_tags[/kt|b]. Для того, чтобы
	отобразить контент только из каких-либо категорий или содержащих какие-либо тэги, используйте параметры блока
	[kt|b]show_categories[/kt|b] или [kt|b]show_tags[/kt|b].
	[kt|br][kt|br]

	Если вы хотите показать только контент с непустым описанием, то включите параметр блока
	[kt|b]show_only_with_description[/kt|b].
	[kt|br][kt|br]

	Для того, чтобы вывести контент по какой-либо категории (тэгу или модели, по какому-либо контент провайдеру),
	следует использовать один из параметров блока [kt|b]var_xxx_dir[/kt|b] или [kt|b]var_xxx_id[/kt|b].
	[kt|br][kt|br]

	Вы можете настраивать этот блок, чтобы он выводил контент, который появился в заданный промежуток времени,
	относительно текущей даты, например, контент за сегодня, контент за вчера, за неделю и т.д. Этот промежуток должен
	задаваться парой параметров блока [kt|b]days_passed_from[/kt|b] и [kt|b]days_passed_to[/kt|b].
	[kt|br][kt|br]

	Для реализации поиска по контенту вы можете использовать параметр блока [kt|b]var_search[/kt|b]. Он должен
	ссылаться на HTTP параметр, который содержит поисковую строку. Вы можете также выбрать один из нескольких поисковых
	методов в параметре блока [kt|b]search_method[/kt|b]. Результаты поиска могут быть расширены за счет включения
	параметров блока [kt|b]enable_search_on_xxx[/kt|b], однако они могут сильно ухудшить производительность поиска.
	[kt|br][kt|br]

	Фильтрация по категории, модели, тэгу, контент провайдеру и поисковой строке является взаимоисключающей, т.е.
	нельзя одновременно использовать фильтрацию и по категории, и по поисковой строке.
	[kt|br][kt|br]

	Если вам необходимо спрятать личный или премиум контент от различных категорий пользователей (незарегистрированных
	или стандартных пользователей), воспользуйтесь параметрами блока [kt|b]show_private[/kt|b] или [kt|b]show_premium[/kt|b].
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и та
	же версия кэша. Блок не кэшируется, когда отображает список закладок текущего пользователя. При выводе результатов
	поиска по строке поведение кэширования зависит от поискового запроса.
";

$lang['list_content']['block_examples'] = "
";

?>