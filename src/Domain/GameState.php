<?php
// src/Domain/GameState.php

namespace App\Domain;

use Marcosh\LamPHPda\Either;
use Marcosh\LamPHPda\Maybe;

/**
 * @psalm-immutable
 */
final class GameState
{
    private const TOTAL_DISKS = 7;

    /** @var array<int, array<int>> */
    private array $pegs;

    /**
     * @param array<int, array<int>> $pegs
     */
    private function __construct(array $pegs)
    {
        $this->pegs = $pegs;
    }

    public static function initial(): self
    {
        return new self([
            range(self::TOTAL_DISKS, 1),
            [],
            []
        ]);
    }

    /**
     * @return array<int, array<int>>
     */
    public function getPegs(): array
    {
        return $this->pegs;
    }

    public function moveDisk(int $from, int $to): Either
    {
        $diskToMove = $this->pegs[$from][array_key_last($this->pegs[$from])] ?? null;

        return Maybe::fromNullable($diskToMove)
            ->toEither('No disk to move')
            ->bind(fn(int $diskToMove) => $this->validateMove($from, $to, $diskToMove)) // Use `bind` instead of `flatMap`
            ->map(fn() => $this->executeMove($from, $to));
    }

    private function validateMove(int $from, int $to, int $diskToMove): Either
    {
        if ($from < 0 || $from > 2 || $to < 0 || $to > 2) {
            return Either::left('Invalid peg numbers');
        }

        $topDisk = $this->pegs[$to][array_key_last($this->pegs[$to])] ?? null;
        if ($topDisk !== null && $diskToMove > $topDisk) {
            return Either::left('Cannot place larger disk on smaller one');
        }

        return Either::right($diskToMove);
    }

    private function executeMove(int $from, int $to): self
    {
        $newPegs = $this->pegs;
        $disk = array_pop($newPegs[$from]);
        $newPegs[$to][] = $disk;
        return new self($newPegs);
    }

    public function isComplete(): bool
    {
        return empty($this->pegs[0]) &&
               empty($this->pegs[1]) &&
               count($this->pegs[2]) === self::TOTAL_DISKS;
    }
}
