=== WP Foto Vote ===
Requires at least: 3.6
Tested up to: 4.4
Stable tag: 3.6

Just another photo contest plugin. Simple but flexible.

== Description ==

This plugin allows you simply create photo contest in you site.


== Installation ==

http://wp-vote.net/instructions/

== Changelog ==

/* VER 2.2.123 - 08/01/2016 */

- [improvement] FB login fix + added min user age (like 13+,18+,21+)
- [fix] voting bug fix (from 2.2.122)

/* VER 2.2.122 - 18/12/2015 */

- [improvement] Removed parameter 'contest_id' from full contestant url
- [improvement] Chars counter in photo editing form
- [improvement] Allowed html in photo Description & Full description
- [fix] imageLightbox & ajax pagination fix
- [fix] Bug with retrieving user country
- [fix] Compatibility fix with Wp 4.4
- [fix] Removed Twitter shares counter, because Twitter partial disabled this feature
- [improvement] Rewritten Addons part, that allows decrease memory usage for 40%.
- [new] "Agree with rules" addon now integrated into package
- [new] Recoding user "Display size" on voting
- [removed] Removed Share via Email option

/* VER 2.2.120 - 18/11/2015 */

- [new] Social counter
- [new] You can limit upload photo dimensions (like photo must be bigger that 1024 * 768 px)

/* VER 2.2.111 - -/09/2015 */

- [new] A lot little fixes in themes, added Lazy load in Pinterest and Flickr, now grid in this themes generates a lot faster
- [improvement] Rewritten generating thumbnails from BFI_thumb to more stable and faster https://github.com/gambitph/WP-OTF-Regenerate-Thumbnails
- [new] Integrated Jetpack Photon module direct support (now it used by default, if enabled) - https://jetpack.me/support/photon/
- [improvement] Minified JS files
- [fix] Security fixes in `Cookie + Social Login` voting type
- [fix] Fallback, if jQuery(document).ready() not works because of JS errors

/* VER 2.2.110 - 21/09/2015 */

- [new] #2 new voting security types - "IP+cookies+evercookie + Recaptcha" and "cookies+evercookie + Recaptcha"
- [new] #Integrated BFI thumb library (https://github.com/bfintal/bfi_thumb)
- [new] #Added ability change Toolbar background, text and other colors
- [new] #Added ability to user upload from public more than 1(up to 10) photos for one upload action
- [new] #New pagination modes - "Ajax" and "Infinity loading"
- [improvement] Rewritten some moments in "Like" skin
- [new] Added `voting chart` with votes per day for Contest or Photo (in `Analytic` tab)
- [improvement] After upload message shows as overflow in form.

- [fix] In default English messages text removed some mistakes @@Thanks to Richard Hellier
- [fix] Now possible hide warning about "Using cache"
- [fix] Added some responsive styles from upload form
- [fix] Little memory optimization
- [new] Some styles optimization (minimized some styles files)
- [new] Now possible disable addons support for little decrease memory usage
- [new] Added new options for customization Contest list shortcode - `contest block size` and `thumb size`
- [new] Added Alphabetical photos order [A-Z or Z-A]
- [fix] Now possible export max 5000 records
- [fix] Added indexes for some table fields
- [fix] Added fallback in FormBuilder page, if jQuery.ready() not works
- [new] Added debug options for Voting and Upload (save to log information about process)

/* VER 2.2.105f - 21/08/2015 */

- [fix] Fixed issue with Wordpress 4.3 and editing contestant popup
- [fix] Added responsive css to Toolbar
- [fix] Fixed email validation in upload form
- [improvement] Updated Redux Framework

/* VER 2.2.104 - -/06/2015 */

- Some fixes
- [new] Added support Cloudinary.com

/* VER 2.2.103 - 08/06/2015 */

