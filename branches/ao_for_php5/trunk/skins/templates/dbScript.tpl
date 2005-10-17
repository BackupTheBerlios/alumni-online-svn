-- 
-- Table : configuration
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}configuration;
CREATE TABLE {{$databaseTablePrefix}}configuration (
  config_key varchar(50) NOT NULL default '',
  config_value text NOT NULL
);

-- 
-- Table data : configuration
-- 

INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('email', '{{$parameters.siteEmail}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('showVersion', '1');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('emailFrom', '{{$parameters.mailerFrom}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('sitePath', '');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('theme', '{{$parameters.siteSkin}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('title', '{{$parameters.siteTitle}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('container', '{{$parameters.siteContainer}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('emailMailer', '{{$parameters.mailer}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('emailPassword', '{{$parameters.mailerPassword}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('emailUsername', '{{$parameters.mailerUser}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('trackerUsername', '{{$parameters.wsUser}}');
INSERT INTO {{$databaseTablePrefix}}configuration VALUES ('trackerPassword', '{{$parameters.wsPassword}}');

-- --------------------------------------------------------

-- 
-- Table : events
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}events;
CREATE TABLE {{$databaseTablePrefix}}events (
  event_name varchar(20) NOT NULL default '',
  description text,
  PRIMARY KEY  (event_name)
);

-- 
-- Table data : events
-- 

INSERT INTO {{$databaseTablePrefix}}events VALUES ('login_succeed', 'Occures When User logs into the system');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('login_fail', 'Occures after a bad login');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('user_registration', 'Occures when a visitor registers itself');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('user_new', 'Occures when a new user created by system administrator');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('user_deactivate', 'Occures when a user deactivated by system administrator');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('user_activate', 'Occures when a user activated by system administrator');
INSERT INTO {{$databaseTablePrefix}}events VALUES ('exception', 'Occures on an unhandled system exception');

-- --------------------------------------------------------

-- 
-- Table : events_adapters
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}events_adapters;
CREATE TABLE {{$databaseTablePrefix}}events_adapters (
  events_adapters_id bigint(20) NOT NULL auto_increment,
  description text NOT NULL,
  control_src text NOT NULL,
  PRIMARY KEY  (events_adapters_id)
);

INSERT INTO {{$databaseTablePrefix}}events_adapters VALUES (1, 'Informs system administrator about events', 'InformAdmin.php');

-- --------------------------------------------------------

-- 
-- Table : events_listeners
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}events_listeners;
CREATE TABLE {{$databaseTablePrefix}}events_listeners (
  events_listener_id bigint(20) NOT NULL auto_increment,
  event_name varchar(20) NOT NULL default '',
  events_adapters_id bigint(20) NOT NULL default '0',
  PRIMARY KEY  (events_listener_id)
);

INSERT INTO {{$databaseTablePrefix}}events_listeners VALUES (1, 'login_succeed', 1, 1);
INSERT INTO {{$databaseTablePrefix}}events_listeners VALUES (2, 'login_fail', 1, 1);
INSERT INTO {{$databaseTablePrefix}}events_listeners VALUES (3, 'user_new', 1, 1);
INSERT INTO {{$databaseTablePrefix}}events_listeners VALUES (4, 'user_activate', 1, 1);
INSERT INTO {{$databaseTablePrefix}}events_listeners VALUES (5, 'user_deactivate', 1, 1);


-- --------------------------------------------------------

-- 
-- Table : metatags
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}metatags;
CREATE TABLE {{$databaseTablePrefix}}metatags (
  tag_name varchar(50) NOT NULL default '',
  tag_value text NOT NULL
);

-- 
-- Table data : metatags
-- 

INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('product', 'Alumni-Online');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('product_version', '3.0.0.1');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('RESOURCE-TYPE', 'DOCUMENT');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('product_creator', 'Fatih BOY [fatih@enterprisecoding.com]');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('DISTRIBUTION', 'GLOBAL');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('AUTHOR', 'Fatih Boy');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('RATING', 'SOFTWARE');
INSERT INTO {{$databaseTablePrefix}}metatags VALUES ('ROBOTS', 'NOINDEX');

-- --------------------------------------------------------

-- 
-- Table : module_controls
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}module_controls;
CREATE TABLE {{$databaseTablePrefix}}module_controls (
  module_controls_id bigint(20) NOT NULL auto_increment,
  module_definition_id bigint(20) NOT NULL default '0',
  control_key varchar(30) default NULL,
  control_title varchar(100) default NULL,
  control_src text NOT NULL,
  PRIMARY KEY  (module_controls_id)
);

-- 
-- Table data : module_controls
-- 

INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (1, 1, NULL, NULL, 'html/html.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (2, 1, 'Edit', 'Edit Content', 'html/htmlEdit.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (3, -1, NULL, NULL, 'login/login.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (4, -2, NULL, NULL, 'login/logout.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (5, -3, NULL, NULL, 'admin/modules/addModule.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (6, -4, NULL, NULL, 'admin/tabs/addTab.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (7, -6, NULL, NULL, 'admin/tabs/deleteTab.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (8, -7, NULL, NULL, 'myInfo.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (9, -5, NULL, NULL, 'admin/tabs/editTab.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (10, -8, NULL, NULL, 'admin/modules/moveModule.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (11, -9, NULL, NULL, 'admin/modules/deleteModule.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (12, -10, NULL, NULL, 'admin/modules/editModule.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (13, 2, NULL, NULL, 'siteMap.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (14, -11, NULL, NULL, 'admin/siteSettings.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (15, -12, NULL, NULL, 'admin/users/users.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (16, -12, 'addUser', 'Add User', 'admin/users/addUser.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (22, -13, 'message2Groups', 'Send Message', 'admin/userGroups/sendMessage.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (18, -12, 'message2Users', 'Send Message', 'admin/users/sendMessage.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (19, -13, NULL, NULL, 'admin/userGroups/groups.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (20, -13, 'addGroup', 'Add Group', 'admin/userGroups/addGroup.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (23, -14, NULL, NULL, 'admin/users/deleteUser.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (24, -15, NULL, NULL, 'admin/userGroups/deleteGroup.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (25, -16, NULL, NULL, 'admin/users/userInfo.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (26, -17, NULL, NULL, 'admin/exceptionViewer/exceptionViewer.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (27, 3, NULL, NULL, 'poll/poll.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (28, 4, NULL, NULL, 'status/msn/msnStatus.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (29, 5, NULL, NULL, 'status/icq/icqStatus.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (30, 6, NULL, NULL, 'status/yahoo/yahooStatus.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (31, 7, NULL, NULL, 'feedBack.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (32, 8, NULL, NULL, 'rss/rssFeed.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (35, 5, 'editIcqStatus', 'Edit ICQ Status', 'status/icq/icqStatusEdit.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (34, 8, 'editRSS', 'Edit RSS Feed', 'rss/rssEdit.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (36, 4, 'editMsnStatus', 'Edit MSN Status', 'status/msn/msnStatusEdit.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (37, 6, 'editYahooStatus', 'Edit Yahoo! Status', 'status/yahoo/yahooStatusEdit.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (38, -18, NULL, NULL, 'admin/users/sendMessage2User.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (39, -20, NULL, NULL, 'faq/faqEditAnswer.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (40, 3, 'editPollQuestion', 'Edit Question', 'poll/pollEditQuestion.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (41, 3, 'editPollAnswers', 'Edit Answers', 'poll/pollEditAnswers.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (42, 3, 'addPollAnswer', 'Add Answer', 'poll/pollAddAnswer.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (43, -19, NULL, NULL, 'login/forgetPassword.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (44, 9, NULL, NULL, 'faq/faq.php');
INSERT INTO {{$databaseTablePrefix}}module_controls VALUES (45, 9, 'AddFaqQuestion', 'Add Question', 'faq/faqEdit.php');

-- --------------------------------------------------------

-- 
-- Table : module_definitons
-- 


DROP TABLE IF EXISTS {{$databaseTablePrefix}}module_definitons;
CREATE TABLE {{$databaseTablePrefix}}module_definitons (
  module_definition_id bigint(20) NOT NULL auto_increment,
  friendly_name varchar(100) NOT NULL default '',
  definition text NOT NULL,
  PRIMARY KEY  (module_definition_id)
);

-- 
-- Table data : module_definitons
-- 

INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-1, 'Login Frame', 'Displays login form');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-2, 'Logout', 'Logs user out');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-3, 'Add New Module', 'Adds new module');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-4, 'Add New Tab', 'Adds new tab');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-5, 'Edit Tab', 'Edits active tab');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-6, 'Delete Tab', 'Deletes active tab');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-7, 'My Information', 'Displays current user''s personal information');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-8, 'Move Module', 'Moves module to given pane');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-9, 'Delete Module', 'Delete given module');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-10, 'Edit Module', 'Edits given module');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-11, 'Site Settings', 'Edits web site settings');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-12, 'User Settings', 'Displays user list');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-13, 'User Group Settings', 'Displays user group list');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-14, 'Delete User', 'Deletes given user');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-15, 'Delete Group', 'Deletes given user group');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-16, 'Exception Viewer', 'Displays Alumni-Online exception logs');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-18, 'Send Message', 'Sends message to user');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-19, 'Forget Password', 'Generates new password for given user and sends it via e-mail');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (-20, 'Edit FAQ', 'Edits/Deletes given FAQ');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (1, 'Html Content', 'Displays Html contents');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (2, 'Site Map', 'Displays map of the web site in a tree');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (3, 'Poll Module', 'Displays poll');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (4, 'Msn Status', 'Displays msn status of given user');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (5, 'Icq Status', 'Displays icq status of given user');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (6, 'Yahoo! Status', 'Displays yahoo status of given user');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (7, 'Feed Back', 'Allow visitors to send feedbacks');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (8, 'Rss Feed', 'Parses given rss feed');
INSERT INTO {{$databaseTablePrefix}}module_definitons VALUES (9, 'FAQ', 'Display Question-Answer, FAQs');

-- --------------------------------------------------------

-- 
-- Table : module_settings
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}module_settings;
CREATE TABLE {{$databaseTablePrefix}}module_settings (
  module_id bigint(20) NOT NULL default '0',
  setting_key varchar(100) NOT NULL default '',
  setting_value text NOT NULL
);

-- 
-- Table data : module_settings
-- 

INSERT INTO {{$databaseTablePrefix}}module_settings VALUES (22, 'html_content', '<p>Welcome to Alumni-Online v3.0.0.1</p>\r\n<p>Here is the list of changes on AO v3.0.0.1 :</p>\r\n<ul>\r\n  <li>Some bugfixes</li>\r\n  <li>Each module could have seperate containers</li>\r\n  <li>Automatic error reporting has been cancelled</li>\r\n  <li><b>Forget password</b> option added to login form</li>\r\n</ul>\r\n<p>List of changes on AO v3.0.0.0 :\r\n<ul>\r\n  <li> Alumni-Online completely re-written! </li>\r\n  <li>With new template system, it''s so easy to design templates  with no php knowledge,</li>\r\n  <li>With the help of container system, system administrators can assign seperate container<br>\r\n  templates to each module,</li>\r\n<li>User management has been improved.System administrators can assign user rights for<br>each tab, as well as for each module within tabs!</li>\r\n  <li>Error logs could save in xml format. This will help system administrators for<br> tracking system bugs, also\r\nit is used by <a href="http://tracker.enterprisecoding.com">enterprisecoding.com bug tracker system</a></li>\r\n<li>New module system, it is so easy to manage Alumni-Online. It also allows<br> contributers to extend Alumni-Online easily</li>\r\n<li>ADOdb library used for database layer, so has a wide range of support for db</li>\r\n</ul>\r\n<p>Followings are the open-source projects used in Alumni-Online :\r\n<ul>\r\n  <li><strong>adodb</strong>, for database connections</li>\r\n  <li><strong>nuSoap</strong>, for soap protocol support</li>\r\n  <li><strong>Smarty</strong>, for template system</li>\r\n  <li><strong>PhpLayersMenu</strong>, for links menu</li>\r\n  <li><strong>PhpMailer</strong>, Brent R. Matzelle, for sending e-mails</li>\r\n  <li><strong>CYahooStatus</strong>, Setec Astronomy, for yahoo status check class</li>\r\n  <li><strong>ICQStatus</strong>, for icq status check class</li>\r\n  <li><strong>class.RSS</strong>, Joseph Harris, for rss feed class</li>\r\n</ul></p><p>Also would like to thank <strong>DotNetNuke</strong> staff for their web management concept</p>');
INSERT INTO {{$databaseTablePrefix}}module_settings VALUES (24, 'html_content', '<img src="images/alumni-online.gif" alt="Powered by Alumni-Online"><br><br><img src="images/adodb.gif"><br><br><img src="images/smarty_icon.gif">');

-- --------------------------------------------------------

-- 
-- Table : module_special
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}module_special;
CREATE TABLE {{$databaseTablePrefix}}module_special (
  module_id bigint(20) NOT NULL default '0',
  query_string varchar(50) NOT NULL default '',
  PRIMARY KEY  (module_id)
);

-- 
-- Table data : module_special
-- 

INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-1, 'login');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-2, 'logout');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-3, 'AddModule');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-4, 'AddTab');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-5, 'EditTab');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-6, 'DeleteTab');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-7, 'MyInfo');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-8, 'MoveModule');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-9, 'DeleteModule');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-10, 'EditModule');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-11, 'DeleteUser');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-12, 'DeleteGroup');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-13, 'UserInfo');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-14, 'SendMessage');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-15, 'ForgetPassword');
INSERT INTO {{$databaseTablePrefix}}module_special VALUES (-16, 'EditFAQAnswer');

