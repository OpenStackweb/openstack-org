<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 10/2/13
 * Time: 1:03 PM
 * To change this template use File | Settings | File Templates.
 */

class OrganizationRegistrationRequest extends DataObject {
    static $db = array(
    );

    static $has_one = array(
        'Member' => 'Member',
        'Organization'=>'Org',
    );
}