<?
namespace injector\tests;


class TestInjector{
	/**
	 * Test bind interface
	 *
	 */
	static function testBind(\injector\Bind $spec, $expect, $singleton){
		assert($spec->provide()     == $expect);
		assert($spec->isSingleton() == $singleton);

		return true;
	}
}
