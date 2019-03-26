<?php

namespace Tests\Feature;

use App\Models\JobManager;
use Tests\TestCase;

class JobManagerTest extends TestCase
{
    /**
     * Given the following job structure:
     *   a =>
     *   The result should be a sequence consisting of a single job a.
     */
    public function testWithSingleElementJob()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ]
        ];
        $jobManager = new JobManager($testArray);
        $result = $jobManager->getList();

        $this->assertEquals(['a'], $result);
    }

    /**
     * Given the following job structure:
     * a =>
     * b =>
     * c =>
     * The result should be a sequence containing all three jobs abc in no significant order.
     */
    public function testWithSingleJob()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => null
            ],
            [
                'elementName' => 'c',
                'dependency' => null
            ]
        ];
        $jobManager = new JobManager($testArray);
        $result = $jobManager->getList();

        $this->assertEquals(['a','b','c'], $result);
    }

    /**
     * Given the following job structure:
     * a =>
     * b => c
     * c =>
     * The result should be a sequence that positions c before b, containing all three jobs abc..
     */
    public function testWithSingleDependency()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => 'c'
            ],
            [
                'elementName' => 'c',
                'dependency' => null
            ]
        ];
        $jobManager = new JobManager($testArray);
        $result = $jobManager->getList();

        $this->assertEquals(['a','c','b'], $result);
    }

    /**
     * Given the following job structure:
     * a =>
     * b => c
     * c => f
     * d => a
     * e => b
     * f =>
     * The result should be a sequence that positions f before c, c before b, b before e and a before d
     * containing all six jobs abcdef.
     */
    public function testWithMultipleDependency()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => 'c'
            ],
            [
                'elementName' => 'c',
                'dependency' => 'f'
            ],
            [
                'elementName' => 'd',
                'dependency' => 'a'
            ],
            [
                'elementName' => 'e',
                'dependency' => 'b'
            ],
            [
                'elementName' => 'f',
                'dependency' => null
            ],
        ];
        $jobManager = new JobManager($testArray);
        $result = $jobManager->getList();

        $this->assertEquals(['a','f','c','b','d','e'], $result);
    }

    /**
     * Given the following job structure:
     * a =>
     * b =>
     * c => c
     * The result should be an error stating that jobs can’t depend on themselves.
     */
    public function testDependencyException()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => null
            ],
            [
                'elementName' => 'c',
                'dependency' => 'c'
            ]
        ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Jobs can’t depend on themselves.');

        $jobManager = new JobManager($testArray);
        $jobManager->getList();
    }

    /**
     * Given the following job structure:
     * a =>
     * b => z
     * The result should be an error stating
     */
    public function testDependencyNotFoundException()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => 'z'
            ]
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Dependencies not found.');

        $jobManager = new JobManager($testArray);
        $jobManager->getList();
    }

    /**
     * Given the following job structure:
     * a =>
     * b => c
     * c => f
     * d => a
     * e =>
     * f => b
     * The result should be an error stating that jobs can’t have circular dependencies.
     */
    public function testCircularDependenciesException()
    {
        $testArray = [
            [
                'elementName' => 'a',
                'dependency' => null
            ],
            [
                'elementName' => 'b',
                'dependency' => 'c'
            ],
            [
                'elementName' => 'c',
                'dependency' => 'f'
            ],
            [
                'elementName' => 'd',
                'dependency' => 'a'
            ],
            [
                'elementName' => 'e',
                'dependency' => 'b'
            ],
            [
                'elementName' => 'f',
                'dependency' => 'b'
            ],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Jobs can’t have circular dependencies.');

        $jobManager = new JobManager($testArray);
        $jobManager->getList();
    }
}
