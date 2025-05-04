<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'blank_line_after_namespace' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'no_superfluous_phpdoc_tags' => true,
        'blank_line_before_statement' => false,
        'single_quote' => false,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(false);
