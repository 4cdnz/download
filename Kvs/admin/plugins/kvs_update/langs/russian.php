<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['kvs_update']['title']         = "Обновление KVS";
$lang['plugins']['kvs_update']['description']   = "Пошаговый ассистент для обновления KVS на новую версию.";
$lang['plugins']['kvs_update']['long_desc']     = "
	Плагин позволяет частично автоматизировать процесс обновления. Вы должны загрузить полученный архив с обновлением и
	указать MD5 хэш архива, который должен быть выведен в личной зоне клиента на сайте KVS. Плагин проверит,
	предназначен ли данный архив для обновления вашего проекта, после чего будет выводить пошаговые инструкции и
	проверять ход их выполнения. Если на каком-либо шаге плагин выдает ошибку о некорректном выполнении шага, повторите
	выполнение инструкций.
";
$lang['permissions']['plugins|kvs_update']      = $lang['plugins']['kvs_update']['title'];

$lang['plugins']['kvs_update']['field_step']                            = "Шаг";
$lang['plugins']['kvs_update']['field_step_value']                      = "%1% из %2%";
$lang['plugins']['kvs_update']['field_description']                     = "Инструкции";
$lang['plugins']['kvs_update']['field_update_version']                  = "Версия обновления";
$lang['plugins']['kvs_update']['field_description_db']                  = "База данных была обновлена автоматически. Вы можете видеть лог обновления ниже. В логе не должно содержаться никаких ошибок, если вы выполняете это обновление впервые.";
$lang['plugins']['kvs_update']['field_update_info']                     = "Доп. информация";
$lang['plugins']['kvs_update']['field_custom_changes']                  = "Кастомные изменения";
$lang['plugins']['kvs_update']['field_custom_changes_notice']           = "[kt|b]ВНИМАНИЕ![/kt|b] Ваш проект содержит кастомные изменения в некоторых файлах, которые будут затронуты обновлением. Если вы продолжите, эти изменения будут утрачены без возможности восстановления.";
$lang['plugins']['kvs_update']['field_custom_changes_confirm']          = "продолжить обновление";
$lang['plugins']['kvs_update']['field_mysql_update_summary']            = "Суммарно";
$lang['plugins']['kvs_update']['field_mysql_update_summary_value']      = "%1% успешных обновлений, %2% ошибок";
$lang['plugins']['kvs_update']['field_mysql_update_log']                = "Лог обновления БД";
$lang['plugins']['kvs_update']['field_get_update']                      = "Доступ к обновлению";
$lang['plugins']['kvs_update']['field_get_update_hint']                 = "войдите, используя ваш доступ в клиентскую зону KVS, и скачайте архив обновления на странице информации вашей лицензии";
$lang['plugins']['kvs_update']['field_update_archive']                  = "Архив обновления";
$lang['plugins']['kvs_update']['field_update_archive_hint']             = "загрузите архив с обновлением, который вы скачали в клиентской зоне KVS (вы также можете загрузить, используя прямую ссылку на архив из зоны клиента KVS)";
$lang['plugins']['kvs_update']['field_validation_hash']                 = "MD5 хэш";
$lang['plugins']['kvs_update']['field_validation_hash_hint']            = "скопируйте MD5 хэш обновления, для проверки целостности файла обновления (MD5 хэш должен выводиться в клиентской зоне KVS для каждого обновления)";
$lang['plugins']['kvs_update']['field_backup']                          = "Резервная копия";
$lang['plugins']['kvs_update']['field_backup_hint']                     = "воспользуйтесь плагином \"Резервное копирование\" в панели администрирования для создания резервной копии вашего проекта";
$lang['plugins']['kvs_update']['field_backup_text']                     = "Я сделал резервную копию";
$lang['plugins']['kvs_update']['field_update_logs']                     = "Логи предыдущих обновлений";
$lang['plugins']['kvs_update']['btn_validate_and_next']                 = "Проверить и дальше";
$lang['plugins']['kvs_update']['btn_continue']                          = "Продолжить";
$lang['plugins']['kvs_update']['btn_start']                             = "Начать";
$lang['plugins']['kvs_update']['btn_finish']                            = "Завершить";
$lang['plugins']['kvs_update']['btn_cancel']                            = "Отменить";
$lang['plugins']['kvs_update']['error_unsupported_update_file_format']  = "[kt|b][%1%][/kt|b]: загруженный файл не поддерживается этим плагином";
$lang['plugins']['kvs_update']['error_unsupported_update_version']      = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для версий [kt|b]%2%[/kt|b], версия вашего проекта: [kt|b]%3%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_domain']       = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для домена [kt|b]%2%[/kt|b], домен вашего проекта: [kt|b]%3%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_multi_db']     = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для префикса базы данных [kt|b]%2%[/kt|b], префикс базы данных вашего проекта: [kt|b]%3%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_package']      = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для пакета [kt|b]%2%[/kt|b], пакет вашего проекта: [kt|b]%3%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_package_1']    = "Базовый";
$lang['plugins']['kvs_update']['error_unsupported_update_package_2']    = "Расширенный";
$lang['plugins']['kvs_update']['error_unsupported_update_package_3']    = "Премиум";
$lang['plugins']['kvs_update']['error_unsupported_update_package_4']    = "Полный";
$lang['plugins']['kvs_update']['error_unsupported_source_code1']        = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для проекта без исходного кода, но ваш проект имеет исходный код";
$lang['plugins']['kvs_update']['error_unsupported_source_code2']        = "[kt|b][%1%][/kt|b]: загруженный файл обновления предназначен для проекта с исходным кодом, но ваш проект не имеет исходного кода";
$lang['plugins']['kvs_update']['error_satellite_db_not_updated']        = "Вы пытаетесь обновить сателлит без обновления главного проекта, сначала необходимо обновить главный проект";
$lang['plugins']['kvs_update']['error_no_stamp']                        = "Информация о файлах проекта недоступна";
$lang['plugins']['kvs_update']['error_invalid_validation_hash']         = "[kt|b][%1%][/kt|b]: некорректный хэш MD5, убедитесь что он был скопирован верно";
$lang['plugins']['kvs_update']['error_invalid_archive_signature']       = "[kt|b][%1%][/kt|b]: некорректная подпись архива обновления, свяжитесь со службой поддержки. Детали: %2%";
$lang['plugins']['kvs_update']['error_backup_is_not_done']              = "[kt|b][%1%][/kt|b]: вы должны подтвердить, что сделали резервную копию";
$lang['plugins']['kvs_update']['error_not_confirmed']                   = "[kt|b][%1%][/kt|b]: вы должны подтвердить, что хотите продолжить";
$lang['plugins']['kvs_update']['error_step_validation_failed']          = "Проверка шага не успешна. Убедитесь, что вы полностью следовали инструкции данного шага.";
$lang['plugins']['kvs_update']['error_step_doesnt_exist']               = "Шаг не существует";