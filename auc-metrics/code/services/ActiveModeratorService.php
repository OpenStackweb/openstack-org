<?php

namespace OpenStack\AUC;

use Symfony\Component\Process\Exception\ProcessFailedException;
use \Controller;
use \Member;

/**
 * Class ActiveModeratorService
 * @package OpenStack\AUC
 */
class ActiveModeratorService implements MetricService
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

    public function getResults()
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
        $results = ResultList::create();

        foreach ($parts as $line) {
            if (preg_match('/^Getting page: [0-9]+/', $line)) {
                continue;
            }

            preg_match('/(.*?)([0-9]+)\s+$/', $line, $matches);

            if (!$matches) {
                continue;
            }

            $username = trim($matches[1]);
            $value = trim($matches[2]);

            $member = Member::get()->filterAny([
            	'AskOpenStackUsername' => $username,
                'Email' => $username,
                'SecondEmail' => $username,
                'ThirdEmail' => $username,
                'IRCHandle' => $username,
                'TwitterName' => $username
            ])->first();

            if ($member) {
                $results->push(
                    Result::create($member, $value)
                );
            }
        }

        return $results;
    }
}