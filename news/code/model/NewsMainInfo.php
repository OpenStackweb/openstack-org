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
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $state;
    /**
     * @var string
     */
    private $country;
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var string
     */
    private $body;
    /**
     * @var string
     */
    private $link;
    /**
     * @var array
     */
    private $image;
    /**
     * @var array
     */
    private $document;
    /**
     * @var DateTime
     */
    private $date_embargo;
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
    private $date_expire;

    /**
     * @param string $headline
     * @param string $summary
     * @param string $city
     * @param string $state
     * @param string $country
     * @param DateTime $date
     * @param string $body
     * @param string $link
     * @param array $image
     * @param array $document
     * @param DateTime $date_embargo
     * @param DateTime $date_expire
     */
    public function __construct($headline,$summary,$city,$state,$country,$date,$body,$link, array $image, array $document,$date_embargo,$date_expire){
        $this->headline     = $headline;
        $this->summary      = $summary;
        $this->city         = $city;
        $this->state        = $state;
        $this->country      = $country;
        $this->date         = $date;
        $this->body         = $body;
        $this->link         = $link;
        $this->image        = $image;
        $this->document     = $document;
        $this->date_embargo = $date_embargo;
        $this->date_expire  = $date_expire;
    }

    public function getHeadline(){
        return $this->headline;
    }

    public function getDate(){
        return $this->date;
    }

    public function getSummary(){
        return $this->summary;
    }

    public function getCity(){
        return $this->city;
    }

    public function getState(){
        return $this->state;
    }

    public function getCountry(){
        return $this->country;
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

    public function getDateEmbargo(){
        return $this->date_embargo;
    }

    public function getDateExpire(){
        return $this->date_expire;
    }
} 