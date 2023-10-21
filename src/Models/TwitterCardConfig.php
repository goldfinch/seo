<?php

namespace Goldfinch\Seo\Models;

use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class TwitterCardConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'TwitterCardConfig';

    private static $db = [];
}
