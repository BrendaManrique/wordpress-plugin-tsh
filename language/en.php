<?php
/*
 * Please note - if you are translating this file, that any quotes that have a backslash (\") need that
 * back slash to escape the extra quotes in the string.
 * Any variables in this file need to stay in the same format.
 *
 * If you have any questions at all about this file, please contact me through my CodeCanyon profile.
 * http://codecanyon.net/user/Luminary
 */

// All Pages - Globals
// --------------------------------------------------------------------------------------------------
$curSym		 				= "$";
$accessErrorHeader			= "Access Error";
$permissionDenied			= "Permission Denied. You can not access this page.";
$pageNotFoundHeader			= "Page Not Found";
$loggedInAsMsg				= "Logged in as";
$htmlNotAllowed				= "HTML not allowed &amp; will be saved as plain text.";
$max50Characs				= "Max 50 Characters.";
$hoursMinsSecsTooltip		= "Hours:Minutes:Seconds";
$dateFormatHelp				= "Format: YYYY-MM-DD";
$timeFormatHelp				= "hh:mm:ss";
$timeFormatHelp1			= "Format: HH:MM";
$logoutConfirmationMsg		= ", are you sure you want to signout?";
$signOutConfBtn				= "Sign Out";

$cancelBtn					= "Cancel";
$closeBtn					= "Close";
$okBtn						= "OK";
$yesBtn						= "Yes";
$noBtn						= "No";
$saveChangesBtn				= "Save Changes";
$saveBtn					= "Save";
$deleteBtn					= "Delete";
$updateBtn					= "Update";
$selectOption				= "Select...";
$reqField					= "*";

$emailLoginLink				= "You can log in to your account at ".$set['installUrl'];
$emailThankYou				= "Thank you,<br>".$set['siteName'];

// Calendar Include File
// --------------------------------------------------------------------------------------------------
$todayLink					= "Today";
$newEventLink				= "New Event";
$monthLink					= "Month";
$weekLink					= "Week";
$dayLink					= "Day";
$noTimesSet					= "No times have been set";
$sharedEvent				= "Shared Event";
$pulicEvent					= "Public Event";
$eventPostedBy				= "Posted By";
$editEvent					= "Edit";
$deleteEvent				= "Delete";

// Header Include
// --------------------------------------------------------------------------------------------------
$todayIsQuip				= "Today is";

// Navigation Include
// --------------------------------------------------------------------------------------------------
$toggleNavQuip				= "Toggle Navigation";
$dashboardNav				= "Dashboard";
$calendarNav				= "Calendar";
$myTimeNav					= "My Time";
$tasksNav					= "Tasks";
$messagesNav				= "Messages";
$employeesNav				= "Employees";
$listEmpNav					= "List Employees";
$activeEmpNav				= "Active Employees";
$inactiveEmpNav				= "Inactive Employees";
$newEmpNav					= "New Employee";
$manageNav					= "Manage";
$siteNotNav					= "Site Notifications";
$busDocsNav					= "Business Documents";
$reportsNav					= "Reports";
$timeCardsNav				= "Time Cards";
$siteSettingsNav			= "Site Settings";
$searchPlaceholder			= "Single Value Search";
$hireDateText				= "Hire Date";
$myProfileNav				= "My Profile";
$signOutNav					= "Sign Out";
$signOutConf				= ", are you sure you want to signout of your account?";

// Footer Include
// --------------------------------------------------------------------------------------------------
$footerText1				= "&copy; 2014";
$footerText2				= "Employee Management &amp; Time Clock";
$footerText3				= "Created by <a href=\"http://codecanyon.net/user/Luminary?ref=Luminary\">Luminary on CodeCanyon</a>.";

// Login
// --------------------------------------------------------------------------------------------------
$loginPageTitle				= "Employee Sign In";
$emailAddyField				= "Email Address";
$passwordField				= "Password";
$resetPasswordLink			= "Reset Password";
$signInNav					= "Sign In";
$resetPasswordModal			= "Reset Your Account Password";
$accountEmailAddyField		= "Account Email";
$accountEmailAddyFieldHelp	= "The Email Address associated with your account.";
$accountEmailAddyFieldHelp2 = "The Employee's Email Address is also used as their account login.";
$passwordReset1				= "Your Password has been reset";
$passwordReset2				= "Please check your email for your new password, and instructions on how to update your account.";
$accountEmailReq			= "Your Account Email Address is required.";
$accountPasswordReq			= "Your Account Password is required.";
$loginFailedMsg				= "Log in failed, Please check your entries.";
$inactiveAccountMsg			= "Your account is inactive, and you can not log in.";
$noAccountFoundMsg			= "Account not found for that email address.";
$resetPassEmailSubject		= "Your ".$set['siteName']." Password has been Reset";
$resetPassEmail1			= "Your temporary password is:";
$resetPassEmail2			= "Please take the time to change your password to something you can easily remember. You can change your
password on your My Profile page after logging into your account. There you can update your password, as well as your account details.";
$resetPassEmail3			= "You can log into your account with your email address and new password at: ".$set['installUrl'];
$passwordResetMsg			= "Your Password has been reset.";

