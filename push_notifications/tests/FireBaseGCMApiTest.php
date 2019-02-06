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


class FireBaseGCMApiTest extends SapphireTest
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function test(){
        $client = new FireBaseGCMApi(FIREBASE_GCM_SERVER_KEY);
        $serializer_mock = Mockery::mock("IFireBasePushNotificationSerializationStrategy");
        $serializer_mock->shouldReceive("getToField")->andReturn(['smarcet']);
        $serializer_mock->shouldReceive("getDataField")->andReturn(['title' => 'test']);
        $notification_mock = Mockery::mock("PushNotificationMessage");
        $notification_mock->shouldReceive("getPlatform")->andReturn('MOBILE');

        $response = $client->sendPush
        (
            $serializer_mock->getToField(),
            $serializer_mock->getDataField(),
            IPushNotificationMessage::HighPriority,
            $notification_mock->getPlatform()
        );

    }
}