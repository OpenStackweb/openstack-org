<?php


class PresentationAdmin extends ModelAdmin
{

    private static $url_segment = "presentations";


    private static $menu_title = "Presentations";


    private static $managed_models = array (
        'Presentation',
        'SchedPresentation',
        'PresentationTopic',
        'PresentationSpeaker',
    );
    
    public function getList() {
        $list = parent::getList();
        if($this->modelClass == 'Presentation' || $this->modelClass == 'PresentationSpeaker') {
            $list = $list->sort('Created','DESC');
        }
        return $list;
    }


}