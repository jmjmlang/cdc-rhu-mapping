# PHC Mapping System - Chapter 3 Figure Descriptions

This document provides organized figure titles and descriptions for Chapter 3 screenshots of the PHC Mapping System. The figure numbering starts at Figure 9 to follow the existing research-paper sequence. Each description is written for use under screenshots of pages, modal views, charts, filters, and major frontend subfeatures.

## Authentication Pages

### Figure 9. Login Page of the PHC Mapping System

Figure 9 shows the login page of the PHC Mapping System, which serves as the main entry point for registered users. The page allows approved users to sign in using their email address and password before accessing the dashboard assigned to their role. It also presents the system identity clearly, helping users recognize that they are entering the Healthcare Mapping platform for Luna, Apayao.

### Figure 9.1 Citizen Registration Tab on the Login Page

Figure 9.1 shows the citizen registration tab, where new users can create an account for the PHC Mapping System. The form collects the user's first name, last name, email address, gender, birthdate, barangay, and password credentials. This page supports the account approval workflow by allowing citizens to register while preventing immediate access until an administrator reviews the account.



## Admin Pages and Features

### Figure 10. Admin Dashboard Page

Figure 10 shows the admin dashboard, which provides an overview of the current health reporting status in the municipality. The page displays summary cards for pending reports, approved reports, total approved cases, and malnutrition cases. This dashboard helps administrators quickly understand the volume of reports that need action and the overall number of verified cases in the system.

### Figure 10.1 Create New Report Modal on the Admin Dashboard

Figure 10.1 shows the Create New Report modal used by administrators to directly encode a health case report. The modal includes fields for barangay, health category, number of cases, report date, notes, symptoms, and optional patient information. Unlike citizen submissions, reports created by administrators are automatically approved because they are entered by an authorized user.

### Figure 10.2 Report Details Modal from the Admin Dashboard

Figure 10.2 shows the report details modal opened from the dashboard's approved report actions. The modal displays the barangay, health category, report date, number of cases, reporter, submission time, and notes when available. This view allows administrators to inspect a report without leaving the dashboard page.

### Figure 10.3 Edit Report Modal from the Admin Dashboard

Figure 10.3 shows the edit mode inside the report actions modal. Administrators can update the barangay, health category, number of cases, report date, and optional notes of an existing report. This feature is useful when a report was approved but later requires correction based on updated or verified information.

### Figure 10.4 Delete Report Modal from the Admin Dashboard

Figure 10.4 shows the delete mode of the report actions modal. The administrator must provide a reason for deleting the report before it is removed from the active map and report lists. This requirement supports accountability because the submitting citizen can later view the reason why the report was removed.

### Figure 11. Municipality Case Report Map Page

Figure 11 shows the full Municipality Case Report Map page, which displays verified case reports from the past 30 days in Luna, Apayao. The page uses an interactive Leaflet map to show case activity by barangay using circles placed on geographic locations. This page serves as the main visual monitoring tool for understanding where recent health cases are concentrated.

### Figure 11.1 Barangay Case Breakdown Popup on the Map Page

Figure 11.1 shows the popup that appears when a barangay case circle is selected. The popup displays the barangay name, disease categories reported in that barangay, case totals per category, risk indicators, and the total number of cases. This modal-like popup gives users detailed information while keeping them within the map interface.

### Figure 12. Reports Management Page

Figure 12 shows the Reports Management page used by administrators to monitor and maintain case reports. The page includes status cards for approved, pending, and rejected or deleted reports. It functions as the main workspace for report verification, correction, filtering, sorting, and review.

### Figure 12.1 View Report Details Modal on the Reports Page

Figure 12.1 shows the View Details mode of the report actions modal. It provides a structured summary of the selected report, including barangay, category, date, cases, reporter, submission time, and notes. This modal helps administrators confirm the contents of a report before taking further action.

### Figure 12.2 Edit Report Modal on the Reports Page

Figure 12.2 shows the Edit Report mode from the report actions modal. This form allows administrators to correct the barangay, category, number of cases, report date, and notes of an approved or pending report. The modal-based design keeps the user on the same page while allowing direct updates to report information.

