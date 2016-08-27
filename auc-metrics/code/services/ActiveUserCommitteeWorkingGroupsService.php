<?php

namespace OpenStack\AUC;

use Symfony\Component\Process\Exception\ProcessFailedException;
use \Controller;
use \Member;

/**
 * Class ActiveUserCommitteeWorkingGroupsService
 * @package OpenStack\AUC
 */
class ActiveUserCommitteeWorkingGroupsService extends BaseService implements MetricService
{

    use ProcessCreator;

    public function getMetricIdentifier()
    {
        return "ACITIVE_MEMBER_UC_WORKING_GROUP";
    }


    public function getMetricValueDescription()
    {
        return "Attendance count / Lines said";
    }


    public function run()
    {
        $outputDir = Controller::join_links(
            TEMP_FOLDER,
            'auc-metrics',
            'meeting-data'
        );

        @mkdir($outputDir, 0755, true);

        $collectionDir = Controller::join_links(
            $outputDir,
            'eavesdrop.openstack.org/meetings'
        );

        @mkdir($collectionDir, 0755, true);

        $execPath = Controller::join_links(
        	BASE_PATH,
        	AUC_METRICS_DIR,
        	'lib/uc-recognition/tools/get_meeting_data.sh ' . $outputDir
        );

        $process = $this->getProcess($execPath);
        $process->start();

        while($process->isRunning()) {}

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $execPath = Controller::join_links(
            BASE_PATH,
            AUC_METRICS_DIR,
            'lib/uc-recognition/tools/get_active_wg_members.py ' . $collectionDir
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
        $parts = explode('OVERALL STATS', $output);
        $parts = preg_split("/((\r?\n)|(\r\n?))/", $parts[1]);
        $this->results = ResultList::create();

        foreach ($parts as $line) {
            preg_match('/^([^=\s]+)\s+([0-9]+)\s+([0-9]+)\s*$/', $parts[1], $matches);
            if (!$matches) {
                continue;
            }
            $nickname = trim($matches[1]);
            $attendanceCount = trim($matches[2]);
            $linesSaid = trim($matches[3]);

            $member = Member::get()->filter('IRCHandle', $nickname)->first();
            if ($member) {
                $this->results->push(Result::create(
                    $member,
                    "$attendanceCount / $linesSaid"
                ));
            } else {
                $this->logError("No member with nickname {$nickname}");
            }

        }
    }
}