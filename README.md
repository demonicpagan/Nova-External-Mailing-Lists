Nova External Mailing Lists
===========================
Developer: Dustin Lennon<br />
Email: <demonicpagan@gmail.com>

This application is developed under the licenses of Nova and CodeIgniter.

Install Instructions
--------------------
The following application will alter your Nova installation to handle external mailing lists. To install this
application you need to perform the following steps.

*Note: This at this moment assumes that the external mailing list you are using allows anyone to post to alleviate
the need to add each one of your members of your group to your mailing list. However, if you opt to go this route,
that is totally your choice and you are free to do so. You can read how this MOD started here:
<http://forums.anodyne-productions.com/viewtopic.php?f=62&t=2806>

1. Log into your Nova installation.

2. Goto Site Management > Settings.

3. Click "Manage User-Created Settings &raquo;"

4. Click "Add User-Created Setting"

5. Fill in the Label text box and Setting Key text box.
   Example (the setting key in this example is what is used in write.php):

	Label: External Mailing List   
	Setting Key: external_mailing_list (this is only an example, make this whatever you want, just remember it for use in write.php)

	(You could create 3 different settings for the different kinds of posts if you want, just repeat steps 4 and 5 as needed.)

6. Upload application/controllers/write.php to your application/controllers folder of your Nova install replacing 
the existing one if you haven't already modified this file. If you already have changes in this file, it's best 
that you just take the contents of this file and add it into your existing write.php file.

7. Upload application/controllers/manage.php to your application/controllers folder of your Nova install replacing
the existing one if you haven't already modified this file. If you already have changes in this file, it's best
that you just take the contents of this file and add it inot your existing manage.php file.

***UCIP MEMBERS***<br />
You will need to modify the application/controllers/write.php to avoid getting double emails (one from Nova, one from
UCIP mailing list).

Comment out every occurance of $this->email->cc($this->settings->get_setting('external_mailing_list');

Change the following lines (write.php)

| Line 	|          From          	|                                    To                                    	|
|:----:	|:----------------------:	|:------------------------------------------------------------------------:	|
| 57   	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|
| 141  	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|
| 240  	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|

Change the following lines (manage.php)

| Line 	|          From          	|                                    To                                    	|
|:----:	|:----------------------:	|:------------------------------------------------------------------------:	|
| 56   	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|
| 95  	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|
| 155  	| $this->email->to($to); 	| $this->email->to($this->settings->get_setting('external_mailing_list')); 	|

If you experience any issues please submit a bug report on
<http://github.com/demonicpagan/Nova-External-Mailing-Lists/issues>.

You can always get the latest source from <http://github.com/demonicpagan/Nova-External-Mailing-Lists> as well.

Changelog - Dates are in Epoch time
-----------------------------------
1459829902:

*	Updated _email function to use the mail CI library instead of the email library.

1413681439:

*	Resolved bug issue #1 reported by @mooeypoo from #USS-Vindicator on KDFSnet.

1410003966:

*	Updated files to be compatible with Nova 2.3.2

1328661075:

*	Updating files to be compatible with Nova 2.0.1

1294316085:

*	Updated the _email function to version 1.2.2 of Nova

1284469613:

*	Made sure that the code for the write controller was up to par with Nova 1.1.

1272510152:

*	Created a more readable README for GitHub.

1270020606:

*	Started work with external email lists. This will submit news, logs, and posts to an external mailing list that 
you have set up in your Nova settings.
