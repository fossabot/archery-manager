<?php

namespace App\Scrapper;

class FftaResult
{
    private int $position;
    private string $name;
    private string $club;
    private string $license;
    private string $category;
    private int $distance;
    private int $size;
    private int $score1;
    private int $score2;
    private int $total;
    private int $nb10;
    private int $nb10p;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): FftaResult
    {
        $this->position = $position;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): FftaResult
    {
        $this->name = $name;

        return $this;
    }

    public function getClub(): string
    {
        return $this->club;
    }

    public function setClub(string $club): FftaResult
    {
        $this->club = $club;

        return $this;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function setLicense(string $license): FftaResult
    {
        $this->license = $license;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): FftaResult
    {
        $this->category = $category;

        return $this;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): FftaResult
    {
        $this->distance = $distance;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): FftaResult
    {
        $this->size = $size;

        return $this;
    }

    public function getScore1(): int
    {
        return $this->score1;
    }

    public function setScore1(int $score1): FftaResult
    {
        $this->score1 = $score1;

        return $this;
    }

    public function getScore2(): int
    {
        return $this->score2;
    }

    public function setScore2(int $score2): FftaResult
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): FftaResult
    {
        $this->total = $total;

        return $this;
    }

    public function getNb10(): int
    {
        return $this->nb10;
    }

    public function setNb10(int $nb10): FftaResult
    {
        $this->nb10 = $nb10;

        return $this;
    }

    public function getNb10p(): int
    {
        return $this->nb10p;
    }

    public function setNb10p(int $nb10p): FftaResult
    {
        $this->nb10p = $nb10p;

        return $this;
    }
}
