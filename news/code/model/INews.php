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
 * Interface INews
 */
interface INews extends IEntity {

    /**
     * @param NewsMainInfo $info
     * @return void
     */
    function registerMainInfo(NewsMainInfo $info);

    /**
     * @param string[] $tags
     * @return void
     */
    public function registerTags($tags);

    /**
     * @param NewsSubmitter $submitter
     * @return void
     */
    public function registerSubmitter(NewsSubmitter $info);

    /**
     * @return ISubmitter
     */
    public function getSubmitter();

    public function setSubmitter(ISubmitter $submitter);

    /**
     * @return INewsTag[]
     */
    public function getTags();

    /**
     * @return string
     */
    public function getTagsCSV();

    public function addTag(INewsTag $tag);

    public function clearTags();

	/**
	 * @param array $file_ids
	 * @param IFileUploadService $upload_service
	 */
	public function registerImage(array $file_ids, IFileUploadService $upload_service);

    public function removeImage();

    public function getImage();

    /**
     * @param array $file_ids
     * @param IFileUploadService $upload_service
     */
    public function registerDocument(array $file_ids, IFileUploadService $upload_service);

    public function removeDocument();

    public function registerSection($section);

    public function registerRestored($restored);

    public function registerRank($rank);

    public function getHTMLBody();

    public function getHTMLSummary();

    public function getHeadlineForUrl();

    public function getImageForArticle();

    public function getImageThumb();

    public function shortenText($text, $chars);

    public function deleteArticle();

    public function getDateEmbargoCentral($format='Y-m-d H:i:s');

    public function setDateEmbargoInGMT($date_embargo);

    public function getDateExpireCentral($format='Y-m-d H:i:s');

    public function setDateExpireInGMT($date_expire);

} 