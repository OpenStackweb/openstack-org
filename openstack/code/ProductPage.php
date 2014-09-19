<?php
	class ProductPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
		static $has_many = array(
			'Features' => 'Feature'
		);
		
		
		function getCMSFields() {
			$fields = parent::getCMSFields();
			$featureGroup = new GridField('Features','Features',$this->Features());
			$fields->addFieldToTab('Root.Features',$featureGroup);
			return $fields;
		}
				
		
	}

	class ProductPage_Controller extends Page_Controller {

		function init() {
			parent::init();
		}

		
		function FeatureList() {
			$FeatureQuery = "Roadmap = FALSE AND ProductPageID =".$this->ID;
			$FeatureList = Feature::get()->where($FeatureQuery);
			return $FeatureList;
		}
		
		function RoadmapList() {
			$RoadmapQuery = "Roadmap = TRUE AND ProductPageID =".$this->ID;
			$RoadmapList = Feature::get()->where($RoadmapQuery);
			return $RoadmapList;
		}
		
	}