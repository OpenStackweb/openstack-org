<?php

namespace OpenStack\AUC;

use Symfony\Component\Process\Exception\ProcessFailedException;
use \Controller;
use \Member;

/**
 * Class ActiveModeratorService
 * @package OpenStack\AUC
 */
class ActiveModeratorService extends BaseService implements MetricService
{
    use ProcessCreator;

    public function getMetricIdentifier()
    {
        return "ACTIVE_MODERATOR_ASK_OPENSTACK";
    }

    public function getMetricValueDescription()
    {
        return "User ID";
    }

    public function run()
    {
        $execPath = Controller::join_links(
            BASE_PATH,
            AUC_METRICS_DIR,
            'lib/uc-recognition/tools/get_active_moderator.py'
        );

        $process = $this->getProcess($execPath);
        $process->start();

        while ($process->isRunning()) {

        }
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $parts = preg_split("/((\r?\n)|(\r\n?))/", $output);
        $this->results = ResultList::create();

        foreach ($parts as $line) {
            if (preg_match('/^Getting page: [0-9]+/', $line)) {
                continue;
            }

            preg_match('/(.*?)([0-9]+)\s+$/', $line, $matches);

            if (!$matches) {
                continue;
            }

            $username = trim($matches[1]);
            $value    = trim($matches[2]);

            $member = Member::get()->filterAny([
            	'AskOpenStackUsername' => $username,
                'Email' => $username,
                'SecondEmail' => $username,
                'ThirdEmail' => $username,
                'IRCHandle' => $username,
                'TwitterName' => $username
            ])->first();

            if(!$member){
                // check translation table
                $trans = \AUCMetricTranslation::get()->filter(['UserIdentifier' => $username])->first();
                $member = $trans ? $trans->MappedFoundationMember() : null;
            }

            if(!$member){
                if(\AUCMetricMissMatchError::get()->filter
                    (
                        [
                            "ServiceIdentifier" => $this->getMetricIdentifier(),
                            "UserIdentifier"    => $username
                        ]
                    )->count() == 0 ) {
                    $error = new \AUCMetricMissMatchError();
                    $error->ServiceIdentifier = $this->getMetricIdentifier();
                    $error->UserIdentifier = $username;
                    $error->write();
                }
                $this->logError("Member $username not found.");
                continue;
            }

            $this->results->push
            (
                Result::create($member, $value)
            );
        }
    }
}