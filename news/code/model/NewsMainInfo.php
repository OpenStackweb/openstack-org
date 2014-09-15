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
     * @var BetterImage
     */
    private $image;
    /**
     * @var File
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
     * @param DateTime $date
     * @param string $body
     * @param string $link
     * @param string $image
     * @param string $document
     * @param DateTime $date_embargo
     * @param DateTime $date_expire
     */
    public function __construct($headline,$summary,$date,$body,$link,$image,$document,$date_embargo,$date_expire){
        $this->headline     = $headline;
        $this->summary      = $summary;
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