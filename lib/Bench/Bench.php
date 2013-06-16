<?php
class Bench
{
	const MESSAGE_NOT_RUN = "Didn't run benchmark(s) yet.";

	protected $target_files = array();
	protected $results = null;
	protected $repeat = 1;

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
	 * 実行回数を設定する
	 * @param int $repeat_number
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function set_repeat($repeat_number)
	{
		if(!is_numeric($repeat_number) || (int)$repeat_number <= 0){
			throw new InvalidArgumentException();
		}
		$this->repeat = (int)$repeat_number;
		return $this;
	}

	/**
	 * ベンチマークを実行して結果をプロパティに保存する
	 * @return $this
	 */
	public function run()
	{
		foreach ($this->target_files as $key => $target_file) {
			try {
				$results = array();
				$min_micro_seconds = floatval(PHP_INT_MAX);
				$max_micro_seconds = 0.0;
				$total_micro_seconds = 0.0;
				$function = function () use ($target_file) {
					include($target_file);
				};
				for($i = 0; $i < $this->repeat; $i++){
					$result = array();
					$start_time = microtime(true);
					$function();
					$result['micro_seconds'] = microtime(true) - $start_time;
					$result['milli_seconds'] = (int)round($result['micro_seconds'] * 1000);
					$results[] = $result;

					$min_micro_seconds = min($result['micro_seconds'], $min_micro_seconds);
					$max_micro_seconds = max($result['micro_seconds'], $max_micro_seconds);
					$total_micro_seconds += $result['micro_seconds'];
				}
				$average_micro_seconds = $total_micro_seconds / count($results);
				$this->results[$key] = array(
					'script' => $target_file,
					'max_micro_seconds' => $max_micro_seconds,
					'min_micro_seconds' => $min_micro_seconds,
					'max_milli_seconds' => (int)round($max_micro_seconds * 1000),
					'min_milli_seconds' => (int)round($min_micro_seconds * 1000),
					'avg_micro_seconds' => $average_micro_seconds,
					'avg_milli_seconds' => (int)round($average_micro_seconds * 1000),
					'total_micro_seconds' => $total_micro_seconds,
					'total_milli_seconds' => (int)round($total_micro_seconds * 1000),
					'count' => count($results),
					'results' => $results,
				);
			} catch (Exception $error) {
			}
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
				'    (' . $result['count'] . ' times)',
				'    avg time: ' . $result['avg_milli_seconds'] . ' milli sec (' . $result['avg_micro_seconds'] . ' micro sec)',
				'    max time: ' . $result['max_milli_seconds'] . ' milli sec (' . $result['max_micro_seconds'] . ' micro sec)',
				'    min time: ' . $result['min_milli_seconds'] . ' milli sec (' . $result['min_micro_seconds'] . ' micro sec)',
				'    total time: ' . $result['total_milli_seconds'] . ' milli sec (' . $result['total_micro_seconds'] . ' micro sec)',
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