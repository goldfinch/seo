<?php

namespace Goldfinch\Seo\Forms\GridField;

use BadMethodCallException;
use Axllent\MetaEditor\MetaEditor;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use Goldfinch\Seo\Models\SchemaConfig;
use Goldfinch\Seo\Admin\SEOSchemaAdmin;
use Goldfinch\Seo\Models\OpenGraphConfig;
use Goldfinch\Seo\Admin\SEOOpenGraphAdmin;
use Goldfinch\Seo\Models\TwitterCardConfig;
use Goldfinch\Seo\Admin\SEOTwitterCardAdmin;
use Axllent\MetaEditor\Lib\MetaEditorPermissions;
use Axllent\MetaEditor\Forms\MetaEditorTitleColumn;

class MetaEditorSEOColumn extends MetaEditorTitleColumn
{
    /**
     * Augment Columns
     *
     * @param GridField $gridField Gridfield
     * @param array     $columns   Columns
     *
     * @return null
     */
    public function augmentColumns($gridField, &$columns)
    {
    }

    /**
     * GetColumnsHandled
     *
     * @param GridField $gridField Gridfield
     *
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return [
            'MetaEditorSEOColumn',
        ];
    }

    /**
     * GetColumnMetaData
     *
     * @param GridField $gridField  Gridfield
     * @param string    $columnName Column name
     *
     * @return array
     */
    public function getColumnMetaData($gridField, $columnName)
    {
        return [
            'title' => 'SEO',
        ];
    }

    /**
     * Get column attributes
     *
     * @param GridField  $gridField  Gridfield
     * @param DataObject $record     Record
     * @param string     $columnName Column name
     *
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        if (!MetaEditorPermissions::canEdit($record)) {
            return [];
        }

        $errors = self::getErrors($record);

        return [
            'class' => count($errors)
            ? 'has-warning meta-editor-error ' . implode(' ', $errors)
            : 'has-success',
        ];
    }

    /**
     * Get errors
     *
     * @param DataObject $record Record
     *
     * @return array
     */
    public static function getErrors($record)
    {
        $description_field = Config::inst()->get(
            MetaEditor::class,
            'meta_description_field'
        );
        $description_min = Config::inst()->get(
            MetaEditor::class,
            'meta_description_min_length'
        );
        $description_max = Config::inst()->get(
            MetaEditor::class,
            'meta_description_max_length'
        );

        if (!MetaEditorPermissions::canEdit($record)) {
            return [];
        }

        $errors = [];

        if (!$record->{$description_field}
            || strlen($record->{$description_field}) < $description_min
        ) {
            $errors[] = 'meta-editor-error-too-short';
        } elseif ($record->{$description_field}
            && strlen($record->{$description_field}) > $description_max
        ) {
            $errors[] = 'meta-editor-error-too-long';
        } elseif ($record->{$description_field}
            && self::getAllEditableRecords()
                ->filter($description_field, $record->{$description_field})->count() > 1
        ) {
            $errors[] = 'meta-editor-error-duplicate';
        }

        return $errors;
    }

