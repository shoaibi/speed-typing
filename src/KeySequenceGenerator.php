<?php
    require_once('Graph.php');
    require_once('NodeNotFoundException.php');
    require_once('KeyPress.php');

    /**
     * Class KeySequenceGenerator
     * Class used to find shortest paths between characters of a sentence.
     */
    class KeySequenceGenerator
    {
        /**
         * Array containing subArrays of paths of characters inside a sentence
         * @var array
         */
        protected $fullPath;
        /**
         * @var string
         */
        protected $sentence;
        /**
         * Contains nodes from Graph
         * @var array
         */
        protected $nodes;

        public function __construct($sentence, $enableUnProgrammedKeyForTraversal = false)
        {
            if (!is_string($sentence))
            {
                throw new InvalidArgumentException("Sentence should be string");
            }
            if (empty($sentence))
            {
                throw new InvalidArgumentException("Sentence should not be empty");
            }
            $this->sentence = $sentence;
            $graph          = Graph::getInstance($enableUnProgrammedKeyForTraversal);
            $this->nodes    = $graph->getNodes();
            $this->fullPath = array();
        }

        public function process()
        {
            $this->fullPath[]  = $this->doBreadthFirstSearch($this->sentence[0], $this->sentence[0]);
            for ($i = 0; $i < strlen($this->sentence) -1 ; $i++)
            {
                $source             = $this->sentence[$i];
                $target             = $this->sentence[$i + 1];
                $path               = $this->doBreadthFirstSearch($source, $target);
                $this->fullPath[]   = $path;
            }
        }

        public function printSequences($return = false, $withNodeNames = false)
        {
            $output = '';
            foreach ($this->fullPath as $pathComponents)
            {
                foreach($pathComponents as $pathComponent)
                {
                    $output .= $this->printPathComponent($pathComponent[0], !$withNodeNames, $return);
                    $output .= $this->printPathComponent($pathComponent[1], false, $return);
                }
            }
            if ($return)
            {
                return $output;
            }
        }

        protected function printPathComponent($component, $skip, $return)
        {
            if (!$skip)
            {
                $output = $component . PHP_EOL;
                if ($return)
                {
                    return $output;
                }
                echo $output;
            }
        }

        protected function doBreadthFirstSearch($sourceCharacter, $targetCharacter)
        {
            if ($sourceCharacter == $targetCharacter)
            {
                return array(array($sourceCharacter, KeyPress::ENTER));
            }
            if (!isset($this->nodes[ord($sourceCharacter)]))
            {
                $this->throwNodeNotFoundException($sourceCharacter);
            }
            if (!isset($this->nodes[ord($targetCharacter)]))
            {
                $this->throwNodeNotFoundException($targetCharacter);
            }

            $found                          = false;
            $queue                          = array();
            $visited                        = array();
            $visited[$sourceCharacter]      = true;
            array_unshift($queue, $sourceCharacter);
            while (!empty($queue))
            {
                $character  = array_shift($queue);
                if ($character === $targetCharacter)
                {
                    $found = true;
                    break;
                }
                $neighbors  = $this->nodes[ord($character)];
                foreach ($neighbors as $direction => $neighborCharacter)
                {
                    if (isset($neighborCharacter) && !isset($visited[$neighborCharacter]))
                    {
                        array_push($queue, $neighborCharacter);
                        $visited[$neighborCharacter] = array($direction => $character);
                    }
                }
            }

            if ($found)
            {
                $character          = $targetCharacter;
                $path               = array(array($character, KeyPress::ENTER));
                while ($targetPredecessor = $visited[$character])
                {
                    $character  = reset($targetPredecessor);
                    $key        = key($targetPredecessor);
                    array_unshift($path, array($character, $key));
                    if ($character === $sourceCharacter)
                    {
                        break;
                    }
                }
                return $path;
            }
        }

        protected function throwNodeNotFoundException($character)
        {
            throw new NodeNotFoundException("$character key not found.");
        }
    }