<?php


class Sluggable extends DataExtension {

  private static $db = array (
    'Slug' => 'Varchar(255)'
  );

  private static $indexes = array (
    'Slug' => true
  );

  public static function create_slug($obj, $field) {
    if(!isset($_REQUEST[$field])) {
      $title = $obj->$field;
    }
    else {
      $title = $_REQUEST[$field];
    }
    $slug = singleton('SiteTree')->generateURLSegment($title);
    $original_slug = $slug;
    $i = 0;
    $class = $obj->class;
    if(!Object::has_extension($class, "Sluggable")) {
      while($parent = get_parent_class($obj)) {
        if(Object::has_extension($parent,"Sluggable")) {
          $class = $parent;
          break;
        }
        else {
          $obj = singleton($parent);
        }
      }
    }
    while($t = DataList::create($class)
                ->filter(array("Slug" => "$slug"))
                ->exclude(array("{$class}.ID" => $obj->ID))
                ->first()) {
      $i++;
      $slug = $original_slug."-{$i}";
    }
    $obj->Slug = $slug;
  }


  public static function get_by_slug($class, $slug) {
    return DataList::create($class)->filter("Slug",$slug)->first();
  }


  public function onBeforeWrite() {
    $f = $this->owner->config()->slug_field;
    $field = $f ? $f : "Title";
    self::create_slug($this->owner, $field);
  }
}