- [improvement] ImageLightbox fix in IE8 and some other
- [improvement] Admin page editing contest rewrite to Bootstrap css and Bootstrap modal
- [improvement] Changes in photo editing form - removed field Additional, added Description, Full description, Social description
- [improvement] Some rewrite Like theme
- [improvement] Into Votes log added field `User ID`
- [fix] Email title - photo deleted
- [fix] Bug with 24hFonce voting type
- [fix] Bug with voting security - users with some mobile browsers can vote more that once
- [new] ** Integrated new Form Builder **
- [new] Ability to create custom upload form styles
- [new] Added detailed environment information for Debug
- [new] Email share ability with ReCaptcha security
- [new] Added button `reset all votes` in contest edit page
- [new] Added toolbar
- [new] Anti fraud system in Beta
- [new] Lazy Load for images  (Not works in Pinterest, Flikr and Fashion theme)
- [new] Cache plugins support (ajax reload votes after cached page loading)
- [new] Added simple login form, if user need be Logged in for upload
- [improvement] Pagination changes: now possible set any photos per page number and some changes to more compatibility for support pagination in posts

/* VER 2.2.101 - 04/04/2015 */

- [improvement] Lightbox closing fixes in mobile
- [improvement] Removed some unused functions
- [improvement] Rewrite some upload form parts for allow place many forms in one page
- [new] Shortcode to show Contest leaders in any page
- [new] Shortcode to show Countdown in any page
- [improvement] Countdown fixes (more correct show leave time)
- [new] Ability use custom countdown
- [new] Addons support
- [new] Into export data added custom upload fields

/* VER 2.2.083 - 01/03/2015 */

- [fix] Little translations fixes and rewrited some JS code
- [new] In translations notify mail body now are textareas with multiline supports
- [improvement] Added css code editor in settings
- [new] Integrated new lightbox and added ability to simply integrate new lightboxes
- [new] Change user capability to manage contest

/* VER 2.2.082 */

- [new] New theme - flickr
- [new] Integrated new WP_image_uploader in admin
- [new] Multi adding photos in admin
- [new] Select Facebook SDK loading position - in head or footer
- [improvement] some responsive styles for vote modal
- [fix] Some problems with voting frequency `24 hours`

/* VER 2.2.081 */

- [new] Rotate images on admin
- [new] Added setting for select delimiter in CSV file
- [improvement] Recoded export data to CSV functions
- [fix] error on deleting photo in `Moderation` page
- [improvement] Removed some old translation JS files
- [new] Popup messages in admin on actions (add, delete, save)

/* VER 2.2.073, 2.2.08 */

- [new] New sharing and vote box
- [new] New pagination styles
- [new] Limit image size on upload photos
- [new] Set, from email send notify to users about photo uploaded, etc (early wordpress sended from `wordpress@domain.com`)
- [new] Email validation with javascript in upload form
- [new] Ability to set different date ranges for upload photos and voting
- [new] More precise designation of dates with hours and minutes
- [new] !Ability to simply create custom themes without plugin code changing, that allows update plugin in future without problems
- [new] Notices in admin, when "vote log" row is deleted
- [fix] Count votes in log when uses filter by contest or photo
- [fix] Upload form shortcode have some troubles
- [fix] When user get error on upload photo, form not reset
- [fix] When selected contest "Security type" as "Default + Facebook Sharing" now shows share not runs automatically,
        for exclude block share window for browser

/* VER 2.2.071 */

- [new] Export contest data into CSV (photos list with emails, names, votes count, etc)
- [new] Upload form shortcode
- [new] Pre or after upload photo moderation
- [improvement] social autorization without ip checks
- [new (test)] Map, with votes by country (filtrable by contest and photo)
- [fix] error on deleting vote from log

/* VER 2.2.06 */

** Fixed bugs
- image resize doe's not work
- problems with upload photo in admin

** Added
- social authorization
- filter in @votes log@ by photo ID
- countdown  timer
- facebook share improvement
- ability, to change notify messages, when photo uploaded, approved, deleted

/* VER 2.2.05 */

** Fixed bugs

** Added
- custom css code in admin
- widget Gallery type
- change lightbox theme in constest settings
- change photos order in constest settings
- new year theme

