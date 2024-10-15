<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['template_cache_cleanup']['title']         = "Очистка кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['description']   = "Удаляет устаревший кэш блоков на диске.";
$lang['plugins']['template_cache_cleanup']['long_desc']     = "
		Этот плагин предназначен для автоматического подчищения файлов кэша, которые больше не требуются вашему сайту.
		Плагин сам определяет, какие файлы и когда могут быть удалены без влияния на работу сайта. Рекомендуется 
		устанавливать интервал от 24 до 48 часов, а также настроить его запуск во время дня, когда у вас на сайте меньше 
		всего трафика. Плагин будет стараться не перегрузить диск, поэтому для проектов с большим числом контента он 
		может выполняться на фоне более 8 часов, что абсолютно нормально.
		[kt|br][kt|br]
		[kt|b]ВАЖНО[/kt|b]: не пытайтесь использовать этот плагин для сброса кэша, т.к. он удаляет только тот кэш, 
		который считает устаревшим, а не полностью весь кэш. Для полного сброса кэша предназначены опции в боковом меню
		раздела UI сайта.
";
$lang['permissions']['plugins|template_cache_cleanup']      = $lang['plugins']['template_cache_cleanup']['title'];

$lang['plugins']['template_cache_cleanup']['message_calculating']                           = "Расчет размера %1%";

$lang['plugins']['template_cache_cleanup']['field_cache_folder']            = "Расположение кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['field_cache_size']              = "Размер кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['field_storage_folder']          = "Расположение кэша \$storage";
$lang['plugins']['template_cache_cleanup']['field_storage_size']            = "Размер кэша \$storage";
$lang['plugins']['template_cache_cleanup']['field_size_check']              = "N/A";
$lang['plugins']['template_cache_cleanup']['field_size_files']              = "файл(ов)";
$lang['plugins']['template_cache_cleanup']['field_speed']                   = "Скорость очистки";
$lang['plugins']['template_cache_cleanup']['field_speed_veryslow']          = "Очень медленно";
$lang['plugins']['template_cache_cleanup']['field_speed_slow']              = "Медленно";
$lang['plugins']['template_cache_cleanup']['field_speed_normal']            = "Обычно";
$lang['plugins']['template_cache_cleanup']['field_speed_fast']              = "Быстро";
$lang['plugins']['template_cache_cleanup']['field_speed_ultrafast']         = "Очень быстро";
$lang['plugins']['template_cache_cleanup']['field_speed_hint']              = "slower speed means lower disk usage and produces lower server load";
$lang['plugins']['template_cache_cleanup']['field_enable']                  = "Запуск по расписанию";
$lang['plugins']['template_cache_cleanup']['field_enable_enabled']          = "включен";
$lang['plugins']['template_cache_cleanup']['field_schedule']                = "Расписание";
$lang['plugins']['template_cache_cleanup']['field_schedule_interval']       = "минимальный интервал (ч)";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod']            = "время дня";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod_any']        = "как получится";
$lang['plugins']['template_cache_cleanup']['field_schedule_hint']           = "укажите минимальный интервал между повторными запусками этого плагина, а также время дня если требуется";
$lang['plugins']['template_cache_cleanup']['field_last_exec']               = "Последний запуск";
$lang['plugins']['template_cache_cleanup']['field_last_exec_none']          = "нет";
$lang['plugins']['template_cache_cleanup']['field_last_exec_data']          = "%1% секунд, %2% файлов удалено";
$lang['plugins']['template_cache_cleanup']['field_next_exec']               = "Следующий запуск";
$lang['plugins']['template_cache_cleanup']['field_next_exec_none']          = "нет";
$lang['plugins']['template_cache_cleanup']['field_next_exec_running']       = "работает сейчас...";
$lang['plugins']['template_cache_cleanup']['btn_save']                      = "Сохранить";
$lang['plugins']['template_cache_cleanup']['btn_calculate_stats']           = "Вычислить размер кэша";
$lang['plugins']['template_cache_cleanup']['btn_start_now']                 = "Запустить очистку";
