<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['recaptcha']['title']          = "Captcha";
$lang['plugins']['recaptcha']['description']    = "Интегрирует сторонние сервисы Captcha в KVS.";
$lang['plugins']['recaptcha']['long_desc']      = "
		[kt|b]Для Google reCAPTCHA:[/kt|b]
		[kt|br][kt|br]
		Зайдите в панель управления Google reCAPTCHA и добавьте ваш сайт с reCAPTCHA v2. После чего Google
		создаст пару ключей [kt|b]Ключ сайта[/kt|b] и [kt|b]Секретный ключ[/kt|b], которые требуются для данного
		плагина.
		[kt|br][kt|br]
		[kt|b]Для Cloudflare Turnstile:[/kt|b]
		[kt|br][kt|br]
		Зайдите в панель управления Cloudflare Turnstile и добавьте ваш сайт. После чего Turnstile
		создаст пару ключей [kt|b]Ключ сайта[/kt|b] и [kt|b]Секретный ключ[/kt|b], которые требуются для данного
		плагина.
";
$lang['permissions']['plugins|recaptcha']    = $lang['plugins']['recaptcha']['title'];

$lang['plugins']['recaptcha']['field_enable']                       = "Включить каптчу";
$lang['plugins']['recaptcha']['field_enable_kvs']                   = "Captcha KVS по умолчанию";
$lang['plugins']['recaptcha']['field_enable_recaptcha']             = "Google reCAPTCHA";
$lang['plugins']['recaptcha']['field_enable_turnstile']             = "Cloudflare Turnstile";
$lang['plugins']['recaptcha']['field_enable_hint']                  = "если выбрана опция стороннего сервиса, то все каптчи KVS будут заменены на сторонние";
$lang['plugins']['recaptcha']['field_site_key']                     = "Ключ сайта";
$lang['plugins']['recaptcha']['field_site_key_hint']                = "скопируйте значение ключа сайта из конфигуратора стороннего сервиса";
$lang['plugins']['recaptcha']['field_secret_key']                   = "Секретный ключ";
$lang['plugins']['recaptcha']['field_secret_key_hint']              = "скопируйте значение секретного ключа из конфигуратора стороннего сервиса";
$lang['plugins']['recaptcha']['field_alias_domain']                 = "Домен зеркала";
$lang['plugins']['recaptcha']['field_alias_number']                 = "#";
$lang['plugins']['recaptcha']['field_alias_delete']                 = "Удалить";
$lang['plugins']['recaptcha']['field_aliases']                      = "Зеркала";
$lang['plugins']['recaptcha']['field_aliases_enabled']              = "настроить";
$lang['plugins']['recaptcha']['field_aliases_hint']                 = "используйте эту опцию, если вам необходимо указать разные ключи для разных доменов, когда к вашему проекту подключены зеркала";
$lang['plugins']['recaptcha']['btn_save']                           = "Сохранить";
$lang['plugins']['recaptcha']['error_template_not_ready_recaptcha'] = "Ваша версия темы не поддерживает интеграцию с Google reCAPTCHA. Пожалуйста, обратитесь в службу поддержки.";
$lang['plugins']['recaptcha']['error_template_not_ready_turnstile'] = "Ваша версия темы не поддерживает интеграцию с Cloudflare Turnstile. Пожалуйста, обратитесь в службу поддержки.";
