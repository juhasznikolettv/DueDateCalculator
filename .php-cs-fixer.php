<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->ignoreVCSIgnored(true)
    ->exclude('vendor/');

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'array_indentation' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'blank_line_before_statement' => true,
        'declare_strict_types' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'multiline_comment_opening_closing' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'single_line_throw' => false,
        'strict_param' => true,
        'strict_comparison' => true,
        'class_definition' => [
            'single_line' => false,
        ],
    ])
    ->setFinder($finder);

return $config;