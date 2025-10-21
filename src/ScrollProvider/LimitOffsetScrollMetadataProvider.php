<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ScrollProvider;

/**
 * Provides infinite scroll metadata for the classic limit and offset pagination.
 */
class LimitOffsetScrollMetadataProvider implements ScrollMetadataProviderInterface
{
    protected int $offset = 0;

    public function __construct(
        protected readonly int $total,
        protected readonly int $limit,
        int $currentPage = 1,
        protected readonly int $firstPage = 1,
        protected readonly string $pageName = self::DEFAULT_PAGE_NAME,
    ) {
        $this->setCurrentPage($currentPage);
    }

    public function getCurrentPage(): int
    {
        return (int) floor($this->offset / $this->limit) + $this->firstPage;
    }

    public function setCurrentPage(int $page): void
    {
        $this->offset = ($page - $this->firstPage) * $this->limit;
    }

    public function getPageCount(): int
    {
        return (int) ceil($this->total / $this->limit);
    }

    public function getFirstPage(): int
    {
        return $this->firstPage;
    }

    public function getLastPage(): int
    {
        return $this->firstPage + max($this->getPageCount() - 1, 0);
    }

    public function getPreviousPage(): int|null
    {
        $page = $this->getCurrentPage();

        if ($page <= $this->getFirstPage()) {
            return null;
        }

        return $page - 1;
    }

    public function getNextPage(): int|null
    {
        $page = $this->getCurrentPage();

        if ($page >= $this->getLastPage()) {
            return null;
        }

        return $page + 1;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getPageName(): string
    {
        return $this->pageName;
    }
}
