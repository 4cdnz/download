<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// logon messages
// =====================================================================================================================

$lang['logon']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['logon']['groups']['limitation']      = "Ограничение входа";
$lang['logon']['groups']['multilogin']      = "Защита от использования доступа разными людьми";

$lang['logon']['params']['redirect_to']                     = "Указывает URL, на который перенаправлять пользователя после успешного входа. Вы можете использовать токен [kt|b]%USER_ID%[/kt|b] для редиректа на URL, в котором требуется прописать ID пользователя. По умолчанию делается редирект на предыдущую страницу или на индекс сайта.";
$lang['logon']['params']['notify_to']                       = "Указывает URL, который должен использоваться для логгирования каждого успешного входа. Вы можете использовать такие токены: [kt|b]%USER_ID%[/kt|b], [kt|b]%USERNAME%[/kt|b], [kt|b]%EMAIL%[/kt|b], [kt|b]%IP%[/kt|b] и [kt|b]%AGENT%[/kt|b].";
$lang['logon']['params']['use_captcha']                     = "Включает использование визуальной защиты от авто-сабмита.";
$lang['logon']['params']['enable_brute_force_protection']   = "Включает защиту от перебора паролей.";
$lang['logon']['params']['remember_me']                     = "Включает возможность запоминать пользователей на указанное кол-во дней.";
$lang['logon']['params']['single_sign_on']                  = "Включает возможность стороннего логина без пароля (SSO) и задает секретный ключ для подписи данных логина. Более детальная информация содержится в документации блока.";
$lang['logon']['params']['allow_only_premium']              = "Позволяет входить в мемберзону только премиум пользователям.";
$lang['logon']['params']['allow_only_webmasters']           = "Позволяет входить в мемберзону только вебмастерам.";
$lang['logon']['params']['ban_by_ips']                      = "Включает защиту мемберзоны, блокируя аккаунты пользователей, которые входят с указанного кол-ва разных IP адресов в течение указанного промежутка времени (в секундах). Укажите кол-во уникальных IP адресов / период в секундах.";
$lang['logon']['params']['ban_by_ip_masks']                 = "Включает защиту мемберзоны, блокируя аккаунты пользователей, которые входят с указанного кол-ва разных масок IP адресов в течение указанного промежутка времени (в секундах). Укажите кол-во уникальных масок IP адресов / период в секундах.";
$lang['logon']['params']['ban_by_countries']                = "Включает защиту мемберзоны, блокируя аккаунты пользователей, которые входят с указанного кол-ва разных стран в течение указанного промежутка времени (в секундах). Укажите кол-во уникальных стран / период в секундах.";
$lang['logon']['params']['ban_by_browsers']                 = "Включает защиту мемберзоны, блокируя аккаунты пользователей, которые входят с указанного кол-ва разных браузеров в течение указанного промежутка времени (в секундах). Укажите кол-во уникальных браузеров / период в секундах.";
$lang['logon']['params']['ban_type']                        = "Устанавливает тип блокировки.";
$lang['logon']['params']['ban_count']                       = "Используется только для временной блокировки. Указывает максимальное число временных блокировок, после которого наступает постоянная блокировка.";

$lang['logon']['values']['ban_type']['0']   = "Постоянная";
$lang['logon']['values']['ban_type']['1']   = "Временная";

$lang['logon']['block_short_desc'] = "Предоставляет функционал для входа пользователей в личную зону";

