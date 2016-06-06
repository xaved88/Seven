<?php

require_once "lib/helper.php"; 
load_all_files();

$game = new Game();
$adam = $game->add_player( ['id'=>'1', 'name' => 'Adam'] );
$game->add_player( ['id'=>'2', 'name' => 'Eve'] );
$game->add_player( ['id'=>'3', 'name' => 'Jane'] );
// $game->add_player( ['id'=>'4', 'name' => 'Bob'] );
// $game->add_player( ['id'=>'5', 'name' => 'Bill'] );
// $game->add_player( ['id'=>'6', 'name' => 'Lacy'] );
$game->deck->deal();
$game->display_hands();
// die();

$adam->display();
foreach($adam->hand->cards as $c){
	$rs = $adam->play($c);
	echo "\nRESPONSE:\n";
	print_r($rs);
	echo "\nDISPLAY:\n";
	$adam->display();
}