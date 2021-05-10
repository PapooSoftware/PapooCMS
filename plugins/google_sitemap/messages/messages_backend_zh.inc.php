<?php 
$this->content->template['plugin']['google_sitemap']['text'] = "<h2>谷歌网站地图插件</h2><p>有了这个插件，你可以创建谷歌网站地图。</p>"; 
$this->content->template['plugin']['google_sitemap']['change'] = "改变谷歌网站地图的数据"; 
$this->content->template['plugin']['google_sitemap']['text2'] = "指定你的页面的更新频率，以及条目应具有的优先级。"; 
$this->content->template['plugin']['google_sitemap']['changefreq'] = "选择变化频率。"; 
$this->content->template['plugin']['google_sitemap']['prioritaet'] = "选择优先级。"; 
$this->content->template['plugin']['google_sitemap']['eintragen'] = "进入"; 
$this->content->template['plugin']['google_sitemap']['erlaeuterung'] = "解释一下。"; 
$this->content->template['plugin']['google_sitemap']['text3'] = "<p><b>changefreq:</b> <br />预计页面变化的频率。这个值为搜索引擎提供一般信息。它不一定与你抓取页面的频率有关。有效值是。<br />"; 
$this->content->template['plugin']['google_sitemap']['text4'] = "值 \"always \"用于描述每次访问都会改变的文件。值 \"从未 \"用于描述存档的URL。<br /> <br />这个标签的值被当作一个提示，而不是一个命令。搜索引擎爬虫在做决定时确实会考虑到这些信息。然而，他们抓取标记为 \"每小时 \"的网页的频率可能低于每小时，或者标记为 \"每年 \"的网页的频率可能高于每年。即使是标记为 \"从不 \"的页面，也有可能在一定的时间间隔内被爬虫抓取，以检测此类页面的意外变化。<br /></p><p><b>优先权:</b> <br />这个URL相对于你网站上其他URL的优先权。这个值并不影响你的网页与其他网站的网页进行比较，它只是通知搜索引擎哪些网页对你具有最高的优先权。然后在此基础上对网页进行抓取。<br /> <br />一个页面的默认优先级是0.5。<br /> <br />你给一个页面指定的优先级不会影响你的URL在搜索引擎结果页面中的位置。这些信息只被搜索引擎用来在同一网站的URL之间进行选择。因此，使用这个标签可以增加你的更重要的网页被列入搜索索引的可能性。<br /> <br />同样，给你网站的所有URL分配高优先级也不是一个好主意。由于优先权是相对的，它只用于在你自己网站内的URL之间进行选择。你的网页的优先级不与其他网站上的网页的优先级相比较。<br /></p>"; 
$this->content->template['plugin']['google_sitemap']['ready'] = "谷歌网站地图已经创建。"; 
$this->content->template['plugin']['google_sitemap']['link'] = "你的谷歌账户的链接。"; 
$this->content->template['plugin']['google_sitemap']['error'] = "无法创建谷歌网站地图。"; 
$this->content->template['plugin']['google_sitemap']['datei'] = "该文件 "; 
$this->content->template['plugin']['google_sitemap']['datei2'] = "存在，但不能被覆盖。请改变该文件的访问权限（公开的写入权限）。"; 
$this->content->template['plugin']['google_sitemap']['gespeichert'] = "网站地图已被保存。"; 
$this->content->template['plugin']['google_sitemap']['ordner'] = "文件夹 "; 
$this->content->template['plugin']['google_sitemap']['ordner2'] = "不能写。请通过ftp更改访问权限。或者保存一个空文件\" . $filename .\"在htdocs目录下，并改变文件的访问权限（公开的写入权限）。"; 
$this->content->template['plugin']['google_sitemap']['geaendert'] = "日期已被更改"; 

 ?>