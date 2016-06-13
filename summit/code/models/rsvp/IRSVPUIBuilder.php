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

/**
 * Interface IRSVPUIBuilder
 */
interface IRSVPUIBuilder
{
    /**
     * @param IRSVPTemplate $template
     * @param IRSVP $rsvp
     * @param ISummitEvent $event_id
     * @param string $form_name
     * @return Form
     */
    public function build(IRSVPTemplate $template, IRSVP $rsvp, ISummitEvent $event, $form_name ='RSVPForm');
}