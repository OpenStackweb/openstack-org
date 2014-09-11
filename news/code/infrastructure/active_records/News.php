<?php

/**
 * Class News
 */
final class News extends DataObject implements INews {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'DateTime'  => 'Datetime',
		'Headline' => 'Text',
        'Summary' => 'Text',
        'Body' => 'Text',
        'Link' => 'Text',
        'Image' => 'Text',
        'Document' => 'Text',
        'EmbargoDate' => 'Datetime',
        'ExpireDate' => 'Datetime',
        'Rank' => 'Int',
        'Featured' => 'Boolean',
        'Slider' => 'Boolean',
        'Approved' => 'Boolean',
	);

    static $has_one = array(
        'Submitter' => 'Submitter',
    );

    static $many_many = array(
        'Tags' => 'Tag',
    );

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

    public function formatDate() {
        return date('M d, g:i a',strtotime($this->DateTime));
    }

    /**
     * @param NewsMainInfo $info
     * @return void
     */
    function registerMainInfo(NewsMainInfo $info)
    {
        $this->Headline = $info->getHeadline();
        $this->Summary   = $info->getSummary();
        $this->Body = $info->getBody();
        $this->DateTime = $info->getDatetime();
        $this->Link   = $info->getLink();
        $this->Image = $info->getImage();
        $this->Document = $info->getDocument();
        $this->EmbargoDate   = $info->getEmbargoDate();
        $this->Rank = $info->getRank();
        $this->Slider = $info->getSlider();
        $this->Featured   = $info->getFeatured();
        $this->ExpireDate = $info->getExpireDate();
    }

    /**
     * @param string[] $tags
     * @return void
     */
    public function registerTags($tags)
    {
        foreach ($tags as $tag_name) {
            $tag = new Tag();
            $tag->Tag = $tag_name;
            $this->Tags->push($tag);
        }
    }

    /**
     * @param integer $submitter
     * @return void
     */
    public function registerSubmitter($submitter)
    {
        //get submitter by id and add it to the News object
    }
}