// index.php
// --------------------------------------------------------------------------------------------------
$myProfilePage				= "My Profile";
$myCalendarPage				= "My Calendar";
$myTasksPage				= "My Tasks";
$closedTasksPage			= "Closed/Completed Task";
$newTaskPage				= "Add a New Task";
$viewTaskPage				= "View Task";
$inboxPage					= "Inbox";
$sentPage					= "Sent Messages";
$archivePage				= "Archived Messages";
$composePage				= "Compose a Private Message";
$viewMsgPage				= "View Private Message";
$myTimePage					= "My Time Logs";
$viewTimePage				= "View/Edit Time Record";
$activeEmpPage				= "Active Employees";
$inactiveEmpPage			= "Inactive Employees";
$newEmpPage					= "Add a New Employee";
$viewEmpPage				= "View Employee Account";
$viewTimeCardsPage			= "All Time Cards";
$reportsPage				= "Reports";
$empReportPage				= "Employee Report";
$empTimeReportPage			= "Employee Time Report";
$siteNotPage				= "Site Notifications";
$newNoticePage				= "Add a New Site Notification";
$empTimeCardsPage			= "Employee Time Cards";
$searchResPage				= "Search Results";
$documentsPage				= "Business Documents";
$newDocPage					= "Upload a New Business Document";
$viewDocPage				= "View Business Document";
$settingsPage				= "Global Site Settings";
$importPage					= "Import Data from TimeZone v.1";
$dashboardPage				= "Dashboard";
$pageError1					= "404 Error";
$pageError2					= "Not Found";
$pageError3					= "The page might have been removed, had its name changed, or is temporarily unavailable.";

// Dashboard
// --------------------------------------------------------------------------------------------------
$messagesBox1				= "You Have";
$messagesBox2				= "unread message";
$messagesBox3				= "unread messages";
$viewMessagesTooltip		= "View Messages";
$timeBox1					= "You have worked";
$timeBox2					= "this week.";
$clockBox					= "You are currently";
$recentTasksTitle			= "Recent Open Tasks";
$dateDueField				= "Date Due";
$noRecentTasksFound			= "No Recent Open Tasks Found";
$recentMsgsTitle			= "Recent Messages Received";
$viewMsgTooltip				= "View Message";
$rcvdFromField				= "Received From";
$dateRcvdField				= "Date Received";
$noRecentMsgFound			= "No Recent Private Messages Found";
$postedByField				= "Posted By";

// Active Employees
// --------------------------------------------------------------------------------------------------
$empName					= "Employee Name";
$emailField					= "Email";
$positionField				= "Position";
$hireDateField				= "Date of Hire";
$hireDateFieldHelp			= "The Date the Employee will start work. Format: YYYY-MM-DD";
$accountTypeField			= "Account Type";
$lastLoginField				= "Last Login";
$actionText					= "Actions";
$viewEmpTooltip				= "View Employee Account";
$notAvailTooltip			= "Not Available";
$noDeactivateTooltip		= "You cannot Deactivate the Primary Admin Account";
$deactivateAccTooltip		= "Deactivate Employee Account";
$deactivateAccConf1			= "Are you sure you want to Deactivate the Account for:";
$deactivateAccConf2			= "Inactive Employees can NOT log into their accounts.";
$accDeactivatedConf			= "The Employees Account has been Deactivated.";

// Inactive Employees
// --------------------------------------------------------------------------------------------------
$noInactEmpFound			= "No Inactive Employee Accounts found.";
$reactEmpAcctTooltip		= "Reactivate Employee Account";
$reactivateEmpConf			= "Are you sure you want to Reactivate the Account for:";
$deleteAccountConf			= "Are you sure you want to DELETE the Account for:";
$deleteAcctBtn				= "DELETE Employee Account";
$empReactivedMsg			= "The Employees Account has been Reactivated.";
$empDeletedMsg				= "The Employee Account has been deleted.";

// New Employee
// --------------------------------------------------------------------------------------------------
$generatePassTooltip		= "Generate Password";
$showPlainText				= "Show Plain Text";
$hidePlainText				= "Hide Plain Text";
$repeatAccPassField			= "Repeat Account Password";
$repeatAccPassFieldHelp		= "Repeat the New Password again. Passwords MUST Match.";
$adminAccField				= "Administrator Account?";
$adminAccFieldHelp			= "Admins have full read/write/update and Delete permissions.";
$addNewEmpBtn				= "Add New Employee";
$empFirstNameReq			= "The Employee's First Name is required.";
$empLastNameReq				= "The Employee's Last Name is required.";
$dateOfHireReq				= "The Employee's Date of Hire is required.";
$empEmailReq				= "The Employee's Account Email of Hire is required.";
$empAccPassReq				= "A New Account Password is required.";
$empPhoneReq				= "The Employee's Primary Contact Phone Number is required.";
$empMailingAddyReq			= "The Employee's Mailing Address is required.";
$acctExistsMsg				= "There is all ready an account registered with that Email Address.";
$empAcctCreatedMsg			= "The New Employee Account has been created.";