-- --------------------------------------------------------

-- 
-- Table : modules
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}modules;
CREATE TABLE {{$databaseTablePrefix}}modules (
  module_id bigint(20) NOT NULL auto_increment,
  tab_id bigint(20) NOT NULL default '0',
  module_definition_id bigint(20) NOT NULL default '0',
  module_order int(11) NOT NULL default '0',
  authorized_roles varchar(50) NOT NULL default '0',
  administrator_roles varchar(20) NOT NULL default '1',
  pane_name varchar(20) NOT NULL default '',
  module_title text NOT NULL,
  module_icon varchar(100) default NULL,
  alignment varchar(20) NOT NULL default '',
  can_show_title tinyint(4) NOT NULL default '1',
  display_all_tabs tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (module_id)
);

-- 
-- Table data : modules
-- 

INSERT INTO {{$databaseTablePrefix}}modules VALUES (1, -1, -1, 0, ';1;', ';2;', 'contentpane', 'Login Form', NULL, 'left', 0, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (2, -2, -2, 0, ';1;', ';2;', 'contentpane', 'Logout Form', NULL, 'left', 0, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (5, -3, -3, 0, ';2;', ';2;', 'contentpane', 'Add New Module', 'addModule.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (6, -4, -4, 0, ';2;', ';2;', 'contentpane', 'Add New Tab', 'addTab.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (7, -5, -5, 0, ';1;', ';2;', 'contentpane', 'Edit Tab', 'editTab.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (9, -7, -7, 0, ';1;', ';2;', 'contentpane', 'My Information', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (8, -6, -6, 0, ';1;', ';2;', 'contentpane', 'Delete Tab', 'deleteTab.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (15, -8, -8, 0, ';2;', ';2;', 'contentpane', 'Move Module', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (19, -9, -9, 0, ';2;', ';2;', 'contentpane', 'Delete Module', 'deleteModule.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (20, -10, -10, 0, ';2;', ';2;', 'contentpane', 'Edit Module', 'editModule.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (21, 1, 2, 0, ';1;', ';2;', 'leftPane', 'Site Map', NULL, '', 1, 1);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (22, 1, 1, 0, ';1;', ';2;', 'contentPane', 'Welcome to Alumni-Online v3.0.0.1', NULL, '', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (24, 1, 1, 0, ';1;', ';2;', 'leftPane', 'Powered By', NULL, 'center', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (25, 11, -11, 0, ';2;', ';2;', 'contentpane', 'Site Settings', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (26, 10, -12, 0, ';2;', ';2;', 'contentpane', 'User List', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (27, 9, -13, 0, ';2;', ';2;', 'contentpane', 'User Group List', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (28, -11, -14, 0, ';2;', ';2;', 'contentpane', 'Delete User', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (29, -12, -15, 0, ';2;', ';2;', 'contentpane', 'Delete User Group', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (30, -13, -16, 0, ';2;', ';2;', 'contentpane', 'User Information', 'editUser.gif', 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (31, 12, -17, 0, ';2;', ';2;', 'contentpane', 'Exception Viewer', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (38, -14, -18, 0, ';2;', ';2;', 'contentpane', 'Send Message', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (39, -15, -19, 0, ';1;', ';2;', 'contentpane', 'Forget Password', NULL, 'left', 1, 0);
INSERT INTO {{$databaseTablePrefix}}modules VALUES (40, -16, -20, 0, ';1;', ';2;', 'contentpane', 'Edit FAQ', NULL, 'left', 1, 0);


-- --------------------------------------------------------

-- 
-- Table : poll_answers
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}poll_answers;
CREATE TABLE {{$databaseTablePrefix}}poll_answers (
  poll_answers_id bigint(20) NOT NULL auto_increment,
  poll_module_id bigint(20) NOT NULL default '0',
  value varchar(255) NOT NULL default '',
  score bigint(20) NOT NULL default '0',
  PRIMARY KEY  (poll_answers_id)
);

-- --------------------------------------------------------

-- 
-- Table : poll_questions
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}poll_questions;
CREATE TABLE {{$databaseTablePrefix}}poll_questions (
  poll_module_id bigint(20) NOT NULL default '0',
  question text NOT NULL,
  PRIMARY KEY  (poll_module_id)
);

-- --------------------------------------------------------

-- 
-- Table : tabs
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}tabs;
CREATE TABLE {{$databaseTablePrefix}}tabs (
  tab_id bigint(20) NOT NULL auto_increment,
  tab_order mediumint(9) NOT NULL default '0',
  tab_name varchar(50) NOT NULL default 'New Tab',
  authorized_roles varchar(50) NOT NULL default ';1;',
  administrator_roles varchar(50) NOT NULL default ';2;',
  parent_id bigint(20) NOT NULL default '0',
  is_visible tinyint(4) NOT NULL default '1',
  title varchar(100) NOT NULL default 'Alumni-Online',
  description varchar(100) default NULL,
  keywords varchar(100) default NULL,
  PRIMARY KEY  (tab_id)
);

-- 
-- Table data : tabs
-- 

INSERT INTO {{$databaseTablePrefix}}tabs VALUES (-1, 0, 'Login/Logout', ';1;', ';2;', 0, 1, '', NULL, NULL);
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (1, 0, 'Home', ';1;', ';2;', 0, 1, 'Home Page', 'Your web site description', 'alumni-online, fatih boy, 2.2.0.0, alumni');
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (6, 100001, 'Administration', ';2;', ';-1;', 0, 1, 'System Administration', NULL, NULL);
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (9, 100011, 'User Groups', ';2;', ';-1;', 6, 1, 'User Group Administration', NULL, NULL);
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (10, 100031, 'Users', ';2;', ';-1;', 6, 1, 'User Administration', NULL, NULL);
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (11, 100005, 'Site Settings', ';2;', ';-1;', 6, 1, 'Site Administration', NULL, NULL);
INSERT INTO {{$databaseTablePrefix}}tabs VALUES (12, 100041, 'Exception Viewer', ';2;', ';-1;', 6, 1, 'Exception Viewer', NULL, NULL);
 

-- --------------------------------------------------------

-- 
-- Table : templates
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}templates;
CREATE TABLE {{$databaseTablePrefix}}templates (
  type varchar(50) NOT NULL default '',
  name varchar(50) NOT NULL default '',
  content text,
  PRIMARY KEY  (type,name)
);

INSERT INTO {{$databaseTablePrefix}}templates VALUES ('mail', 'forgetPasswordSubject', 'Password Reminder from {siteName}');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ('mail', 'forgetPasswordBody', 'You''ve request password remind. Here is detailed information\r\n\r\nUsername : {$username}\r\nPassword : {$password}');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ('event', 'loginFailSubject', 'Login failed!');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ("event", "loginFaildBody", "Failed login attempt with the username '{$username}'");
INSERT INTO {{$databaseTablePrefix}}templates VALUES ('event', 'loginSucceedSubject', 'Successfull login!');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ("event", "loginSucceedBody", "User '{$username}' has been successfully login into system");
INSERT INTO {{$databaseTablePrefix}}templates VALUES ('event', 'userActivateSubject_Admin', 'User Activated!');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ("event", "userActivateBody_Admin", "User '{$username}' has been activated.");
INSERT INTO {{$databaseTablePrefix}}templates VALUES ('event', 'userDeactivateSubject_Admin', 'User Deactivated!');
INSERT INTO {{$databaseTablePrefix}}templates VALUES ("event", "userDeactivateBody_Admin", "User '{$username}' has been deactivated.");

-- --------------------------------------------------------

-- 
-- Table : user_groups
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}user_groups;
CREATE TABLE {{$databaseTablePrefix}}user_groups (
  user_group_id bigint(20) NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  PRIMARY KEY  (user_group_id)
);

-- 
-- Table data : user_groups
-- 

INSERT INTO {{$databaseTablePrefix}}user_groups VALUES (1, 'Everyone');
INSERT INTO {{$databaseTablePrefix}}user_groups VALUES (2, 'Administrator');
INSERT INTO {{$databaseTablePrefix}}user_groups VALUES (3, 'Registered Users');

-- --------------------------------------------------------

-- 
-- Table : users
-- 

DROP TABLE IF EXISTS {{$databaseTablePrefix}}users;
CREATE TABLE {{$databaseTablePrefix}}users (
  username varchar(50) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  name varchar(50) NOT NULL default '',
  surname varchar(70) NOT NULL default '',
  user_group_id bigint(20) NOT NULL default '2',
  email varchar(50) NOT NULL default '',
  icq varchar(9) default NULL,
  msn varchar(50) default NULL,
  yahoo varchar(50) default NULL,
  image varchar(50) default NULL,
  is_active tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (username)
);

INSERT INTO {{$databaseTablePrefix}}users VALUES ('{{$parameters.adminUser}}', '{{$adminPassword}}', 'Administator', 'User', 2, '{{$parameters.adminUserEmail}}', NULL, NULL, NULL, '097', 1);

CREATE TABLE ao_faqs (
  faq_id bigint(20) NOT NULL auto_increment,
  module_id bigint(20) NOT NULL default '0',
  question varchar(255) NOT NULL default '',
  answer text NOT NULL,
  PRIMARY KEY  (faq_id)
)