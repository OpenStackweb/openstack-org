<?php


class SummitPackage extends DataObject
{

    private static $db = array (
        'Title' => 'Text',
        'SubTitle' => 'Text',        
        'Cost' => 'Currency',
        'MaxAvailable' => 'Int',
        'CurrentlyAvailable' => 'Int',
        'Order' => 'Int',
        'ShowQuantity' => 'Boolean'
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
        return FieldList::create(TabSet::create('Root'))
            ->text('Title')
            ->text('SubTitle')
            ->text('Cost')
            ->checkbox('ShowQuantity','Show Quantities')
            ->text('MaxAvailable')
            ->text('CurrentlyAvailable');
    } 
    
    public function SoldOut() {
        return $this->CurrentlyAvailable == 0;
    }

}