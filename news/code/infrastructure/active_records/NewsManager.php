<?php

/**
 * Class NewsManager
 */
final class NewsManager extends DataObjectDecorator implements INewsManager {

    /**
     * @return int
     */
    public function getIdentifier()
    {

    }

    /**
     * @return bool
     */
    public function isNewsManager()
    {
        return $this->owner->inGroup('news-managers');
    }
}