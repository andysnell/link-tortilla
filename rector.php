<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withImportNames(importShortClasses: false)
    ->withCache(__DIR__ . '/build/rector')
    ->withRootFiles()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php83: true)
    ->withAttributesSets(phpunit: true)
    ->withPreparedSets(codeQuality: true, codingStyle: true, typeDeclarations: true, phpunit: true)
    ->withRules([
        DeclareStrictTypesRector::class,
        AddTypeToConstRector::class,
    ])
    ->withSkip([
        ClosureToArrowFunctionRector::class,
    ]);
