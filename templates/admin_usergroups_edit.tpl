<? InsertView('header', array('title' => l10n::S('ADMIN_USERGROUPS_TITLE'))) ?>

<? Insertview('admin_sidebar') ?>

<form action="<?= EventLink('AdminUsergroupsEdit', $this->usergroup->usergroup_id) ?>" method="post">
<input type="hidden" name="_id" value="$[usergroup.usergroup_id]" />

<table id="settings">
  <tr>
    <th><label for="title"><?= l10n::S('ADMIN_USERGROUP_TITLE') ?></label></th>
    <td><input type="text" name="title" value="$[usergroup.title]" id="title"></th>
  </tr>
  <tr>
    <th><label for="title"><?= l10n::S('ADMIN_USERGROUP_DISPLAY_TITLE') ?></label></th>
    <td><input type="text" name="display_title" value="$[usergroup.display_title]" id="display_title"></th>
  </tr>
  <tr>
    <th colspan="2" style="text-align: center; background-color: lightgray"><?= l10n::S('ADMIN_USERGROUP_PERMISSIONS') ?></th>
  </tr>
  <? foreach (Usergroup::$permissions as $name => $value): ?>
    <tr>
      <td colspan="2" style="text-align: left">
        <? $on = ($this->usergroup->mask & $value) ?>
        <input type="checkbox" name="permissions[<?= $name ?>]" value="1" id="permissions[<?= $name ?>]" <?= $on ? ' checked="checked"' : '' ?> />
        <label for="permissions[<?= $name ?>]"><?= l10n::S('ADMIN_USERGROUP_PERMISSION_' . $name) ?></label>
      </td>
    </tr>
  <? endforeach ?>
  <tr>
    <th colspan="2" style="text-align: center; background-color: lightgray">
      <input type="submit" name="submit" value="<?= l10n::S('BUTTON_SAVE_CHANGES') ?>" id="submit" />
      <input type="reset" name="reset" value="<?= l10n::S('BUTTON_RESET') ?>" id="reset" />
    </th>
  </tr>
</table>

</form>

<? InsertView('footer') ?>