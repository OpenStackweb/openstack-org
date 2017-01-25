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
final class NewsFeaturedToArchiveMigration extends AbstractDBMigrationTask
{
    protected $title = "News Featured To Archive Migration";

    protected $description = "now limiting featured news articles to 5 so we move the rest to archive";

    function doUp()
    {
        global $database;

        $news_repository = new SapphireNewsRepository();
        $news_manager = new NewsRequestManager(
            $news_repository,
            new SapphireSubmitterRepository,
            new NewsFactory,
            new NewsValidationFactory,
            new SapphireFileUploadService(),
            SapphireTransactionManager::getInstance()
        );

        $where_string = "Featured = 1 AND Approved = 1";
        $featured_articles = News::get()->where($where_string)->sort('Rank','ASC')->limit(1000,5);

        foreach ($featured_articles as $article) {
            $news_manager->archiveNewsArticle($article->ID);
        }

    }

    function doDown()
    {

    }
}
