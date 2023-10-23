<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Forms\DropdownField;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class MetaConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'MetaConfig';

    private static $db = [
        'GeoPosition' => 'Varchar',
        'GeoRegion' => 'Varchar',
        'GeoPlacename' => 'Varchar',
        'Rating' => 'Varchar',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $ratingTypes = [
          '' => '-',
          'general' => 'general',
          'mature' => 'mature',
          'restricted' => 'restricted',
          'adult' => 'adult',
          '14 years' => '14 years',
          'safe for kids' => 'safe for kids',
          'RTA-5042-1996-1400-1577-RTA' => 'RTA-5042-1996-1400-1577-RTA',
        ];

        $fields->dataFieldByName('GeoPosition')->setDescription('Contains first the geographic latitude and arranged behind the geographic longitude<br>eg: 48.169822;11.601171');
        $fields->dataFieldByName('GeoRegion')->setDescription('Tag is composed of two parts: country code and regional code, eg: NZ-AUK<br>Use <a target="_blank" href="https://www.iso.org/obp/ui/#home">www.iso.org/obp/ui</a> to find out country and region code');
        $fields->dataFieldByName('GeoPlacename')->setDescription('Name of the place (city/town)');

        $fields->addFieldsToTab('Root.Main', [

          DropdownField::create(
            'Rating',
            'Rating',
            $ratingTypes
          )->setDescription('Labels a page as containing sexually-explicit adult content, to signal that it be filtered by SafeSearch results. <a href="https://developers.google.com/search/docs/crawling-indexing/safesearch" target="_blank">Learn more about labeling SafeSearch pages</a>.')

        ]);

        return $fields;
    }
}
