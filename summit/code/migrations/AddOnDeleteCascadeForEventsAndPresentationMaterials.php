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

final class AddOnDeleteCascadeForEventsAndPresentationMaterials extends AbstractDBMigrationTask
{
    protected $title = "AddOnDeleteCascadeForEventsAndPresentationMaterials";

    protected $description = "AddOnDeleteCascadeForEventsAndPresentationMaterials";

    function doUp()
    {
        $queries = [
            "DELETE from SummitEventWithFile WHERE NOT EXISTS ( SELECT SummitEvent.ID From SummitEvent where SummitEvent.ID = SummitEventWithFile.ID);",
            "ALTER TABLE SummitEventWithFile ADD CONSTRAINT FK_C1BB641711D36333 FOREIGN KEY (ID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;",
            "DELETE from SummitGroupEvent WHERE NOT EXISTS ( SELECT SummitEvent.ID From SummitEvent where SummitEvent.ID = SummitGroupEvent.ID);",
            "ALTER TABLE SummitGroupEvent ADD CONSTRAINT FK_93CE043011D3633A FOREIGN KEY (ID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;",
            "DELETE FROM Presentation WHERE NOT EXISTS ( SELECT SummitEvent.ID FROM SummitEvent WHERE SummitEvent.ID = Presentation.ID);",
            "ALTER TABLE Presentation ADD CONSTRAINT FK_760FE6EC280A331N FOREIGN KEY (ID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;",
            "DELETE from PresentationMaterial WHERE NOT EXISTS ( SELECT Presentation.ID From Presentation where Presentation.ID = PresentationID);",
            "ALTER TABLE PresentationMaterial ADD CONSTRAINT FK_760FE6EC280A3317 FOREIGN KEY (PresentationID) REFERENCES Presentation (ID);",
            "DELETE from PresentationLink WHERE NOT EXISTS ( SELECT PresentationMaterial.ID From PresentationMaterial where PresentationMaterial.ID = PresentationLink.ID);",
            "ALTER TABLE PresentationLink ADD CONSTRAINT FK_DDDF040D11D3633A FOREIGN KEY (ID) REFERENCES PresentationMaterial (ID) ON DELETE CASCADE;",
            "DELETE from PresentationVideo WHERE NOT EXISTS ( SELECT PresentationMaterial.ID From PresentationMaterial where PresentationMaterial.ID = PresentationVideo.ID);",
            "ALTER TABLE PresentationVideo ADD CONSTRAINT FK_C827178611D3633A FOREIGN KEY (ID) REFERENCES PresentationMaterial (ID) ON DELETE CASCADE;",
            "DELETE from PresentationSlide WHERE NOT EXISTS ( SELECT PresentationMaterial.ID From PresentationMaterial where PresentationMaterial.ID = PresentationSlide.ID);",
            "ALTER TABLE PresentationSlide ADD CONSTRAINT FK_C60F23C811D3633A FOREIGN KEY (ID) REFERENCES PresentationMaterial (ID) ON DELETE CASCADE;",
        ];

        foreach($queries as $ql)
            DB::query($ql)->value();
    }

    function doDown()
    {

    }
}