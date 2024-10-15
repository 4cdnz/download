<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
header('Content-Type: application/javascript; charset=utf-8');

require_once '../include/setup.php';
require_once '../include/functions_base.php';

start_session();
require_once "$config[project_path]/admin/langs/english.php";
if (isset($_SESSION['userdata']) && $_SESSION['userdata']['lang']<>'' && $_SESSION['userdata']['lang']<>'english' && is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php";
	if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php"))
	{
		require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php";
	}
}

?>
var lang = new Object();

// dialog messages
lang['dialog_ok'] = '<?php echo $lang['javascript']['dialog_ok'];?>';
lang['dialog_yes'] = '<?php echo $lang['javascript']['dialog_yes'];?>';
lang['dialog_cancel'] = '<?php echo $lang['javascript']['dialog_cancel'];?>';
lang['dialog_error'] = '<?php echo $lang['javascript']['dialog_error'];?>';
lang['dialog_confirmation'] = '<?php echo $lang['javascript']['dialog_confirmation'];?>';
lang['dialog_warning'] = '<?php echo $lang['javascript']['dialog_warning'];?>';
lang['dialog_download'] = '<?php echo $lang['javascript']['dialog_download'];?>';
lang['dialog_open_in_new_window'] = '<?php echo $lang['javascript']['dialog_open_in_new_window'];?>';
lang['dialog_do_not_ask'] = '<?php echo $lang['javascript']['dialog_do_not_ask'];?>';

// upload messages
lang['ktfuf_field_upload_type_file'] = '<?php echo $lang['javascript']['ktfuf_field_upload_type_file'];?>';
lang['ktfuf_field_upload_type_url'] = '<?php echo $lang['javascript']['ktfuf_field_upload_type_url'];?>';
lang['ktfuf_field_file'] = '<?php echo $lang['javascript']['ktfuf_field_file'];?>';
lang['ktfuf_field_url'] = '<?php echo $lang['javascript']['ktfuf_field_url'];?>';
lang['ktfuf_btn_upload'] = '<?php echo $lang['javascript']['ktfuf_btn_upload'];?>';
lang['ktfuf_btn_browse'] = '<?php echo $lang['javascript']['ktfuf_btn_browse'];?>';
lang['ktfuf_ext_error'] = '<?php echo str_replace('%1%', '${formats}', $lang['javascript']['ktfuf_ext_error']);?>';
lang['ktfuf_filesize_error'] = '<?php echo str_replace('%1%', '${size}', $lang['javascript']['ktfuf_filesize_error']);?>';
lang['ktfuf_n_files'] = '<?php echo str_replace('%1%', '${number}', $lang['javascript']['ktfuf_n_files']);?>';
lang['ktfudc_preparing'] = '<?php echo $lang['javascript']['ktfudc_preparing'];?>';
lang['ktfudc_uploading'] = '<?php echo str_replace(array('%1%', '%2%', '%3%', '%4%'), array('${timeLeft}', '${loaded}', '${total}', '${speed}'), $lang['javascript']['ktfudc_uploading']);?>';
lang['ktfudc_finished'] = '<?php echo $lang['javascript']['ktfudc_finished'];?>';
lang['ktfudc_filesize_error'] = '<?php echo $lang['javascript']['ktfudc_filesize_error'];?>';
lang['ktfudc_unexpected_error'] = '<?php echo $lang['javascript']['ktfudc_unexpected_error'];?>';
lang['ktfudc_notallowed_error'] = '<?php echo $lang['javascript']['ktfudc_notallowed_error'];?>';
lang['ktfudc_url_error'] = '<?php echo $lang['javascript']['ktfudc_url_error'];?>';

// ajax messages
lang['kta_bad_request_error'] = '<?php echo $lang['javascript']['kta_bad_request_error']?>';
lang['kta_session_error'] = '<?php echo $lang['javascript']['kta_session_error']?>';
lang['kta_server_error'] = '<?php echo str_replace('%1%', '${error}', $lang['javascript']['kta_server_error']);?>';
lang['kta_unexpected_error'] = '<?php echo $lang['javascript']['kta_unexpected_error'];?>';
lang['kta_unexpected_response_error'] = '<?php echo $lang['javascript']['kta_unexpected_response_error'];?>';

// size messages
lang['bytes'] = '<?php echo $lang['javascript']['bytes'];?>';
lang['kilo_bytes'] = '<?php echo $lang['javascript']['kilo_bytes'];?>';
lang['mega_bytes'] = '<?php echo $lang['javascript']['mega_bytes'];?>';
lang['giga_bytes'] = '<?php echo $lang['javascript']['giga_bytes'];?>';

