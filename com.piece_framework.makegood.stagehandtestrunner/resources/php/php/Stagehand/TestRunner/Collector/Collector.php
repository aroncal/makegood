<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5.3
 *
 * Copyright (c) 2007-2012 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007-2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 3.2.0
 * @since      File available since Release 2.1.0
 */

namespace Stagehand\TestRunner\Collector;

use Symfony\Component\Finder\Finder;

use Stagehand\TestRunner\Collector\CollectingTypeFactory;
use Stagehand\TestRunner\Core\ApplicationContext;
use Stagehand\TestRunner\Core\TestTargets;

/**
 * The base class for test collectors.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007-2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 3.2.0
 * @since      Class available since Release 2.1.0
 */
abstract class Collector
{
    protected $suite;

    /**
     * @var \Stagehand\TestRunner\Core\TestTargets
     * @since Property available since Release 3.0.0
     */
    protected $testTargets;

    /**
     * @var \Stagehand\TestRunner\Collector\CollectingTypeFactory
     * @since Property available since Release 3.0.0
     */
    protected $collectingTypeFactory;

    /**
     * Initializes some properties of an instance.
     *
     * @param \Stagehand\TestRunner\Core\TestTargets $testTargets
     */
    public function __construct(TestTargets $testTargets)
    {
        $this->testTargets = $testTargets;
        $this->suite = $this->createTestSuite('The test suite generated by Stagehand_TestRunner');
    }

    /**
     * Collects tests.
     *
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function collect()
    {
        $collector = array($this, 'collectTestCasesFromFile');
        $this->testTargets->walkOnResources(function ($resource, $index, TestTargets $testTargets) use ($collector) {
            $absoluteTargetPath = realpath($resource);
            if ($absoluteTargetPath === false) {
                throw new \UnexpectedValueException(sprintf('The directory or file [ %s ] is not found', $resource));
            }

            if (is_dir($absoluteTargetPath)) {
                $finder = Finder::create()
                    ->files()
                    ->in($absoluteTargetPath)
                    ->depth($testTargets->recursivelyScans() ? '>= 0' : '== 0')
                    ->sortByName();
                foreach ($finder as $file) {
                    call_user_func($collector, $file->getPathname());
                }
            } else {
                call_user_func($collector, $absoluteTargetPath);
            }
        });

        return $this->suite;
    }

    /**
     * @param string $testCase
     * @since Method available since Release 2.10.0
     */
    abstract public function collectTestCase($testCase);

    /**
     * @param \Stagehand\TestRunner\Collector\CollectingTypeFactory $collectingTypeFactory
     * @since Method available since Release 3.0.0
     */
    public function setCollectingTypeFactory(CollectingTypeFactory $collectingTypeFactory)
    {
        $this->collectingTypeFactory = $collectingTypeFactory;
    }

    /**
     * Creates the test suite object.
     *
     * @param string $name
     * @return mixed
     */
    abstract protected function createTestSuite($name);

    /**
     * Collects all test cases included in the given file.
     *
     * @param string $file
     */
    public function collectTestCasesFromFile($file)
    {
        if (!$this->testTargets->shouldTreatFileAsTest($file)) return;

        foreach ($this->findNewClasses($file) as $newClass) {
            $collectingTypeFactory = $this->collectingTypeFactory->create(
                $newClass,
                $this->testTargets->getRequiredSuperTypes()
            );
            if ($collectingTypeFactory->isTest()) {
                $this->collectTestCase($newClass);
            }
        }
    }

    /**
     * @param string $file
     * @return boolean
     * @since Method available since Release 2.14.0
     */
    protected function findNewClasses($file)
    {
        $currentClasses = get_declared_classes();
        if (!include_once $file) return array();
        return array_values(array_diff(get_declared_classes(), $currentClasses));
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
