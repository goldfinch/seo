app/_config/seo.yml
```
---
Name: app-seo
After: '#goldfinch-seo'
---

Goldfinch\Seo\Models\OpenGraph:
  has_many:
    Pages: Page

Page:
  extensions:
    - Goldfinch\Seo\Extensions\SchemaExtension
    - Goldfinch\Seo\Extensions\OpenGraphExtension
    - Goldfinch\Seo\Extensions\TwitterCardExtension
```

```
cd root-project
cp vendor/goldfinch/imaginarium/webp.php.example public/webp.php
```

```
APP_META_SOURCESPACE=4
APP_META_LOCALE=en-NZ
APP_SEO_GEO_POSITION=
APP_SEO_GEO_REGION=
APP_SEO_GEO_PLACENAME=
APP_SEO_RATING=
APP_APPLE_WEBAPP_TITLE
APP_APPLE_WEBAPP_CAPABLE
APP_APPLE_WEBAPP_STATUS_BAR_STYLE
APP_SEO_AUTHOR
APP_SEO_COPYRIGHT
APP_SEO_ROBOTS_BASE

APP_SEO_PINTEREST=
APP_SEO_BING_CONSOLE=
APP_SEO_YANDEX=
APP_SEO_GOOGLE_CONSOLE=

APP_COLOR_SCHEME
APP_THEME_COLOR


APP_SEO_MANIFEST_START_URL="/"
APP_SEO_MANIFEST_ID=""
APP_SEO_MANIFEST_SCOPE=""
APP_SEO_MANIFEST_BACKGROUND_COLOR= (#3367D6)
APP_SEO_MANIFEST_DISPLAY= (fullscreen/standalone/minimal-ui/browser)
APP_SEO_MANIFEST_THEME_COLOR= (#3367D6)
APP_SEO_MANIFEST_DISPLAY_OVERRIDE= (["window-control-overlay", "minimal-ui"])
```

https://ogp.me/#no_vertical

https://ogvideo.app/

FYI:

Might need to be updated to a fix version later on
```
"silvershop/silverstripe-hasonefield": "dev-main" (as 4.0.0 has issue)
```

$MetaUniverse || $MetaUniverseCached
