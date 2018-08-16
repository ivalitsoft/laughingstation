<?php
/*

	File: king-include/king-page-admin-categories.php
	Description: Controller for admin page for editing categories


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
	require_once QA_INCLUDE_DIR.'king-db/selects.php';
	require_once QA_INCLUDE_DIR.'king-db/admin.php';

	ini_set('user_agent', 'Mozilla/5.0');

//	Check admin privileges (do late to allow one DB query)

	if (!qa_admin_check_privileges2($qa_content))
		return $qa_content;

//	Process saving options

	$savedoptions=false;
	$securityexpired=false;

		$king_key1=qa_post_text('king_key');
		$king_key = qa_opt('king_key');
		$enavato_username =  'RedKings';
		$enavato_api =  'td9yq1tbagu5r7m2xfhxuzjksjb6y1or';
		$enavato_itemid =  '7877877';
		$label = '';
		
	if (!empty($king_key1)) {
	$url= 'http://marketplace.envato.com/api/edge/'.$enavato_username.'/'.$enavato_api.'/verify-purchase:'.qa_html(isset($king_key1) ? $king_key1 : @$king_key).'.json';	
	$result_from_json = file_get_contents($url);
	$result = json_decode($result_from_json, true);
	if (! isset($result['verify-purchase']['item_id']) ) {
		if (! $result['verify-purchase']['item_id'] == $enavato_itemid ) {
			$label='DONE !';
			qa_set_option('king_key', qa_post_text('king_key'));
		} else {
			$label='Invalid Purchase code !';
		}
	} else {
		$label='Missing Purchase Code !';
	}
	}
	if (qa_clicked('dosaveoptions')) {
		if (!qa_check_form_security_code('admin/categories', qa_post_text('code')))
			$securityexpired=true;

		else {
			$savedoptions=false;
		}
	}



//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/admin_title').' - '.qa_lang_html('admin/categories_title');
	$qa_content['error']=$securityexpired ? qa_lang_html('admin/form_security_expired') : qa_admin_page_error();

		$qa_content['form']=array(
			'tags' => 'method="post" action="'.qa_path_html(qa_request()).'"',

			'ok' => $savedoptions ? qa_lang_html('admin/options_saved') : null,

			'style' => 'tall',

			'fields' => array(
				'intro' => array(
					'label' => $label,
					'type' => 'static',
				),
				'name' => array(
					'id' => 'king_key',
					'tags' => 'name="king_key" id="king_key" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"',
					'label' => 'King Media Purchase Code',
					'value' => qa_html(isset($king_key1) ? $king_key1 : @$king_key),
					'error' => qa_html(@$errors['king_key']),
				),				
			),

			'buttons' => array(
				'save' => array(
					'tags' => 'name="dosaveoptions" id="dosaveoptions"',
					'label' => qa_lang_html('main/save_button'),
				),

			),

			'hidden' => array(
				'code' => qa_get_form_security_code('admin/categories'),
			),
		);

	$qa_content['navigation']['sub']=qa_admin_sub_navigation();


	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/