<?php

namespace Goldfinch\Seo\Extensions;

use DateTime;
use BadMethodCallException;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use Astrotomic\OpenGraph\OpenGraph;
use Goldfinch\Seo\Models\MetaConfig;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\Security\Permission;
use Goldfinch\Seo\Models\SchemaConfig;
use SilverStripe\SiteConfig\SiteConfig;
use Goldfinch\Seo\Models\ManifestConfig;
use SilverStripe\Security\SecurityToken;
use Goldfinch\Seo\Models\OpenGraphConfig;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Goldfinch\Seo\Models\TwitterCardConfig;
use SilverStripe\CMS\Controllers\ContentController;
use Astrotomic\OpenGraph\StructuredProperties\Audio;
use Astrotomic\OpenGraph\StructuredProperties\Video;
use Astrotomic\OpenGraph\Types\Twitter\App as AppTC;
use Astrotomic\OpenGraph\Types\Twitter\Player as PlayerTC;
use Astrotomic\OpenGraph\Types\Twitter\Summary as SummaryTC;
use Astrotomic\OpenGraph\StructuredProperties\Audio as AudioOG;
use Astrotomic\OpenGraph\StructuredProperties\Image as ImageOG;
use Astrotomic\OpenGraph\StructuredProperties\Video as VideoOG;
use Astrotomic\OpenGraph\Types\Twitter\SummaryLargeImage as SummaryLargeImageTC;

class MetaUniverse extends Extension
{
    public $universeClass = 'Goldfinch\Seo\Traits\MetaUniverse';

    public function MetaUniverse()
    {
        return $this->owner
            ->customise([
                'CommonMeta' => $this->owner->uTagsFormatter(
                    $this->owner->uCommonMeta(),
                ),
                'SensitiveMeta' => $this->owner->uTagsFormatter(
                    $this->owner->uSensitiveMeta(),
                ),
                'CommonLinks' => $this->owner->uTagsFormatter(
                    $this->owner->uCommonLinks(),
                ),
            ])
            ->renderWith('Goldfinch/Seo/MetaUniverse');
    }

    public function MetaUniverseCached()
    {
        if (Director::isLive()) {
            $cacheKey = crypt(
                $this->owner->ID . get_class($this->owner),
                ss_env('APP_KEY'),
            );

            return $this->owner
                ->customise([
                    'CacheKey' => $cacheKey,
                    'URI' => $_SERVER['REQUEST_URI'],
                    'CommonMeta' => $this->owner->uTagsFormatter(
                        $this->owner->uCommonMeta(),
                    ),
                    'SensitiveMeta' => $this->owner->uTagsFormatter(
                        $this->owner->uSensitiveMeta(),
                    ),
                    'CommonLinks' => $this->owner->uTagsFormatter(
                        $this->owner->uCommonLinks(),
                    ),
                ])
                ->renderWith('Goldfinch/Seo/MetaUniverseCached');
        } else {
            return $this->MetaUniverse();
        }
    }

    public function uCommonMeta()
    {
        $html =
            $this->owner->metaBase() .
            $this->owner->metaTitle() .
            $this->owner->metaContentTypeCharset() .
            $this->owner->metaCompatible() .
            $this->owner->metaDnsPrefetchControl() .
            $this->owner->metaRefresh() .
            $this->owner->metaDates() .
            $this->owner->metaViewport() .
            $this->owner->metaReferrer() .
            $this->owner->metaLang() .
            $this->owner->metaRobots() .
            $this->owner->metaApplicationName() .
            $this->owner->metaIdentifierURL() .
            $this->owner->metaVerifications() .
            $this->owner->metaTheme() .
            $this->owner->metaRating() .
            $this->owner->metaNewsKeywords() . // only for article
            $this->owner->metaGeo() . // perhaps contact page only
            $this->owner->metaDescription() .
            $this->owner->metaCategory() . // for sites catalogs
            $this->owner->metaMobile() .
            $this->owner->metaFormatDetection() .
            $this->owner->metaAppleMobile() .
            $this->owner->metaWindowsPhone() .
            $this->owner->metaXCMS() .
            $this->owner->metaAuthor() .
            $this->owner->metaCopyright();

        return $html;

        // metaCategory
        // metaNewsKeywords
        // metaDates
        // linkAmphtml

        // linkSearch
    }

    public function uSensitiveMeta()
    {
        $html = $this->owner->metaCSRF();

        return $html;
    }

    public function uCommonLinks()
    {
        $html =
            $this->owner->OpenGraph() .
            $this->owner->TwitterCard() .
            $this->owner->linkHome() .
            $this->owner->linkCanonical() .
            $this->owner->linkShortlink() .
            $this->owner->linkSearch() .
            $this->owner->linkPreconnect() .
            $this->owner->linkAmphtml() .
            $this->owner->linkImageSrc() .
            $this->owner->linkAppleMobile() .
            $this->owner->linkIcons() .
            $this->owner->linkManifest() .
            $this->owner->linkHumans() .
            PHP_EOL .
            $this->owner->SchemaData();

        return $html;
    }

    public function uTagsFormatter($html)
    {
        $output = DBHTMLText::create();

        $html = preg_replace(['/\s{2,}/', '/\n/'], PHP_EOL, $html);
        $html = preg_replace('/^[ \t]*[\r\n]+/m', '', $html);

        $tags = explode(PHP_EOL, $html);

        if ($space = Environment::getEnv('APP_META_SOURCESPACE')) {
            $spacing = '';

            for ($i = 0; $space > $i; $i++) {
                $spacing .= ' ';
            }
        } else {
            // 4 space by default
            $spacing = '    ';
        }

        $html = '';

        foreach ($tags as $key => $tag) {
            if ($key !== 0) {
                $html .= $spacing . $tag . PHP_EOL;
            } else {
                $html .= $tag . PHP_EOL;
            }
        }

        $output->setValue($html);

        return $output;
    }

