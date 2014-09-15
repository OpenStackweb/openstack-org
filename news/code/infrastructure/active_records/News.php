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
        'Body' => 'Text',
        'Link' => 'Text',
        'EmbargoDate' => 'Datetime',
        'ExpireDate' => 'Datetime',
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
        $this->Body = $info->getBody();
        $this->Date = $info->getDate();
        $this->Link   = $info->getLink();
        $this->Image   = $info->getImage();
        $this->Document   = $info->getDocument();
        $this->EmbargoDate   = $info->getDateEmbargo();
        $this->ExpireDate = $info->getDateExpire();
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

        $this->addSubmitter($submitter);
    }

    /**
     * @return ISubmitter
     */
    public function getSubmitter()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Submitter')->toArray();
    }

    public function addSubmitter(ISubmitter $submitter)
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

    public function addTag(ITag $tag)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Tags')->add($tag);
    }
}