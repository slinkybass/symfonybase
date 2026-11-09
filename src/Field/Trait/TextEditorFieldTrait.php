<?php

namespace App\Field\Trait;

/**
 * TinyMCE options for `TextEditorField` (`data-texteditor-*`).
 *
 * Placeholder / readonly use `FieldTrait::setPlaceholder()` and `isReadonly()`
 * (`placeholder` / `readonly` HTML attrs).
 *
 * Presets (`setPreset`): named bundles (e.g. `basic`) applied before per-option
 * attrs — explicit `setToolbar()` / `setPlugins()` still win.
 *
 * Not mapped: setup/init callbacks; file_picker_callback; images_upload_*;
 * custom formats; skin/content_css (forced off for Tabler theme sync).
 */
trait TextEditorFieldTrait
{
    public const OPTION_PLUGIN = 'data-texteditor-field';
    public const OPTION_PRESET = 'data-texteditor-preset';

    public const PRESET_BASIC = 'basic';

    public const OPTION_RESIZE = 'data-texteditor-resize';
    public const OPTION_SPELLCHECK = 'data-texteditor-spellcheck';
    public const OPTION_TOOLBAR = 'data-texteditor-toolbar';
    public const OPTION_MENUBAR = 'data-texteditor-menubar';
    public const OPTION_PLUGINS = 'data-texteditor-plugins';
    public const OPTION_HEIGHT = 'data-texteditor-height';
    public const OPTION_MIN_HEIGHT = 'data-texteditor-min-height';
    public const OPTION_MAX_HEIGHT = 'data-texteditor-max-height';
    public const OPTION_LANGUAGE = 'data-texteditor-language';
    public const OPTION_BRANDING = 'data-texteditor-branding';
    public const OPTION_ELEMENTPATH = 'data-texteditor-elementpath';
    public const OPTION_STATUSBAR = 'data-texteditor-statusbar';
    public const OPTION_TOOLBAR_MODE = 'data-texteditor-toolbar-mode';
    public const OPTION_TOOLBAR_STICKY = 'data-texteditor-toolbar-sticky';
    public const OPTION_AUTORESIZE_BOTTOM_MARGIN = 'data-texteditor-autoresize-bottom-margin';
    public const OPTION_CONVERT_URLS = 'data-texteditor-convert-urls';
    public const OPTION_PASTE_AS_TEXT = 'data-texteditor-paste-as-text';
    public const OPTION_CONTEXTMENU = 'data-texteditor-contextmenu';
    public const OPTION_QUICKBARS_SELECTION_TOOLBAR = 'data-texteditor-quickbars-selection-toolbar';
    public const OPTION_QUICKBARS_INSERT_TOOLBAR = 'data-texteditor-quickbars-insert-toolbar';
    public const OPTION_CONTENT_STYLE = 'data-texteditor-content-style';

    public const RESIZE_BOTH = 'both';

    public const TOOLBAR_MODE_FLOATING = 'floating';
    public const TOOLBAR_MODE_SLIDING = 'sliding';
    public const TOOLBAR_MODE_SCROLLING = 'scrolling';
    public const TOOLBAR_MODE_WRAP = 'wrap';

    public const DEFAULT_TOOLBAR = 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | link image media table | emoticons charmap | pagebreak anchor | codesample code | preview fullscreen';

    /** Load TinyMCE (`data-texteditor-field`). `false` keeps a plain textarea. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-texteditor', self::OPTION_PLUGIN, $enable);
    }


    /**
     * Named TinyMCE option bundle (`data-texteditor-preset`).
     * `basic` = safe public content (undo/b/i/u/lists/link/emoticons/charmap/preview;
     * paste_as_text; no fonts/colors/blocks; autoresize min≈5 rows / max 200px).
     * Per-option setters override the preset.
     *
     * @param self::PRESET_* $preset
     */
    public function setPreset(string $preset): self
    {
        $this->setHtmlAttribute(self::OPTION_PRESET, $preset);

        return $this;
    }

    /** Editor chrome resize handle (`resize`). `true`, `false`, or `both`. */
    public function isResizeable(bool $resizeable = true): self
    {
        return $this->setDataAttr(self::OPTION_RESIZE, $resizeable);
    }

    /**
     * Editor chrome resize (`resize`). Use {@see self::RESIZE_BOTH} for both axes.
     *
     * @param bool|self::RESIZE_BOTH $resize
     */
    public function setResize(bool|string $resize): self
    {
        if (\is_bool($resize)) {
            return $this->setDataAttr(self::OPTION_RESIZE, $resize);
        }
        $this->setHtmlAttribute(self::OPTION_RESIZE, $resize);

        return $this;
    }

    /** Browser spellcheck inside the editor (`browser_spellcheck`). Default `true`. */
    public function isSpellcheck(bool $spellcheck = true): self
    {
        return $this->setDataAttr(self::OPTION_SPELLCHECK, $spellcheck);
    }

    /** Toolbar string (`toolbar`). */
    public function setToolbar(string $toolbar): self
    {
        $this->setHtmlAttribute(self::OPTION_TOOLBAR, $toolbar);

        return $this;
    }

