<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\ValueObject;

use Webmozart\Assert\Assert;

class ForumStatus
{
    public const PUBLIC = 'public';
    public const PRIVATE = 'private';

    public const VALUES = [
        self::PUBLIC,
        self::PRIVATE,
    ];

    private string $value;

    public function __construct(string $value)
    {
        self::inArray($value);

        $this->value = $value;
    }

    public static function inArray(string $value): void
    {
        Assert::inArray($value, self::VALUES);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPrivate(): bool
    {
        return $this->value === self::PRIVATE;
    }

    public function isPublic(): bool
    {
        return $this->value === self::PUBLIC;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public static function getStatusChoices(): array
    {
        return [
            self::PUBLIC => self::PUBLIC,
            self::PRIVATE => self::PRIVATE
        ];
    }
}
