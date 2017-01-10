<?php

namespace jlis\Tests\Judge\Voters;

use jlis\Tests\Judge\StdObject;
use jlis\Judge\Voters\ExpressionLanguageVoter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ExpressionLanguageVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool  $expected
     * @param mixed $parameter
     * @param mixed $user
     * @param array $additional
     */
    public function testVote($expected, $parameter, $user = null, array $additional = [])
    {
        $voter = new ExpressionLanguageVoter();
        static::assertEquals($expected, $voter->vote($parameter, $user, $additional));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [false, ''],
            [true, '1+1==2'],
            [false, '1+1==3'],
            [true, 'user.getName()=="Mike"', $this->getUser()],
            [false, 'user.getName()=="Mike"', $this->getUser(), ['user' => $this->getFoo('getName', 'Adam')]],
            [true, 'foo.getBar()=="bar"', null, ['foo' => $this->getFoo('getBar', 'bar')]],
        ];
    }

    /**
     * @return StdObject
     */
    protected function getFoo($method, $return)
    {
        $foo = new StdObject();
        $foo->$method = function () use ($return) {
            return $return;
        };

        return $foo;
    }

    /**
     * @return StdObject
     */
    protected function getUser()
    {
        return $this->getFoo('getName', 'Mike');
    }
}
