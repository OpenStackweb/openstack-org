<?php

/**
 * Class NewsMainInfo
 */
final class NewsMainInfo {
    /**
     * @var string
     */
    private $headline;
    /**
     * @var string
     */
    private $summary;
    /**
     * @var DateTime
     */
    private $datetime;
    /**
     * @var string
     */
    private $body;
    /**
     * @var string
     */
    private $link;
    /**
     * @var string
     */
    private $image;
    /**
     * @var string
     */
    private $document;
    /**
     * @var DateTime
     */
    private $embargo_date;
    /**
     * @var Integer
     */
    private $rank;
    /**
     * @var Integer
     */
    private $slider;
    /**
     * @var Integer
     */
    private $featured;
    /**
     * @var DateTime
     */
    private $expire_date;

    /**
     * @param string $headline
     * @param string $summary
     * @param Datetime $datetime
     * @param string $body
     * @param string $link
     * @param string $image
     * @param string $document
     * @param Datetime $embargo_date
     * @param Integer $rank
     * @param Integer $slider
     * @param Integer $featured
     * @param Datetime $expire_date
     */
    public function __construct($headline,$summary,$datetime,$body,$link,$image,$document,$embargo_date,$rank,$slider,$featured,$expire_date){
        $this->headline     = $headline;
        $this->summary      = $summary;
        $this->datetime     = $datetime;
        $this->body         = $body;
        $this->link         = $link;
        $this->image        = $image;
        $this->document     = $document;
        $this->embargo_date = $embargo_date;
        $this->rank         = $rank;
        $this->slider       = $slider;
        $this->featured     = $featured;
        $this->expire_date  = $expire_date;
    }

    public function getHeadline(){
        return $this->headline;
    }

    public function getDatetime(){
        return $this->datetime;
    }

    public function getSummary(){
        return $this->summary;
    }

    public function getBody(){
        return $this->body;
    }

    public function getLink(){
        return $this->link;
    }

    public function getImage(){
        return $this->image;
    }

    public function getDocument(){
        return $this->document;
    }

    public function getEmbargoDate(){
        return $this->embargo_date;
    }

    public function getRank(){
        return $this->rank;
    }

    public function getSlider(){
        return $this->slider;
    }

    public function getFeatured(){
        return $this->featured;
    }

    public function getExpireDate(){
        return $this->expire_date;
    }
} 