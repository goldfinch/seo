<?php

namespace Goldfinch\Seo\Forms\GridField;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridFieldEditButton;

class GridFieldEditNoInlineButton extends GridFieldEditButton
{
    public function getExtraData($gridField, $record, $columnName)
    {
        return [
            // "classNames" => "font-icon-edit action-detail edit-link"
            'classNames' => 'font-icon-edit action-detail',
        ];
    }

    public function getUrl($gridField, $record, $columnName, $addState = true)
    {
        $link = Controller::join_links(
            $gridField->Link('item'),
            $record->ID,
            'edit',
        );

        if (
            $record->getClassName() == 'Page' ||
            $record->getClassName() == SiteTree::class ||
            get_parent_class($record) == SiteTree::class ||
            get_parent_class($record) == 'Page'
        ) {
            return str_replace('/edit/', '/settings/', $record->CMSEditLink()) .
                '#Root_SEO';
        } else {
            return $gridField->addAllStateToUrl($link, $addState) .
                '#Root_Settings_set';
        }
    }
}
