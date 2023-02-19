<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        'strict_param' => true,
        'declare_strict_types' => true,
        'single_line_comment_spacing' => true,
        'single_line_after_imports' => true,
        'no_empty_phpdoc' => true,
        'trim_array_spaces' => true,
        'braces' => [
            'allow_single_line_closure' => true,
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'array_syntax' => ['syntax' => 'short'],
        'constant_case' => [
            'case' => 'lower',
        ],
        'cast_spaces' => ['space' => 'single'],
        'control_structure_braces' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
            'import_classes' => true,
        ],
        'no_unused_imports' => true,
        'ordered_imports' =>
            ['sort_algorithm' => 'alpha',
                'imports_order' =>
                    ['const', 'class', 'function'],
            ],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'phpdoc_add_missing_param_annotation' =>
            ['only_untyped' => true],
        'phpdoc_align' =>
            ['align' => 'vertical'],
        'phpdoc_indent' => true,
        'single_blank_line_at_eof' => true,
        'no_whitespace_in_blank_line' => true,
        'array_indentation' => true
    ])
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder);
