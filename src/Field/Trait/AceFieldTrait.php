<?php

namespace App\Field\Trait;

/**
 * Ace options for `CodeEditorField` (`lib/ace.js`, `data-codeeditor-*`).
 *
 * Placeholder / readonly use `FieldTrait::setPlaceholder()` and `isReadonly()`
 * (`placeholder` / `readonly` HTML attrs).
 *
 * Not mapped: Ace extensions (autocomplete, snippets, emmet, keybinding);
 * session/document APIs; annotations; custom commands; keyboardHandler.
 */
trait AceFieldTrait
{
    public const OPTION_PLUGIN = 'data-codeeditor-field';

    public const OPTION_THEME = 'data-codeeditor-theme';
    public const OPTION_DARK_THEME = 'data-codeeditor-dark-theme';
    public const OPTION_LANGUAGE = 'data-codeeditor-language';
    public const OPTION_TAB_SIZE = 'data-codeeditor-tab-size';
    public const OPTION_INDENT_WITH_TABS = 'data-codeeditor-indent-with-tabs';
    public const OPTION_SHOW_LINE_NUMBERS = 'data-codeeditor-show-line-numbers';
    public const OPTION_SHOW_GUTTER = 'data-codeeditor-show-gutter';
    public const OPTION_MIN_LINES = 'data-codeeditor-min-lines';
    public const OPTION_MAX_LINES = 'data-codeeditor-max-lines';
    public const OPTION_FONT_SIZE = 'data-codeeditor-font-size';
    public const OPTION_FONT_FAMILY = 'data-codeeditor-font-family';
    public const OPTION_WRAP = 'data-codeeditor-wrap';
    public const OPTION_SHOW_PRINT_MARGIN = 'data-codeeditor-show-print-margin';
    public const OPTION_PRINT_MARGIN_COLUMN = 'data-codeeditor-print-margin-column';
    public const OPTION_HIGHLIGHT_ACTIVE_LINE = 'data-codeeditor-highlight-active-line';
    public const OPTION_HIGHLIGHT_SELECTED_WORD = 'data-codeeditor-highlight-selected-word';
    public const OPTION_SHOW_INVISIBLES = 'data-codeeditor-show-invisibles';
    public const OPTION_DISPLAY_INDENT_GUIDES = 'data-codeeditor-display-indent-guides';
    public const OPTION_SHOW_FOLD_WIDGETS = 'data-codeeditor-show-fold-widgets';
    public const OPTION_USE_WORKER = 'data-codeeditor-use-worker';