// Inbox
// --------------------------------------------------------------------------------------------------
$inboxEmptyMsg				= "Your Inbox is empty.";
$sendReplyMsgModal			= "Send a Reply Message";
$sendReplyBtn				= "Send Reply";
$selectPrivMsgQuip			= "Select a Private Message to view the message content &amp; options.";
$msgReadMsg					= "The Message has been marked as read.";
$msgArchivedMsg				= "The Message has been Archived.";
$replyMsgEmailSubject		= "You have received a Reply Message from";
$replyMsgSent				= "Your Reply Message has been sent.";

// Sent Messages
// --------------------------------------------------------------------------------------------------
$emptySentMsg				= "Your Sent Messages is empty.";
$dateSentField				= "Date Sent";
$selectSentMsgQuip			= "Select a Sent Message to view the message content &amp; options.";
$sentMsgDeletedMsg			= "The Sent Message has been deleted.";

// Archived Messages
// --------------------------------------------------------------------------------------------------
$sentNavLink				= "Sent";
$archiveNavLink				= "Archive";
$composeNavLink				= "Compose";
$emptyArchives				= "Your Archives are empty.";
$fromField					= "From";
$subjectField				= "Subject";
$dateRcvdField				= "Date Received";
$deleteMsgConf				= "Are you sure you want to DELETE the message:";
$youText					= "You";
$selectMsgQuip				= "Select an Archived Message to view the message content &amp; options.";
$archivedToInboxMsg			= "The Archived Message has been placed in your Inbox.";
$msgDeletedMsg				= "The Message has been deleted.";

// View Message
// --------------------------------------------------------------------------------------------------
$markAsReadBtn				= "Mark as Read";

// Compose Message
// --------------------------------------------------------------------------------------------------
$recipientField				= "Recipient";
$recipientFieldHelp			= "Select the Employee to send the Message to.";
$messageField				= "Message";
$sendMsgBtn					= "Send Message";
$recipientReq				= "You need to select a Recipient.";
$msgSubjectReq				= "The Message Subject is required.";
$msgReq						= "The Message Content is required.";
$newMsgEmailSubject			= "You have received a new Personal Message from";
$msgSentMsg					= "The Personal Message has been sent.";
$msgErrorMsg				= "There was an error, and the email could not be sent at this time.";

// Business Documents
// --------------------------------------------------------------------------------------------------
$busDocsNavLink				= "Business Documents";
$uplNewDocNavLink			= "Upload a New Document";
$noUploadsFound				= "No Uploaded Documents Found.";
$DocNameField				= "Document Name";
$descField					= "Description";
$dateUplField				= "Date Uploaded";
$uploadedByField			= "Uploaded By";
$viewDocTooltip				= "View Document";
$deleteDocTooltip			= "Delete Document";
$deleteDocConf				= "Are you sure you want to DELETE the document:";
$docDeletedMsg				= "The Business Document has been deleted.";
$deleteErrorMsg				= "An error was encountered, and the Business Document could not be deleted.";

// New Document
// --------------------------------------------------------------------------------------------------
$maxUploadText				= "Max Upload File Size:";
$mbText						= "mb";
$docTitleField				= "Document Title";
$docDescField				= "Document Description";
$uploadDocBtn				= "Upload Document";
$docTitleReq				= "The Document Title is required.";
$docDescReq					= "The Document Description is required.";
$docFileReq					= "Please select the Document to upload.";
$invalidDocTypeMsg			= "The Document is not an approved type to be uploaded.";
$docUplMsg					= "The Business Document has been uploaded.";

// View Document
// --------------------------------------------------------------------------------------------------
$docTitleField				= "Title";
$editDocDescBtn				= "Edit Document Description";
$noPrevAvail				= "No preview available for";
$downloadText				= "Download";
$editDocDescModal			= "Edit Document Description";
$docDescUpdt				= "The Documents Description has been updated.";

