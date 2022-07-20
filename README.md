# New Breed Paintball & Airsoft Trade App

This is the code repository for the [New Breed](https://newbreedpb.com) [trade app](https://newbreedpb.com/trade) by [yak](https://isaacyakl.com). The form is built with Boostrap and jQuery, and it supports adjustable limits for number of trade items and pictures. The server-side uses PHP to create an email thread between the customer and trade processor. Pictures, links, and other trade information are embedded into the email receipts.

[Read more...](https://www.isaacyakl.com/work/newbreedpb)

# To-Do

-  Update Item Make Model field title in emails sent to customer and NBPA
-  Add gun example photos or video demonstration of photo angles
-  Switch to JSON for settings instead of extracting from scripts.js
-  Sanitize uploaded file names on client-side after compression before upload
-  Add git hooks
-  Add visual feedback green (checkmarks) for valid input
-  Add Expand All/Collapse All buttons
-  Polyfill/support more browsers with BabelJS
-  Add picture selection(s) thumbnails/removal
-  Internal server submission error should not double number of compressed picture being uploaded and embedded in email (reset uploaded files when an internal error is encountered)