    /** themes */
    public const CODEEDITOR_THEME_AMBIENCE = 'ambiance';
    public const CODEEDITOR_THEME_CHAOS = 'chaos';
    public const CODEEDITOR_THEME_CHROME = 'chrome';
    public const CODEEDITOR_THEME_CLOUD_EDITOR = 'cloud_editor';
    public const CODEEDITOR_THEME_CLOUD_EDITOR_DARK = 'cloud_editor_dark';
    public const CODEEDITOR_THEME_CLOUD9_DAY = 'cloud9_day';
    public const CODEEDITOR_THEME_CLOUD9_NIGHT = 'cloud9_night';
    public const CODEEDITOR_THEME_CLOUD9_NIGHT_LOW_COLOR = 'cloud9_night_low_color';
    public const CODEEDITOR_THEME_CLOUDS = 'clouds';
    public const CODEEDITOR_THEME_CLOUDS_MIDNIGHT = 'clouds_midnight';
    public const CODEEDITOR_THEME_COBALT = 'cobalt';
    public const CODEEDITOR_THEME_CRIMSON_EDITOR = 'crimson_editor';
    public const CODEEDITOR_THEME_DAWN = 'dawn';
    public const CODEEDITOR_THEME_DRACULA = 'dracula';
    public const CODEEDITOR_THEME_DREAMWEAVER = 'dreamweaver';
    public const CODEEDITOR_THEME_ECLIPSE = 'eclipse';
    public const CODEEDITOR_THEME_GITHUB = 'github';
    public const CODEEDITOR_THEME_GITHUB_DARK = 'github_dark';
    public const CODEEDITOR_THEME_GITHUB_LIGHT_DEFAULT = 'github_light_default';
    public const CODEEDITOR_THEME_GOB = 'gob';
    public const CODEEDITOR_THEME_GRUVBOX = 'gruvbox';
    public const CODEEDITOR_THEME_GRUVBOX_DARK_HARD = 'gruvbox_dark_hard';
    public const CODEEDITOR_THEME_GRUVBOX_LIGHT_HARD = 'gruvbox_light_hard';
    public const CODEEDITOR_THEME_IDLE_FINGERS = 'idle_fingers';
    public const CODEEDITOR_THEME_IPLASTIC = 'iplastic';
    public const CODEEDITOR_THEME_KATZENMILCH = 'katzenmilch';
    public const CODEEDITOR_THEME_KR_THEME = 'kr_theme';
    public const CODEEDITOR_THEME_KUROI = 'kuroir';
    public const CODEEDITOR_THEME_MERBIVORE = 'merbivore';
    public const CODEEDITOR_THEME_MERBIVORE_SOFT = 'merbivore_soft';
    public const CODEEDITOR_THEME_MONO_INDUSTRIAL = 'mono_industrial';
    public const CODEEDITOR_THEME_MONOKAI = 'monokai';
    public const CODEEDITOR_THEME_NORD_DARK = 'nord_dark';
    public const CODEEDITOR_THEME_ONE_DARK = 'one_dark';
    public const CODEEDITOR_THEME_PASTEL_ON_DARK = 'pastel_on_dark';
    public const CODEEDITOR_THEME_SOLARIZED_DARK = 'solarized_dark';
    public const CODEEDITOR_THEME_SOLARIZED_LIGHT = 'solarized_light';
    public const CODEEDITOR_THEME_SQLSERVER = 'sqlserver';
    public const CODEEDITOR_THEME_TERMINAL = 'terminal';
    public const CODEEDITOR_THEME_TEXTMATE = 'textmate';
    public const CODEEDITOR_THEME_TOMORROW = 'tomorrow';
    public const CODEEDITOR_THEME_TOMORROW_NIGHT = 'tomorrow_night';
    public const CODEEDITOR_THEME_TOMORROW_NIGHT_BLUE = 'tomorrow_night_blue';
    public const CODEEDITOR_THEME_TOMORROW_NIGHT_BRIGHT = 'tomorrow_night_bright';
    public const CODEEDITOR_THEME_TOMORROW_NIGHT_EIGHTIES = 'tomorrow_night_eighties';
    public const CODEEDITOR_THEME_TWILIGHT = 'twilight';
    public const CODEEDITOR_THEME_VIBRANT_INK = 'vibrant_ink';
    public const CODEEDITOR_THEME_XCODE = 'xcode';

