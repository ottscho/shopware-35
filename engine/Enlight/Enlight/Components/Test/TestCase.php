<?php
/**
 * Test case class
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Test
 */
abstract class Enlight_Components_Test_TestCase extends PHPUnit_Framework_TestCase
{
	/**
     * @var PHPUnit_Extensions_Database_ITester
     */
    protected $databaseTester;
	
	/**
     * Returns a mock object for the specified class.
     *
     * @param  string  $originalClassName
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.0.0
     */
	public function getMock($originalClassName, $methods = array(), array $arguments = array(), 
	  $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true)
	{
		$originalClassName = Enlight_Class::getClassName($originalClassName);
		return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
	}
	    
    /**
     * Gets the IDatabaseTester for this testCase. If the IDatabaseTester is
     * not set yet, this method calls newDatabaseTester() to obtain a new
     * instance.
     *
     * @return PHPUnit_Extensions_Database_ITester
     */
    protected function getDatabaseTester()
    {
        if ($this->databaseTester===null) {
            $this->databaseTester = $this->newDatabaseTester();
        }

        return $this->databaseTester;
    }
    
    /**
     * Creates a IDatabaseTester for this testCase.
     *
     * @return PHPUnit_Extensions_Database_ITester
     */
    protected function newDatabaseTester()
    {
        return new Enlight_Components_Test_Database_DefaultTester();
    }
     
    /**
     * Sets up the fixture, for example, open a network connection.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->databaseTester = null;
		if(method_exists($this, 'getSetUpOperation')) {
			 $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
		}
        if(method_exists($this, 'getDataSet')) {
	        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        }
        if($this->databaseTester!==null) {
        	$this->getDatabaseTester()->onSetUp();
        }
    }
    
    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function tearDown()
    {
    	if($this->databaseTester !== null) {
    		if(method_exists($this, 'getTearDownOperation')) {
    			$this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
    		}
    		if(method_exists($this, 'getDataSet')) {
    			 $this->getDatabaseTester()->setDataSet($this->getDataSet());
    		}
	        $this->getDatabaseTester()->onTearDown();
    	}
    	
        $this->databaseTester = null;
        
        set_time_limit(0);
    }
    
    /**
     * Creates a new XMLDataSet with the given $xmlFile. (absolute path.)
     *
     * @param string $xmlFile
     * @return PHPUnit_Extensions_Database_DataSet_XmlDataSet
     */
    protected function createXMLDataSet($xmlFile)
    {
        return new PHPUnit_Extensions_Database_DataSet_XmlDataSet($xmlFile);
    }
    
	/**
     * Asserts that a link exists.
     *
     * @param  string $link
     * @param  string $message
     */
    public static function assertLinkExists($link, $message = '')
    {
    	if (!is_string($link)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new Enlight_Components_Test_Constraint_LinkExists;

        self::assertThat($link, $constraint, $message);
    }
    
    /**
     *Asserts that a link does not exist.
     *
     * @param  string $link
     * @param  string $message
     */
    public static function assertLinkNotExists($link, $message = '')
    {
    	if (!is_string($link)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $constraint = new PHPUnit_Framework_Constraint_Not(
          new Enlight_Components_Test_Constraint_LinkExists
        );

        self::assertThat($link, $constraint, $message);
    }
    
    /**
     * Asserts that an array has count values.
     *
     * @param int $count
     * @param array $array
     * @param string $message
     */
    public static function assertArrayCount($count, array $array, $message = '')
    {
    	if (!is_int($count)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $constraint = new Enlight_Components_Test_Constraint_ArrayCount($count);

        self::assertThat($array, $constraint, $message);
    }
    
    /**
     * Asserts that an array does not have count values.
     *
     * @param int $count
     * @param array $array
     * @param string $message
     */
    public static function assertArrayNotCount($count, array $array, $message = '')
    {
    	if (!is_int($count)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }
        
        $constraint = new PHPUnit_Framework_Constraint_Not(
          new Enlight_Components_Test_Constraint_ArrayCount($count)
        );

        self::assertThat($array, $constraint, $message);
    }
}