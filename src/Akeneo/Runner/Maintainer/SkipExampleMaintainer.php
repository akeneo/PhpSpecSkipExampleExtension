<?php

namespace Akeneo\Runner\Maintainer;

use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Exception\Example\SkippingException;

class SkipExampleMaintainer implements MaintainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExampleNode $example)
    {
        return false !== $example->getFunctionReflection()->getDocComment();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        foreach ($this->getTags($example) as $tag) {

            if (preg_match('#@skip (.*)#', $tag, $match)) {
                throw $this->createSkippingException($match[1]);
            }

            if (preg_match('#@require extension (\w+)#', $tag, $match)) {
                if (!extension_loaded($match[1])) {
                    throw $this->createSkippingException(
                        sprintf('Extension "%s" is not loaded', $match[1])
                    );
                }
            }

            if (preg_match('#@require php (<=|<>|>=|>|==|=|!=|<)?(.*)$#', $tag, $match)) {
                if (false === version_compare(PHP_VERSION, $match[2], $match[1])) {
                    throw $this->createSkippingException(sprintf(
                        'Current php version (%s) violates constraint "%s"',
                        PHP_VERSION,
                        $match[1].$match[2]
                    ));
                }
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context,
                             MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 75;
    }

    /**
     * Get example tags
     *
     * @return array
     */
    protected function getTags(ExampleNode $example)
    {
        return array_filter(
            array_map(
                'trim',
                explode(
                    "\n",
                    str_replace(
                        "\r\n",
                        "\n",
                        $example->getFunctionReflection()->getDocComment()
                    )
                )
            ),
            function($docline) {
                return 0 === strpos($docline, '* @');
            }
        );
    }

    /**
     * Create an instance of the skipping exception
     *
     * @param string $message
     *
     * @return SkippingException
     */
    protected function createSkippingException($message)
    {
        return new SkippingException($message);
    }
}
