<?php
/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class SummitEventManager
 */
final class SummitEventManager implements ISummitEventManager
{
    /**
     * @var ISummitRepository
     */
    private $summit_repository;
    /**
     * @var ISummitEventRepository
     */
    private $event_repository;
    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;
    /**
     * @var IMemberRepository
     */
    private $member_repository;
    /**
     * @var ISummitReportRepository
     */
    private $report_repository;

    /**
     * @var ISummitEventFactory
     */
    private $event_factory;
    /**
     * @var ITransactionManager
     */
    private $tx_service;

    /**
     * SummitEventManager constructor.
     * @param ISummitRepository $summit_repository
     * @param ISummitEventRepository $event_repository
     * @param ISpeakerRepository $speaker_repository
     * @param IMemberRepository $member_repository
     * @param ISummitReportRepository $report_repository
     * @param ISummitEventFactory $event_factory
     * @param ITransactionManager $tx_service
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ISpeakerRepository $speaker_repository,
        IMemberRepository $member_repository,
        ISummitReportRepository $report_repository,
        ISummitEventFactory $event_factory,
        ITransactionManager $tx_service
    )
    {
        $this->summit_repository  = $summit_repository;
        $this->event_repository   = $event_repository;
        $this->speaker_repository = $speaker_repository;
        $this->member_repository  = $member_repository;
        $this->report_repository  = $report_repository;
        $this->event_factory      = $event_factory;
        $this->tx_service         = $tx_service;
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function publishEvent(ISummit $summit, array $event_data)
    {
        $event_repository = $this->event_repository;

        return $this->tx_service->transaction(function() use($summit, $event_data, $event_repository){

            if(!isset($event_data['id'])) throw new EntityValidationException('missing required param: id');
            $event_id = intval($event_data['id']);
            $event = $event_repository->getById($event_id);

            if(is_null($event))
                throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException('event does not belong to summit');

            if(!$event->Type()->exists())
                throw new EntityValidationException('event does not have a valid event type');

            $event->setStartDate($event_data['start_datetime']);
            $event->setEndDate($event_data['end_datetime']);
            $event->LocationID = intval($event_data['location_id']);
            $this->validateBlackOutTimesAndTimes($event);
            $event->unPublish();
            $event->publish();
            return $event;
        });
    }

    /**
     * @param SummitEvent $event
     * @throws EntityValidationException
     */
    public function validateBlackOutTimesAndTimes(SummitEvent $event)
    {
        // validate blackout times and speaker conflict
        $event_on_timeframe = $this->event_repository->getPublishedByTimeFrame(intval($event->SummitID), $event->getStartDate(), $event->getEndDate());
        foreach ($event_on_timeframe as $c_event) {
            // if the published event is BlackoutTime or if there is a BlackoutTime event in this timeframe
            if (!$event->Location()->overridesBlackouts() && ($event->Type()->BlackoutTimes || $c_event->Type()->BlackoutTimes) && $event->ID != $c_event->ID) {
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "You can't publish Event (%s) %s  on this timeframe, it conflicts with (%s) %s.",
                        $event->ID,
                        $event->Title,
                        $c_event->ID,
                        $c_event->Title
                    )
                );
            }
            // if trying to publish an event on a slot occupied by another event
            if (intval($event->LocationID) == $c_event->LocationID && $event->ID != $c_event->ID) {
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "You can't publish Event (%s) %s  on this timeframe, it conflicts with (%s) %s.",
                        $event->ID,
                        $event->Title,
                        $c_event->ID,
                        $c_event->Title
                    )
                );
            }

            // validate speaker conflict
            if ($event instanceof Presentation && $c_event instanceof Presentation && $event->ID != $c_event->ID) {
                $all_speakers = $event->Speakers()->toArray();
                if ($event->ModeratorID) $all_speakers->push($event->Moderator());

                foreach ($all_speakers as $speaker) {
                    if ($c_event->Speakers()->find('ID', $speaker->ID) || $c_event->ModeratorID == $speaker->ID) {
                        throw new EntityValidationException
                        (
                            sprintf
                            (
                                "You can't publish Event %s (%s) on this timeframe, speaker %s its presention in room %s at this time.",
                                $event->Title,
                                $event->ID,
                                $speaker->getName(),
                                $c_event->getLocationName()
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * @param ISummit $summit
     * @param ISummitEvent $event
     * @return mixed
     */
    public function unpublishEvent(ISummit $summit, ISummitEvent $event)
    {
        $event_repository = $this->event_repository;
        return $this->tx_service->transaction(function() use($summit, $event, $event_repository){

            if(intval($event->SummitID) !== intval($summit->getIdentifier()))
                throw new EntityValidationException(EntityValidationException::buildMessage('event doest not belongs to summit'));
            $event->unPublish();
            return $event;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function createEvent(ISummit $summit, array $event_data)
    {
        return $this->saveOrUpdateEvent($summit, $event_data);
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function updateEvent(ISummit $summit, array $event_data)
    {

        return $this->tx_service->transaction(function() use($summit, $event_data){

            if(!isset($event_data['id'])) throw new EntityValidationException('missing required param: id');

            return $this->saveOrUpdateEvent($summit, $event_data);
        });
    }

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    private function saveOrUpdateEvent(ISummit $summit, array $event_data){
        return $this->tx_service->transaction(function() use($summit, $event_data){

            $event_type_id = intval($event_data['event_type']);
            $event_type    = SummitEventType::get()->byID($event_type_id);
            if(is_null($event_type)) throw new NotFoundEntityException('EventType');

            $event_id = isset($event_data['id']) ? intval($event_data['id']) : 0;
            if($event_id > 0) {
                $event = $this->event_repository->getById($event_id);

                if (is_null($event))
                    throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

                if (intval($event->SummitID) !== intval($summit->getIdentifier()))
                    throw new EntityValidationException('event doest not belongs to summit');
            }
            else {
                // new one
                $event =  $this->event_factory->build($event_type, $summit);
            }

            if(!self::checkEventType($event, $event_type))
                throw new EntityValidationException('Invalid event type!');

            $start_date = $event_data['start_date'];
            $end_date   = $event_data['end_date'];

            if(!empty($start_date) || !empty($end_date))
            {
                $d1 = new DateTime($start_date);
                $d2 = new DateTime($end_date);
                if($d1 >= $d2) throw new EntityValidationException('Start Date should be lower than End Date!');
                if(!$summit->belongsToDuration($d1))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
                if(!$summit->belongsToDuration($d2))
                    throw new EntityValidationException('Start Date should be inside Summit Duration!');
            }

            $event->setStartDate($event_data['start_date']);
            $event->setEndDate($event_data['end_date']);

            $event->Title            = html_entity_decode($event_data['title']);
            $event->RSVPLink         = html_entity_decode($event_data['rsvp_link']);
            $event->HeadCount        = intval($event_data['headcount']);
            $event->Abstract         = html_entity_decode($event_data['abstract']);
            $event->SocialSummary    = strip_tags($event_data['social_summary']);
            $event->AllowFeedBack    = isset($event_data['allow_feedback']) ? $event_data['allow_feedback'] : 0;
            $event->LocationID       = intval($event_data['location_id']);
            $event->TypeID           = $event_type_id;

            $track = PresentationCategory::get()->byID(intval($event_data['track']));
            if(is_null($track)) throw new NotFoundEntityException('Track');

            $event->CategoryID = $track->ID;

            $tags = ($event_data['tags']) ? explode(',',$event_data['tags']) : array();
            $event->Tags()->setByIDList($tags);

            $sponsors = ($event_type->UseSponsors && isset($event_data['sponsors']) && $event_data['sponsors']) ?
                explode(',',$event_data['sponsors']) : [];

            if($event_type->AreSponsorsMandatory && count($sponsors) == 0){
                throw new EntityValidationException('Sponsors are mandatory!');
            }

            $event->Sponsors()->setByIDList($sponsors);

            self::updatePresentationType($event, $event_type, $event_data);

            self::updatePrivateType($event, $event_type, $event_data);

            $must_publish = (bool)$event_data['publish'];
            if($event->isPublished() || $must_publish)
            {
                $this->validateBlackOutTimesAndTimes($event);
                $event->unPublish();
                $event->publish();
            }
            $event->write();
            return $event;
        });
    }

    /**
     * @param ISummit $summit
     * @param $event_id
     * @param $tmp_file
     * @param int $max_file_size
     * @return File
     */
    public function uploadAttachment(ISummit $summit, $event_id, $tmp_file, $max_file_size = 10*1024*1024)
    {

        return $this->tx_service->transaction(function () use ($summit, $event_id, $tmp_file, $max_file_size) {

            $event_id = intval($event_id);
            $event    = $this->event_repository->getById($event_id);

            if(is_null($event) || !$event instanceof SummitEventWithFile) throw new NotFoundEntityException('SummitEventWithFile');

            $attachment = new File();
            $upload     = new Upload();
            $validator  = new Upload_Validator();

            $validator->setAllowedExtensions(['png','jpg','jpeg','gif','pdf']);
            $validator->setAllowedMaxFileSize($max_file_size);
            $upload->setValidator($validator);

            if (!$upload->loadIntoFile($tmp_file, $attachment, 'summit-event-attachments')) {
                throw new EntityValidationException($upload->getErrors());
            }

            $new_file_id = $attachment->write();

            $event->AttachmentID = $new_file_id;

            return $attachment;

        });
    }

    /**
     * @param ISummitEvent $event
     * @param ISummitEventType $event_type
     * @param array $event_data
     * @return ISummitEvent
     * @throws NotFoundEntityException
     * @throws ValidationException
     * @throws null
     */
    private static function updatePresentationType(ISummitEvent $event, ISummitEventType $event_type, array $event_data)
    {
        // Speakers, if one of the added members is not a speaker, we need to make him one
        if ($event_type instanceof PresentationType) {


            if(!isset($event_data['expect_learn']))
                throw new EntityValidationException('expect_learn is required');

            if(!isset($event_data['level']))
                throw new EntityValidationException('level is required');

            // set data
            // if we are creating the presentation from admin, then
            // we should mark it as received and complete
            $event->Status                  = Presentation::STATUS_RECEIVED;
            $event->Progress                = Presentation::PHASE_COMPLETE;
            $event->AttendeesExpectedLearnt = html_entity_decode($event_data['expect_learn']);
            $event->Level                   = $event_data['level'];
            $event->ToRecord                = isset($event_data['to_record'])? $event_data['to_record'] : 0;
            $event->AttendingMedia          = isset($event_data['attending_media'])? $event_data['attending_media'] : 0;

            // speakers ...
            $speaker_ids = [];

            if($event_type->UseSpeakers) {

                if ($event_type->AreSpeakersMandatory && (!isset($event_data['speakers']) || count($event_data['speakers']) == 0)) {
                    throw new EntityValidationException('speakers are mandatory !!!');
                }

                if (isset($event_data['speakers'])) {

                    foreach ($event_data['speakers'] as $speaker) {
                        if (!isset($speaker['speaker_id']) || !isset($speaker['member_id']))
                            throw new EntityValidationException('missing parameter on speakers collection!');

                        $speaker_id = intval($speaker['speaker_id']);
                        $member_id = intval($speaker['member_id']);
                        $speaker = $speaker_id > 0 ? PresentationSpeaker::get()->byID($speaker_id) : null;
                        $speaker = is_null($speaker) && $member_id > 0 ? PresentationSpeaker::get()->filter('MemberID', $member_id)->first() : $speaker;

                        if (is_null($speaker)) {
                            $member = Member::get()->byID($member_id);
                            if (is_null($member)) throw new NotFoundEntityException('Member', sprintf(' member id %s', $member_id));
                            $speaker = new PresentationSpeaker();
                            $speaker->FirstName = $member->FirstName;
                            $speaker->LastName = $member->Surname;
                            $speaker->MemberID = $member->ID;
                            $speaker->write();
                        }

                        $speaker_ids[] = $speaker->ID;

                    }
                }
            }

            $event->Speakers()->setByIDList($speaker_ids);

            // moderators

            $event->ModeratorID = 0;

            if($event_type->UseModerator)
            {
                if($event_type->IsModeratorMandatory && !isset($event_data['moderator']))
                    throw new EntityValidationException('moderator is required!');

                if(isset($event_data['moderator'])) {
                    $moderator = $event_data['moderator'];

                    if (!isset($moderator['member_id']) || !isset($moderator['speaker_id']))
                        throw new EntityValidationException('missing parameter on moderator!');

                    $speaker_id = intval($moderator['speaker_id']);
                    $member_id = intval($moderator['member_id']);
                    $moderator = $speaker_id > 0 ? PresentationSpeaker::get()->byID($speaker_id) : null;
                    $moderator = is_null($moderator) && $member_id > 0 ? PresentationSpeaker::get()->filter('MemberID', $member_id)->first() : $moderator;

                    if (is_null($moderator)) {
                        $member = Member::get()->byID($member_id);
                        if (is_null($member)) throw new NotFoundEntityException('Member', sprintf(' member id %s', $member_id));
                        $moderator = PresentationSpeaker::create();
                        $moderator->FirstName = $member->FirstName;
                        $moderator->LastName = $member->Surname;
                        $moderator->MemberID = $member->ID;
                        $moderator->write();
                    }
                    $event->ModeratorID = $moderator->ID;
                }
            }

        }
        return $event;
    }

    /**
     * @param ISummitEvent $event
     * @param ISummitEventType $event_type
     * @param array $event_data
     * @throws EntityValidationException
     */
    private static function updatePrivateType(ISummitEvent $event, ISummitEventType $event_type, array $event_data){

        if($event_type->Type == ISummitEventType::GroupsEvents){
            if(!isset($event_data['groups']) || count($event_data['groups']) == 0)
                throw new EntityValidationException('groups is required');
            $groups_ids = [];
            foreach ($event_data['groups'] as $group) {
                if (!isset($group['id']))
                    throw new EntityValidationException('missing parameter on groups collection!');

                $groups_ids[] = $group['id'];

            }
            $event->Groups()->setByIDList($groups_ids);
        }
    }
    /**
     * @param ISummitEvent $event
     * @param SummitEventType $type
     * @return bool
     */
    public static function checkEventType(ISummitEvent $event, SummitEventType $type)
    {
        if($event->isPresentation() ){
            return PresentationType::IsPresentationEventType($type->Type);
        }
        return SummitEventType::IsSummitEventType($type->Type);
    }

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateAndPublishBulkEvents(ISummit $summit, array $data)
    {
        $event_repository = $this->event_repository;

        $this->tx_service->transaction(function() use($summit, $data, $event_repository){

            $events = $data['events'];
            foreach($events as $event_dto) {
                $event = $event_repository->getById($event_dto['id']);
                if(is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if(intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');

                $event->LocationID = intval($event_dto['location_id']);
                $event->setStartDate(sprintf("%s %s", $event_dto['start_date'], $event_dto['start_time']));
                $event->setEndDate(sprintf("%s %s", $event_dto['end_date'], $event_dto['end_time']));
                $this->validateBlackOutTimesAndTimes($event);
                $event->unPublish();
                $event->publish();
                $event->write();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkEvents(ISummit $summit, array $data)
    {
        $this->tx_service->transaction(function() use($summit, $data){

            $events = $data['events'];
            foreach($events as $event_dto) {
                $event = $this->event_repository->getById($event_dto['id']);

                if(is_null($event))
                    throw new NotFoundEntityException('SummitEvent');

                if(intval($event->SummitID) !== $summit->getIdentifier())
                    throw new EntityValidationException('SummitEvent does not belong to Summit!');

                $event->LocationID = intval($event_dto['location_id']);
                $event->setStartDate(sprintf("%s %s", $event_dto['start_date'], $event_dto['start_time']));
                $event->setEndDate(sprintf("%s %s", $event_dto['end_date'], $event_dto['end_time']));
                $event->unPublish();
                $event->write();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $event_ids
     */
    public function unPublishBulkEvents(ISummit $summit, array $event_ids)
    {

        $this->tx_service->transaction(function() use($summit, $event_ids) {
            foreach ($event_ids as $event_id) {
                $event = $this->event_repository->getById($event_id);
                if (is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if (intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');
                $event->unPublish();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     * @return mixed
     */
    public function updateAssistance(ISummit $summit, array $data)
    {
        $speaker_repository    = $this->speaker_repository;

        $this->tx_service->transaction(function() use($summit, $data, $speaker_repository){

            foreach ($data as $assistance_data) {

                $speaker_id    = isset($assistance_data['speaker_id']) ? intval($assistance_data['speaker_id']) : 0;

                if(!$speaker_id)
                    throw new EntityValidationException('speaker_id param is missing!');

                $speaker = $speaker_repository->getById($speaker_id);

                if(is_null($speaker))
                    throw new NotFoundEntityException('Speaker');

                $assistance = $speaker->getAssistanceFor($summit->getIdentifier());
                if(is_null($assistance))
                {
                    $assistance = $speaker->createAssistanceFor($summit->getIdentifier());
                    $assistance->write();
                }

                $assistance->OnSitePhoneNumber   = $assistance_data['phone'];
                $assistance->IsConfirmed         = $assistance_data['confirmed'];
                $assistance->RegisteredForSummit = $assistance_data['registered'];
                $assistance->CheckedIn           = $assistance_data['checked_in'];

                $assistance->write();

                if (isset($assistance_data['promo_code']) && !empty($assistance_data['promo_code'])) {
                    $code = $speaker->registerSummitPromoCodeByValue($assistance_data['promo_code'], $summit);
                    $code->write();
                }
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateHeadCount(ISummit $summit, $data)
    {
        $event_repository = $this->event_repository;

        $this->tx_service->transaction(function () use ( $summit, $data, $event_repository) {
            foreach ($data as $event_data) {
                if (!isset($event_data['id']))
                    throw new EntityValidationException('missing required param: id');

                $event_id = intval($event_data['id']);
                $event = $event_repository->getById($event_id);

                if (is_null($event))
                    throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

                if (intval($event->SummitID) !== intval($summit->getIdentifier()))
                    throw new EntityValidationException('event doest not belongs to summit');

                $event->HeadCount = intval($event_data['headcount']);

                $event->write();
            }
        });
    }

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateVideoDisplay(ISummit $summit, $data)
    {
        $this->tx_service->transaction(function () use ($summit, $data) {
            foreach ($data as $event_data) {
                if (!isset($event_data['id']))
                    throw new EntityValidationException('missing required param: id');

                $event_id = intval($event_data['id']);
                $event = $this->event_repository->getById($event_id);

                if (is_null($event))
                    throw new NotFoundEntityException('Summit Event', sprintf('id %s', $event_id));

                if (intval($event->SummitID) !== intval($summit->getIdentifier()))
                    throw new EntityValidationException('event doest not belongs to summit');

                foreach ($event->Materials()->filter('ClassName','PresentationVideo') as $video) {
                    $video->DisplayOnSite = intval($event_data['display_video']);
                    $video->write();
                }

            }
        });
    }

    /**
     * @param $report_name
     * @param $data
     */
    public function updateReportConfig($report_name, $data)
    {

        $report = $this->tx_service->transaction(function () use ($report_name, $data) {
            if (!$report_name)
                throw new EntityValidationException('missing required param: report_name');

            $report = $this->report_repository->getByName($report_name);

            if (is_null($report)) {
                $report = new SummitReport();
                $report->Name = $report_name;
            }

            $report->setConfigByName($data['config_name'],$data['config_value']);

            $report->write();
            return $report;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkPresentations(ISummit $summit, array $data)
    {
        $event_repository = $this->event_repository;

        $this->tx_service->transaction(function() use($summit, $data, $event_repository){

            foreach($data as $presentation) {
                $event = $event_repository->getById($presentation['id']);
                if(is_null($event)) throw new NotFoundEntityException('SummitEvent');
                if(intval($event->SummitID) !== $summit->getIdentifier()) throw new EntityValidationException('SummitEvent does not belong to Summit!');

                $event->Title = $presentation['title'];
                $event->write();
            }
        });
    }

}
