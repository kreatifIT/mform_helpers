<?php

namespace MFormHelpers;

use Cke5\Utils\Cke5Lang;
use MForm\MFormElements;
use rex_article;
use rex_clang;
use rex_i18n;
use rex_url;


class MForm extends \MForm
{
    const SETTINGS_VALUE_ID = 20;
    const ANCHOR_VALUE_ID = 10;

    protected bool $firstTab = true;

    protected ?array $config = null;

    /** @param array<string,string> $config */
    public function addSettingFields(array $config = []): void
    {
        $this->config = $config;
    }

    public function addAnchorFields($mform): void
    {
        $mform->addHtml('<div class="alert alert-success anchor-alert hide">Link zum Ankerpunkt kopiert!</div>');

        $mform->addTextField(self::ANCHOR_VALUE_ID . '.anchor_title', [
            'label' => rex_i18n::msg('label.module.settings.module_title'),
            'class' => 'anchor-field',
        ]);

        if ('add' == rex_get('function', 'string')) {
            $mform->addHtml(
                '<div class="form-group">
                    <div class="copy-unavailable anchor-field">
                    ' . rex_i18n::msg('label.module.settings.anchor.save') . '
                    </div>
                </div>'
            );
        } else {
            $article = rex_article::getCurrent();
            $anchorUrl = $article->getUrl() . '#section-' . rex_get('slice_id', 'int');
            $mform->addHtml(
                '<div class="form-group">
                    <div class="btn-copy btn anchor-field" onclick="navigator.clipboard.writeText(\'' . $anchorUrl . '\')">
                    ' . rex_i18n::msg('label.module.settings.anchor.copy') . '
                    </div>
                </div>'
            );
        }
    }

    public function setAnchorFields(): void
    {
        $this->addHtml('<div class="alert alert-success anchor-alert hide">Link zum Ankerpunkt kopiert!</div>');

        $this->addTextField(self::ANCHOR_VALUE_ID . '.anchor_title', [
            'label' => rex_i18n::msg('label.module.settings.module_title'),
            'class' => 'anchor-field',
        ]);

        if ('add' == rex_get('function', 'string')) {
            $this->addHtml(
                '<div class="form-group">
                    <div class="copy-unavailable anchor-field">
                    ' . rex_i18n::msg('label.module.settings.anchor.save') . '
                    </div>
                </div>'
            );
        } else {
            $article = rex_article::getCurrent();
            $anchorUrl = $article->getUrl() . '#section-' . rex_get('slice_id', 'int');
            $this->addHtml(
                '<div class="form-group">
                    <div class="btn-copy btn anchor-field" onclick="KreatifAddon.copy(`' . $anchorUrl . '`);">
                    ' . rex_i18n::msg('label.module.settings.anchor.copy') . '
                    </div>
                </div>'
            );
        }
    }

    public function addTitleField(string $id = '1', string $fieldName = 'title', string $class = ''): void
    {
        $this->addTextAreaField("$id.$fieldName", [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'class' => "cke5-editor $class",
            'data-lang' => Cke5Lang::getUserLang(),
            'data-profile' => 'headline',
        ]);
    }

    public function addImageField(int $id = 1, string $fieldName = 'image'): void
    {
        $this->addMediaField($id, [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'preview' => 1,
            'types' => 'png,svg,jpg,jpeg,gif,webp',
        ]);
    }

    public function addImagesField(int $id = 1, string $fieldName = 'images'): void
    {
        $this->addMedialistField($id, [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'preview' => 1,
            'types' => 'png,svg,jpg,jpeg,gif,webp',
        ]);
    }

    public function addVideoField(int $id = 1, string $fieldName = 'video'): void
    {
        $this->addMediaField($id, [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'types' => 'mp4',
        ]);
    }

    public function addFileField(int $id = 1, string $fieldName = 'file'): void
    {
        $this->addMediaField($id, [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
        ]);
    }

    public function addFilesField(int $id, string $fieldName = 'files'): void
    {
        $this->addMedialistField($id, [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
        ]);
    }

    public function addTitleSmallField(string $id = '1', string $class = ''): void
    {
        $this->addTitleField($id, 'title_small', $class);
    }

    public function addTitleLargeField(string $id = '1', string $class = ''): void
    {
        $this->addTitleField($id, 'title_large', $class);
    }

    public function addDefaultTextField(string $id = '1', string $fieldName = 'text', string $class = ''): void
    {
        $this->addTextAreaField("$id.$fieldName", [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'class' => "cke5-editor $class",
            'data-lang' => Cke5Lang::getUserLang(),
            'data-profile' => 'default',
        ]);
    }

    public function addFullTitleField(string $id = '1'): void
    {
        $this->addTitleLargeField($id);
        $this->addTitleSmallField($id);
    }

    public function addTab(callable $cb, string $label = 'content'): void
    {
        $tab = \Kreatif\Project\MForm::factory();
        $cb($tab);
        $this->addTabElement(
            \rex_i18n::msg("label.module.global.tab.$label"),
            $tab,
            $this->firstTab
        );
        $this->firstTab = false;
    }

