<?php

/**
 * Class SummitPackage
 */
class SummitPackage
    extends DataObject
    implements ISummitPackage
{

    private static $db = array (
        'Title'              => 'Text',
        'SubTitle'           => 'Text',
        'Cost'               => 'Currency',
        'MaxAvailable'       => 'Int',
        'CurrentlyAvailable' => 'Int',
        'Order'              => 'Int',
        'ShowQuantity'       => 'Boolean'
    );

    private static $defaults = array(
        'ShowQuantity' => TRUE
    ); 
    
    private static $has_one = array (
        "Summit" => "Summit"
    );

    /**
     * @var array
     */
    private static $many_many = array
    (
        'DiscountPackages' => 'SummitPackage',
    );

    private static $many_many_extraFields = array(
        'DiscountPackages' => array(
            'Discount' => "Percentage"
        ),
    );
    
    private static $summary_fields = array(
        'Title',
        'Cost',
        'MaxAvailable',
        'CurrentlyAvailable'
    );
    
    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new DropdownField('SummitID','Summit', Summit::get()->map("ID","Title")));
        $fields->add(new TextField('Title','Title'));
        $fields->add(new TextField('SubTitle','SubTitle'));
        $fields->add(new CurrencyField('Cost','Cost'));
        $fields->add(new NumericField('MaxAvailable','Max. Available'));
        $fields->add(new NumericField('CurrentlyAvailable','Currently Available'));
        $fields->add(new CheckboxField('ShowQuantity','Show Quantities'));
        if($this->ID > 0){
            $config = GridFieldConfig_RelationEditor::create(25);
            $config->removeComponentsByType(new GridFieldDetailForm());
            $editconf = new GridFieldDetailForm();
            $editconf->setFields(FieldList::create(
                ReadonlyField::create('SummitID','Summit'),
                ReadonlyField::create('Title','Title'),
                TextField::create('ManyMany[Discount]', 'Discount ( Number between 0.00 and 1.00 )')
            ));
            $config->addComponent($editconf);
            $discounts_packages = new GridField('DiscountPackages', 'Discount Packages', $this->DiscountPackages(), $config);
            $fields->add($discounts_packages);
        }
        return $fields;
    }

    public static $validation_enabled = true;

    protected function validate()
    {
        if(!self::$validation_enabled) return ValidationResult::create();

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        if($this->SummitID == 0){
            return $valid->error('Summit is required!');
        }

        $name = trim($this->Title);
        if (empty($name)) {
            return $valid->error('Title is required!');
        }


        return $valid;
    }

    public function SoldOut() {
        return $this->CurrentlyAvailable == 0;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @throws EntityValidationException
     * @return void
     */
    public function sell()
    {
        if($this->SoldOut())
            throw new EntityValidationException(EntityValidationException::buildMessage('Sold Out'));
        $this->CurrentlyAvailable -= 1;
    }

    /**
     * @param int $page_id
     * @return bool
     */
    public function isParentPage($page_id)
    {
       return intval($page_id) === intval($this->SummitSponsorPageID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $res      = $this->getField('Title');
        $subtitle = $this->getField('SubTitle');
        if(!empty($subtitle)){
            $res .= ' - '.$subtitle;
        }
        return $res;
    }

    /**
     * @return string
     */
    public function getSummitName()
    {
        return $this->Summit()->Title;
    }

    public function getReducedCost(){
        $discount     = (float)$this->Discount;
        $cost         = (float)$this->Cost;
        $discount_val =  ($cost * $discount);
        $c = new Currency('Reduced');
        $c->setValue($cost - $discount_val);
        return $c;
    }
}