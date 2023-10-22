<?php

namespace Goldfinch\Seo\Extensions;

use Goldfinch\Seo\Models\TwitterCard;
use SilverShop\HasOneField\HasOneButtonField;
use SilverShop\HasOneField\GridFieldHasOneButtonRow;
// use SilverShop\HasOneField\HasOneAddExistingAutoCompleter;

class TwitterCardExtension extends SeoDataExtension
{
    private static $has_one = [
        'TwitterCardObject' => TwitterCard::class,
    ];

    protected function seoFieldsTab(&$fields, $tab)
    {
        if ($this->owner->ID)
        {
            $fields->addFieldsToTab(
                $tab,
                [
                    $og = HasOneButtonField::create($this->owner, 'TwitterCardObject'),
                ]
            );
        }

        // $og
        //     ->getConfig()
        //     ->getComponentByType(HasOneAddExistingAutoCompleter::class)
        //     ->setSearchList(OpenGraph::get());
    }
}
