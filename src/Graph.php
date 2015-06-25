<?php
    require_once('KeyPress.php');

    /**
     * Class Graph
     * Represents Graph with an array of Node objects.
     */
    class Graph
    {
        /**
         * Used to hold singleton object
         * @var Graph
         */
        protected static $instance;
        /**
         * Array of Node objects
         * @var array
         */
        protected $nodes;

        /**
         * Enable/Disable the empty string key below 8
         * @var bool
         */
        protected $enableUnProgrammedKeyForTraversal = false;

        /**
         * returns singleton instance
         * @return Graph
         */
        public static function getInstance($enableUnProgrammedKeyForTraversal = false)
        {
            // singleton because keyboard layout remains same for all sentence
            if (!isset(static::$instance) || (static::$instance->enableUnProgrammedKeyForTraversal !== $enableUnProgrammedKeyForTraversal))
            {
                static::destroy();
                $className          = get_called_class();
                static::$instance  = new $className($enableUnProgrammedKeyForTraversal);
            }
            return static::$instance;
        }

        public function getNodes()
        {
            // readonly property
            return $this->nodes;
        }

        protected static function destroy()
        {
            static::$instance = null;
        }

        protected function __construct($enableUnProgrammedKeyForTraversal = false)
        {
            $this->enableUnProgrammedKeyForTraversal    = $enableUnProgrammedKeyForTraversal;
            $this->populateGraphWithNodes();
        }

        protected static function validateNodeValue($value)
        {
            if (!is_string($value) || strlen($value) != 1)
            {
                throw new InvalidArgumentException("Node value should be one character long string");
            }
        }

        /**
         * Populate nodes array with Node objects for each character on keyboard
         */
        protected function populateGraphWithNodes()
        {
            $this->generateFirstRow();
            $this->generateSecondRow();
            $this->generateThirdRow();
            $this->generateFourthRow();
        }

        /**
         * Adds a node object to graph given value and neighbors
         * @param string $value
         * @param array $neighbors
         */
        protected function populateRowItem($value, array $neighbors)
        {
            KeyPress::validateInput($neighbors);
            // add node to graph, patten: key: ascii value of character, value: node object.
            $this->nodes[ord($value)] = $neighbors;
        }

        /**
         * Populates nodes array with an object
         * @param string $value string
         * @param null|array $specialNeighbor
         * @param bool $firstRow defaults to true
         */
        protected function populateFirstOrSecondRowItem($value, $specialNeighbor = null, $firstRow = true)
        {
            $neighbors = $this->getNeighborsForFirstOrSecondRow($value, $specialNeighbor, $firstRow);
            $this->populateRowItem($value, $neighbors);
        }

        /**
         * Returns provided default value is current value is not inside a given alphabetic range
         * @param string $value
         * @param string $default
         * @param string $start
         * @param null|string $end
         * @return string
         */
        protected function returnDefaultIfNotInRange($value, $default, $start = 'A', $end = null)
        {
            if (!in_array($start, array('a', 'A')))
            {
                throw new InvalidArgumentException("start should either be A or a.");
            }
            if (!isset($end))
            {
                // this gives Z or z depending on $start
                $end = chr(ord($start) + 25);
            }
            if (!in_array($value, range($start, $end)))
            {
                return $default;
            }
            return $value;
        }

        /**
         * Returns neighbors array for items of first or second row.
         * @param string $value
         * @param null|string $specialNeighbor The one odd neighbor that has to be supplied.
         * @param bool $firstRow defaults to true.
         * @return array
         */
        protected function getNeighborsForFirstOrSecondRow($value, $specialNeighbor = null, $firstRow = true)
        {
            // assume that we are going for first row
            $startingCharacter  = 'A';
            $up                 = $specialNeighbor;
            $down               = strtolower($value);
            if (!$firstRow)
            {
                $startingCharacter  = 'a';
                $up                 = strtoupper($value);
                $down               = $specialNeighbor;
            }
            $defaultRight       = $startingCharacter;
            // again, this would give Z or z depending on startingCharacter for the range.
            $defaultLeft        = chr(ord($defaultRight) + 25);
            // chr(ord($value) - 1) : get the character value of one less ascii code.
            $left   = $this->returnDefaultIfNotInRange(chr(ord($value) - 1), $defaultLeft, $startingCharacter);
            $right  = $this->returnDefaultIfNotInRange(chr(ord($value) + 1), $defaultRight, $startingCharacter);
            // build the neighbors array
            return KeyPress::generateNeighbors($up, $right, $down, $left);
        }

        protected function getUnprogrammedKeyAsNeighborIfEnabled()
        {
            if ($this->enableUnProgrammedKeyForTraversal)
            {
                return '';
            }
            return null;
        }

        protected function generateFirstRow()
        {
            $this->populateFirstOrSecondRowItem('A', '`');
            $this->populateFirstOrSecondRowItem('B', '~');
            $this->populateFirstOrSecondRowItem('C', '[');
            $this->populateFirstOrSecondRowItem('D', ']');
            $this->populateFirstOrSecondRowItem('E', '{');
            $this->populateFirstOrSecondRowItem('F', '}');
            $this->populateFirstOrSecondRowItem('G', '<');
            $this->populateFirstOrSecondRowItem('H', '>');
            $this->populateFirstOrSecondRowItem('I', $this->getUnprogrammedKeyAsNeighborIfEnabled());
            $this->populateFirstOrSecondRowItem('J', ' ');
            $this->populateFirstOrSecondRowItem('K', ' ');
            $this->populateFirstOrSecondRowItem('L', ' ');
            $this->populateFirstOrSecondRowItem('M', ' ');
            $this->populateFirstOrSecondRowItem('N', ' ');
            $this->populateFirstOrSecondRowItem('O', ' ');
            $this->populateFirstOrSecondRowItem('P', ' ');
            $this->populateFirstOrSecondRowItem('Q', '.');
            $this->populateFirstOrSecondRowItem('R', ',');
            $this->populateFirstOrSecondRowItem('S', ';');
            $this->populateFirstOrSecondRowItem('T', ':');
            $this->populateFirstOrSecondRowItem('U', '\'');
            $this->populateFirstOrSecondRowItem('V', '"');
            $this->populateFirstOrSecondRowItem('W', '_');
            $this->populateFirstOrSecondRowItem('X', '=');
            $this->populateFirstOrSecondRowItem('Y', "\x8");
            $this->populateFirstOrSecondRowItem('Z', "\x8");
        }

        protected function generateSecondRow()
        {
            $this->populateFirstOrSecondRowItem('a', '0', false);
            $this->populateFirstOrSecondRowItem('b', '1', false);
            $this->populateFirstOrSecondRowItem('c', '2', false);
            $this->populateFirstOrSecondRowItem('d', '3', false);
            $this->populateFirstOrSecondRowItem('e', '4', false);
            $this->populateFirstOrSecondRowItem('f', '5', false);
            $this->populateFirstOrSecondRowItem('g', '6', false);
            $this->populateFirstOrSecondRowItem('h', '7', false);
            $this->populateFirstOrSecondRowItem('i', '8', false);
            $this->populateFirstOrSecondRowItem('j', '9', false);
            $this->populateFirstOrSecondRowItem('k', '!', false);
            $this->populateFirstOrSecondRowItem('l', '@', false);
            $this->populateFirstOrSecondRowItem('m', '#', false);
            $this->populateFirstOrSecondRowItem('n', '$', false);
            $this->populateFirstOrSecondRowItem('o', '%', false);
            $this->populateFirstOrSecondRowItem('p', '^', false);
            $this->populateFirstOrSecondRowItem('q', '&', false);
            $this->populateFirstOrSecondRowItem('r', '*', false);
            $this->populateFirstOrSecondRowItem('s', '(', false);
            $this->populateFirstOrSecondRowItem('t', ')', false);
            $this->populateFirstOrSecondRowItem('u', '?', false);
            $this->populateFirstOrSecondRowItem('v', '/', false);
            $this->populateFirstOrSecondRowItem('w', '|', false);
            $this->populateFirstOrSecondRowItem('x', '\\', false);
            $this->populateFirstOrSecondRowItem('y', '+', false);
            $this->populateFirstOrSecondRowItem('z', '-', false);
        }

        protected function generateThirdRow()
        {
            $this->populateRowItem('0', KeyPress::generateNeighbors('a', '1', '`', '-'));
            $this->populateRowItem('1', KeyPress::generateNeighbors('b', '2', '~', '0'));
            $this->populateRowItem('2', KeyPress::generateNeighbors('c', '3', '[', '1'));
            $this->populateRowItem('3', KeyPress::generateNeighbors('d', '4', ']', '2'));
            $this->populateRowItem('4', KeyPress::generateNeighbors('e', '5', '{', '3'));
            $this->populateRowItem('5', KeyPress::generateNeighbors('f', '6', '}', '4'));
            $this->populateRowItem('6', KeyPress::generateNeighbors('g', '7', '<', '5'));
            $this->populateRowItem('7', KeyPress::generateNeighbors('h', '8', '>', '6'));
            $this->populateRowItem('8', KeyPress::generateNeighbors('i', '9', $this->getUnprogrammedKeyAsNeighborIfEnabled(), '7'));
            $this->populateRowItem('9', KeyPress::generateNeighbors('j', '!', ' ', '8'));
            $this->populateRowItem('!', KeyPress::generateNeighbors('k', '@', ' ', '9'));
            $this->populateRowItem('@', KeyPress::generateNeighbors('l', '#', ' ', '!'));
            $this->populateRowItem('#', KeyPress::generateNeighbors('m', '$', ' ', '@'));
            $this->populateRowItem('$', KeyPress::generateNeighbors('n', '%', ' ', '#'));
            $this->populateRowItem('%', KeyPress::generateNeighbors('o', '^', ' ', '$'));
            $this->populateRowItem('^', KeyPress::generateNeighbors('p', '&', ' ', '%'));
            $this->populateRowItem('&', KeyPress::generateNeighbors('q', '*', '.', '^'));
            $this->populateRowItem('*', KeyPress::generateNeighbors('r', '(', ',', '&'));
            $this->populateRowItem('(', KeyPress::generateNeighbors('s', ')', ';', '*'));
            $this->populateRowItem(')', KeyPress::generateNeighbors('t', '?', ':', '('));
            $this->populateRowItem('?', KeyPress::generateNeighbors('u', '/', '\'', ')'));
            $this->populateRowItem('/', KeyPress::generateNeighbors('v', '|', '"', '?'));
            $this->populateRowItem('|', KeyPress::generateNeighbors('w', '\\', '_', '/'));
            $this->populateRowItem('\\', KeyPress::generateNeighbors('x', '+', '=', '|'));
            $this->populateRowItem('+', KeyPress::generateNeighbors('y', '-', "\x8", '\\'));
            $this->populateRowItem('-', KeyPress::generateNeighbors('z', '0', "\x8", '+'));
        }

        protected function generateFourthRow()
        {
            $this->populateRowItem('`', KeyPress::generateNeighbors('0', '~', 'A', "\x8"));
            $this->populateRowItem('~', KeyPress::generateNeighbors('1', '[', 'B', '`'));
            $this->populateRowItem('[', KeyPress::generateNeighbors('2', ']', 'C', '~'));
            $this->populateRowItem(']', KeyPress::generateNeighbors('3', '{', 'D', '['));
            $this->populateRowItem('{', KeyPress::generateNeighbors('4', '}', 'E', ']'));
            $this->populateRowItem('}', KeyPress::generateNeighbors('5', '<', 'G', '{'));
            $this->populateRowItem('<', KeyPress::generateNeighbors('6', '>', 'G', '}'));
            $this->populateRowItem('>', KeyPress::generateNeighbors('7', $this->getUnprogrammedKeyAsNeighborIfEnabled(), 'H', '<'));
            if ($this->enableUnProgrammedKeyForTraversal)
            {
                $this->populateRowItem('', KeyPress::generateNeighbors('8', ' ', 'I', '>'));
            }
            $this->populateRowItem(' ', KeyPress::generateNeighbors('#', '.', 'J', $this->getUnprogrammedKeyAsNeighborIfEnabled()));
            $this->populateRowItem('.', KeyPress::generateNeighbors('&', ',', 'Q', ' '));
            $this->populateRowItem(',', KeyPress::generateNeighbors('*', ';', 'R', '.'));
            $this->populateRowItem(';', KeyPress::generateNeighbors('(', ':', 'S', ','));
            $this->populateRowItem(':', KeyPress::generateNeighbors(')', '\'', 'T', ';'));
            $this->populateRowItem('\'', KeyPress::generateNeighbors('?', '"', 'U', ':'));
            $this->populateRowItem('"', KeyPress::generateNeighbors('/', '_', 'V', '\''));
            $this->populateRowItem('_', KeyPress::generateNeighbors('|', '=', 'W', '"'));
            $this->populateRowItem('=', KeyPress::generateNeighbors('\\', "\x8", 'X', '_'));
            $this->populateRowItem("\x8", KeyPress::generateNeighbors('-', '`', 'Z', '='));
        }
    }