    /** languages */
    public const CODEEDITOR_LANGUAGE_ABAP = 'abap';
    public const CODEEDITOR_LANGUAGE_ABC = 'abc';
    public const CODEEDITOR_LANGUAGE_ACTIONSCRIPT = 'actionscript';
    public const CODEEDITOR_LANGUAGE_ADA = 'ada';
    public const CODEEDITOR_LANGUAGE_ALDA = 'alda';
    public const CODEEDITOR_LANGUAGE_APACHE_CONF = 'apache_conf';
    public const CODEEDITOR_LANGUAGE_APEX = 'apex';
    public const CODEEDITOR_LANGUAGE_APPLESCRIPT = 'applescript';
    public const CODEEDITOR_LANGUAGE_AQL = 'aql';
    public const CODEEDITOR_LANGUAGE_ASCIIDOC = 'asciidoc';
    public const CODEEDITOR_LANGUAGE_ASL = 'asl';
    public const CODEEDITOR_LANGUAGE_ASSEMBLY_ARM32 = 'assembly_arm32';
    public const CODEEDITOR_LANGUAGE_ASSEMBLY_X86 = 'assembly_x86';
    public const CODEEDITOR_LANGUAGE_ASTRO = 'astro';
    public const CODEEDITOR_LANGUAGE_AUTOHOTKEY = 'autohotkey';
    public const CODEEDITOR_LANGUAGE_BASIC = 'basic';
    public const CODEEDITOR_LANGUAGE_BATCHFILE = 'batchfile';
    public const CODEEDITOR_LANGUAGE_BIBTEX = 'bibtex';
    public const CODEEDITOR_LANGUAGE_C_CPP = 'c_cpp';
    public const CODEEDITOR_LANGUAGE_C9SEARCH = 'c9search';
    public const CODEEDITOR_LANGUAGE_CIRRU = 'cirru';
    public const CODEEDITOR_LANGUAGE_CLOJURE = 'clojure';
    public const CODEEDITOR_LANGUAGE_COBOL = 'cobol';
    public const CODEEDITOR_LANGUAGE_COFFEE = 'coffee';
    public const CODEEDITOR_LANGUAGE_COLD_FUSION = 'coldfusion';
    public const CODEEDITOR_LANGUAGE_CRYSTAL = 'crystal';
    public const CODEEDITOR_LANGUAGE_CSHARP = 'csharp';
    public const CODEEDITOR_LANGUAGE_CSDOCUMENT = 'csound_document';
    public const CODEEDITOR_LANGUAGE_CSORCHESTRA = 'csound_orchestra';
    public const CODEEDITOR_LANGUAGE_CSSCORE = 'csound_score';
    public const CODEEDITOR_LANGUAGE_CSP = 'csp';
    public const CODEEDITOR_LANGUAGE_CSS = 'css';
    public const CODEEDITOR_LANGUAGE_CSV = 'csv';
    public const CODEEDITOR_LANGUAGE_CURLY = 'curly';
    public const CODEEDITOR_LANGUAGE_CUTTLEFISH = 'cuttlefish';
    public const CODEEDITOR_LANGUAGE_D = 'd';
    public const CODEEDITOR_LANGUAGE_DART = 'dart';
    public const CODEEDITOR_LANGUAGE_DIFF = 'diff';
    public const CODEEDITOR_LANGUAGE_DJANGO = 'django';
    public const CODEEDITOR_LANGUAGE_DOCKERFILE = 'dockerfile';
    public const CODEEDITOR_LANGUAGE_DOT = 'dot';
    public const CODEEDITOR_LANGUAGE_DROOLS = 'drools';
    public const CODEEDITOR_LANGUAGE_EDIFACT = 'edifact';
    public const CODEEDITOR_LANGUAGE_EIFFEL = 'eiffel';
    public const CODEEDITOR_LANGUAGE_EJS = 'ejs';
    public const CODEEDITOR_LANGUAGE_ELIXIR = 'elixir';
    public const CODEEDITOR_LANGUAGE_ELM = 'elm';
    public const CODEEDITOR_LANGUAGE_ERLANG = 'erlang';
    public const CODEEDITOR_LANGUAGE_FLIX = 'flix';
    public const CODEEDITOR_LANGUAGE_FORTH = 'forth';
    public const CODEEDITOR_LANGUAGE_FORTRAN = 'fortran';
    public const CODEEDITOR_LANGUAGE_FSHARP = 'fsharp';
    public const CODEEDITOR_LANGUAGE_FSL = 'fsl';
    public const CODEEDITOR_LANGUAGE_FTL = 'ftl';
    public const CODEEDITOR_LANGUAGE_GCODE = 'gcode';
    public const CODEEDITOR_LANGUAGE_GHERKIN = 'gherkin';
    public const CODEEDITOR_LANGUAGE_GITIGNORE = 'gitignore';
    public const CODEEDITOR_LANGUAGE_GLSL = 'glsl';
    public const CODEEDITOR_LANGUAGE_GOBSTONES = 'gobstones';
    public const CODEEDITOR_LANGUAGE_GO_LANG = 'golang';
    public const CODEEDITOR_LANGUAGE_GRAPHQLSCHEMA = 'graphqlschema';
    public const CODEEDITOR_LANGUAGE_GROOVY = 'groovy';
    public const CODEEDITOR_LANGUAGE_HAML = 'haml';
    public const CODEEDITOR_LANGUAGE_HANDLEBARS = 'handlebars';
    public const CODEEDITOR_LANGUAGE_HASKELL = 'haskell';
    public const CODEEDITOR_LANGUAGE_HASKELL_CABAL = 'haskell_cabal';
    public const CODEEDITOR_LANGUAGE_HAXE = 'haxe';
    public const CODEEDITOR_LANGUAGE_HJSON = 'hjson';
    public const CODEEDITOR_LANGUAGE_HTML = 'html';
    public const CODEEDITOR_LANGUAGE_HTML_ELIXIR = 'html_elixir';
    public const CODEEDITOR_LANGUAGE_HTML_RUBY = 'html_ruby';
    public const CODEEDITOR_LANGUAGE_INI = 'ini';
    public const CODEEDITOR_LANGUAGE_IO = 'io';
    public const CODEEDITOR_LANGUAGE_ION = 'ion';
    public const CODEEDITOR_LANGUAGE_JACK = 'jack';
    public const CODEEDITOR_LANGUAGE_JADE = 'jade';
    public const CODEEDITOR_LANGUAGE_JAVA = 'java';
    public const CODEEDITOR_LANGUAGE_JAVASCRIPT = 'javascript';
    public const CODEEDITOR_LANGUAGE_JEXL = 'jexl';
    public const CODEEDITOR_LANGUAGE_JSON = 'json';
    public const CODEEDITOR_LANGUAGE_JSON5 = 'json5';
    public const CODEEDITOR_LANGUAGE_JSONIQ = 'jsoniq';
    public const CODEEDITOR_LANGUAGE_JSP = 'jsp';
    public const CODEEDITOR_LANGUAGE_JSSM = 'jssm';
    public const CODEEDITOR_LANGUAGE_JSX = 'jsx';
    public const CODEEDITOR_LANGUAGE_JULIA = 'julia';
    public const CODEEDITOR_LANGUAGE_KOTLIN = 'kotlin';
    public const CODEEDITOR_LANGUAGE_LATEX = 'latex';
    public const CODEEDITOR_LANGUAGE_LATTE = 'latte';
    public const CODEEDITOR_LANGUAGE_LESS = 'less';
    public const CODEEDITOR_LANGUAGE_LIQUID = 'liquid';
    public const CODEEDITOR_LANGUAGE_LISP = 'lisp';
    public const CODEEDITOR_LANGUAGE_LIVESCRIPT = 'livescript';
    public const CODEEDITOR_LANGUAGE_LOGIQL = 'logiql';
    public const CODEEDITOR_LANGUAGE_LOGTALK = 'logtalk';
    public const CODEEDITOR_LANGUAGE_LSL = 'lsl';
    public const CODEEDITOR_LANGUAGE_LUA = 'lua';
    public const CODEEDITOR_LANGUAGE_LUAPAGE = 'luapage';
    public const CODEEDITOR_LANGUAGE_LUCENE = 'lucene';
    public const CODEEDITOR_LANGUAGE_MAKEFILE = 'makefile';
    public const CODEEDITOR_LANGUAGE_MARKDOWN = 'markdown';
    public const CODEEDITOR_LANGUAGE_MASK = 'mask';
    public const CODEEDITOR_LANGUAGE_MATLAB = 'matlab';
    public const CODEEDITOR_LANGUAGE_MAZE = 'maze';
    public const CODEEDITOR_LANGUAGE_MEDIAWIKI = 'mediawiki';
    public const CODEEDITOR_LANGUAGE_MEL = 'mel';
    public const CODEEDITOR_LANGUAGE_MIPS = 'mips';
    public const CODEEDITOR_LANGUAGE_MIXAL = 'mixal';
    public const CODEEDITOR_LANGUAGE_MUSHCODE = 'mushcode';
    public const CODEEDITOR_LANGUAGE_MYSQL = 'mysql';
    public const CODEEDITOR_LANGUAGE_NASAL = 'nasal';
    public const CODEEDITOR_LANGUAGE_NGINX = 'nginx';
    public const CODEEDITOR_LANGUAGE_NIM = 'nim';
    public const CODEEDITOR_LANGUAGE_NIX = 'nix';
    public const CODEEDITOR_LANGUAGE_NSIS = 'nsis';
    public const CODEEDITOR_LANGUAGE_NUNJUCKS = 'nunjucks';
    public const CODEEDITOR_LANGUAGE_OBJECTIVEC = 'objectivec';
    public const CODEEDITOR_LANGUAGE_OCAML = 'ocaml';
    public const CODEEDITOR_LANGUAGE_ODIN = 'odin';
    public const CODEEDITOR_LANGUAGE_PARTIQL = 'partiql';
    public const CODEEDITOR_LANGUAGE_PASCAL = 'pascal';
    public const CODEEDITOR_LANGUAGE_PERL = 'perl';
    public const CODEEDITOR_LANGUAGE_PGSQL = 'pgsql';
    public const CODEEDITOR_LANGUAGE_PHP = 'php';
    public const CODEEDITOR_LANGUAGE_PHP_LARAVEL_BLADE = 'php_laravel_blade';
    public const CODEEDITOR_LANGUAGE_PIG = 'pig';
    public const CODEEDITOR_LANGUAGE_PLAIN_TEXT = 'plain_text';
    public const CODEEDITOR_LANGUAGE_PLSQL = 'plsql';
    public const CODEEDITOR_LANGUAGE_POWERSHELL = 'powershell';
    public const CODEEDITOR_LANGUAGE_PRAAT = 'praat';
    public const CODEEDITOR_LANGUAGE_PRISMA = 'prisma';
    public const CODEEDITOR_LANGUAGE_PROLOG = 'prolog';
    public const CODEEDITOR_LANGUAGE_PROPERTIES = 'properties';
    public const CODEEDITOR_LANGUAGE_PROTOBUF = 'protobuf';
    public const CODEEDITOR_LANGUAGE_PRQL = 'prql';
    public const CODEEDITOR_LANGUAGE_PUPPET = 'puppet';
    public const CODEEDITOR_LANGUAGE_PYTHON = 'python';
    public const CODEEDITOR_LANGUAGE_QML = 'qml';
    public const CODEEDITOR_LANGUAGE_R = 'r';
    public const CODEEDITOR_LANGUAGE_RAKU = 'raku';
    public const CODEEDITOR_LANGUAGE_RAZOR = 'razor';
    public const CODEEDITOR_LANGUAGE_RDOC = 'rdoc';
    public const CODEEDITOR_LANGUAGE_RED = 'red';
    public const CODEEDITOR_LANGUAGE_REDSHIFT = 'redshift';
    public const CODEEDITOR_LANGUAGE_RHTML = 'rhtml';
    public const CODEEDITOR_LANGUAGE_ROBOT = 'robot';
    public const CODEEDITOR_LANGUAGE_RST = 'rst';
    public const CODEEDITOR_LANGUAGE_RUBY = 'ruby';
    public const CODEEDITOR_LANGUAGE_RUST = 'rust';
    public const CODEEDITOR_LANGUAGE_SAC = 'sac';
    public const CODEEDITOR_LANGUAGE_SASS = 'sass';
    public const CODEEDITOR_LANGUAGE_SCAD = 'scad';
    public const CODEEDITOR_LANGUAGE_SCALA = 'scala';
    public const CODEEDITOR_LANGUAGE_SCHEME = 'scheme';
    public const CODEEDITOR_LANGUAGE_SCrypt = 'scrypt';
    public const CODEEDITOR_LANGUAGE_SCSS = 'scss';
    public const CODEEDITOR_LANGUAGE_SH = 'sh';
    public const CODEEDITOR_LANGUAGE_SJS = 'sjs';
    public const CODEEDITOR_LANGUAGE_SLIM = 'slim';
    public const CODEEDITOR_LANGUAGE_SMARTY = 'smarty';
    public const CODEEDITOR_LANGUAGE_SMITHY = 'smithy';
    public const CODEEDITOR_LANGUAGE_SNIPPETS = 'snippets';
    public const CODEEDITOR_LANGUAGE_SOY_TEMPLATE = 'soy_template';
    public const CODEEDITOR_LANGUAGE_SPACE = 'space';
    public const CODEEDITOR_LANGUAGE_SPARQL = 'sparql';
    public const CODEEDITOR_LANGUAGE_SQL = 'sql';
    public const CODEEDITOR_LANGUAGE_SQLSERVER = 'sqlserver';
    public const CODEEDITOR_LANGUAGE_STYLUS = 'stylus';
    public const CODEEDITOR_LANGUAGE_SVG = 'svg';
    public const CODEEDITOR_LANGUAGE_SWIFT = 'swift';
    public const CODEEDITOR_LANGUAGE_TCL = 'tcl';
    public const CODEEDITOR_LANGUAGE_TERRAFORM = 'terraform';
    public const CODEEDITOR_LANGUAGE_TEX = 'tex';
    public const CODEEDITOR_LANGUAGE_TEXT = 'text';
    public const CODEEDITOR_LANGUAGE_TEXTILE = 'textile';
    public const CODEEDITOR_LANGUAGE_TOML = 'toml';
    public const CODEEDITOR_LANGUAGE_TSV = 'tsv';
    public const CODEEDITOR_LANGUAGE_TSX = 'tsx';
    public const CODEEDITOR_LANGUAGE_TURTLE = 'turtle';
    public const CODEEDITOR_LANGUAGE_TWIG = 'twig';
    public const CODEEDITOR_LANGUAGE_TYPESCRIPT = 'typescript';
    public const CODEEDITOR_LANGUAGE_VALA = 'vala';
    public const CODEEDITOR_LANGUAGE_VBSCRIPT = 'vbscript';
    public const CODEEDITOR_LANGUAGE_VELOCITY = 'velocity';
    public const CODEEDITOR_LANGUAGE_VERILOG = 'verilog';
    public const CODEEDITOR_LANGUAGE_VHDL = 'vhdl';
    public const CODEEDITOR_LANGUAGE_VISUALFORCE = 'visualforce';
    public const CODEEDITOR_LANGUAGE_VUE = 'vue';
    public const CODEEDITOR_LANGUAGE_WOLLOK = 'wollok';
    public const CODEEDITOR_LANGUAGE_XML = 'xml';
    public const CODEEDITOR_LANGUAGE_XQUERY = 'xquery';
    public const CODEEDITOR_LANGUAGE_YAML = 'yaml';
    public const CODEEDITOR_LANGUAGE_ZEEK = 'zeek';
    public const CODEEDITOR_LANGUAGE_ZIG = 'zig';

