<?php

namespace App\Field\Trait;

/**
 * Autogrow options for `TextareaField` (`data-textarea-*`).
 */
trait TextareaFieldTrait
{
    public const OPTION_PLUGIN = 'data-textarea-field';
    public const OPTION_MAX_HEIGHT = 'data-textarea-max-height';
    public const OPTION_RESIZEABLE = 'resizeable';

    public const DEFAULT_MAX_HEIGHT = '200px';

    /** Load the autogrow plugin (`data-textarea-field`). `false` keeps a plain textarea. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-textarea', self::OPTION_PLUGIN, $enable);
    }

    /** CSS max-height while autogrowing (`data-textarea-max-height`). Default `200px`. */
    public function setMaxHeight(string $maxHeight): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_HEIGHT, $maxHeight);

        return $this;
    }

    /**
     * Allow native CSS resize when the plugin is off (`resizeable` custom option → Twig).
     * Ignored while autogrow is on (JS forces `resize: none`).
     */
    public function isResizeable(bool $resizeable = true): self
    {
        $this->setCustomOption(self::OPTION_RESIZEABLE, $resizeable);

        return $this;
    }
}