// Calendar
// --------------------------------------------------------------------------------------------------
$calendarQuip				= "Click on an Event for more information & options.";
$editEventModal				= "Edit Event";
$startDateField				= "Start Date";
$startTimeField				= "Start Time";
$startTimeFieldHelp			= "Format: HH:MM or leave blank for an All Day event.";
$endDateField				= "End Date";
$endTimeField				= "End Time";
$eventTitleField			= "Event Title";
$eventDescField				= "Event Description";
$eventDescFieldHelp			= "Description of the Event.";
$deleteEventConf			= "Are you sure you want to DELETE the Calendar Event:";
$newEventModal				= "Add a New Calendar Event";
$publicEventField			= "Public Event?";
$publicEventFieldHelp		= "Public Events appear on all Managers, Admins &amp; Employees calendars.";
$shareEventField			= "Share Event with all Managers &amp; Admins?";
$shareEventFieldHelp		= "Sharing an Event makes it visible on all Managers &amp; Admins calendars.";
$saveNewEvtBtn				= "Save New Event";
$startDateReq				= "The Calendar Event Start Date is required.";
$endDateReq					= "The Calendar Event End Date is required.";
$eventTitleReq				= "The Calendar Event Title is required.";
$newEventSavedMsg			= "The New Calendar Event has been saved.";
$eventUpdMsg				= "The Calendar Event has been updated.";
$eventDeletedMsg			= "The Calendar Event has been deleted.";
$eventDeleteError			= "An Error was encountered, and the Calendar Event could not be deleted.";

// Open Tasks
// --------------------------------------------------------------------------------------------------
$noOpenTasksFound			= "No Open Tasks found.";
$markTaskCmpTooltip			= "Mark Task Completed";
$completeTaskText			= "Complete Task:";
$taskMarkedCmpMsg			= "The Task has been marked as completed.";

// Closed Tasks
// --------------------------------------------------------------------------------------------------
$openTasksNavLink			= "Open Tasks";
$closedTasksNavLink			= "Closed/Completed Tasks";
$newTaskNavLink				= "New Task";
$noClosedTasksFound			= "No Closed/Completed Tasks found.";
$taskTitleField				= "Task Title";
$createdByField				= "Created By";
$priorityField				= "Priority";
$statusField				= "Status";
$dateCreatedField			= "Date Created";
$dateCompletedField			= "Date Completed";
$viewTaskTooltip			= "View/Update Task";
$reopenTaskTooltip			= "Re-open Task";
$deleteTaskTooltip			= "Delete Task";
$reopenTaskConf				= "Re-open the Task:";
$deleteTaskConf				= "Are you sure you want to Delete the Task:";
$taskReopenedMsg			= "The Task has been re-opened";
$taskDeletedMsg				= "The Task has been deleted.";

// View Task
// --------------------------------------------------------------------------------------------------
$taskNotesField				= "Task Notes";
$editTaskForm				= "Edit/Update Task";
$updateTaskBtn				= "Update Task";
$taskUpdatedMsg				= "The Task has been updated.";

// New Task
// --------------------------------------------------------------------------------------------------
$assignTaskField			= "Assign Task";
$assignTaskField1			= "Assign to Myself";
$assignTaskFieldHelp		= "As an Admin/Manager, You can assign this task to someone else.";
$taskDueField				= "Task Due Date";
$addToCalField				= "Add to Calendar";
$addToCalFieldHelp			= "Check to add the Task to your Personal Calendar. The Task will display on the Task Due Date.";
$taskDescField				= "Task Description";
$saveNewTaskBtn				= "Save New Task";
$taskTitleReq				= "The Task Title is required.";
$taskDescReq				= "The Task Description is required.";
$taskDueDateReq				= "The Task Due Date is required.";
$taskAddedMsg1				= "The New Task has been saved, and added to your calendar.";
$taskAddedMsg2				= "The New Task has been added.";

// Employees Report
// --------------------------------------------------------------------------------------------------
$empReportExportBtn			= "Export Report to CSV";
$noResultsFound				= "No Results Found.";
$phone1Field				= "Phone";
$typeField					= "Type";
$administratorText			= "Administrator";
$employeeText				= "Employee";
$managerText				= "Manager";
$inactiveText				= "Inactive";
$activeText					= "Active";
$allActiveEmpText			= "All Active Employees";
$allInactiveEmpText			= "All Inactive Employees";
$allEmpText					= "All Employees";

// Employee Time Report
// --------------------------------------------------------------------------------------------------
$yearField					= "Year";
$weekNoField				= "Week No.";
$dateInField				= "Date In";
$timeInField				= "Time In";
$dateOutField				= "Date Out";
$timeOutField				= "Time Out";
$totalHoursField			= "Total Hours";
$totalText					= "Total:";
$selectEmpReq				= "Please select an Employee to run the Report on.";
$fromDateReq				= "The From Date is required.";
$toDateReq					= "The To Date is required.";
$datesText					= "Dates";
$totalRecordsText			= "Total Records";

