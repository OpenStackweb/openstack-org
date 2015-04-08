<?php
// Only add SMTP Mailer (SendGrid) if on live site
if(Director::isLive() && !defined("SENDGRID_REST_API")) Email::set_mailer(new SmtpMailer());