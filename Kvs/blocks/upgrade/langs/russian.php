<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// upgrade messages
// =====================================================================================================================

$lang['upgrade']['groups']['paid_access']   = "Платный доступ";

$lang['upgrade']['params']['enable_card_payment']   = "Включает возможность web оплаты. Вы должны активировать хотя бы один web биллинг в панели администрирования в разделе [kt|b]Мемберзона[/kt|b].";
$lang['upgrade']['params']['enable_access_codes']   = "Разрешает использовать коды доступа при апгрейде доступа. Вы должны сначала создать коды доступа через плагин [kt|b]Генератор аккаунтов для пользователей[/kt|b] и предоставить их пользователям через сторонний канал.";

$lang['upgrade']['block_short_desc'] = "Предоставляет функционал для апгрейда уровня доступа или покупки токенов";

$lang['upgrade']['block_desc'] = "
	Блок предназначен для апгрейда уровня доступа или покупки токенов для уже зарегистрированных пользователей.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]payment_option_required[/kt|b]: когда пользователь не выбрал пакет доступа и не указал код доступа [поле = payment_option][kt|br]
	- [kt|b]access_code_invalid[/kt|b]: когда пользователь пытается использовать неверный код доступа [поле = access_code][kt|br]
	- [kt|b]card_package_id_invalid[/kt|b]: когда пользователь пытается выбрать несуществующий или неактивный пакет доступа [поле = card_package_id][kt|br]
	- [kt|b]card_package_id_not_enough_tokens[/kt|b]: когда пользователь пытается купить пакет доступа через биллинг [kt|b]Internal Tokens[/kt|b], но у него недостаточно токенов [поле = card_package_id][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Платный доступ[/kt|b]
	[kt|br][kt|br]

	KVS поддерживает платный доступ через web процессоры приема оплаты (биллинги по карточкам, Paypal и т.д.).
	Дополнительно вы можете генерировать коды доступа через плагин [kt|b]Генератор аккаунтов для пользователей[/kt|b] и
	распространять их по сторонним каналам, например через промо акции или магазины цифровых товаров. Вместо кодов
	доступа вы также можете генерировать непосредственно профили с нужными настройками доступа, но при этом
	пользователи не смогут выбрать имя пользователя и пароль, и будут вынуждены использовать полученные от вас.
	[kt|br][kt|br]

	Параметры в группе настроек платного доступа предназначены для включения различных платежных опций и управления
	ими. При включении веб оплаты у вас должен быть активирован хотя бы один из биллингов в разделе
	[kt|b]Мемберзона[/kt|b] панели администрирования KVS. Если вы планируете использовать коды доступа, то их сначала
	необходимо сгенерировать через плагин [kt|b]Генератор аккаунтов для пользователей[/kt|b], а затем распространить
	через сторонние каналы и, получив их, пользователи смогут воспользоваться ими при апгрейде профиля или для покупки
	токенов.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['upgrade']['block_examples'] = "
	[kt|b]Показать форму апгрейда уровня доступа[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- enable_card_payment[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
