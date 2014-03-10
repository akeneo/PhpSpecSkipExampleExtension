<?php

namespace spec\Akeneo\Runner\Maintainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;

class SkipExampleMaintainerSpec extends ObjectBehavior
{
    function it_is_a_maintainer()
    {
        $this->shouldImplement('PhpSpec\Runner\Maintainer\MaintainerInterface');
    }

    function its_priority_is_75()
    {
        $this->getPriority()->shouldBe(75);
    }

    function it_supports_example_that_has_doc_comment(
        ExampleNode $example,
        \ReflectionFunctionAbstract $funcRefl
    ) {
        $example->getFunctionReflection()->willReturn($funcRefl);
        $funcRefl->getDocComment()->willReturn('doc comment');

        $this->supports($example)->shouldBe(true);
    }

    function it_does_not_support_example_that_does_not_have_doc_comment(
        ExampleNode $example,
        \ReflectionFunctionAbstract $funcRefl
    ) {
        $example->getFunctionReflection()->willReturn($funcRefl);
        $funcRefl->getDocComment()->willReturn(false);

        $this->supports($example)->shouldBe(false);
    }

    function its_prepare_method_throws_skipping_exception_when_example_have_a_skip_tag(
        ExampleNode $example,
        SpecificationInterface $specification,
        MatcherManager $matchers,
        CollaboratorManager $collaborators,
        \ReflectionFunctionAbstract $funcRefl
    ) {
        $example->getFunctionReflection()->willReturn($funcRefl);
        $funcRefl->getDocComment()->willReturn("/**\n     * @skip it is unrunnable\n     */");

        $exception = new SkippingException('it is unrunnable');
        $this->shouldThrow($exception)->duringPrepare($example, $specification, $matchers, $collaborators);
    }

    function its_prepare_method_throws_skipping_exception_when_example_have_an_extension_tag_which_is_not_loaded(
        ExampleNode $example,
        SpecificationInterface $specification,
        MatcherManager $matchers,
        CollaboratorManager $collaborators,
        \ReflectionFunctionAbstract $funcRefl
    ) {
        $example->getFunctionReflection()->willReturn($funcRefl);
        $funcRefl->getDocComment()->willReturn("/**\n     * @require extension foobar\n     */");

        $exception = new SkippingException('Extension "foobar" is not loaded');
        $this->shouldThrow($exception)->duringPrepare($example, $specification, $matchers, $collaborators);
    }

    function its_prepare_method_throws_skipping_exception_when_example_have_a_php_tag_constraint_which_is_not_valid(
        ExampleNode $example,
        SpecificationInterface $specification,
        MatcherManager $matchers,
        CollaboratorManager $collaborators,
        \ReflectionFunctionAbstract $funcRefl
    ) {
        $example->getFunctionReflection()->willReturn($funcRefl);
        $funcRefl->getDocComment()->willReturn("/**\n     * @require php <5.3.3\n     */");

        $this->shouldThrow('PhpSpec\Exception\Example\SkippingException')->duringPrepare($example, $specification, $matchers, $collaborators);
    }
}
