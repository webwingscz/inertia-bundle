<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ScrollProvider;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Traversable;

/**
 * @template T
 */
class PaginatorScrollProvider extends LimitOffsetScrollMetadataProvider implements ScrollProviderInterface
{
    /**
     * @param Paginator<T> $paginator
     */
    public function __construct(
        protected readonly Paginator $paginator,
        int $limit,
        int $currentPage = 1,
        int $firstPage = 1,
        string $pageName = self::DEFAULT_PAGE_NAME,
    ) {
        parent::__construct(
            $this->paginator->count(),
            $limit,
            $currentPage,
            $firstPage,
            $pageName,
        );
        $this->paginator->getQuery()->setFirstResult($this->getOffset());
        $this->paginator->getQuery()->setMaxResults($this->getLimit());
    }

    public function setCurrentPage(int $page): void
    {
        parent::setCurrentPage($page);
        $this->paginator->getQuery()->setFirstResult($this->getOffset());
    }

    /**
     * @return array<string, Traversable<array-key, T>>
     */
    public function getData(string $wrapper): mixed
    {
        return [$wrapper => $this->paginator];
    }
}
