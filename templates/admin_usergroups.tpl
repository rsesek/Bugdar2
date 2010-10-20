<? InsertView('header', array('title' => l10n::S('ADMIN_USERGROUPS_TITLE'))) ?>

<? Insertview('admin_sidebar') ?>

<table id="settings">
  <tr>
    <th><?= l10n::S('ADMIN_USERGROUP_TITLE') ?></th>
    <th><?= l10n::S('ADMIN_USERGROUP_HAS_MASK') ?></th>
  </tr>

<? foreach ($this->usergroups as $usergroup): ?>
  <tr>
    <td><a href="<?= EventLink('AdminUsergroupsEdit', $usergroup->usergroup_id) ?>"><?= $this->HTML($usergroup->title) ?></a></td>
    <td><?= ($usergroup->mask) ? l10n::S('ADMIN_USERGROUP_ROLE_GROUP') : l10n::S('ADMIN_USERGROUP_PURE_GROUP') ?></td>
  </tr>
<? endforeach?>

  <tr>
    <td colspan="2" style="background-color: lightgray">
      <a href="<?= EventLink('AdminUsergroupsEdit') ?>"><input type="button" name="add_group" value="<?= l10n::S('ADMIN_USERGROUP_NEW') ?>" /></a>
    </td>
  </tr>

</table>

<? InsertView('footer') ?>