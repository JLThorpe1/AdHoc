<!--
	document.getElementsByTagName("button")[0].onclick = addMember;
	document.getElementsByTagName("button")[1].onclick = submitHousehold;

	// Add household member
	function addMember()
	{
		// Validate form
		if (document.getElementsByName("age")[0].value == "")
		{
			alert("Age is required!");
			return false;
		}
		if (document.getElementsByName("age")[0].value <= 0)
		{
			alert("Age must be greater than 0!");
			return false;
		}
		if (document.getElementsByName("rel")[0].value == "")
		{
			alert("Relationship is required!");
			return false;
		}

		// Put household member form data into an object
		var obj = {"age": document.getElementsByName("age")[0].value, "rel": document.getElementsByName("rel")[0].value, "smoker": document.getElementsByName("smoker")[0].checked};

		// Create list element and add object as property
		var li = document.createElement("li");
		li.formData = obj;

		// Stringify object for display
		var myJSON = JSON.stringify(obj);
		li.appendChild(document.createTextNode(myJSON));

		// Create remove button to remove list element
		var button = document.createElement("button");
		button.innerHTML = "Remove";
		button.addEventListener("click", function() {
			this.parentNode.parentNode.removeChild(this.parentNode);
		});
		li.appendChild(button);

		// Add list element to list
		document.getElementsByTagName("ol")[0].appendChild(li);

		return false;
	}

	// Submit household data
	function submitHousehold()
	{
		var householdArray = [];

		// Iterate through list and push each household member onto array
		var listItem = document.getElementsByTagName("li");
		for (var i = 0; i < listItem.length; i++)
		{
			householdArray.push(listItem[i].formData);
		}

		// Stringify array for form submission and display
		document.getElementsByTagName("pre")[0].innerHTML = JSON.stringify(householdArray);
		document.getElementsByTagName("pre")[0].style.display = "block";

		return false;
	}
//-->