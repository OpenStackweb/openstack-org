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
 * Class DeploymentSurveyTask
 */
final class DeploymentSurveyTask extends CronTask
{

    function run()
    {

        $batch_size = 15;

        if (isset($_GET['batch_size'])) {
            $batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
        }

        $surveys     = DeploymentSurvey::getNotDigestSent($batch_size);
        $deployments = Deployment::getNotDigestSent($batch_size);


        if ($surveys) {
            foreach ($surveys as $survey) {
                $survey->SendDigest = 1;
                $survey->write();
            }
        }

        if ($deployments) {
            foreach ($deployments as $dep) {
                $dep->SendDigest = 1;
                $dep->write();
            }
        }

        if ((!is_null($surveys) && count($surveys)) > 0 || (!is_null($deployments) && count($deployments))) {
         
            global $email_new_deployment;

            $email = EmailFactory::getInstance()->buildEmail($email_new_deployment, $email_new_deployment, $subject = 'New Deployments and Surveys');

            $email->setTemplate('NewDeploymentsSurveysEmail');

            $email->populateTemplate(array(
                'SurveysUrl'            => Director::absoluteURL('admin/deployments/DeploymentSurvey/EditForm/field/DeploymentSurvey/item'),
                'DeploymentsUrl'        => Director::absoluteURL('admin/deployments/Deployment/EditForm/field/Deployment/item'),
                'SangriaDeploymentsUrl' => Director::absoluteURL('sangria/ViewDeploymentDetails'),
                'Surveys'               => $surveys,
                'Deployments'           => $deployments
            ));

            $email->send();
        }
    }
} 