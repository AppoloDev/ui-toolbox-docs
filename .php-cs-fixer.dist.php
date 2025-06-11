<?php


$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'var',
        'vendor',
        'node_modules',
        'tests/Fixtures',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PSR12' => true,
        'strict_param' => true,
        'declare_strict_types' => false,
        'native_function_invocation' => [
            'include' => ['@internal'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_to_comment' => false,
        'modernize_strpos' => true,
        'no_superfluous_phpdoc_tags' => false,
        'not_operator_with_space' => false,
        'single_line_throw' => false,
        'yoda_style' => false,
        'no_useless_else' => true,
        'simplified_if_return' => true,
        'combine_consecutive_unsets' => true,
        'php_unit_internal_class' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
