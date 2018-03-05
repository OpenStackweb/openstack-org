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
 * Class ElectionManager
 */
final class ElectionManager implements IElectionManager {

	/**
	 * @var IEntityRepository
	 */
	private $election_repository;

	/**
	 * @var
	 */
	private $foundation_member_repository;

	/**
	 * @var IEntityRepository
	 */
	private $vote_repository;

	/**
	 * @var IVoterFileRepository
	 */
	private $voter_file_repository;

	/**
	 * @var IVoteFactory
	 */
	private $vote_factory;

	/**
	 * @var IVoterFileFactory
	 */
	private $voter_file_factory;

	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	/**
	 * @var IElectionFactory
	 */
	private $election_factory;

	/**
	 * @param IEntityRepository    $election_repository
	 * @param IEntityRepository    $foundation_member_repository
	 * @param IEntityRepository    $vote_repository
	 * @param IVoterFileRepository $voter_file_repository
	 * @param IVoteFactory         $vote_factory
	 * @param IVoterFileFactory    $voter_file_factory
	 * @param IElectionFactory     $election_factory
	 * @param ITransactionManager  $tx_manager
	 */
	public function __construct(IEntityRepository    $election_repository,
	                            IEntityRepository    $foundation_member_repository,
								IEntityRepository    $vote_repository,
								IVoterFileRepository $voter_file_repository,
	                            IVoteFactory         $vote_factory,
	                            IVoterFileFactory    $voter_file_factory,
								IElectionFactory     $election_factory,
								ITransactionManager   $tx_manager){

		$this->election_repository          = $election_repository;
		$this->foundation_member_repository = $foundation_member_repository;
		$this->vote_repository              = $vote_repository;
		$this->voter_file_repository        = $voter_file_repository;
		$this->voter_file_factory           = $voter_file_factory;
		$this->vote_factory                 = $vote_factory;
		$this->election_factory             = $election_factory;
		$this->tx_manager                   = $tx_manager;
	}


	/**
	 * @param string   $filename
	 * @param int      $election_id
	 * @return array
	 */
	public function ingestVotersForElection($filename, $election_id){


		return $this->tx_manager->transaction(function() use ($filename, $election_id){
            $output = '';
			if($this->voter_file_repository->getByFileName($filename))
				throw new EntityAlreadyExistsException('ElectionVoterFile',sprintf('filename = %s',$filename));


			$election =  $this->election_repository->getById($election_id);
			if(is_null($election)){
				throw new NotFoundEntityException('Election');
			}

			if($election->VoterFileID > 0){
			    throw new EntityValidationException("Election already processed a voter file!");
            }

			$reader        = new CSVReader($filename);
			$line          = false;
			$header        = $reader->getLine();
            $first_name    = $header[1];
            $last_name     = $header[2];
            $voter_id      = $header[3];

            if(!(strtolower(trim($first_name)) == 'first name'
                && strtolower(trim($last_name)) == 'last name'
                && strtolower(trim($voter_id)) == 'voter id'))
                throw new EntityValidationException("csv file format is incorrect!");

			$count         = 0;
			$not_processed = [];
            $already_voted = [];
			while($line = $reader->getLine()){

                $first_name        = $line[1];
				$last_name         = $line[2];
				$member_id         = (int)$line[3];
				$members_2_process = [];
				$member            = $this->foundation_member_repository->getById($member_id);

                sprintf("processing member id %s - first_name %s - last_name %s", $member_id, $first_name, $last_name).PHP_EOL;
                if(is_null($member)){
                    $output .= sprintf("cant find member by id %s. trying by first_name (%s) - last_name (%s)", $member_id, $first_name, $last_name).PHP_EOL;
                    // possible returns a list (array)
                    $members_2_process = $this->foundation_member_repository->getByCompleteName($first_name, $last_name);
                }
                else $members_2_process[] = $member;

                if(count($members_2_process) == 0)
                {
                    $output .= sprintf("cant find matches for member id %s - first_name %s - last_name %s on db. skipping it", $member_id, $first_name, $last_name).PHP_EOL;
                    $not_processed[] = ['id' => $member_id, 'first_name' => $first_name, 'last_name' => $last_name ];
                    continue;
                }

                foreach($members_2_process as $member_2_process) {

                    if (!$member_2_process->isFoundationMember()) {
                        $output .= sprintf("member id %s - first_name %s - last_name %s is not foundation member. skipping it ...", $member_id, $first_name, $last_name).PHP_EOL;
                        $not_processed[] = ['id' => $member_id, 'first_name' => $first_name, 'last_name' => $last_name];
                        continue;
                    }

                    if(in_array($member_2_process->ID, $already_voted)){
                        $output .= sprintf("member id %s - first_name %s - last_name %s already voted as member id %s", $member_id, $first_name, $last_name, $member_2_process->ID).PHP_EOL;
                        $not_processed[] = ['id' => $member_id, 'first_name' => $first_name, 'last_name' => $last_name];
                        continue;
                    }

                    $output .= sprintf("processed member id %s - first_name %s - last_name %s OK", $member_id, $first_name, $last_name).PHP_EOL;
                    $vote = $this->vote_factory->buildVote($election, $member_2_process);
                    $vote->write();
                    $already_voted[] = $member_2_process->ID;
                    $count++;
                }
         	}

			$voter_file = $this->voter_file_factory->build($filename);
			$voter_file->write();

			$election->VoterFileID = $voter_file->ID;
            $election->write();

			return [$output, $count, $not_processed];
		});
	}

