<?php

namespace Akeneo\Runner\Maintainer;

use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Specification;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Exception\Example\SkippingException;

class SkipExampleMaintainer implements Maintainer
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
    public function prepare(ExampleNode $example, Specification $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        if ($docComment = $this->getDocComment($example)) {
            foreach ($this->getRequirements($docComment) as $requirement) {
                if (!class_exists($requirement) && !interface_exists($requirement)) {
                    throw new SkippingException(
                        sprintf('"%s" is not available', $requirement)
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, Specification $context,
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
    protected function getRequirements($docComment)
    {
        return array_map(
            function($tag) {
                preg_match('#@require ([^ ]*)#', $tag, $match);

                return $match[1];
            },
            array_filter(
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
                    return 0 === strpos($docline, '* @require');
                }
            )
        );
    }

    /**
     * Get spec doc comment
     *
     * @param ExampleNode $example
     *
     * @return string|false
     */
    protected function getDocComment(ExampleNode $example)
    {
        return $example->getSpecification()->getClassReflection()->getDocComment();
    }
}