// Import Data
// --------------------------------------------------------------------------------------------------
$globalSiteSetNavLink		= "Global Site Settings";
$importDataNavLink			= "Import Data";
$importDataTitle			= "Import Data Instructions";
$importDataQuip1			= "You can import your previous TimeZone data into TimeZone V2. If you choose to upload your old data, you will need to do this BEFORE you add any new data through TimeZone.
Once you have added any records (not including the records created during the install), you will no longer be able to import your old data. This is to prevent duplicate ID's in the database.";
$importDataQuip2			= "To export your old data from TimeZone v.1, please refer to the documentation that is included in the TimeZone download zip file you downloaded from CodeCanyon. If you have any questions
about exporting/importing, please do not hesitate to contact me through my <a href=\"http://codecanyon.net/user/Luminary\">CodeCanyon Profile</a>.";
$importDataQuip3			= "If this is your first time using TimeZone, you do not need to do anything special. The Import Data page will not effect TimeZone in any way.";
$employeeDataTitle			= "Employee Data";
$selectFileField			= "Select File";
$impEmpBtn					= "Import Employees";
$recordsExistsMsg			= "The Database all ready contains records, and you can not import additional data.";
$leaveDataTitle				= "Leave Data";
$leaveEarnedTitle			= "Leave Earned";
$leaveEarnedBtn				= "Import Leave Earned";
$leaveTakenTitle			= "Leave Taken";
$leaveTakenBtn				= "Import Leave Taken";
$compiledLeaveTitle			= "Compiled Leave";
$compiledLeaveBtn			= "Import Compiled Leave";
$timeClockTitle				= "Time Clock Data";
$timeClocksTitle			= "Time Clocks";
$timeClocksBtn				= "Import Clock Data";
$timeEntriesTitle			= "Time Entries";
$timeEntriesBtn				= "Import Time Entries";
$timeEditsTitle				= "Time Edits";
$timeEditsBtn				= "Import Time Edits";
$siteNoticesTitle			= "Site Notices/Alerts";
$siteNoticesText			= "Site Notices";
$siteNoticesBtn				= "Import Site Notices";
$importErrorMsg				= "There was an error, and the import did not complete.";
$empDataUploadedMsg			= "The Employee data has been successfully imported.";
$compiledLeaveUploadedMsg	= "The Compiled Leave has been successfully imported.";
$leaveEarnedUploadedMsg		= "The Leave Earned has been successfully imported.";
$leaveTakenUploadedMsg		= "The Leave Taken has been successfully imported.";
$timeClockDataUploadedMsg	= "The Time Clock data has been successfully imported.";
$timeEntriesUploadedMsg		= "The Time Entry data has been successfully imported.";
$timeEditsUploadedMsg		= "The Time Edits data has been successfully imported.";
$siteNoticesUploadedMsg		= "The Site Notices have been successfully imported.";

// My Profile
// --------------------------------------------------------------------------------------------------
$changeAvatarBtn			= "Change Avatar";
$persInfoBtn				= "Update Personal Info";
$updtEmailBtn				= "Update Email";
$changePasswordBtn			= "Change Password";
$availText					= "Available";
$hoursText					= "Hours";
$noLeaveEarnedMsg			= "You have not earned any leave.";
$dateEnteredField			= "Date Entered";
$hoursEarnedField			= "Hours Earned";
$leaveUsedText				= "Leave Used";
$hoursUsedText				= "Hours Used";
$noLeaveTakedMsg			= "You have not taken any leave.";
$hoursTakenField			= "Hours Taken";
$personalInfoTitle			= "Your Personal Information is secure.";
$personalInfoQuip			= "We store your personal information in our database in an encrypted format.
We do not sell or make your information available to any one for any reason. We value our employee's privacy and appreciate your trust in us.";
$profileAvatarModal			= "Profile Avatar";
$profileAvatarQuip1			= "You can remove your current Avatar, and use the default Avatar.";
$profileAvatarQuip2			= "To upload a new Avatar image you will need to first remove your current Avatar.";
$removeAvatarBtn			= "Remove Avatar";
$uplNewAvatarField			= "Upload a New Avatar Image";
$allowedFileTypesText		= "Allowed File Types:";
$selectNewAvatarField		= "Select New Avatar";
$uplAvatarBtn				= "Upload Avatar";
$deleteAvatarConf			= "Are you sure you want to remove your current Avatar?";
$updatePersInfoModal		= "Update Personal Information";
global $firstNameField	;
 $firstNameField				= "First Name";