    public function metaBase()
    {
        if (!ss_config($this->universeClass, 'headrules', 'base')) {
            return;
        }

        // <!--[if lte IE 6]></base><![endif]-->
        $output =
            '
        <base href="' .
            Director::absoluteBaseURL() .
            '">
        ';

        return $output;
    }

    /**
     * Category of the page (for sites catalogs)
     */
    public function metaCategory()
    {
        if (!ss_config($this->universeClass, 'headrules', 'category')) {
            return;
        }

        $output = '
        <meta name="category" content="">
        ';

        return $output;
    }

    /**
     * To enable/disable DNS prefetching
     *
     * By default, Chromium does not prefetch host names in hyperlinks that appear in HTTPS pages. This   * restriction helps prevent an eavesdropper from inferring the host names of hyperlinks that appear * in HTTPS pages based on DNS prefetch traffic. The one exception is that Chromium may periodically * re-resolve the domain of the HTTPS page itself. — Source: chromium.org
     *
     * As stated in this MDN entry, you can enable/disable this functionality in Chrome and Firefox
     * by adding a X-DNS-Prefetch-Control header. It can also be set up through a meta tag:
     * <meta http-equiv="x-dns-prefetch-control" content="on">.
     *
     * This option should only be enabled if we know for sure it will not interfere with the user
     * event tracking system. Otherwise users might be registered for visiting links they have
     * not clicked on.
     */
    public function metaDnsPrefetchControl()
    {
        if (
            !ss_config(
                $this->universeClass,
                'headrules',
                'dns-prefetch-control',
            )
        ) {
            return;
        }

        $output = '
        <meta http-equiv="x-dns-prefetch-control" content="on">
        ';

        return $output;
    }

    public function metaCompatible()
    {
        if (!ss_config($this->universeClass, 'headrules', 'compatible')) {
            return;
        }
        // imagetoolbar - This is an IE specific meta, In some older versions of Internet Explorer, when an image is hovered, an image toolbar appears. content=no used to disable the image toolbar.
        // "IE=edge" indicates that the webpage should be displayed in the latest version of IE available
        // <meta http-equiv="Page-Enter" content="RevealTrans(Duration=2.0,Transition=2)">
        // <meta http-equiv="Page-Exit" content="RevealTrans(Duration=3.0,Transition=12)">
        // <meta name="Classification" content="Business">
        // <meta name="HandheldFriendly" content="True">
        // <meta name="MobileOptimized" content="320">
        $output = '
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta http-equiv="imagetoolbar" content="no">
        ';

        return $output;
    }

    public function metaLang()
    {
        if (!ss_config($this->universeClass, 'headrules', 'language')) {
            return;
        }
        // <meta http-equiv="content-language" content="language–Country"> - Enables language specification, enabling search engines to accurately categorise the document into language and country. The language is the main language code, and the country is the country where the dialect of the language is more specific, such as en-US versus en-GB
        // <meta http-equiv="content-script-type" content="language“> - The default script language for the script element is javascript. This informs the browser which type of scripting language you are using by default.
        // <meta name="google" content="notranslate">
        // <meta name="locale" content="en-NZ">
        $output = '';

        if (ss_env('APP_META_LOCALE')) {
            $output .=
                '
            <meta name="language" content="' .
                ss_env('APP_META_LOCALE') .
                '">
            ';
        }

        return $output;
    }

    /**
     * Only for article
     */
    public function metaNewsKeywords()
    {
        if (!ss_config($this->universeClass, 'headrules', 'news_keywords')) {
            return;
        }

        $output = '
        <meta name="news_keywords" content="">
        ';

        return $output;
    }

    /**
     * Include the date and time that the page was created.
     * The content is the date and time the page was last modified.
     */
    public function metaDates()
    {
        if (!ss_config($this->universeClass, 'headrules', 'dates')) {
            return;
        }

        $output = '
        <meta http-equiv="date" content="date">
        <meta http-equiv="last-modified" content="date">
        ';

        return $output;
    }

    /**
     * URL of your site. This tag is not obligatory. If you use it, put the same value in all the pages of your site.
     */
    public function metaIdentifierURL()
    {
        if (!ss_config($this->universeClass, 'headrules', 'identifier-URL')) {
            return;
        }

        $output =
            '
        <meta name="identifier-URL" content="' .
            Director::absoluteBaseURL() .
            '">
        ';

        return $output;
    }

    /**
     * (Here IN represents the country and DL represents the state. It is good to define geo details clearly)
     * (Here you can put the City name which you wanted to represent)
     * (put the city coordinates)
     * (add the location coordinates here)
     *
     * There is some speculation that it might possibly be used for Google local search optimization, which falls outside of regular organic search SEO.  And some SEOs simply add it for a “cover all bases" approach to optimization.   And other search engines may also use the geo meta tags, even if Google does not.
     */
    public function metaGeo()
    {
        if (!ss_config($this->universeClass, 'headrules', 'geo')) {
            return;
        }

        // 00.000000;00.000000
        // IN-DL
        // Delhi

        $output = '';

        $cfg = MetaConfig::current_config();

        if ($cfg->GeoPosition) {
            $geoPosition = $cfg->GeoPosition;
        } elseif (ss_env('APP_SEO_GEO_POSITION')) {
            $geoPosition = ss_env('APP_SEO_GEO_POSITION');
        }

        if ($cfg->GeoRegion) {
            $geoRegion = $cfg->GeoRegion;
        } elseif (ss_env('APP_SEO_GEO_REGION')) {
            $geoRegion = ss_env('APP_SEO_GEO_REGION');
        }

        if ($cfg->GeoPlacename) {
            $geoPlacename = $cfg->GeoPlacename;
        } elseif (ss_env('APP_SEO_GEO_PLACENAME')) {
            $geoPlacename = ss_env('APP_SEO_GEO_PLACENAME');
        }

        if (isset($geoPosition)) {
            $output .=
                '
            <meta name="ICBM" content="' .
                str_replace(';', ',', $geoPosition) .
                '">
            <meta name="geo.position" content="' .
                $geoPosition .
                '">
            ';
        }

        if (isset($geoRegion)) {
            $output .=
                '
            <meta name="geo.region" content="' .
                $geoRegion .
                '">
            ';
        }

        if (isset($geoPlacename)) {
            $output .=
                '
            <meta name="geo.placename" content="' .
                $geoPlacename .
                '">
            ';
        }

        return $output;
    }

