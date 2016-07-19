<?php

namespace spec\Akeneo\Runner\Maintainer;

use Akeneo\Runner\Maintainer\SkipExampleMaintainer;
use PhpSpec\ObjectBehavior;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Specification;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Loader\Node\SpecificationNode;

/**
 * @mixin SkipExampleMaintainer
 */
class SkipExampleMaintainerSpec extends ObjectBehavior
{
    function it_is_a_maintainer()
    {
        $this->shouldImplement(Maintainer::class);
    }

    function its_priority_is_75()
    {
        $this->getPriority()->shouldBe(75);
    }

    function it_supports_specification_that_has_doc_comment(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass
    ) {
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn('doc comment');

        $this->supports($example)->shouldBe(true);
    }

    function it_does_not_support_specification_that_does_not_have_doc_comment(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass
    ) {
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn(false);

        $this->supports($example)->shouldBe(false);
    }

    function its_prepare_method_throws_skipping_exception_when_specification_requires_a_non_existing_interface(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn("/**\n     * @require Foo\\Bar\n     */");

        $exception = new SkippingException('"Foo\\Bar" is not available');
        $this->shouldThrow($exception)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_specification_requires_an_existing_class(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn("/**\n     * @require Akeneo\Runner\Maintainer\SkipExampleMaintainer\n     */");

        $this->shouldNotThrow('PhpSpec\Exception\Example\SkippingException')->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_specification_requires_an_existing_interface(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn("/**\n     * @require PhpSpec\Runner\Maintainer\Maintainer\n     */");

        $this->shouldNotThrow('PhpSpec\Exception\Example\SkippingException')->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_ignores_other_annotation(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ){
        $example->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn("/**\n     * @author foo@example.com \n     */");

        $this->shouldNotThrow('PhpSpec\Exception\Example\SkippingException')->duringPrepare($example, $context, $matchers, $collaborators);
    }
}
