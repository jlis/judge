<?php

namespace jlis\Tests\Judge\Voters;

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
        return array(
            array(true, '1+1==2'),
            array(false, '1+1==3'),
            array(true, 'user.getName()=="Mike"', $this->getUser()),
            array(false, 'user.getName()=="Mike"', $this->getUser(), array('user' => $this->getFoo('getName', 'Adam'))),
            array(true, 'foo.getBar()=="bar"', null, array('foo' => $this->getFoo('getBar', 'bar'))),
        );
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
