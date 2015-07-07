<?php
/**
 * Class SummitBackgroundColor
 */

class SummitColor extends DataObject
{
    private static $db = array (
        'Color' => 'Color',
    );

    private static $summary_fields = array (
        'Color' => 'Color',
    );

    private static $has_one = array (
        'Summit' => 'Summit'
    );
}