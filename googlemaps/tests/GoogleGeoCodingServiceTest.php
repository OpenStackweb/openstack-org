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

class GoogleGeoCodingServiceTest extends SapphireTest
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCityCoordinates(){
        $repository_mock = Mockery::mock(IGeoCodingQueryRepository::class);
        $repository_mock->shouldReceive("getByGeoQuery")->andReturnNull();
        $repository_mock->shouldReceive("add")->andReturnNull();
        $service = new GoogleGeoCodingService
        (
            $repository_mock,
            new UtilFactory,
            SapphireTransactionManager::getInstance()

        );

        list($lat,$lng) = $service->getCityCoordinates("Lanus", "Argentina", "Buenos Aires");

        $this->assertTrue(sprintf("%s", $lat) == "-34.6994795");
        $this->assertTrue(sprintf("%s", $lng) == "-58.3920795");
    }

}