    /**
     * - This meta tag is often used to let the younger web-surfers know the content is appropriate. If you use this tag the wrong way (call an adult website safe for kids is bad!) you will get banned for life.
     * - general / mature / restricted / adult / 14 years / safe for kids
     */
    public function metaRating()
    {
        if (!ss_config($this->universeClass, 'headrules', 'rating')) {
            return;
        }
        // <meta http-equiv="pics-label" content="labellist"> - The Platform for Internet Content Selection (PICS) is a standard for labelling online content: basically online content rating.
        // <meta name="rating" content="RTA-5042-1996-1400-1577-RTA">
        // general, mature, restricted, adult, 14 years, safe for kids

        $output = '';

        $cfg = MetaConfig::current_config();

        if ($cfg->Rating) {
            $rating = $cfg->Rating;
        } elseif (ss_env('APP_SEO_RATING')) {
            $rating = ss_env('APP_SEO_RATING');
        }

        if (isset($rating)) {
            $output .=
                '
            <meta name="rating" content="' .
                $rating .
                '">
            ';
        }

        return $output;
    }

    public function metaMobile()
    {
        if (
            !ss_config(
                $this->universeClass,
                'headrules',
                'mobile-web-app-capable',
            )
        ) {
            return;
        }
        // Since Chrome M31, you can set up your web app to have an application shortcut icon added to a device's homescreen, and have the app launch in full-screen "app mode" using Chrome for Android’s "Add to homescreen" menu item.
        $output = '
        <meta name="mobile-web-app-capable" content="yes">
        ';

        return $output;
    }

    public function metaRefresh()
    {
        if (!ss_config($this->universeClass, 'headrules', 'refresh')) {
            return;
        }
        // The refresh meta tag is used to refresh a document. It’s useful if your page uses dynamic content that changes constantly. In the example below, your page will be automatically refreshed every 30 seconds:
        // Redirect page after 3 seconds: <meta http-equiv="refresh" content="3;url=https://www.mozilla.org">
        $output = '
        <meta http-equiv="refresh" content="30">
        ';

        return $output;
    }

    /**
     * Apple Specific Meta Tags
     *
     * apple-mobile-web-app-title - On iOS, you can specify a web application title for the launch icon. By default, the <title> tag is used. To set a different title, add this meta tag to the webpage
     *
     * apple-mobile-web-app-capable : On iOS, as part of optimising your web application, have it use the standalone mode to look more like a native application. When you use this standalone mode, Safari is not used to display the web content — specifically, there is no browser URL text field at the top of the screen or button bar at the bottom of the screen. Only a status bar appears at the top of the screen.
     *
     * apple-mobile-web-app-status-bar-style : <!-- black | black-translucent | default --> If your web application displays in standalone mode like that of a native application, you can minimise the status bar that is displayed at the top of the screen on iOS.
     *
     */
    public function metaAppleMobile()
    {
        if (!ss_config($this->universeClass, 'headrules', 'apple-webapp')) {
            return;
        }

        $cfg = SiteConfig::current_site_config();

        $output = '';

        if (ss_env('APP_APPLE_WEBAPP_TITLE') || $cfg->Title) {
            $output .=
                '
            <meta name="apple-mobile-web-app-title" content="' .
                (ss_env('APP_APPLE_WEBAPP_TITLE') ?? $cfg->Title) .
                '">
            ';
        }

        if (ss_env('APP_APPLE_WEBAPP_CAPABLE')) {
            // yes|no
            $output .=
                '
            <meta name="apple-mobile-web-app-capable" content="' .
                ss_env('APP_APPLE_WEBAPP_CAPABLE') .
                '">
            ';
        }

        if (ss_env('APP_APPLE_WEBAPP_STATUS_BAR_STYLE')) {
            // black | black-translucent | default
            $output .=
                '
            <meta name="apple-mobile-web-app-status-bar-style" content="' .
                ss_env('APP_APPLE_WEBAPP_STATUS_BAR_STYLE') .
                '">
            ';
        }

        return $output;
    }

    /**
     * When running in a browser on a mobile phone, this meta determines whether or not telephone numbers in the HTML content will appear as hypertext links. The user can click a link with a telephone number to initiate a phone call to that phone number.
     */
    public function metaFormatDetection()
    {
        if (!ss_config($this->universeClass, 'headrules', 'format-detection')) {
            return;
        }

        $output = '
        <meta name="format-detection" content="telephone=no">
        ';

        return $output;
    }

