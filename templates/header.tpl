<?/*
Header Template Variables:
--------------------------
title - Used as the title of the web page.
*/?>
<!DOCTYPE html>
<html lang="<?= l10n::Instance()->GetLanguage()->code() ?>">
<head>
  <title>$[title] - Bugdar 2 Beta 1</title>
  <link rel="stylesheet" href="<?= WebRoot() ?>css/reset.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?= WebRoot() ?>css/master.css" type="text/css" media="screen" />
</head>

<div id="header">
  <div id="userinfo">
    <? if (Bugdar::$auth->IsLoggedIn()): ?>
      <?= Bugdar::$auth->current_user()->alias ?>
      (<?= Bugdar::$auth->current_user()->email ?>)
    <? else: ?>
      <a href="<?= EventLink('UserLogin') ?>">Not Logged In</a>
    <? endif ?>
  </div>

  <ul id="navigation">
    <li><a href="<?= EventLink('BugList') ?>">List</a></li>
    <li>Report</li>
    <li>Options</li>
  </ul>
</div>

<body>
