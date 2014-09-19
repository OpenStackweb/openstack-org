<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Patricio T
 * Date: 10/29/13
 */

class UpdateDeploymentsOrgTask extends MigrationTask
{

    protected $title = "Update Deployment Organization";

    protected $description = "Update the OrgID in Deployment Table with the Member Organizatoin";

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
	    $migration = Migration::get()->filter('Name',$this->title)->first();
        if (!$migration) {
            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
           
            //run migration
            $query = 
            "UPDATE Deployment, (
                SELECT Org.ID AS OrgID, Deployment.ID AS DepID
                FROM Org, Deployment, DeploymentSurvey, Member
                WHERE
                Member.ID = DeploymentSurvey.MemberID AND
                Org.ID = Member.OrgID AND
                Deployment.DeploymentSurveyID = DeploymentSurvey.ID
            ) AS t1
            SET Deployment.OrgID = t1.OrgID
            WHERE t1.DepID = Deployment.ID";
            DB::query($query);
            
            
        }
        else{
            echo "Migration Already Ran! <BR>";
        }
        echo "Migration Done <BR>";
    }

    function down()
    {

    }
}