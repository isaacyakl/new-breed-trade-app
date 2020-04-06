// Check if DOM is ready
let formReady = (callback) => {
	if (document.readyState != "loading") callback();
	else document.addEventListener("DOMContentLoaded", callback);
};

// Execute form code when DOM is ready
formReady(() => {
	// Trade form settings
	const maxTradeItems = 15; // Maximum trade items allowed
	const minTradeItems = 1; // Minimum trade items allowed
	const minPicturesPerItem = 2; // Minimum pictures allowed per item
	const maxPicturesPerItem = 10; // Maximum pictures allowed per item
	const maxPictureSizeMB = 1; // in MB, maximum file size per picture
	const maxPictureWidthPX = 1440; // in pixels, maximum picture width
	const msgTimeout = 4000; // in ms, length of time for form error messages to appears

	let tradeFormElement = document.getElementById("tradeForm"); // Create a variable for the trade form
	let tradeFormData = new FormData(); // Create a FormData object for the trade form
	let itemsWrapper = document.getElementById("itemsWrapper"); // Create variable for itemWrapper
	let addItemBtnElement = document.getElementById("addItem"); // Create variable for add item button
	let removeItemBtnElement = document.getElementById("removeItem"); // Create variable for remove item button
	let submitBtnElement = document.getElementById("tradeSubmit"); // Create variable for submit button
	let submitHelpElement = document.getElementById("submitHelp"); // Create a variable for the submit help element
	let submissionResultsElement = document.getElementById("submissionResults"); // Create a variable for the submission results element

	// Function for adding a new trade item
	function addItem() {
		// If already at max items
		if (document.querySelectorAll(".tradeItem").length > maxTradeItems - 1) {
			return; // Don't allow adding additional items
		} else if (document.querySelectorAll(".tradeItem").length == maxTradeItems - 1) {
			// If adding the maxth item
			addItemBtnElement.disabled = true; // Disable add button
		}

		// Calculate what number the new item will be
		let newItemNum = document.querySelectorAll(".tradeItem").length + 1;

		// Create row for new trade item(s)
		let newTradeItemElement = document.createElement("div");
		newTradeItemElement.classList.add("row");
		newTradeItemElement.classList.add("tradeItem");
		newTradeItemElement.id = "tradeItem" + newItemNum;

		// Create col for new trade item(s)
		let dColElement = document.createElement("div");
		dColElement.classList.add("col");
		newTradeItemElement.appendChild(dColElement); // Add col

		// Create header
		let headerElement = document.createElement("h4");
		headerElement.id = "tradeItemHeader" + newItemNum;
		headerElement.setAttribute("data-toggle", "collapse");
		headerElement.setAttribute("data-target", "#tradeItemForm" + newItemNum);
		headerElement.setAttribute("aria-expanded", "true");
		headerElement.setAttribute("aria-controls", "tradeItemForm" + newItemNum);
		headerElement.classList.add("itemHeader");
		headerElement.innerHTML = "Item " + newItemNum;
		dColElement.appendChild(headerElement); // Add header

		// Create a trade item form wrapper
		let newTradeItemFormElement = document.createElement("div");
		newTradeItemFormElement.classList.add("collapse");
		newTradeItemFormElement.classList.add("show");
		newTradeItemFormElement.classList.add("multi-collapse");
		newTradeItemFormElement.id = "tradeItemForm" + newItemNum;

		// Make, model, and color
		let makeModelGrpElement = document.createElement("div");
		makeModelGrpElement.classList.add("form-group");
		let makeModelLblElement = document.createElement("label");
		makeModelLblElement.setAttribute("for", "makeModel" + newItemNum);
		makeModelLblElement.innerHTML =
			'Make, Model, and Color <span class="text-danger" role="none">*</span>';
		let makeModelInputElement = document.createElement("input");
		makeModelInputElement.setAttribute("type", "text");
		makeModelInputElement.classList.add("form-control");
		makeModelInputElement.setAttribute("id", "makeModel" + newItemNum);
		makeModelInputElement.setAttribute("name", "makeModel" + newItemNum);
		makeModelInputElement.setAttribute("aria-describedby", "makeModelHelpElement" + newItemNum);
		makeModelInputElement.setAttribute("placeholder", "E.g. EMPIRE AXE 2.0 Dust Black");
		makeModelInputElement.setAttribute("minlength", "8");
		makeModelInputElement.required = true;
		let makeModelHelpElement = document.createElement("small");
		makeModelHelpElement.setAttribute("id", "makeModelHelpElement" + newItemNum);
		makeModelHelpElement.classList.add("form-text");
		makeModelHelpElement.classList.add("text-muted");
		makeModelHelpElement.innerHTML = "";
		makeModelGrpElement.appendChild(makeModelLblElement);
		makeModelGrpElement.appendChild(makeModelInputElement);
		makeModelGrpElement.appendChild(makeModelHelpElement);
		newTradeItemFormElement.appendChild(makeModelGrpElement);

		// Quantity
		let qtyGrpElement = document.createElement("div");
		qtyGrpElement.classList.add("form-group");
		let qtyLblElement = document.createElement("label");
		qtyLblElement.setAttribute("for", "qty" + newItemNum);
		qtyLblElement.innerHTML = 'Quantity <span class="text-danger" role="none">*</span>';
		let qtyInputElement = document.createElement("input");
		qtyInputElement.setAttribute("type", "number");
		qtyInputElement.classList.add("form-control");
		qtyInputElement.setAttribute("id", "qty" + newItemNum);
		qtyInputElement.setAttribute("name", "qty" + newItemNum);
		qtyInputElement.setAttribute("aria-describedby", "qtyHelpElement");
		qtyInputElement.setAttribute("placeholder", "E.g. 1");
		qtyInputElement.setAttribute("min", "1");
		qtyInputElement.required = true;
		let qtyHelpElement = document.createElement("small");
		qtyHelpElement.setAttribute("id", "qtyHelpElement" + newItemNum);
		qtyHelpElement.classList.add("form-text");
		qtyHelpElement.classList.add("text-muted");
		qtyHelpElement.innerHTML = "How many of this item do you have?";
		qtyGrpElement.appendChild(qtyLblElement);
		qtyGrpElement.appendChild(qtyInputElement);
		qtyGrpElement.appendChild(qtyHelpElement);
		newTradeItemFormElement.appendChild(qtyGrpElement);

		// Condition
		let conditionGrpElement = document.createElement("div");
		conditionGrpElement.classList.add("form-group");
		let conditionLblElement = document.createElement("label");
		conditionLblElement.setAttribute("for", "condition" + newItemNum);
		conditionLblElement.innerHTML = 'Condition <span class="text-danger" role="none">*</span>';
		let conditionInputElement = document.createElement("input");
		conditionInputElement.setAttribute("type", "text");
		conditionInputElement.classList.add("form-control");
		conditionInputElement.setAttribute("id", "condition" + newItemNum);
		conditionInputElement.setAttribute("name", "condition" + newItemNum);
		conditionInputElement.setAttribute("aria-describedby", "conditionHelpElement");
		conditionInputElement.setAttribute("placeholder", "E.g. Used");
		conditionInputElement.setAttribute("minlength", "4");
		conditionInputElement.required = true;
		let conditionHelpElement = document.createElement("small");
		conditionHelpElement.setAttribute("id", "conditionHelpElement" + newItemNum);
		conditionHelpElement.classList.add("form-text");
		conditionHelpElement.classList.add("text-muted");
		conditionHelpElement.innerHTML =
			'If used please write "Used" and describe the visual and operational condition of the item(s). If brand new write "Brand New".';
		conditionGrpElement.appendChild(conditionLblElement);
		conditionGrpElement.appendChild(conditionInputElement);
		conditionGrpElement.appendChild(conditionHelpElement);
		newTradeItemFormElement.appendChild(conditionGrpElement);

		// Upgrades and modifications
		let upgradesModsGrpElement = document.createElement("div");
		upgradesModsGrpElement.classList.add("form-group");
		let upgradesModsLblElement = document.createElement("label");
		upgradesModsLblElement.setAttribute("for", "upgradesMods" + newItemNum);
		upgradesModsLblElement.innerHTML = "Upgrades / Modifications (If Applicable)";
		let upgradesModsInputElement = document.createElement("textarea");
		upgradesModsInputElement.classList.add("form-control");
		upgradesModsInputElement.setAttribute("id", "upgradesMods" + newItemNum);
		upgradesModsInputElement.setAttribute("name", "upgradesMods" + newItemNum);
		upgradesModsInputElement.setAttribute("aria-describedby", "upgradesModsHelpElement");
		upgradesModsInputElement.setAttribute("placeholder", "E.g. Reflex Engine");
		let upgradesModsHelpElement = document.createElement("small");
		upgradesModsHelpElement.setAttribute("id", "upgradesModsHelpElement" + newItemNum);
		upgradesModsHelpElement.classList.add("form-text");
		upgradesModsHelpElement.classList.add("text-muted");
		upgradesModsHelpElement.innerHTML = "";
		upgradesModsGrpElement.appendChild(upgradesModsLblElement);
		upgradesModsGrpElement.appendChild(upgradesModsInputElement);
		upgradesModsGrpElement.appendChild(upgradesModsHelpElement);
		newTradeItemFormElement.appendChild(upgradesModsGrpElement);

		// Accessories
		let accessoriesGrpElement = document.createElement("div");
		accessoriesGrpElement.classList.add("form-group");
		let accessoriesLblElement = document.createElement("label");
		accessoriesLblElement.setAttribute("for", "accessories" + newItemNum);
		accessoriesLblElement.innerHTML = "Accessories (If Applicable)";
		let accessoriesInputElement = document.createElement("textarea");
		accessoriesInputElement.classList.add("form-control");
		accessoriesInputElement.setAttribute("id", "accessories" + newItemNum);
		accessoriesInputElement.setAttribute("name", "accessories" + newItemNum);
		accessoriesInputElement.setAttribute("aria-describedby", "accessoriesHelpElement");
		accessoriesInputElement.setAttribute("placeholder", "E.g. Barrel Bag");
		let accessoriesHelpElement = document.createElement("small");
		accessoriesHelpElement.setAttribute("id", "accessoriesHelpElement" + newItemNum);
		accessoriesHelpElement.classList.add("form-text");
		accessoriesHelpElement.classList.add("text-muted");
		accessoriesHelpElement.innerHTML = "What all is included with the item(s)?";
		accessoriesGrpElement.appendChild(accessoriesLblElement);
		accessoriesGrpElement.appendChild(accessoriesInputElement);
		accessoriesGrpElement.appendChild(accessoriesHelpElement);
		newTradeItemFormElement.appendChild(accessoriesGrpElement);

		// Pictures
		let picturesGrpElement = document.createElement("div");
		picturesGrpElement.classList.add("form-group");
		let picturesLblElement = document.createElement("label");
		picturesLblElement.setAttribute("for", "pictures" + newItemNum + "[]");
		picturesLblElement.innerHTML =
			minPicturesPerItem == 0
				? "Pictures"
				: 'Pictures <span class="text-danger" role="none">*</span>';
		let picturesInputElement = document.createElement("input");
		picturesInputElement.setAttribute("type", "file");
		picturesInputElement.setAttribute("accept", "image/png, image/jpeg");
		picturesInputElement.classList.add("form-control-file");
		picturesInputElement.setAttribute("id", "pictures" + newItemNum);
		picturesInputElement.setAttribute("name", "pictures" + newItemNum + "[]"); // Square brackets need for multiple file upload handling on backend
		picturesInputElement.setAttribute("aria-describedby", "picturesHelpElement");
		// If pictures are not required
		if (minPicturesPerItem != 0) {
			picturesInputElement.required = true;
		}
		picturesInputElement.multiple = true;
		let picturesHelpElement = document.createElement("small");
		picturesHelpElement.setAttribute("id", "picturesHelpElement" + newItemNum);
		picturesHelpElement.classList.add("form-text");
		picturesHelpElement.classList.add("text-muted");
		picturesHelpElement.innerHTML = `Provide ${minPicturesPerItem}-${maxPicturesPerItem} pictures.`;

		// Verify the number of pictures selected is valid
		picturesInputElement.addEventListener("input", () => {
			// If the number of selected pictures is more than the max allowed
			if (picturesInputElement.files.length > maxPicturesPerItem) {
				// Let the user know
				triggerMsg(picturesHelpElement, "Too many pictures selected.", "warning", msgTimeout);
				picturesInputElement.value = ""; // Clear the selection
			}
			// If the number of selected pictures is less than the min allowed
			else if (picturesInputElement.files.length < minPicturesPerItem) {
				// Let the user know
				triggerMsg(picturesHelpElement, `Select more pictures.`, "warning", msgTimeout);
				picturesInputElement.value = ""; // Clear the selection
			}
		});
		picturesGrpElement.appendChild(picturesLblElement);
		picturesGrpElement.appendChild(picturesInputElement);
		picturesGrpElement.appendChild(picturesHelpElement);
		newTradeItemFormElement.appendChild(picturesGrpElement);

		// Video
		let videoGrpElement = document.createElement("div");
		videoGrpElement.classList.add("form-group");
		let videoLblElement = document.createElement("label");
		videoLblElement.setAttribute("for", "video" + newItemNum);
		videoLblElement.innerHTML = "Video";
		let videoInputElement = document.createElement("input");
		videoInputElement.setAttribute("type", "url");
		videoInputElement.classList.add("form-control");
		videoInputElement.setAttribute("id", "video" + newItemNum);
		videoInputElement.setAttribute("name", "video" + newItemNum);
		videoInputElement.setAttribute("aria-describedby", "videoHelpElement");
		videoInputElement.setAttribute("placeholder", "E.g. https://youtu.be/dQw4w9WgXcQ");
		let videoHelpElement = document.createElement("small");
		videoHelpElement.setAttribute("id", "videoHelpElement" + newItemNum);
		videoHelpElement.classList.add("form-text");
		videoHelpElement.classList.add("text-muted");
		videoHelpElement.innerHTML =
			"If you have a video of your item(s) on YouTube or elsewhere you can put the URL link here.";
		videoGrpElement.appendChild(videoLblElement);
		videoGrpElement.appendChild(videoInputElement);
		videoGrpElement.appendChild(videoHelpElement);
		newTradeItemFormElement.appendChild(videoGrpElement);

		// Add new item to itemsWrapper
		dColElement.appendChild(newTradeItemFormElement);
		itemsWrapper.appendChild(newTradeItemElement);

		// If more than min items
		if (document.querySelectorAll(".tradeItem").length > minTradeItems) {
			removeItemBtnElement.disabled = false; // Enabled remove item button
		}
	}

	// Function for removing a trade item
	function removeItem() {
		// If there is more than the minimum number of items
		if (document.querySelectorAll(".tradeItem").length > minTradeItems) {
			itemsWrapper.removeChild(itemsWrapper.lastChild); // Remove last item

			// If after removing the last item the number of items is the minimum
			if (document.querySelectorAll(".tradeItem").length == minTradeItems) {
				removeItemBtnElement.disabled = true; // Disable remove item button
			}
		}

		// If not the max number of items
		if (document.querySelectorAll(".tradeItem").length < maxTradeItems) {
			addItemBtnElement.disabled = false; // Enable add item button
		}
	}

	// Function for sending trade
	// Use after calling validateTrade())
	function sendTrade() {
		// Clear submission results in case previous submissions failed
		submissionResultsElement.innerHTML = "";

		// Add number of trade items
		tradeFormData.append("numTradeItems", document.querySelectorAll(".tradeItem").length);

		// Setup a new request
		let request = new XMLHttpRequest();
		request.open("POST", "trade.php");
		request.send(tradeFormData);

		// When the trade successfully submits
		request.onload = () => {
			// Update and hide submit button
			submitBtnElement.disabled = true;
			submitBtnElement.innerHTML = "Submitted!";
			submitBtnElement.classList.add("btn-success");
			submitBtnElement.classList.remove("btn-primary");

			// Hide submit section
			document.getElementById("submitSection").style.display = "none";

			submissionResultsElement.innerHTML = request.response;
		};

		// Only triggers if the request couldn't be made at all
		request.onerror = () => {
			triggerMsg(
				submitHelpElement,
				"Failed to send trade. Please check your internet connection and try again.",
				"warning",
				msgTimeout
			);

			// Re-enable submit button
			submitBtnElement.innerHTML = "Submit";
			submitBtnElement.classList.add("btn-success");
			submitBtnElement.classList.remove("btn-primary");
			submitBtnElement.disabled = false;

			// Redisplay the form sections after msgTimeout
			setTimeout(() => {
				// Re-display form sections
				document.getElementById("introSection").style.display = "initial";
				document.getElementById("personalInfoSection").style.display = "initial";
				document.getElementById("itemsSection").style.display = "initial";
				document.getElementById("tradeOptionsSection").style.display = "initial";
			}, msgTimeout);
		};
	}

	// Function for updating input labels
	// element <object>: html element
	// msg <string>: message
	// type <string>: "normal"(default),"warning", "pending", "success"
	// timeout <int>: time in milliseconds, 0 means do not remove msg
	// postAction <function>: post-timeout function to execute
	function triggerMsg(element, msg, type, timeout, postAction) {
		// Store original msg
		let originalMsg;

		// If the element has no content
		if (element.innerHTML === undefined) {
			originalMsg = ""; // Set the original message to be blank
		} else {
			originalMsg = element.innerHTML; // Store the original contents
		}

		// Style based on msg type
		if (type == "warning") {
			element.classList.remove("text-muted");
			element.classList.add("text-danger");
		} else if (type == "pending") {
			element.classList.remove("text-muted");
			element.classList.add("text-primary");
		} else if (type == "success") {
			element.classList.remove("text-muted");
			element.classList.add("text-success");
		}

		// Set msg
		element.innerHTML = msg;

		function execPostAction() {
			// If a postAction was provided
			if (typeof postAction === "function") {
				// Else execute postAction function argument
				postAction();
			}
		}

		// If a timeout is not 0
		if (timeout != 0) {
			// Wait for requested time
			setTimeout(() => {
				// Style reversion based on msg type
				if (type == "warning") {
					element.classList.remove("text-danger");
					element.classList.add("text-muted");
				} else if (type == "pending") {
					element.classList.remove("text-primary");
					element.classList.add("text-muted");
				} else if (type == "success") {
					element.classList.remove("text-success");
					element.classList.add("text-muted");
				}

				// Revert to original message
				element.innerHTML = originalMsg;

				// Execute post action
				execPostAction();
			}, timeout);
		} else {
			// Execute post action
			execPostAction();
		}
	}

	// Function for validating trade
	// returns (bool)
	async function validateTrade() {
		// If the trade items are less than the min allowed
		if (document.querySelectorAll(".tradeItem").length < minTradeItems) {
			triggerMsg(submitHelpElement, "Not enough trade items!", "warning", msgTimeout);
			return false; // Return trade is not valid
		} // Else if the trade items are more than the max allowed
		else if (document.querySelectorAll(".tradeItem").length > maxTradeItems) {
			triggerMsg(submitHelpElement, "Too many trade items!", "warning", msgTimeout);
			return false; // Return trade is not valid
		}

		// Compress pictures for uploading
		// Makes use of: https://www.npmjs.com/package/browser-image-compression

		let totalSizeOfSelectedPictures = 0.0; // Variable to hold total file size of all selected pictures
		let totalSizeOfCompressedPictures = 0.0; // Variable to hold total file size of all compressed pictures

		// Create array for all file input elements
		let fileInputs = Array.from(document.querySelectorAll("input[type=file]"));

		// Iterate through file input elements
		for (let i = 0; i < fileInputs.length; i++) {
			// Create a file array from current input
			let currentInputFiles = Array.from(fileInputs[i].files);

			// Iterate through files in current input element
			for (let f = 0; f < currentInputFiles.length; f++) {
				// Add each uncompressed file size to total file size
				totalSizeOfSelectedPictures += currentInputFiles[f].size;

				// Configure compression options per file
				const options = {
					maxSizeMB: maxPictureSizeMB, // Use max size specified above
					maxWidthOrHeight: maxPictureWidthPX, // Use max width specified above
					useWebWorker: false, // Use main thread as UI/form input is already disabled
					onProgress: (p) =>
						triggerMsg(
							submitHelpElement,
							`Compressing ${currentInputFiles[f].name} (${p}%)`,
							"pending",
							0
						),
				};

				try {
					// Compress current picture
					const compressedPictureBlob = await imageCompression(currentInputFiles[f], options);

					// Add compressed blob to form data
					tradeFormData.append(
						`compressedPictures${i + 1}[]`,
						compressedPictureBlob,
						compressedPictureBlob.name
					);

					// Add compressed file size to total compressed file size
					totalSizeOfCompressedPictures += compressedPictureBlob.size;
				} catch (error) {
					// Alert user if compression failed for this picture
					triggerMsg(
						submitHelpElement,
						`Failed to compress ${currentInputFiles[f].name}. Please remove it or try again.`,
						"warning",
						msgTimeout
					);

					// Clear current input file selection
					fileInputs[i].value = "";

					// Delete unused blobs of compressed pictures
					for (let d = 0; d < fileInputs.length; d++) {
						tradeFormData.delete(`compressedPictures${d + 1}[]`);
					}

					console.log(error);

					// End validation & compression so user can select a different file.
					return false;
				}
			}
		}

		// Alert compression done
		triggerMsg(
			submitHelpElement,
			`Compression finished and saved ${(
				(totalSizeOfSelectedPictures - totalSizeOfCompressedPictures) /
				1024 /
				1024
			).toFixed(2)} MBs. New total file upload size: ${(
				totalSizeOfCompressedPictures /
				1024 /
				1024
			).toFixed(2)} MBs.`,
			"success",
			0
		);
		console.log(
			`Compression finished and saved ${(
				(totalSizeOfSelectedPictures - totalSizeOfCompressedPictures) /
				1024 /
				1024
			).toFixed(2)} MBs. New total file upload size: ${(
				totalSizeOfCompressedPictures /
				1024 /
				1024
			).toFixed(2)} MBs.`
		);

		// Make sure the total compressed files' size is less than 25 MBs (max allowable size of email attachments)
		if ((totalSizeOfCompressedPictures / 1024 / 1024).toFixed(2) > 25) {
			// Alert user
			triggerMsg(
				submitHelpElement,
				`Total selected picture size of ${(totalSizeOfCompressedPictures / 1024 / 1024).toFixed(
					2
				)} MBs is too large. Please remove one and try again.`,
				"warning",
				msgTimeout
			);

			// Delete unused blobs of compressed pictures
			for (let d = 0; d < fileInputs.length; d++) {
				tradeFormData.delete(`compressedPictures${d + 1}[]`);
			}

			// Return trade is invalid
			return false;
		}

		// Return trade is valid
		return true;
	}

	// When the add item button is clicked
	addItemBtnElement.addEventListener("click", () => {
		// Add item
		addItem();
	});

	// When the remove item button is clicked
	removeItemBtnElement.addEventListener("click", () => {
		// Remove item
		removeItem();
	});

	// Add submission event listener
	tradeFormElement.addEventListener("submit", (e) => {
		// Let the user know we are submitting the form
		submitBtnElement.innerHTML = "Validating...";
		submitBtnElement.classList.add("btn-primary");
		submitBtnElement.classList.remove("btn-success");

		// Disable submit button
		submitBtnElement.disabled = true;

		// Hide form sections
		document.getElementById("introSection").style.display = "none";
		document.getElementById("personalInfoSection").style.display = "none";
		document.getElementById("itemsSection").style.display = "none";
		document.getElementById("tradeOptionsSection").style.display = "none";

		// Validate the trade
		validateTrade()
			.then((validationResult) => {
				// If the trade validated
				if (validationResult) {
					// Update button text to denote sending trade
					submitBtnElement.innerHTML = "Sending...";

					// Send trade
					sendTrade();
				}
				// The trade was invalid
				else {
					// Re-enable submit button
					submitBtnElement.innerHTML = "Submit";
					submitBtnElement.classList.add("btn-success");
					submitBtnElement.classList.remove("btn-primary");
					submitBtnElement.disabled = false;

					// Redisplay the form sections after msgTimeout
					setTimeout(() => {
						// Re-display form sections
						document.getElementById("introSection").style.display = "initial";
						document.getElementById("personalInfoSection").style.display = "initial";
						document.getElementById("itemsSection").style.display = "initial";
						document.getElementById("tradeOptionsSection").style.display = "initial";
					}, msgTimeout);
				}
			})
			.catch((error) => {
				console.log(error);
			});

		// Prevent default submission
		e.preventDefault();
	});

	// Listen and capture invalid events within the trade form
	tradeFormElement.addEventListener(
		"invalid",
		() => {
			// Expand all tradeItems so that validation can scroll to missing input
			document.querySelectorAll(".collapse").forEach((tI) => {
				tI.classList.add("show");
			});
		},
		true
	);

	// Add one item by default
	addItem();

	// Display form
	tradeFormElement.style.display = "initial";
});
