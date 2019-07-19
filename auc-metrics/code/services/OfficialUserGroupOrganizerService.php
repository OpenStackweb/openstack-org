<?php

namespace OpenStack\AUC;

use \Exception;
use \Controller;
use \Member;
use \Injector;

/**
 * Class OfficialUserGroupOrganizerService
 * @package OpenStack\AUC
 */
class OfficialUserGroupOrganizerService extends BaseService implements MetricService
{

    use ParserCreator;

    public function getMetricIdentifier()
    {
        return "OFFICIAL_USER_GROUP_ORGANIZER";
    }

    public function getMetricValueDescription()
    {
        return null;
    }

    public function run()
    {
        if (!defined('GROUP_CONTACT_REPORT_TOKEN')) {
            throw new Exception(
                'Constant GROUP_CONTACT_REPORT_TOKEN not defined'
            );
        }
        $this->results = ResultList::create();
        $api = new \MeetupApi();
        $groups = $api->getGroups(PHP_INT_MAX);
        foreach($groups as $group){
            $members = $api->getGroupMembers($group['urlname'], PHP_INT_MAX);
            foreach($members as $member) {
                $fullName  = $member['name'];
                $nameParts = explode(' ', $fullName);
                $firstName = count($nameParts) >= 1 ? $nameParts[0]: null;
                $lastName  = count($nameParts) >= 2 ? $nameParts[1]: null;
                if(Member::get()->filterAny([
                    'FirstName' => $firstName,
                    'Surname'   => $lastName,
                ])->count() > 1){
                    $this->logError("Member with name " . $fullName . " has more than once instance on DB");
                    continue;
                }

                $dbMember = Member::get()->filterAny([
                    'FirstName' => $firstName,
                    'Surname'   => $lastName,
                ])->first();

                if(!$dbMember){
                    if(\AUCMetricMissMatchError::get()->filter
                        (
                            [
                                "ServiceIdentifier" => $this->getMetricIdentifier(),
                                "UserIdentifier"    => $fullName
                            ]
                        )->count() == 0 ) {
                        $error = new \AUCMetricMissMatchError();
                        $error->ServiceIdentifier = $this->getMetricIdentifier();
                        $error->UserIdentifier = $fullName;
                        $error->write();
                    }
                    $this->logError("Member with name " . $fullName . " not found");
                    continue;
                }

                $this->results->push(Result::create($dbMember));
            }
        }
    }

    protected function getHTTPClient()
    {
        return Injector::inst()->get('AUCHTTPClient');
    }


}
