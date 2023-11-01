<?php

namespace Goldfinch\Seo;

use Goldfinch\Seo\Models\RobotsConfig;
use TractorCow\Robots\Robots as Robots_Origin;

class Robots extends Robots_Origin
{
    public function index()
    {
        $response = parent::index();

        $body = $response->getBody();

        // custom
        $cfg = RobotsConfig::current_config();

        if ($cfg->CompleteRewrite)
        {
            $body = $cfg->CustomRules;
        }
        else if ($cfg->CustomRules)
        {
            $body .= $cfg->CustomRules;
        }

        $response->setBody($body);

        return $response;
    }
}
