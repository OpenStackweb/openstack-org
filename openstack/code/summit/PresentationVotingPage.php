<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
/**
 * Used to vote on summit presentations
 */

class PresentationVotingPage extends Page {
  static $db = array(
  );
  static $has_one = array(
  );
  static $defaults = array(
        'ShowInMenus' => false
  );

  // Used to filter searches to only the presentations we want to see
  static $talk_limit_clause = ' AND MarkedToDelete IS NULL';

}
 
class PresentationVotingPage_Controller extends Page_Controller {

    static $allowed_actions = array(
          'SpeakerVotingLoginForm',
          'Presentation',
          'Category',
          'SaveRating',
          'SaveComment',
          'Done',
          'FullPresentationList',
          'ShowFullPresentationList',
          'SearchForm'
    );

    function init() {
      if (!$this->request->param('Action')) $this->redirect($this->Link().'Presentation/');

      parent::init();
        
      Requirements::clear();
      Requirements::javascript('themes/openstack/javascript/jquery.min.js');
      Requirements::javascript('themes/openstack/javascript/bootstrap.min.js');
      Requirements::javascript('themes/openstack/javascript/bootstrap.min.js');
      Requirements::javascript('themes/openstack/javascript/presentationeditor/mousetrap.min.js');
      Requirements::javascript('themes/openstack/javascript/speaker-voting.js');                
        
    }

    function CategoryList() {
      return array(
          array('ID' => 42, 'Name' => 'Enterprise IT Strategies', 'URLSegment' => 'enterprise-it-strategies'),
          array('ID' => 43, 'Name' => 'Telco Strategies', 'URLSegment' => 'telco-strategies'),
          array('ID' => 44, 'Name' => 'How to Contribute', 'URLSegment' => 'how-to-contribute'),
          array('ID' => 45, 'Name' => 'Planning Your OpenStack Project', 'URLSegment' => 'planning-your-openStack-project'),
          array('ID' => 46, 'Name' => 'Products Tools Services', 'URLSegment' => 'products-tools-services'),
          array('ID' => 47, 'Name' => 'User Stories', 'URLSegment' => 'user-stories'),
          array('ID' => 48, 'Name' => 'Community Building', 'URLSegment' => 'community-building'),
          array('ID' => 49, 'Name' => 'Related OSS Projects', 'URLSegment' => 'related-oss-projects'),
          array('ID' => 50, 'Name' => 'Operations', 'URLSegment' => 'operations'),
          array('ID' => 51, 'Name' => 'Cloud Security', 'URLSegment' => 'cloud-security'),
          array('ID' => 52, 'Name' => 'Compute', 'URLSegment' => 'compute'),
          array('ID' => 53, 'Name' => 'Storage', 'URLSegment' => 'storage'),
          array('ID' => 54, 'Name' => 'Networking', 'URLSegment' => 'networking'),
          array('ID' => 55, 'Name' => 'Public & Hybrid Clouds', 'URLSegment' => 'public-and-hybrid-clouds'),
          array('ID' => 56, 'Name' => 'Hands-on Labs', 'URLSegment' => 'hands-on-labs'),
          array('ID' => 57, 'Name' => 'Targeting OpenStack Clouds', 'URLSegment' => 'targeting-openstack-clouds'),
          array('ID' => 58, 'Name' => 'Cloudfunding', 'URLSegment' => 'cloudfunding')
      );
    }

    // Build a page for GCSE
    function FullPresentationList() {
      return Talk::get()->filter('MarkedToDelete',0)->sort('PresentationTitle','ASC');
    }
    
    function LoggedOutPresentationList($catID) {
        
        $SummitID = Summit::CurrentSummit()->ID;
        
        if ($catID) {
        
            $filter = array (
                'MarkedToDelete' => FALSE,
                'SummitID' => $SummitID,
                'SummitCategoryID' => $catID
            );
        
        } else {
        
            $filter = array (
                'MarkedToDelete' => FALSE,
                'SummitID' => $SummitID
            );        
        
        }
        
        
        return Talk::get()->filter($filter);
        
    }
    
