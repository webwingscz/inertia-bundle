<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Webwings\InertiaBundle\InertiaFlash;
use Webwings\InertiaBundle\InertiaHeaders;

/**
 * Default validation error response factory that flashes the violations as errors and redirects to the previous page.
 *
 * @implements InertiaResponseFactoryInterface<ValidationFailedException>
 * @phpstan-import-type InertiaErrors from InertiaFlash
 */
readonly class ValidationFailedResponseFactory implements InertiaResponseFactoryInterface
{
    use RedirectBackResponseFactoryTrait;
    use ExtractThrowableResponseFactoryTrait;

    public function __construct(protected TranslatorInterface|null $translator)
    {
    }

    public static function getPriority(): int
    {
        return -255;
    }

    public function isHandling(Request $request, Throwable $throwable): Throwable|null
    {
        return $this->extractThrowable($throwable, ValidationFailedException::class);
    }

    public function handle(Request $request, Throwable $throwable): Response
    {
        $headers = InertiaHeaders::fromRequest($request);
        $flash = InertiaFlash::fromRequest($request);
        $flash->setErrors(
            $this->getErrorsFromViolations($throwable->getViolations()),
            $headers->getErrorBag(),
        );

        return $this->redirectBack($request);
    }

    /**
     * @return InertiaErrors
     */
    public function getErrorsFromViolations(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            if ($this->translator !== null) {
                $errors[$violation->getPropertyPath()] = $this->translator->trans(
                    (string) $violation->getMessage(),
                    $violation->getParameters()
                );
            } else {
                $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
            }
        }

        return $errors;
    }
}
