<?php

/**
 * Copyright 2015 OpenStack Foundation
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
 * Class SummitHighlightsPage
 */
final class SummitHighlightsPage extends SummitPage
{
    private static $default_parent = 'SummitHomePage';

    private static $db = array
    (
        'ThankYouText'                => 'HTMLText',
        'NextSummitText'              => 'HTMLText',
        'SuccessTitle'                => 'HTMLText',
        'SuccessAttribution'          => 'Text',
        'SuccessAttributionURL'          => 'Text',
        'AttendanceQty'               => 'Text',
        'CompaniesRepresentedQty'     => 'Text',
        'CountriesRepresentedQty'     => 'Text',
        'SessionsQty'                 => 'Text',
        'ReleaseAnnouncedTitle'       => 'Text',
        'ReleaseAnnouncedDescription' => 'HTMLText',
        'ReleaseAnnouncedButtonTitle' => 'Text',
        'ReleaseAnnouncedButtonLink' => 'Text',
        'CurrentSummitFlickrUrl'      => 'Text',
        'StatisticsVideoUrl'          => 'Text',
        'StatisticsVideoUrl2'         => 'Text',
        'StatisticsVideoUrl3'         => 'Text',
        'StatisticsVideoUrl4'         => 'Text',
    );

    private static $has_many = array
    (
        'KeynotesImages'                  => 'SummitKeynoteHighlight',
        'Pics'                            => 'SummitHighlightPic',
    );

    private static $has_one = array
    (
        'ReleaseAnnouncedImage'           => 'CloudImage',
        'CurrentSummitBackgroundImage'    => 'CloudImage',
        'NextSummitTinyBackgroundImage'   => 'CloudImage',
        'NextSummitBackgroundImage'       => 'SummitImage',
        'StatisticsVideoPoster'           => 'CloudImage',
        'StatisticsVideoPoster'           => 'CloudImage',
        'StatisticsVideo'                 => 'CloudFile',
    );

    public function getStatisticsVideoUrl()
    {
        $video = $this->StatisticsVideo();
        if(!is_null($video) && $video->ID > 0)
            return $video->Link();
        return $this->getField('StatisticsVideoUrl');
    }

    public function getVideoUrls()
    {
        $video_urls = new ArrayList();
        $url = 'https://www.youtube.com/embed/';
        if ($this->StatisticsVideoUrl) {
            $video_urls->push(
                new ArrayData([
                    'Url' => $url.$this->StatisticsVideoUrl
                ])
            );
        }
        if ($this->StatisticsVideoUrl2) {
            $video_urls->push(
                new ArrayData([
                    'Url' => $url.$this->StatisticsVideoUrl2
                ])
            );
        }
        if ($this->StatisticsVideoUrl3) {
            $video_urls->push(
                new ArrayData([
                    'Url' => $url.$this->StatisticsVideoUrl3
                ])
            );
        }
        if ($this->StatisticsVideoUrl4) {
            $video_urls->push(
                new ArrayData([
                    'Url' => $url.$this->StatisticsVideoUrl4
                ])
            );
        }

        return $video_urls;
    }

    function getVideoDescription($id) {
        $videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=".OPENSTACK_YOUTUBE_API_KEY."&fields=items(id,snippet(title),snippet(description))&part=snippet");
        if ($videoTitle) {
            $json = json_decode($videoTitle, true);
            return $json['items'][0]['snippet']['description'];
        } else {
            return false;
        }
    }

