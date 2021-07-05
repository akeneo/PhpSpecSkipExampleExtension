<?php

declare(strict_types=1);

namespace Akeneo\Runner\Maintainer;

use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Specification;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Exception\Example\SkippingException;

final class SkipExampleMaintainer implements Maintainer
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExampleNode $example): bool
    {
        $specDocComments = $this->getSpecDocComment($example);
        $exampleDocCommnts = $this->getExampleDocComment($example);

        return count($this->getRequirements($specDocComments . $exampleDocCommnts)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {

        $specRequirements = $this->getRequirements($this->getSpecDocComment($example));
        $exampleRequirements = $this->getRequirements($this->getExampleDocComment($example));

        // Push example node requirement to the end of check list,
        // if any spec class requirements are missing, all example node will not be executed as well.
        $requirements = \array_merge($specRequirements, $exampleRequirements);

        foreach ($requirements as $requirement) {
            if (!class_exists($requirement) && !interface_exists($requirement)) {
                throw new SkippingException(
                    sprintf('"%s" is not available', $requirement)
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function teardown(
        ExampleNode $example,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {

    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
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
    private function getRequirements(string $docComment): array
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
     * @return string
     */
    private function getSpecDocComment(ExampleNode $example): string
    {
        return $example->getSpecification()->getClassReflection()->getDocComment() ?: '';
    }

    /**
     * Get example node doc comment
     * 
     * @param ExampleNode $example
     * 
     * @return string
     */
    private function getExampleDocComment(ExampleNode $example): string
    {
        return $example->getFunctionReflection()->getDocComment() ?: '';
    }
}
