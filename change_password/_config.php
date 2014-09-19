<?php

define('CHANGE_PASSWORD_EMAIL_FROM','noreply@openstack.org');
define('CHANGE_PASSWORD_EMAIL_SUBJECT','OpenStack Profile Password Recovery');

Object::useCustomClass('Member_ForgotPasswordEmail', 'CustomMember_ForgotPasswordEmail');