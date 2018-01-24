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
        $cmd =  sprintf(
            "lib/uc-recognition/tools/get_active_commiters.py %s -b %s -p %s -k %s",
            ACTIVECOMMITTERSERVICE_GERRIT_USER,
            $sixMonthsAgo,
            $outputDir,
            ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE
        );

        if(defined('ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE_PASSWORD') && ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE_PASSWORD != '')
            $cmd .=' -s '.ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE_PASSWORD;

        $execPath = Controller::join_links(
            BASE_PATH,
            AUC_METRICS_DIR,
            $cmd
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
                        )->count() == 0)
                    {
                        $error = new \AUCMetricMissMatchError();
                        $error->ServiceIdentifier = $this->getMetricIdentifier();
                        $error->UserIdentifier    = $email;
                        $error->write();
                    }
                    $this->logError("Member with email " . $row['Email'] . " not found");
                    continue;
                }

                if (!isset($memberMap[$member->Email])) {
                    $memberMap[$member->Email] = [];
                }
                $memberMap[$member->Email][] = basename($filename, '.csv');
            }

            unlink($filename);
        }

        foreach ($memberMap as $email => $repos) {
            $member = Member::get()->filter('Email', $email)->first();
            if (!$member) {
                $error = new \AUCMetricMissMatchError();
                $error->ServiceIdentifier = $this->getMetricIdentifier();
                $error->UserIdentifier    = $email;
                $error->write();
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