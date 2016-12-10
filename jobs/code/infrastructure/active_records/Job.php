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
 * Class Job
 */
final class Job	extends DataObject implements IJob {

    const LocationTypeNA      = 'N/A';
    const LocationTypeRemote  = 'Remote';
    const LocationTypeVarious = 'Various';

    private static $db = array(
        'Title'                 => 'Varchar(255)',
        "Description"           => "HTMLText",
		'PostedDate'            => 'SS_Datetime',
		'ExpirationDate'        => 'SS_Datetime',
		'CompanyName'           => 'Text',
		'MoreInfoLink'          => 'Text',
		'Location'              => 'Text',
		'IsFoundationJob'       => 'Boolean',
		'IsActive'              => 'Boolean',
		'Instructions2Apply'    => 'HTMLText',
		'LocationType'          => 'Enum(array("N/A","Remote", "Various"), "Various")',
        'IsCOANeeded'           => 'Boolean',
	);

    private static $has_many = array(
		'Locations'  => 'JobLocation',
	);

    private static $has_one = array
    (
        'Company'             => 'Company',
        'Type'                => 'JobType',
        'RegistrationRequest' => 'JobRegistrationRequest',
    );

	private static $defaults = array(
		"IsActive"        => 1,
        "IsFoundationJob" => 0,
        "IsCOANeeded"     => 0,
	);

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		if(empty($this->ExpirationDate)){
		    $expiration_date      = new DateTime();
			$expiration_date->add(new DateInterval('P2M'));
			$this->ExpirationDate = $expiration_date->format('Y-m-d H:i:s');
		}
	}

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        if($this->RegistrationRequestID > 0 && $this->RegistrationRequest()->Exists())
            $this->RegistrationRequest()->delete();
        foreach($this->Locations() as $location)
            $location->delete();
    }

	public function getCompanyName(){
	    return $this->CompanyID > 0 ?$this->Company()->Name : $this->getField('CompanyName');
    }

	/**
	 * @return string
	 */
	public function getFormattedLocation(){
		if(!empty($this->LocationType)){
			switch($this->LocationType){
                case self::LocationTypeVarious:{
					$res = '';
					foreach($this->locations() as $location){
						$str_location = $location->city();
						$state = $location->state();
						if(!empty($state))
							$str_location .= ', '.$state;
						$str_location .= ', '.$location->country();
						$res .= $str_location.'<BR>';
					}
					return $res;
				}
				break;
				case self::LocationTypeNA:
					if(!empty($this->Location)){
						return $this->Location;
					}
					return $this->LocationType;
					break;
				default:
					return $this->LocationType;
					break;
			}

		}
	}

	public function isExpired()
    {
        $expiration_date     = new DateTime($this->ExpirationDate);
        $now                 = new DateTime();
        return $expiration_date < $now;
    }

	public function RecentJob() {
		//check if the job posting is less than two weeks old
		return $this->PostedDate > date('Y-m-d H:i:s',strtotime('-2 weeks'));
	}

	/**
	 * @return int
	 */
	public function getIdentifier()	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return void
	 */
	public function deactivate(){
		$this->IsActive = 0;
	}

    public function toggleFoundation() {
        $this->IsFoundationJob = !$this->IsFoundationJob;
    }

	/**
	 * @return IJobLocation[]
	 */
	public function locations()
	{
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->toArray();
	}

	public function addLocation(IJobLocation $location)
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->add($location);
	}

    /**
     * @return JobMainInfo
     */
    function getMainInfo()
    {
        $company       = $this->Company();

        if($this->CompanyID == 0)
            $company->Name = $this->CompanyName;

        return new JobMainInfo
        (
            $this->Title,
            $this->IsCOANeeded,
            $this->TypeID,
            $company,
            $this->MoreInfoLink,
            $this->Description,
            $this->Instructions2Apply,
            $this->LocationType,
            new DateTime($this->ExpirationDate)
        );
    }

    /**
     * @param JobMainInfo $info
     * @return void
     */
    public function registerMainInfo(JobMainInfo $info)	{
        $this->Title              = $info->getTitle();
        $this->MoreInfoLink       = $info->getUrl();
        $this->Description        = $info->getDescription();
        $this->Instructions2Apply = $info->getInstructions();
        $this->LocationType       = $info->getLocationType();
        $this->IsCOANeeded        = $info->isCoaNeeded();
        $this->TypeID             = intval($info->getType());
        $this->CompanyID          = $info->getCompany()->ID;
        $this->CompanyName        = $info->getCompany()->Name;
    }

    public function registerPostedDate ($posted_date) {
        $this->PostedDate = $posted_date;
    }

	/**
	 * @return void
	 */
	public function clearLocations()
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->removeAll();
	}

    public function getSlug() {
        $lcase_title = strtolower(trim($this->Title));
        $title_for_url = str_replace(' ','-',$lcase_title);
        return $title_for_url;
    }

    /**
     * @return boolean
     */
    public function isCOANeeded()
    {
        return $this->getField('IsCOANeeded');
    }
}
