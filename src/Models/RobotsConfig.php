<?php

namespace Goldfinch\Seo\Models;

use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class RobotsConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'RobotsConfig';

    private static $db = [
        'CustomRules' => 'Text',
        'CompleteRewrite' => 'Boolean',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('CompleteRewrite')->setDescription('If ticked, auto-generated rules will be completely overwritten and the <strong>Custom rules</strong> will be the rules specified above.');

        return $fields;
    }
}