    public function metaWindowsPhone()
    {
        if (!ss_config($this->universeClass, 'headrules', 'msapplication')) {
            return;
        }

        // notification : frequency=30; polling-uri=;
        // window : width=1024;height=768
        // badge : frequency=30; polling-uri=https://demo.com/q/sitemap.xml
        // config : ../browserconfig.xml
        // task : name=Search;action-uri=https://demo.com/search.ico

        $baseCfg = SiteConfig::current_site_config();

        $metaCfg = MetaConfig::current_config();

        $output = '';

        $output .=
            '
        <meta name="msapplication-starturl" content="' .
            Director::absoluteBaseURL() .
            '">
        <meta name="msapplication-navbutton-color" content="#ffffff">
        <meta name="msapplication-tooltip" content="' .
            $baseCfg->Title .
            '">
        <meta name="msapplication-tap-highlight" content="no" />
        ';

        if ($metaCfg->MsapplicationTileColor) {
            $output .=
                '
            <meta name="msapplication-TileColor" content="' .
                $metaCfg->MsapplicationTileColor .
                '">
            ';
        }

        if (
            $metaCfg->MsapplicationBackgroundImage &&
            $metaCfg->MsapplicationBackgroundImage->exists()
        ) {
            $url = $metaCfg->MsapplicationBackgroundImage
                ->FocusFill(500, 500)
                ->getAbsoluteURL();

            $output .=
                '
            <meta name="msapplication-TileImage" content="' .
                $url .
                '">
            ';
        }

        if (
            $metaCfg->MsapplicationTileImage &&
            $metaCfg->MsapplicationTileImage->exists()
        ) {
            $url70x70 = $metaCfg->MsapplicationTileImage
                ->FocusFill(70, 70)
                ->getAbsoluteURL();
            $url150x150 = $metaCfg->MsapplicationTileImage
                ->FocusFill(150, 150)
                ->getAbsoluteURL();
            $url310x310 = $metaCfg->MsapplicationTileImage
                ->FocusFill(310, 310)
                ->getAbsoluteURL();
            $url310x150 = $metaCfg->MsapplicationTileImage
                ->FocusFill(310, 150)
                ->getAbsoluteURL();

            $output .=
                '
            <meta name="msapplication-square70x70logo" content="' .
                $url70x70 .
                '">
            <meta name="msapplication-square150x150logo" content="' .
                $url150x150 .
                '">
            <meta name="msapplication-square310x310logo" content="' .
                $url310x310 .
                '">
            <meta name="msapplication-wide310x150logo" content="' .
                $url310x150 .
                '">
            ';
        }

        // TODO
        // $output .= '
        // <meta name="msapplication-config" content="browserconfig.xml">
        // <meta name="msapplication-window" content="width=1024;height=768">
        // <meta name="msapplication-notification" content="frequency=30; polling-uri=;">
        // <meta name="msapplication-badge" content="frequency=30; polling-uri=">
        // ';

        // $output .= '
        // <meta name="msapplication-task" content="">
        // <meta name="msapplication-allowDomainMetaTags" content="true">
        // <meta name="msapplication-allowDomainApiCalls" content="true">
        // ';

        return $output;
    }

    public function metaAuthor()
    {
        if (!ss_config($this->universeClass, 'headrules', 'author')) {
            return;
        }
        $output = '';

        if (ss_env('APP_SEO_AUTHOR')) {
            $output =
                '
            <meta name="author" content="' .
                ss_env('APP_SEO_AUTHOR') .
                '">
            ';
        }

        return $output;
    }

    public function metaCopyright()
    {
        if (!ss_config($this->universeClass, 'headrules', 'copyright')) {
            return;
        }

        $output = '';

        if (ss_env('APP_SEO_COPYRIGHT')) {
            $output =
                '
            <meta name="copyright" content="' .
                ss_env('APP_SEO_COPYRIGHT') .
                '">
            ';
        }

        return $output;
    }

    /**
     * https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag#directives
     */
    public function metaRobots()
    {
        if (!ss_config($this->universeClass, 'headrules', 'robots')) {
            return;
        }

        if ($this->owner->ClassName == ErrorPage::class) {
            $output = '
            <meta name="robots" content="noindex, follow">
            ';
        } else {
            // <meta name="robots" content="index, follow">
            // <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
            // <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

            $output =
                '
            <meta name="robots" content="' .
                ss_env('APP_SEO_ROBOTS_BASE') .
                '">
            ';
        }

        return $output;
    }

    public function metaReferrer()
    {
        if (!ss_config($this->universeClass, 'headrules', 'referrer')) {
            return;
        }

        // <meta name="referrer" content="origin-when-cross-origin">
        $output = '
        <meta name="referrer" content="no-referrer-when-downgrade">
        ';

        return $output;
    }

    public function metaApplicationName()
    {
        if (!ss_config($this->universeClass, 'headrules', 'application-name')) {
            return;
        }

        $cfg = SiteConfig::current_site_config();

        $output =
            '
        <meta name="application-name" content="' .
            $cfg->Title .
            '">
        ';

        return $output;
    }

    public function metaContentTypeCharset()
    {
        if (
            !ss_config(
                $this->universeClass,
                'headrules',
                'content-type-charset',
            )
        ) {
            return;
        }

        $output = '
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        ';

        return $output;
    }

    /**
     * - Google typically shows 55-64 characters (keep it under 60).
     * - Keep important keywords within the first 8 words
     */
    public function metaTitle()
    {
        if (!ss_config($this->universeClass, 'headrules', 'title')) {
            return;
        }

        $cfg = SiteConfig::current_site_config();

        if ($this->owner->MetaTitle) {
            $title = $this->owner->MetaTitle;
        } else {
            $title = $this->owner->Title . ' - ' . $cfg->Title;
        }

        $output =
            '
        <title>' .
            $title .
            '</title>
        ';

        return $output;
    }

    public function metaVerifications()
    {
        if (!ss_config($this->universeClass, 'headrules', 'verifications')) {
            return;
        }

        $output = '';

        if (ss_env('APP_SEO_BING_CONSOLE')) {
            $output .=
                '
            <meta name="google-site-verification" content="' .
                ss_env('APP_SEO_GOOGLE_CONSOLE') .
                '">
            ';
        }

        if (ss_env('APP_SEO_BING_CONSOLE')) {
            $output .=
                '
            <meta name="msvalidate.01" content="' .
                ss_env('APP_SEO_BING_CONSOLE') .
                '">
            ';
        }

        if (ss_env('APP_SEO_YANDEX')) {
            $output .=
                '
            <meta name="yandex-verification" content="' .
                ss_env('APP_SEO_YANDEX') .
                '" />
            ';
        }

        if (ss_env('APP_SEO_PINTEREST')) {
            $output .=
                '
            <meta name="p:domain_verify" content="' .
                ss_env('APP_SEO_PINTEREST') .
                '">
            ';
        }

        return $output;
    }

