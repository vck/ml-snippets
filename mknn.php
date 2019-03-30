<?php 

/*
modified k-nearest neighbor 

reference: http://www.iaeng.org/publication/WCECS2008/WCECS2008_pp831-834.pdf
*/

require 'vendor/autoload.php'; 
use Phpml\Metric\Accuracy;
use Phpml\Metric\ConfusionMatrix;
use Phpml\CrossValidation\RandomSplit;

 
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

function weight_voting(array $train, array $test, array $train_validity, int $k): array{
   print_r(count($train));
   echo "\n";
   print_r(count($test));
   $train_test_distance = array();

   for($test_index=0; $test_index < count($test); $test_index++){
      for($train_index=0; $train_index < count($train); $train_index++){
         $train_test_distance[$test_index][$train_index] = distance($test[$test_index], $train[$train_index]);
      }
   }
   $weights = array();
   $weight_class = array();
   
   for($test_index=0; $test_index < count($test); $test_index++){
      for($train_index=0; $train_index < count($train); $train_index++){
         $data_weight = $train_validity[$train_index]/($train_test_distance[$test_index][$train_index] + 0.5);
         $weight_class_item = array();
         $weight_class_item["weight"] = $data_weight;
         $weight_class_item["class"] = $train[$train_index][4];
         $weights[$test_index][$train_index] = $weight_class_item;
      }
   }
   
   $predicted_class = array();
   
   for($row=0; $row<count($weights); $row++){
      $i_test_weights = $weights[$row];
      $largest_weights = array_column($i_test_weights, "weight");
      $i_class = array_column($i_test_weights, "class");
      array_multisort($largest_weights, SORT_DESC, $i_class, SORT_ASC, $i_test_weights); 
      
      $majority_class = array_column($i_test_weights, "class");
      $sliced_majority = array_slice($majority_class, 0, $k);
      $predicted_class[] = (int) $sliced_majority[0];
   }
   return $predicted_class;
}

function data_class(array $data): array
{
   $class = array();
   for($i=0; $i<count($data); $i++){
      $class[] = (int) end($data[$i]);
   }
   return $class;
}
?>
   
