<?php

declare(strict_types=1);

use PhpCsFixerCustomFixers\Fixer\CommentSurroundedBySpacesFixer;

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
;

return new PhpCsFixer\Config()
    ->setCacheFile(__DIR__.'/var/static/php-cs-fixer/.php-cs-fixer.cache')
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        CommentSurroundedBySpacesFixer::name() => true,
        'multiline_promoted_properties' => true,
        'full_opening_tag' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'native_function_invocation' => true,
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => ['arrays', 'arguments', 'parameters'],
        ],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'line_ending' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'modifier_keywords' => true,
        'phpdoc_separation' =>['skip_unlisted_annotations' => true],
        'phpdoc_line_span' => ['method' => 'single', 'property' => 'single'],
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
    ->setFinder($finder)
    ;
