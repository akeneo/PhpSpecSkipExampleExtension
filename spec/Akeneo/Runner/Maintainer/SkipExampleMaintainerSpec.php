<?php

declare(strict_types=1);

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
final class SkipExampleMaintainerSpec extends ObjectBehavior
{
    const MISSING_REQUIRE    = "/**\n     * @require Foo\\Bar\n     */";
    const EXISTING_CLASS     = "/**\n     * @require Akeneo\Runner\Maintainer\SkipExampleMaintainer\n     */";
    const EXISTING_INTERFACE = "/**\n     * @require PhpSpec\Runner\Maintainer\Maintainer\n     */";

    function it_is_a_maintainer(): void
    {
        $this->shouldImplement(Maintainer::class);
    }

    function its_priority_is_75(): void
    {
        $this->getPriority()->shouldBe(75);
    }

    function it_supports_specification_that_has_require_class_doc_comment(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(self::MISSING_REQUIRE);
        $refMethod->getDocComment()->willReturn(false);

        $this->supports($example)->shouldBe(true);
    }

    function it_supports_example_node_that_has_require_method_doc_comment(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(false);
        $refMethod->getDocComment()->willReturn(self::MISSING_REQUIRE);

        $this->supports($example)->shouldBe(true);
    }

    function it_does_not_support_specification_that_does_not_have_any_doc_comment(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);

        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(false);
        $refMethod->getDocComment()->willReturn(false);

        $this->supports($example)->shouldBe(false);
    }

    function its_prepare_method_throws_skipping_exception_when_specification_requires_a_non_existing_interface(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn(self::MISSING_REQUIRE);
        $refMethod->getDocComment()->willReturn(self::EXISTING_INTERFACE);

        $exception = new SkippingException('"Foo\\Bar" is not available');
        $this->shouldThrow($exception)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_specification_requires_an_existing_class(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(self::EXISTING_CLASS);
        $refMethod->getDocComment()->willReturn(false);

        $this->shouldNotThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_example_node_requires_an_existing_class(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(false);
        $refMethod->getDocComment()->willReturn(self::EXISTING_CLASS);

        $this->shouldNotThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_specification_requires_an_existing_interface(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn(self::EXISTING_INTERFACE);
        $refMethod->getDocComment()->willReturn(false);

        $this->shouldNotThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_does_not_throw_exception_when_example_node_requires_an_existing_interface(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);

        $refClass->getDocComment()->willReturn(false);
        $refMethod->getDocComment()->willReturn(self::EXISTING_INTERFACE);

        $this->shouldNotThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_throw_exception_when_specification_requires_an_existing_class_but_example_node_requires_an_missing_class(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn(self::EXISTING_CLASS);
        $refMethod->getDocComment()->willReturn(self::MISSING_REQUIRE);

        $this->shouldThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_throw_exception_when_specification_requires_an_missing_class_but_example_node_requires_an_existing_class(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn(self::MISSING_REQUIRE);
        $refMethod->getDocComment()->willReturn(self::EXISTING_CLASS);

        $this->shouldThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }

    function its_prepare_method_ignores_other_annotation(
        ExampleNode $example,
        SpecificationNode $specification,
        \ReflectionClass $refClass,
        \ReflectionFunction $refMethod,
        Specification $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ): void {
        $example->getSpecification()->willReturn($specification);
        $example->getFunctionReflection()->willReturn($refMethod);
        $specification->getClassReflection()->willReturn($refClass);
        $refClass->getDocComment()->willReturn("/**\n     * @author foo@example.com \n     */");
        $refMethod->getDocComment()->willReturn("/**\n     * @author foo@example.com \n     */");

        $this->shouldNotThrow(SkippingException::class)->duringPrepare($example, $context, $matchers, $collaborators);
    }
}
