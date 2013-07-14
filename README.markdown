Quick RSS Post, Iteration 3
===========================

A simple little thing to let me quickly write a note to myself (or bookmark a page) from anywhere, to an RSS feed.

Features:
 - Text input box for quick or long writing
 - Bookmarklet for easy, one-click bookmarking of a page
 - Templates for every view/message
 - MySQL storage
 - RSS generated from MySQL
 - Somewhat configurable


Why might you need this?
------------------------

Honestly, it's utility is somewhat fading. I originally wanted the idea when I was working a couple jobs where I was bounced around from one computer to another all the time, multiple times a day, so no computer was "my own". Also, these computers were all fairly adware/bloatware/virus-laden, so I really did not feel comfortable even logging in to my email account. I still needed to write notes and remember links (for me personally, not professionally).

These days, I still use it in that kind of a situation, even though I do have my own computer at work now, since it can be nice to send something direct to an RSS feed I'll check later. Also, I use it on my phone and ipad to effectively send links to my desktop. Your utility and enjoyment may vary.


History
-------

Originally, Iteration 1 was designed to work only from the address bar, for the ultimate speed and ease. And it did work. But it had some serious issues:
 - Horrible code -- So so so bad, since it was whipped up super duper quickly.
 - Ghastly security holes -- Due to its very nature, and its speed of development, it was like a big kick-me sign.
 - Spooky failures on certain characters -- Eventually I realized some query strings were being dropped, and some other reserved characters in URLs weren't doing so well either.
 - Shockingly poor RSS -- The frankenstein rss classes just kept adding to the RSS file, until various feed readers decided they had had enough.
 - Frightful to maintain -- I was trying to keep it small and barebones, and may have gone too far.

Some of those issues I knew about from the beginning but chose to ignore, others only occurred to me later, but they all drove me insane after a while. I found myself emailing or hand-writing notes again instead of using this, even though that was exactly what I had hoped this would cure. So, I set out to re-do it, and thus was born Iteration 2.

Iteration 2 solved (or was going to solve) all (or most) of those issues, and provide some new features that occurred to me after much use. But for some reason I still wanted to avoid databases, and I kept with my weird apache rewrite rules (and even tried to make more, complex ones). And since this was all building off the original shoddy base, it was becoming even more a nightmare to work with. Eventually I realized I had taken the wrong design route, and I rethought a lot of things and began Iteration 3.

Compared to previoud iterations, this current 3rd Iteration is a ground-up rewrite. It stores entries in a database, and has left behind the neat but impossible to work perfectly address-bar-only input scheme. Everything is encoded and/or escaped in the right places now, so you don't have to avoid such useful characters as question marks. And while still very very far from ideal, the code is fairly simple, modular-ish and easy to maintain and configure. (Maybe sometime I'll even add tests!) I'm fairly confident that any future versions I release will all be versions of this iteration.


