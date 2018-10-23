<?php

namespace OpenStack\AUC;

use \Member;

/**
 * Class Result
 * @package OpenStack\AUC
 */
class Result extends SS_Object
{

    /**
     * @var Member
     */
    protected $member;


    /**
     * @var null
     */
    protected $value;


    /**
     * Result constructor.
     * @param Member $member
     * @param null $value
     */
    public function __construct(Member $member, $value = null)
    {
        $this->member = $member;
        $this->value = $value;

        parent::__construct();
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

}