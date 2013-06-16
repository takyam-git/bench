<?php
class Bench
{
	const MESSAGE_NOT_RUN = "Didn't run benchmark(s) yet.";

	protected $target_files = array();
	protected $results = null;

	/**
	 * ベンチ対象のターゲットファイルを配列に格納して初期化する
	 * @param array|string $file_paths
	 */
	public function __construct($file_paths)
	{
		if (!is_array($file_paths)) {
			$file_paths = func_get_args();
		}
		foreach ($file_paths as $file_path) {
			$this->set_file($file_path);
		}
	}

	/**
	 * ベンチ対象のターゲットファイルを追加する
	 * @param string $file_path
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function set_file($file_path)
	{
		if ($this->file_exists($file_path)) {
			$this->target_files[] = $file_path;
		} else {
			throw new InvalidArgumentException();
		}

		return $this;
	}

	/**
	 * ファイルが存在するかどうかを返す
	 * @param string $file_path
	 * @return bool
	 */
	protected function file_exists($file_path)
	{
		return is_file($file_path);
	}

	/**
	 * ベンチマークを実行して結果をプロパティに保存する
	 * @return $this
	 */
	public function run()
	{
		foreach ($this->target_files as $key => $target_file) {
			$result = array('script' => $target_file);
			try {
				$start_time = microtime(true);
				$function = function () use ($target_file) {
					include($target_file);
				};
				$function();
				$result['micro_seconds'] = microtime(true) - $start_time;
				$result['milli_seconds'] = (int)round($result['micro_seconds'] * 1000);
			} catch (Exception $error) {

			}
			$this->results[$key] = $result;
		}
		return $this;
	}

	/**
	 * 結果の配列を返す
	 * @return null|array
	 */
	public function get_results()
	{
		return $this->results;
	}

	/**
	 * 結果を文字列で返す
	 * @return string
	 */
	public function format()
	{
		if (is_null($this->get_results())) {
			return static::MESSAGE_NOT_RUN;
		}

		$result_messages = [];
		foreach ($this->get_results() as $key => $result) {
			$result_messages[] = array(
				'#' . $key . ' : ' . $result['script'],
				'    time: ' . $result['milli_seconds'] . ' milli sec (' . $result['micro_seconds'] . ' micro sec)',
			);
		}
		$max_width = max(array_map(function ($result_array) {
			return max(array_map(function ($row) {
				return mb_strlen($row);
			}, $result_array));
		}, $result_messages));
		$separator = str_repeat('-', $max_width);

		$header_text = 'Benchmark results';
		$header_separator = str_repeat('#', round(($max_width - strlen($header_text)) / 2));
		$messages = array(
			$header_separator . ' ' . $header_text . ' ' . $header_separator,
			str_repeat('=', $max_width),
		);
		$messages[] = implode(PHP_EOL . $separator . PHP_EOL, array_map(function ($rows) {
			return implode(PHP_EOL, $rows);
		}, $result_messages));

		return implode(PHP_EOL, $messages);
	}

	/**
	 * 結果を文字列で返す
	 * @return string
	 */
	public function __toString()
	{
		return $this->format();
	}
}