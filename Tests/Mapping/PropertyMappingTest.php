<?php

namespace Vich\UploaderBundle\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Tests\DummyEntity;

/**
 * PropertyMappingTest.
 *
 * @author Dustin Dobervich <ddobervich@gmail.com>
 */
class PropertyMappingTest extends TestCase
{
    /**
     * Test that the configured mappings are accessed
     * correctly.
     */
    public function testConfiguredMappingAccess()
    {
        $object = new DummyEntity();
        $prop = new PropertyMapping('file', 'fileName');
        $prop->setMapping([
            'upload_destination' => '/tmp',
        ]);

        $this->assertEquals('', $prop->getUploadDir($object));
        $this->assertEquals('/tmp', $prop->getUploadDestination());
        $this->assertEquals('file', $prop->getFilePropertyName());
        $this->assertEquals('fileName', $prop->getFileNamePropertyName());
    }

    /**
     * @dataProvider propertiesAccessProvider
     */
    public function testPropertiesAreAccessed($object, $file, $fileName)
    {
        $prop = new PropertyMapping('file', 'fileName');

        $this->assertSame($file, $prop->getFile($object));
        $this->assertSame($fileName, $prop->getFileName($object));
    }

    public function propertiesAccessProvider()
    {
        $date = new \DateTime();
        $object = new DummyEntity();
        $object->setFileName('joe.png');
        $object->setFile($date);

        $array = [
            'fileName' => 'joe.png',
            'file' => $date,
        ];

        return [
            [$object, $date, 'joe.png'],
            [$array,  $date, 'joe.png'],
        ];
    }

    public function testPropertiesAreSet()
    {
        $date = new \DateTime();
        $object = new DummyEntity();

        $prop = new PropertyMapping('file', 'fileName');
        $prop->setFile($object, $date);
        $prop->setFileName($object, 'joe.png');

        $this->assertSame($date, $object->getFile());
        $this->assertSame('joe.png', $object->getFileName());
    }

    /**
     * @dataProvider directoryProvider
     */
    public function testDirectoryNamerIsCalled($dir, $expectedDir)
    {
        $object = new DummyEntity();
        $prop = new PropertyMapping('file', 'fileName');
        $prop->setMapping([
            'upload_destination' => '/tmp',
        ]);

        $namer = $this->createMock('Vich\UploaderBundle\Naming\DirectoryNamerInterface');
        $namer
            ->expects($this->once())
            ->method('directoryName')
            ->with($object, $prop)
            ->will($this->returnValue($dir));

        $prop->setDirectoryNamer($namer);

        $this->assertEquals($expectedDir, $prop->getUploadDir($object));
        $this->assertEquals('/tmp', $prop->getUploadDestination());
    }

    public function directoryProvider()
    {
        return [
            ['other_dir', 'other_dir'],
            ['other_dir/', 'other_dir'],
            ['other_dir\\', 'other_dir'],
            ['other_dir\\sub_dir', 'other_dir\\sub_dir'],
            ['other_dir\\sub_dir\\', 'other_dir\\sub_dir'],
        ];
    }
}
