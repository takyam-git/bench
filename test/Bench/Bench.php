<?php
require_once('lib/Bench/Bench.php');
class Test_Bench extends PHPUnit_Framework_TestCase
{
	public function target_files()
	{
		$example_dir = dirname(__FILE__) . '/../../example/functions';
		return array(
			array(
				$example_dir . '/array_map.php',
				$example_dir . '/foreach.php',
			),
		);
	}

	public function test__construct()
	{
		$this->assertInstanceOf('Bench', $bench = new Bench(__FILE__));

		$target_files = new ReflectionProperty($bench, 'target_files');
		$target_files->setAccessible(true);
		$this->assertInternalType('array', $target_files_value = $target_files->getValue($bench));
		$this->assertTrue(in_array(__FILE__, $target_files_value));

		$this->assertInstanceOf('Bench', $bench = new Bench(__FILE__, __FILE__, __FILE__));

		$target_files = new ReflectionProperty($bench, 'target_files');
		$target_files->setAccessible(true);
		$this->assertInternalType('array', $target_files_value = $target_files->getValue($bench));
		$this->assertEquals(array(__FILE__, __FILE__, __FILE__), $target_files_value);

		$this->setExpectedException('InvalidArgumentException');
		new Bench(__FUNCTION__);
	}

	public function test_set_file()
	{
		$this->assertInstanceOf('Bench', $bench = new Bench(__FILE__));

		$target_files = new ReflectionProperty($bench, 'target_files');
		$target_files->setAccessible(true);
		$this->assertInternalType('array', $target_files_value = $target_files->getValue($bench));
		$this->assertTrue(in_array(__FILE__, $target_files_value));

		$this->assertInstanceOf('Bench', $bench->set_file(__FILE__));

		$this->assertInternalType('array', $target_files_value = $target_files->getValue($bench));
		$this->assertEquals(array(__FILE__, __FILE__), $target_files_value);

		$this->setExpectedException('InvalidArgumentException');
		$bench->set_file(__FUNCTION__);
	}

	public function test_file_exists()
	{
		$bench = new Bench(__FILE__);
		$method = new ReflectionMethod($bench, 'file_exists');
		$method->setAccessible(true);
		$this->assertTrue($method->invoke($bench, __FILE__));
		$this->assertFalse($method->invoke($bench, __FUNCTION__));
	}

	public function test_set_repeat()
	{
		$bench = new Bench(__FILE__);
		$repeat = new ReflectionProperty($bench, 'repeat');
		$repeat->setAccessible(true);
		$this->assertSame(1, $repeat->getValue($bench));
		$this->assertInstanceOf('Bench', $bench->set_repeat(100));
		$this->assertSame(100, $repeat->getValue($bench));

		$this->setExpectedException('InvalidArgumentException');
		$bench->set_repeat(-1);
		$bench->set_repeat(0);
	}

