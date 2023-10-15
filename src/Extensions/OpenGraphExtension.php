<?php

namespace Goldfinch\Seo\Extensions;

use Goldfinch\Seo\Models\OpenGraph;
use SilverShop\HasOneField\HasOneButtonField;
use SilverShop\HasOneField\GridFieldHasOneButtonRow;
// use SilverShop\HasOneField\HasOneAddExistingAutoCompleter;

class OpenGraphExtension extends SeoDataExtension
{
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
                ]
            );
        }

        // $og
        //     ->getConfig()
        //     ->getComponentByType(HasOneAddExistingAutoCompleter::class)
        //     ->setSearchList(OpenGraph::get());
    }
}
