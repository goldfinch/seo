<?php

namespace Goldfinch\Seo;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;

class Opensearch extends RequestHandler
{
    public function index()
    {
        $cfg = $this->config()->opensearch;

        if ($cfg['enable']) {
            $xml = $this->customise(
                new ArrayData([
                    'Title' => $cfg['title'],
                    'Encoding' => $cfg['encoding'],
                    'IcoIcon' => $cfg['icoIcon'],
                    'SearchURL' => $cfg['searchURL'],
                    'SuggestionURL' => $cfg['suggestionURL'],
                    'SearchForm' => $cfg['searchForm'],
                ]),
            )->renderWith('Goldfinch/Seo/xml-opensearch');

            $response = new HTTPResponse($xml, 200);

            $response->addHeader(
                'Content-Type',
                'application/xml; charset="utf-8"',
            );
            $response->addHeader('X-Robots-Tag', 'noindex');

            return $response;
        } else {
            return new HTTPResponse('Page not found', 404);
        }
    }
}
