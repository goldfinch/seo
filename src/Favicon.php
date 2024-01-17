<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use Goldfinch\Seo\Models\ManifestConfig;
use SilverStripe\Control\RequestHandler;

class Favicon extends RequestHandler
{
    public function index()
    {
        $manifestCfg = ManifestConfig::current_config();

        if ($manifestCfg->IcoIcon && $manifestCfg->IcoIcon->exists()) {
            $path = PUBLIC_PATH . $manifestCfg->IcoIcon->getSourceURL();

            if (file_exists($path)) {
                $response = new HTTPResponse(file_get_contents($path), 200);
                $response->addHeader(
                    'Content-Type',
                    $manifestCfg->IcoIcon->getMimeType(),
                );
                return $response;
            }
        } else {
            // load local file if exists
            $path = PUBLIC_PATH . '/favicon.ico';

            if (file_exists($path)) {
                $response = new HTTPResponse(file_get_contents($path), 200);
                $response->addHeader(
                    'Content-Type',
                    $manifestCfg->IcoIcon->getMimeType(),
                ); // "image/x-icon"
                return $response;
            }
        }

        return new HTTPResponse('Page not found', 404);
    }
}
