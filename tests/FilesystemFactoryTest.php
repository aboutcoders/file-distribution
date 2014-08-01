<?php

use Abc\Filesystem\AdapterFactoryInterface;
use Abc\Filesystem\Definition;
use Abc\Filesystem\FilesystemFactory;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class FilesystemFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var AdapterFactoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $adapterFactory;
    /** @var FilesystemFactory */
    private $subject;

    public function setUp()
    {
        $this->adapterFactory = $this->getMock('Abc\Filesystem\AdapterFactoryInterface');
        $this->subject = new FilesystemFactory($this->adapterFactory);
    }

    public function testCreate()
    {
        $this->adapterFactory->expects($this->once())
            ->method('create')
            ->willReturn(new \Gaufrette\Adapter\Local(sys_get_temp_dir()));

        $definition = new Definition;
        $filesystem = $this->subject->create($definition);

        $this->assertInstanceOf('Abc\Filesystem\Filesystem', $filesystem);
    }
}
 