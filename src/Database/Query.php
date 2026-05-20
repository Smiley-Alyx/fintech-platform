<?php

declare(strict_types=1);

namespace App\Database;

/**
 * @template T of object
 */
interface Query
{
    /**
     * @param class-string<T> $model
     * @return T|null
     */
    public function findBy(string $model, string $field, mixed $value): ?object;

    /**
     * @param class-string<T> $model
     * @param array<string, mixed> $conditions
     * @return T[]
     */
    public function getBy(string $model, array $conditions): array;

    public function save(object $model): void;

    /**
     * @param class-string<T> $model
     * @param array<string, mixed> $attributes
     * @return T
     */
    public function create(string $model, array $attributes): object;
}
