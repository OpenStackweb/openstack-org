<?php

/**
 * Class EntityRegularStepForm
 */
class EntityRegularStepForm extends RegularStepForm
{
    /**
     * @return ISurvey
     */
    public function getSurvey(){
        return $this->step->survey();
    }

    /**
     * @return bool
     */
    public function AllowTeams(){
        $entity_survey = $this->getSurvey();
        return $entity_survey->isTeamEditionAllowed() &&
        $entity_survey->createdBy()->getIdentifier() === Member::currentUserID() &&
        $entity_survey->isFirstStep();// only show on first step
    }

    public function TeamMembers(){
        return new ArrayList($this->getSurvey()->getTeamMembers());
    }

    /**
     * @return int
     */
    public function CurrentStepIndex(){
        return $this->getSurvey()->getCurrentStepIndexNice();
    }

    /**
     * @return int
     */
    public function MaxStepIndex(){
        return $this->getSurvey()->getStepsCount();
    }
}