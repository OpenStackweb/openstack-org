<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 10/22/13
 * Time: 1:06 PM
 * To change this template use File | Settings | File Templates.
 */

class Migration extends DataObject{

    static $db = array(
        'Name' => 'Text',
        'Description' => 'Text'
    );

}