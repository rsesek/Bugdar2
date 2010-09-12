<?/*
Header Template Variables:
--------------------------
title - Used as the title of the web page.
*/?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>$[title] - Bugdar 2 Beta 1</title>
</head>

<div id="header">
  <ul id="navigation">
    <li><a href="<?= EventLink('BugList') ?>">List</a></li>
    <li>Report</li>
    <li>Options</li>
  </ul>
  <div id="userinfo">
    <? if (Bugdar::$auth->IsLoggedIn()): ?>
      <?= Bugdar::$auth->current_user()->alias ?>
      (<?= Bugdar::$auth->current_user()->email ?>)
    <? else: ?>
      <a href="<?= EventLink('UserLogin') ?>">Not Logged In</a>
    <? endif ?>
  </div>
</div>

<body>
