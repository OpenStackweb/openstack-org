<?php
/**
 * Copyright 2018 OpenStack Foundation
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

class AddOnDeleteCascadeFKPromoCodesMigration extends AbstractDBMigrationTask
{
    protected $title = "AddOnDeleteCascadeFKPromoCodesMigration";

    protected $description = "AddOnDeleteCascadeFKPromoCodesMigration";

    function doUp()
    {
        global $database;

        $res = DB::query("DELETE FROM MemberSummitRegistrationPromoCode
               WHERE ID not in (select ID from SummitRegistrationPromoCode);")->value();

        $res = DB::query("ALTER TABLE MemberSummitRegistrationPromoCode 
ADD CONSTRAINT FK_MemberSummitRegistrationPromoCode_SummitRegistrationPromoCode 
FOREIGN KEY (ID) REFERENCES SummitRegistrationPromoCode(ID) ON DELETE CASCADE;")->value();

        $res = DB::query("delete from SponsorSummitRegistrationPromoCode
where ID not in (select ID from MemberSummitRegistrationPromoCode);")->value();

        $res = DB::query("ALTER TABLE SponsorSummitRegistrationPromoCode 
ADD CONSTRAINT FK_Sponsor_Member_PromoCode
FOREIGN KEY (ID) REFERENCES MemberSummitRegistrationPromoCode(ID) ON DELETE CASCADE;")->value();

        $res = DB::query("delete from SpeakerSummitRegistrationPromoCode
where ID not in (select ID from SummitRegistrationPromoCode);")->value();

        $res = DB::query("ALTER TABLE SpeakerSummitRegistrationPromoCode 
ADD CONSTRAINT FK_Speaker_PromoCode 
FOREIGN KEY (ID) REFERENCES SummitRegistrationPromoCode(ID) ON DELETE CASCADE;")->value();
    }

    function doDown()
    {

    }

}