    function MemberPresentationList() {
        $member = Member::currentUser();
        $catID = Session::get('CategoryID');
        return $member->getRandomisedPresentations($catID);
    }

    // Render category buttons
    function CategoryLinks() {

      $items = new ArrayList();
      $Categories = $this->CategoryList();

      foreach($Categories as $Category) {
        $items->push( new ArrayData( $Category ) ); 
      }

      return $items;

    }

    function CategoryIDFromURL($CategoryURL) {

      $Categories = $this->CategoryList();

      foreach ($Categories as $key => $val) {
        if ($val['URLSegment'] === $CategoryURL) {
            return $Categories[$key]['ID'];
        }
      }
      return null;
    }

    function Category() {
      $URLSegment = $this->request->param("ID");

      if($URLSegment == 'All') {
        Session::clear('CategoryID');
        $Category = NULL;
      } elseif($URLSegment) {
        $Category = $this->CategoryIDFromURL($URLSegment);
        Session::set('CategoryID',$Category);
      }
        
      $member = Member::currentUser();
    
      if($member) {
        $url = $member->getRandomisedPresentations($Category)->first()->URLSegment;
      } else {
        $url = $this->LoggedOutPresentationList($Category)->first()->URLSegment;
      }
        
          
      $this->redirect($this->Link().'Presentation/'.$url);

    }

    function PresentationByID($ID) {
      // Clean ID to be safe
      $ID = Convert::raw2sql($ID);
      if(is_numeric($ID)) {
        $Presentation = Talk::get()->byID($ID);
        return $Presentation;
      }
    }

    function SearchForm() {
      $SearchForm = new PresentationVotingSearchForm($this, 'SearchForm');
      $SearchForm->disableSecurityToken();
      return $SearchForm;
    }

    function doSearch($data, $form) {

      $Talks = NULL;
        
      $SummitID = Summit::CurrentSummit()->ID;

      if($data['Search'] && strlen($data['Search']) > 1) {
         $query = Convert::raw2sql($data['Search']);

          $sqlQuery = new SQLQuery();
          $sqlQuery->setSelect( array(
            'DISTINCT Talk.URLSegment',
            'Talk.PresentationTitle',
            // IMPORTANT: Needs to be set after other selects to avoid overlays
            'Talk.ClassName',
            'Talk.ClassName',
            'Talk.ID'
          ));
          $sqlQuery->setFrom( array(
            "Talk",
            "left join Talk_Speakers on Talk.ID = Talk_Speakers.TalkID left join Speaker on Talk_Speakers.SpeakerID = Speaker.ID"
          ));
          $sqlQuery->setWhere( array(
            "(Talk.MarkedToDelete IS FALSE) AND (Talk.SummitID = 4) AND ((concat_ws(' ', Speaker.FirstName, Speaker.Surname) like '%$query%') OR (Talk.PresentationTitle like '%$query%') or (Talk.Abstract like '%$query%'))"
          ));
           
          $result = $sqlQuery->execute();
           
          // let Silverstripe work the magic

	      $arrayList = new ArrayList();

	      foreach($result as $rowArray) {
		      // concept: new Product($rowArray)
		      $arrayList->push(new $rowArray['ClassName']($rowArray));
	      }

	      $Talks = $arrayList;

      }
      
      // Clear the category if one was set
      Session::set('CategoryID',NULL);
      $data["SearchMode"] = TRUE;
      if($Talks) $data["SearchResults"] = $Talks;

      return $this->Customise($data);

   }

   function ShowIntro() {
      $MemberID = Member::currentUserID();
      If ($MemberID) {
        $Votes = SpeakerVote::get()->filter('VoterID', $MemberID);
        if(!$Votes && !(Session::get('IntroShown'))) {
          Session::set('IntroShown',TRUE);
          return 'yes';
        }
      } else {
        return 'no';
      }
      return 'no';

   }

    function CurrentVote($TalkID) {
      if(Member::currentUserID()) {
        $SpeakerVote = SpeakerVote::get()->filter(array('VoterID'=>Member::currentUserID(),'TalkID'=>$TalkID))->first();
        if ($SpeakerVote) return $SpeakerVote->VoteValue;
      }
    }

