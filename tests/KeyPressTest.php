<?php
    require_once('../src/KeyPress.php');

    class KeyPressTest extends PHPUnit_Framework_TestCase
    {
        public function testGenerateNeighbors()
        {
            $neighbors = KeyPress::generateNeighbors(1, 2, 3, 4);
            $expected = array(KeyPress::UP => 1, KeyPress::RIGHT => 2, KeyPress::DOWN => 3, KeyPress::LEFT => 4);
            $this->assertEquals($expected, $neighbors);
        }

        /**
         * @depends testGenerateNeighbors
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage Neighbor should only be one character, empty string or null.
         */
        public function testValidateInputWithIntegerValues()
        {
            $neighbors = KeyPress::generateNeighbors(1, 2, 3, 4);
            KeyPress::validateInput($neighbors);
        }

        /**
         * @depends testGenerateNeighbors
         */
        public function testValidateInputWithEmptyString()
        {
            $neighbors = KeyPress::generateNeighbors('1', '', '3', '4');
            KeyPress::validateInput($neighbors);
        }

        /**
         * @depends testGenerateNeighbors
         */
        public function testValidateInputWithNull()
        {
            $neighbors = KeyPress::generateNeighbors('1', null, '3', '4');
            KeyPress::validateInput($neighbors);
        }

        /**
         * @depends testGenerateNeighbors
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage Neighbor should only be one character, empty string or null.
         */
        public function testValidateInputWithMultipleCharacter()
        {
            $neighbors = KeyPress::generateNeighbors('a', 'bb', 'c', 'd');
            KeyPress::validateInput($neighbors);
        }

        /**
         * @expectedException InvalidArgumentException
         * @expectedExceptionMessage 1 is not a valid KeyPress direction.
         */
        public function testValidateInputWithInvalidKey()
        {
            $neighbors = array('1', '2', '3', '4');
            KeyPress::validateInput($neighbors);
        }
    }