    /** Load Ace (`data-codeeditor-field`). `false` keeps a plain textarea. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-codeeditor', self::OPTION_PLUGIN, $enable);
    }


    /**
     * Ace theme for light app mode (`theme`). Without {@see setDarkTheme()}, stays fixed.
     * Omitting both follows default chrome / twilight with `data-bs-theme`.
     */
    public function setTheme(string $theme): self
    {
        $this->setHtmlAttribute(self::OPTION_THEME, $theme);

        return $this;
    }

    /**
     * Ace theme for dark app mode (`dark-theme`). Pair with {@see setTheme()} to sync
     * on `data-bs-theme` (e.g. github + github_dark).
     */
    public function setDarkTheme(string $theme): self
    {
        $this->setHtmlAttribute(self::OPTION_DARK_THEME, $theme);

        return $this;
    }

    /** Ace mode id without `ace/mode/` prefix (`mode`). Default `javascript`. */
    public function setLanguage(string $language): self
    {
        $this->setHtmlAttribute(self::OPTION_LANGUAGE, $language);

        return $this;
    }

    /** Tab width in spaces (`tabSize`). Default `4`. */
    public function setTabSize(int $tabSize): self
    {
        $this->setHtmlAttribute(self::OPTION_TAB_SIZE, (string) $tabSize);

        return $this;
    }

