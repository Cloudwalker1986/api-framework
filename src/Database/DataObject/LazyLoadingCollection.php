<?php

declare(strict_types=1);

namespace ApiCore\Database\DataObject;

use ApiCore\Database\BaseRepository;
use ApiCore\Database\Enum\LazyLoadingCollectionStrategy;
use ApiCore\Database\Hydrator\HydrateEntity;
use ApiCore\Utils\CollectionInterface;
use ApiCore\Utils\TypedCollection;

class LazyLoadingCollection extends TypedCollection
{
    private bool $initialized = false;

    private $loader;


    private int $limit = 1000;

    private int $offset = 0;

    private string|int $parentId;

    public function __construct(
        callable $loader,
        private readonly HydrateEntity $hydrateEntity,
        private readonly string $fqcnEntity,
        private readonly BaseRepository $baseRepository,
        private readonly LazyLoadingCollectionStrategy $lazyLoadingStrategy,
        string|int $parentId
    ) {
        $this->loader = $loader;
        $this->parentId = $parentId;
    }

    public function valid(): bool
    {
        $isValid = parent::valid();

        if (!$isValid && $this->lazyLoadingStrategy->isOnDemandEachTime()) {
            if (count($this->elements) === $this->limit) {
                $this->elements = [];
                $this->offset += $this->limit;
                $this->load();
                $isValid = true;
            } else {
                $this->offset = 0;
                $isValid = false;
                $this->elements = [];
            }
            $this->initialized = false;
        }

        return $isValid;
    }

    public function rewind(): void
    {
        $this->load();
        parent::rewind(); // TODO: Change the autogenerated stub
    }

    private function load(): void
    {
        if ($this->lazyLoadingStrategy->isOnDemandEachTime()) {
            $dbRows = call_user_func($this->loader, $this->limit, $this->offset, $this->parentId);
            array_walk($dbRows, function($dbRow) {
                parent::add($this->hydrateEntity->hydrate($this->fqcnEntity, $dbRow, $this->baseRepository));
            });
            return;
        }

        if (!$this->initialized) {
            do {
                $dbRows = call_user_func($this->loader, $this->limit, $this->offset, $this->parentId);
                array_walk($dbRows, function($dbRow) {
                    parent::add($this->hydrateEntity->hydrate($this->fqcnEntity, $dbRow, $this->baseRepository));
                });
            } while(count($dbRows) === $this->limit);
            $this->initialized = true;
        }
    }
}