<?/*
Header Template Variables:
--------------------------
title - Used as the title of the web page.
*/?>
<!DOCTYPE html>
<html lang="<?= l10n::Instance()->GetLanguage()->code() ?>">
<head>
  <title>$[title] - <?= Cleaner::HTML(Bugdar::$settings['tracker_name']) ?></title>
  <link rel="stylesheet" href="<?= WebRoot('css/reset.css') ?>" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?= WebRoot('css/master.css') ?>" type="text/css" media="screen" />
</head>

<body>

<div id="header">
  <div id="userinfo">
    <? if (Bugdar::$auth->IsLoggedIn()): ?>
      <?= Bugdar::$auth->current_user()->alias ?>
      (<?= Bugdar::$auth->current_user()->email ?>)
    <? else: ?>
      <a href="<?= EventLink('UserLogin') ?>">Not Logged In</a>
    <? endif ?>
  </div>

  <h1><?= Cleaner::HTML(Bugdar::$settings['tracker_name']) ?></h1>
  <ul id="navigation">
    <li><a href="<?= EventLink('BugList') ?>">List</a></li>
    <li><a href="<?= EventLink('BugNew') ?>">Report</a></li>
    <li><a href="<?= EventLink('AdminSettings') ?>">Options</a></li>
  </ul>
</div>

<!-- wrapper -->
<div id="<?= ($this->disable_wrapper) ? 'no-wrapper' : 'wrapper' ?>">
