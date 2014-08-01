<?php


use Abc\Filesystem\DefinitionFactory;
use Abc\Filesystem\FilesystemType;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class DefinitionFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getData
     */
    public function testCreate($type = null, $path = null, $options = null)
    {
        $subject = new DefinitionFactory;

        if($options != null)
        {
            $definition = $subject->create($type, $path, $options);
        }
        else
        {
            $definition = $subject->create($type, $path);
        }

        $this->assertEquals($type, $definition->getType());
        $this->assertEquals($path, $definition->getPath());
        if($options == null)
        {
            $this->assertEquals(array(), $definition->getProperties());
        }
        else {
            $this->assertEquals($options, $definition->getProperties());
        }

    }

    public static function getData()
    {
        return array(
            array(FilesystemType::LOCAL, null),
            array(FilesystemType::LOCAL, '/'),
            array(FilesystemType::LOCAL, '/', array('foo' => 'bar')),
        );
    }
}
 