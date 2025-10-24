<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

/**
 * @phpstan-type InertiaErrors array<string, string>
 * @phpstan-type InertiaErrorBag array<string, InertiaErrors>
 */
readonly class InertiaFlash
{
    public const string FLASH_ERRORS = 'inertia.errors';

    public function __construct(protected FlashBagInterface|null $flashBag)
    {
    }

    public static function fromRequest(Request $request): self
    {
        if ($request->hasSession() && $request->getSession() instanceof FlashBagAwareSessionInterface) {
            $flashBag = $request->getSession()->getFlashBag();
        } else {
            $flashBag = null;
        }

        return new self($flashBag);
    }

    /**
     * @return InertiaErrorBag|InertiaErrors
     */
    public function getErrors(): array
    {
        return $this->flashBag?->get(self::FLASH_ERRORS) ?? [];
    }

    /**
     * @param InertiaErrors $errors
     */
    public function setErrors(array $errors, string|null $errorBag = null): void
    {
        if ($errorBag !== null) {
            $errors = [$errorBag => $errors];
        }

        $this->flashBag?->set(self::FLASH_ERRORS, $errors);
    }
}
