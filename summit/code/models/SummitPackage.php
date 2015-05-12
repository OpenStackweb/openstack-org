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
        'SummitSponsorPage' => 'SummitSponsorPage'
    );
    
    private static $summary_fields = array(
        'Title',
        'Cost',
        'MaxAvailable',
        'CurrentlyAvailable'
    );
    
    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Title','Title'));
        $fields->add(new TextField('SubTitle','SubTitle'));
        $fields->add(new CurrencyField('Cost','Cost'));
        $fields->add(new NumericField('MaxAvailable','Max. Available'));
        $fields->add(new NumericField('CurrentlyAvailable','Currently Available'));
        $fields->add(new CheckboxField('ShowQuantity','Show Quantities'));
        return $fields;
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
}