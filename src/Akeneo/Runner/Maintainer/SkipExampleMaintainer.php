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
        return false !== $this->getDocComment($example);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        if ($docComment = $this->getDocComment($example)) {
            foreach ($this->getRequiredInterfaces($docComment) as $interface) {
                if (!interface_exists($interface)) {
                    throw $this->createSkippingException(
                        sprintf('Interface "%s" is not available', $interface)
                    );
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
     * Get required interfaces
     *
     * @param string $docComment
     *
     * @return array
     */
    protected function getRequiredInterfaces($docComment)
    {
        $tags = array_filter(
            array_map(
                'trim',
                explode(
                    "\n",
                    str_replace(
                        "\r\n",
                        "\n",
                        $docComment
                    )
                )
            ),
            function($docline) {
                return 0 === strpos($docline, '* @');
            }
        );

        $interfaces = array();
        foreach ($tags as $tag) {
            if (preg_match('#@require interface (.*)#', $tag, $match)) {
                $interfaces[] = $match[1];
            }
        }

        return $interfaces;
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

    /**
     * Get spec doc comment
     *
     * @param ExampleNode $example
     *
     * @return string
     */
    protected function getDocComment(ExampleNode $example)
    {
        return $example->getSpecification()->getClassReflection()->getDocComment();
    }
}
