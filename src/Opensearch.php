<?php

namespace Goldfinch\Seo;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;

class Opensearch extends RequestHandler
{
    public function index()
    {
        $response = new HTTPResponse('', 200);

        $response->addHeader('Content-Type', 'application/xml; charset="utf-8"');
        $response->addHeader('X-Robots-Tag', 'noindex');

        return $this->customise(new ArrayData([
          'BaseURL' => '',
        ]))->renderWith(__CLASS__);

        // return new HTTPResponse('Page not found', 404);
    }
}
