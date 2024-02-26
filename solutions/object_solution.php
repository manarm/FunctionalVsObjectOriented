<?php
// class Contact {
// 	public static function BulkUpdateCustomData($User, $RecordType, $CustomDataToUpdate, $ContactIds) {
// 		// Do actual update here.
// 		echo "Update: ".json_encode([
// 				'User' => $User, 
// 				"RecordType" => $RecordType, 
// 				"CustomDataToUpdate" => $CustomDataToUpdate, 
// 				"ContactIds" => $ContactIds, 
// 		])."\n";
// 		return true;
// 	}
// }

abstract class BulkRunner {
	protected $UserId;
	protected $SearchType;
	protected $SearchTerms;
	protected $RecordIds;

	public function __construct($UserId, $SearchType, $SearchTerms, $RecordIds) {
		$this->UserId = $UserId; 
		$this->SearchType = $SearchType; 
		$this->SearchTerms = $SearchTerms; 
		$this->RecordIds = $RecordIds; 
	}

	public function GetContactIds($NumberOfContacts) {
		if ($this->SearchType === 'ContactIds') {
			$Numbers = range(0, 100);
			return array_slice($Numbers, 0, $NumberOfContacts);
		} else {
			throw new Exception("not implemented");
		}
	}

	// null = not chunked
	abstract function GetActionChunkSize();
	abstract function ApplyChanges($RecordIds);

	public function RunAction() {
		$ContactIds = $this->GetContactIds(10);
		$RecordChunks = [];
		$ChunkSize = $this->GetActionChunkSize();
		if ($ChunkSize) {
			$RecordChunks = array_chunk($ContactIds, $ChunkSize);
		} else {
			// null > no chunking 
			$RecordChunks = [$ContactIds];
		}

		foreach($RecordChunks as $Index => $RecordChunk) {
			echo "start sql transaction $Index \n";
			$this->ApplyChanges($RecordChunk);
			echo "end sql transaction $Index  \n";
		}
	}
}

function Main() {
	$User = "Matchell"; 
	$RecordType = "Contacts";
	$CustomDataToUpdate = ['BackgroundInfo' => "bulk updated"];
	$AllContactIds = BulkActions::GetContactIds(10);

	// TODO
	// 1. create curried function
	// 2. pass to runner
}
Main();