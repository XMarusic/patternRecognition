<?php

	$eps = 0.5;
	$minCount = 3;

	function DBSCAN($point, $eps, $minCount){
		$C = 0;
		foreach ($point as $key => $value) {
			if($visited[$value]){
				continue;
			}
			$visited[$value] = true;

			$NeighborPts = regionQuery($point, $eps);
			if(sizeof($NeighborPts) < $minCount)
				$noise[$value] = true;

			else {
				$C = nextCluster;
				expandCluster($points, $NeighborPts, $C, $eps, $minCount);
			}
		}
	}

	function expandCluster($point, $NeighborPts, $C, $eps, $minCount){
		array_push($cluster, $point);

		foreach ($NeighborPts as $key => $value) {
			if(!$visited[$value]){
				$visited[$point] = true;
				$NeighborPts = regionQuery($point, $eps);
				if(sizeof($NeighborPts) >= $minCount)
					$NeighborPts;
			}
			if(!$clustered[$P]){
				array_push($cluster, $point);
			}
		}
	}

?>