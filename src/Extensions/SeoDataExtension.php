<?php

namespace Goldfinch\Seo\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class SeoDataExtension extends DataExtension
{
    public function updateSettingsFields(FieldList $fields)
    {
        $tab = 'Root.SEO';

        if ($fields->findTab($tab))
        {
            $this->seoFieldsTab($fields, $tab);
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        if (!method_exists($this->owner, 'getSettingsFields'))
        {
            $tab = 'Root.Settings.SEO.SEO_Inner';

            if ($fields->findTab($tab))
            {
                $this->seoFieldsTab($fields, $tab);
            }
        }
    }
}
