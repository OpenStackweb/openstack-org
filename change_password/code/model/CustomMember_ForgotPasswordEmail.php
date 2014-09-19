<?php

/**
 * Class CustomMember_ForgotPasswordEmail
 */
final class CustomMember_ForgotPasswordEmail extends Member_ForgotPasswordEmail {
	protected $from = '';  // setting a blank from address uses the site's default administrator email
	protected $subject = '';
	protected $ss_template = 'CustomForgotPasswordEmail';

	function __construct() {
		parent::__construct();
		$this->subject = _t('Member.SUBJECTPASSWORDRESET', "Your password reset link", PR_MEDIUM, 'Email subject');
	}
} 