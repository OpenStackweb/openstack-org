<?php
/**
 * Copyright 2014 Openstack Foundation
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

/**
 * Class SapphireTransactionManager
 */
final class SapphireTransactionManager implements ITransactionManager
{

    /**
     * @var ITransactionManager
     */
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return ITransactionManager
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new SapphireTransactionManager();
        }

        return self::$instance;
    }

    /**
     * @var int
     */
    private $transactions = 0;

    private function beginTransaction()
    {
        ++$this->transactions;
        if ($this->transactions == 1) {
           DB::getConn()->transactionStart();
        }
    }

    private function commit()
    {
        if ($this->transactions == 1) {
            $queries = SapphireBulkQueryRegistry::getInstance()->getPreQueries();
            foreach ($queries as $q) {
                foreach ($q->toSQL() as $sql) {
                    DB::query($sql);
                }
            }

            UnitOfWork::getInstance()->commit();

            $queries = SapphireBulkQueryRegistry::getInstance()->getPostQueries();
            foreach ($queries as $q) {
                foreach ($q->toSQL() as $sql) {
                    DB::query($sql);
                }
            }
            DB::getConn()->transactionEnd();
        }
        --$this->transactions;
    }

    private function rollBack()
    {
        if ($this->transactions == 1) {
            $this->transactions = 0;
            DB::getConn()->transactionRollback();
        } else {
            --$this->transactions;
        }
    }

    /**
     * @param Closure $callback
     * @return null
     * @throws EntityValidationException
     * @throws Exception
     */
    public function transaction(Closure $callback)
    {
        $result = null;
        try {
            $this->beginTransaction();
            $r = new ReflectionFunction($callback);
            // reload on UOW entities that could not being on the update context
            foreach ($r->getStaticVariables() as $var) {
                if ($var instanceof IEntity && $var->getIdentifier() > 0) {
                    UnitOfWork::getInstance()->scheduleForUpdate($var);
                }
            }
            $result = $callback($this);
            $this->commit();
        }
        catch(ValidationException $ex1)
        {
            $this->rollBack();
            throw new EntityValidationException($ex1->getMessage());
        }
        catch (Exception $ex) {
            $this->rollBack();
            throw $ex;
        }

        return $result;
    }
}