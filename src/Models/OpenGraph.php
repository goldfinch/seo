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

class OpenGraph extends DataObject
{
    private static $singular_name = 'open graph';

    private static $plural_name = 'open graphs';

    private static $table_name = 'OpenGraph';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
        'Disabled' => 'Boolean',

        'OG_Title' => 'Varchar',
        'OG_Type' => 'Varchar',
        'OG_Url' => 'Text',

        'OG_Article_Author' => 'Text',
        'OG_Article_PublishedTime' => 'Varchar',
        'OG_Article_ModifiedTime' => 'Varchar',
        'OG_Article_ExpirationTime' => 'Varchar',
        'OG_Article_Section' => 'Varchar',
        'OG_Article_Tags' => 'Text',

        'OG_Profile_FirstName' => 'Varchar',
        'OG_Profile_LastName' => 'Varchar',
        'OG_Profile_Username' => 'Varchar',
        'OG_Profile_Gender' => 'Varchar',

        'OG_Book_Author' => 'Text',
        'OG_Book_Isbn' => 'Varchar',
        'OG_Book_ReleaseDate' => 'Varchar',
        'OG_Book_Tags' => 'Text',

        'OG_SiteName' => 'Varchar',
        'OG_Description' => 'Varchar',
        'OG_Determiner' => 'Varchar',
        'OG_Locale' => 'Varchar',
        'OG_LocaleAlternate' => 'Text',
    ];

    // private static $casting = [];

    // private static $indexes = null;

    // private static $defaults = [];

    private static $many_many = [
        'OG_Images' => Image::class,
        'OG_Videos' => File::class,
        'OG_Audios' => File::class,
    ];

    private static $owns = [
        'OG_Images',
        'OG_Videos',
        'OG_Audios',
    ];

    private static $many_many_extraFields = [
        'OG_Images' => [
            'SortOrder' => 'Int',
            'OG_Image_Width' => 'Int',
            'OG_Image_Height' => 'Int',
            'OG_Image_Alt' => 'Varchar',
        ],
        'OG_Videos' => [
          'SortOrder' => 'Int',
          'OG_Image_Width' => 'Int',
          'OG_Image_Height' => 'Int',
        ],
        'OG_Audios' => [
          'SortOrder' => 'Int',
        ],
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
        ]);

        $types = [

          // No Vertical
          'website' => 'website',
          'profile' => 'profile',
          'article' => 'article',
          // 'book' => 'book',

          // Video
          // 'video.movie' => 'video.movie',
          // 'video.episode' => 'video.episode',
          // 'video.tv_show' => 'video.tv_show',
          // 'video.other' => 'video.other',

          // Music
          // 'music.song' => 'music.song',
          // 'music.album' => 'music.album',
          // 'music.playlist' => 'music.playlist',
          // 'music.radio_station' => 'music.radio_station',
        ];

        $fields->addFieldsToTab('Root.Main', [

          CompositeField::create(

            TextField::create('Title', 'Open Graph Record Title'),
            CheckboxField::create('Disabled', 'Disable this OG')->setDescription('Any page that is using this OG record will not be displaying any og-related tags'),

          )->addExtraClass('mb-5'),

          CompositeField::create(

            LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Basic metadata</h2><p class="mb-4">The four required properties for every page are:</p>'),

            TextField::create('OG_Title', 'Title')->setDescription('The title of your object as it should appear within the graph, e.g., "The Rock".'),
            DropdownField::create('OG_Type', 'Type', $types)->setDescription('The type of your object, e.g., "video.movie". Depending on the type you specify, other properties may also be required.'),
            SortableUploadField::create('OG_Images', 'Images')->setDescription('An image URL which should represent your object within the graph.'),
            TextField::create('OG_Url', 'URL')->setDescription('The canonical URL of your object that will be used as its permanent ID in the graph, e.g., "https://www.imdb.com/title/tt0117500/".'),

          )->addExtraClass('mb-5'),

          Wrapper::create(

            CompositeField::create(

              LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Article metadata</h2><p class="mb-4">Structured properties</p>'),

              TextareaField::create('OG_Article_Author', 'Authors')->setDescription('[profile array] - Writers of the article.'),
              TextField::create('OG_Article_PublishedTime', 'Published time')->setDescription('[datetime] - When the article was first published.<br>eg: <em>1972-06-18 / 1972-06-18T01:23:45Z / 1972-06-17T20:23:45-05:00</em>'),
              TextField::create('OG_Article_ModifiedTime', 'Modified time')->setDescription('[datetime] - When the article was last changed.<br>eg: <em>1972-06-18 / 1972-06-18T01:23:45Z / 1972-06-17T20:23:45-05:00</em>'),
              TextField::create('OG_Article_ExpirationTime', 'Expiration time')->setDescription('[datetime] - When the article is out of date after.<br>eg: <em>1972-06-18 / 1972-06-18T01:23:45Z / 1972-06-17T20:23:45-05:00</em>'),
              TextField::create('OG_Article_Section', 'Section')->setDescription('[string] - A high-level section name. E.g. Technology'),
              TextareaField::create('OG_Article_Tags', 'Tags')->setDescription('[string array] - Tag words associated with this article.'),

            )->addExtraClass('mb-5'),

          )->displayIf('OG_Type')->contains('article')->end(),

          Wrapper::create(

            CompositeField::create(

              LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Profile metadata</h2><p class="mb-4">Structured properties</p>'),

              TextField::create('OG_Profile_FirstName', 'First name')->setDescription('[string] - A name normally given to an individual by a parent or self-chosen.'),
              TextField::create('OG_Profile_LastName', 'Last name')->setDescription('[string] - A name inherited from a family or marriage and by which the individual is commonly known.'),
              TextField::create('OG_Profile_Username', 'Username')->setDescription('[string] - A short unique string to identify them.'),
              TextField::create('OG_Profile_Gender', 'Gender')->setDescription('enum(male, female) - Their gender.'),

            )->addExtraClass('mb-5'),

          )->displayIf('OG_Type')->contains('profile')->end(),

          // Wrapper::create(

          //   CompositeField::create(

          //     LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Book metadata</h2><p class="mb-4">Structured properties</p>'),

          //     TextField::create('OG_Book_Author', 'Author')->setDescription('[profile array] - Who wrote this book.'),
          //     TextField::create('OG_Book_Isbn', 'ISBN')->setDescription('[string] - The <a href="https://en.wikipedia.org/wiki/International_Standard_Book_Number" target="_blank">ISBN</a>'),
          //     TextField::create('OG_Book_ReleaseDate', 'Release date')->setDescription('[datetime] - The date the book was released.
          //     '),
          //     TextareaField::create('OG_Book_Tags', 'Tags')->setDescription('[string array] - Tag words associated with this book.'),

          //   )->addExtraClass('mb-5'),

          // )->displayIf('OG_Type')->contains('book')->end(),

          CompositeField::create(

            LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Optional metadata</h2><p class="mb-4">The following properties are optional for any object and are generally recommended:</p>'),

            TextField::create('OG_SiteName', 'Site name')->setDescription('If your object is part of a larger web site, the name which should be displayed for the overall site. e.g., "IMDb".'),
            TextareaField::create('OG_Description', 'Description')->setDescription('A one to two sentence description of your object.'),
            TextField::create('OG_Determiner', 'Determiner')->setDescription('The word that appears before this object\'s title in a sentence. An enum of (a, an, the, "", auto). If auto is chosen, the consumer of your data should chose between "a" or "an". Default is "" (blank).'),

            TextField::create('OG_Locale', 'Locale')->setDescription('The locale these tags are marked up in. Of the format language_TERRITORY. Default is en_US.'),
            TextareaField::create('OG_LocaleAlternate', 'Locale alternate')->setDescription('An array of other locales this page is available in.'),

            SortableUploadField::create('OG_Videos', 'Videos')->setDescription('A URL to a video file that complements this object.'),
            SortableUploadField::create('OG_Audios', 'Audios')->setDescription('A URL to an audio file to accompany this object.'),

          ),

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
