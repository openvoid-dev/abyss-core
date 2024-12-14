<?php

namespace Abyss\Ward;

class WardBlueprint
{
    /**
     * All items in a Ward
     *
     * @var array
     */
    protected array $items = [];

    public function string($item_name): StringWard
    {

    }

    public function int($item_name): WardItem
    {

    }
}