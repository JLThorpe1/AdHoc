<?php
	// Read data from file and separate into header and records
	$data = file_get_contents("txnlog.dat");
	$header = substr($data, 0, 9);
	$records = substr($data, 9);

	// Store data in array
	$recordArray = array();
	$count = 0;

	// Initialize variables for transaction-related data
	$totalDebits = 0;
	$totalCredits = 0;
	$startedAutopays = 0;
	$endedAutopays = 0;
	$user = "2456938384156277127";
	$userBalance = 0;

	// Generate array to handle field lengths, field names, and format codes for unpacking
//	$fieldInfo = explode("|", "4magic|1version|4numrecN");
	$fieldInfo = explode("|", "1rectypeH|4timestampN|8uidJ");

	// Process records until reaching the end of the data
	do
	{
		// Iterate through each field
		foreach ($fieldInfo as $length)
		{
			$name = preg_replace("/^\d+/", "", $length);
			$length = (int) $length;
			$code = substr($name, -1);

			if ($code == "N" or $code == "J")
			{
				$int = unpack($code . "int", substr($records, 0, $length));
				$recordArray[$count][$name] = $int["int"];
			}
			else if ($code == "H")
			{
				$enum = bin2hex(substr($records, 0, $length));
				$recordArray[$count][$name] = $enum;
			}
			else
			{
				$recordArray[$count][$name] = substr($records, 0, $length);
			}
			$records = substr($records, $length);
		}

		if ($enum == "00" or $enum == "01")
		{
			// Process amount field based on record type value
			$float = unpack("Efloat", substr($records, 0, 8));
			$float = round($float["float"], 2);
			$recordArray[$count]["amount"] = $float;
			$records = substr($records, 8);

			// Determine total amount of debits and credits
			if ($enum == "00")
			{
				$totalDebits += $float;

				// Determine debits for specified user
				if ($recordArray[$count]["uidJ"] == $user)
				{
					$userBalance -= $float;
				}
			}
			else if ($enum == "01")
			{
				$totalCredits += $float;

				// Determine credits for specified user
				if ($recordArray[$count]["uidJ"] == $user)
				{
					$userBalance += $float;
				}
			}
		}
		else if ($enum == "02")
		{
			// Determine how many autopays were started
			$startedAutopays++;
		}
		else if ($enum == "03")
		{
			// Determine how many autopays were ended
			$endedAutopays++;
		}

		$count++;
	}
	while (strlen($records) !== 0);

//	print_r($recordArray);

	// Display results
	echo "What is the total amount in dollars of debits? $" . $totalDebits . "<BR>";
	echo "What is the total amount in dollars of credits? $" . $totalCredits . "<BR>";
	echo "How many autopays were started? " . $startedAutopays . "<BR>";
	echo "How many autopays were ended? " . $endedAutopays . "<BR>";
	echo "What is balance of user ID " . $user . "? $" . $userBalance;
?>