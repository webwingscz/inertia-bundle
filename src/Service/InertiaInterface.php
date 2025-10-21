<?php

namespace Webwings\InertiaBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Twig\Error\Error as TwigError;
use Webwings\InertiaBundle\InertiaPage;

interface InertiaInterface
{
    /**
     * Share a value across all Inertia responses.
     */
    public function share(string $key, mixed $value = null): void;

    /**
     * Get a shared value.
     */
    public function getShared(string $key): mixed;

    /**
     * Get all shared values.
     *
     * @return array<string, mixed>
     */
    public function allShared(): array;

    /**
     * Set a value for the root twig template.
     */
    public function viewData(string $key, mixed $value = null): void;

    /**
     * Get a root twig template value.
     */
    public function getViewData(string $key): mixed;

    /**
     * Get all root twig template values.
     *
     * @return array<string, mixed>
     */
    public function allViewData(): array;

    /**
     * Set a serializer context value.
     */
    public function context(string $key, mixed $value = null): void;

    /**
     * Get a serializer context value.
     */
    public function getContext(string $key): mixed;

    /**
     * Get all serializer context values.
     *
     * @return array<string, mixed>
     */
    public function allContext(): array;

    /**
     * Set current asset version.
     */
    public function version(string $version): void;

    /**
     * Get current version.
     */
    public function getVersion(): string|null;

    /**
     * Set current root twig template.
     */
    public function setRootView(string $rootView): void;

    /**
     * Get current root twig template.
     */
    public function getRootView(): string|null;

    /**
     * Force a regular synchronous redirect.
     */
    public function location(RedirectResponse|string $url): Response;

    /**
     * Create a valid HTML or JSON Inertia response based on the current request.
     *
     * @param  string               $component path to the page component, relative to your asset root
     * @param  array<string, mixed> $props     parameters for the page component
     * @param  array<string, mixed> $viewData  parameters for the root twig template
     * @param  array<string, mixed> $context   serializer context for InertiaPage serialization
     * @param  string|null          $url       URL of the page
     * @throws TwigError
     * @throws SerializerException
     */
    public function render(
        string $component,
        array $props = [],
        array $viewData = [],
        array $context = [],
        string|null $url = null,
        bool $clearHistory = false,
        bool $encryptHistory = false,
    ): Response;

    /**
     * Serialize InertiaPage to JSON.
     *
     * @param  array<string, mixed> $context
     * @throws SerializerException
     */
    public function serialize(InertiaPage $page, array $context = []): string;
}
