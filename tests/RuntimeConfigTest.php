<?php

namespace elementary\config\Runtime\Test;

use elementary\config\Runtime\RuntimeConfig;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \elementary\config\Runtime\RuntimeConfig
 */
class RuntimeConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RuntimeConfig
     */
    protected $config = null;

    /**
     * @test
     * @covers ::me()
     */
    public function me()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $me1 = RuntimeConfig::me();
        $me2 = RuntimeConfig::me();

        $this->assertInstanceOf('\elementary\config\Runtime\RuntimeConfig', $me1);
        $this->assertInstanceOf('\elementary\config\Runtime\RuntimeConfig', $me2);

        $me1->set('test/test3/test4', 1234);
        $this->assertEquals($me1->get('test/test3/test4'), $me2->get('test/test3/test4'));
    }

    /**
     * @test
     * @covers ::set()
     * @covers ::get()
     */
    public function set()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->assertEquals(1234, $this->getConfig()->set('test2', 1234)->get('test2'));
        $this->assertEquals(1234, $this->getConfig()->set('test/test3/test4', 1234)->get('test/test3/test4'));
        $this->assertEquals(1234, $this->getConfig()->set('test/test3/test4/test5', 1234)->get('test/test3/test4/test5'));
    }

    /**
     * @test
     * @covers ::get()
     */
    public function get()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->assertEquals(1234, $this->getConfig()->get('test/test3/test4/test5', 1234));
    }

    /**
     * @test
     * @dataProvider getDataProvider
     * @covers ::has()
     * @covers ::getPath()
     *
     * @param array  $data
     * @param string $separate
     * @param string $hasKey
     * @param string $notKey
     */
    public function has($data, $separate, $hasKey, $notKey)
    {
        fwrite(STDOUT, "\n". __METHOD__ .' with ('. $separate .')');

        $this->getConfig()->setAll($data)->setSeparate($separate);
        $this->assertTrue($this->getConfig()->has($hasKey));
        $this->assertFalse($this->getConfig()->has($notKey));
    }

    /**
     * @test
     * @covers ::delete()
     */
    public function delete()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->getConfig()->set('test/test3/test4/test5', 1234);
        $this->assertTrue($this->getConfig()->delete('test/test3'));
        $this->assertFalse($this->getConfig()->has('test/test3'));
    }

    /**
     * @test
     * @dataProvider getDataProvider
     * @covers ::setAll()
     * @covers ::all()
     * @covers ::getCache()
     * @covers ::setCache()
     * @covers ::setToCache()
     * @covers ::getFromCache()
     *
     * @param array  $data
     */
    public function cache($data)
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->assertEquals($data, $this->getConfig()->setAll($data)->all());
    }

    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            [['test' => ['test2' => ['test4' => 123]]], '/', 'test/test2', 'test/test4'],
            [['test' => ['test2' => 123, 'test3' => ['test4' => 123]]], '.', 'test.test3.test4', 'test.test2.test4'],
        ];
    }

    /**
     * @test
     * @covers ::setSeparate()
     * @covers ::getSeparate()
     */
    public function separate()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->assertEquals('/', $this->getConfig()->getSeparate());
        $this->assertEquals('.', $this->getConfig()->setSeparate('.')->getSeparate());
    }

    protected function setUp()
    {
        $this->setConfig(new RuntimeConfig());
    }

    /**
     * @return RuntimeConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param RuntimeConfig $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}
