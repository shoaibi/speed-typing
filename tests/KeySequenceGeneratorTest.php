<?php
    require_once('../src/KeySequenceGenerator.php');

    class KeySequenceGeneratorTest extends PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage Sentence should be string
         */
        public function testConstructWithInvalidSentence()
        {
            new KeySequenceGenerator(array());
        }

        /**
         * @depends testConstructWithInvalidSentence
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage Sentence should not be empty
         */
        public function testConstructWithEmptySentence()
        {
            new KeySequenceGenerator('');
        }

        /**
         * @depends testConstructWithEmptySentence
         */
        public function testConstruct()
        {
            new KeySequenceGeneratorTest('some sentence');
        }

        /**
         * @depends testConstruct
         */
        public function testProcessWithSimpleString()
        {
            $sentence           = 'ABC';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true);
            $expectedSequence   = "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL;
            $this->assertEquals($expectedSequence, $sequence);
        }

        /**
         * @depends testProcessWithSimpleString
         */
        public function testProcessWithStringWithDuplicates()
        {
            $sentence           = 'AABBCC';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true);
            $expectedSequence   = "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL;
            $this->assertEquals($expectedSequence, $sequence);

        }

        /**
         * @depends testProcessWithSimpleString
         * @expectedException NodeNotFoundException
         */
        public function testProcessWithInexistingNodeAtEnd()
        {
            $sentence           = 'A€';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
        }

        /**
         * @depends testProcessWithSimpleString
         * @expectedException NodeNotFoundException
         */
        public function testProcessWithInexistingNodeAtStart()
        {
            $sentence           = '€A';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
        }

        /**
         * @depends testProcessWithSimpleString
         */
        public function testProcessWithComplicatedString()
        {
            $sentence           = 'AA qu!c7 br0wn (fox) {jumps} ov:e> a la,zy_[dog].';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true, true);
            $this->assertEquals(2659, strlen($sequence));
            $this->assertEquals(44, substr_count($sequence, KeyPress::UP));
            $this->assertEquals(23, substr_count($sequence, KeyPress::DOWN));
            $this->assertEquals(130, substr_count($sequence, KeyPress::LEFT));
            $this->assertEquals(122, substr_count($sequence, KeyPress::RIGHT));
        }

        /**
         * @depends testProcessWithComplicatedString
         */
        public function testProcessWithComplicatedStringWithSentenceGenerationFromSequence()
        {
            $sentence           = 'AA qu!c7 br0wn (fox) {jumps} ov:e> a la,zy_[dog].';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true, true);
            $this->assertEquals(2659, strlen($sequence));
            $sequenceTokens     = explode(PHP_EOL, $sequence);
            $generatedSentence  = '';
            foreach ($sequenceTokens as $index => $sequenceToken)
            {
                if ($sequenceToken === KeyPress::ENTER)
                {
                    $generatedSentence .= $sequenceTokens[--$index];
                }
            }
            $this->assertEquals($sentence, $generatedSentence);
        }

        /**
         * @depends testProcessWithComplicatedStringWithSentenceGenerationFromSequence
         */
        public function testProcessWithComplicatedStringWithTraversal()
        {
            $sentence           = 'AA qu!c7 br0wn (fox) {jumps} ov:e> a la,zy_[dog].';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true, true);
            $this->assertEquals(2659, strlen($sequence));
            $sequenceTokens     = explode(PHP_EOL, $sequence);
            $graph              = Graph::getInstance();
            $nodes              = $graph->getNodes();
            $node               = $nodes[ord($sequenceTokens[0])];
            for ($i = 1; $i < count($sequenceTokens); $i++)
            {
                if ($sequenceTokens[$i] === KeyPress::ENTER)
                {
                    continue;
                }
                if (in_array($sequenceTokens[$i], array(KeyPress::UP, KeyPress::RIGHT, KeyPress::DOWN, KeyPress::LEFT)))
                {
                    $next = $i + 1;
                    $this->assertEquals($node[$sequenceTokens[$i]], $sequenceTokens[$next]);
                    $node = $nodes[ord($sequenceTokens[$next])];
                }
            }
        }

        /**
         * @//depends testProcessWithSimpleString
         */
        public function testProcessWithUnprogrammedKeyEnabled()
        {
            $sentence = '<> .,';
            $generator          = new KeySequenceGenerator($sentence, true);
            $generator->process();
            $sequenceWithNodes  = $generator->printSequences(true, true);
            $expected           = '<' . PHP_EOL . 'Enter' . PHP_EOL . '<' . PHP_EOL . 'Right' . PHP_EOL . '>' .
                                    PHP_EOL . 'Enter' . PHP_EOL . '>' . PHP_EOL . 'Right' . PHP_EOL . '' . PHP_EOL .
                                    'Right' . PHP_EOL . ' ' . PHP_EOL . 'Enter' . PHP_EOL . ' ' . PHP_EOL . 'Right' .
                                    PHP_EOL . '.' . PHP_EOL . 'Enter' . PHP_EOL . '.' . PHP_EOL . 'Right' . PHP_EOL .
                                    ',' . PHP_EOL . 'Enter' . PHP_EOL;
            $this->assertEquals($expected, $sequenceWithNodes);
        }

        /**
         * @depends testProcessWithStringWithDuplicates
         */
        public function testPrintSequence()
        {
            $sentence           = 'AABBCC';
            $generator          = new KeySequenceGenerator($sentence);
            $generator->process();
            $sequence           = $generator->printSequences(true);
            $expectedSequence   = "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Right" . PHP_EOL .
                                    "Enter" . PHP_EOL .
                                    "Enter" . PHP_EOL;
            $this->assertEquals($expectedSequence, $sequence);
            $sequence           = $generator->printSequences(true, true);
            $expectedSequence   = "A" . PHP_EOL . "Enter" . PHP_EOL .
                                    "A" . PHP_EOL ."Enter" . PHP_EOL .
                                    "A" . PHP_EOL ."Right" . PHP_EOL .
                                    "B" . PHP_EOL ."Enter" . PHP_EOL .
                                    "B" . PHP_EOL ."Enter" . PHP_EOL .
                                    "B" . PHP_EOL ."Right" . PHP_EOL .
                                    "C" . PHP_EOL ."Enter" . PHP_EOL .
                                    "C" . PHP_EOL ."Enter" . PHP_EOL;
            $this->assertEquals($expectedSequence, $sequence);
        }
    }