### Figure 12.3 Delete Report Modal with Deletion Reason

Figure 12.3 shows the Delete Report mode that requires a written deletion reason. The system warns the administrator that the report will be removed from the map and that the submitting citizen will see the reason. This feature promotes transparency and reduces confusion when a submitted report is removed by an administrator.

### Figure 12.4 Rejected and Deleted Reports Section

Figure 12.4 shows the Rejected and Deleted section of the Reports page. This section lists reports that were either rejected during verification or deleted after review, including their status and deletion reason when available. It helps administrators maintain a record of reports that were excluded from active mapping and analysis.

### Figure 13. Health Categories Page

Figure 13 shows the Health Categories page, where administrators manage the health categories used in reports and map filters. Each category represents a disease or health concern that can be selected during report submission. This page supports consistent classification of health cases throughout the system.

### Figure 13.1 Add New Health Category Panel

Figure 13.1 shows the Add New Category panel on the Health Categories page. The form allows administrators to enter a category name and an optional description. Adding categories through this panel makes the system flexible when the municipality needs to track additional health concerns.

### Figure 13.2 Existing Health Categories List

Figure 13.2 shows the list of existing health categories. Each category displays its name, description when available, and the number of linked case reports. Categories without guide content are marked with a Needs Guide badge, which helps administrators identify missing health education content.

### Figure 13.3 Edit Health Category Form

Figure 13.3 shows the inline edit form for updating an existing health category. Administrators can revise the category name and description without opening a separate page. This keeps category maintenance simple and allows corrections to be made directly from the list.

### Figure 13.4 Edit Health Guide Panel for a Category

Figure 13.4 shows the Health Guide editing panel for a selected category. Administrators can enter prevention tips and action steps, with one item written per line. The content saved in this panel is later displayed to citizens in the Health Guide page.

### Figure 14. User Approval and Account Management Page

Figure 14 shows the User Approval and Account Management page used by administrators. The page displays pending citizen registrations, recently reviewed accounts, and a complete list of system users. It supports account approval, user editing, role management, and monitoring of registered users.

### Figure 14.1 Edit User Modal

Figure 14.1 shows the Edit User modal opened from the All Users table. Administrators can update a user's full name, email, role, barangay, designation, and password. This modal centralizes user maintenance while keeping the administrator on the same page.

### Figure 15. Decision Support Page

Figure 15 shows the Decision Support page, which analyzes verified reports from the past 30 days. The page displays total cases, affected barangays, critical alerts, and high-risk counts through summary cards. This page helps administrators identify priority health concerns based on recent approved report data.

### Figure 15.1 Needs Immediate Attention Table

Figure 15.1 shows the Needs Immediate Attention table in the Decision Support page. It lists barangay-disease pairs classified as High or Critical risk, along with case totals and recommended actions. This section guides administrators toward areas that may require immediate intervention or closer coordination with health personnel.

### Figure 15.2 Under Monitoring Table

Figure 15.2 shows the Under Monitoring table, which contains barangay-disease pairs classified as Low or Moderate risk. The table still provides case totals and recommended actions so that administrators can continue routine surveillance. This feature helps prevent smaller case clusters from being ignored.

### Figure 15.3 Risk Level Threshold Settings

Figure 15.3 shows the Risk Level Threshold Settings form in the Decision Support page. Administrators can configure the case-count thresholds used to classify Moderate, High, and Critical risk levels. This makes the decision support feature adaptable to local health office standards and changing public health conditions.

### Figure 16. Activity Log Page

Figure 16 shows the Activity Log page, which records important user and report actions in the system. The log includes activities such as profile updates, report submissions, approvals, rejections, edits, deletions, and role changes. This page supports accountability by showing who performed an action, what type of action occurred, and when it happened.

## Citizen Pages and Features

### Figure 17. Citizen Dashboard Page

Figure 17 shows the Citizen Dashboard page, which provides citizens with access to their reporting activity and the municipal case map. The page displays the citizen's own report totals, approved reports, and pending reports through summary cards. It acts as the citizen's main workspace for submitting new reports and tracking report status.