    public function metaCSRF()
    {
        if (!ss_config($this->universeClass, 'headrules', 'csrf')) {
            return;
        }

        $output =
            '
        <meta name="csrf-param" content="authenticity_token">
        <meta name="csrf-token" content="' .
            SecurityToken::getSecurityID() .
            '">
        ';

        return $output;
    }

    /**
     * - Google typically shows max. 165 characters (keep it under 160).
     * ~ 155
     */
    public function metaDescription()
    {
        if (!ss_config($this->universeClass, 'headrules', 'description')) {
            return;
        }

        $output =
            '
        <meta name="description" content="' .
            strip_tags($this->owner->MetaDescription ?? '') .
            '">
        ';

        return $output;
    }

    public function metaTheme()
    {
        if (!ss_config($this->universeClass, 'headrules', 'theme')) {
            return;
        }

        // color-scheme : Status & Address Bar
        // theme-color : Chrome, Firefox OS and Opera
        // <meta name="theme-color" media="(prefers-color-scheme: light)" content="#F6F7F8">
        // <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#111317">
        $output = '';

        if (ss_env('APP_COLOR_SCHEME')) {
            $output .=
                '
            <meta name="color-scheme" content="' .
                ss_env('APP_COLOR_SCHEME') .
                '">
            ';
        }

        if (ss_env('APP_THEME_COLOR')) {
            $output .=
                '
            <meta name="theme-color" content="' .
                ss_env('APP_THEME_COLOR') .
                '">
            ';
        }

        return $output;
    }

    /**
     * #width : Logical width of the viewport, in pixels. The special device-width value indicates that the viewport width should be the screen width of the device.
     * #height : Logical height of the viewport, in pixels. The special device-width value indicates that the viewport height should be the screen width of the device.
     * #user-scalable : Indicates whether the user can zoom in and out of the viewport, scaling the display of a web page. Possible values are yes of no.
     * #initial-scale : Sets the first scaling or zoom out factor (or multiplier) and can be used to view a web page. A value of 1.0 shows an unscaled web document.
     * #maximum-scale : Sets a maximum limit for the user to scale and zoom a webpage. Values are numeric and can range from 0.25 to 10.0.
     * #minimum-scale : Limits the user to a minimum for enlarging or zooming in on a webpage. Values are numeric and can be from 0.25 to 10.0.
     *
     * Prevent shrink: 'shrink-to-fit=no'
     *
     */

    public function metaViewport()
    {
        if (!ss_config($this->universeClass, 'headrules', 'viewport')) {
            return;
        }

        $content = [
            'width=device-width',
            'initial-scale=1.0',
            'viewport-fit=cover',
            'user-scalable=yes',
        ];

        $output =
            '
        <meta name="viewport" content="' .
            implode(', ', $content) .
            '">
        ';

        return $output;
    }

    // vendor/silverstripe/cms/code/Model/SiteTree.php # MetaComponents
    public function metaXCMS()
    {
        if (!ss_config($this->universeClass, 'headrules', 'x-cms')) {
            return;
        }

        $output = '';

        if (Permission::check('CMS_ACCESS_CMSMain') && $this->owner->ID > 0) {
            $output =
                '
            <meta name="x-page-id" content="' .
                $this->owner->ID .
                '">
            ';

            try {
                $output .=
                    '
                <meta name="x-cms-edit-link" content="' .
                    $this->owner->CMSEditLink() .
                    '">
                ';
            } catch (BadMethodCallException $e) {
            }

            try {
                $output .=
                    '
                <meta name="x-cms-logout-link" content="' .
                    $this->owner->LogoutURL() .
                    '">
                ';
            } catch (BadMethodCallException $e) {
            }
        }

        return $output;
    }

    public function linkSearch()
    {
        if (!ss_config($this->universeClass, 'headrules', 'search')) {
            return;
        }

        // href : /__static__/semrush-opensearch.xml / https://web.dev/s/opensearch.xml / https://open.spotifycdn.com/cdn/generated/opensearch.4cd8879e.xml
        $output = '
        <link rel="search" type="application/opensearchdescription+xml" title="" href="">
        ';

        return $output;
    }

    public function linkImageSrc()
    {
        if (!ss_config($this->universeClass, 'headrules', 'image_src')) {
            return;
        }

        $output = '';

        $cfg = MetaConfig::current_config();

        if ($cfg->ImageSRC && $cfg->ImageSRC->exists()) {
            $width = 1200;
            $height = 630;

            $link = $cfg->ImageSRC
                ->FocusFill($width, $height)
                ->getAbsoluteURL();

            $output =
                '
            <link rel="image_src" href="' .
                $link .
                '" type="' .
                $cfg->ImageSRC->getMimeType() .
                '">
            ';
        }

        return $output;
    }

    public function linkHumans()
    {
        if (!ss_config($this->universeClass, 'headrules', 'humans')) {
            return;
        }

        $output = '
        <link rel="author" href="/humans.txt" type="text/plain">
        ';

        return $output;
    }

    public function linkAppleMobile()
    {
        if (!ss_config($this->universeClass, 'headrules', 'apple-touch')) {
            return;
        }

        $output = '
        <link rel="apple-touch-startup-image" href="">
        ';

        return $output;
    }

    public function linkHome()
    {
        if (!ss_config($this->universeClass, 'headrules', 'home')) {
            return;
        }

        $output =
            '
        <link rel="home" href="' .
            Director::absoluteBaseURL() .
            '">
        ';

        return $output;
    }

    public function linkShortlink()
    {
        if (!ss_config($this->universeClass, 'headrules', 'shortlink')) {
            return;
        }

        $output =
            '
        <link rel="shortlink" href="' .
            Director::absoluteURL($_SERVER['REQUEST_URI']) .
            '">
        ';

        return $output;
    }

