<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class DBSchema
{
    /**
     * @param string $db_name
     * @param string $table_name
     * @return bool
     */
    public static function existsTable($db_name, $table_name)
    {
        $sql = <<<SQL
    SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA ='{$db_name}' AND TABLE_NAME= '{$table_name}';
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    /**
     * @param string $db_name
     * @param string $table_name
     * @param string $col_name
     * @return bool
     */
    public static function existsColumn($db_name, $table_name, $col_name){
        $sql = <<<SQL
        SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA ='{$db_name}' AND TABLE_NAME= '{$table_name}' AND COLUMN_NAME = '{$col_name}';
SQL;
        return intval(DB::query($sql)->value()) > 0;

    }

    /**
     * @param string $db_name
     * @param string $table_name
     * @param string $index_name
     * @return bool
     */
    public static function existsIndex($db_name, $table_name, $index_name){
        $sql = <<<SQL
      SELECT COUNT(*) FROM information_schema.statistics WHERE TABLE_SCHEMA ='{$db_name}' AND TABLE_NAME = '{$table_name}' AND INDEX_NAME = '{$index_name}';
SQL;
       return intval(DB::query($sql)->value()) > 0;
    }

    public static function dropColumn($db_name, $table_name, $col_name)
    {
        $sql = <<<SQL
       ALTER TABLE $table_name DROP COLUMN $col_name;
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    public static function renameColumn($db_name, $table_name, $old_column_name, $new_column_name, $data_type)
    {
        $sql = <<<SQL
       ALTER TABLE "{$table_name}" CHANGE '{$old_column_name}' '{$new_column_name}' {$data_type};
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    public static function dropIndex($db_name, $table_name, $index_name)
    {
        $sql = <<<SQL
      ALTER TABLE {$table_name} DROP INDEX {$index_name};
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    public static function dropTable($db_name, $table_name)
    {
        $sql = <<<SQL
      DROP TABLE {$table_name};
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }

    public static function truncateTable($db_name, $table_name){
        $sql = <<<SQL
      TRUNCATE TABLE {$table_name};
SQL;
        return intval(DB::query($sql)->value()) > 0;
    }
}