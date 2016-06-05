<?php 

function load_all_files(){
	require_once "constants.php";
	require_once "card_objects.php";
	require_once "game_objects.php";
	require_once "other_objects.php";
}

function import_csv($location){	
	$row = 1;
	$ret = [];
	$columns = [];
	if (($handle = fopen($location, "r")) !== FALSE) {
		while (($data = fgetcsv($handle)) !== FALSE) {
			$num = count($data);
			if($row != 1){
				for ($c=0; $c < $num; $c++) {
					$ret[$row-2][strtolower($columns[$c])] = $data[$c];
				}
			}
			else{
				$columns = $data;
			}
			$row++;
		}
		fclose($handle);
	}
	return $ret;
}
function import_standard_deck(){
	$deck_raw = import_csv("resources/standard_deck.csv");
	return build_raw_deck($deck_raw);
}
function build_raw_deck($raw_cards){
	$deck = new Deck();
	foreach($raw_cards as $rc){
		$data = [];
		if(!empty($rc['serial']))
			$data['serial'] = $rc['serial'];
		if(!empty($rc['name']))
			$data['name'] = $rc['name'];
		if(!empty($rc['description']))
			$data['description'] = $rc['description'];
		if(!empty($rc['image']))
			$data['image'] = $rc['image'];		
		if(!empty($rc['type']) && defined( "TYPE_" . strtoupper($rc['type']))){
			$data['type'] = constant("TYPE_" . strtoupper($rc['type']) );
		}
		if(!empty($rc['age']))
			$data['age'] = $rc['age'];		
		if(!empty($rc['player_count']))
			$data['player_count'] = $rc['player_count'];		
		
		if(!empty($rc['cost'])){
			$cost = make_stock_from_string($rc['cost']);
			if($cost)
				$data['cost'] = $cost;
		}
		
		if(!empty($rc['builds_into']))
			$data['builds_into'] = explode(",",$rc['builds_into']);
		if(!empty($rc['builds_from']))
			$data['builds_from'] = explode(",",$rc['builds_from']);
		
		$bdata = [];
		if(!empty($rc['b_res']))
			$bdata['resources'] = make_stock_from_string($rc['b_res']);
		if(!empty($rc['b_points']))
			$bdata['points'] = $rc['b_points'];
		if(!empty($rc['b_army']))
			$bdata['army'] = $rc['b_army'];
		if(!empty($rc['b_abilities']))
			$bdata['abilities'] = explode(",",$rc['b_abilities']);
		if(!empty($rc['b_science']) && defined("SCI_" . $rc['b_science']))
			$bdata['science'] =  constant("SCI_" . $rc['b_science']);
		if(!empty($rc['b_bonuses']))
			$bdata['bonuses'] = explode(",",$rc['b_bonuses']);
		
		$data['benefit'] = new Benefit($bdata);
		
		$card = new Card($data);
		$deck->add_card($card);
	}
	
	return $deck;
}
function make_stock_from_string($string, $make_object = true){
	$res = explode(',',$string);
	if(empty($res))
		return false;
	
	$ret = [];
	foreach($res as $r){
		trim($r);
		$r = explode(' ',$r);
		$name = array_pop($r);
		$amount = array_shift($r);
		
		if(empty($amount) || !defined("RES_" . strtoupper($name)))
			continue;
		else
			$ret[  constant( "RES_" . strtoupper($name) ) ] = $amount;
	}
	unset($r);
	
	if(!$make_object)
		return $ret;
	
	return new Stock($ret);
}
