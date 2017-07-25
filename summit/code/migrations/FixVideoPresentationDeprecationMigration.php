<?php

final class FixVideoPresentationDeprecationMigration extends AbstractDBMigrationTask
{

    protected $title = "FixVideoPresentationDeprecationMigration";


    protected $description = "VideoPresentation to PresentationVideo Migration Fixing Former Migration Bug";


    protected $errors = [];


    protected $stats = [
        'Summits created' => 0,
        'Speakers created' => 0,
        'Speakers resolved' => 0,
        'Speakers found' => 0,
        'Presentations created' => 0,
        'Videos Created Show' => 0,
        'Videos Created Hide' => 0
    ];

    protected $presentations_skipped = 0;

    protected $summit_stats = [];

    private static $summits = [
        19 => ['San Diego', 409],
        20 => ['Portland', 741],
        21 => ['Atlanta', 1550],
        1  => ['Hong Kong', 1134],
        3  => ['Paris', 2096],
        4  => ['Vancouver', 2520],
        5  => ['Tokyo', 2681],
    ];

    /**
     * Helper method to write context sensitive line breaks
     * @param  integer $times Number of repetitinos
     * @return string
     */
    private function br($times = 1)
    {
        return str_repeat(Director::is_cli() ? PHP_EOL : '<br>', $times);
    }

    private function incrementStat($summit, $stat, $value = 1) {
        if (!isset($this->summit_stats[$summit])) {
            $this->summit_stats[$summit] = $this->stats;
        }

        $this->summit_stats[$summit][$stat] += $value;
    }

    /**
     * Maintains a persistent list of errors
     * @param int $summitID
     * @param string $error
     */
    private function addError($summitID, $error)
    {
        if (!isset($this->errors[$summitID])) {
            $this->errors[$summitID] = [];
        }
        $this->errors[$summitID][] = $error;
    }

    /**
     * Some VideoPresentation objects have wacky d/m/y dates that are stored as Varchar.
     * This method fixes those to be Y-m-d H:i:s
     *
     * There are also some records with weird alphanumeric values like "S55", so we throw those out.
     *
     * @param  VideoPresentation $v
     * @param  string $field The date field name to update
     * @return VideoPresentation
     */
    private function normaliseDateTime(VideoPresentation $v, $field)
    {
        if (strpos($v->$field, '/') !== false) {
            if (strpos($v->$field, ':') === false) {
                $v->$field .= ' 00:00';
            }
            $v->$field = DateTime::createFromFormat('j/n/y H:i', $v->$field)->format('Y-m-d H:i:s');
        } else {
            $v->$field = null;
        }

        return $v;
    }


    /**
     * Given a VideoPresentation object, create a Presentation
     * @param  VideoPresentation $v
     * @return Presentation
     */
    private function createLegacyPresentation(VideoPresentation $v)
    {
        return Presentation::create([
            'Title' => $v->Name,
            'Description' => $v->Description,
            'StartDate' => $v->StartTime ?: $v->Summit()->SummitBeginDate,
            'EndDate' => $v->EndTime ?: $v->Summit()->SummitBeginDate,
            'Published' => true,
            'PublishedDate' => $v->Created,
            'SummitID' => $v->SummitID,
            'Legacy' => true
        ]);
    }


    /**
     * Attempt to add speakers to the presentation by fuzzy matching the FirstName / Surname
     * @param Presentation $p
     * @param VideoPresentation $v
     */
    private function addLegacySpeakers(Presentation $p, VideoPresentation $v)
    {
        $speakers = explode(',', $v->Speakers);

        foreach ($speakers as $speakerName) {
            $speakerName = trim($speakerName);
            $speakerName = preg_replace('/\s\s+/', ' ', $speakerName);
            $speaker = PresentationSpeaker::get()->where("CONCAT(FirstName, ' ', LastName) = '" . Convert::raw2sql($speakerName) . "'");
            if (!$speaker->exists()) {
                $names = explode(' ', $speakerName);
                $firstName = array_shift($names);
                $lastName = implode(' ', $names);
                $speaker = PresentationSpeaker::create([
                    'FirstName' => $firstName,
                    'LastName' => $lastName,
                    'Notes' => '[LEGACY]'
                ]);
                $speaker->write();
                $p->Speakers()->add($speaker);
                echo "Created legacy speaker {$speaker->getName()} from $speakerName" . $this->br();
                $this->incrementStat($p->SummitID, 'Speakers created');
            } else if ($speaker->count() > 1) {
                echo "Found more than one speaker matching \"$speakerName\": " . implode(',', $speaker->column('ID')) . $this->br();
                $bestCandidate = null;
                $highScore = 0;
                foreach ($speaker as $s) {
                    $numSpeakings = DB::query("SELECT COUNT(*) FROM `Presentation_Speakers` WHERE PresentationSpeakerID = {$s->ID}")->value();
                    $hasMember = (int)$s->Member()->exists();
                    $score = $numSpeakings + $hasMember;
                    if ($score > $highScore) {
                        $highScore = $score;
                        $bestCandidate = $s;
                    }
                }

                if ($bestCandidate) {
                    echo "Found best candidate #{$bestCandidate->ID} with a score of {$highScore}" . $this->br();
                    $p->Speakers()->add($bestCandidate);
                    $this->incrementStat($p->SummitID, 'Speakers resolved');
                } else {
                    $this->addError("No speakings or MemberIDs for " . implode(',', $speaker->map('ID', 'Name')->toArray()) . " on \"{$p->Title}\"");
                }
            } else {
                $p->Speakers()->add($speaker->first());
                echo "Added speaker " . $speaker->first()->getName() . $this->br();
                $this->incrementStat($p->SummitID, 'Speakers found');
            }
        }

        return $p;
    }


