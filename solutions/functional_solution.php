<?php
class Contact {
	public static function BulkUpdateCustomData($User, $RecordType, $CustomDataToUpdate, $ContactIds) {
		// Do actual update here.
		echo "Update: ".json_encode([
				'User' => $User, 
				"RecordType" => $RecordType, 
				"CustomDataToUpdate" => $CustomDataToUpdate, 
				"ContactIds" => $ContactIds, 
		])."\n";
		return true;
	}
}

class BulkActions {
	const ACTION_CHUNK_SIZE = ['UpdateFields' => 3];

	public static function GetContactIds($NumberOfContacts) {
		$Numbers = range(0, 100);
		return array_slice($Numbers, 0, $NumberOfContacts);
	}

	// Action function only takes list of record ids/records.
	public static function RunBulkAction($ActionType, $ActionFunction, $Records) {
		if (!in_array($ActionType, array_keys(self::ACTION_CHUNK_SIZE))) {
			throw new Exception("u028efvqlulmwhhf - Bulk action type not recognized: $ActionType");
		}

		$RecordChunks = [];
		if (isset(self::ACTION_CHUNK_SIZE[$ActionType])) {
			$RecordChunks = array_chunk($Records, self::ACTION_CHUNK_SIZE[$ActionType]);
		} else {
			// null > no chunking 
			$RecordChunks = [$Records];
		}

		foreach($RecordChunks as $Index => $RecordChunk) {
			echo "start sql transaction $Index \n";
			$ActionFunction($RecordChunk);
			echo "end sql transaction $Index  \n";
		}
	}
}

function Main() {
	$User = "Matchell"; 
	$RecordType = "Contacts";
	$CustomDataToUpdate = ['BackgroundInfo' => "bulk updated"];
	$AllContactIds = BulkActions::GetContactIds(10);

	// Create curried function
	$RunBulkUpdate = function($ContactIdChunk) use ($User, $RecordType, $CustomDataToUpdate) {
		Contact::BulkUpdateCustomData($User, $RecordType, $CustomDataToUpdate, $ContactIdChunk);
	};
	BulkActions::RunBulkAction("UpdateFields", $RunBulkUpdate, $AllContactIds);
}
Main();