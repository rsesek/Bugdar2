<? InsertView('header', array('title' => l10n::S('ADMIN_SETTINGS_TITLE'))) ?>

<form action="<?= EventLink('AdminSettings') ?>" method="post">

<table id="settings">
  <tr>
    <th>
      <label for="settings[webroot]"><?= l10n::S('ADMIN_SETTINGS_WEBROOT_VAR') ?></label>
      <dfn><?= l10n::S('ADMIN_SETTINGS_WEBROOT_DFN') ?></dfn>
    </th>
    <td><input type="text" name="settings[webroot]" value="$[settings.webroot]" id="settings[webroot]"></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" name="submit" value="<?= l10n::S('ADMIN_SETTINGS_SAVE') ?>" id="submit"></td>
  </tr>
</table>

</form>

<? InsertView('footer') ?>