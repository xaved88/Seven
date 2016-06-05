<?php


class Card{
	public 
		$id,
		$cost, // STOCK
		$type,
		$name,
		$image,
		$description,
		$age,
		$player_count,
		$builds_into,
		$builds_from,
		$benefit;
		
	public static $master_id = 1;
	
	public function __construct($data = []){ 
		if(!empty($data['cost'])){
			if( $data['cost'] instanceof Stock)
				$this->cost = $data['cost'];
			else
				$this->cost = new Stock ($data['cost']);
		}
		else
			$this->cost = new Stock();
		if(!empty($data['type']))
			$this->type = $data['type'];
		else
			$this->type = TYPE_BLUE;
		
		if(!empty($data['name']))
			$this->name = $data['name'];
		else
			$this->name = "Default Name";
		
		if(!empty($data['image']))
			$this->image = $data['image'];
		else
			$this->image = "Default Image";
		
		if(!empty($data['description']))
			$this->description = $data['description'];
		else
			$this->description = "";
		
		if(!empty($data['player_count']))
			$this->player_count = $data['player_count'];
		else
			$this->player_count = 3;
		
		if(!empty($data['age']))
			$this->age = $data['age'];
		else
			$this->age = 1;
		
		if(!empty($data['builds_into']))
			$this->builds_into = $data['builds_into'];
		else
			$this->builds_into = [];
		
		if(!empty($data['builds_from']))
			$this->builds_from = $data['builds_from'];
		else
			$this->builds_from = [];
		
		if(!empty($data['benefit']))
			if($data['benefit'] instanceof Benefit)
				$this->benefit = $data['benefit'];
			else
				$this->benefit = new Benefit($data['benefit']);
		else
			$this->benefit = false;
		
		$this->id = self::$master_id;
		self::$master_id ++;
	}
	
}
class CardContainer{ // PARENT OBJECT
	public 
		$cards,
		$index,
		$card_count;
	
	public function __construct( $cards = [], $data = []){
		if(!empty($cards))
			$this->cards = $cards;
		
		$this->rebuild_index();
	}
	
	public function add_card( $cardObj ){
		$this->cards[] = $cardObj;
		$this->rebuild_index();
	}
	public function get_card( $id ){
		if(isset($this->index[$id]))
			return $this->cards[ $this->index[$id] ];
		return false;
	}
	public function remove_card( $cardObj ){ // or card_id work fine too
		if($cardObj instanceof Card)
			$card_id = $cardObj->id;
		else
			$card_id = $cardObj;
		
		if($this->has_card($card_id)){
			unset( $this->cards[ $this->index[ $card_id ] ] );
			$this->rebuild_index();
		}
		else{
			
		}
	}
	public function has_card( $cardObj ) {  // or card_id works fine too
		if($cardObj instanceof Card)
			$card_id = $cardObj->id;
		else
			$card_id = $cardObj;
		if( isset($this->index[ $card_id ] ) && isset($this->cards[ $this->index[ $card_id ] ] ))
			return true;
		return false;
	}
	public function shuffle(){
		shuffle($this->cards);
		$this->rebuild_index();
	}
	public function rebuild_index(){
		$this->index = [];
		if(!empty($this->cards)){
			$this->cards = array_values($this->cards);
			foreach($this->cards as $i => $card){
				$this->index[ $card->id ] = $i;
			}
		}
		$this->card_count = count($this->cards);
	}
	public function display(){
		if(!empty($this->cards))
		foreach($this->cards as $card){
			echo $card->name . "\n";
		}
	}
	
}
class Hand extends CardContainer{ // THE HANDS THAT GET PASSED
	
}
class Deck extends CardContainer{ // THE DECK
	public function deal( ){
		global $game;
		$age = $game->age;
		$player_count = $game->player_count;
		
		// Get all the cards from the age into a card container
		$cards = $this->get_cards_for_deal($age, $player_count);
		
		// create the hands and give them to the players
		$game->hands = [];
		for($i=0; $i<$player_count; $i++){
			$game->hands[$i] = new Hand();
			$game->players[$i]->hand = $game->hands[$i];
		}
		
		// shuffle the card container
		$cards->shuffle();
		
		// deal them to the hands
		if(!empty($cards->cards)){
			foreach($cards->cards as $i => $card){
				$game->hands[$i % $player_count]->add_card($card);
			}
		}
		
	}
	public function get_cards_for_deal( $age, $player_count ){  // returns a card container of cards
		$cards = new CardContainer();
		
		if(!empty($this->cards))
		foreach($this->cards as $card){
			if($card->age == $age && $player_count >= $card->player_count)
				$cards->add_card($card);
		}
		
		return $cards;
	}
}
class Discard extends CardContainer{ /// DISCARD PILE
	
}
class City extends CardContainer{ // A PLAYERS CITY - showing their played cards.
	
}

