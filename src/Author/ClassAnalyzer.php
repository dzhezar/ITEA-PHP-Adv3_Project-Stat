<?php
/**
 * Created by PhpStorm.
 * User: dzhezar-bazar
 * Date: 13.12.18
 * Time: 18:13
 */

namespace App\Author;


use Symfony\Component\Finder\Finder;

/**
 * Class ClassAnalyzer describes Analyzing logic
 * @package App\Author
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
     * @param string $class
     * @return array|null
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
                $counterProps['public'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_PUBLIC));
                $counterProps['public']['static'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC));

                $counterProps['protected'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_PROTECTED));
                $counterProps['protected']['static'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PROTECTED));

                $counterProps['private'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_PRIVATE));
                $counterProps['private']['static'][] = sizeof($reflection->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PRIVATE));

                $counterMethods['public'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
                $counterMethods['public']['static'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC));

                $counterMethods['protected'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_PROTECTED));
                $counterMethods['protected']['static'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PROTECTED));

                $counterMethods['private'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_PRIVATE));
                $counterMethods['private']['static'][] = sizeof($reflection->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PRIVATE));

                $counter = ['properties'=>$counterProps, 'methods'=>$counterMethods];

                return $counter;
            }

        }
        return null;
    }
}