    /**
     * @param int $member_id
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return bool
     */
	public function isValidNomination($member_id){
        return $this->tx_manager->transaction(function() use($member_id){
            $current_member  = Member::currentUser();
            if (is_null($current_member) || !$current_member->isFoundationMember()) {
                throw new EntityValidationException("INVALID VOTER");
            }

            $current_election = Election::getCurrent();

            if(is_null($current_election))
                throw new NotFoundEntityException("NO ACTIVE NOMINATIONS");

            if  (!$current_election->NominationsAreOpen()) {
                throw new EntityValidationException("NO ACTIVE NOMINATIONS");
            }

            $foundation_member = $this->foundation_member_repository->getById($member_id);
            if(is_null($foundation_member)){
                throw new NotFoundEntityException("INVALID CANDIDATE");
            }

            if ( !$foundation_member->isFoundationMember()) {
                throw new EntityValidationException("INVALID CANDIDATE");
            }


            if ($current_election->isNominated($foundation_member)) {
                throw new EntityValidationException("ALREADY NOMINATED");
            }

            $nominations = $current_election->getNominationsFor($foundation_member);


            if ($nominations->count() >= 10) {
                throw new EntityValidationException("LIMIT EXCEEDED");
            }
            return true;
        });
    }
    /**
     * @param int $member_id
     * @param IMessageSenderService $nomination_email_sender
     * @return CandidateNomination
     */
	public function nominateMemberOnCurrentElection($member_id, IMessageSenderService $nomination_email_sender){
	    return $this->tx_manager->transaction(function() use($member_id, $nomination_email_sender){

            $current_election = Election::getCurrent();

            $this->isValidNomination($member_id);

            $nomination              = new CandidateNomination();
            $nomination->MemberID    = Member::currentUserID();
            $nomination->CandidateID = $member_id;
            $nomination->ElectionID  = $current_election->ID;
            $nomination->write();

            $candidate = Candidate::get()->filter([
                'MemberID'   => $member_id,
                'ElectionID' => $current_election->ID
            ])->first();

            if (is_null($candidate)) {

                $candidate             = new Candidate();
                $candidate->MemberID   = $member_id;
                $candidate->ElectionID = $current_election->ID;

                $candidate->write();
            }

            $nomination_email_sender->send([
               'Candidate'       => $candidate,
               'CurrentElection' => $current_election
            ]);

            return $nomination;
        });
    }

    /**
     * @param Member $member
     * @param Election $election
     * @param array $data
     * @return Candidate
     * @throws EntityValidationException
     */
    public function registerCandidate(Member $member, Election $election, array $data){
	    return $this->tx_manager->transaction(function () use($member, $election, $data){

            if (!$member->isFoundationMember()) {
                throw new EntityValidationException("INVALID CANDIDATE");
            }

            $candidate = Candidate::get()->filter([
                'MemberID'   => $member->ID,
                'ElectionID' => $election->ID
            ])->first();

            if(!$election->NominationsAreOpen() && !is_null($candidate) && !$candidate->IsGoldMemberCandidate){
                throw new EntityValidationException("ELECTION NOMINATIONS CLOSED");
            }

            if(!$election->isOpen()){
                throw new EntityValidationException('ELECTION CLOSED');
            }

            if(is_null($candidate)){
                $candidate = new Candidate();
                $candidate->MemberID = $member->ID;
                $candidate->ElectionID = $election->ID;
            }

            $config = HTMLPurifier_Config::createDefault();

            // Remove any CSS or inline styles
            $config->set('CSS.AllowedProperties', []);
            $purifier = new HTMLPurifier($config);

            // Clean Bio field
            if ($data["Bio"]) {
                $member->Bio = $purifier->purify($data["Bio"]);
                $member->write();
            }

            // Clean RelationshipToOpenStack field
            if ($toClean = $data["RelationshipToOpenStack"]) {
                $candidate->RelationshipToOpenStack = $purifier->purify($toClean);
            }

            // Clean Experience field
            if ($toClean = $data["Experience"]) {
                $candidate->Experience = $purifier->purify($toClean);
            }

            // Clean BoardsRole field
            if ($toClean = $data["BoardsRole"]) {
                $candidate->BoardsRole = $purifier->purify($toClean);
            }

            // Clean HasAcceptedNomination field
            if ($toClean = $data["TopPriority"]) {
                $candidate->TopPriority = $purifier->purify($toClean);
            }

            if (
                (strlen($data['Bio'])) < 4 ||
                (strlen($data['RelationshipToOpenStack'])) < 4 ||
                (strlen($data['Experience'])) < 4 ||
                (strlen($data['BoardsRole'])) < 4 ||
                (strlen($data['TopPriority'])) < 4

            )
            {

                $candidate->HasAcceptedNomination = false;
            }
            else {
                $candidate->HasAcceptedNomination = true;
            }

            $candidate->write();

            return $candidate;

        });
    }
} 