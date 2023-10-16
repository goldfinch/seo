<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Security\Permission;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class OpenGraph extends DataObject
{
    private static $singular_name = 'open graph';

    private static $plural_name = 'open graphs';

    private static $table_name = 'OpenGraph';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
    ];

    // private static $casting = [];

    // private static $indexes = null;

    // private static $defaults = [];

    // private static $has_one = [];
    // private static $belongs_to = [];
    // private static $has_many = [];
    // private static $many_many = [];
    // private static $many_many_extraFields = [];
    // private static $belongs_many_many = [];

    // private static $default_sort = null;

    // private static $searchable_fields = [];

    // private static $field_labels = [];

    // // composer require goldfinch/helpers
    // private static $field_descriptions = [];
    // private static $required_fields = [];

    // private static $summary_fields = [];

    public function validate()
    {
        $result = parent::validate();

        // $result->addError('Error message');

        return $result;
    }

    public function onBeforeWrite()
    {
        // ..

        parent::onBeforeWrite();
    }

    public function onBeforeDelete()
    {
        // ..

        parent::onBeforeDelete();
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $types = [
          'website' => 'website',
          'article' => 'article',
          'book' => 'book',
          'profile' => 'profile',

          'music.song' => 'music.song',
          'music.album' => 'music.album',
          'music.playlist' => 'music.playlist',
          'music.radio_station' => 'music.radio_station',

          'video.movie' => 'video.movie',
          'video.episode' => 'video.episode',
          'video.tv_show' => 'video.tv_show',
          'video.other' => 'video.other',
        ];

        $fields->addFieldsToTab('Root.Main', [

          DropdownField::create('OG_Type', 'Choose a type', $types)->setEmptyString('-- choose --'),
          Wrapper::create(

            TextField::create('MetaFacebookAppSecret', 'App Secret')

          )->displayIf('OG_Type')->contains('website')->end()
        ]);

        return $fields;
    }

    // public function canView($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }
}
