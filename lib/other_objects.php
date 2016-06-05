<?php 
// STANDARD FUNCTIONS
class RS{
	public 
		$status,
		$code,
		$data;
		
	public function __construct($status,$code,$data){
		$this->status = $status;
		$this->code = $code;
		$this->data = $data;
	}
	
	public function get_status(){
		return $this->status;
	}

	public function success(){
		return ($this->status == "success");
	}
	public function warning(){
		return ($this->status == "warning");
	}
	public function error(){
		return ($this->status == "error");
	}
}
function rs_error($code = "general", $data = [] ){
	return new RS('error',$code,$data);
}
function rs_warning($code = "general", $data = [] ){
	return new RS('warning',$code,$data);
}
function rs_success($code = "general", $data = [] ){
	return new RS('success',$code,$data);
}