    /**
     * Create a PresentationVideo object for a presentation from a VideoPresentation
     * @param  Presentation $p
     * @param  VideoPresentation $v
     * @return PresentationVideo
     */
    private function createLegacyVideoMaterial(Presentation $p, VideoPresentation $v)
    {
        $display = (in_array($v->SummitID, [1,3,19,20,21])) ? 1 : $v->DisplayOnSite;

        $pres_video = PresentationVideo::create([
            'PresentationID' => $p->ID,
            'Name' => $v->Name,
            'DisplayOnSite' => $display,
            'Processed' => true,
            'YouTubeID' => trim($v->YouTubeID),
            'Featured' => $v->Featured,
            'Created' => $v->LastEdited,
            'LastEdited' => $v->LastEdited,
            'DateUploaded' => $v->StartTime ? $v->StartTime : $p->Summit()->SummitBeginDate
        ]);

        if ($display) {
            $this->incrementStat($p->SummitID, 'Videos Created Show');
        } else {
            $this->incrementStat($p->SummitID, 'Videos Created Hide');
        }

        return $pres_video;
    }

    private static function DBCleanup(){
        DB::query("
        DELETE PresentationVideo FROM PresentationVideo
INNER JOIN PresentationMaterial ON PresentationMaterial.ID = PresentationVideo.ID
INNER JOIN Presentation ON Presentation.ID = PresentationMaterial.PresentationID
INNER JOIN SummitEvent ON SummitEvent.ID = Presentation.ID 
WHERE SummitEvent.SummitID IN (19, 20, 21, 1, 3, 4, 5);
        ");

        DB::query("
        DELETE PresentationMaterial FROM PresentationMaterial
INNER JOIN Presentation ON Presentation.ID = PresentationMaterial.PresentationID
INNER JOIN SummitEvent ON SummitEvent.ID = Presentation.ID 
WHERE SummitEvent.SummitID IN (19, 20, 21, 1, 3, 4, 5);
");

        DB::query("
        DELETE PresentationVideo FROM PresentationVideo
INNER JOIN PresentationMaterial ON PresentationMaterial.ID = PresentationVideo.ID
WHERE PresentationMaterial.PresentationID IS NULL OR PresentationMaterial.PresentationID = 0;
");

        DB::query("        
DELETE PresentationMaterial FROM PresentationMaterial
WHERE PresentationMaterial.PresentationID IS NULL OR PresentationMaterial.PresentationID = 0;
");

        DB::query("
DELETE Presentation FROM Presentation
INNER JOIN SummitEvent ON SummitEvent.ID = Presentation.ID 
WHERE SummitEvent.SummitID IN (19, 20, 21, 1, 3, 4, 5) AND 
(SummitEvent.Title IS NULL OR SummitEvent.Title  = '');");

        DB::query("
DELETE SummitEvent FROM SummitEvent
WHERE SummitEvent.SummitID IN (19, 20, 21, 1, 3, 4, 5) AND 
(SummitEvent.Title IS NULL OR SummitEvent.Title  = '');");
    }

    /**
     * Performs the migration
     */
    public function doUp()
    {
        self::DBCleanup();
        // We're mocking some stuff, e.g. Summit, Presentation, and we need some forgiveness here
        Config::inst()->update('DataObject', 'validation_enabled', false);
        $i = 0;
        echo "Migrating VideoPresentations..." . $this->br(3);
        $presentationLookup = [];

        // Finding a potential presentation for the video can only be done on fuzzy matching
        // the title. Special characters in the title can defeat that matching, so this
        // loop creates a lookup of alphnumeric skus of the title for easier matching later.
        $summit_ids  = array_keys(self::$summits);
        foreach (Summit::get()->filterAny(['ID' => $summit_ids]) as $s) {
            $presentationLookup[$s->ID] = [];
            foreach ($s->Presentations()->sort('Title ASC') as $p) {
                $presentationLookup[$s->ID][strtolower(
                    preg_replace('/[^a-zA-Z0-9]/', '', $p->Title)
                )] = $p->ID;
            }
        }


        foreach(self::$summits as $summitId => $summitInfo){

            foreach (VideoPresentation::get()->filter(['PresentationCategoryPageID' => $summitInfo[1]]) as $v) {
                $i++;
                if (!$v->YouTubeID) {
                    $this->presentations_skipped++;
                    continue;
                }

                if (PresentationVideo::get()->filter('YouTubeID',trim($v->YouTubeID))->Exists()) {
                    continue;
                }

                $v->SummitID = $summitId;
                // Attempt to match a Presentation of the same name as the video
                $lookup = $presentationLookup[$v->SummitID];
                $cleanTitle = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $v->Name));
                $originalPresentation = false;
                if (isset($lookup[$cleanTitle])) {
                    $originalPresentation = Presentation::get()->byID($lookup[$cleanTitle]);
                }

                // If no Presentation exists, create a shell.
                if (!$originalPresentation) {
                    echo "********* {$v->Name} has no original presentation." . $this->br();

                    $v = $this->normaliseDateTime($v, 'StartTime');
                    $v = $this->normaliseDateTime($v, 'EndTime');

                    $originalPresentation = $this->createLegacyPresentation($v);
                    $originalPresentation->write(false, true);

                    echo "Created presentation {$v->ID} -> {$originalPresentation->ID}...";
                    $originalPresentation = $this->addLegacySpeakers($originalPresentation, $v);
                    $this->incrementStat($originalPresentation->SummitID, 'Presentations created');
                } else {
                    echo "*** Original presentation found: " . $originalPresentation->ID . $this->br();
                }

                $material = $this->createLegacyVideoMaterial($originalPresentation, $v);
                $material->write();

                DB::query(sprintf(
                    "UPDATE PresentationMaterial SET Created = '%s', LastEdited = '%s' WHERE ID = '%s'",
                    $v->LastEdited,
                    $v->LastEdited,
                    $material->ID
                ));

                if(!empty($v->URLSegment))
                    DB::query(sprintf(
                        "UPDATE Presentation 
                        SET Slug = '%s' , Legacy = 1 
                        WHERE ID = '%s'",
                        $v->URLSegment,
                        $originalPresentation->ID
                    ));

                echo "Created video with date uploaded {$material->DateUploaded}." . $this->br();
            }

            echo $this->br(3);
            echo "Migration complete!" . $this->br(3);
            if (!empty($this->errors)) {
                echo "Warnings:" . $this->br();
                foreach ($this->errors as $summitID => $errors) {
                    echo Summit::get()->byID($summitID)->Title . " (" . sizeof($errors) . ")" . $this->br();
                    echo "----------------------------";
                    echo $this->br(2);
                    foreach ($errors as $e) {
                        echo "\t{$e}" . $this->br();
                    }
                }
            }

            if (!empty($this->summit_stats)) {
                echo $this->br(3);

                foreach($this->summit_stats as $summit_id => $summit_stat) {
                    $summit_name = Summit::get()->byID($summit_id);
                    echo "Stats for " . $summit_name->Title . $this->br();
                    echo "---------------------" . $this->br();
                    foreach ($summit_stat as $title => $stat) {
                        echo "{$title}: $stat" . $this->br();
                    }
                    echo $this->br(2);
                }

                echo $this->br(2);
                echo 'Presentations skipped bc no YoutubeId: '.$this->presentations_skipped. $this->br(1);
            }

        }

    }


    /**
     * Reverses the migration
     */
    public function doDown()
    {

        foreach (Presentation::get()->filter('Legacy', true) as $p) {
            $p->Speakers()->setByIdList(array());
            foreach ($p->Materials()->filter('ClassName', 'PresentationVideo') as $material) {

                echo "Deleted video # ".$material->ID . $this->br();
                $material->delete();
            }
            echo "Deleted presentation # ".$p->ID . $this->br();
            $p->delete();
        }

        if (!Presentation::get()->filter('Legacy', true)->exists()) {
            echo "Deleted all legacy presentations" . $this->br();
        } else {
            echo "*** FAIL: Did not delete all legacy presentations" . $this->br();
        }

        foreach (PresentationSpeaker::get()->filter('Notes', '[LEGACY]') as $speaker) {
            $speaker->delete();
        }
        if (!PresentationSpeaker::get()->filter('Notes', '[LEGACY]')->exists()) {
            echo "Deleted all legacy speakers" . $this->br();
        } else {
            echo "*** FAIL: Did not delete all legacy speakers" . $this->br();
        }

        Summit::get()->filter('Title', ['San Diego', 'Portland', 'Atlanta'])->removeAll();

        if (!Summit::get()->filter('Title', ['San Diego', 'Portland', 'Atlanta'])->exists()) {
            echo "Deleted legacy summits" . $this->br();
        } else {
            echo "*** FAIL: Did not delete legacy summits" . $this->br();
        }
    }
}