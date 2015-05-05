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

class ReviewsMigrationTask extends MigrationTask
{

    protected $title = "Reviews Migration";

    protected $description = "Migrates all reviews from rating system to our own db";

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = Migration::get()->filter('Name', $this->title)->First();
        if (!$migration) {
            //if not create migration and run it...
            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
            //run migration proc

            //get migration data from cvs file,
            //this cvs file contains all Reviews
            $ds = new CvsDataSourceReader("\t");
            $cur_path = Director::baseFolder();
            $ds->Open($cur_path . "/marketplace/code/migrations/data/reviews.csv");
            $headers = $ds->getFieldsInfo();

            $split_reviews = array();
            //and stored those ones on a hash
            try {
                while (($row = $ds->getNextRow()) !== FALSE) {
                    $review = explode(',',$row[0]);
                    $created = $review[5];
                    $title = $review[14];
                    $comment = $review[12];
                    $rating = $review[0];
                    $email = $review[13];
                    $product_name = $review[10];
                    $is_approved = $review[20];
                    if ($is_approved == 'TRUE') {
                        $split_reviews[] = array(
                            "Created" => $created,
                            "Title" => $title,
                            "Comment" => $comment,
                            "Rating" => $rating,
                            "Approved" => 1,
                            "Email" => $email,
                            "ProductName" => $product_name
                        );
                    }

                }
            } catch (Exception $e) {
                $status = 0;
            }

            echo sprintf("Review Count %d </BR>", count($split_reviews));
            echo 'Procesing .... </BR>';

            foreach($split_reviews as $review) {
                $member = DB::query("SELECT * FROM member WHERE Email = '".$review['Email']."' LIMIT 1;");
                $product = DB::query("SELECT * FROM companyservice WHERE Name = '".$review['ProductName']."' LIMIT 1;");

                //echo "'".$review['Email']."',";
                $reviews_migrated = 0;
                if ($member->numRecords()) {
                    if ($product->numRecords()) {
                        $this->writeReview($review,$member->first()['ID'],$product->first()['ID']);
                        $reviews_migrated++;
                    } else {
                        echo 'Product not found for : '.$review['ProductName'].'<br>';
                    }
                } else {
                    echo 'Member not found for : '.$review['Email'].'<br>';
                }
            }

        }
        else{
            echo "Migration Already Ran! <BR>";
        }
        echo "Migration Done : ".$reviews_migrated." reviews mirgated <BR>";
    }


    private function writeReview($review, $member_id, $product_id)
    {
        $title = Convert::raw2sql($review['Title']);
        $comment = Convert::raw2sql($review['Comment']);
        $created = date('Y-m-d H:i:s',strtotime($review['Created']));
        $query = "INSERT INTO  `marketplacereview` (`ClassName`,`Created`,`LastEdited`,`Title`,`Comment`,`Rating`,`Approved`,`MemberID`,`CompanyServiceID`)";
        $query .=" VALUES('MarketPlaceReview','".$created."',now(),'".$title."','".$comment."',".$review['Rating'].",1,".$member_id.",".$product_id.")";
        DB::query($query);
    }

    function down()
    {

    }
}