    /**
     * Menubar (`menubar`). `true`/`false`, or a menu list string (e.g. `file edit insert`).
     */
    public function setMenubar(bool|string $menubar): self
    {
        if (\is_bool($menubar)) {
            return $this->setDataAttr(self::OPTION_MENUBAR, $menubar);
        }
        $this->setHtmlAttribute(self::OPTION_MENUBAR, $menubar);

        return $this;
    }

    /**
     * Plugin list (`plugins`). Space- or comma-separated names from the bundled set.
     *
     * @param string|list<string> $plugins
     */
    public function setPlugins(string|array $plugins): self
    {
        $value = \is_array($plugins) ? implode(' ', $plugins) : $plugins;
        $this->setHtmlAttribute(self::OPTION_PLUGINS, $value);

        return $this;
    }

    /**
     * Fixed editor height in px (`height`).
     * Drops default `autoresize` (TinyMCE ignores `height` while that plugin is on).
     * Prefer `setMinHeight()` / `setMaxHeight()` when you want autoresize bounds.
     */
    public function setHeight(int $height): self
    {
        $this->setHtmlAttribute(self::OPTION_HEIGHT, (string) $height);

        return $this;
    }

    /** Autoresize minimum height in px (`min_height`). */
    public function setMinHeight(int $minHeight): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_HEIGHT, (string) $minHeight);

        return $this;
    }

    /** Autoresize maximum height in px (`max_height`). */
    public function setMaxHeight(int $maxHeight): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_HEIGHT, (string) $maxHeight);

        return $this;
    }

    /** UI language code (`language`). Default from `moment.locale()` or `es`. */
    public function setLanguage(string $language): self
    {
        $this->setHtmlAttribute(self::OPTION_LANGUAGE, $language);

        return $this;
    }

    /**
     * TinyMCE “Powered by” link in the status bar (`branding`).
     * On/off only — the label is not customizable. Default `false` (hidden).
     */
    public function showBranding(bool $branding = true): self
    {
        return $this->setDataAttr(self::OPTION_BRANDING, $branding);
    }

    /** Element path in the status bar (`elementpath`). */
    public function showElementPath(bool $elementpath = true): self
    {
        return $this->setDataAttr(self::OPTION_ELEMENTPATH, $elementpath);
    }

    /** Status bar visibility (`statusbar`). */
    public function showStatusbar(bool $statusbar = true): self
    {
        return $this->setDataAttr(self::OPTION_STATUSBAR, $statusbar);
    }

    /**
     * Toolbar overflow mode (`toolbar_mode`).
     *
     * @param self::TOOLBAR_MODE_* $mode
     */
    public function setToolbarMode(string $mode): self
    {
        $this->setHtmlAttribute(self::OPTION_TOOLBAR_MODE, $mode);

        return $this;
    }

    /** Sticky toolbar while scrolling (`toolbar_sticky`). */
    public function isToolbarSticky(bool $sticky = true): self
    {
        return $this->setDataAttr(self::OPTION_TOOLBAR_STICKY, $sticky);
    }

    /** Extra bottom margin for autoresize (`autoresize_bottom_margin`). */
    public function setAutoresizeBottomMargin(int $margin): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTORESIZE_BOTTOM_MARGIN, (string) $margin);

        return $this;
    }

    /** Convert URLs (`convert_urls`). Default `false`. */
    public function isConvertUrls(bool $convert = true): self
    {
        return $this->setDataAttr(self::OPTION_CONVERT_URLS, $convert);
    }

    /** Paste as plain text (`paste_as_text`). */
    public function isPasteAsText(bool $pasteAsText = true): self
    {
        return $this->setDataAttr(self::OPTION_PASTE_AS_TEXT, $pasteAsText);
    }

    /**
     * Context menu (`contextmenu`). `false` disables; otherwise a plugin list string.
     */
    public function setContextMenu(bool|string $contextmenu): self
    {
        if (\is_bool($contextmenu)) {
            return $this->setDataAttr(self::OPTION_CONTEXTMENU, $contextmenu);
        }
        $this->setHtmlAttribute(self::OPTION_CONTEXTMENU, $contextmenu);

        return $this;
    }

    /**
     * Quickbars selection toolbar (`quickbars_selection_toolbar`).
     * `false` disables; otherwise a toolbar string.
     */
    public function setQuickbarsSelectionToolbar(bool|string $toolbar): self
    {
        if (\is_bool($toolbar)) {
            return $this->setDataAttr(self::OPTION_QUICKBARS_SELECTION_TOOLBAR, $toolbar);
        }
        $this->setHtmlAttribute(self::OPTION_QUICKBARS_SELECTION_TOOLBAR, $toolbar);

        return $this;
    }

    /**
     * Quickbars insert toolbar (`quickbars_insert_toolbar`).
     * `false` disables; otherwise a toolbar string.
     */
    public function setQuickbarsInsertToolbar(bool|string $toolbar): self
    {
        if (\is_bool($toolbar)) {
            return $this->setDataAttr(self::OPTION_QUICKBARS_INSERT_TOOLBAR, $toolbar);
        }
        $this->setHtmlAttribute(self::OPTION_QUICKBARS_INSERT_TOOLBAR, $toolbar);

        return $this;
    }

    /** Extra CSS appended after Tabler content theme (`content_style`). */
    public function setContentStyle(string $css): self
    {
        $this->setHtmlAttribute(self::OPTION_CONTENT_STYLE, $css);

        return $this;
    }
}