$miField					= "Middle Initial";
$lastNameField				= "Last Name";
$primPhoneField				= "Primary Phone";
$altPhone1					= "Alternate Phone";
$mailingAddyField			= "Mailing Address";
$altAddyField				= "Alternate Address";
$updateInfoBtn				= "Update Information";
$updateEmailModal			= "Update Account Email";
$emailFieldHelp				= "Your email address is also used for your Account log In.";
$changePasswordModal		= "Change Account Password";
$currPasswordField			= "Current Password";
$currPasswordFieldHelp		= "Your Current Account Password.";
$newPasswordField			= "New Password";
$newPasswordFieldHelp		= "Type a new Password for your Account.";
$confNewPasswordField		= "Confirm New Password";
$confNewPasswordFieldHelp	= "Type the New Password again. Passwords MUST Match.";
$avatarDeletedMsg			= "Your Profile Avatar has been removed successfully.";
$avatarDeleteError			= "An Error was encountered, and your Profile Avatar could not be removed.";
$invalidAvatarMsg			= "The Avatar Type was not an allowed file type to be uploaded.";
$avatarUplMsg				= "Your Profile Avatar has been uploaded and saved.";
$avatarUplError				= "An Error was encountered, and your Profile Avatar could not be uploaded.";
$firstNameReq				= "First Name is required.";
$lastNameReq				= "Last Name is required.";
$primaryPhoneReq			= "A Primary Contact Phone Number is required.";
$mailingAddyReq				= "A Mailing Address is required.";
$accountInfoUpdMsg			= "Your Account Information has been updated.";
$validEmailReq				= "A valid Email Address is required.";
$acctEmailUpdatedMsg		= "Your Account Email Address has been updated.";
$currentPassReq				= "Your Current Account Password is required.";
$currPassIncorectMsg		= "Your Current Password is incorrect, please check your entries.";
$newPassReq					= "A New Account Password is required.";
$retypePassReq				= "Please retype the New Password. Passwords MUST match.";
$empAccPassReq				= "New Passwords do not match, please check your entries.";
$accountPassChangedMsg		= "Your Account Password has been changed.";

// View Employee
// --------------------------------------------------------------------------------------------------
$remAvatarTooltip			= "Remove Profile Avatar";
$updtEmpDataTooltip			= "Update Employee Data";
$updtEmpEmailTooltip		= "Update Employee's Email";
$changeEmpPassTooltip		= "Change Employee's Password";
$updateEmpPosTooltip		= "Update Employee's Position &amp; Pay";
$termEmpTooltip				= "Terminate Employee";
$deleteEmpAvatarConf		= "Are you sure you want to remove the Employee's Profile Avatar?";
$dobField					= "Date of Birth";
$ssnField					= "Social Security Number";
$updEmpEmailModal			= "Update Employee's Account Email";
$empEmailAddFieldHelp		= "The Employee's email address is also used as their Account log In.";
$changeEmpPassModal			= "Change Employee's Account Password";
$typeNewEmpPassHelp			= "Type a new Password for the Employee's Account.";
$posTitleField				= "Position Title";
$payGradeField				= "Pay Grade";
$startSalaryField			= "Starting Salary";
$currSalaryField			= "Current Salary";
$salaryTermField			= "Salary Term";
$leavePerWeekField			= "Leave Earned per Week";
$mngrAccField				= "Manager Account?";
$mngrAccFieldHelp			= "Make this Employee a Manager. Managers have limited access to other Employee data.";
$adminAccField				= "Administrator Account?";
$adminAccFieldHelp			= "Make this Employee an Administrator. Admins have full access, read, update and delete permissions.";
$termEmpFieldHelp			= "Set the Employee as Terminated.";
$termDateField				= "Termination Date";
$termDateFieldHelp			= "Required for Termination. The last day the Employee worked.";
$termReasonField			= "Termination Reason";
$termReasonFieldHelp		= "Required for Termination. The Reason for the Termination.";
$updTermStatusBtn			= "Update Termination Status";
$hasWorkedText				= "has worked";
$isCurrentlyText			= "is currently";
$manuallyClockOutText		= "You can manually Clock the Employee In or Out.";
$inactEmpText				= "Inactive Employee";
$viewTimeCardsText			= "View All Time Cards for";
$addLeaveBtn				= "Add Leave";
$doesNotHaveLeaveMsg		= "does not currently have any Leave available.";
$subLeaveBtn				= "Subtract Leave";
$noLeaveTakenMsg			= "has not taken any Leave.";
$empPersInfoTitle			= "Employee's Personal Information is secure.";
$empPersInfoQuip1			= "Personally Identifiable Information (PII) is stored in the database in an encrypted format.";
$empPersInfoQuip2			= "As an Administrator, you can disable the DOB and SSN fields in the Site Settings.";
$addLeaveModal				= "Add Additional Leave";
$addHoursField				= "Additional Hours";
$weekNumberField			= "Week Number";
$subLeaveModal				= "Subtract Leave";
$empAvatarRemMsg			= "The Employee's Profile Avatar has been removed successfully.";
$empAvatarRemError			= "An Error was encountered, and the Employee's Profile Avatar could not be removed.";
$empAccUpdatedMsg			= "The Employee's Account Data has been updated.";
$empEmailAddyUpdatedMsg		= "The Employee's Account Email Address has been updated.";
$empPassUpdatedMsg			= "The Employee's Account Password has been changed.";
$empPositionReq				= "The Employee's Position Title is required.";
$empDateOfHireReq			= "The Date of Hire is required.";
$empStartSalaryReq			= "The Employee's Starting Salary is required.";
$empCurrSalarayReq			= "The Employee's Current Salary is required.";
$salaryTermReq				= "The Salary Term is required.";
$leavePerWeekReq			= "The Leave Earned per Week is required.";
$empPosPayUpdatedMsg		= "The Employee's Position &amp; Pay has been updated.";
$empTermError				= "You can not Terminate the Primary Administrator.";
$termDateReq				= "The Termination Date is required.";
$termReasonReq				= "The Termination Reason is required.";
$empTermStatusUpdatedMsg	= "The Employee's Termination Status has been saved.";
$addLeaveHoursReq			= "Additional Hours is required.";
$weekNumberReq				= "The Week Number is required.";
$yearReq					= "The Year is required.";
$addHoursSavedMsg			= "The additional Leave has been added.";
$hoursTakenReq				= "Hours Taken is required.";
$leaveTakenSavedMsg			= "The Leave taken has been saved.";

