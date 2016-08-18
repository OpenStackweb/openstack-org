<?php

namespace OpenStack\AUC;

use Symfony\Component\Process\Exception\ProcessFailedException;
use \Controller;
use \Config;
use \Member;

/**
 * Class ActiveCommitterService
 * @package OpenStack\AUC
 */
class ActiveCommitterService extends BaseService implements MetricService
{
    use ProcessCreator;
    use ParserCreator;

    /**
     * @return string
     */
    public function getMetricIdentifier()
    {
        return "REPOSITORY_CONTRIBUTOR";
    }

    /**
     * @return string
     */
    public function getMetricValueDescription()
    {
        return "Repositories";
    }

    /**
     * @return void
     */
    public function run()
    {
        $outputDir = Controller::join_links(
            TEMP_FOLDER,
            'auc-metrics',
            'active-committers'
        );

        @mkdir($outputDir, 0755, true);

        $sixMonthsAgo = date('YmdHis', strtotime('-6 months'));
        $user = Config::inst()->get('AUCActiveCommitterService', 'user');
        $keyFile = Config::inst()->get('AUCActiveCommitterService', 'keyfile');

        $execPath = Controller::join_links(
            BASE_PATH,
            AUC_METRICS_DIR,
            sprintf(
                "lib/uc-recognition/tools/get_active_commiters.py %s -b %s -p %s -k %s",
                $user,
                $sixMonthsAgo,
                $outputDir,                
                $keyFile
            )
        );

        $process = $this->getProcess($execPath);
        $process->start();

        while ($process->isRunning()) {
        }

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $memberMap = [];
        $this->results = ResultList::create();

        foreach (glob($outputDir . '/*.csv') as $filename) {
            $parser = $this->createCommitterParser($filename);
            foreach ($parser as $row) {
                $email = $row['Email'];
                $member = Member::get()->filterAny([
                    'Email' => $email,
                    'SecondEmail' => $email,
                    'ThirdEmail' => $email
                ])->first();

                if ($member) {
                    if (!isset($memberMap[$member->Email])) {
                        $memberMap[$member->Email] = [];
                    }
                    $memberMap[$member->Email][] = basename($filename, '.csv');
                } else {
                    $this->logError("Member with email " . $row['Email'] . " not found");
                }
            }

            unlink($filename);
        }

        foreach ($memberMap as $email => $repos) {
            $member = Member::get()->filter('Email', $email)->first();
            if (!$member) {
            	$this->logError("Member $email not found.");
                continue;
            }

            $this->results->push(Result::create(
                $member,
                implode(', ', $repos)
            ));
        }
    }

    /**
     * @param $filename
     * @return mixed
     */
    protected function createCommitterParser($filename)
    {
        $parser = $this->getParser($filename);

        $parser->provideHeaderRow([
            'Username',
            'Name',
            'Email'
        ]);

        $parser->mapColumns([
            'Username' => 'Username',
            'Name' => 'Name',
            'Email' => 'Email'
        ]);

        return $parser;
    }

}