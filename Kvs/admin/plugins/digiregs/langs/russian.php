<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['digiregs']['title']          = "DigiRegs";
$lang['plugins']['digiregs']['description']    = "Автоматически определяет видео с авторскими правами и позволяет снизить кол-во DMCA обращений.";
$lang['plugins']['digiregs']['long_desc']      = "
		DigiRegs предоставляет платный доступ к API для определения авторских прав на видеофайлы. Используйте этот
		плагин, чтобы автоматизировать обработку видео с авторскими правами на своем сайте и снизить количество DMCA
		запросов на удаление контента с вашего сайта.
";
$lang['permissions']['plugins|digiregs']   = $lang['plugins']['digiregs']['title'];

$lang['plugins']['digiregs']['divider_general']                                                     = "Настройки API DigiRegs";
$lang['plugins']['digiregs']['divider_copyright']                                                   = "Определение видео с авторскими правами";
$lang['plugins']['digiregs']['divider_copyright_hint']                                              = "KVS будет отправлять скриншоты новых видео в DigiRegs для анализа перед их обработкой; это позволит запретить обработку видео с авторскими правами, или обрезать их до разрешенной длительности.";
$lang['plugins']['digiregs']['field_api_key']                                                       = "Ключ API";
$lang['plugins']['digiregs']['field_api_key_hint']                                                  = "<a href=\"https://digiregs.net/kvs/register.php\">Зарегистрируйтесь</a> или <a href=\"https://digiregs.net/kvs/\">Залогиньтесь</a> в ваш профиль DigiRegs чтобы получить ключ API";
$lang['plugins']['digiregs']['field_balance']                                                       = "Ваш баланс";
$lang['plugins']['digiregs']['field_balance_value']                                                 = "%1% кредитов";
$lang['plugins']['digiregs']['field_on_empty_balance']                                              = "При отсутствии средств";
$lang['plugins']['digiregs']['field_on_empty_balance_wait']                                         = "Ожидать пополнения баланса";
$lang['plugins']['digiregs']['field_on_empty_balance_ignore']                                       = "Обрабатывать видео без проверки";
$lang['plugins']['digiregs']['field_on_empty_balance_hint']                                         = "установите режим ожидания, если вы хотите чтобы проверялось 100% видео, но это может привести к забиванию очереди обработки";
$lang['plugins']['digiregs']['field_enable_debug']                                                  = "Включить отладку";
$lang['plugins']['digiregs']['field_enable_debug_enabled']                                          = "включена";
$lang['plugins']['digiregs']['field_enable_debug_log']                                              = "лог отладки";
$lang['plugins']['digiregs']['field_enable_debug_hint']                                             = "включает логгирование всех запросов API";
$lang['plugins']['digiregs']['field_copyright_enable']                                              = "Включить определение видео с авторскими правами";
$lang['plugins']['digiregs']['field_copyright_enable_enabled']                                      = "включить";
$lang['plugins']['digiregs']['field_copyright_known_action']                                        = "Контент известных правообладателей";
$lang['plugins']['digiregs']['field_copyright_known_action_delete']                                 = "Удалять любой контент с авторскими правами";
$lang['plugins']['digiregs']['field_copyright_known_action_delete_hint']                            = "любой контент от известных правообладателей не будет добавлен [kt|br] [kt|b]ПРИМЕЧАНИЕ: [/kt|b] это действие допустимо только для нового контента";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_with_duration_limit']         = "Разрешать только контент с известной допустимой длительностью";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_with_duration_limit_hint']    = "будет разрешен только контент от правообладателей, которые позволяют публиковать их контент с ограничением по длительности";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_from_known_sources']          = "Разрешать только контент от известных контент провайдеров / каналов";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_from_known_sources_hint']     = "будет разрешен только контент от правообладателей, которые добавлены как контент провайдеры / каналы на вашем сайте (по совпадению названия или синонимов)";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_all']                              = "Разрешать любой контент с авторскими правами";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_all_hint']                         = "будет разрешен весь контент от правообладателей, но вы можете использовать дополнительные опции для дальнейшей ручной обработки такого контента";
$lang['plugins']['digiregs']['field_copyright_known_deactivate']                                    = "Деактивировать контент";
$lang['plugins']['digiregs']['field_copyright_known_deactivate_hint']                               = "весь контент от правообладателей будет деактивирован";
$lang['plugins']['digiregs']['field_copyright_known_set_admin_flag']                                = "Устанавливать флаг администратора";
$lang['plugins']['digiregs']['field_copyright_known_set_admin_flag_hint']                           = "весь контент от правообладателей получит указанный флаг администратора";
$lang['plugins']['digiregs']['field_copyright_known_truncate_duration']                             = "Обрезать длительность при наличии сведений от правообладателя";
$lang['plugins']['digiregs']['field_copyright_known_truncate_duration_hint']                        = "контент от правообладателей, которые допускают публикацию урезанных версий, будет обрезаться до разрешенной длительности";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources']                        = "Создавать контент провайдеры";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources_hint']                   = "для известных правобладателей будут автоматически создаваться контент провайдеры, если у вас их нет; это может в дальнейшем использоваться для публикации ссылки на сайт правообладателя вместе с контентом";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources_disabled']               = "в неактивном статусе";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds']                                   = "Создавать каналы";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds_hint']                              = "для известных правобладателей будут автоматически создаваться каналы, если у вас их нет; это может в дальнейшем использоваться для публикации ссылки на сайт правообладателя вместе с контентом";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds_disabled']                          = "в неактивном статусе";
$lang['plugins']['digiregs']['field_copyright_unknown_action']                                      = "Контент с неизвестными водяными знаками";
$lang['plugins']['digiregs']['field_copyright_unknown_action_delete']                               = "Удалять любой контент с неизвестными водяными знаками";
$lang['plugins']['digiregs']['field_copyright_unknown_action_delete_hint']                          = "любой контент с неизвестными водяными знаками не будет добавлен [kt|br] [kt|b]ПРИМЕЧАНИЕ: [/kt|b] это действие допустимо только для нового контента";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_only_from_known_sources']        = "Разрешать только контент от известных контент провайдеров / каналов";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_only_from_known_sources_hint']   = "будет разрешен только контент с неизвестными водяными знаками, которые добавлены как контент провайдеры / каналы на вашем сайте (по совпадению названия или синонимов)";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_all']                            = "Разрешать любой контент с неизвестными водяными знаками";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_all_hint']                       = "будет разрешен весь контент с неизвестными водяными знаками, но вы можете использовать дополнительные опции для дальнейшей ручной обработки такого контента";
$lang['plugins']['digiregs']['field_copyright_unknown_deactivate']                                  = "Деактивировать контент";
$lang['plugins']['digiregs']['field_copyright_unknown_deactivate_hint']                             = "весь контент с неизвестными водяными знаками будет деактивирован";
$lang['plugins']['digiregs']['field_copyright_unknown_set_admin_flag']                              = "Устанавливать флаг администратора";
$lang['plugins']['digiregs']['field_copyright_unknown_set_admin_flag_hint']                         = "весь контент с неизвестными водяными знаками получит указанный флаг администратора";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources']                      = "Создавать контент провайдеры";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources_hint']                 = "для неизвестных водяных знаков будут автоматически создаваться контент провайдеры, если у вас их нет";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources_disabled']             = "в неактивном статусе";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds']                                 = "Создавать каналы";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds_hint']                            = "для неизвестных водяных знаков будут автоматически создаваться каналы, если у вас их нет";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds_disabled']                        = "в неактивном статусе";
$lang['plugins']['digiregs']['field_copyright_empty_action']                                        = "Неопределенный контент";
$lang['plugins']['digiregs']['field_copyright_empty_action_delete']                                 = "Удалять любой неопределенный контент";
$lang['plugins']['digiregs']['field_copyright_empty_action_delete_hint']                            = "любой неопределенный контент, по которому Digiregs не нашел никакой информации, не будет добавлен [kt|br] [kt|b]ПРИМЕЧАНИЕ: [/kt|b] это действие допустимо только для нового контента";
$lang['plugins']['digiregs']['field_copyright_empty_action_allow_all']                              = "Разрешать любой неопределенный контент";
$lang['plugins']['digiregs']['field_copyright_empty_action_allow_all_hint']                         = "будет разрешен весь неопределенный контент, по которому Digiregs не нашел никакой информации";
$lang['plugins']['digiregs']['field_copyright_blacklisted_holders']                                 = "Черный список правобладателей и водяных знаков";
$lang['plugins']['digiregs']['field_copyright_blacklisted_holders_hint']                            = "список правобладателей или водяных знаков, разделенных запятыми или с новой строки, контент которых никогда не должен добавляться";
$lang['plugins']['digiregs']['field_apply_to']                                                      = "Использовать для";
$lang['plugins']['digiregs']['field_apply_to_list']['admins']                                       = "Новых видео, добавленных вручную админами";
$lang['plugins']['digiregs']['field_apply_to_list']['import']                                       = "Новых видео, импортированных админами";
$lang['plugins']['digiregs']['field_apply_to_list']['feeds']                                        = "Новых видео, добавленных фидами импорта";
$lang['plugins']['digiregs']['field_apply_to_list']['grabbers']                                     = "Новых видео, импортированных грабберами";
$lang['plugins']['digiregs']['field_apply_to_list']['ftp']                                          = "Новых видео, добавленных через плагин FTP загрузки";
$lang['plugins']['digiregs']['field_apply_to_list']['site']                                         = "Новых видео, добавленных пользователями сайта";
$lang['plugins']['digiregs']['field_apply_to_list']['manual']                                       = "Ручного режима выполнения";
$lang['plugins']['digiregs']['field_apply_to_list']['manual_hint']                                  = "ручной режим выполнения доступен через интерфейс массового редактирования и может использоваться для тестирования";
$lang['plugins']['digiregs']['field_apply_to_feeds_all']                                            = "Всеми фидами";
$lang['plugins']['digiregs']['field_apply_to_feeds_selected']                                       = "Только указанными фидами";
$lang['plugins']['digiregs']['field_apply_to_feeds_feed']                                           = "Фид [kt|b]%1%[/kt|b]";
$lang['plugins']['digiregs']['field_apply_only_with_empty_content_source']                          = "Только для видео без указанного контент провайдера";
$lang['plugins']['digiregs']['field_apply_only_with_empty_content_source_hint']                     = "включите, если хотите чтобы плагин пропускал видео, у которых установлен контент провайдер";
$lang['plugins']['digiregs']['btn_save']                                                            = "Сохранить";
$lang['plugins']['digiregs']['error_invalid_api_response_code']                                     = "Неверный ключ API, или проблемы с доступом к API DigiRegs (%1%)";
$lang['plugins']['digiregs']['error_invalid_api_response_format']                                   = "Неверный формат ответа API";
$lang['plugins']['digiregs']['error_delete_not_possible_with_manual_execution']                     = "[kt|b][%1%][/kt|b]: использование этой опции не совместимо с ручным режимом запуска";