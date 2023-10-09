# mform_helpers

Das Addon stellt einige Hilfsfunktionen für das MForm Addon bereit.

## Beispiele

```php
<?php

$mform = \Kreatif\Project\MForm::factory();

$mform->addTab(function (\Kreatif\Project\MForm $tab) {
    $tab->addTitleTextField();
    $tab->addDefaultLinkField();
});
echo $mform->showWithSettings();
```

```php
<?php

$mform = new \Kreatif\Project\MForm();

$mform->addTab(function (\Kreatif\Project\MForm $tab) {
    $tab->addTitleLargeField();
});
$mform->addMBlockTab(function (\Kreatif\Project\MForm $tab, $id) {
    $tab->addImageField();
    $tab->addTitleLargeField($id);
    $tab->addTitleSmallField($id);
    $tab->addSimpleTextField($id, 'year');
    $tab->addDefaultTextField($id);
    $tab->addDefaultLinkField($id);
}, [
    'item_label' => 'fact',
    'tab_label' => 'facts',
]);

echo $mform->showWithSettings();
```

```php
<?php

use RexGraphQL\Type\Reference;

$mform = new \Kreatif\Project\MForm();

$mform->addTab(function (\Kreatif\Project\MForm $tab) {
    $tab->addTitleLargeField();

    $references = Reference::getQuery()->find();
    $options = [];
    foreach ($references as $reference) {
        $options[$reference->getId()] = $reference->getName();
    }
    $tab->addSelectField("1.reference_ids", $options, ['multiple' => true, 'label' => rex_i18n::msg('label.select_references')]);
});
echo $mform->showWithSettings();

```
