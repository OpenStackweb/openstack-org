<?php

/**
 * Class MarketPlaceDriverPage
 */
final class MarketPlaceDriverPage extends MarketPlaceDirectoryPage {

}

/**
 * Class MarketPlaceDriverPage_Controller
 */
final class MarketPlaceDriverPage_Controller extends MarketPlaceDirectoryPage_Controller {


  function init() {
    parent::init();
    Requirements::customScript("jQuery(document).ready(function($) {
            $('#drivers','.marketplace-nav').addClass('current');
        });");
  }

  public static function DriverTable(){

    $url = 'http://stackalytics.com/driverlog/api/1.0/drivers';
    $jsonResponse = @file_get_contents($url);

    $driverArray = json_decode($jsonResponse, true); // passing true tells php to make the whole thing an array (not array of objects)
    
    // creating a new empty DataObjectSet that we'll populate with each row of the table
    $tableEntries = new ArrayList();

    // loop over the array created by json_decode to add each table row to $tableEntries
    $array = $driverArray['drivers'];
    foreach ($array as $driver => $contents) {

      // Name and metadata
      isset($contents['name']) == TRUE ? $name = $contents['name'] : $name = '';
      isset($contents['description']) == TRUE ? $description = $contents['description'] : $description = '';
      isset($contents['vendor']) == TRUE ? $vendor = $contents['vendor'] : $vendor = '';
      isset($contents['project_name']) == TRUE ? $project = $contents['project_name'] : $project = '';
      isset($contents['wiki']) == TRUE ? $wiki = $contents['wiki'] : $wiki = '';

      // Creating a nested DataObject of each supported release and it's name and URL
      $releasesDataObjectSet = new ArrayList();

      if (isset($contents['releases_info'])) {
        $releases = $contents['releases_info'];
        foreach ($releases as $release) {
          $releaseEntry = new ArrayData(array(
            'Name' => $release['name'],
            'Url' => $release['wiki']
          ));
          $releasesDataObjectSet->push($releaseEntry);
        }
      }

      // each row of the table is an ArrayData object
      $tableEnty = new ArrayData(array(
        'Project' => $project,
        'Name' => $name,
        'Description' => $description,
        'Vendor' => $vendor,
        'Project' => $project,
        'Url' => $wiki,
        'Releases' => $releasesDataObjectSet
      ));

      // add row to tableEntries DO
      $tableEntries->push($tableEnty);

    }

    return $tableEntries;

  }

}