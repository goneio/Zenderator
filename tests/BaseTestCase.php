<?php
namespace Zenderator\Test;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Faker\Provider;
use Zenderator\Db;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @see https://github.com/fzaninotto/Faker
     * @var Generator $faker
     */
    private static $faker;

    private $app;

    private $container;

    static private $startTime;

    private $singleTestTime;

    private $waypoint_count;
    private $waypoint_last_time;

    // Set this to true if you want to see whats going on inside some unit tests..
    const DEBUG_MODE = false;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        /**
         * @var \Slim\App $app
         */
        if (!defined("APP_CORE_NAME")) {
            throw new \Exception("You must define AppCoreName in bootstrap.php. This must be the same as the core app container in /src");
        }
        $coreAppName                        = APP_CORE_NAME;
        $app                                = $coreAppName::Instance();
        $this->container                    = $app->getContainer();
        $this->container['TestAppInstance'] = function (\Slim\Container $c) use ($app) {
            return $app;
        };
        $this->app = $app;
    }

    public function setUp()
    {
        parent::setUp();
        $this->singleTestTime     = microtime(true);
        $this->waypoint_count     = 0;
        $this->waypoint_last_time = $this->singleTestTime;
    }

    public function tearDown()
    {
        parent::tearDown();
        if (self::DEBUG_MODE) {
            $time = microtime(true) - $this->singleTestTime;
            echo "" . get_called_class() . ":" . $this->getName() . ": Took " . number_format($time, 3) . " seconds\n\n";
        }
    }

    public static function setUpBeforeClass()
    {
        self::$startTime = microtime(true);
        Db::getInstance()->driver->getConnection()->beginTransaction();
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        Db::getInstance()->driver->getConnection()->rollback();
        parent::tearDownAfterClass();
        if (self::DEBUG_MODE) {
            $time = microtime(true) - self::$startTime;
            echo "\n" . get_called_class() . ": Took " . number_format($time, 3) . " seconds\n";
        }
    }

    public function waypoint($message = "")
    {
        if (self::DEBUG_MODE) {
            $time_since_last_waypoint = number_format((microtime(true) - $this->waypoint_last_time) * 1000, 2, '.', '');
            $time_since_begin         = number_format((microtime(true) - $this->singleTestTime) * 1000, 2, '.', '');
            $this->waypoint_count++;
            if ($this->waypoint_count == 1) {
                echo "\n";
            }
            echo " > Waypoint {$this->waypoint_count} - {$time_since_last_waypoint}ms / {$time_since_begin}ms {$message}\n";
            $this->waypoint_last_time = microtime(true);
        }
    }

    /**
     * @return \Slim\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return \Slim\Container
     */
    public function getDIContainer()
    {
        return $this->container;
    }

    /**
     * @return Generator
     */
    public function getFaker()
    {
        if (!self::$faker) {
            self::$faker = FakerFactory::create();
            self::$faker->addProvider(new Provider\Base(self::$faker));
            self::$faker->addProvider(new Provider\DateTime(self::$faker));
            self::$faker->addProvider(new Provider\Lorem(self::$faker));
            self::$faker->addProvider(new Provider\Internet(self::$faker));
            self::$faker->addProvider(new Provider\Payment(self::$faker));
            self::$faker->addProvider(new Provider\en_US\Person(self::$faker));
            self::$faker->addProvider(new Provider\en_US\Address(self::$faker));
            self::$faker->addProvider(new Provider\en_US\PhoneNumber(self::$faker));
            self::$faker->addProvider(new Provider\en_US\Company(self::$faker));
        }
        return self::$faker;
    }
}
