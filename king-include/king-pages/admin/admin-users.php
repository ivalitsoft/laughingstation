<?php
/*

	File: king-include/king-page-admin-approve.php
	Description: Controller for admin page showing new users waiting for approval


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: LICENCE.html
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'king-app/admin.php';
	require_once QA_INCLUDE_DIR.'king-db/admin.php';


//	Check we're not using single-sign on integration

	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User accounts are handled by external code');


//	Find most flagged questions, answers, comments

	$userid=qa_get_logged_in_userid();
$start = qa_get_start();
	$users=qa_db_select_with_pending(qa_db_top_users_selectspec($start, 20));
	$userfields=qa_db_select_with_pending(qa_db_userfields_selectspec());
	$usercount = qa_opt('cache_userpointscount');
	$pagesize = 20;
$users = array_slice($users, 0, $pagesize);	
$usershtml = qa_userids_handles_html($users);
//	Check admin privileges (do late to allow one DB query)

	if (qa_get_logged_in_level()<QA_USER_LEVEL_MODERATOR) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}


//	Check to see if any were approved or blocked here

	$pageerror=qa_admin_check_clicks();


//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/approve_users_title');
	$qa_content['error']=isset($pageerror) ? $pageerror : qa_admin_page_error();

	$qa_content['message_list']=array(
		'form' => array(
			'tags' => 'method="post" action="'.qa_self_html().'"',

			'hidden' => array(
				'code' => qa_get_form_security_code('admin/click'),
			),
		),

		'messages' => array(),
	);

	$qa_content['ranking'] = array(
		'items' => array(),
		'rows' => ceil($pagesize/qa_opt('columns_users')),
		'type' => 'users'
	);
	if (count($users)) {
		foreach ($users as $user) {
			$message=array();

			$message['tags']='id="p'.qa_html($user['userid']).'"'; // use p prefix for qa_admin_click() in king-admin.js

				if (QA_FINAL_EXTERNAL_USERS)
				$avatarhtml = qa_get_external_avatar_html($user['userid'], qa_opt('avatar_users_size'), true);
			else {
				$avatarhtml = qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'],
					$user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), true);
			}		

			$htmlemail=qa_html($user['email']);
			$message['content']= $avatarhtml . '<a href="'.qa_path_html('user/'.$user['handle']).'" target="_blank">'.$user['handle'] . '</a>' . ' - ';
			$message['content'].=qa_lang_html('users/email_label').' <a href="mailto:'.$htmlemail.'">'.$htmlemail.'</a>';

			if (qa_opt('confirm_user_emails'))
				$message['content'].='<small> - '.qa_lang_html(($user['flags'] & QA_USER_FLAGS_EMAIL_CONFIRMED) ? 'users/email_confirmed' : 'users/email_not_confirmed').'</small>';

			foreach ($userfields as $userfield)
				if (strlen(@$user['profile'][$userfield['title']]))
					$message['content'].='<br/>'.qa_html($userfield['content'].': '.$user['profile'][$userfield['title']]);

			$message['meta_order']=qa_lang_html('main/meta_order');
			$message['who']['data']=qa_get_one_user_html($user['handle']);

			$message['form']=array(
				'style' => 'light',
				'title' => '<a href="'.qa_path_html('user/'.$user['handle']).'?state=edit" target="_blank">Edit</a>',
				'buttons' => array(
					'kingdelete' => array(
						'tags' => 'name="admin_'.$user['userid'].'_kingdelete" onclick="return qa_admin_click(this);"',
						'label' => 'delete',
						'popup' => qa_lang_html('admin/approve_user_popup'),
					),
				),
			);

			$qa_content['message_list']['messages'][]=$message;
		}

	} else
		$qa_content['title']=qa_lang_html('admin/no_unapproved_found');

$qa_content['page_links'] = qa_html_page_links(qa_request(), $start, $pagesize, $usercount, qa_opt('pages_prev_next'));
	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	$qa_content['script_rel'][]='king-content/king-admin.js?'.QA_VERSION;


	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/
