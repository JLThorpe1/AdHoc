<?php
	// Read zips.csv file into array
	if (($handle = fopen("zips.csv", "r")) !== FALSE)
	{
		$zipsCount = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			$zips[$zipsCount]["zipcode"] = $data[0];
			$zips[$zipsCount]["state"] = $data[1];
			$zips[$zipsCount]["county_code"] = $data[2];
			$zips[$zipsCount]["name"] = $data[3];
			$zips[$zipsCount]["rate_area"] = $data[4];
			$zipsCount++;
		}
		fclose($handle);
	}

	// Read plans.csv file into array
	if (($handle = fopen("plans.csv", "r")) !== FALSE)
	{
		$plansCount = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			$plans[$plansCount]["plan_id"] = $data[0];
			$plans[$plansCount]["state"] = $data[1];
			$plans[$plansCount]["metal_level"] = $data[2];
			$plans[$plansCount]["rate"] = $data[3];
			$plans[$plansCount]["rate_area"] = $data[4];
			$plansCount++;
		}
		fclose($handle);
	}

	// Read slcsp.csv file into array
	if (($handle = fopen("slcsp.csv", "r")) !== FALSE)
	{
		$slcspCount = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			$slcsp[$slcspCount]["zipcode"] = $data[0];
			$slcspCount++;
		}
		fclose($handle);
	}

	// Open slcspMODIFIED.csv for writing
	$file = fopen("slcspMODIFIED.csv", "w");
	fputs($file, "zipcode,rate\n");

	// Iterate through $slcsp array (skip header row)
	for ($i = 1; $i < $slcspCount; $i++)
	{
		$line = $slcsp[$i]["zipcode"];

		// Retrieve all rate areas
		$area = array();
		foreach ($zips as $key => $value)
		{
			if ($value["zipcode"] == $slcsp[$i]["zipcode"])
			{
				array_push($area, $value["state"] . $value["rate_area"]);
			}
		}

		//Determine if there is only one rate area
		if (count(array_count_values($area)) == 1)
		{
			// Retrieve all silver plan costs
			$rate = array();
			foreach ($plans as $key => $value)
			{
				if ($value["state"] . $value["rate_area"] == $area[0] and $value["metal_level"] == "Silver")
				{
					array_push($rate, $value["rate"]);
				}
			}

			// Determine second lowest cost silver plan
			if (count($rate) > 0)
			{
				sort($rate, SORT_NUMERIC);
				$rate = array_unique($rate);
				$smallest = array_shift($rate);
				$smallest2nd = array_shift($rate);
				$line .= "," . $smallest2nd;
			}
		}

		$line .= "\n";

		// Write row to slcspMODIFIED.csv
		fputs($file, $line);
	}

	// Close slcspMODIFIED.csv
	fclose($file);
?>