    function RandomPresentationURLSegment($Category = NULL) {

      $Talk = NULL;
      $CategoryID = Session::get('CategoryID');
      $currentMemberID = Member::currentUserID();
        
      $SummitID = Summit::CurrentSummit()->ID;


      // Set up a filter to not display any presentations that have already recieved votes for a logged in member
      $CurrentUserJoin = NULL;
      if($currentMemberID) $CurrentUserJoin = " AND SpeakerVote.VoterID = ".Member::currentUserID();

      $CurrentUserWhere = NULL;
      if($currentMemberID) $CurrentUserWhere = " `SpeakerVote`.VoteValue IS NULL";

      if(!$CategoryID) {

        if($currentMemberID) {
	      $Talks = Talk::get()->filter(array('MarkedToDelete'=>0, 'SummitID' => $SummitID))->sort('RAND()')->leftJoin('SpeakerVote',"(Talk.ID = SpeakerVote.TalkID" . $CurrentUserJoin . ")");
	        if(!empty($CurrentUserWhere))
		        $Talks->where($CurrentUserWhere);
        } else {
	        $Talks = Talk::get()->filter(array('MarkedToDelete'=>0, 'SummitID' => $SummitID))->sort('RAND()');
        }

        if($Talks) $Talk = $Talks->first();

      } else {

        if($currentMemberID) {
	        $Talks = Talk::get()->filter(array('MarkedToDelete'=>0, 'SummitID' => $SummitID, 'SummitCategoryID'=>$CategoryID))->leftJoin('SpeakerVote',"(Talk.ID = SpeakerVote.TalkID" . $CurrentUserJoin . ")")->sort('RAND()');
	        if(!empty($CurrentUserWhere))
		        $Talks->where($CurrentUserWhere);
        } else {
	        $Talks = Talk::get()->filter(array('MarkedToDelete'=>0, 'SummitID' => $SummitID, 'SummitCategoryID'=>$CategoryID))->sort('RAND()');
        }

        if($Talks) $Talk = $Talks->first();
      }

      if($Talk) {
        return $Talk->URLSegment;
      } else {
        return 'none';
      }

    }
    
    function Done() {

      $Member = Member::currentUser();

      if($Member) {
          
          $data = array();

          $CategoryID = Session::get('CategoryID');
          if(is_numeric($CategoryID)) $Category = SummitCategory::get()->byID($CategoryID);
          if(isset($Category)) $data["CategoryName"] = $Category->Name;

          $Subject = 'Voting Event';


          if(isset($Category)) {
            $Body = $Member->FirstName . ' ' . $Member->Surname . ' just completed voting for all presentations in the category ' . $Category->Name;
          } else {
            $Body = $Member->FirstName . ' ' . $Member->Surname . ' just completed voting for every single presentation listed!';
          }

          $email = EmailFactory::getInstance()->buildEmail(PRESENTATION_VOTING_EVENT_FROM_EMAIL, PRESENTATION_VOTING_EVENT_TO_EMAIL, $Subject, $Body);
          $email->send();

          //return our $Data to use on the page
          return $this->Customise($data);
      }

    }    

    function PresentationByURLSegment($URLSegment) {
        
    
     $SummitID = Summit::CurrentSummit()->ID;
    
      // Clean ID to be safe
      $URLSegment = Convert::raw2sql($URLSegment);
      // Look up a specific presentation
      $Presentation = Talk::get()->filter(array('URLSegment'=>$URLSegment,'SummitID'=>$SummitID))->first();
      return $Presentation;
    }        

