<?php 

require 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

$spreadsheet = $reader->load("target.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();
 
function distance(array $a, array $b): float{
   /*
	prevent function from computing class label, with consensus that
	data label always on the right-most row,
	each data $a, $b length need to be reduced by 1
   */
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

function train_test_distance(array $train, array $test): array{
	$dist = array();
	for($train_index=0; $train_index<count($train); $train_index++){
		for($test_index=0; $test_index<count($test); $test_index++){
			$dist[] = distance($train[$train_index], $test[$test_index]);
		}
	}
	return $dist;
}

function validity(array $train, int $k){
	
	$train_data = $train;
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

		$train_validity[$train_index] = array_sum($s_func)/$k;		
	}
	return $train_validity;

}

function get_weights(array $validity, array $distance, $train_length, $test_length){
	$weight_voting = array();
	$empty = array();

	for($train_index=0; $train_index<count($train_data); $train_index++){
		for($test_index=0; $test_index<count($test_data); $test_index++){
			$dist = distance($train_data[$train_index], $test_data[$test_index]);
			$weight_voting[$train_index] = $train_validity[$train_index]/($dist+0.5);
		}
	}
	return $weight_voting;
}

function train_mknn(array $train, int $k){

	/*
	1. compute distance of i-th train data over all of data points
	2. get label of k nearest distance data points
	3. plug those data point to S function
	4. divide over K
	5. use the validity information to compute weight information  
	6. compute i-th train data validity over every data
	*/

	

	// weight voting 
	// compute weight using validity(i) * (1/(d_i + 0.5))
	// where i is train data index and d_i is distance(data_train[i], data_test[j])

	
}

function test_mknn($test, $k){
	// return confusion matrix
}


function train_test_split(array $datasets, float $ratio): array
{
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


/*

todo functions:

- train

--> input: training, k
--> output: 

- test

--> input: y_test, y_predict
--> output: accuracy

- confusion matrix

--> input: y_test, y_predict
--> output: confusion matrix
*/

function weight_voting(array $train, array $test, array $train_validity){

	// output: array of class test data
	
	$train_test_distance = array();

	for($test_index=0; $test_index < count($train); $test_index++){
	   for($train_index=0; $train_index < count($test); $train_index++){
	      $train_test_distance[$test_index][$train_index] = distance($test[$test_index], $train[$train_index]);
	   }
	}

	//print_r($train_test_distance);

	$weights = array();
	$weight_class = array();

	for($test_index=0; $test_index < count($test); $test_index++){
	   for($train_index=0; $train_index < count($train); $train_index++){
	      $data_weight = $train_validity[$train_index]/($train_test_distance[$test_index][$train_index] + 0.5);
	      //$weight_class[$test_index][$train_index] = $train[$train_index][4];
	      $weight_class_item = array();
	      $weight_class_item["weight"] = $data_weight;
	      $weight_class_item["class"] = $train[$train_index][4];
	      $weights[$test_index][$train_index] = $weight_class_item;
	   }

	   // cari kelas dengan nilai weight voting tertinggi, kelas data uji adalah k kelas mayoritas
	   // dengan weight paling tinggi
	}

	//print_r($weight_class);
	//print_r($train[0][4]);
	print_r($weights);
	}

$splited_datasets = train_test_split($sheetData, 0.5);
$train = $splited_datasets[0];
$test = $splited_datasets[1];
$train_validity = validity($train, 3);

weight_voting($train, $test, $train_validity);
?>