    public function getSummitKeynoteHighlightAvailableDays()
    {
        $res =  SummitKeynoteHighlight::getAvailableDays();
        $list = array();
        foreach($res as $key => $val)
        {
            array_push($list, new ArrayData(array('Label' => $val)));
        }
        return new ArrayList($list);
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        //current summit
        $f->removeByName('Content');

        $f->addFieldToTab('Root.CurrentSummit', new HtmlEditorField('ThankYouText', 'ThankYouText'));
        $f->addFieldToTab('Root.CurrentSummit', new TextField('CurrentSummitFlickrUrl', 'Flickr Url'));

        $image = UploadField::create('CurrentSummitBackgroundImage','Background Image');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/highlights/', $this->SummitID));
        $f->addFieldToTab('Root.CurrentSummit', $image);

        // statistics
        $f->addFieldToTab('Root.Statistics', new HtmlEditorField('SuccessTitle', 'Success Stats Title'));
        $f->addFieldToTab('Root.Statistics', new TextField('SuccessAttribution', 'Attribution (appears below title)'));
        $f->addFieldToTab('Root.Statistics', new TextField('SuccessAttributionURL', 'Attribution URL'));
        $f->addFieldToTab('Root.Statistics', new TextField('AttendanceQty', 'Attendance Qty'));
        $f->addFieldToTab('Root.Statistics', new TextField('CompaniesRepresentedQty', 'Companies Represented Qty'));
        $f->addFieldToTab('Root.Statistics', new TextField('CountriesRepresentedQty', 'Countries Represented Qty'));
        $f->addFieldToTab('Root.Statistics', new TextField('SessionsQty', 'Sessions Qty'));
        $f->addFieldToTab('Root.Statistics', new TextField('StatisticsVideoUrl', 'Youtube ID'));
        $f->addFieldToTab('Root.Statistics', new TextField('StatisticsVideoUrl2', 'Youtube ID'));
        $f->addFieldToTab('Root.Statistics', new TextField('StatisticsVideoUrl3', 'Youtube ID'));
        $f->addFieldToTab('Root.Statistics', new TextField('StatisticsVideoUrl4', 'Youtube ID'));

        $file = UploadField::create('StatisticsVideo','Video');
        $file->setAllowedMaxFileNumber(1);
        $file->setAllowedExtensions(array('mp4'));
        $file->setFolderName(sprintf('summits/%s/highlights/statistics', $this->SummitID));
        $f->addFieldToTab('Root.Statistics', $file);

        $image = UploadField::create('StatisticsVideoPoster','Video Poster');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/highlights/statistics', $this->SummitID));
        $f->addFieldToTab('Root.Statistics', $image);

        // next summit
        $f->addFieldToTab('Root.NextSummit', new HtmlEditorField('NextSummitText', 'Next Summit Text'));
        $dropdown = DropdownField::create
        (
            'NextSummitBackgroundImageID',
            'Please choose an image for this page',
            SummitImage::get()->map("ID", "Title", "Please Select")
        )
        ->setEmptyString('(None)');
        $f->addFieldToTab('Root.NextSummit', $dropdown);

        $image = UploadField::create('NextSummitTinyBackgroundImage','Promo Background Image');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/highlights/next_summit/', $this->SummitID));
        $f->addFieldToTab('Root.NextSummit', $image);

        // release
        $f->addFieldToTab('Root.ReleaseAnnounced', new TextField('ReleaseAnnouncedTitle', 'Title'));
        $f->addFieldToTab('Root.ReleaseAnnounced', new HtmlEditorField('ReleaseAnnouncedDescription', 'Description'));
        $f->addFieldToTab('Root.ReleaseAnnounced', new TextField('ReleaseAnnouncedButtonTitle', 'Button Text'));
        $f->addFieldToTab('Root.ReleaseAnnounced', new TextField('ReleaseAnnouncedButtonLink', 'Button Link'));
        $release_image = UploadField::create('ReleaseAnnouncedImage','Image');
        $release_image->setAllowedMaxFileNumber(1);
        $release_image->setFolderName(sprintf('summits/%s/highlights/release/', $this->SummitID));
        $f->addFieldToTab('Root.ReleaseAnnounced', $release_image);

        if($this->ID > 0)
        {
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('KeynotesImages', 'KeynotesImages', $this->KeynotesImages(), $config);
            $f->addFieldToTab('Root.KeyNotesImages', $gridField);

            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Pics', 'Pics', $this->Pics(), $config);
            $f->addFieldToTab('Root.Pics', $gridField);
        }
        return $f;
    }
}

/**
 * Class SummitHighlightsPage_Controller
 */
final class SummitHighlightsPage_Controller extends SummitPage_Controller
{
    public function init()
    {
        parent::init();
        Requirements::css('summit/css/summit.highlights.css');
        Requirements::javascript('themes/openstack/css/bootstrap.lightbox.css');
        Requirements::javascript('themes/openstack/javascript/bootstrap.lightbox.js');
    }

    public function getNextSummit()
    {
        $current = $this->Summit();
        return $current->getNext();
    }
}