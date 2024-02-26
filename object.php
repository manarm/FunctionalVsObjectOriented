<?php
abstract class BulkRunner {
	protected $NumberOfContacts;

	public function __construct($NumberOfContacts) {
		$this->NumberOfContacts = $NumberOfContacts;
	}

	public function GetContactIds() {
			$Numbers = range(0, 100);
			return array_slice($Numbers, 0, $this->NumberOfContacts);
	}

	// null = not chunked
	abstract protected function GetActionChunkSize();
	abstract protected function ApplyChanges($RecordIds);

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
	$NumberOfContacts = 10;
}
Main();