### Figure 17.1 Case Map Preview on the Citizen Dashboard

Figure 17.1 shows the case map preview on the Citizen Dashboard. The map displays verified reports from the past 30 days using barangay-based circles and popups. This allows citizens to view current health case activity in the municipality without opening the full map page.

### Figure 17.2 My Reporting Activity Summary Cards

Figure 17.2 shows the My Reporting Activity cards on the Citizen Dashboard. These cards summarize the total number of reports submitted by the citizen, how many were approved, and how many are still pending review. The feature helps citizens understand the status of their participation in the reporting process.

### Figure 17.3 My Reports Table

Figure 17.3 shows the My Reports table, where citizens can view their submitted case reports. Each row displays the report date, barangay, health category, number of cases, status, and available information action. This table helps citizens monitor whether their reports are pending, approved, rejected, or removed.

### Figure 17.4 Submit New Report Modal

Figure 17.4 shows the Submit New Report modal used by citizens. The form allows citizens to select the barangay and health category, enter the number of cases, choose a report date, and add optional notes or symptoms. Once submitted, the report is saved as pending and waits for administrator verification.

### Figure 17.5 Detailed Report Section in the Citizen Report Modal

Figure 17.5 shows the Detailed Report section inside the citizen submission modal. When enabled, it displays optional patient information fields such as patient name, gender, birthdate, and age. This feature allows citizens to submit more detailed information when available while keeping the basic reporting form short.

### Figure 17.6 Citizen Report Detail Modal

Figure 17.6 shows the report detail modal available from the citizen's My Reports table. It displays the report number, status, barangay, category, case count, report date, submission time, review status, and notes when available. This modal helps citizens confirm the information they submitted and check how the report was processed.

### Figure 17.7 Report Removed Reason Modal

Figure 17.7 shows the modal displayed when a citizen opens the reason for a deleted report. The modal shows the barangay, category, report date, and the administrator's reason for removing the report. This feature makes the report removal process more transparent to the citizen who submitted the information.

### Figure 18. Citizen Health Guide Page

Figure 18 shows the Citizen Health Guide page, which provides general health information for tracked disease categories. The page begins with a reminder that the guide is for information only and is not a medical diagnosis. It helps citizens access prevention tips and action steps related to diseases monitored by the system.

### Figure 18.1 Expanded Disease Guide Accordion

Figure 18.1 shows an expanded disease guide accordion on the Health Guide page. The accordion displays prevention tips and recommended action steps for the selected health category. This design keeps the guide organized while allowing citizens to focus on one disease topic at a time.

## Shared Account Features

### Figure 19. Profile Page

Figure 19 shows the Profile page, where users can manage their account information. The page contains sections for profile information, password updates, and account deletion. This shared feature is available to authenticated users and allows them to maintain their personal account details.

### Figure 19.1 Profile Information Form

Figure 19.1 shows the Profile Information form. Users can update their name, email, gender, birthdate, and age, while barangay information is shown as read-only when already assigned. For admin users, the form also displays their role and designation information.

### Figure 19.2 Update Password Form

Figure 19.2 shows the Update Password form on the Profile page. The user must provide the current password, new password, and password confirmation before the system accepts the change. This feature helps users maintain account security by requiring verification before updating credentials.

### Figure 19.3 Delete Account Confirmation Modal

Figure 19.3 shows the Delete Account confirmation modal. The modal warns users that deleting the account is permanent and asks for password confirmation before proceeding. This extra confirmation step helps prevent accidental account deletion.

## Suggested Screenshot Order

1. Capture authentication pages first, beginning with Figure 9.
2. Capture admin pages next, starting with dashboard screenshots and then moving through map, reports, categories, users, DSS, and activity log.
3. Capture citizen pages after the admin section, including dashboard, report modals, and health guide views.
4. Capture shared account features last, because the Profile page applies to both roles.
5. For modal screenshots, open the modal from the related parent page and use decimal numbering such as Figure 12.4 or Figure 17.6.