$lang['logon']['block_desc'] = "
	Блок отображает форму логина и позволяет зарегистрированным пользователям входить в личную зону.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]username_required[/kt|b]: когда поле логина не заполнено [поле = username][kt|br]
	- [kt|b]pass_required[/kt|b]: когда поле пароля не заполнено [поле = pass][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	- [kt|b]please_wait[/kt|b]: когда сработала защита от перебора паролей и пользователь должен подождать 5 минут до следующей попытки[kt|br]
	- [kt|b]invalid_login[/kt|b]: когда данные логина не подходят[kt|br]
	- [kt|b]not_confirmed[/kt|b]: когда аккаунт пользователя не подтвержден по email[kt|br]
	- [kt|b]disabled_login[/kt|b]: когда аккаунт пользователя выключен[kt|br]
	- [kt|b]tempbanned_login[/kt|b]: когда аккаунт пользователя заблокирован временной блокировкой и пользователь может снять блокировку, кликнув по ссылке в письме[kt|br]
	- [kt|b]banned_login[/kt|b]: когда аккаунт пользователя заблокирован постоянной блокировкой[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['functionality']}[/kt|b]
	[kt|br][kt|br]

	После успешного входа пользователи будут перенаправлены на URL заданный в параметре [kt|b]redirect_to[/kt|b]. Если
	параметр не включен, то пользователи будут перенаправлены на страницу сайта, с которой они пришли, или на индекс
	сайта.
	[kt|br][kt|br]

	При закрытии браузера пользователем произойдет автоматический выход из мемберзоны, поскольку сессия пользователя
	будет уничтожена. Если вы хотите чтобы пользователь не терял свою сессию, вы можете включить параметр
	[kt|b]remember_me[/kt|b] и задать кол-во дней, на протяжении которого стоит оставлять пользователя залогиненным.
	[kt|br][kt|br]

	Вы можете использовать сторонний функционал анализа входов в мемберзону, указав URL логгирования в параметре
	[kt|b]notify_to[/kt|b]. При интеграции с NATS, это может позволить улучшить безопасность:[kt|br]
	[kt|code]http://tmmwiki.com/index.php/Nats4_Member_Logging[/kt|code]
	[kt|br][kt|br]

	[kt|b]Технология единого входа (SSO)[/kt|b]
	[kt|br][kt|br]

	Блок логина поддерживает технологию единого входа для пользователей ваших других приложений или сайтов путем 
	перехода по созданной специальным образом ссылке. Для включения этой функциональности включите параметр блока 
	[kt|b]single_sign_on[/kt|b] и установите в нем секретный ключ. Этот ключ будет использоваться для создания SSO 
	ссылок на ваших других сайтах.
	[kt|br][kt|br]

	Важной особенностью технологии единого входа является то, что вам не нужно создавать пользователей в базе KVS 
	заранее. Когда пользователи переходят по SSO ссылке, KVS проверяет ссылку на действительность и дальше осуществляет
	вход в профиль пользователя, закодированного в этой ссылке. Если такого пользователя не существует, то KVS создаст
	его прямо на лету и осуществит вход. Это особенно удобно, если вы хотите предоставить доступ к проекту KVS для 
	пользователей своего форума или блога - им достаточно перейти по ссылке и для них уже будет создан профиль. Правда,
	есть и нюансы. Например, пользователи не смогут входить по паролю - для входа на сайт KVS им будет необходимо 
	сначала войти в свой профиль на другом сайте, чтобы получить новую ссылку для входа.
	[kt|br][kt|br]

	Вы можете использовать такой мета код для создания SSO ссылок в вашем приложении:[kt|br]

	[kt|code]
	\$username = 'admin';[kt|br]
	\$email = 'admin@site.com';[kt|br]
	\$time = time();[kt|br]
	\$secret_key = 'secretkey';[kt|br]
	\$sso_token = [[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'username' => \$username,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'email' => \$email,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'token' => \$time,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'digest' => md5(\$username . \$time . \$secret_key)[kt|br]
	];[kt|br]
	echo \"https://domain.com/test.php?sso=\" . base64_encode(json_encode(\$sso_token));[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['limitation']}[/kt|b]
	[kt|br][kt|br]

	Если ваш сайт поддерживает несколько типов пользователей, для которых требуются разные формы логина, вы можете
	использовать параметры из этой секции для того, чтобы разрешить логиниться только определенному типу пользователей.
	Например, вы можете сделать отдельную страницу логина для вебмастеров, которым разрешено загружать видео на ваш
	сайт, в то время как обычным пользователям эта функция недоступна. Вы также можете вынести эту часть сайта на
	сабдомен, например [kt|b]webmasters.domain.com[/kt|b].
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['multilogin']}[/kt|b]
	[kt|br][kt|br]

	KVS поддерживает надежную защиту от использования одного аккаунта несколькими людьми. Используйте параметры
	[kt|b]ban_by_xxx[/kt|b] для включения отдельных критериев блокировки (например, заблокировать, если доступ из 3 или
	более стран в течение 3600 секунд и т.д.). Вы можете комбинировать несколько критериев параллельно; срабатывание
	любого из них вызовет блокировку. Существуют 2 типа блокировки: временная и постоянная. В случае временной
	блокировки пользователю будет отправлено письмо с возможностью разблокировать свой аккаунт. Если вы также включите
	параметр [kt|b]ban_count[/kt|b], то кол-во временных блокировок будет ограничено, после чего наступит постоянная
	блокировка. В случае постоянной блокировки разблокировать аккаунт пользователя может только администратор.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";
