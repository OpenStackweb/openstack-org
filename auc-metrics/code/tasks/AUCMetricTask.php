<?php

/**
 * Class AUCMetricTask
 */
class AUCMetricTask extends CronTask
{

    /**
     * @var array
     */
    protected $services = [];


    /**
     * AUCMetricTask constructor.
     */
    public function __construct()
    {
        foreach (func_get_args() as $arg) {
            if ($arg instanceof OpenStack\AUC\MetricService) {
                $this->services[] = $arg;
            }
        }

        parent::__construct();
    }


    /**
     * @throws ValidationException
     * @throws null
     */
    public function run()
    {
        SapphireTransactionManager::getInstance()->transaction(function(){
            SS_Log::log('Running AUCMetricTask', SS_Log::INFO);
            $expiredMetrics = AUCMetric::get()
                ->where("Expires < DATE(NOW())");

            if($expiredMetrics->exists()) {
                $msg = "Deleting {$expiredMetrics->count()} metrics.";
                $this->writeln($msg);
                SS_Log::log($msg, SS_Log::INFO);
                $expiredMetrics->removeAll();
            }

            $expiryDate = date('Y-m-d', strtotime(AUCMetric::config()->expiry));
            $newMetricCount = 0;
            $errorData = ArrayList::create();
            foreach ($this->services as $service) {
                $identifier = $service->getMetricIdentifier();
                $this->writeHeader("Running service $identifier");
                try {
                    $service->run();
                } catch (Exception $e) {
                    $this->writeln($e->getMessage());
                    SS_Log::log($e->getMessage(), SS_Log::ERR);
                    continue;
                }

                $results = $service->getResults();
                if(!$results) {
                	$this->writeln('No results!');
                	continue;
                }
                
                $members = $results->getMemberList();

                if (!$members) {
                    $this->writeln("--- no members found ---");
                    continue;
                }

                foreach ($members as $m) {
                    if ($m->hasAUCMetric($identifier)) {                        
                        continue;
                    }

                    $m->AUCMetrics()->add(AUCMetric::create([
                        'Identifier' => $identifier,
                        'ValueDescription' => $service->getMetricValueDescription(),
                        'Value' => $results->getValueForMember($m),
                        'Expires' => $expiryDate
                    ])->write());

                    $this->writeln(sprintf(
                        "Added metric to %s %s %s",
                        $m->Email,
                        $service->getMetricValueDescription(),
                        $results->getValueForMember($m)
                    ));

                    $newMetricCount++;
                }

                $errors = $service->getErrors();
                if(!empty($errors)) {
                	$errorList = ArrayList::create();
                	foreach($errors as $e) {
                		$errorList->push(ArrayData::create(['Title' => $e]));
                	}
                	$serviceData = ArrayData::create([
                		'Title' => $identifier,
                		'Errors' => $errorList
                	]);

                	$errorData->push($serviceData);
                }


            }

            $msg = "Done. Created $newMetricCount new metric assignments";
            SS_Log::log($msg, SS_Log::INFO);

            $this->writeln($msg);

            if($errorData->exists()) {
	            $email = EmailFactory::getInstance()->buildEmail(
	            	null,
	            	$this->config()->email_to,
	            	'AUC Metric task: A few things need your attention'
	            );
	            $email->setTemplate('AUCMetricTaskEmail');
	            $email->populateTemplate([
	            	'Services' => $errorData
	            ]);
	            $email->send();

            }
        });
    }

    /**
     * Writes to the browser or the output buffer
     * @param  string $msg     
     */
    protected function write($msg = '')
    {
    	if(Director::is_cli()) {
    		fwrite(STDOUT, $msg);
    	}
    	else {
    		echo $msg;
    	}
    }

    /**
     * Writes a line to the browser or the output buffer
     * @param  string $msg     
     */
    protected function writeln($msg = '')
    {
    	$ln = Director::is_cli() ? PHP_EOL : '<br>';
    	$this->write($msg.$ln);
    }

    /**
     * Writes a header browser or the output buffer
     * @param  string $msg     
     */
    protected function writeHeader($msg)
    {
    	$this->writeln();
    	$this->writeln();
    	$this->writeln("*** $msg ***");
    	$this->writeln();
    	$this->writeln();
    }
}