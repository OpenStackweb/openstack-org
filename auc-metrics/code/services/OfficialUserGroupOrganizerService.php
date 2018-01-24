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

        $csvPath = "https://groups.openstack.org/reports/group-contact-report/csv?token=" . GROUP_CONTACT_REPORT_TOKEN . "&status=official";

        $client = $this->getHTTPClient();
        $response = $client->get($csvPath);
        if (200 !== $response->getStatusCode()) {
            throw new Exception(
                "URL $csvPath returned status code: " . $response->getStatusCode()
            );
        }

        $csvDir = Controller::join_links(
            TEMP_FOLDER,
            'auc-metrics'
        );
        $csvPath = $csvDir.'/group-contacts.csv';

        @mkdir($csvDir);

        $body = $response->getBody();

        // The first line of the CSV is written erroneously. Fix it.
        $fileData = explode(PHP_EOL, $body);
        $fileData[0] = "'User group','Full name','Email','Type'";
        file_put_contents($csvPath, implode(PHP_EOL, $fileData));

        $parser = $this->getParser($csvPath);
        $parser->mapColumns([
            'User group' => 'UserGroup',
            'Full name' => 'FullName',
            'Email' => 'Email',
            'Type' => 'Type'
        ]);

        $this->results = ResultList::create();
        foreach ($parser as $row) {

            $email  = $row['Email'];
            $member = Member::get()->filterAny([
                'Email'       => $email,
                'SecondEmail' => $email,
                'ThirdEmail'  => $email
            ])->first();


            if(!$member){
                // check translation table
                $trans = \AUCMetricTranslation::get()->filter(['UserIdentifier' => $email])->first();
                $member = $trans ? $trans->MappedFoundationMember() : null;
            }

            if(!$member){
                if(\AUCMetricMissMatchError::get()->filter
                    (
                        [
                            "ServiceIdentifier" => $this->getMetricIdentifier(),
                            "UserIdentifier"    => $email
                        ]
                    )->count() == 0 ) {
                    $error = new \AUCMetricMissMatchError();
                    $error->ServiceIdentifier = $this->getMetricIdentifier();
                    $error->UserIdentifier = $email;
                    $error->write();
                }
                $this->logError("Member with email " . $row['Email'] . " not found");
                continue;
            }

            $this->results->push(Result::create($member));

        }

        unlink($csvPath);
    }

    protected function getHTTPClient()
    {
        return Injector::inst()->get('AUCHTTPClient');
    }


}
