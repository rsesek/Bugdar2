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

</table>

<? InsertView('footer') ?>