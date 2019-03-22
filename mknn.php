<?php 

require 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

$spreadsheet = $reader->load("target.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();
 
function distance($a, $b){
   $len_a = count($a)-1;
   $len_b = count($b)-1;

   $dist = array();

   if($len_b == $len_a){
      for($i=0; $i<$len_a; $i++){
      	$res = ($a[$i]-$b[$i])**2;
         $dist[] = $res;
      }
      return sqrt(array_sum($dist));
   }else{
      die("array length not equal");
      print_r(count($a));
      print_r(count($b));
   }
}

function train_mknn($train, $k){

	// compute distance of i-th train data over all of data points
	// get label of k nearest distance data points
	// plug those data point to S function
	// divide over K
	// use the validity information to compute weight information  
	// compute i-th train data validity over every data

	$datasets = train_test_split($train, 0.5);
	$train_data = $datasets[0];
	$test_data = $datasets[1];
	$length_train = count($train_data);

	$train_validity = array();

	for($train_index=0; $train_index<$length_train; $train_index++){
		$distances = array();
		$validity = array();
		for($j=0; $j<$length_train; $j++){
			$dist = distance($train[$train_index], $train[$j]);
			if($dist != 0){
				$distances[$j] = $dist;
			}
		}
		// get k closest data point

		$sorted_distance = array_values($distances);
		sort($sorted_distance);
		print_r($distances);
		$k_closest_neighbors = array();
		$s_func = array();

		for($neighbor_index=0; $neighbor_index<$k; $neighbor_index++){
			$k_closest_neighbors[] = $sorted_distance[$neighbor_index];
		}

		for($i=0; $i<count($k_closest_neighbors); $i++){
			// get key of value using array_search
			$key = array_search($k_closest_neighbors[$i], $distances);

			if($train[$key][4] == $train[$train_index][4]){
				$s_func[] = 1;
			}else{
				$s_func[] = 0;
			}
		}
		print_r($s_func);
		$train_validity[$train_index] = array_sum($s_func)/$k;		
	}

	// weight voting 
	// compute weight using validity(i) * (1/(d_i + 0.5))
	// where i is train data index and d_i is distance(data_train[i], data_test[j])

	$weight_voting = array();

	for($train_index=0; $train_index<count($train_data); $train_index++){
		for($test_index=0; $test_index<count($test_data); $test_index++){
			print_r(count($test_data[$test_index]));
			echo "\n";
			print_r(count($train_data[$train_index]));
			echo "\n";
			$dist = distance($train_data[$train_index], $test_data[$test_index]);
			$weight_voting[$train_index] = $train_validity[$train_index]/($dist+0.5);
		}
	}
	//print_r(array_values($train_validity));
	print_r($weight_voting);
}

function test_mknn($test, $k){
	// return confusion matrix
}


function train_test_split($datasets, $ratio){
	$train_data = array();
	$test_data = array();

	for($train_index = 0; $train_index < $ratio*count($datasets); $train_index++){
		$train_data[] = $datasets[$train_index];
	}

	for($test_index = intval($ratio*count($datasets)); $test_index < count($datasets); $test_index++){
		$test_data[] = $datasets[$test_index];
	}
	return array($train_data, $test_data);
}

train_mknn($sheetData, 5);

?>
