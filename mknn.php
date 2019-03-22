<?php 


$train = array([0.823, 0.212, 0.111, 0.002, 0],
			   [0.814, 0.439, 0.211, 0.004, 0],
			   [0.125, 0.964, 0.321, 0.081, 0],
			   [0.226, 0.212, 0.421, 0.213, 0],
			   [0.966, 0.921, 0.342, 0.224, 0],
			   [0.789, 0.271, 0.411, 0.226, 1],
			   [0.821, 0.892, 0.927, 0.227, 1],
			   [0.985, 0.988, 0.829, 0.826, 1],
			   [0.964, 0.227, 0.289, 0.666, 1],
			   [0.464, 0.272, 0.982, 0.789, 1]);

function distance($a, $b){
   $len_a = count($a);
   $len_b = count($b);

   $dist = array();

   if($len_b == $len_a){
      for($i=0; $i<$len_a; $i++){
      	$res = ($a[$i]-$b[$i])**2;
         $dist[] = $res;
      }
      return sqrt(array_sum($dist));
   }else{
      die("array length not equal");
   }
}

/*
for($i=0; $i < count($train); $i++){
	for($j=0; $j < count($train); $j++){
		$nearest = array();
		$dist = distance($train[$i], $train[$j]);
		if($dist!=0){
			print_r($dist);
		}
	}
}

*/	

function train_mknn($train, $k){

	// compute distance of i-th train data over all of data points
	// get label of k nearest distance data points
	// plug those data point to S function
	// divide over K
	// use the validity(i) to compute weight(i)  
	// compute i-th train data validity over every data
	// compute i-th train data and test data weight

	$length_train = count($train);

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

		for($i=0; $i<$k; $i++){
			$k_closest_neighbors[] = $sorted_distance[$i];
		}

		for($i=0; $i<count($k_closest_neighbors); $i++){
			// get key of value using array_search
			$key = array_search($k_closest_neighbors[$i], $distances);
			//print_r($key);
			//echo "\n";
			// get data point on index $key

			if($train[$key][4] == $train[$train_index][4]){
				$s_func[] = 1;
			}else{
				$s_func[] = 0;
			}
		}
		print_r($s_func);
		$train_validity[$train_index] = array_sum($s_func)/$k;
		//print_r($train_validity);
		
	}
	//print_r(array_values($train_validity));
	//print_r($train_validity);
}

train_mknn($train, 3)

?>
