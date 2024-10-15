<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['database_repair']['title']       = "Статус базы данных";
$lang['plugins']['database_repair']['description'] = "Проверяет базу данных на ошибки и позволяет исправить их средствами MySQL.";
$lang['plugins']['database_repair']['long_desc']   = "
		Используйте этот плагин для просмотра статуса базы данных и исправления ошибок.
";
$lang['permissions']['plugins|database_repair']    = $lang['plugins']['database_repair']['title'];

$lang['plugins']['database_repair']['divider_queries']              = "Активные запросы";
$lang['plugins']['database_repair']['divider_table_details']        = "Таблица %1%";
$lang['plugins']['database_repair']['divider_tables']               = "Таблицы";
$lang['plugins']['database_repair']['field_database_version']       = "Версия MySQL";
$lang['plugins']['database_repair']['dg_queries_col_kill']          = "Убить";
$lang['plugins']['database_repair']['dg_queries_col_id']            = "ID";
$lang['plugins']['database_repair']['dg_queries_col_command']       = "Команда";
$lang['plugins']['database_repair']['dg_queries_col_time']          = "Время";
$lang['plugins']['database_repair']['dg_queries_col_state']         = "Состояние";
$lang['plugins']['database_repair']['dg_queries_col_info']          = "Запрос";
$lang['plugins']['database_repair']['dg_details_col_field_name']    = "Название поля";
$lang['plugins']['database_repair']['dg_details_col_field_type']    = "Тип поля";
$lang['plugins']['database_repair']['dg_details_col_field_key']     = "Ключ поля";
$lang['plugins']['database_repair']['dg_details_col_field_extra']   = "Опции поля";
$lang['plugins']['database_repair']['dg_details_col_index_name']    = "Название индекса";
$lang['plugins']['database_repair']['dg_details_col_index_power']   = "Мощность индекса";
$lang['plugins']['database_repair']['dg_data_col_table_name']       = "Таблица";
$lang['plugins']['database_repair']['dg_data_col_engine']           = "Движок";
$lang['plugins']['database_repair']['dg_data_col_rows']             = "Строки";
$lang['plugins']['database_repair']['dg_data_col_size']             = "Размер";
$lang['plugins']['database_repair']['dg_data_col_status']           = "Статус";
$lang['plugins']['database_repair']['dg_data_col_message']          = "Сообщение";
$lang['plugins']['database_repair']['btn_kill']                     = "Убить выбранные запросы";
$lang['plugins']['database_repair']['btn_repair']                   = "Исправить ошибки";
$lang['plugins']['database_repair']['btn_check_tables']             = "Проверить таблицы";

$lang['plugins']['database_repair']['message_repairing_table']      = "Исправление %1%";
