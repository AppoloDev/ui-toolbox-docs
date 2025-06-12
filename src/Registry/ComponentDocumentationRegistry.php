<?php

namespace AppoloDev\UIToolboxDocBundle\Registry;

use AppoloDev\UIToolboxBundle\Attribute\UIComponentDoc;
use AppoloDev\UIToolboxBundle\Attribute\UIComponentExample;
use AppoloDev\UIToolboxBundle\Attribute\UIComponentProp;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

class ComponentDocumentationRegistry
{
    private array $components = [];

    public function __construct(KernelInterface $kernel)
    {
        $componentsPath = $kernel->getBundle('UIToolboxBundle')->getPath().'/src/UI';
        $finder = new Finder();
        $finder->files()->in($componentsPath)->name('*.php');

        foreach ($finder as $file) {
            $className = $this->extractClassName($file->getRealPath());
            if (!\class_exists($className)) {
                dd('Classe introuvable ou non définie correctement dans le fichier : '.$file->getRealPath(), $className);
            }

            $refClass = new ReflectionClass($className);
            if (empty($refClass->getAttributes(UIComponentDoc::class))) {
                continue;
            }

            $name = $refClass->getShortName();
            $parts = \explode('\\', $className);
            $category = $parts[\array_search('UI', $parts, true) + 1] ?? 'Uncategorized';

            $this->components[] = \array_merge([
                'name' => $name,
                'class' => $className,
                'path' => $file->getRealPath(),
                'category' => $category,
                'slug' => \strtolower($name),
            ], $this->extractComponentMetadata($className));
        }

        \usort($this->components, function ($a, $b) {
            return [$a['category'], $a['name']] <=> [$b['category'], $b['name']];
        });
    }

    public function all(): array
    {
        return $this->components;
    }

    private function extractClassName(string $filepath): string
    {
        $content = \file_get_contents($filepath);
        if ($content === false) {
            dd('Impossible de lire le fichier', ['filepath' => $filepath]);
        }

        \preg_match('/^namespace\s+(.+?);/m', $content, $ns);
        \preg_match('/^\s*class\s+(\w+)/m', $content, $class);

        if (!isset($ns[1], $class[1])) {
            dd('Échec extraction namespace ou class', [
                'filepath' => $filepath,
                'namespace_match' => $ns,
                'class_match' => $class,
            ]);
        }

        $className = $ns[1].'\\'.$class[1];

        if (!\class_exists($className)) {
            dd('Classe introuvable après construction', $className);
        }

        return $className;
    }

    private function extractComponentMetadata(string $className): array
    {
        if (!\class_exists($className)) {
            return [
                'doc' => null,
                'props' => [],
                'examples' => [],
            ];
        }

        $ref = new ReflectionClass($className);

        try {
            $docAttr = $ref->getAttributes(UIComponentDoc::class)[0] ?? null;
        } catch (Throwable $e) {
            dd("Erreur lors de l'extraction de l'attribut UIComponentDoc sur le composant : ".$className, $e->getMessage(), $e->getTraceAsString());
        }
        try {
            $examplesAttr = $ref->getAttributes(UIComponentExample::class);
        } catch (Throwable $e) {
            dd("Erreur lors de l'extraction de l'attribut UIComponentExample sur le composant : ".$className, $e->getMessage(), $e->getTraceAsString());
        }
        $props = [];

        foreach ($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            try {
                $attr = $property->getAttributes(UIComponentProp::class)[0] ?? null;
                if ($attr) {
                    /** @var UIComponentProp $instance */
                    $instance = $attr->newInstance();
                    $props[] = [
                        'name' => $property->getName(),
                        'description' => $instance->description,
                        'default' => $instance->default ?? null,
                        'acceptedValues' => $instance->acceptedValues ?? null,
                    ];
                }
            } catch (Throwable $e) {
                dd("Erreur lors de l'extraction de l'attribut UIComponentProp sur la propriété {$property->getName()} du composant : ".$className, $e->getMessage(), $e->getTraceAsString());
            }
        }


        return [
            'doc' => $docAttr?->newInstance(),
            'props' => $props,
            'examples' => \array_map(fn ($a) => $a->newInstance(), $examplesAttr),
        ];
    }
}
