<?php

class CustomEmail  extends  Email {

    public function __construct($from = null, $to = null, $subject = null, $body = null, $plaintext_body=null, $bounceHandlerURL = null, $cc = null, $bcc = null) {
        parent::__construct($from, $to, $subject , $body, $bounceHandlerURL, $cc , $bcc);
        $this->plaintext_body = $plaintext_body;
    }

    public function setPlainBody($plaintext_body){
        $this->plaintext_body = $plaintext_body;
    }
}