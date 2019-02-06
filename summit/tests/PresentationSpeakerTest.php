<?php
/**
 * Copyright 2019 OpenStack Foundation
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

class PresentationSpeakerTest extends SapphireTest
{

    public function testModeratedPresentationsSummit25(){
        $speaker = PresentationSpeaker::get()->filter(["ID" => 1796])->first();
        $this->assertTrue($speaker->FirstName == 'Roger');
        $presentations = $speaker->ModeratorPresentations(25);
        $this->assertTrue(intval($presentations->count()) == 1);
    }

    public function testModeratedPresentationsSummit25Other(){
        $speaker = PresentationSpeaker::get()->filter(["ID" => 1796])->first();
        $this->assertTrue($speaker->FirstName == 'Roger');
        $presentations = $speaker->OtherModeratorPresentations(25);
        $this->assertTrue(intval($presentations->count()) == 0);
    }

    public function testModeratedPresentationsSummit24(){
        $speaker = PresentationSpeaker::get()->filter(["ID" => 1796])->first();
        $this->assertTrue($speaker->FirstName == 'Roger');
        $presentations = $speaker->ModeratorPresentations(24);
        $this->assertTrue(intval($presentations->count()) == 0);
    }
}