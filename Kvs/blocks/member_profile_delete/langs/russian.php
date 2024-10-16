<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_delete messages
// =====================================================================================================================

$lang['member_profile_delete']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['member_profile_delete']['groups']['validation']      = $lang['website_ui']['block_group_default_validation'];
$lang['member_profile_delete']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['member_profile_delete']['params']['delete_mode']                 = "Устанавливает режим удаления профилей: должны ли они сразу удаляться, или ожидать подтверждения администрацией. [kt|b]ВАЖНО![/kt|b] Если вы выбираете удалять профили вместе с контентом, без вашего подтверждения будут удалены все видео, альбомы, записи, плэйлисты и комментарии пользователя.";
$lang['member_profile_delete']['params']['require_reason']              = "Делает поле причины обязательным для заполнения.";
$lang['member_profile_delete']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['member_profile_delete']['values']['delete_mode']['0']    = "Всегда требовать подтверждения администратора";
$lang['member_profile_delete']['values']['delete_mode']['1']    = "Требовать подтверждения администратора только для профилей с контентом";
$lang['member_profile_delete']['values']['delete_mode']['2']    = "Автоматически удалять профили вместе с контентом";

$lang['member_profile_delete']['block_short_desc'] = "Предоставляет функционал для запроса удаления профиля пользователей";

$lang['member_profile_delete']['block_desc'] = "
	Блок позволяет пользователям запросить удаление своих профилей. В зависимости от выбранных настроек, профили могут
	сразу же удаляться (в том числе и вместе со всем загруженным контентом), или отправлять запрос на удаление, который
	должен быть рассмотрен администрацией сайта.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]reason_required[/kt|b]: когда поле причины не заполнено, но настроено как обязательное [поле = reason][kt|br]
	- [kt|b]confirm_delete_required[/kt|b]: когда не активирован чекбокс подтверждения запроса [поле = confirm_delete][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['member_profile_delete']['block_examples'] = "
	[kt|b]Показать форму удаления профиля[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
