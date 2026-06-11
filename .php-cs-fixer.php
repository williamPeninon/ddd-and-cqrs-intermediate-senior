<?php

$finder = PhpCsFixer\Finder::create()->in(__DIR__ . '/src')->in(__DIR__ . '/tests')->in(__DIR__ . '/features/bootstrap');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);
