<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['autoreplace_words']['title']          = "Синонимайзер";
$lang['plugins']['autoreplace_words']['description']    = "Заменяет слова на их случайные синонимы в названиях и описаниях, или удаляет запрещенные слова.";
$lang['plugins']['autoreplace_words']['long_desc']      = "
		Вы можете воспользоваться этим плагином для создания уникальных названий и описаний объектов контента, или
		удалить запрещенные слова из них. Вам необходимо задать список известных слов и их синонимов. На основе этого 
		списка плагин будет заменять найденные в названии и / или описании объекта слова на случайные синонимы или на
		пустую строку, если вы хотите удалить слово. Словоформы с большим / маленьким регистром будут определяться 
		автоматически, поэтому вам лишь необходимо задать словарь автозамены в маленьком регистре.
		[kt|br][kt|br]
		Этот плагин будет срабатывать только для нового контента. Если вы хотите выполнить автозамену для существующего
		контента, используйте интерфейс массового редактирования для запуска этого плагина для выбранного множества
		контента. Учитывайте также, что директории контента также будут заменяться в соответствии с названием, что
		приведет к изменению URL-ов контента, если там используются директории (по умолчанию это так).
";
$lang['permissions']['plugins|autoreplace_words']   = $lang['plugins']['autoreplace_words']['title'];

$lang['plugins']['autoreplace_words']['divider_settings']               = "Настройки";
$lang['plugins']['autoreplace_words']['divider_vocabulary']             = "Словарь автозамены";
$lang['plugins']['autoreplace_words']['divider_vocabulary_hint']        = "Каждая запись словаря должна быть указана на отдельной строке и может быть задана одной из 2 форм: [kt|br] 1) Список слов-синонимов, разделенных запятыми. Когда в тексте содержится любое из этих слов, оно заменится на какой-то другой синоним из этого списка. Например: [kt|b]синоним1, синоним2, синоним3[/kt|b] [kt|br] 2) Слово и список замен для него после двоеточия (или просто одно слово для замены). Если вы хотите удалить слово, укажите 2 двойных ковычки \"\" в качестве замены. Например: [kt|b]запрещенное_слово: \"\"[/kt|b] [kt|br] Вы можете использовать как отдельные слова, так и выражения из нескольких слов.";
$lang['plugins']['autoreplace_words']['field_replace_videos']           = "Видео";
$lang['plugins']['autoreplace_words']['field_replace_albums']           = "Альбомы";
$lang['plugins']['autoreplace_words']['field_replace_in_title']         = "Заменять в названии";
$lang['plugins']['autoreplace_words']['field_replace_in_description']   = "Заменять в описании";
$lang['plugins']['autoreplace_words']['field_limit']                    = "Ограничение применения";
$lang['plugins']['autoreplace_words']['field_limit_feeds']              = "Контент созданный фидами импорта";
$lang['plugins']['autoreplace_words']['field_limit_grabbers']           = "Контент созданный грабберами";
$lang['plugins']['autoreplace_words']['field_limit_hint']               = "по умолчанию автозамена применяется ко всему контенту, но вы можете ограничить автозамену только для определенной группы контента";
$lang['plugins']['autoreplace_words']['field_vocabulary_example']       = "синоним1, синоним2, синоним3";
$lang['plugins']['autoreplace_words']['error_row_format']               = "[kt|b][%1%][/kt|b]: строка %2% имеет некорректный формат";
$lang['plugins']['autoreplace_words']['error_word_duplicate']           = "[kt|b][%1%][/kt|b]: строка %2% указывает слово \"%3%\" повторно";
$lang['plugins']['autoreplace_words']['btn_save']                       = "Сохранить";