	/**
	 * @dataProvider target_files
	 */
	public function test_run($array_map, $foreach)
	{
		$bench = new Bench($array_map, $foreach);

		$result_property = new ReflectionProperty($bench, 'results');
		$result_property->setAccessible(true);
		$this->assertNull($result_property->getValue($bench));

		$this->assertInstanceOf('Bench', $bench->run());

		$this->assertInternalType('array', $results = $result_property->getValue($bench));
		foreach ($results as $key => $value) {
			$this->assertInternalType('array', $value);
			$this->assertArrayHasKey('script', $value);
			$this->assertFileExists($value['script']);
			$this->assertArrayHasKey('avg_milli_seconds', $value);
			$this->assertInternalType('int', $value['avg_milli_seconds']);
			$this->assertArrayHasKey('avg_micro_seconds', $value);
			$this->assertInternalType('float', $value['avg_micro_seconds']);
			$this->assertArrayHasKey('max_milli_seconds', $value);
			$this->assertInternalType('int', $value['max_milli_seconds']);
			$this->assertArrayHasKey('max_micro_seconds', $value);
			$this->assertInternalType('float', $value['max_micro_seconds']);
			$this->assertArrayHasKey('min_milli_seconds', $value);
			$this->assertInternalType('int', $value['min_milli_seconds']);
			$this->assertArrayHasKey('min_micro_seconds', $value);
			$this->assertInternalType('float', $value['min_micro_seconds']);
			$this->assertArrayHasKey('total_milli_seconds', $value);
			$this->assertInternalType('int', $value['total_milli_seconds']);
			$this->assertArrayHasKey('total_micro_seconds', $value);
			$this->assertInternalType('float', $value['total_micro_seconds']);
			$this->assertArrayHasKey('count', $value);
			$this->assertInternalType('int', $value['count']);
			$this->assertArrayHasKey('results', $value);
			$this->assertInternalType('array', $value['results']);
			foreach ($value['results'] as $row) {
				$this->assertInternalType('array', $row);
				$this->assertArrayHasKey('micro_seconds', $row);
				$this->assertInternalType('float', $row['micro_seconds']);
				$this->assertArrayHasKey('milli_seconds', $row);
				$this->assertInternalType('int', $row['milli_seconds']);
			}
		}
	}

	/**
	 * @dataProvider target_files
	 */
	public function test_get_results($array_map, $foreach)
	{
		$bench = new Bench($array_map, $foreach);
		$this->assertNull($bench->get_results());
		$this->assertInstanceOf('Bench', $bench->run());
		$this->assertInternalType('array', $results = $bench->get_results());
		foreach ($results as $key => $value) {
			$this->assertInternalType('array', $value);
			$this->assertArrayHasKey('script', $value);
			$this->assertFileExists($value['script']);
			$this->assertArrayHasKey('avg_milli_seconds', $value);
			$this->assertInternalType('int', $value['avg_milli_seconds']);
			$this->assertArrayHasKey('avg_micro_seconds', $value);
			$this->assertInternalType('float', $value['avg_micro_seconds']);
			$this->assertArrayHasKey('max_milli_seconds', $value);
			$this->assertInternalType('int', $value['max_milli_seconds']);
			$this->assertArrayHasKey('max_micro_seconds', $value);
			$this->assertInternalType('float', $value['max_micro_seconds']);
			$this->assertArrayHasKey('min_milli_seconds', $value);
			$this->assertInternalType('int', $value['min_milli_seconds']);
			$this->assertArrayHasKey('min_micro_seconds', $value);
			$this->assertInternalType('float', $value['min_micro_seconds']);
			$this->assertArrayHasKey('total_milli_seconds', $value);
			$this->assertInternalType('int', $value['total_milli_seconds']);
			$this->assertArrayHasKey('total_micro_seconds', $value);
			$this->assertInternalType('float', $value['total_micro_seconds']);
			$this->assertArrayHasKey('count', $value);
			$this->assertInternalType('int', $value['count']);
			$this->assertArrayHasKey('results', $value);
			$this->assertInternalType('array', $value['results']);
			foreach ($value['results'] as $row) {
				$this->assertInternalType('array', $row);
				$this->assertArrayHasKey('micro_seconds', $row);
				$this->assertInternalType('float', $row['micro_seconds']);
				$this->assertArrayHasKey('milli_seconds', $row);
				$this->assertInternalType('int', $row['milli_seconds']);
			}
		}
	}

	/**
	 * @dataProvider target_files
	 */
	public function test_format($array_map, $foreach)
	{
		$bench = new Bench($array_map, $foreach);
		$this->assertEquals(Bench::MESSAGE_NOT_RUN, $bench->format());
		$this->assertInternalType('string', $result = $bench->run()->format());

	}

	/**
	 * @dataProvider target_files
	 */
	public function test___toString($array_map, $foreach)
	{
		$bench = new Bench($array_map, $foreach);
		$this->assertEquals(Bench::MESSAGE_NOT_RUN, (string)$bench);
		$this->assertInternalType('string', (string)$bench->run());
	}
}