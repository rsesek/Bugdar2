<? InsertView('header', array('title' => l10n::S('BUG_LIST_TITLE'))) ?>

<table id="bug-list">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Reporter</th>
        <th>Date</th>
    </tr>  
    <? foreach ($this->bugs as $bug): ?>
    <tr>
        <td><a href="<?= EventLink('BugView', $bug->bug_id) ?>"><?= Cleaner::HTML($bug->bug_id) ?></a></td>
        <td><a href="<?= EventLink('BugView', $bug->bug_id) ?>"><?= Cleaner::HTML($bug->title) ?></a></td>
        <td><a href="<?= EventLink('UserView', $bug->reporting_alias) ?>"><?= Cleaner::HTML($bug->reporting_alias) ?></a></td>
        <td><?= gmdate('r', $bug->reporting_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></td>
    </tr>
    <? endforeach ?>
</table>

<? InsertView('footer') ?>