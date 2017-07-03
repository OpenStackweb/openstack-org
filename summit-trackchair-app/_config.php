<?php
PublisherSubscriberManager::getInstance()->subscribe(ISummitEntityEvent::UpdatedEntity, function($entity){

    $summit_id = isset($_REQUEST['SummitID']) ? intval($_REQUEST['SummitID']) : $entity->getField("SummitID");
    if(is_null($summit_id) || $summit_id == 0) $summit_id = Summit::ActiveSummitID();

    if($entity instanceof Presentation)
    {
        // send push notification
        $notification = new PresentationPushNotification();
        $notification->PresentationID = $entity->ID;
        $notification->Message = 'Presentation Updated';
        $notification->Channel = 'TRACKCHAIRS';
        $notification->approve();
        $notification->write();
    }

});
