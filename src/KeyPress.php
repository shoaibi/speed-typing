<?php
    /**
     * Class KeyPress
     * Used to denote key presses with uniquely identified constants.
     */
    abstract class KeyPress
    {
        const UP    = 'Up';
        const DOWN  = 'Down';
        const LEFT  = 'Left';
        const RIGHT = 'Right';
        const ENTER = 'Enter';

        public static function generateNeighbors($up, $right, $down, $left)
        {
            return array(
                static::UP      => $up,
                static::RIGHT   => $right,
                static::DOWN    => $down,
                static::LEFT    => $left,
            );
        }

        public static function validateInput($neighbors)
        {
            foreach ($neighbors as $key => $neighbor)
            {
                if (!in_array($key, array(static::UP, static::RIGHT, static::DOWN, static::LEFT)))
                {
                    throw new InvalidArgumentException("$key is not a valid KeyPress direction.");
                }
                if (!is_null($neighbor) && (!is_string($neighbor) || (strlen($neighbor) != 0 && strlen($neighbor) != 1)))
                {
                    throw new InvalidArgumentException("Neighbor should only be one character, empty string or null.");
                }
            }
        }
    }