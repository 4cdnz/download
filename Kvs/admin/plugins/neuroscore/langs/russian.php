<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['neuroscore']['title']          = "Neuroscore";
$lang['plugins']['neuroscore']['description']    = "Интегрирует технологии ИИ в KVS.";
$lang['plugins']['neuroscore']['long_desc']      = "
		Neuroscore предоставляет API нейросетей для разных задач классификации. Зарегистрируйтесь на сайте 
		https://neuroscore.ai, укажите ваш ключ API и получите преимущество использования современных ИИ технологий на 
		вашем проекте.
		[kt|br][kt|br]
		Данный плагин работает асинхронно. Сначала он отправляет задачи в Neuroscore, после чего каждые 5 минут 
		проверяет ход их выполнения. Как только задачи будут завершены, данные видео обновятся на основе полученных 
		результатов.
";
$lang['permissions']['plugins|neuroscore']   = $lang['plugins']['neuroscore']['title'];

$lang['plugins']['neuroscore']['divider_general']                           = "Настройки API Neuroscore";
$lang['plugins']['neuroscore']['divider_score']                             = "Скоринг скриншотов";
$lang['plugins']['neuroscore']['divider_score_hint']                        = "Использует нейронную сеть, чтобы определить наилучшие скриншоты. Типичная схема использования - создавать в KVS 10-20 скриншотов для каждого видео, после чего отправлять их в Neuroscore для определения наилучшего скриншота, или нескольких лучших, которые затем могут быть отротированы через ротатор с целью получения наиболее кликабельного скриншота.";
$lang['plugins']['neuroscore']['divider_title']                             = "Переписывание названий";
$lang['plugins']['neuroscore']['divider_title_hint']                        = "Использует нейронную сеть сеть для генерации новых названий из старых, а также из категорий и моделей.";
$lang['plugins']['neuroscore']['divider_categories']                        = "Автоподбор категорий (тэгов)";
$lang['plugins']['neuroscore']['divider_categories_hint']                   = "Анализирует скриншоты, чтобы автоматически подобрать категории или тэги.";
$lang['plugins']['neuroscore']['divider_models']                            = "Автоподбор моделей";
$lang['plugins']['neuroscore']['divider_models_hint']                       = "Анализирует скриншоты, чтобы автоматически подобрать известных моделей через технологию определение лиц.";
$lang['plugins']['neuroscore']['field_api_key']                             = "Ключ API";
$lang['plugins']['neuroscore']['field_api_key_hint']                        = "<a href=\"https://neuroscore.ai/app/#/signup\">зарегистрируйтесь</a> или <a href=\"https://neuroscore.ai/app/#/login\">залогиньтесь</a> в ваш NeuroScore.ai профиль чтобы получить ваш ключ (вы можете увидеть его в разделе Profile > Api Key)";
$lang['plugins']['neuroscore']['field_balance']                             = "Ваш баланс";
$lang['plugins']['neuroscore']['field_on_empty_balance']                    = "При отсутствии средств";
$lang['plugins']['neuroscore']['field_on_empty_balance_schedule']           = "Планировать задачи на будущее, чтобы они отработали позже";
$lang['plugins']['neuroscore']['field_on_empty_balance_ignore']             = "Игнорировать все задачи, которые происходят во время недостатка средств";
$lang['plugins']['neuroscore']['field_on_empty_balance_hint']               = "выберите, хотите ли вы чтобы KVS запускал пропущенные задачи после того как вы пополните баланс";
$lang['plugins']['neuroscore']['field_enable_debug']                        = "Включить отладку";
$lang['plugins']['neuroscore']['field_enable_debug_enabled']                = "включена";
$lang['plugins']['neuroscore']['field_enable_debug_log']                    = "лог отладки";
$lang['plugins']['neuroscore']['field_enable_debug_hint']                   = "включает логгирование всех запросов API";
$lang['plugins']['neuroscore']['field_score_enable']                        = "Включить скоринг скриншотов";
$lang['plugins']['neuroscore']['field_score_enable_enabled']                = "включить";
$lang['plugins']['neuroscore']['field_score_enable_hint']                   = "посмотреть информацию по задаче скоринга каждого видео можно в его логе (Видео -> Открыть лог видео)";
$lang['plugins']['neuroscore']['field_score_screenshot_type']               = "Применять к скриншотам";
$lang['plugins']['neuroscore']['field_score_screenshot_type_all']           = "К видео и с автоматически создаваемыми, и с загруженными вручную скриншотами";
$lang['plugins']['neuroscore']['field_score_screenshot_type_auto']          = "Пропускать видео с загруженными вручную скриншотами";
$lang['plugins']['neuroscore']['field_score_screenshot_type_hint']          = "устанавливает, необходимо ли делать скоринг скриншотов для видео, скриншоты которых загружены вручную";
$lang['plugins']['neuroscore']['field_score_screenshot_max_count']          = "Максимум скриншотов для скоринга";
$lang['plugins']['neuroscore']['field_score_screenshot_max_count_hint']     = "укажите лимит отправки скриншотов в скоринг для одного видео; например, если у видео будет больше скриншотов, KVS отправит только первые N скриншотов [kt|br] оставьте пустым, если хотите чтобы в скоринг отправлялись абсолютно все скриншоты независимо от кол-ва";
$lang['plugins']['neuroscore']['field_score_screenshot_retain']             = "После скоринга";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_all']         = "Только изменять главный скриншот, не удалять другие";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_all_hint']    = "в админ панели можно будет посмотреть данные по скорингу, если вы хотите в каждом случае принимать решение вручную";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_count']       = "Оставлять N лучших скриншотов, изменять главный и удалять остальные";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_count_hint']  = "укажите какое число лучших скриншотов вы хотите оставлять; все остальные будут автоматически удалены";
$lang['plugins']['neuroscore']['field_title_enable']                        = "Включить переписывание названий";
$lang['plugins']['neuroscore']['field_title_enable_enabled']                = "включить";
$lang['plugins']['neuroscore']['field_title_rewrite_directories']           = "Синхронизировать URL-ы с названиями";
$lang['plugins']['neuroscore']['field_title_rewrite_directories_enabled']   = "включить";
$lang['plugins']['neuroscore']['field_title_rewrite_directories_hint']      = "если включено, директории и URL-ы видео будут изменяться вместе с названиями [kt|br] [kt|b]ВНИМАНИЕ![/kt|b] Не используйте эту опцию, если у вас в URL-ах видео нет цифровых ID, иначе старые URL-ы станут 404 ошибками.";
$lang['plugins']['neuroscore']['field_categories_enable']                   = "Включить автоподбор категорий (тэгов)";
$lang['plugins']['neuroscore']['field_categories_enable_enabled']           = "включить";
$lang['plugins']['neuroscore']['field_categories_type']                     = "Добавлять как";
$lang['plugins']['neuroscore']['field_categories_type_categories']          = "Категории";
$lang['plugins']['neuroscore']['field_categories_type_tags']                = "Тэги";
$lang['plugins']['neuroscore']['field_models_enable']                       = "Включить автоподбор моделей";
$lang['plugins']['neuroscore']['field_models_enable_enabled']               = "включить";
$lang['plugins']['neuroscore']['field_apply_to']                            = "Использовать для";
$lang['plugins']['neuroscore']['field_apply_to_list']['admins']             = "Новых видео, добавленных вручную админами";
$lang['plugins']['neuroscore']['field_apply_to_list']['import']             = "Новых видео, импортированных админами";
$lang['plugins']['neuroscore']['field_apply_to_list']['feeds']              = "Новых видео, добавленных фидами импорта";
$lang['plugins']['neuroscore']['field_apply_to_list']['grabbers']           = "Новых видео, импортированных грабберами";
$lang['plugins']['neuroscore']['field_apply_to_list']['ftp']                = "Новых видео, добавленных через плагин FTP загрузки";
$lang['plugins']['neuroscore']['field_apply_to_list']['site']               = "Новых видео, добавленных пользователями сайта";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual']             = "Ручного режима выполнения";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_hint']        = "ручной режим выполнения доступен через интерфейс массового редактирования и может использоваться для тестирования";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_repeat']      = "Разрешить повторную обработку";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_repeat_hint'] = "по умолчанию KVS будет пропускать видео, обработанные ранее; включите эту опцию, если вы хотите форсировать повторную обработку видео";
$lang['plugins']['neuroscore']['field_apply_to_feeds_all']                  = "Всеми фидами";
$lang['plugins']['neuroscore']['field_apply_to_feeds_selected']             = "Только указанными фидами";
$lang['plugins']['neuroscore']['field_apply_to_feeds_feed']                 = "Фид [kt|b]%1%[/kt|b]";
$lang['plugins']['neuroscore']['field_apply_to_empty_categories']           = "Только для видео без категорий (тэгов)";
$lang['plugins']['neuroscore']['field_apply_to_empty_models']               = "Только для видео без моделей";
$lang['plugins']['neuroscore']['field_stats']                               = "Статистика";
$lang['plugins']['neuroscore']['field_stats_none']                          = "N/A";
$lang['plugins']['neuroscore']['field_stats_postponed']                     = "%1% видео отложено";
$lang['plugins']['neuroscore']['field_stats_processing']                    = "%1% видео в процессе";
$lang['plugins']['neuroscore']['field_stats_finished']                      = "%1% видео обработано";
$lang['plugins']['neuroscore']['field_stats_deleted']                       = "%1% видео обработано и удалено";
$lang['plugins']['neuroscore']['field_tasks']                               = "Выполненные задачи";
$lang['plugins']['neuroscore']['field_status_missing']                      = "Не применялся";
$lang['plugins']['neuroscore']['field_status_postponed']                    = "Отложен";
$lang['plugins']['neuroscore']['field_status_processing']                   = "В процессе";
$lang['plugins']['neuroscore']['field_status_finished']                     = "Завершен";
$lang['plugins']['neuroscore']['btn_save']                                  = "Сохранить";
$lang['plugins']['neuroscore']['error_invalid_api_response_code']           = "Неверный ключ API, или проблемы с доступом к API Neuroscore";
$lang['plugins']['neuroscore']['error_invalid_api_response_format']         = "Неверный формат ответа API";