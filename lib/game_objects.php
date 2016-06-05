<?php

class Game{
	public
		$deck,
		$players,
		$hands,
		$discard,
		
		$age,
		$player_count,
		$player_index;
	public function __construct(){	
		$this->deck = import_standard_deck();
		$this->age = 1;
	}
	
	public function add_player($player){
		if(!($player instanceof Player))
			$player = new Player($player);
		
		// $player->add_coins(3,LOG_SETUP);
		
		$this->players[] = $player;
		$this->player_count = count($this->players);
		
		$this->rebuild_player_index();
		
		return $player;
	}
	public function get_player($id){
		if(isset($this->player_index[$id]))
			return $this->players[ $this->player_index[$id] ];
		return false;
	}
	public function rebuild_player_index(){
		$this->player_index = [];
		if(!empty($this->players))
		foreach($this->players as $index => $player){
			$this->player_index[ $player->id ] = $index;
		}
	}
	
	public function display_hands(){
		if(!empty($this->hands))
		foreach($this->hands as $hand){
			$hand->display();
			echo "-----\n";
		}
	}
}
class Player{
	public
		$id,
		$name,
		$hand,
		$wonder,
		$city,
		$stock; // THIS IS SET STOCK, WITHOUT TAKING INTO ACCOUNT ANY ALGORITHMS OR ANYTHING
	
	public function __construct($details){
		if(!empty($details['id']))
			$this->id = $details['id'];
		if(!empty($details['name']))
			$this->name = $details['name'];
		
		$this->city = new City();
		$this->stock = new Stock();
	}
	
	public function display(){
		// print_r($this);
		
		echo "{$this->id} :: {$this->name}\n";		
		echo "Hand:\n";
		$this->hand->display();
		echo "City:\n";
		$this->city->display();
	}
	
	// STILL TO DO
	public function can_play($cardObj){ // OR ID
		// PREP
		if($cardObj instanceof Card)
			$card = $cardObj;
		else{
			global $game;
			$card = $game->deck->get_card($cardObj);
		}
		if(empty($card))
			return rs_error("card_doesnt_exist");
		
		// CHECK THAT PLAYER HOLDS THE CARD
		if(!$this->hand->has_card($card))
			return rs_error("card_not_in_hand");
		
		// CHECK THAT PLAYER HAS THE RESOURCES
		$rs = $this->has_resources_for($card->cost);
		
		return $rs;
		
	}
	public function play( $cardObj, $play_type = PLAY_DEFAULT ){ // OR ID
		// PREP
		if($cardObj instanceof Card)
			$card = $cardObj;
		else{
			global $game;
			$card = $game->deck->get_card($cardObj);
		}
		if(empty($card))
			return rs_error("card_doesnt_exist");
		
		$rs = $this->can_play($cardObj);
		
		if(!$rs->success())
			return $rs;
		
		switch($play_type){
			case PLAY_DEFAULT:
				$this->city->add_card($cardObj);
				break;
			case PLAY_WONDER:
			
				break;
			
			case PLAY_DISCARD:
			
				break;
		}
		$this->hand->remove_card($cardObj);
		
		return $rs;
	}
	public function has_resources_for($stockObj){
		$missing = new Stock();
		
		foreach($stockObj->resources as $resource => $amount ){
			if($amount > $this->stock->resources[$resource])
				$missing->add($resource,$amount - $this->stock->resources[$resource]);
		}
		
		if($missing->is_empty())
			return rs_success("has_resources");
		
		return rs_warning("missing_resources");
	}
	
	public function add_coins($amount, $log_reason = LOG_DEFAULT){
		$this->stock->add(RES_COIN, $amount);
	}
	public function remove_coins($amount, $log_reason = LOG_DEFAULT){
		$this->stock->remove(RES_COIN, $amount);
	}
	
}
class Turn{
	
}

class Stock{
	public 
		$resources = [];
	
	
	public function __construct($resources = []){
		$this->resources = $resources;
		
		for($i= RES_COIN; $i <= RES_WOOD; $i++){
			if(!isset($this->resources[$i]))
				$this->resources[$i] = 0;
		}
		for($i= RES_CLOTH; $i <= RES_PAPER; $i++){
			if(!isset($this->resources[$i]))
				$this->resources[$i] = 0;
		}
	}
	
	public function add($type, $amount = 1){ // VOID
		if(empty($this->resources[$type]))
			$this->resources[$type] = $amount;
		else
			$this->resources[$type] += $amount;
	}
	
	public function remove($type, $amount = 1){ // RS
		if(empty($this->resources[$type]))
			return rs_error('missing_resource', ['type' => $type, 'amount' => $amount]);
		else if ($this->resources[$type] - $amount < 0)
			return rs_error('missing_resource', ['type' => $type, 'amount' => $this->resources[$type]  - $amount]);
		else
			$this->resources[$type] -= $amount;
		return rs_success();
	}
	
	public function compare($stockObj){ // RS
		$lacking = [];
		if(!empty($stockObj->resources))
		foreach($stockObj->resources as $type => $amount){
			if($amount <= 0)
				continue;
			if(!isset($this->resources[$type])){
				$lacking[ $type ] = $amount;
			}
			else if($this->resources[$type] < $amount){
				$lacking[$type] = $amount - $this->resources[$type];
			}
		}
		
		if(!empty($lacking))
			return rs_warning('missing_resource',['lacking' => $lacking]);
		else
			return rs_success();
	}
	
	public function display(){ // VOID
		$ress = [];
		if(!empty($this->resources))
		foreach($this->resources as $res => $amount){
			$ress[] = constant("RES_" . $res) . "\t " . $amount;
		}
		echo implode("\n",$ress) . "\n";
	}
	
	public function is_empty(){
		foreach($this->resources as $r){
			if($r)
				return false;
		}
		return true;
	}
	
}
class Benefit{ // ON CARDS, ETC
	public 
		$resources, // STOCK. ARRAY IF IT'S X OR Y OR Z.
		$points, // direct points, just a number amount
		$army,
		$abilities, // [] Trading, once per round do things, bonus resources. Things you can act upon
		$science,
		$bonuses; // [] THINGS LIKE "POINTS FOR XYZ" , or "$$ for XYZ". Things that are passive.
		
	public function __construct($data = []){
		if(!empty($data['resources'])){
			if( $data['resources'] instanceof Stock)
				$this->resources = $data['resources'];
			else
				$this->resources= new Stock ($data['resources']);
		}
		else
			$this->resources = new Stock();
		
		if(!empty($data['points']))
			$this->points = $data['points'];
		else
			$this->points = 0;
		
		if(!empty($data['army']))
			$this->army = $data['army'];
		else
			$this->army = 0;
		
		if(!empty($data['abilities']))
			$this->abilities = $data['abilities'];
		else
			$this->abilities = [];
				
		if(!empty($data['science']))
			$this->science = $data['science'];
		else
			$this->science = false;
		
		if(!empty($data['bonuses']))
			$this->bonuses = $data['bonuses'];
		else
			$this->bonuses = [];
	}
	
	
}
