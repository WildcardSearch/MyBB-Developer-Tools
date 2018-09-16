<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * ACP language file
 */

$l['developer_tools'] = 'Developer Tools';
$l['developer_tools_description'] = 'tools to aid in development, theme design, and testing';

/* settings */

$l['developer_tools_plugin_settings'] = 'Plugin Settings';
$l['developer_tools_settingsgroup_description'] = 'placeholder';

$l['developer_tools_minify_js_title'] = 'Minify JavaScript?';
$l['developer_tools_minify_js_desc'] = 'YES (default) to serve client-side scripts minified to increase performance, NO to serve beautiful, commented code ;)';

// acp
$l['developer_tools_admin_permissions_desc'] = 'Can use Developer Tools?';
$l['developer_tools_page_permissions_desc'] = 'Can use "{1}" Module?';

// plugin requirements
$l['developer_tools_folders_requirement_warning'] = 'One or more folders are not writable. These folders need to be writable during installation and upgrades for themeable items to be upgraded on a per-theme basis.<br /><strong>Folder(s):</strong><br />';
$l['developer_tools_subfolders_unwritable'] = 'One or more subfolders in <span style="font-family: Courier New; font-weight: bolder; font-size: small; color: black;">{1}</span>';
$l['developer_tools_cannot_be_installed'] = 'Developer Tools cannot be installed!';

// PHiddle
$l['developer_tools_admin_home'] = 'PHiddle';

$l['developer_tools_phiddle_tab_php'] = 'PHP';
$l['developer_tools_phiddle_tab_output'] = 'Output';

// modules
$l['developer_tools_module_execute'] = 'Execute';
$l['developer_tools_module_execute_form_title'] = 'Settings for "{1}" Module';

// create_users
$l['developer_tools_create_users_title'] = 'Create Users';
$l['developer_tools_create_users_description'] = 'create users with random details';

$l['developer_tools_create_users_amount_title'] = 'User Count';
$l['developer_tools_create_users_amount_desc'] = 'enter the number of users to create';

$l['developer_tools_create_users_usergroup_title'] = 'User Group';
$l['developer_tools_create_users_usergroup_desc'] = 'choose which user group to add the new users to';

$l['developer_tools_create_users_password_title'] = 'Password';
$l['developer_tools_create_users_password_desc'] = 'enter a common password for all created users or leave blank (default) to generate a random pass';

$l['developer_tools_create_users_email_title'] = 'Email';
$l['developer_tools_create_users_email_desc'] = 'enter a common email address for all created users or leave blank (default) to use <span style="font-family: Courier New; font-size: 1.2em; font-weight: bolder; color: black;">admin@localhost.com</span>';

$l['developer_tools_create_users_name_count_title'] = 'Name Count';
$l['developer_tools_create_users_name_count_desc'] = 'choose how many space-separated names to assign to each user';

$l['developer_tools_create_users_caps_title'] = 'Capitalize?';
$l['developer_tools_create_users_caps_desc'] = 'YES (default) to capitalize the first letters of names, NO to leave names all lowercase';

$l['developer_tools_create_users_local_names_title'] = 'Local Names?';
$l['developer_tools_create_users_local_names_desc'] = 'YES to use local names, NO (default) to use names from an international list';

$l['developer_tools_create_users_referrer_title'] = 'Referrer';
$l['developer_tools_create_users_referrer_desc'] = 'blank (default) to forego assigning a referrer to created account(s), string user name to assign a user as the referrer';

$l['developer_tools_create_users_success_message'] = '{1} user(s) successfully created.';

// create_threads
$l['developer_tools_create_threads_title'] = 'Create Threads';
$l['developer_tools_create_threads_description'] = 'create threads with posts from random users';

$l['developer_tools_create_threads_threadcount_title'] = 'Thread Count';
$l['developer_tools_create_threads_threadcount_desc'] = 'enter the number of threads to create';

$l['developer_tools_create_threads_postcount_title'] = 'Posts Per Thread';
$l['developer_tools_create_threads_postcount_desc'] = 'enter the number of posts to create in each thread';

$l['developer_tools_create_threads_fid_title'] = 'Forum';
$l['developer_tools_create_threads_fid_desc'] = 'select the forum in which to create the new thread (<strong>selecting a category will cause the module to fail</strong>)';

$l['developer_tools_create_threads_success_message'] = 'Created {1} thread(s) and {2} post(s)';

$l['developer_tools_create_threads_error_message_no_forum'] = 'Forum doesn\'t exist!';
$l['developer_tools_create_threads_error_message_category'] = 'Categories are not a valid option';

// create_username_avatars
$l['developer_tools_create_username_avatars_title'] = 'Create User Name Avatars';
$l['developer_tools_create_username_avatars_description'] = 'create avatars for each user that have the user name in the image';
$l['developer_tools_create_username_avatars_long_description_1'] = 'This module creates avatars for every user on the forum (except id=1) that have the user\'s name written across the image. This can prove useful when developing plugins that display user avatars in lieu of user names.';
$l['developer_tools_create_username_avatars_long_description_2'] = 'The created images are 100px by 100px and are PNG images.';
$l['developer_tools_create_username_avatars_long_description_img_alt'] = 'example image';
$l['developer_tools_create_username_avatars_long_description_img_title'] = 'example image; shown half size';

$l['developer_tools_create_username_avatars_threadcount_title'] = 'Thread Count';
$l['developer_tools_create_username_avatars_threadcount_desc'] = 'enter the number of threads to create';

$l['developer_tools_create_username_avatars_success_message'] = 'Updated {1} avatar(s)';

$l['developer_tools_create_username_avatars_error_message_folder'] = 'Avatar folder doesn\'t exist and could not be created.';

$l['developer_tools_create_username_avatars_error_message_no_users'] = 'The avatar assignment failed because there were no users. Note: The original account (id=1) will not be used.';

?>
