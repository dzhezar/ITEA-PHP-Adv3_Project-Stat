<?php

/*
 * This file is part of the "PHP Project Stat" project.
 *
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Author;

use Symfony\Component\Finder\Finder;

/**
 * Class ClassAnalyzer describes Analyzing logic
 */
final class ClassAnalyzer
{
    /**
     * @var string
     */
    private $rootDir;
    /**
     * @var string
     */
    private $rootNamespace;

    /**
     * ClassAnalyzer constructor.
     *
     * @param string $rootDir
     * @param string $rootNamespace
     */
    public function __construct(string $rootDir, string $rootNamespace)
    {
        $this->rootDir = $rootDir;
        $this->rootNamespace = $rootNamespace;
    }

    /**
     * Function describes analyzing process
     *
     * @param string $class
     *
     * @return null|array
     */
    public function analyze(string $class): ?array
    {
        $counterProps = [];
        $counterMethods = [];
        $finder = new Finder();
        $finder
            ->in($this->rootDir)
            ->files()
            ->name('/^[A-Z].+\.php$/');

        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $fullClassName = $this->rootNamespace
                . '\\'
                . \str_replace('/', '\\', \rtrim($path, '.php'));
            try {
                $reflection = new \ReflectionClass($fullClassName);
            } catch (\ReflectionException $e) {
                continue;
            }

            if ($fullClassName == $class) {
                $counterProps['public'][] = \count($reflection->getProperties(\ReflectionProperty::IS_PUBLIC));
                $counterProps['public']['static'][] = \count($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC));

                $counterProps['protected'][] = \count($reflection->getProperties(\ReflectionProperty::IS_PROTECTED));
                $counterProps['protected']['static'][] = \count($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PROTECTED));

                $counterProps['private'][] = \count($reflection->getProperties(\ReflectionProperty::IS_PRIVATE));
                $counterProps['private']['static'][] = \count($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PRIVATE));

                $counterMethods['public'][] = \count($reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
                $counterMethods['public']['static'][] = \count($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC));

                $counterMethods['protected'][] = \count($reflection->getMethods(\ReflectionMethod::IS_PROTECTED));
                $counterMethods['protected']['static'][] = \count($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PROTECTED));

                $counterMethods['private'][] = \count($reflection->getMethods(\ReflectionMethod::IS_PRIVATE));
                $counterMethods['private']['static'][] = \count($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PRIVATE));

                $counter = ['properties'=>$counterProps, 'methods'=>$counterMethods];

                return $counter;
            }
        }

        return null;
    }
}
