<?php

namespace Goldfinch\Seo\Extensions;

use Goldfinch\Seo\Models\OpenGraph;
use SilverStripe\Forms\CheckboxField;
use SilverShop\HasOneField\HasOneButtonField;
// use SilverShop\HasOneField\HasOneAddExistingAutoCompleter;

class OpenGraphExtension extends SeoDataExtension
{
    private static $db = [
        'DisableDefaultOpenGraphObject' => 'Boolean',
    ];

    private static $has_one = [
        'OpenGraphObject' => OpenGraph::class,
    ];

    protected function seoFieldsTab(&$fields, $tab)
    {
        if ($this->owner->ID)
        {
            $fields->addFieldsToTab(
                $tab,
                [
                    $og = HasOneButtonField::create($this->owner, 'OpenGraphObject'),
                    CheckboxField::create('DisableDefaultOpenGraphObject', 'Disable default OG for this page'),
                ]
            );
        }

        // $og
        //     ->getConfig()
        //     ->getComponentByType(HasOneAddExistingAutoCompleter::class)
        //     ->setSearchList(OpenGraph::get());
    }
}
