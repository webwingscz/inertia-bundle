<?php

namespace Webwings\InertiaBundle\Service;

use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use Twig\Error\Error as TwigError;
use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\InertiaPage;

class InertiaService implements InertiaInterface
{
    /** @var array<string, mixed> */
    protected array $props = [];
    /** @var array<string, mixed> */
    protected array $viewData = [];
    /** @var array<string, mixed> */
    protected array $context = [];
    protected string $rootView;
    protected string|null $version = null;

    public function __construct(
        protected readonly Environment $engine,
        protected readonly RequestStack $requestStack,
        protected readonly SerializerInterface $serializer,
        string $rootView,
    ) {
        $this->setRootView($rootView);
    }

    public function share(string $key, mixed $value = null): void
    {
        $this->props[$key] = $value;
    }

    public function getShared(string $key): mixed
    {
        return $this->props[$key] ?? null;
    }

    public function allShared(): array
    {
        return $this->props;
    }

    public function viewData(string $key, mixed $value = null): void
    {
        $this->viewData[$key] = $value;
    }

    public function getViewData(string $key): mixed
    {
        return $this->viewData[$key] ?? null;
    }

    public function allViewData(): array
    {
        return $this->viewData;
    }

    public function getVersion(): string|null
    {
        return $this->version;
    }

    public function version(string|null $version): void
    {
        $this->version = $version;
    }

    public function getRootView(): string
    {
        return $this->rootView;
    }

    public function setRootView(string $rootView): void
    {
        $this->rootView = $rootView;
    }

    public function context(string $key, mixed $value = null): void
    {
        $this->context[$key] = $value;
    }

    public function getContext(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }

    public function allContext(): array
    {
        return $this->context;
    }

    /**
     * @throws SerializerException
     * @throws TwigError
     */
    public function render(
        string $component,
        array $props = [],
        array $viewData = [],
        array $context = [],
        string|null $url = null,
        bool $clearHistory = false,
        bool $encryptHistory = false,
    ): Response {
        $request = $this->getRequest();
        $headers = InertiaHeaders::fromRequest($request);
        $url = $url ?? $request->getRequestUri() ?: null;
        $viewData = [...$this->viewData, ...$viewData];
        $props = [...$this->props, ...$props];
        $page = new InertiaPage(
            $headers,
            $component,
            $props,
            $url,
            $this->version,
            $clearHistory,
            $encryptHistory,
        );

        if ($headers->isInertiaRequest()) {
            return new JsonResponse(
                $this->serialize($page, $context),
                Response::HTTP_OK,
                [
                    'Vary' => 'Accept',
                    'X-Inertia' => true,
                ],
                true,
            );
        }

        $response = new Response();
        $response->setContent($this->engine->render(
            $this->rootView,
            ['page' => $page, 'viewData' => $viewData],
        ));

        return $response;
    }

    /**
     * Function to redirect users from the backend to a non inertia page.
     */
    public function location(string|RedirectResponse $url): Response
    {
        $request = $this->getRequest();

        if ($url instanceof RedirectResponse) {
            $url = $url->getTargetUrl();
        }

        if ($request->headers->has(InertiaHeaders::INERTIA)) {
            return new Response('', Response::HTTP_CONFLICT, [
                InertiaHeaders::LOCATION => $url,
            ]);
        }

        return new RedirectResponse($url);
    }

    public function serialize(InertiaPage $page, array $context = []): string
    {
        return $this->serializer->serialize(
            $page,
            'json',
            [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function () {
                    return null;
                },
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                ...$this->context,
                ...$context,
            ]
        );
    }

    private function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest()
            ?? throw new LogicException('Could not obtain current request');
    }
}