    /**
     * preconnect links can be declared followed by a dns-prefetch link so that if the first one
     * is not supported, time can be saved for the DNS resolution of the origin
     *
     * first preconnect, second dns-prefetch
     *
     * If our resource is going to be fetched through a CORS connection using anonymous mode,
     * we need to add the attribute crossorigin to the link tag:
     * <link rel="preconnect" href="https://cdn.vrbo.com" crossorigin>
     *
     * no more than 4-6 domains is recommended in `preconnect` and we can still use `dns-prefetch`
     * for other connections.
     *
     * dns-prefetch is only effective for DNS lookups on cross-origin domains, so avoid using it to
     * point to your site or domain. This is because the IP behind your site’s domain will have
     * already been resolved by the time the browser sees the hint.
     *
     *
     */
    public function linkPreconnect()
    {
        if (!ss_config($this->universeClass, 'headrules', 'preconnect')) {
            return;
        }

        $output = '';

        $defaults = [];

        $cfg = ss_config($this->universeClass);

        foreach ($cfg['preconnect'] as $link => $state) {
            if (!$state) {
                continue;
            }

            $crossorigin = '';

            if (
                isset($cfg['crossorigin']) &&
                isset($cfg['crossorigin'][$link]) &&
                $cfg['crossorigin'][$link]
            ) {
                $crossorigin = ' crossorigin';
            }

            $output .=
                '
            <link rel="preconnect" href="' .
                $link .
                '"' .
                $crossorigin .
                '>
            ';
        }

        foreach ($cfg['dnsprefetch'] as $link => $state) {
            if (!$state) {
                continue;
            }

            $output .=
                '
            <link rel="dns-prefetch" href="' .
                $link .
                '">
            ';
        }

        return $output;
    }

    public function linkCanonical()
    {
        if (!ss_config($this->universeClass, 'headrules', 'canonical')) {
            return;
        }

        $output =
            '
        <link rel="canonical" href="' .
            Director::absoluteURL($_SERVER['REQUEST_URI']) .
            '">
        ';

        return $output;
    }

    public function linkAmphtml()
    {
        if (!ss_config($this->universeClass, 'headrules', 'amphtml')) {
            return;
        }

        $output = '
        <link rel="amphtml" href="">
        ';

        return $output;
    }

    /**
     * - mask-icon : Safari 9 Pinned tabs. Pinned Sites allow your users to keep their favorite websites open, running, and easily accessible. You can set the icon that the user sees when they pin your site by providing a vector image.
     *
     *
     * - 512x512 (png)
     * - 192x192 (png)
     * - 512x512 (svg)
     * - 32x32 (ico)
     * - apple-touch-icon : 180×180
     * -
     *
     * The request for the manifest is made without credentials (even if it's on the same domain), thus if the manifest requires credentials,
     * you must include crossorigin="use-credentials" in the manifest tag.
     */
    public function linkIcons()
    {
        if (!ss_config($this->universeClass, 'headrules', 'icon')) {
            return;
        }

        $output = '';

        $cfg = ManifestConfig::current_config();

        if ($cfg->IcoIcon->exists()) {
            $url = $cfg->IcoIcon->getAbsoluteURL();

            $output .=
                '
            <link rel="shortcut icon" href="' .
                $url .
                '">
            <link rel="icon" href="' .
                $url .
                '" sizes="32x32">
            ';
        }

        if ($cfg->VectorIcon->exists()) {
            $url = $cfg->VectorIcon->getAbsoluteURL();

            $output .=
                '
            <link rel="mask-icon" href="' .
                $url .
                '" color="' .
                ss_env('APP_THEME_COLOR') .
                '">
            <link rel="icon" href="' .
                $url .
                '" type="image/svg+xml">
            ';
        }

        if ($cfg->PortableImage->exists()) {
            $url = $cfg->PortableImage->FocusFill(180, 180)->getAbsoluteURL();

            $output .=
                '
            <link rel="apple-touch-icon" href="' .
                $url .
                '">
            ';
        }

        return $output;
    }

    public function linkManifest()
    {
        if (!ss_config($this->universeClass, 'headrules', 'manifest')) {
            return;
        }

        $output = '
        <link rel="manifest" href="/manifest.webmanifest">
        ';

        return $output;
    }

