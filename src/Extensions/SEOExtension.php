<?php

namespace Goldfinch\Seo\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;

class SEOExtension extends Extension
{
    private static $casting = [
        'SchemaData' => DBHTMLText::class,
        'OpenGraph' => DBHTMLText::class,
    ];

    public function SchemaData()
    {
        // Schema
    }

    public function OpenGraph()
    {
        // OpenGraph
    }
}
