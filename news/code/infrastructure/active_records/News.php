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
 * Class News
 */
final class News extends DataObject implements INews
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
        'Date' => 'Datetime',
        'Headline' => 'Text',
        'Summary' => 'Text',
        'SummaryHtmlFree' => 'Text',
        'City' => 'Text',
        'State' => 'Text',
        'Country' => 'Text',
        'Body' => 'Text',
        'BodyHtmlFree' => 'Text',
        'Link' => 'Text',
        'DateEmbargo' => 'Datetime',
        'DateExpire' => 'Datetime',
        'Rank' => 'Int',
        'Featured' => 'Boolean',
        'Slider' => 'Boolean',
        'Approved' => 'Boolean',
        'IsLandscape' => 'Boolean',
        'Archived' => 'Boolean',
        'Restored' => 'Boolean',
        'Deleted' => 'Boolean'
    );

    private static $defaults = array
    (
        'Headline' => '',
        'Summary'  => '',
    );

    static $has_one = array(
        'Submitter' => 'Submitter',
        'Document' => 'File',
        'Image' => 'BetterImage',
    );

    static $many_many = array(
        'Tags' => 'NewsTag',
    );

    public static $indexes = array(
      /*  'Headline_SummaryHtmlFree_BodyHtmlFree' => array(
            'type' => 'fulltext',
            'value' => 'Headline,SummaryHtmlFree,BodyHtmlFree'
        )*/
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function formatDate()
    {
        return $this->getDateEmbargoCentral('M d, g:i a');
    }

    /**
     * @param NewsMainInfo $info
     * @return void
     */
    function registerMainInfo(NewsMainInfo $info)
    {
        $this->Headline = $info->getHeadline();
        $this->Summary = $info->getSummary();
        $this->SummaryHtmlFree = strip_tags($info->getSummary());
        $this->City = $info->getCity();
        $this->State = $info->getState();
        $this->Country = $info->getCountry();
        $this->Body = $info->getBody();
        $this->BodyHtmlFree = strip_tags($info->getBody());
        $this->Link = $info->getLink();
        $this->setDateEmbargoInGMT($info->getDateEmbargo());
        $this->setDateExpireInGMT($info->getDateExpire());
        $this->IsLandscape = $info->getIsLandscape();
    }

    /**
     * @param string[] $tags
     * @return void
     */
    public function registerTags($tags)
    {
        $tags = explode(',', $tags);

        foreach ($tags as $tag_name) {
            $tag = NewsTag::get("NewsTag","Tag = '".$tag_name."'")->first();

            if (!$tag) {
                $tag = new NewsTag();
                $tag->Tag = $tag_name;
                $tag->write();
            }

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
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Submitter')->getTarget();
    }

    public function setSubmitter(ISubmitter $submitter)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Submitter')->setTarget($submitter);
    }

    /**
     * @return ITag[]
     */
    public function getTags()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Tags')->toArray();
    }

    /**
     * @return string
     */
    public function getTagsCSV()
    {
        $tags = $this->getTags();
        $tags_csv = '';
        foreach ($tags as $tag) {
            $tags_csv .= $tag->Tag . ',';
        }

        return trim($tags_csv, ",");
    }

    public function addTag(INewsTag $tag)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Tags')->add($tag);
    }

    public function clearTags()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Tags')->removeAll();
    }

    /**
     * @param array $file_ids
     * @param IFileUploadService $upload_service
     */
    public function registerImage(array $file_ids, IFileUploadService $upload_service)
    {
        $upload_service->upload($file_ids, 'Image', $this);
    }

    public function removeImage()
    {
        $this->Image->deleteDatabaseOnly();
    }

    public function getImage()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Image')->getTarget();
    }

    /**
     * @param array $file_ids
     * @param IFileUploadService $upload_service
     */
    public function registerDocument(array $file_ids, IFileUploadService $upload_service)
    {
        $upload_service->upload($file_ids, 'Document', $this);
    }

    public function removeDocument()
    {
        $target = AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Document')->getTarget();
        if ($target->Name) {
            $target->deleteDatabaseOnly();
        }
    }

    public function registerSection($section)
    {
        $slider = $featured = $archived = 0;
        if ($section == 'slider') {
            $slider = 1;
            $this->Approved = 1;
        } elseif ($section == 'featured') {
            $featured = 1;
            $this->Approved = 1;
        } elseif ($section == 'recent') {
            $this->Approved = 1;
        } elseif ($section == 'archive') {
            $archived = 1;
        }

        $this->Archived = $archived;
        $this->Featured = $featured;
        $this->Slider = $slider;
    }

    public function registerRestored($restored) {
        $this->Restored = $restored;
    }

    public function registerRank($rank)
    {
        $this->Rank = $rank;
    }

    public function getHTMLBody()
    {
        return html_entity_decode($this->Body);
    }

    public function getHTMLSummary()
    {
        return html_entity_decode($this->Summary);
    }

    public function getHeadlineForUrl()
    {
        $lcase_headline = strtolower(trim($this->Headline));
        $headline_for_url = str_replace(array(' ', '/'), '-', $lcase_headline);

        return $headline_for_url;
    }

    public function getImageForArticle()
    {
        if ($this->Image->exists()) {
            if (!$this->Slider) {
                if ($this->IsLandscape) {
                    $image_html = '<div class="article_full_image">' . $this->Image->getTag() . '</div>';
                } else {
                    $cropped = $this->Image->SetWidth(360);
                    if ($cropped) {
                        $image_html = '<div class="article_cropped_image">' . $cropped->getTag() . '</div>';
                    } else {
                        $image_html = '<div class="article_cropped_image">N/A</div>';
                    }
                }

                return $image_html;
            }
        }
    }

    public function getImageThumb()
    {
        if ($this->Image->exists()) {
            $thumb = $this->Image->SetWidth(100);
            if ($thumb) {
                $image_html = '<div class="recent_image">' . $thumb->getTag() . '</div>';

                return $image_html;
            }
        }
    }

    public function formattedDate()
    {
        return $this->getDateEmbargoCentral('M jS Y');
    }

    public function shortenText($text, $chars)
    {
        if (strlen($text) > $chars) {
            $maxLength = $chars - 3;
            $text = substr($text, 0, $maxLength);
            $text .= '...';
        }

        return $text;
    }

    public function deleteArticle() {
        $this->Deleted = true;
    }

    public function getDateEmbargoCentral($format='Y-m-d H:i:s') {
        $date_embargo = $this->DateEmbargo;

        if ($date_embargo) {
            $date_embargo_gmt = new DateTime($date_embargo, new DateTimeZone('GMT'));
            $date_embargo_central = $date_embargo_gmt->setTimezone(new DateTimeZone('America/Chicago'));
            $date_embargo = $date_embargo_central->format($format);
        }

        return $date_embargo;
    }

    public function setDateEmbargoInGMT($date_embargo) {
        if ($date_embargo) {
            $date_embargo_central = new DateTime($date_embargo, new DateTimeZone('America/Chicago'));
            $date_embargo_gmt = $date_embargo_central->setTimezone(new DateTimeZone('GMT'));
            $date_embargo = $date_embargo_gmt->format('Y-m-d H:i:s');
        }

        $this->DateEmbargo = $date_embargo;
    }

    public function getDateExpireCentral($format='Y-m-d H:i:s') {
        $date_expire = $this->DateExpire;

        if ($date_expire) {
            $date_expire_gmt = new DateTime($date_expire, new DateTimeZone('GMT'));
            $date_expire_central = $date_expire_gmt->setTimezone(new DateTimeZone('America/Chicago'));
            $date_expire = $date_expire_central->format($format);
        }

        return $date_expire;
    }

    public function setDateExpireInGMT($date_expire) {
        if ($date_expire) {
            $date_expire_central = new DateTime($date_expire, new DateTimeZone('America/Chicago'));
            $date_expire_gmt = $date_expire_central->setTimezone(new DateTimeZone('GMT'));
            $date_expire = $date_expire_gmt->format('Y-m-d H:i:s');
        }

        $this->DateExpire = $date_expire;
    }


}