    public function addMBlockTab(callable $cb, array $options = []): void
    {
        $options = array_merge(
            [
                'id' => '2',
                'item_label' => 'block',
                'tab_label' => 'blocks',
            ],
            $options
        );
        $id = $options['id'];

        $this->addTab(function (MForm $tab) use ($cb, $id, $options) {
            $itemLabel = $options['item_label'];
            $mform = static::factory();
            $fieldset = static::factory();
            $cb($fieldset, $id . '.0');
            $mform->addFieldsetArea(\rex_i18n::msg("label.module.global.$itemLabel"), $fieldset);
            $tab->addHtml(\MBlock::show($id, $mform, $options));
        }, $options['tab_label']);
    }

    public function addTitleTextField(string $id = '1'): void
    {
        $this->addFullTitleField($id);
        $this->addDefaultTextField($id);
    }

    public function addDefaultLinkField(string $id = '1', string $fieldName = '', array $linkOptions = [], string $label = '', string $class = ''): void
    {
        $field = $fieldName && $fieldName !== 'link' ? "link_$fieldName" : 'link';

        $this->addCustomLinkField(
            "$id.$field",
            array_merge(

                [
                    'label' => rex_i18n::msg(
                        "label.module.global.$field"
                    ),
                    'data-intern' => 'enable',
                    'data-extern' => 'enable',
                    'data-media' => 'disabled',
                    'data-mailto' => 'enable',
                    'data-tel' => 'enable',
                ], $linkOptions,
                ['class' => $class]
            )
        );

        $this->addTextField("$id.text_$field", [
            'label' => $label ?: rex_i18n::msg("label.module.global.text_$field"),
            'class' => $class,
        ]);
    }

    public function addSettingsFields(): void
    {
        $this->setAnchorFields();
    }

    public function addSettingsTab(): void
    {
        $this->addTab(function (\Kreatif\Project\MForm $tab) {
            $tab->addSettingsFields();
        }, 'settings');
    }

    public function addSelectField($id, array $options = null, array $attributes = null, int $size = 1, string $defaultValue = null): MFormElements
    {
        return parent::addSelectField($id, $options, array_merge(['data-live-search' => true], $attributes), $size, $defaultValue);
    }

    public function showWithSettings(bool $settings = true): string
    {
        if ($settings) {
            $this->addSettingsTab();
        }
        return parent::show();
    }

    public function addIconPickerField(string $id = '1', string $fieldName = 'icon'): void
    {
        $this->addTextField("$id.$fieldName", [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'class' => 'form-control icp',
        ]);
    }

    public function addSimpleTextField(string $id = '1', string $fieldName = ''): void
    {
        $this->addTextField("$id.$fieldName", [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
        ]);
    }

    public function addWidgetCode(string $id = '1', string $fieldName = 'code'): void
    {
        $this->addTextAreaField("$id.$fieldName", [
            'label' => rex_i18n::msg("label.module.global.$fieldName"),
            'class' => 'rex-js-code',
            'rows' => 5,
        ]);
    }


    /** Shopware Elements */

    public function addShopwareLinkField(string $id = '1', string $fieldName = '', array $linkOptions = [], string $label = '', string $class = ''): void
    {
        $this->addDefaultLinkField(
            $id,
            $fieldName,
            array_merge($linkOptions, [
                'data-shopware' => 'enable',
            ]),
            $label,
            $class
        );
    }

    public function addShopwareCategorySelector(string $id = '1', string $fieldName = 'product_category_id', array $options = []): void
    {
        $categoryApi = new \Kreatif\kShopware\StoreApi\CategoryApi();
        $response = $categoryApi->fetchCategoryList();
        $categories = [];

        foreach ($response->elements as $category) {
            $categories[$category->id] = $category->name;
        }
        $this->addSelectField(
            "$id.$fieldName",
            $categories,
            array_merge([
                'label' => rex_i18n::msg("label.module.global.$fieldName"),
            ], $options)
        );
    }

    public function addShopwareProductSelector(string $id = '1', string $fieldName = 'product_id', array $class = []): void
    {
        //Fetch Products
        $productApi = new \Kreatif\kShopware\StoreApi\ProductApi();
        $response = $productApi->fetchProductList();
        $products = [];
        foreach ($response->elements as $product) {
            $products[$product->id] = $product->translated->name;
        }

        $this->addSelectField("$id.$fieldName", $products, [
            'label' => 'Produkte',
            'class' => 'form-control sw_search_api w-100 ' . implode(' ', $class),
            'data-live-search' => true,
        ]);
    }

    public function addCustomLinkField($id, array $attributes = null, string $defaultValue = null): MFormElements
    {
        $field = parent::addCustomLinkField($id, $attributes, $defaultValue);
        if ($attributes['data-anchors'] === 'enable') {
            $this->addCustomLinkAnchorField($id);
        }
        return $field;
    }

    /**
     * @param float|int|string $id
     * @return void
     */
    private function addCustomLinkAnchorField(float|int|string $id): void
    {
        $backendUrl = rex_url::backendController([
            'page' => 'content/edit',
            'clang' => rex_clang::getCurrentId(),
            'function' => 'edit',
        ]);
        $this->addHtml('<div class="kreatif-anchor" data-kreatif-anchor>');
        $this->addTextField($id . "_anchor", [
            'label' => rex_i18n::msg("label.module.global.link_anchor"),
            'attributes' => [
                'data-kreatif-anchor-input' => ''
            ],
        ]);
        $this->addHtml('<a href="#" class="kreatif-anchor-btn" target="_blank" data-base-url="' . $backendUrl . '" data-kreatif-anchor-link><i class="rex-icon rex-icon-view-media"></i> Anker-Vorschau</a>');
        $this->addHtml('</div>');
    }

}
