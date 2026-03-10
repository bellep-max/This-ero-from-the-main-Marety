<?php

namespace App\Enums;

use BackedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ValueError;

trait EnumMethods
{
    /**
     * Get an associative array of [value => name].
     */
    public static function toArray(): array
    {
        $cases = static::cases();

        if (($cases[0] ?? null) instanceof BackedEnum) {
            return array_column(array_map(function ($case) {
                /** @var static $case */
                return ['name' => $case->label(), 'value' => $case->value];
            }, $cases), 'name', 'value');
        }

        return array_column($cases, 'name');
    }

    /**
     * Get an array of values.
     */
    public static function values(): array
    {
        $cases = static::cases();

        if (($cases[0] ?? null) instanceof BackedEnum) {
            return array_column($cases, 'value');
        }

        return array_column($cases, 'name');
    }

    public static function toOptions(): array
    {
        return array_map(function ($case) {
            /** @var static $case */
            if ($case instanceof BackedEnum) {
                return ['text' => $case->label(), 'value' => $case->value];
            } else {
                return ['text' => $case->label(), 'value' => $case->name];
            }
        }, static::cases());

    }

    /**
     * Get an array of names.
     */
    public static function names(): array
    {
        $cases = static::cases();

        return array_column($cases, 'name');
    }

    public function label(): string
    {
        $labels = static::getLabels();

        return $labels[$this->value] ?? Str::headline($this->name);
    }

    public static function getLabels(): array
    {
        return [];
    }

    public static function except(array $keys = []): array
    {
        $keys = Arr::map($keys, fn ($key) => $key instanceof BackedEnum ? $key->value : $key);

        return array_filter(
            static::cases(),
            fn ($item) => !in_array($item->value, $keys)
        );
    }

    public static function valuesExcept(array $keys = []): array
    {
        $keys = Arr::map($keys, fn ($key) => $key instanceof BackedEnum ? $key->value : $key);

        return array_values(
            array_filter(
                static::values(),
                fn ($item) => !in_array($item, $keys)
            )
        );
    }

    public function in(array|Arrayable $values): bool
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $values = Arr::map($values, fn ($value) => $value instanceof BackedEnum ? $value->value : $value);

        return in_array($this->value, $values);
    }

    public static function resolve(mixed $value): static|string|int|null
    {
        if (is_string($value) || is_int($value)) {
            return static::tryFrom($value) ?? $value;
        }

        return $value;
    }

    public static function fromName(string $name): string
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name) {
                return $status->value;
            }
        }

        throw new ValueError("$name is not a valid backing value for enum " . self::class);
    }
}
