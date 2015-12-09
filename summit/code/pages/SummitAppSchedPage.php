<?php

/**
 * Class SummitAppSchedPage
 */
class SummitAppSchedPage extends SummitPage {

}

class SummitAppSchedPage_Controller extends SummitPage_Controller {

    static $allowed_actions = array(
        'ViewEvent',
        'ViewSpeakerProfile',
        'ViewAttendeeProfile',
        'DoGlobalSearch',
    );

    static $url_handlers = array(
        'events/$EVENT_ID/$EVENT_TITLE' => 'ViewEvent',
        'speakers/$SPEAKER_ID'          => 'ViewSpeakerProfile',
        'attendees/$ATTENDEE_ID'        => 'ViewAttendeeProfile',
        'global-search'                 => 'DoGlobalSearch',
    );

    public function init() {
        
        $this->top_section = 'full';
        $this->event_repository = new SapphireSummitEventRepository();
        $this->speaker_repository = new SapphirePresentationSpeakerRepository();

        parent::init();
        Requirements::css('themes/openstack/bower_assets/jquery-loading/dist/jquery.loading.min.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css("summit/css/schedule-grid.css");
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
   }

    public function ViewEvent() {
        $event_id = intval($this->request->param('EVENT_ID'));
        $this->event_id = $event_id;
        $event = $this->event_repository->getById($event_id);

        if (!isset($event)) {
            return $this->httpError(404, 'Sorry that article could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-event.css");

        return $this->renderWith(array('SummitAppEventPage','SummitPage','Page'), array('Event' => $event) );
    }

    public function ViewSpeakerProfile() {
        $speaker_id = intval($this->request->param('SPEAKER_ID'));
        $speaker = $this->speaker_repository->getById($speaker_id);

        if (!isset($speaker)) {
            return $this->httpError(404, 'Sorry that speaker could not be found');
        }

        Requirements::block("summit/css/schedule-grid.css");
        Requirements::css("summit/css/summitapp-speaker.css");

        return $this->renderWith(array('SummitAppSpeakerPage','SummitPage','Page'), array('Speaker' => $speaker) );
    }

    public function ViewAttendeeProfile()
    {
        // TODO : implement view
        $attendee_id = intval($this->request->param('ATTENDEE_ID'));
        return $this->httpError(404, 'Sorry that attendee could not be found');
    }

    public function getFeedbackForm() {
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/star-rating.min.js");
        Requirements::javascript("summit/javascript/summitapp-feedbackform.js");
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");
        $form = new SummitEventFeedbackForm($this, 'SummitEventFeedbackForm');
        return $form;
    }

    public function getCurrentSummitEventsBy1stDate()
    {
        $summit = $this->Summit();
        return new ArrayList($summit->getSchedule($summit->getBeginDate()));
    }

    public function isEventOnMySchedule($event_id)
    {
       return SummitAppScheduleApi::isEventOnMySchedule($event_id, $this->Summit());
    }

    public function DoGlobalSearch(SS_HTTPRequest $request){
        $term      = Convert::raw2sql($request->requestVar('t'));
        if(empty($term)) return $this->httpError(404);

        $summit_id = $this->Summit()->ID;

        $db_term = SummitScheduleGlobalSearchTerm::get()->filter(array('Term' => $term, 'SummitID' => $summit_id))->first();
        if(is_null($db_term)) $db_term = SummitScheduleGlobalSearchTerm::create();
        $db_term->Hits     = intval($db_term->Hits) + 1;
        $db_term->Term     = $term;
        $db_term->SummitID = $summit_id;
        $db_term->write();

        $popular_terms = SummitScheduleGlobalSearchTerm::get()->filter(array('SummitID' => $summit_id))->sort('Term', 'ASC')->limit(25,0);

        // TODO : move all this sql code to repository
        $sql_events_template = <<<SQL
    SELECT DISTINCT E.* FROM SummitEvent E
    WHERE
    E.SummitID = {$summit_id} AND E.Published = 1
    AND
    (
        EXISTS
        (
            SELECT S.ID FROM PresentationSpeaker S INNER JOIN Presentation_Speakers PS ON PS.PresentationSpeakerID = S.ID
            WHERE S.SummitID = {$summit_id} AND PS.PresentationID = E.ID AND (S.FirstName LIKE '%:term%' OR  LastName LIKE '%:term%' OR  Bio LIKE '%:term%')
        )
        OR
        EXISTS
        (
            SELECT T.ID FROM Tag T INNER JOIN SummitEvent_Tags ET ON ET.TagID = T.ID
            WHERE ET.SummitEventID = E.ID AND T.Tag LIKE '%{$term}%'
        )
        OR
        EXISTS
        (
            SELECT P.ID FROM Presentation P
            INNER JOIN PresentationCategory T ON T.ID = P.CategoryID
            WHERE P.ID = E.ID AND T.Title LIKE '%:term%'
        )
        OR
        EXISTS
        (
            SELECT P.ID FROM Presentation P
            WHERE P.Level LIKE '%:term%'
        )
        OR
        Title LIKE '%:term%'
        OR
        Description LIKE '%:term%'
        OR
        ShortDescription LIKE '%:term%'
    )
SQL;

$sql_speakers_template = <<<SQL
    SELECT DISTINCT S.* FROM PresentationSpeaker S
    WHERE EXISTS
    (
        SELECT P.ID From Presentation P
        INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
        INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
        WHERE PS.PresentationSpeakerID = S.ID
    )
    AND :field LIKE '%:term%'
SQL;

        $speakers        = array();
        $speakers_fields = array('FirstName', 'LastName');
        $events          = array();

        $sql_speakers = <<<SQL
      SELECT DISTINCT S.* FROM PresentationSpeaker S INNER JOIN
      ( SELECT ID, CONCAT(FirstName,' ',LastName) AS FullName From PresentationSpeaker ) AS SN
      ON S.ID = SN.ID
      WHERE EXISTS
      (
            SELECT P.ID From Presentation P
            INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
            WHERE PS.PresentationSpeakerID = S.ID
      )
      AND SN.FullName LIKE '%{$term}%'
      UNION
      SELECT DISTINCT S.* FROM PresentationSpeaker S INNER JOIN
      ( SELECT ID, CONCAT(FirstName,' ',LastName) AS FullName From PresentationSpeaker ) AS SN
      ON S.ID = SN.ID  WHERE EXISTS
      (
            SELECT P.ID From Presentation P
            INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
             WHERE PS.PresentationSpeakerID = S.ID
      ) AND SOUNDEX(SN.FullName) = SOUNDEX('{$term}')
SQL;

        $sql_events   = '';
        $terms        = explode(' ',$term);

        foreach($terms as $t)
        {
            $t = trim($t);
            if(empty($t)) continue;
            foreach($speakers_fields as $speaker_field)
            {
                $sql_speakers = $sql_speakers . (!empty($sql_speakers) ? ' UNION ' : '') . str_replace(':field',$speaker_field, str_replace(':term', $t,
                        $sql_speakers_template));
            }
            $sql_events   = $sql_events . (!empty($sql_events) ? ' UNION ':'') .str_replace(':term', $t, $sql_events_template);
        }

        foreach(DB::query($sql_speakers) as $row)
        {
            $class = $row['ClassName'];
            array_push($speakers, new $class($row));
        }

        foreach(DB::query($sql_events," ORDER BY E.StartDate ASC, E.EndDate ASC ;") as $row)
        {
            $class = $row['ClassName'];
            array_push($events, new $class($row));
        }

        return $this->renderWith
        (
            array
            (
                'SummitAppSchedPage_globalSearchResults',
                'SummitPage',
                'Page'
            ),
            array
            (
                'SpeakerResults'   => new ArrayList($speakers),
                'EventResults'     => new ArrayList($events),
                'SearchTerm'       => $term,
                'PopularTerms'     => $popular_terms,
            )
        );
    }

    public function getPresentationLevels()
    {
        return Presentation::getLevels();
    }
}