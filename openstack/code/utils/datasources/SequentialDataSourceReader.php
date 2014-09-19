<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 10/22/13
 * Time: 1:15 PM
 * To change this template use File | Settings | File Templates.
 */

interface SequentialDataSourceReader{
    public function getFieldsInfo();
    public function getNextRow();
    public function Open($path);
    public function Close();
}