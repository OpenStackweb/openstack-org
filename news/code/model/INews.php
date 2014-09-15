<?php

/**
 * Interface INews
 */
interface INews extends IEntity {

    /**
     * @return ISubmitter
     */
    public function getSubmitter();

    public function addSubmitter(ISubmitter $submitter);

    /**
     * @return ITag[]
     */
    public function getTags();

    public function addTag(ITag $tag);
} 