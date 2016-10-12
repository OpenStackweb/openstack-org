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
    use ParserCreator;

    public function getMetricIdentifier()
    {
        return "ACTIVE_MEMBER_UC_WORKING_GROUP";
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

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $execPath = Controller::join_links(
            BASE_PATH,
            AUC_METRICS_DIR,
            'lib/uc-recognition/tools/get_active_wg_members.py --datadir=' . $collectionDir
        );

        $process = $this->getProcess($execPath);
        $process->start();

        while ($process->isRunning()) {
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        
        $fileData = explode(PHP_EOL, $output);
        $fileData[0] = "'Username','AttendanceCount','LinesSaid'";
        $csvPath = Controller::join_links(
        	$outputDir,
        	'results.csv'
        );

        file_put_contents($csvPath, implode(PHP_EOL, $fileData));

        $parser = $this->getParser($csvPath);
        $parser->mapColumns([
            'Username' => 'Username',
            'AttendanceCount' => 'AttendanceCount',
            'LinesSaid' => 'LinesSaid'
        ]);

        $this->results = ResultList::create();

        foreach ($parser as $row) {
            $nickname = $row['Username'];
            $attendanceCount = $row['AttendanceCount'];
            $linesSaid = $row['LinesSaid'];

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