// Site Notices
// --------------------------------------------------------------------------------------------------
$noNoticesFound				= "No Site Notifications Found.";
$noticeTitleField			= "Notice Title";
$editNoticeTooltip			= "Edit Notification";
$deleteNoticeTooltip		= "Delete Notification";
$editNoticeModal			= "Edit Site Notification";
$deleteNoticeConf			= "Are you sure you want to Delete this Site Notification?";
$siteNoticeUpdMsg			= "The Site Notification has been updated.";
$siteNoticeDeletedMsg		= "The Site Notification has been deleted.";

// New Site Notice
// --------------------------------------------------------------------------------------------------
$siteNoticesNavLink			= "All Site Notifications";
$newNoticeNavLink			= "Add a New Site Notification";
$siteNoticesQuip			= "To use a Start Date and/or an End Date, set the Site Notification as inactive.
Site Notifications set to Active will display regardless of what dates are set.";
$noteStartDateHelp			= "Leave blank if the Site Notification does not have a start date.";
$noteEndDateHelp			= "Leave blank if the Site Notification never expires.";
$noteIsActiveField			= "Active Notification?";
$noteIsActiveFieldHelp		= "Selecting Yes makes this Notification visible for everyone on their Dashboard.";
$noteTitleField				= "Site Notification Title";
$siteNoteTextField			= "Site Notification Text";
$saveNoteBtn				= "Save Site Notification";
$noteTitleReq				= "The Site Notice Title is required.";
$noteTextReq				= "The Site Notice Text is required.";
$siteNoteSavedMsg			= "The New Site Notification has been saved.";

// Reports
// --------------------------------------------------------------------------------------------------
$employeeReportsTitle		= "Employees Report";
$includeEmpField			= "Include Employees";
$includeEmpField0			= "Active Employees Only";
$includeEmpField1			= "Inactive Employees Only";
$includeEmpField2			= "All Active &amp; Inactive Employees";
$includeEmpFieldHelp		= "Select the Employee Status to run the report on.";
$runReportBtn				= "Run Report";
$empTimeRptTitle			= "Employee Time Report";
$selectEmpField				= "Select Employee";
$selectEmpFieldHelp			= "Select the Employee to run the report on. Inactive Employees are marked with an asterisk.";
$selectFromDateField		= "From Date";
$selectFromDateHelp			= "Select a beginning date.<br />Format: YYYY-MM-DD.";
$selectToDateField			= "To Date";
$selectToDateHelp			= "Select an ending date.<br />Format: YYYY-MM-DD.";

// Search Results
// --------------------------------------------------------------------------------------------------
$resultFoundText			= "Result Found";
$resultsFoundText			= "Results Found";
$noResultsFoundMsg			= "No Employees matching your Search Terms were found.";

