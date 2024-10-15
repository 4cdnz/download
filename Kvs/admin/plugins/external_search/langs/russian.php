<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['external_search']['title']        = "Внешний поиск";
$lang['plugins']['external_search']['description']  = "Позволяет подключить внешний поисковый движок и интегрировать его с поиском или похожими видео / альбомами у вас на сайте.";
$lang['plugins']['external_search']['long_desc']    = "
		Этот плагин позволит вам использовать внешний API для организации поиска или похожих видео и альбомов. Вы можете 
		настроить условия, при которых результаты внешнего поиска полностью заменят результаты внутреннего поиска или 
		дополнят его. Этот плагин, как правило, используется для подключения поиска и похожих видео / альбомов через Sphinx.
";
$lang['permissions']['plugins|external_search']     = $lang['plugins']['external_search']['title'];

$lang['plugins']['external_search']['validation_error_api_no_result']           = "[kt|b][%1%][/kt|b]: указанный API ничего не возвращает";
$lang['plugins']['external_search']['validation_error_api_invalid']             = "[kt|b][%1%][/kt|b]: указанный API не возвращает результаты в нужном формате";
$lang['plugins']['external_search']['divider_videos']                           = "Внешний поиск для видео";
$lang['plugins']['external_search']['divider_albums']                           = "Внешний поиск для альбомов";
$lang['plugins']['external_search']['divider_searches']                         = "Внешний поиск для поисковых запросов";
$lang['plugins']['external_search']['field_enable_external_search_videos']      = "Использовать внешний поиск для видео";
$lang['plugins']['external_search']['field_enable_external_search_albums']      = "Использовать внешний поиск для альбомов";
$lang['plugins']['external_search']['field_enable_external_search_searches']    = "Использовать внешний поиск для поисковых запросов";
$lang['plugins']['external_search']['field_enable_external_search_never']       = "Никогда";
$lang['plugins']['external_search']['field_enable_external_search_always']      = "Всегда";
$lang['plugins']['external_search']['field_enable_external_search_condition']   = "Только если результатов внутреннего поиска меньше чем";
$lang['plugins']['external_search']['field_enable_external_search_hint']        = "при подключении Sphinx выберите [kt|b]Всегда[/kt|b]";
$lang['plugins']['external_search']['field_display_results']                    = "Показывать результаты внешнего поиска";
$lang['plugins']['external_search']['field_display_results_replace']            = "Полностью заменить внутренний поиск";
$lang['plugins']['external_search']['field_display_results_beginning']          = "В начале";
$lang['plugins']['external_search']['field_display_results_end']                = "В конце";
$lang['plugins']['external_search']['field_display_results_hint']               = "при подключении Sphinx выберите [kt|b]Полностью заменить внутренний поиск[/kt|b]";
$lang['plugins']['external_search']['field_api_call']                           = "Вызов API";
$lang['plugins']['external_search']['field_api_call_hint_videos']               = "строка вызова API внешнего поиска; должна содержать токен [kt|b]%QUERY%[/kt|b], который во время запроса заменится на строку поиска, введенную пользователем на сайте; [kt|br] при подключении Sphinx укажите [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_videos.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_api_call_hint_albums']               = "строка вызова API внешнего поиска; должна содержать токен [kt|b]%QUERY%[/kt|b], который во время запроса заменится на строку поиска, введенную пользователем на сайте; [kt|br] при подключении Sphinx укажите [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_albums.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_api_call_hint_searches']             = "строка вызова API внешнего поиска; должна содержать токен [kt|b]%QUERY%[/kt|b], который во время запроса заменится на строку поиска, введенную пользователем на сайте; [kt|br] при подключении Sphinx укажите [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_searches.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_outgoing_url']                       = "URL выхода";
$lang['plugins']['external_search']['field_outgoing_url_hint']                  = "URL выхода при кликах на результаты внешнего поиска; может содержать токен [kt|b]%QUERY%[/kt|b] который во время запроса заменится на строку поиска, введенную пользователем на сайте; [kt|br] при подключении Sphinx укажите URL текущего домена";
$lang['plugins']['external_search']['field_avg_query_time']                     = "Средняя длит. запроса";
$lang['plugins']['external_search']['field_avg_parse_time']                     = "Средняя длит. обработки данных";
$lang['plugins']['external_search']['btn_save']                                 = "Сохранить";