    // Used as a URL action to display a presentation
    function Presentation() {
      $URLSegment = $this->request->param("ID");

      if($URLSegment == 'none') {
        $this->redirect($this->Link().'Done');
        return;
      }

      if($URLSegment) {
        $Talk = $this->PresentationByURLSegment($URLSegment);

        if($Talk && $Talk->MainTopic != Session::get('Category')) Session::clear('Category');

      } else {
        $CategoryID = Session::get('CategoryID');        
        $this->redirect($this->Link().'Presentation/'.$this->RandomPresentationURLSegment($CategoryID));
        return;
      }

      if($Talk) {
        $data["Presentation"] = $Talk;
        $data["VoteValue"] = $this->CurrentVote($Talk->ID);
        
        $CategoryID = Session::get('CategoryID');
        $data["CategoryID"] = $CategoryID;
        
          if(is_numeric($CategoryID)) $Category = SummitCategory::get()->byID($CategoryID);
        if(isset($Category)) $data["CategoryName"] = $Category->Name;

        //return our $Data to use on the page
        return $this->Customise($data);
      } else {
        //Talk not found
        return $this->httpError(404, 'Sorry that talk could not be found');
      }
    }

    function ClientIP() {
      $inSSL = ( isset($_SERVER['SSL']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ) ? true : false;
      if($inSSL) {
        $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $clientIP = $_SERVER['REMOTE_ADDR'];
      }
      return $clientIP;
    }

    function SpeakerVotingLoginForm() {
      $URLSegment = $this->request->param("ID");    
      Session::set('BackURL',$this->Link().'/Presentation/'.$URLSegment);
      $SpeakerVotingLoginForm = new SpeakerVotingLoginForm($this, 'SpeakerVotingLoginForm');
      return $SpeakerVotingLoginForm;
    }


    function SaveRating() {

      if(!Member::currentUserID()) {
        return $this->httpError(403, 'You need to be logged in to perform this action.');
      }

      $rating = '';
      $TalkID = '';

      if(isset($_GET['rating']) && is_numeric($_GET['rating'])) {
        $rating = $_GET['rating'];
      }

      if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $TalkID = $_GET['id'];
      }

      $Member = member::currentUser();

      $validRatings = array(-1,0,1,2,3);

      if($Member && isset($rating) && (in_array((int)$rating, $validRatings, true)) && $TalkID) {

        $previousVote = SpeakerVote::get()->filter(array('TalkID'=>$TalkID,'VoterID'=>$Member->ID))->first();
          
        $talk = Talk::get()->byID($TalkID);
        $CategoryID = Session::get('CategoryID');

        if(!$previousVote) {
          $speakerVote = new SpeakerVote;
          $speakerVote->TalkID = $TalkID;
          $speakerVote->VoteValue = $rating;
          $speakerVote->IP = $this->ClientIP();
          $speakerVote->VoterID = $Member->ID;
          $speakerVote->write();
          
          $this->redirect($this->Link().'Presentation/'.$talk->getNextMemberPresentation($CategoryID)->URLSegment);
    
        } else {
          $previousVote->VoteValue = $rating;
          $previousVote->IP = $this->ClientIP();
          $previousVote->write();

          $this->redirect($this->Link().'Presentation/'.$talk->getNextMemberPresentation($CategoryID)->URLSegment);

        }
        
      } else {
        return 'no rating saved.';
      }
    }

    function SaveComment($data) {

      if(!Member::currentUserID()) {
        return $this->httpError(403, 'You need to be logged in to perform this action.');
      }

      $VarsPassed = $data->requestVars();
      $comment = Convert::raw2sql($VarsPassed['comment']);
      $TalkID = Convert::raw2sql($VarsPassed['submission']);
      $Member = member::currentUser();

      if($Member) {
        $previousVote = SpeakerVote::get()->filter(array('TalkID'=>$TalkID,'VoterID'=>$Member->ID))->first();
        if(!$previousVote) {
          $speakerVote = new SpeakerVote;
          $speakerVote->TalkID = $TalkID;
          $speakerVote->Note = $comment;
          $speakerVote->IP = $this->ClientIP();
          $speakerVote->VoterID = $Member->ID;
          $speakerVote->write();
          return $VarsPassed["comment"];
        } else {
          $previousVote->Note = $comment;
          $previousVote->IP = $this->ClientIP();
          $previousVote->write();
          return $VarsPassed["comment"];
        }
        
      } else {
        return false;
      }
    }

}
