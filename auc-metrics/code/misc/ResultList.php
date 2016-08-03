<?php

namespace OpenStack\AUC;

use \ArrayList;
use \InvalidArgumentException;
use \Member;

/**
 * Class ResultList
 * @package OpenStack\AUC
 */
class ResultList extends ArrayList
{

    /**
     * @param array|object $item
     */
    public function push($item)
    {
        if (!$item instanceof Result) {
            throw new InvalidArgumentException("OpenStack\AUC\ResultList::push() accepts only OpenStack\AUC\Result objects");
        }

        parent::push($item);
    }

    /**
     * @return array
     */
    public function getMemberIDs()
    {
        $ids = array_map(function ($item) {
            return $item->getMember()->ID;
        }, $this->toArray());

        return $ids;
    }

    /**
     * @return bool|\DataList
     */
    public function getMemberList()
    {
        $ids = $this->getMemberIDs();

        if (count($ids)) {
            return Member::get()->byIDs($this->getMemberIDs());
        }

        return false;
    }


    /**
     * @param Member $member
     * @return bool
     */
    public function getValueForMember(Member $member)
    {
        foreach ($this as $item) {
            if ($item->getMember()->ID == $member->ID) {
                return $item->getValue();
            }
        }

        return false;
    }


}