<? InsertView('header', array('title' => l10n::S('USER_LOGIN_TITLE'))) ?>

<h1><?= l10n::S('USER_LOGIN_TITLE') ?></h1>

<form action="<?= EventLink('UserLogin') ?>" method="post">
<input type="hidden" name="do" value="fire" />
<input type="hidden" name="last_event" value="$[last_event]" />

<dl>
  <dt><?= l10n::S('USER_LOGIN_EMAIL') ?>:</dt>
  <dd><input type="text" name="email" value="" id="email"></dd>

  <dt><?= l10n::S('USER_LOGIN_PASSWORD') ?>:</dt>
  <dd><input type="password" name="password" value="" id="password"></dd>
</dl>

<p><input type="submit" value="Continue &rarr;"></p>
</form>

<? InsertView('footer') ?>