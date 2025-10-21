<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => false,
        'phpdoc_separation' => false,
        'phpdoc_to_comment' => [
            'ignored_tags' => ['var'],
        ],
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'not_operator_with_successor_space' => true,
        'nullable_type_declaration' => [
            'syntax' => 'union',
        ],
    ])
    ->setFinder($finder)
    ->setLineEnding("\n");
