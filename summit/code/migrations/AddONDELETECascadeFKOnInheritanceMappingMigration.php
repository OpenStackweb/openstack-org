<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class AddONDELETECascadeFKOnInheritanceMappingMigration extends AbstractDBMigrationTask
{
    protected $title = "AddONDELETECascadeFKOnInheritanceMappingMigration";

    protected $description = "AddONDELETECascadeFKOnInheritanceMappingMigration";

    function doUp()
    {
        global $database;

        $res = DB::query("ALTER TABLE CalendarSyncInfoCalDav ADD CONSTRAINT FK_CalendarSyncInfoCalDav_CalendarSyncInfo FOREIGN KEY (ID) REFERENCES CalendarSyncInfo(ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE CalendarSyncInfoOAuth2 ADD CONSTRAINT FK_CalendarSyncInfoCalDav_CalendarSyncInfoOAuth2 FOREIGN KEY (ID) REFERENCES CalendarSyncInfo(ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE AdminScheduleSummitActionSyncWorkRequest ADD CONSTRAINT FK_8023C60611D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE AdminSummitEventActionSyncWorkRequest ADD CONSTRAINT FK_EACF2BF011D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE MemberScheduleSummitActionSyncWorkRequest ADD CONSTRAINT FK_6B35B64911D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE MemberEventScheduleSummitActionSyncWorkRequest ADD CONSTRAINT FK_6D4FA41211D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE AdminSummitLocationActionSyncWorkRequest ADD CONSTRAINT FK_DF53639C11D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

        $res = DB::query("ALTER TABLE MemberCalendarScheduleSummitActionSyncWorkRequest ADD CONSTRAINT FK_6442298811D3633A FOREIGN KEY (ID) REFERENCES AbstractCalendarSyncWorkRequest (ID) ON DELETE CASCADE;")->value();

    }

    function doDown()
    {

    }
}