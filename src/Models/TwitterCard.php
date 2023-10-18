<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\CompositeField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Bummzack\SortableFile\Forms\SortableUploadField;

class TwitterCard extends DataObject
{
    /**
     * Cards Markup Tag Reference
     *
     * https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/markup
     *
     * Validator
     *
     * https://cards-dev.twitter.com/validator
     */

    private static $singular_name = 'twitter card';

    private static $plural_name = 'twitter cards';

    private static $table_name = 'TwitterCard';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
        'Disabled' => 'Boolean',

        'TC_Title' => 'Varchar',
        'TC_Type' => 'Varchar',
    ];

    // private static $casting = [];

    // private static $indexes = null;

    // private static $defaults = [];

    private static $has_one = [
        'TC_Image' => Image::class,
    ];

    private static $owns = [
        'TC_Image',
    ];

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

        $fields->removeByName([
          'Title',
          'Disabled',

          'TC_Title',
          'TC_Type',

          'TC_Image',
        ]);

        $types = [
          'summary' => 'Summary',
          'summary_large_image' => 'Summary large image',
          'app' => 'App',
          'player' => 'Player',
        ];

        $fields->addFieldsToTab('Root.Main', [

          CompositeField::create(

            TextField::create('Title', 'Twitter Card Record Title'),
            CheckboxField::create('Disabled', 'Disable this TC')->setDescription('Any page that is using this TC record will not be displaying any twitter-card-related tags'),

          )->addExtraClass('mb-5'),

          CompositeField::create(

            LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Card metadata</h2>'),

            DropdownField::create('TC_Type', 'Type', $types)->setDescription('The card type'),
            TextField::create('TC_Site', 'Site')->setDescription('@username of website. Either twitter:site or twitter:site:id is required.'),

            TextField::create('TC_SiteID', 'Site ID')->setDescription('Same as twitter:site, but the user’s Twitter ID. Either twitter:site or twitter:site:id is required.')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
              ->orIf()->contains('player')
            ->end(),

            TextField::create('TC_Creator', 'Creator')->setDescription('@username of content creator')
              ->displayIf('TC_Type')->contains('summary_large_image')
            ->end(),

            TextField::create('TC_CreatorID', 'Creator ID')->setDescription('Twitter user ID of content creator')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
            ->end(),

            TextareaField::create('TC_Description', 'Description')->setMaxLength(200)->setDescription('Description of content (maximum 200 characters)')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
              ->orIf()->contains('player')
            ->end(),
            TextField::create('TC_Title', 'Title')->setMaxLength(70)->setDescription('Title of content (max 70 characters)')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
              ->orIf()->contains('player')
            ->end(),
            UploadField::create('TC_Image', 'Image')->setDescription('URL of image to use in the card. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported. Only the first frame of an animated GIF will be used. SVG is not supported.')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
              ->orIf()->contains('player')
            ->end(),
            TextareaField::create('TC_ImageAlt', 'Image Alt')->setMaxLength(420)->setDescription('A text description of the image conveying the essential nature of an image to users who are visually impaired. Maximum 420 characters.')
              ->displayIf('TC_Type')->contains('summary')
              ->orIf()->contains('summary_large_image')
              ->orIf()->contains('player')
            ->end(),

            TextField::create('TC_Player', 'Player')->setDescription('HTTPS URL of player iframe')
              ->displayIf('TC_Type')
              ->contains('player')
            ->end(),
            TextField::create('TC_PlayerWidth', 'Player width')->setDescription('Width of iframe in pixels')
              ->displayIf('TC_Type')
              ->contains('player')
            ->end(),
            TextField::create('TC_PlayerHeight', 'Player height')->setDescription('Height of iframe in pixels')
              ->displayIf('TC_Type')
              ->contains('player')
            ->end(),
            TextField::create('TC_PlayerStream', 'Player stream')->setDescription('URL to raw video or audio stream')
              ->displayIf('TC_Type')
              ->contains('player')
            ->end(),

            TextField::create('TC_AppNameIphone', 'App Name Iphone')->setDescription('Name of your iPhone app')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppIdIphone', 'App ID Iphone')->setDescription('Your app ID in the iTunes App Store (Note: NOT your bundle ID)')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppUrlIphone', 'App Url Iphone')->setDescription('Your app’s custom URL scheme (you must include ”://” after your scheme name)')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppNameIpad', 'App Name Ipad')->setDescription('Name of your iPad optimized app')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppIdIpad', 'App ID Ipad')->setDescription('Your app ID in the iTunes App Store')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppUrlIpad', 'App Url Ipad')->setDescription('Your app’s custom URL scheme')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppNameGoogleplay', 'App Name Googleplay')->setDescription('Name of your Android app')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppIDGoogleplay', 'App ID Googleplay')->setDescription('Your app ID in the Google Play Store')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),
            TextField::create('TC_AppUrlGoogleplay', 'App Url Googleplay')->setDescription('Your app’s custom URL scheme')
              ->displayIf('TC_Type')
              ->contains('app')
            ->end(),

          )->addExtraClass('mb-5'),

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
