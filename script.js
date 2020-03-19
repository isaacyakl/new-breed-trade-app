$(document).ready(function() {
	var itemsWrapper = document.querySelector("#itemsWrapper");
	const maxTradeItems = 15;
	const minTradeItems = 1;
	var addItemBtn = document.querySelector("#addItem");
	var removeItemBtn = document.querySelector("#removeItem");

	// When the add item button is clicked
	addItemBtn.addEventListener("click", () => {
		// If already at max items
		if (itemsWrapper.childElementCount > maxTradeItems - 1) {
			return; // Don't allow adding additional items
		} else if (itemsWrapper.childElementCount == maxTradeItems - 1) {
			// If adding the maxth item
			addItemBtn.disabled = true; // Disable add button
		}

		// Calculate what number the new item will be
		var newItemNum = itemsWrapper.childElementCount + 1;

		// Create row for new trade item(s)
		var newTradeItem = document.createElement("div");
		newTradeItem.classList.add("row");
		newTradeItem.classList.add("tradeItem");
		newTradeItem.id = "tradeItem" + newItemNum;

		// Create col for new trade item(s)
		var dCol = document.createElement("div");
		dCol.classList.add("col");
		newTradeItem.appendChild(dCol); // Add col

		// Create header
		var header = document.createElement("h4");
		header.id = "tradeItemHeader" + newItemNum;
		header.setAttribute("data-toggle", "collapse");
		header.setAttribute("data-target", "#tradeItemForm" + newItemNum);
		header.setAttribute("aria-expanded", "true");
		header.setAttribute("aria-controls", "tradeItemForm" + newItemNum);
		header.classList.add("itemHeader");
		header.innerHTML = "Item " + newItemNum;
		dCol.appendChild(header); // Add header

		// Create a trade item form wrapper
		var newTradeItemForm = document.createElement("div");
		newTradeItemForm.classList.add("collapse");
		newTradeItemForm.classList.add("show");
		newTradeItemForm.classList.add("multi-collapse");
		newTradeItemForm.id = "tradeItemForm" + newItemNum;

		// Make, model, and color
		var makeModelGrp = document.createElement("div");
		makeModelGrp.classList.add("form-group");
		var makeModelLbl = document.createElement("label");
		makeModelLbl.setAttribute("for", "makeModel" + newItemNum);
		makeModelLbl.innerHTML =
			'Make, Model, and Color <span class="text-danger" role="none">*</span>';
		var makeModelInput = document.createElement("input");
		makeModelInput.setAttribute("type", "text");
		makeModelInput.classList.add("form-control");
		makeModelInput.setAttribute("id", "makeModel" + newItemNum);
		makeModelInput.setAttribute("name", "makeModel" + newItemNum);
		makeModelInput.setAttribute("aria-describedby", "makeModelHelp" + newItemNum);
		makeModelInput.setAttribute("placeholder", "E.g. EMPIRE AXE 2.0 Dust Black");
		makeModelInput.setAttribute("minlength", "8");
		makeModelInput.required = true;
		var makeModelHelp = document.createElement("small");
		makeModelHelp.setAttribute("id", "makeModelHelp" + newItemNum);
		makeModelHelp.classList.add("form-text");
		makeModelHelp.classList.add("text-muted");
		makeModelHelp.innerHTML = "";
		makeModelGrp.appendChild(makeModelLbl);
		makeModelGrp.appendChild(makeModelInput);
		makeModelGrp.appendChild(makeModelHelp);
		newTradeItemForm.appendChild(makeModelGrp);

		// Quantity
		var qtyGrp = document.createElement("div");
		qtyGrp.classList.add("form-group");
		var qtyLbl = document.createElement("label");
		qtyLbl.setAttribute("for", "qty" + newItemNum);
		qtyLbl.innerHTML = 'Quantity <span class="text-danger" role="none">*</span>';
		var qtyInput = document.createElement("input");
		qtyInput.setAttribute("type", "number");
		qtyInput.classList.add("form-control");
		qtyInput.setAttribute("id", "qty" + newItemNum);
		qtyInput.setAttribute("name", "qty" + newItemNum);
		qtyInput.setAttribute("aria-describedby", "qtyHelp");
		qtyInput.setAttribute("placeholder", "E.g. 1");
		qtyInput.setAttribute("min", "1");
		qtyInput.required = true;
		var qtyHelp = document.createElement("small");
		qtyHelp.setAttribute("id", "qtyHelp" + newItemNum);
		qtyHelp.classList.add("form-text");
		qtyHelp.classList.add("text-muted");
		qtyHelp.innerHTML = "How many of this item do you have?";
		qtyGrp.appendChild(qtyLbl);
		qtyGrp.appendChild(qtyInput);
		qtyGrp.appendChild(qtyHelp);
		newTradeItemForm.appendChild(qtyGrp);

		// Condition
		var conditionGrp = document.createElement("div");
		conditionGrp.classList.add("form-group");
		var conditionLbl = document.createElement("label");
		conditionLbl.setAttribute("for", "condition" + newItemNum);
		conditionLbl.innerHTML = 'Condition <span class="text-danger" role="none">*</span>';
		var conditionInput = document.createElement("input");
		conditionInput.setAttribute("type", "text");
		conditionInput.classList.add("form-control");
		conditionInput.setAttribute("id", "condition" + newItemNum);
		conditionInput.setAttribute("name", "condition" + newItemNum);
		conditionInput.setAttribute("aria-describedby", "conditionHelp");
		conditionInput.setAttribute("placeholder", "E.g. Used");
		conditionInput.setAttribute("minlength", "4");
		conditionInput.required = true;
		var conditionHelp = document.createElement("small");
		conditionHelp.setAttribute("id", "conditionHelp" + newItemNum);
		conditionHelp.classList.add("form-text");
		conditionHelp.classList.add("text-muted");
		conditionHelp.innerHTML =
			'If used please write "Used" and describe the visual and operational condition of the item(s). If brand new write "Brand New".';
		conditionGrp.appendChild(conditionLbl);
		conditionGrp.appendChild(conditionInput);
		conditionGrp.appendChild(conditionHelp);
		newTradeItemForm.appendChild(conditionGrp);

		// Upgrades and modifications
		var upgradesModsGrp = document.createElement("div");
		upgradesModsGrp.classList.add("form-group");
		var upgradesModsLbl = document.createElement("label");
		upgradesModsLbl.setAttribute("for", "upgradesMods" + newItemNum);
		upgradesModsLbl.innerHTML = "Upgrades / Modifications (If Applicable)";
		var upgradesModsInput = document.createElement("textarea");
		upgradesModsInput.classList.add("form-control");
		upgradesModsInput.setAttribute("id", "upgradesMods" + newItemNum);
		upgradesModsInput.setAttribute("name", "upgradesMods" + newItemNum);
		upgradesModsInput.setAttribute("aria-describedby", "upgradesModsHelp");
		upgradesModsInput.setAttribute("placeholder", "E.g. Reflex Engine");
		var upgradesModsHelp = document.createElement("small");
		upgradesModsHelp.setAttribute("id", "upgradesModsHelp" + newItemNum);
		upgradesModsHelp.classList.add("form-text");
		upgradesModsHelp.classList.add("text-muted");
		upgradesModsHelp.innerHTML = "";
		upgradesModsGrp.appendChild(upgradesModsLbl);
		upgradesModsGrp.appendChild(upgradesModsInput);
		upgradesModsGrp.appendChild(upgradesModsHelp);
		newTradeItemForm.appendChild(upgradesModsGrp);

		// Accessories
		var accessoriesGrp = document.createElement("div");
		accessoriesGrp.classList.add("form-group");
		var accessoriesLbl = document.createElement("label");
		accessoriesLbl.setAttribute("for", "accessories" + newItemNum);
		accessoriesLbl.innerHTML = "Accessories (If Applicable)";
		var accessoriesInput = document.createElement("textarea");
		accessoriesInput.classList.add("form-control");
		accessoriesInput.setAttribute("id", "accessories" + newItemNum);
		accessoriesInput.setAttribute("name", "accessories" + newItemNum);
		accessoriesInput.setAttribute("aria-describedby", "accessoriesHelp");
		accessoriesInput.setAttribute("placeholder", "E.g. Barrel Bag");
		var accessoriesHelp = document.createElement("small");
		accessoriesHelp.setAttribute("id", "accessoriesHelp" + newItemNum);
		accessoriesHelp.classList.add("form-text");
		accessoriesHelp.classList.add("text-muted");
		accessoriesHelp.innerHTML = "What all is included with the item(s)?";
		accessoriesGrp.appendChild(accessoriesLbl);
		accessoriesGrp.appendChild(accessoriesInput);
		accessoriesGrp.appendChild(accessoriesHelp);
		newTradeItemForm.appendChild(accessoriesGrp);

		// Pictures
		var picturesGrp = document.createElement("div");
		picturesGrp.classList.add("form-group");
		var picturesLbl = document.createElement("label");
		picturesLbl.setAttribute("for", "pictures" + newItemNum);
		picturesLbl.innerHTML = 'Pictures <span class="text-danger" role="none">*</span>';
		var picturesInput = document.createElement("input");
		picturesInput.setAttribute("type", "file");
		picturesInput.classList.add("form-control-file");
		picturesInput.setAttribute("id", "pictures" + newItemNum);
		picturesInput.setAttribute("name", "pictures" + newItemNum);
		picturesInput.setAttribute("aria-describedby", "picturesHelp");
		picturesInput.required = true;
		picturesInput.multiple = true;
		var picturesHelp = document.createElement("small");
		picturesHelp.setAttribute("id", "picturesHelp" + newItemNum);
		picturesHelp.classList.add("form-text");
		picturesHelp.classList.add("text-muted");
		picturesHelp.innerHTML =
			"Guns/markers MUST include pictures of the: ASA Threads, Breach, Top Down, Left Side, Right Side, and Barrel";
		picturesGrp.appendChild(picturesLbl);
		picturesGrp.appendChild(picturesInput);
		picturesGrp.appendChild(picturesHelp);
		newTradeItemForm.appendChild(picturesGrp);

		// Video
		var videoGrp = document.createElement("div");
		videoGrp.classList.add("form-group");
		var videoLbl = document.createElement("label");
		videoLbl.setAttribute("for", "video" + newItemNum);
		videoLbl.innerHTML = "Video";
		var videoInput = document.createElement("input");
		videoInput.setAttribute("type", "url");
		videoInput.classList.add("form-control");
		videoInput.setAttribute("id", "video" + newItemNum);
		videoInput.setAttribute("name", "video" + newItemNum);
		videoInput.setAttribute("aria-describedby", "videoHelp");
		videoInput.setAttribute("placeholder", "E.g. https://youtu.be/dQw4w9WgXcQ");
		var videoHelp = document.createElement("small");
		videoHelp.setAttribute("id", "videoHelp" + newItemNum);
		videoHelp.classList.add("form-text");
		videoHelp.classList.add("text-muted");
		videoHelp.innerHTML =
			"If you have a video of your item(s) on YouTube or elsewhere you can put the URL link here.";
		videoGrp.appendChild(videoLbl);
		videoGrp.appendChild(videoInput);
		videoGrp.appendChild(videoHelp);
		newTradeItemForm.appendChild(videoGrp);

		// Add new item to itemsWrapper
		dCol.appendChild(newTradeItemForm);
		itemsWrapper.appendChild(newTradeItem);

		// If more than min items
		if (itemsWrapper.childElementCount > minTradeItems) {
			removeItemBtn.disabled = false; // Enabled remove item button
		}
	});

	// When the remove item button is clicked
	removeItemBtn.addEventListener("click", () => {
		// If there is more than the minimum number of items
		if (itemsWrapper.childElementCount > minTradeItems) {
			itemsWrapper.removeChild(itemsWrapper.lastChild); // Remove last item

			// If after removing the last item the number of items is the minimum
			if (itemsWrapper.childElementCount == minTradeItems) {
				removeItemBtn.disabled = true; // Disable remove item button
			}
		}

		// If not the max number of items
		if (itemsWrapper.childElementCount < maxTradeItems) {
			addItemBtn.disabled = false; // Enable add item button
		}
	});

	// Add one item by default
	addItemBtn.dispatchEvent(new MouseEvent("click", () => {}));

	// Disallow trade submission without a trade item
	document.querySelector("#tradeSubmit").addEventListener("click", e => {
		if (itemsWrapper.childElementCount == 0) {
			e.preventDefault();
			document.querySelector("#tradeSubmit").classList.remove("btn-success");
			document.querySelector("#tradeSubmit").classList.add("btn-danger");
			document.querySelector("#tradeSubmit").innerHTML = "Missing Trade Items";
			setTimeout(() => {
				document.querySelector("#tradeSubmit").classList.remove("btn-danger");
				document.querySelector("#tradeSubmit").classList.add("btn-success");
				document.querySelector("#tradeSubmit").innerHTML = "Submit";
			}, 3500);
			return;
		}

		// Create hidden input for passing number of trade items
		var numTradeItems = document.createElement("input");
		numTradeItems.setAttribute("type", "hidden");
		numTradeItems.setAttribute("id", "numTradeItems");
		numTradeItems.setAttribute("name", "numTradeItems");
		numTradeItems.value = itemsWrapper.childElementCount;
		itemsWrapper.appendChild(numTradeItems);
	});
});
