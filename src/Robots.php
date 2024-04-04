<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\Director;
use Goldfinch\Seo\Models\RobotsConfig;
use TractorCow\Robots\Robots as Robots_Origin;

class Robots extends Robots_Origin
{
    public function index()
    {
        $response = parent::index();

        // $body = $response->getBody();
        $body = '';
        $body .= 'Sitemap: '.Director::absoluteBaseURL().'/sitemap.xml' . PHP_EOL;
        $body .='User-agent: *' . PHP_EOL;
        $body .= 'Disallow:' . PHP_EOL;

        // custom
        $cfg = RobotsConfig::current_config();

        if ($cfg->CompleteRewrite) {
            $body = $cfg->CustomRules;
        } elseif ($cfg->CustomRules) {
            $body .= $cfg->CustomRules;
        }

        $response->setBody($body);

        return $response;
    }
}
