<?php


/**
 * Class Training2MarketplaceMigrationTask
 * Migrates TrainingProgram to new Marketplace Structure
 */
class Training2MarketplaceMigrationTask extends MigrationTask {

	protected $title = "Training Migration";

	protected $description = "Migrate all training programs to new marketplace structure";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
		$migration = Migration::get()->filter('Name',$this->title)->first();
		if (!$migration) {

			$programs = DB::query('SELECT P.*,C.CompanyID from TrainingProgram P INNER JOIN Contract C on C.ID = P.TrainingContractID;');

			$service  = new TrainingManager(new SapphireTrainingServiceRepository,
										    new SapphireMarketPlaceTypeRepository,
										    new MarketPlaceTypeAddPolicyStub,
											null,
											new CacheServiceStub,
											new MarketplaceFactory,
											SapphireTransactionManager::getInstance());

			$factory = new TrainingFactory;
			foreach ($programs as $program) {
				$company_id = (int)$program['CompanyID'];
				$company    = Company::get()->byID($company_id);
				$program_id = (int) $program['ID'];

				$training = $factory->buildTraining($program['Name'], $program['Description'],true,$company);
				$training->CompanyID = $company_id;
				$training->write();
				//get former courses and associate it with new entity
			    $courses = TrainingCourse::get()->filter('ProgramID',$program_id);
				if($courses && count($courses) > 0){
					foreach($courses  as $course){
						$course->TrainingServiceID = $training->getIdentifier();
						$course->write();
					}
				}
			}

			//db alter
			DB::query('DROP TABLE Training;');
			DB::query('ALTER TABLE `TrainingCourse` DROP COLUMN ProgramID;');
			DB::query('DROP TABLE TrainingProgram;');
			$new_training_group_slug = ITraining::MarketPlaceGroupSlug;
			DB::query("
			UPDATE `Company_Administrators`
			SET GroupID = (SELECT ID FROM `Group` WHERE code='{$new_training_group_slug}' LIMIT 1 )
			WHERE ID IN (
				SELECT tmp.id FROM
				(
					SELECT ID FROM `Company_Administrators`
					WHERE GroupID IN ( SELECT ID FROM `Group` where code='training-admins')
				) as tmp
			);
			");
			DB::query("
			DELETE Permission
			FROM Permission
			WHERE Code='MANAGE_COMPANY_TRAINING';
			");
			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}

	function down(){

	}
} 