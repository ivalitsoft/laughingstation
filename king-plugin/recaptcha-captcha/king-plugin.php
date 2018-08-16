<?php

/*
	Plugin Name: reCAPTCHA
	Plugin URI: 
	Plugin Description: Provides support for reCAPTCHA captchas
	Plugin Version: 1.0
	Plugin Date: 2011-11-17
	Plugin Author: KingMedia
	Plugin Author URI: 
	Plugin License: GPLv2
	Plugin Minimum KingMedia Version:  1.5
	Plugin Update Check URI:
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('captcha', 'king-recaptcha-captcha.php', 'qa_recaptcha_captcha', 'reCAPTCHA');
	

/*
	Omit PHP closing tag to help avoid accidental output
*/