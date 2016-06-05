<?php 

/*
define("RES_COIN",1); define("RES_1", "coin");
define("RES_STONE",2); define("RES_2","stone");
define("RES_BRICK",3); define("RES_3","brick");
define("RES_ORE",4); define("RES_4","ore");
define("RES_WOOD",5); define("RES_5","wood");
define("RES_CLOTH",11); define("RES_11","cloth");
define("RES_GLASS",12); define("RES_12","glass");
define("RES_PAPER",13); define("RES_13","paper");

define("TYPE_BROWN",1);
define("TYPE_GRAY",2);
define("TYPE_GREY",2);
define("TYPE_YELLOW",3);
define("TYPE_GREEN",4);
define("TYPE_BLUE",5);
define("TYPE_RED",6);
define("TYPE_PURPLE",7);


$cost, // STOCK
$type,
$name,
$image,
$description,
$player_count,
$builds_into,
$builds_from,
$benefits;

*/
/*
$cards = [];


$cards[] = [
	'name' => 'Forester Hut',
	'cost' => []
	'type' => "TYPE_BROWN",
	'player_count' => 3,
	'benefits' => [
		'resources' =>[
			'RES_WOOD' => 1
		]
	]
];

*/
/* FIELDS

SERIAL
NAME
DESCRIPTION
IMAGE
TYPE
AGE
PLAYER_COUNT
COST
BUILDS_INTO	 []
BUILDS_FROM 	 []
B_RES_COIN
B_RES_STONE
B_RES_BRICK
B_RES_ORE
B_RES_WOOD
B_RES_CLOTH
B_RES_GLASS
B_RES_PAPER
B_POINTS
B_ARMY
B_ABILITIES  	[]
B_SCIENCE
B_BONUSES 		[]

*/


function import_csv($location){	
	$row = 1;
	$ret = [];
	$columns = [];
	if (($handle = fopen($location, "r")) !== FALSE) {
		while (($data = fgetcsv($handle)) !== FALSE) {
			$num = count($data);
			if($row != 1){
				for ($c=0; $c < $num; $c++) {
					$ret[$row-2][$columns[$c]] = $data[$c];
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

$deck_raw = import_csv("../lib/standard_deck.csv");
print_r($deck_raw);