// Site Settings
// --------------------------------------------------------------------------------------------------
$optionArabic				= "Arabic";
$optionBulgarian			= "Bulgarian";
$optionChechen				= "Chechen";
$optionCzech				= "Czech";
$optionDanish				= "Danish";
$optionEnglish				= "English";
$optionCanadianEnglish		= "Canadian English";
$optionBritishEnglish		= "British English";
$optionEspanol				= "Espanol";
$optionFrench				= "French";
$optionGerman				= "German";
$optionCroatian				= "Croatian";
$optionHungarian			= "Hungarian";
$optionArmenian				= "Armenian";
$optionIndonesian			= "Indonesian";
$optionItalian				= "Italian";
$optionJapanese				= "Japanese";
$optionKorean				= "Korean";
$optionDutch				= "Dutch";
$optionPortuguese			= "Portuguese";
$optionRomanian				= "Romanian";
$optionSwedish				= "Swedish";
$optionThai					= "Thai";
$optionVietnamese			= "Vietnamese";
$optionCantonese			= "Cantonese";
$installUrlField			= "Installation URL";
$installUrlFieldHelp		= "Used in all uploads & email notifications. Must include the trailing slash.";
$localField					= "Localization";
$localFieldHelp				= "Choose the Default Language file to use throughout TimeZone.";
$siteNameField				= "Site Name";
$busNameField				= "Business Name";
$busEmailField				= "Business Email";
$busPhoneField1				= "Business Phone";
$busPhoneField2				= "Alternate Business Phone";
$busAddyField				= "Business Address";
$allowTimeEditsField		= "Allow Time Entry Edits?";
$allowTimeEditsFieldHelp	= "Set to No to disable the ability for Employees to edit Time Entries.";
$enablePiiField				= "Show Employee's DOB &amp; SSN Fields?";
$enablePiiFieldHelp			= "Set to No to disable and hide the Employee's DOB &amp; SSN Fields.";
$busDocsFoldField			= "Business Documents Folder";
$busDocsFoldFieldHelp		= "Where all Business files upload to.<br />Must include the trailing slash.";
$uplFileTypeField			= "Upload File Types Allowed";
$uplFileTypeFieldHelp		= "The file types you allow to be uploaded.<br />NO spaces & each separated by a comma (Format: jpg,jpeg,png).";
$avatarFoldField			= "Avatar Upload Directory";
$avatarFoldFieldHelp		= "Where all Avatars upload to.<br />Must include the trailing slash.";
$avatarFileTypesField		= "Avatar File Types Allowed";
$avatarFileTypesFieldHelp	= "Avatar file types you allow to be uploaded.<br />NO spaces & each separated by a comma (Format: jpg,jpeg,png).";
$updSettingsBtn				= "Update the Global Site Settings";
$installUrlReq				= "The Installation URL is required.";
$siteNameReq				= "The Site Name is required.";
$busNameReq					= "The Business Name is required.";
$busAddyReq					= "The Business Address is required.";
$busEmailReq				= "The Business Email Address is required.";
$busDocsFoldReq				= "The Business Documents Folder is required.";
$uploadTypesReq				= "The Upload File Types Allowed is required.";
$avatarFoldReq				= "The Avatar Upload Directory is required.";
$avatarTypesReq				= "The Avatar File Types Allowed is required.";
$settingsSavedMsg			= "The Global Site Settings have been saved.";

// My Time Logs
// --------------------------------------------------------------------------------------------------
$manTimeEntryBtn			= "Manual Time Entry";
$noTimeEntriesMsg			= "No Time Entries Found";
$editTimeTooltip			= "View/Edit Time Record";
$deleteTimeTooltip			= "Delete Time Record";
$deleteTimeConf				= "Are you sure you want to permanently DELETE the Time Entry for:";
$saveTimeEntryBtn			= "Save Manual Time Entry";
$timeEntryDeletedMsg		= "The Time Entry Record has been deleted.";
$dateInReq					= "The Date In is required.";
$timeInReq					= "The Time In is required.";
$dateOutReq					= "The Date Out is required.";
$timeOutReq					= "The Time Out is required.";
$manualTimeEntrySaved		= "The Manual Time Entry has been saved.";

// Employee Time Cards
// --------------------------------------------------------------------------------------------------
$noEditMsg					= "You can not make any time entry edits while the current day's time clock is currently running.";
$compileText1				= "Compile Week";
$compileText2				= "Leave Hours";
$compileText3				= "Leave Hours Compiled";
$compileModal				= "Compile Leave for Week";
$compileTimeQuip			= "This action will add Leave Hours for all Active Employees based on what the Employee is set to earn.";
$leaveAllReadyCompiledMsg	= "The Leave has all ready been compiled.";
$leaveCompiledMsg			= "The Week's Leave Hours have been compiled.";

// View Time
// --------------------------------------------------------------------------------------------------
$recordDateField			= "Record Date";
$clockYearField				= "Clock Year";
$entryTypeField				= "Entry Type";
$clockRunningField			= "Clock Running";
$editTimeRecBtn				= "Edit Time Record";
$editTimeRecQuip			= "You can not make any Time Record edits while the current day's time clock is currently running.";
$prevTimeEditsTitle			= "Previous Time Record Updates";
$noTimeEditsFoundMsg		= "No Record Updates have been made.";
$updateDateField			= "Update Date";
$updatedByField				= "Updated By";
$reasonForEditField			= "Reason for Edit";
$reasonForEditFieldHelp		= "Please type a short reason for this Edit.";
$updateTimeRecModal			= "Update Time Record";
$editReasonReq				= "The Reason for the Update is required.";
$timeRecUpdatedMsg			= "The Time Record has been updated.";
$entryType1					= "Regular";
$entryType2					= "Manual Entry";
$entryType3					= "Edited/Updated";

/* --------------------------------------------------------------------------------------------------
 * Updates added December 20, 2014
 *
 * *** IMPORTANT! PLEASE READ ***
 * If you have all ready translated this file, just copy/paste the new localizations below to
 * you translated file. DO NOT over-write your file, or all translations will be lost.
 * --------------------------------------------------------------------------------------------------
 * ------------- ALWAYS MAKE A BACKUP OF YOUR DATABASE AND ALL FILES BEFORE UPGRADING!! -------------
 * -------------------------------------------------------------------------------------------------- */
$editTimeTooltip			= "View/Edit Time Records";
$forText					= "for";