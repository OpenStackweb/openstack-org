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
 * Class MarketplaceUpdatesDigestTask
 */
final class MarketplaceUpdatesDigestTask extends CronTask {

	function run(){

		try{
			$recent_updates = CompanyServiceUpdateRecord::get()->sort(array('CompanyService.ClassName' => 'ASC', 'CompanyService.Name' => 'ASC','Created' => 'DESC'));
            $update_list = '';
            $prev_service = 0;
            $prev_class = '';

            foreach ($recent_updates as $update) {
                if (!$prev_class || $prev_class != $update->CompanyService()->ClassName) {
                    $prev_class = $update->CompanyService()->ClassName;
                    $update_list .= '<br><u>' . $this->getClassNameNice($prev_class) . '</u>: </br>';
                }
                if (!$prev_service || $prev_service != $update->CompanyServiceID) {
                    $prev_service = $update->CompanyServiceID;
                    $update_list .= '<br><b>' . $update->CompanyService()->getName() . '</b></br>';
                }
                $update_list .= date('m-d-Y g:ia',strtotime($update->Created)). ' by ' . $update->Editor()->getName(). '<br>';

                // delete update record.
                $update->delete();
            }

            $email_subject = "Marketplace Company Service Update Digest";
            $email_body = "The following Company Services were updated: <br>";
            $email_body .= $update_list . "<br><br>";
            $email_body .= "Please go <a href='https://openstack.org/marketplaceadmin/'>here</a> to review the data.
                          <br><br>Thank you,<br>Marketplace Admin";

            $email = EmailFactory::getInstance()->buildEmail('noreply@openstack.org', MARKETPLACE_ADMIN_UPDATE_EMAIL_TO, $email_subject, $email_body);
            $email->send();

		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}

        return 'OK';
	}

    public function getClassNameNice($class_name) {
        switch ($class_name) {
            case 'ConsultantDraft' :
                return 'Consultants';
                break;
            case 'DistributionDraft' :
                return 'Distributions';
                break;
            case 'PublicCloudServiceDraft' :
                return 'Public Clouds';
                break;
            case 'PrivateCloudServiceDraft' :
                return 'Private Clouds';
                break;
            case 'RemoteCloudServiceDraft' :
                return 'Remote Clouds';
                break;
            case 'ApplianceDraft' :
                return 'Appliances';
                break;
            default:
                return $class_name;
        }
    }
} 