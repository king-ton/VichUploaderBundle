<?php

namespace Vich\UploaderBundle\Tests\Adapter\PHPCR;

use PHPUnit\Framework\TestCase;
use Vich\UploaderBundle\Adapter\PHPCR\PHPCRAdapter;
use Vich\UploaderBundle\Tests\DummyEntity;

/**
 * PHPCRAdapterTest.
 *
 * @author Ben Glassman <bglassman@gmail.com>
 */
class PHPCRAdapterTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        if (!class_exists('Doctrine\Common\Persistence\Event\LifecycleEventArgs')) {
            self::markTestSkipped('Doctrine\Common\Persistence\Event\LifecycleEventArgs does not exist.');
        }
    }

    /**
     * Test the getObjectFromArgs method.
     */
    public function testGetObjectFromArgs()
    {
        $entity = new DummyEntity();

        $args = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($entity));

        $adapter = new PHPCRAdapter();

        $this->assertEquals($entity, $adapter->getObjectFromArgs($args));
    }
}
