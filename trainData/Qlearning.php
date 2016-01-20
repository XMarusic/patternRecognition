<?php
	class QlearningValues{
		//Action size = 50. 
		//Context of this state, size = 2;
		$context = array();
		//The top 50 actions, size = 50 x 2
		$actionArray = array();
		//list of states, 50 (1 + 50) = 2501 states. Max Q value of each state. 
		$stateList = array();
		//Probability of transition of a state from an action to a state. Size = 2501 x 2501
		$transitionValue = array();
		//Probability of returning to self over an action. Size = 2501 x 50
		$transitionToSelfProbability = array();
		//Q values of each state to different actions. Size = 2501 x 50
		$qValue = array();

		$negativeReward = -8;
		$positiveReward = +10;
		$alpha = 0.3;

		function values(){
			$context = getContext();
			$actionArray = getActionArray();
			$stateList = getStateList();
			$transitionValue = getTransitionValue();
			$qValue = getqValues();
		}

		function deduceAction(){
			$returnAction = 0;
			$value = 0;
			foreach ($actionArray as $action) {
				if(in_array($action, $actionArray))
					continue;
				$tempValue = checkActionValue($context, $action);
				if($tempValue > $value){
					$value = $tempValue;
					$returnAction = $action;
				}
			}
			return $returnAction;
		}

		function checkActionValue($context, $action){
			$nextContext = createContext($context, $action);

			$indexContext = getIndex($context);
			$indexNextContext = getIndex($nextContext);

			$value = $transitionToSelfProbability[$indexContext][$action] * $negativeReward;
			$nextContextValue = $positiveReward + $qValue[$indexNextContext]['qValue'];
			$value += $transitionValue[$indexContext][$indexNextContext] * $nextContextValue;

			return $value;
		}

		function createContext($context, $action){
			array_pop($context);
			array_push($context, $action);
			return $context;
		}

		function updateQValues($context, $action, $nextContext){
			$reward = getReward($context, $nextContext);
			$sample = $reward + $getStateList[getIndex(createContext($context, $action))]['qValue'];
			$oldqValue = $qValue[getIndex($context)][$action];
			$qValue[getIndex($context)][$action] = (1-$alpha)$oldqValue + $alpha * $reward;
		}

		function getReward($context, $nextContext){
			if($context == $nextContext) 
				return $negativeReward;
			return $positiveReward;
		}

		function getIndex($context){
			return array_search($context, array_column($stateList, 'context'));
		}
	}

?>