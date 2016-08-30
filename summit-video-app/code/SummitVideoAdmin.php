<?php

/**
 * Class SummitVideoAdmin
 */
class SummitVideoAdmin extends ModelAdmin
{

    /**
     * @var array
     */
    private static $managed_models = [
        'PresentationVideo'
    ];

    /**
     * @var string
     */
    private static $url_segment = 'summit-videos';

    /**
     * @var string
     */
    private static $menu_title = 'Summit Videos';

    /**
     * @return mixed
     */
    public function getList()
    {
        $list = parent::getList();

        return $list->sort([
            'Featured DESC',
            'Highlighted DESC',
            'DateUploaded DESC',
            'LastEdited DESC'
        ]);
    }
}