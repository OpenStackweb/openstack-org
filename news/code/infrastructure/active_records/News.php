<?php

/**
 * Class News
 */
final class News extends DataObject implements INews {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Date'  => 'Datetime',
		'Headline' => 'Text',
        'Summary' => 'Text',
        'City' => 'Text',
        'State' => 'Text',
        'Country' => 'Text',
        'Body' => 'Text',
        'Link' => 'Text',
        'DateEmbargo' => 'Datetime',
        'DateExpire' => 'Datetime',
        'Rank' => 'Int',
        'Featured' => 'Boolean',
        'Slider' => 'Boolean',
        'Approved' => 'Boolean',
	);

    static $has_one = array(
        'Submitter' => 'Submitter',
        'Document' => 'File',
        'Image' => 'BetterImage',
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
        $this->City   = $info->getCity();
        $this->State   = $info->getState();
        $this->Country   = $info->getCountry();
        $this->Body = $info->getBody();
        $this->Date = $info->getDate();
        $this->Link   = $info->getLink();
        $this->DateEmbargo   = $info->getDateEmbargo();
        $this->DateExpire = $info->getDateExpire();
    }

    /**
     * @param string[] $tags
     * @return void
     */
    public function registerTags($tags)
    {
        $tags = explode(',',$tags);

        foreach ($tags as $tag_name) {
            $tag = new Tag();
            $tag->Tag = $tag_name;
            $tag->write();
            $this->addTag($tag);
        }
    }

    /**
     * @param NewsSubmitter $submitter
     * @return void
     */
    public function registerSubmitter(NewsSubmitter $info)
    {

        $submitter = new Submitter();
        $submitter->FirstName = $info->getFirstName();
        $submitter->LastName = $info->getLastName();
        $submitter->Email = $info->getEmail();
        $submitter->Company = $info->getCompany();
        $submitter->Phone = $info->getPhone();

        $this->setSubmitter($submitter);
    }

    /**
     * @return ISubmitter
     */
    public function getSubmitter()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Submitter')->getTarget();
    }

    public function setSubmitter(ISubmitter $submitter)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Submitter')->setTarget($submitter);
    }

    /**
     * @return ITag[]
     */
    public function getTags()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Tags')->toArray();
    }

    /**
     * @return string
     */
    public function getTagsCSV()
    {
        $tags =  $this->getTags();
        $tags_csv = '';
        foreach ($tags as $tag) {
            $tags_csv .= $tag->Tag.',';
        }

        return trim($tags_csv, ",");
    }


    public function addTag(ITag $tag)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Tags')->add($tag);
    }

    public function clearTags()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Tags')->removeAll();
    }

	/**
	 * @param IFileUploadService $upload_service
	 */
	public function registerImage(IFileUploadService $upload_service)
	{
		$upload_service->setFolderName('news-images');
		$image = $upload_service->upload('Image', $this);
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'Image')->setTarget($image);
	}

    /**
     * @param IFileUploadService $upload_service
     */
    public function registerDocument(IFileUploadService $upload_service)
    {
        $upload_service->setFolderName('news-documents');
        $document = $upload_service->upload('Document', $this);
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Document')->setTarget($document);
    }

    public function registerSection($section)
    {
        $slider = $featured = $approved = 0;
        if ($section == 'slider') {
            $slider = 1;
            $approved = 1;
        } elseif ($section == 'featured') {
            $featured = 1;
            $approved = 1;
        } elseif ($section == 'recent') {
            $approved = 1;
        }

        $this->Featured = $featured;
        $this->Slider = $slider;
        $this->Approved = $approved;

    }

    public function registerRank($rank)
    {

        $this->Rank = $rank;
    }
}