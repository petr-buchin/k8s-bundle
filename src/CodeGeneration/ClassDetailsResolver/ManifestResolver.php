<?php

namespace Dealroadshow\Bundle\K8SBundle\CodeGeneration\ClassDetailsResolver;

use Dealroadshow\Bundle\K8SBundle\CodeGeneration\ClassDetails;
use Dealroadshow\Bundle\K8SBundle\Util\Str;
use Dealroadshow\K8S\Framework\App\AppInterface;

class ManifestResolver
{
    private string $namespacePrefix;

    public function __construct(string $namespacePrefix)
    {
        $this->namespacePrefix = trim($namespacePrefix, '\\');
    }

    /**
     * @param AppInterface $app
     * @param string       $manifestName
     * @param string       $suffix
     * @param bool         $useDedicatedDir Whether to create a dir for this manifest (like dir "Example" for "ExampleDeployment")
     *
     * @return ClassDetails
     */
    public function getClassDetails(AppInterface $app, string $manifestName, string $suffix, bool $useDedicatedDir = false): ClassDetails
    {
        $className = Str::withSuffix(Str::asClassName($manifestName), $suffix);
        $namespace = Str::asNamespace($app).'\\Manifests';
        $dir = Str::asDir($app).DIRECTORY_SEPARATOR.'Manifests';
        $fileName = $className.'.php';

        if ($useDedicatedDir) {
            $dirName = Str::asDirName($manifestName, $suffix);
            $namespace .= '\\'.$dirName;
            $dir .= DIRECTORY_SEPARATOR.$dirName;
        }

        return new ClassDetails($className, $namespace, $dir, $fileName);
    }
}