    /** Indent with hard tabs (`useSoftTabs` inverted). Default `true`. */
    public function isIndentedWithTabs(bool $indent = true): self
    {
        return $this->setDataAttr(self::OPTION_INDENT_WITH_TABS, $indent);
    }

    /** Gutter line numbers (`showLineNumbers`). Default `true`. Independent of {@see showGutter()}. */
    public function showLineNumbers(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SHOW_LINE_NUMBERS, $show);
    }

    /** Left gutter strip (`showGutter`). Default `true`. Independent of {@see showLineNumbers()}. */
    public function showGutter(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SHOW_GUTTER, $show);
    }

    /** Autoresize minimum visible lines (`minLines`). Default `5`. */
    public function setMinLines(int $minLines): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_LINES, (string) $minLines);

        return $this;
    }

    /** Autoresize maximum visible lines (`maxLines`). Default `20`. */
    public function setMaxLines(int $maxLines): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_LINES, (string) $maxLines);

        return $this;
    }

    /** Editor font size in px (`fontSize`). */
    public function setFontSize(int $fontSize): self
    {
        $this->setHtmlAttribute(self::OPTION_FONT_SIZE, (string) $fontSize);

        return $this;
    }

    /** Editor CSS `font-family` (`fontFamily`). */
    public function setFontFamily(string $fontFamily): self
    {
        $this->setHtmlAttribute(self::OPTION_FONT_FAMILY, $fontFamily);

        return $this;
    }

    /** Soft wrap (`wrap`). */
    public function isWrap(bool $wrap = true): self
    {
        return $this->setDataAttr(self::OPTION_WRAP, $wrap);
    }

    /** Print margin column guide (`showPrintMargin`). Default `false`. */
    public function showPrintMargin(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SHOW_PRINT_MARGIN, $show);
    }

    /** Print margin column index (`printMarginColumn`). Default `80`. */
    public function setPrintMarginColumn(int $column): self
    {
        $this->setHtmlAttribute(self::OPTION_PRINT_MARGIN_COLUMN, (string) $column);

        return $this;
    }

    /** Highlight the active line (`highlightActiveLine`). Default `true`. */
    public function isHighlightActiveLine(bool $highlight = true): self
    {
        return $this->setDataAttr(self::OPTION_HIGHLIGHT_ACTIVE_LINE, $highlight);
    }

    /** Highlight all occurrences of the selected word (`highlightSelectedWord`). Default `true`. */
    public function isHighlightSelectedWord(bool $highlight = true): self
    {
        return $this->setDataAttr(self::OPTION_HIGHLIGHT_SELECTED_WORD, $highlight);
    }

    /** Show whitespace/invisibles (`showInvisibles`). */
    public function showInvisibles(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SHOW_INVISIBLES, $show);
    }

    /** Indent guide lines (`displayIndentGuides`). Default `true`. */
    public function showIndentGuides(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_DISPLAY_INDENT_GUIDES, $show);
    }

    /** Code-folding gutter widgets (`showFoldWidgets`). Default `true`. No effect if gutter is hidden. */
    public function showFoldWidgets(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SHOW_FOLD_WIDGETS, $show);
    }

    /** Mode syntax worker (`useWorker`). Default `true`. */
    public function isUseWorker(bool $useWorker = true): self
    {
        return $this->setDataAttr(self::OPTION_USE_WORKER, $useWorker);
    }
}
