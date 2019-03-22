<?php

function train_test_split($datasets, $ratio){
	$train_data = array();
	$test_data = array();

	for($train_index = 0; $train_index < $ratio*count($datasets); $train_index++){
		$train_data[] = $datasets[$train_index];
	}

	for($test_index = intval($ratio*count($datasets)); $test_index < count(datasets); $test_index++){
		$test_data[] = $datasets[$test_index]
	}
	return $train_data, $test_data;
}

?>
