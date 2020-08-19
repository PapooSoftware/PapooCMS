<?php 
/*
* Alle messages fÃ¼r den RSS-Feed
*/
$this->content->template['message_5000']='Description ';
$this->content->template['message_5001']='RSS feed 0.9.6a ';
$this->content->template['message_5002']='Originally (C) by Thomas Schoessow ';
$this->content->template['message_5003']='Web page of the author (opens in new window) ';
$this->content->template['message_5004']='Web page ';
$this->content->template['message_5005']='Mailaddress ';
$this->content->template['message_5006']='Mailaddress ';
$this->content->template['message_5007']='RSS feed for Papoo ';
$this->content->template['message_5008']='Extends Papoo with a RSS feed. It uses the teaser of the selected pages and creates a valid RSS Feeed. ';
$this->content->template['message_5009']='';
$this->content->template['message_5010']='';
$this->content->template['message_5011']='At this time, articles marked "durable" and "use in RSS-Feed" will be published. ';
$this->content->template['message_5012']='If ,during validation, a message appears "feed uses charset (xxx) but the server delivers (yyy), then add the following entry in the .htaccess file  ';
$this->content->template['message_5013']='The Link to the feed file (produce possibly manually with 777) reads then ';
$this->content->template['message_5014']='Link to the feed ';
$this->content->template['message_5015']='Show external RSS feed as static pages ';
$this->content->template['message_5016']='It is possible to show up to 10 RSS feeds as static pages in Papoo. The integration of the feeds is very simple. Provide a new menu entry in Papoo and insert in the textfield link “plugin:rssfeed/templates/rssfeed_show.html”, if you want to show feed1 as a page. For feed 10 its 
"plugin:rssfeed/templates/rssfeed_show9.html". ' ;
$this->content->template['message_5017']='The feeds are held in a Cache on the server for 1 hour, this can be changed.';
$this->content->template['message_5018']='The directory RSSCACHE must be adjusted with CHMOD 777, to allow the feeds to be cached. The directory RSSFEED in the Plugins directory must be likewise adjusted with Chmod 777. ';
$this->content->template['message_5019']='Configuration of your own RSS feed ';
$this->content->template['message_5020']='Please enter the necessary data for the feed. ';
$this->content->template['message_5021']='Title is the title for the feed ';
$this->content->template['message_5022']='The field description should contain a short outline of the feeds content. ';
$this->content->template['message_5023']='The field number of articles contains the number of the last articles, which are to be shown in the feed. ';
$this->content->template['message_5024']='The field language contains the value for the language the feed. 1 corresponds to German 2 to English. ';
$this->content->template['message_5025']='The Prefix for the RSS feed. With domains without www it should read http:// otherwise http://www. ';
$this->content->template['message_5026']='RSS feed Settings ';
$this->content->template['message_5027']='Title of the feed ';
$this->content->template['message_5028']='The RSS feed contains ';
$this->content->template['message_5029']='Description ';
$this->content->template['message_5030']='Contains the description of the feed ';
$this->content->template['message_5031']='Maximum number of articles ';
$this->content->template['message_5032']='The count of articles shown in the feed';
$this->content->template['message_5033']='Language ';
$this->content->template['message_5034']='Language of the RSS feed (1=German, 2=English) ';
$this->content->template['message_5035']='Prefix ';
$this->content->template['message_5036']='The Prefix for the Feed. With domains without www it reads http:// otherwise http://www. ';
$this->content->template['message_5037']='In the following you can add up to 10 RSS Feeds, which can be represented as articles in Papoo. ';
$this->content->template['message_5038']='Configuration feed ';
$this->content->template['message_5039']='Please add the URL to the feed. ';
$this->content->template['message_5040']='Error in the form: ';
$this->content->template['message_5041']='Feed URL ';
$this->content->template['message_5042']='Those contains of URL feed ';
$this->content->template['message_5043']='Data feed ';
$this->content->template['message_5044']='Feed provide ';
$this->content->template['message_5045']='With click on the Button specified down the feed one provides. ';
$this->content->template['message_5046']='The directory RSSCACHE must be adjusted with CHMOD 777, so that the feeds GEC eight to become to be able. The directory RSSFEED in the Plugins directory must be likewise adjusted with Chmod 777. ';
$this->content->template['message_5047']='RSS feed provide ';
$this->content->template['message_5048']='Provide ';
$this->content->template['message_5049']='External feeds merge ';
$this->content->template['plugin']['rssfeed']['error1']='Please  indicate a title for the feed. ';
$this->content->template['plugin']['rssfeed']['error2']='Please indicate a description for the feed. ';
$this->content->template['plugin']['rssfeed']['error3']='Only numbers for field to number of articles permits ';
$this->content->template['plugin']['rssfeed']['error4']='The number of articles must be larger than 0 ';
$this->content->template['plugin']['rssfeed']['error5']='Field number of articles does not contain a number ';
$this->content->template['plugin']['rssfeed']['error6']='Only numbers for field to language permits ';
$this->content->template['plugin']['rssfeed']['error7']='The language must be larger than 0 ';
$this->content->template['plugin']['rssfeed']['error8']='Field language does not contain a number ';

?>