// time messages
lang['seconds'] = '<?php echo $lang['javascript']['seconds'];?>';
lang['minutes'] = '<?php echo $lang['javascript']['minutes'];?>';
lang['hours'] = '<?php echo $lang['javascript']['hours'];?>';
lang['today'] = '<?php echo $lang['javascript']['today'];?>';
lang['now'] = '<?php echo $lang['javascript']['now'];?>';
lang['clear'] = '<?php echo $lang['javascript']['clear'];?>';

// image list layer messages
lang['image_list_text'] = '<?php echo str_replace(array('%1%', '%2%'), array('${item}', '${total}'), $lang['javascript']['image_list_text']);?>';
lang['image_list_label_loading'] = '<?php echo $lang['javascript']['image_list_label_loading'];?>';
lang['image_list_label_error'] = '<?php echo $lang['javascript']['image_list_label_error'];?>';
lang['image_list_btn_prev'] = '<?php echo $lang['javascript']['image_list_btn_prev'];?>';
lang['image_list_btn_next'] = '<?php echo $lang['javascript']['image_list_btn_next'];?>';
lang['image_list_main'] = '<?php echo $lang['javascript']['image_list_main'];?>';
lang['image_list_delete'] = '<?php echo $lang['javascript']['image_list_delete'];?>';

// grid messages
lang['list_view_delete_confirm'] = '<?php echo str_replace('%1%', '${title}', $lang['common']['dg_list_view_delete_confirm']);?>';
lang['list_view_change_confirm'] = '<?php echo str_replace('%1%', '${title}', $lang['common']['dg_list_view_change_confirm']);?>';
lang['grid_menu_item_edit'] = '<?php echo $lang['javascript']['grid_menu_item_edit'];?>';
lang['grid_menu_item_add_to_filter'] = '<?php echo $lang['javascript']['grid_menu_item_add_to_filter'];?>';
lang['grid_menu_item_replace_filter'] = '<?php echo $lang['javascript']['grid_menu_item_replace_filter'];?>';
lang['grid_btn_filter_submit'] = '<?php echo $lang['javascript']['grid_btn_filter_submit'];?>';

// other messages
lang['symbols'] = '<?php echo str_replace('%1%', '${count}', $lang['javascript']['symbols']);?>';
lang['post_wait_popup_text'] = '<?php echo $lang['javascript']['post_wait_popup_text'];?>';
lang['post_progress_popup_text'] = '<?php echo $lang['javascript']['post_progress_popup_text'];?>';
lang['no_preview'] = '<?php echo $lang['javascript']['no_preview'];?>';
lang['no_items_found'] = '<?php echo $lang['javascript']['no_items_found'];?>';
lang['new_item'] = '<?php echo str_replace('%1%', '${object}', $lang['javascript']['new_item']);?>';
lang['new_item_value'] = '<?php echo str_replace('%1%', '${object}', $lang['javascript']['new_item_value']);?>';
lang['insight_popup'] = '<?php echo $lang['javascript']['insight_popup'];?>';
lang['insight_hint'] = '<?php echo $lang['javascript']['insight_hint'];?>';
lang['insight_filter_active'] = '<?php echo $lang['javascript']['insight_filter_active'];?>';
lang['insight_filter_reset'] = '<?php echo $lang['javascript']['insight_filter_reset'];?>';
lang['insight_settings_synonyms_search'] = '<?php echo $lang['javascript']['insight_settings_synonyms_search'];?>';
lang['insight_settings_synonyms_display'] = '<?php echo $lang['javascript']['insight_settings_synonyms_display'];?>';
lang['preset_delete_confirm'] = '<?php echo $lang['javascript']['preset_delete_confirm'];?>';

var config = new Object();
config['locale'] = '<?php echo $lang['system']['language_code'];?>';

<?php if (strpos($config['project_url'], 'https://') !== false) : ?>
config['is_https'] = 'true';
<?php endif; ?>

<?php if(isset($_SESSION['userdata']) && $_SESSION['userdata']['login']!='') : ?>

config['urls_same_window'] = '<?php echo (intval($_SESSION['save']['options']['urls_same_window']) == 1 ? 'true' : 'false');?>';
config['editor_mode'] = '<?php echo $_SESSION['save']['options']['editor_mode'];?>';
config['popup_close_by_click'] = '<?php echo $_SESSION['save']['options']['popup_close_by_click'];?>';

config['file_upload_chunk_size'] = 9 * 1024 * 1024;

config['file_upload_form_url'] = 'include/uploader.php';
config['file_upload_status_url'] = 'include/get_upload_status.php';

config['short_date_format'] = '<?php echo $_SESSION['userdata']['short_date_format'];?>';
config['full_date_format'] = '<?php echo $_SESSION['userdata']['full_date_format'];?>';

<?php endif; ?>
