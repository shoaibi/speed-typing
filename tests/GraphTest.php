<?php
    require_once('../src/Graph.php');

    class GraphTest extends PHPUnit_Framework_TestCase
    {
        public function testSingleton()
        {
            $graph1 = Graph::getInstance();
            $graph2 = Graph::getInstance();
            $this->assertEquals($graph1, $graph2);
        }

        public function testNodes()
        {
            $graph  = Graph::getInstance();
            $nodes  = $graph->getNodes();
            $this->assertCount(96, $nodes);
            $expectedSmallR = array (
                            'Up' => 'R',
                            'Right' => 's',
                            'Down' => '*',
                            'Left' => 'q',
                        );
            $this->assertEquals($expectedSmallR, $nodes[ord('r')]);
            $expectedSpace = array (
                            'Up' => '#',
                            'Right' => '.',
                            'Down' => 'J',
                            'Left' => null,
                        );
            $this->assertEquals($expectedSpace, $nodes[ord(' ')]);
            $expectedBackSpace = array (
                                    'Up' => '-',
                                    'Right' => '`',
                                    'Down' => 'Z',
                                    'Left' => '=',
                                );
            $this->assertEquals($expectedBackSpace, $nodes[ord("\x8")]);
        }

        public function testUnprogrammedKeyMissingByDefault()
        {
            $graph = Graph::getInstance();
            $nodes = $graph->getNodes();
            $this->assertNull($nodes[ord('')]);
        }

        public function testUnprogrammedKeyMissingEnabledUsingConstructor()
        {
            $graph = Graph::getInstance(true);
            $nodes = $graph->getNodes();
            $this->assertNotNull($nodes[ord('')]);
            $expected = array (
                            'Up' => '8',
                            'Right' => ' ',
                            'Down' => 'I',
                            'Left' => '>',
                        );
            $this->assertEquals($expected, $nodes[ord('')]);
        }
    }