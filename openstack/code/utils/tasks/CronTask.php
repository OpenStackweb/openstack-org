<?php
/**
 * Copyright 2015 Openstack Foundation
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

define('LOCK_DIR', '/tmp/');
define('LOCK_SUFFIX', '.lock');

/**
 * Class CronTask
 */
abstract class CronTask extends CliController {

    protected static $pid;

    protected function isRunning() {
        $pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
        if(in_array($this->pid, $pids))
            return TRUE;
        return FALSE;
    }

    private function getLockFile(){
        return LOCK_DIR . strtolower(get_class($this)) .LOCK_SUFFIX;;
    }

    public function index() {
        foreach(ClassInfo::subclassesFor($this->class) as $subclass) {
            echo $subclass . "\n";
            $task = $subclass::create();
            $task->init();
            $task->process();
        }
    }

    protected function unlock() {

        $lock_file = $this->getLockFile();

        if(file_exists($lock_file))
            unlink($lock_file);

        error_log("(".$this->pid.") Releasing lock...");
        return TRUE;
    }

    protected function lock() {

        $lock_file = $this->getLockFile();

        if(file_exists($lock_file)) {
            //return FALSE;

            // Is running?
            $this->pid = file_get_contents($lock_file);
            if(self::isrunning()) {
                error_log("(".$this->pid.") Already in progress...");
                return FALSE;
            }
            else {
                error_log("(".$this->pid.") Previous job died abruptly...");
            }
        }

        $this->pid = getmypid();
        file_put_contents($lock_file, $this->pid);
        error_log("(".$this->pid.") Lock acquired, processing the job...");
        return $this->pid;
    }

    /**
     *
     */
    function process()
    {
        set_time_limit(0);

        if(($pid = $this->lock()) !== FALSE) {
            $this->run();
            $this->unlock();
        }
    }

    /**
     * @return void
     */
    public abstract function run();
}