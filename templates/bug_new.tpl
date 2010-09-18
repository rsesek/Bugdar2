<?= InsertView('header', array('title' => l10n::S('BUG_NEW_TITLE'))) ?>

<h1>New Bug</h1>

<form action="<?= EventLink('BugNew') ?>" method="post">
<input type="hidden" name="do" value="submit" />

<dl>
  <dt>Title:</dt>
  <dd><input type="text" name="title" value="" id="title"></dd>

  <dt>Description:</dt>
  <dd><textarea name="comment_body" rows="8" cols="40"></textarea></dd>
</dl>

<p><input type="submit" value="Continue &rarr;"></p>
</form>

<? InsertView('footer') ?>