<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['recaptcha']['title']       = "Captcha";
$lang['plugins']['recaptcha']['description'] = "Integrates 3rd-party captcha services into KVS.";
$lang['plugins']['recaptcha']['long_desc']   = "
		[kt|b]Google reCAPTCHA:[/kt|b]
		[kt|br][kt|br]
		Go to Google reCAPTCHA configurator and enable your reCAPTCHA v2 for your domain. Then Google will
		generate [kt|b]Site key[/kt|b] and [kt|b]Secret key[/kt|b] pair, that are needed for this plugin.
		[kt|br][kt|br]
		[kt|b]Cloudflare Turnstile:[/kt|b]
		[kt|br][kt|br]
		Go to Cloudflare Turnstile configurator and enable it for your domain. Then Cloudflare will
		generate [kt|b]Site key[/kt|b] and [kt|b]Secret key[/kt|b] pair, that are needed for this plugin.
";
$lang['permissions']['plugins|recaptcha']    = $lang['plugins']['recaptcha']['title'];

$lang['plugins']['recaptcha']['field_enable']                       = "Enable captcha";
$lang['plugins']['recaptcha']['field_enable_kvs']                   = "KVS default captcha";
$lang['plugins']['recaptcha']['field_enable_recaptcha']             = "Google reCAPTCHA";
$lang['plugins']['recaptcha']['field_enable_turnstile']             = "Cloudflare Turnstile";
$lang['plugins']['recaptcha']['field_enable_hint']                  = "all KVS captcha will be replaced with 3rd-party captcha";
$lang['plugins']['recaptcha']['field_site_key']                     = "Site key";
$lang['plugins']['recaptcha']['field_site_key_hint']                = "copy-paste site key value from 3rd-party service configurator";
$lang['plugins']['recaptcha']['field_secret_key']                   = "Secret key";
$lang['plugins']['recaptcha']['field_secret_key_hint']              = "copy-paste secret key value from 3rd-party service configurator";
$lang['plugins']['recaptcha']['field_alias_domain']                 = "Alias domain";
$lang['plugins']['recaptcha']['field_alias_number']                 = "#";
$lang['plugins']['recaptcha']['field_alias_delete']                 = "Delete";
$lang['plugins']['recaptcha']['field_aliases']                      = "Aliases";
$lang['plugins']['recaptcha']['field_aliases_enabled']              = "configure";
$lang['plugins']['recaptcha']['field_aliases_hint']                 = "use this option to specify separate keys for different domains if your project has aliases";
$lang['plugins']['recaptcha']['btn_save']                           = "Save";
$lang['plugins']['recaptcha']['error_template_not_ready_recaptcha'] = "Your theme version does not support Google reCAPTCHA integration.";
$lang['plugins']['recaptcha']['error_template_not_ready_turnstile'] = "Your theme version does not support Cloudflare Turnstile integration. Please contact KVS support.";
