# userreminder
Userreminder is an extension to the phpBB bulletin board to manage inactive users

# Description
Userreminder enables administrators to check their board for three different types of users:

- Users who have not been online for a selectable number of days (called inactive users); these users can be reminded of logging in again with one or two emails and after another period of time can be deleted. The number of days between the emails and the deletion can be selected. You can have done reminding and deleting the users automatically if desired.
- Users who have registered but never visited again after activation (called sleepers), these users can be deleted manually.
- Users who are online on a more or less regular basis but have never posted something (called zero posters), these can be deleted manually, too.

All three above mentioned tables are displayed in the ACP Extension tab. With an additional settings tab you can enter the different time frames as a number of days (e.g. 70 days until a user shows up as inactive). For your convenience you can select to remind and/or delete users automatically. If there are users you want to protect from getting reminded or deleted you can define those users by their user_id.

There is also a possibility to add one email address each for a bcc or cc copy of the reminding mails.

In a second part of the settings tab you can edit the text of the emails including a preview.

# Important !!!
Users are deleted by retaining their posts in order to prevent gaps in your forum threads!
