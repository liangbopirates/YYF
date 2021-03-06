<?php
namespace tests\library;

use \Config as Config;
use \Yaf_Application as Application;

/**
 * @coversDefaultClass \Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
    *测试配置和配置文件是否一致
    * @covers ::get
    */
    public function testConfigConsistency()
    {
        $env=Application::app()->environ();
        $config=parse_ini_file(APP_PATH.'/conf/app.ini', true);
        $current=$config[$env.':common']+$config['common'];

        foreach ($current as $key => $value) {
            $this->assertSame($current[$key], Config::get($key), $key);
        }
    }

    /*检测空值*/
    public function testEmpty()
    {
        $this->assertSame(Config::get(uniqid('_te_', true)), null);
    }

    /*测试默认值*/
    public function testDefault()
    {
        $key=uniqid('_td_', true);
        $default=array(false,null,1,true,array(1,2,4),'test');
        foreach ($default as $k=>$d) {
            $this->assertSame(Config::get($k.$key, $d), $d);
        }
    }

    /*测试secret路径是否存在*/
    public function testSecretPath()
    {
        $secret_ini=Config::get('secret_config_path');
        $this->assertFileExists($secret_ini, $secret_ini.' Config cannot find');
        return $secret_ini;
    }

    /**
     * @depends testSecretPath
     */
    public function testSecret($path)
    {
        $secret=parse_ini_file($path, true);
        foreach ($secret as $name => &$key) {
            foreach ($key as $k => $v) {
                $this->assertSame(Config::getSecret($name, $k), $v, "$name.$k");
            }
        }
    }

    public function testSecretArray()
    {
        $default_db=Config::getSecret('database', 'db._');
        $this->assertNotEmpty($default_db);
        $this->assertArrayHasKey('dsn', $default_db);
    }

    /*检测sceret空值*/
    public function testSecretEmpty()
    {
        $key=uniqid('_tse_', true);
        $this->assertSame(Config::getSecret('database', $key), null);
    }
}
