<?php 

$train_iris = array([7.7,2.6,6.9,2.3,0],
					[6.0,2.2,5.0,1.5,0],
					[6.9,3.2,5.7,2.3,0],
					[5.6,2.8,4.9,2.0,0],
					[7.7,2.8,6.7,2.0,0],
					[6.3,2.7,4.9,1.8,0],
					[6.7,3.3,5.7,2.1,0],
					[7.2,3.2,6.0,1.8,0],
					[6.2,2.8,4.8,1.8,0],
					[6.1,3.0,4.9,1.8,0],
					[5.5,2.6,4.4,1.2,1],
					[6.1,3.0,4.6,1.4,1],
					[5.8,2.6,4.0,1.2,1],
					[5.0,2.3,3.3,1.0,1],
					[5.6,2.7,4.2,1.3,1],
					[5.7,3.0,4.2,1.2,1],
					[5.7,2.9,4.2,1.3,1],
					[6.2,2.9,4.3,1.3,1],
					[5.1,2.5,3.0,1.1,1],
					[5.7,2.8,4.1,1.3,1],
					[5.4,3.9,1.3,0.4,2],
					[5.1,3.5,1.4,0.3,2],
					[5.7,3.8,1.7,0.3,2],
					[5.1,3.8,1.5,0.3,2],
					[5.4,3.4,1.7,0.2,2],
					[5.1,3.7,1.5,0.4,2],
					[4.6,3.6,1.0,0.2,2],
					[5.1,3.3,1.7,0.5,2],
					[4.8,3.4,1.9,0.2,2],
					[5.0,3.0,1.6,0.2,2]);

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
		//print_r($s_func);
		$train_validity[$train_index] = array_sum($s_func)/$k;
		//print_r($train_validity);	
	}
	print_r(array_values($train_validity));
	//print_r($train_validity);
}

train_mknn($train_iris, 3)

?>
