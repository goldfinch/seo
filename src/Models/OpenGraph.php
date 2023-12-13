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
    /**
     * The Open Graph protocol
     *
     * https://ogp.me/
     *
     * Facebook Open Graph Markup
     * https://developers.facebook.com/docs/sharing/webmasters/
     *
     * Validator
     *
     * https://developers.facebook.com/tools/debug/
     */
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

        'FB_AppID' => 'Varchar',
    ];

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
            'SortExtra' => 'Int',
            'OG_Image_Width' => 'Int',
            'OG_Image_Height' => 'Int',
            'OG_Image_Alt' => 'Varchar',
        ],
        'OG_Videos' => [
          'SortExtra' => 'Int',
          'OG_Image_Width' => 'Int',
          'OG_Image_Height' => 'Int',
        ],
        'OG_Audios' => [
          'SortExtra' => 'Int',
        ],
    ];

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
          'OG_Images',
          'OG_Videos',
          'OG_Audios',

          'Title',
          'Disabled',

          'OG_Title',
          'OG_Type',
          'OG_Url',

          'OG_Article_Author',
          'OG_Article_PublishedTime',
          'OG_Article_ModifiedTime',
          'OG_Article_ExpirationTime',
          'OG_Article_Section',
          'OG_Article_Tags',

          'OG_Profile_FirstName',
          'OG_Profile_LastName',
          'OG_Profile_Username',
          'OG_Profile_Gender',

          'OG_Book_Author',
          'OG_Book_Isbn',
          'OG_Book_ReleaseDate',
          'OG_Book_Tags',

          'OG_SiteName',
          'OG_Description',
          'OG_Determiner',
          'OG_Locale',
          'OG_LocaleAlternate',

          'FB_AppID',
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

            TextField::create('OG_Title', 'Title')->setDescription('The title of your object as it should appear within the graph, e.g., "The Rock".<br>---<br>The title of your article without any branding such as your site name.'),
            DropdownField::create('OG_Type', 'Type', $types)->setDescription('The type of your object, e.g., "video.movie". Depending on the type you specify, other properties may also be required.<br>---<br>The type of media of your content. This tag impacts how your content shows up in Feed. If you don\'t specify a type,the default is website. Each URL should be a single object, so multiple og:type values are not possible. Find the full list of object types in <a href="https://ogp.me/#types" target="_blank">Object Types Reference</a>'),
            SortableUploadField::create('OG_Images', 'Images')->setDescription('An image URL which should represent your object within the graph.<br>---<br>The URL of the image that appears when someone shares the content to Facebook. See <a href="https://developers.facebook.com/docs/sharing/webmasters/#images" target="_blank">below</a> for more info, and check out our <a href="https://developers.facebook.com/docs/sharing/best-practices#images" target="_blank">best practices guide</a> to learn how to specify a high quality preview image.<br>---<br>Recommended: at least 1080px in width, 600px at the minimum, 1:1. To get the best display on high-resolution devices, it should be at least 1200x630 pixels. You can go smaller if you wish, but to get the larger image format for posts, it should be at least 600x315px, otherwise you\'ll be left with the less-engaging small image format.'),
            TextField::create('OG_Url', 'URL')->setDescription('The canonical URL of your object that will be used as its permanent ID in the graph, e.g., "https://www.imdb.com/title/tt0117500/".<br>---<br>The <a target="_blank" href="https://developers.facebook.com/docs/sharing/webmasters/getting-started/versioned-link">canonical URL</a> for your page. This should be the undecorated URL, without session variables, user identifying parameters, or counters. Likes and Shares for this URL will aggregate at this URL. For example, mobile domain URLs should point to the desktop version of the URL as the canonical URL to aggregate Likes and Shares across different versions of the page.'),

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
            TextareaField::create('OG_Description', 'Description')->setDescription('A one to two sentence description of your object.<br>---<br>A brief description of the content, usually between 2 and 4 sentences. This will displayed below the title of the post on Facebook.'),
            TextField::create('OG_Determiner', 'Determiner')->setDescription('The word that appears before this object\'s title in a sentence. An enum of (a, an, the, "", auto). If auto is chosen, the consumer of your data should chose between "a" or "an". Default is "" (blank).'),

            TextField::create('OG_Locale', 'Locale')->setDescription('The locale these tags are marked up in. Of the format language_TERRITORY. Default is en_US.<br>---<br>The locale of the resource. Defaults to en_US. You can also use og:locale:alternate if you have other available language translations available. Learn about the locales we support in our <a href="https://developers.facebook.com/docs/javascript/internationalization#locales" target="_blank">documentation on localization</a>.<br>If this field is empty, this value refers to the general value in the config.'),
            TextareaField::create('OG_LocaleAlternate', 'Locale alternate')->setDescription('An array of other locales this page is available in.'),

            SortableUploadField::create('OG_Videos', 'Videos')->setDescription('A URL to a video file that complements this object.'),
            SortableUploadField::create('OG_Audios', 'Audios')->setDescription('A URL to an audio file to accompany this object.'),

          ),

          CompositeField::create(

            LiteralField::create('LF', '<h2 style="font-size: 1.5rem">Facebook specific metadata</h2><p class="mb-4">The following properties are related to Facebook only:</p>'),

            TextField::create('FB_AppID', 'Faceboook App ID')->setDescription('In order to use <a href="https://developers.facebook.com/docs/sharing/referral-insights" target="_blank">Facebook Insights</a> you must add the app ID to your page. Insights lets you view analytics for traffic to your site from Facebook. Find the app ID in your <a href="https://developers.facebook.com/apps/redirect/dashboard" target="_blank">App Dashboard</a>.<br>If this field is empty, this value refers to the general value in the config.'),

          ),

        ]);

        return $fields;
    }
}