    public function OpenGraph()
    {
        // TODO
        // OG video
        // $graph
        //     ->video('ww')
        //     ->video(
        //         Video::make('ww')
        //             ->secureUrl('ss')
        //             video/mp4, video/mpeg, video/webm, video/ogg
        //             ->mimeType('application/x-shockwave-flash')
        //             ->width(100)
        //             ->height(200)
        //             // ->alt('sss')
        //     );

        // // OG audio
        // $graph
        //     ->audio('https://example.com/image1.jpg')
        //     ->audio(
        //         Audio::make('https://example.com/image2.jpg')
        //             ->secureUrl('ss')
        //             ->mimeType('ss')
        //     );
        $ogCfg = OpenGraphConfig::current_config();

        if (
            $this->owner->OpenGraphObject &&
            $this->owner->OpenGraphObject->exists()
        ) {
            $og = $this->owner->OpenGraphObject;
        } elseif (
            !$this->owner->DisableDefaultOpenGraphObject &&
            $ogCfg->DefaultObject &&
            $ogCfg->DefaultObject->exists()
        ) {
            $og = $ogCfg->DefaultObject;
        }

        if (isset($og)) {
            $baseCfg = SiteConfig::current_site_config();

            if ($og->OG_Type == 'website') {
                $graph = OpenGraph::website($og->OG_Title ?? $baseCfg->Title);
            } elseif ($og->OG_Type == 'profile') {
                $graph = OpenGraph::profile($og->OG_Title ?? $baseCfg->Title);

                if ($og->OG_Profile_FirstName) {
                    $graph->firstName($og->OG_Profile_FirstName);
                }
                if ($og->OG_Profile_LastName) {
                    $graph->lastName($og->OG_Profile_LastName);
                }
                if ($og->OG_Profile_Username) {
                    $graph->username($og->OG_Profile_Username);
                }
                if ($og->OG_Profile_Gender) {
                    $graph->gender($og->OG_Profile_Gender);
                }
            } elseif ($og->OG_Type == 'article') {
                $graph = OpenGraph::article($og->OG_Title ?? $baseCfg->Title);

                // TODO
                // if ($og->OG_Article_Author) $graph->author($og->OG_Article_Author);
                if ($og->OG_Article_PublishedTime) {
                    $graph->publishedAt(
                        new DateTime($og->OG_Article_PublishedTime),
                    );
                }
                if ($og->OG_Article_ModifiedTime) {
                    $graph->modifiedAt(
                        new DateTime($og->OG_Article_ModifiedTime),
                    );
                }
                if ($og->OG_Article_ExpirationTime) {
                    $graph->expiresAt(
                        new DateTime($og->OG_Article_ExpirationTime),
                    );
                }
                if ($og->OG_Article_Section) {
                    $graph->section($og->OG_Article_Section);
                }
                // TODO
                // if ($og->OG_Article_Tags) $graph->tag($og->OG_Article_Tags);
                // ->tag('tag1')
                // ->tag('tag2')
            }

            if ($og->OG_Images() && $og->OG_Images()->Count()) {
                foreach ($og->OG_Images() as $image) {
                    $ogImage = null;

                    if ($image->OG_Image_Width && $image->OG_Image_Height) {
                        $width = $image->OG_Image_Width;
                        $height = $image->OG_Image_Height;
                    } else {
                        $width = 1200;
                        $height = 630;
                    }

                    $link = $image
                        ->FocusFill($width, $height)
                        ->getAbsoluteURL();

                    $ogImage = ImageOG::make($link)
                        ->secureUrl($link)
                        ->mimeType($image->getMimeType())
                        ->width($width)
                        ->height($height);

                    if ($image->OG_Image_Alt || $image->Title) {
                        $ogImage->alt($image->OG_Image_Alt ?? $image->Title);
                    }

                    $graph->image($ogImage);
                }
            } elseif ($ogCfg->DefaultImage && $ogCfg->DefaultImage->exists()) {
                $image = $ogCfg->DefaultImage;

                $width = 1200;
                $height = 630;

                $link = $image->FocusFill($width, $height)->getAbsoluteURL();

                $ogImage = ImageOG::make($link)
                    ->secureUrl($link)
                    ->mimeType($image->getMimeType())
                    ->width($width)
                    ->height($height);

                if ($image->Title) {
                    $ogImage->alt($image->Title);
                }

                $graph->image($ogImage);
            }

            // Optional & Basic meta (for all types)
            $graph->url($og->OG_Url ?? Director::absoluteBaseURL());
            $graph->siteName($og->OG_SiteName ?? $baseCfg->Title);
            if ($og->OG_Description) {
                $graph->description($og->OG_Description);
            }
            if ($og->OG_Determiner) {
                $graph->determiner($og->OG_Determiner);
            }

            if ($og->OG_Locale) {
                $graph->locale($og->OG_Locale);
            } elseif ($ogCfg->OG_Locale) {
                $graph->locale($ogCfg->OG_Locale);
            }

            // TODO
            // $graph->alternateLocale('ss')
            // ->alternateLocale('en_GB')

            if ($og->FB_AppID) {
                $graph->setProperty('fb', 'app_id', $og->FB_AppID);
            } elseif ($ogCfg->FB_AppID) {
                $graph->setProperty('fb', 'app_id', $ogCfg->FB_AppID);
            }

            // a wire to page elements (blocks)
            if (
                $this->owner->ElementalArea &&
                $this->owner->ElementalArea->exists()
            ) {
                foreach ($this->owner->ElementalArea->Elements() as $element) {
                    try {
                        $element->updateOpenGraph($graph);
                    } catch (BadMethodCallException $exception) {
                    }
                }
            }

            // additional check for controllers
            if (is_subclass_of($this->owner, ContentController::class)) {
                if (method_exists($this->owner, 'updateOpenGraph')) {
                    $this->owner->updateOpenGraph($graph);
                }
            }

            // a wire to parent page
            $this->updateOpenGraph($graph);

            return '
            ' . $graph->__toString();
        }
    }

