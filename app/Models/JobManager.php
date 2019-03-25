<?php
namespace App\Models;

use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;

/**
 * Class JobManager
 * @package App\Models
 */
class JobManager
{
    private $jobArray;

    /**
     * JobManager constructor.
     * @param null $array
     */
    public function __construct($array = null)
    {
        $this->jobArray = $array;
    }

    /**
     * Generate a list using Topological Sort
     * A topological sort is useful for determining dependency loading.
     * It tells you which elements need to be proceeded first in order to fulfill all dependencies in the correct order.
     * @return array
     * @throws \Exception
     */
    public function getList(): array
    {
        $sorter = new StringSort();
        foreach ($this->jobArray as $job) {
            if ($job['elementName'] === $job['dependency']) {
                throw new \Exception('Jobs can’t depend on themselves.');
            }
            $sorter->add($job['elementName'], $job['dependency']);
        }

        try {
            $result = $sorter->sort();
        } catch (CircularDependencyException $exception) {
            throw new \Exception('Jobs can’t have circular dependencies.');
        } catch (ElementNotFoundException $exception) {
            throw new \Exception('Dependencies not found.');
        }
        return $result;
    }
}
