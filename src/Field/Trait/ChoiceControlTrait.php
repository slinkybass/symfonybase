<?php

namespace App\Field\Trait;

/**
 * Shared EasyAdmin ChoiceField controls for `ChoiceField` / `EnumField`
 * (multiple, expanded radios/checkboxes, index badges).
 *
 * Requires TomSelect {@see enablePlugin()} and an EA `ChoiceField` `$innerField`.
 */
trait ChoiceControlTrait
{
    /** Allow more than one selected value. */
    public function isMultiple(bool $multiple = true): self
    {
        $this->innerField->allowMultipleChoices($multiple);

        return $this;
    }

    /** Expanded radios/checkboxes; turns TomSelect off while expanded. */
    public function isExpanded(bool $expanded = true): self
    {
        $this->enablePlugin(!$expanded);
        $this->innerField->renderExpanded($expanded);

        return $this;
    }

    /** Show the selected values as badges on index and detail. */
    public function renderAsBadges(bool $badges = true): self
    {
        $this->innerField->renderAsBadges($badges);

        return $this;
    }
}
