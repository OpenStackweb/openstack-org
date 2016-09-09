<?php
/**
 * Copyright 2016 OpenStack Foundation
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

final class RoomMetricType extends DataObject implements IRoomMetricType
{
    private static $db = array
    (
        'Type'     => 'Enum(array("Persons","CO2","Temperature","Humidity"), "Persons")',
        'Endpoint' => 'Text',
    );

    private static $has_many = array
    (
        'Samples' => 'RoomMetricSampleData',
    );

    private static $has_one = array
    (
        'Room' => 'SummitVenueRoom',
    );

    private static $summary_fields = array
    (
        'Type',
        'Endpoint',
        'SamplesCount',
    );

    public function getCMSFields()
    {
        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', $ddl_type = new DropdownField('Type','Type', singleton('RoomMetricType')->dbObject('Type')->enumValues()));
        $f->addFieldToTab('Root.Main', new TextField('Endpoint','Endpoint URL'));
        $f->addFieldToTab('Root.Main', new HiddenField('RoomID','RoomID'));
        $ddl_type->setEmptyString("-- SELECT A TYPE --");

        if($this->ID > 0){
            $config    = GridFieldConfig_RecordViewer::create();
            $gridField = new GridField('Samples', 'Samples', $this->Samples(), $config);
            $f->addFieldToTab('Root.Main', $gridField);
        }
        return $f;
    }

    public function getSamplesCount(){
        return $this->Samples()->count();
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        $type = trim($this->Type);
        if (empty($type)) {
            return $valid->error('Type is required!');
        }

        $endpoint = trim($this->Endpoint);
        if (empty($endpoint)) {
            return $valid->error('Endpoint URL is required!');
        }

        if (filter_var($endpoint, FILTER_VALIDATE_URL) === FALSE) {
            return $valid->error('Endpoint URL is not a valid URL!');
        }

        return $valid;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getField('Type');
    }

    /**
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->getField('Endpoint');
    }

    /**
     * @return ISummitVenueRoom
     */
    public function getRoom()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Room')->getTarget();
    }

    /**
     * @param string $value
     * @param int $time_stamp
     * @return void
     */
    public function addSample($value, $time_stamp)
    {
        $sample            = new RoomMetricSampleData();
        $sample->Value     = floatval($value);
        $sample->TimeStamp = intval($time_stamp);
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Samples')->add($sample);
    }

    /**
     * @return int
     */
    public function getLastSampleTimeStamp()
    {
        $sample = $this->Samples()->sort('TimeStamp', 'DESC')->first();
        return (int)(!is_null($sample) ? $sample->TimeStamp : 0);
    }
}