    /**
     * Get column content
     *
     * @param GridField  $gridField  Gridfield
     * @param DataObject $record     Record
     * @param string     $columnName Column name
     *
     * @return string
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ('MetaEditorSEOColumn' == $columnName) {
            $value = $gridField->getDataFieldValue(
                $record,
                Config::inst()->get(MetaEditor::class, 'show_in_search_field')
            );
            if (MetaEditorPermissions::canEdit($record)) {

                $html = '';

                if ($record->ShowInMenus !== null)
                {
                    if ($record->ShowInMenus)
                    {
                        $showInMenusHTML = '<spna style="color: green">yes</span>';
                    }
                    else
                    {
                        $showInMenusHTML = '<spna style="color: red">no</span>';
                    }
                }

                if ($record->ShowInSearch !== null)
                {
                    if ($record->ShowInSearch)
                    {
                        $showInSearchHTML = '<spna style="color: green">yes</span>';
                    }
                    else
                    {
                        $showInSearchHTML = '<spna style="color: red">no</span>';
                    }
                }

                $ogCfg = OpenGraphConfig::current_config();

                if (method_exists($record, 'OpenGraph') && $record->OpenGraph())
                {
                    $openGraphHTML = '<i>code based</i>';
                }
                else if ($record->OpenGraphObject && $record->OpenGraphObject->exists())
                {
                    $og = $record->OpenGraphObject;

                    $ma = new SEOOpenGraphAdmin;
                    $link = $ma->getCMSEditLinkForManagedDataObject($og);

                    $openGraphHTML = '<a href="'.$link.'">'.$og->Title.'</a>';
                }
                else if ($record->DisableDefaultOpenGraphObject && $ogCfg->DefaultObject && $ogCfg->DefaultObject->exists())
                {
                    $og = $ogCfg->DefaultObject;

                    $ma = new SEOOpenGraphAdmin;
                    $link = $ma->getCMSEditLinkForManagedDataObject($og);

                    $openGraphHTML = '<a href="'.$link.'">'.$og->Title.' (default)</a>';
                }
                else
                {
                    $openGraphHTML = '<spna style="color: red">no</span>';
                }

                $tcCfg = TwitterCardConfig::current_config();

                if (method_exists($record, 'TwitterCard') && $record->TwitterCard())
                {
                    $twitterCardHTML = '<i>code based</i>';
                }
                else if ($record->TwitterCardObject && $record->TwitterCardObject->exists())
                {
                    $tc = $record->TwitterCardObject;

                    $ma = new SEOTwitterCardAdmin;
                    $link = $ma->getCMSEditLinkForManagedDataObject($tc);

                    $twitterCardHTML = '<a href="'.$link.'">'.$tc->Title.'</a>';
                }
                else if ($record->DisableDefaultTwitterCardObject && $tcCfg->DefaultObject && $tcCfg->DefaultObject->exists())
                {
                    $tc = $tcCfg->DefaultObject;

                    $ma = new SEOTwitterCardAdmin;
                    $link = $ma->getCMSEditLinkForManagedDataObject($tc);

                    $twitterCardHTML = '<a href="'.$link.'">'.$tc->Title.' (default)</a>';
                }
                else
                {
                    $twitterCardHTML = '<spna style="color: red">no</span>';
                }



                $recordException = false;
                $schemaHTML = '';

                $scCfg = SchemaConfig::current_config();

                try {
                  $record->Schemas();
                } catch (BadMethodCallException $e) {
                  $recordException = true;
                }

                if (method_exists($record, 'SchemaData') && $record->SchemaData())
                {
                    $schemaHTML = '<i>code based</i>';
                }
                else if (!$recordException && $record->Schemas() && $record->Schemas()->count())
                {
                    $schemas = $record->Schemas();

                    $ma = new SEOSchemaAdmin;

                    foreach ($schemas as $schema)
                    {
                        $link = $ma->getCMSEditLinkForManagedDataObject($schema);

                        if ($schemaHTML != '') $schemaHTML .= ', ';

                        $schemaHTML .= '<a href="'.$link.'">'.$schema->Title.'</a>';
                    }
                }
                else if ($record->DisableDefaultSchema && $scCfg->DefaultSchemas()->count())
                {
                    $schemas = $scCfg->DefaultSchemas();

                    $ma = new SEOSchemaAdmin;

                    foreach ($schemas as $schema)
                    {
                        $link = $ma->getCMSEditLinkForManagedDataObject($schema);

                        if ($schemaHTML != '') $schemaHTML .= ', ';

                        $schemaHTML .= '<a href="'.$link.'">'.$schema->Title.' (default)</a>';
                    }
                }
                else
                {
                    $schemaHTML = '<spna style="color: red">no</span>';
                }

                if (isset($showInMenusHTML))
                {
                    $html .= '<div>Show in menu: '.$showInMenusHTML.'</div>';
                }

                if (isset($showInSearchHTML))
                {
                    $html .= '<div>Show in search: '.$showInSearchHTML.'</div>';
                }

                $html .= '
                <div>Open Graph: '.$openGraphHTML.'</div>
                <div>Twitter Card: '.$twitterCardHTML.'</div>
                <div>Schema: '.$schemaHTML.'</div>
                ';


                $ShowInSearch_field = LiteralField::create('Info', $html);
                $ShowInSearch_field->setName(
                    $this->getFieldName(
                        $ShowInSearch_field->getName(),
                        $gridField,
                        $record
                    )
                );

                // $ShowInSearch_field->setValue($record->ShowInSearch ? true : false);

                return $ShowInSearch_field->Field() . $this->getErrorMessages();
            }

            return ''; // blank
        }
    }

    /**
     * Return all the error messages
     *
     * @return string
     */
    public function getErrorMessages()
    {
        $description_min = Config::inst()->get(
            MetaEditor::class,
            'meta_description_min_length'
        );
        $description_max = Config::inst()->get(
            MetaEditor::class,
            'meta_description_max_length'
        );

        return '<div class="meta-editor-errors"></div>';
    }
}
