<?php

namespace Goldfinch\Seo\Forms\GridField;

use SilverStripe\Forms\GridField\GridFieldEditButton;

class GridFieldEditNoInlineButton extends GridFieldEditButton
{
    public function getExtraData($gridField, $record, $columnName)
    {
        return [
            // "classNames" => "font-icon-edit action-detail edit-link"
            "classNames" => "font-icon-edit action-detail"
        ];
    }
}