    public function TwitterCard()
    {
        $tcCfg = TwitterCardConfig::current_config();

        if (
            $this->owner->TwitterCardObject &&
            $this->owner->TwitterCardObject->exists()
        ) {
            $tc = $this->owner->TwitterCardObject;
        } elseif (
            !$this->owner->DisableDefaultTwitterCardObject &&
            $tcCfg->DefaultObject &&
            $tcCfg->DefaultObject->exists()
        ) {
            $tc = $tcCfg->DefaultObject;
        }

        if (isset($tc)) {
            $baseCfg = SiteConfig::current_site_config();

            if ($tc->TC_Type == 'summary') {
                $graph = SummaryTC::make($tc->TC_Title ?? $baseCfg->Title);

                if ($tc->TC_Description) {
                    $graph->description($tc->TC_Description);
                }

                if ($tc->TC_SiteID) {
                    $graph->setProperty('twitter', 'site:id', $tc->TC_SiteID);
                } elseif ($tcCfg->TC_SiteID) {
                    $graph->setProperty(
                        'twitter',
                        'site:id',
                        $tcCfg->TC_SiteID,
                    );
                }

                if ($tc->TC_CreatorID) {
                    $graph->setProperty(
                        'twitter',
                        'creator:id',
                        $tc->TC_CreatorID,
                    );
                }
            } elseif ($tc->TC_Type == 'summary_large_image') {
                $graph = SummaryLargeImageTC::make(
                    $tc->TC_Title ?? $baseCfg->Title,
                );
                if ($tc->TC_Creator) {
                    $graph->creator($tc->TC_Creator);
                }

                if ($tc->TC_CreatorID) {
                    $graph->setProperty(
                        'twitter',
                        'creator:id',
                        $tc->TC_CreatorID,
                    );
                }

                if ($tc->TC_SiteID) {
                    $graph->setProperty('twitter', 'site:id', $tc->TC_SiteID);
                } elseif ($tcCfg->TC_SiteID) {
                    $graph->setProperty(
                        'twitter',
                        'site:id',
                        $tcCfg->TC_SiteID,
                    );
                }

                if ($tc->TC_Description) {
                    $graph->description($tc->TC_Description);
                }
            } elseif ($tc->TC_Type == 'app') {
                $graph = AppTC::make($tc->TC_Title ?? $baseCfg->Title);

                if ($tc->TC_AppNameIphone && $tc->TC_AppIdIphone) {
                    $graph->iPhoneApp(
                        $tc->TC_AppNameIphone,
                        $tc->TC_AppIdIphone,
                        $tc->TC_AppUrlIphone ?? null,
                    );
                }

                if ($tc->TC_AppNameIpad && $tc->TC_AppIdIpad) {
                    $graph->iPadApp(
                        $tc->TC_AppNameIpad,
                        $tc->TC_AppIdIpad,
                        $tc->TC_AppUrlIpad ?? null,
                    );
                }

                if ($tc->TC_AppNameGoogleplay && $tc->TC_AppIDGoogleplay) {
                    $graph->googlePlayApp(
                        $tc->TC_AppNameGoogleplay,
                        $tc->TC_AppIDGoogleplay,
                        $tc->TC_AppUrlGoogleplay ?? null,
                    );
                }

                // $graph->country('name');
            } elseif ($tc->TC_Type == 'player') {
                $graph = PlayerTC::make($tc->TC_Title ?? $baseCfg->Title);

                // 'http://www.example.com/player.iframe', 1920, 1080
                // if ($tc->TC_Player && $tc->TC_PlayerWidth && $tc->TC_PlayerHeight)
                // {
                //     $graph->player($tc->TC_Player, $tc->TC_PlayerWidth, $tc->TC_PlayerHeight);
                // }

                if ($tc->TC_SiteID) {
                    $graph->setProperty('twitter', 'site:id', $tc->TC_SiteID);
                } elseif ($tcCfg->TC_SiteID) {
                    $graph->setProperty(
                        'twitter',
                        'site:id',
                        $tcCfg->TC_SiteID,
                    );
                }

                if ($tc->TC_Description) {
                    $graph->description($tc->TC_Description);
                }
            }

            if (
                $tc->TC_Type == 'player' ||
                $tc->TC_Type == 'summary_large_image' ||
                $tc->TC_Type == 'summary'
            ) {
                if ($tc->TC_Image->exists()) {
                    $url = $tc->TC_Image
                        ->FocusFill(1200, 630)
                        ->getAbsoluteURL();
                    $graph->image($url, $tc->Title);
                } elseif (
                    $tcCfg->DefaultImage &&
                    $tcCfg->DefaultImage->exists()
                ) {
                    $image = $tcCfg->DefaultImage;

                    $url = $tcCfg->DefaultImage
                        ->FocusFill(1200, 630)
                        ->getAbsoluteURL();
                    $graph->image($url, $tcCfg->Title);
                }
            }

            if ($tc->TC_Site) {
                $graph->site($tc->TC_Site);
            } elseif ($tcCfg->TC_Site) {
                $graph->site($tcCfg->TC_Site);
            }

            // a wire to page elements (blocks)
            if (
                $this->owner->ElementalArea &&
                $this->owner->ElementalArea->exists()
            ) {
                foreach ($this->owner->ElementalArea->Elements() as $element) {
                    try {
                        $element->updateTwitterCard($graph);
                    } catch (BadMethodCallException $exception) {
                    }
                }
            }

            // additional check for controllers
            if (is_subclass_of($this->owner, ContentController::class)) {
                if (method_exists($this->owner, 'updateTwitterCard')) {
                    $this->owner->updateTwitterCard($graph);
                }
            }

            // a wire to parent page
            $this->updateTwitterCard($graph);

            return '
            ' . $graph->__toString();
        }
    }

    public function SchemaData()
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [],
        ];

        if (
            isset($this->owner->manyMany()['Schemas']) &&
            get_class($this->owner->Schemas()) == ManyManyThroughList::class &&
            $this->owner->Schemas()->Count()
        ) {
            foreach ($this->owner->Schemas() as $schemaType) {
                if (!$schemaType->Disabled) {
                    $schema['@graph'][] = json_decode(
                        $schemaType->JsonLD,
                        true,
                    );
                }
            }
        }

        $cfg = SchemaConfig::current_config();

        if (
            !$this->owner->DisableDefaultSchema &&
            $cfg->DefaultSchemas()->Count()
        ) {
            foreach ($cfg->DefaultSchemas() as $schemaType) {
                if (!$schemaType->Disabled) {
                    $schema['@graph'][] = json_decode(
                        $schemaType->JsonLD,
                        true,
                    );
                }
            }
        }

        // a wire to page elements (blocks)
        if (
            $this->owner->ElementalArea &&
            $this->owner->ElementalArea->exists()
        ) {
            foreach ($this->owner->ElementalArea->Elements() as $element) {
                try {
                    $element->updateSchemaData($schema);
                } catch (BadMethodCallException $exception) {
                }
            }
        }

        // additional check for controllers
        if (is_subclass_of($this->owner, ContentController::class)) {
            if (method_exists($this->owner, 'updateSchemaData')) {
                $this->owner->updateSchemaData($schema);
            }
        }

        // a wire to parent page
        $this->updateSchemaData($schema);

        if (!empty($schema['@graph'])) {
            return '<script type="application/ld+json">' .
                json_encode($schema, JSON_UNESCAPED_SLASHES) .
                '</script>';
        }
    }

    public function updateOpenGraph(&$graph)
    {
        // use to update open graph within an actual page/object
    }

    public function updateTwitterCard(&$graph)
    {
        // use to update twitter card within an actual page/object
    }

    public function updateSchemaData(&$schema)
    {
        // use to update schema within an actual page/object
    }
}
