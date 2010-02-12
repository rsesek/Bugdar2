<h1>Bug #$[bug.bug_id]: $[bug.title]</h1>

<dl>
    <dt>Reporter:</dt>
    <dd>$[bug.reporting_alias]</dd>

    <dt>Date:</dt>
    <dd><?= gmdate('r', $this->bug->reporting_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></dd>
</dl>

<h2>Comments</h2>

<? foreach ($this->comments as $comment): ?>
<div>
    <strong>Posted by <a href="view_user/<?= Cleaner::HTML($comment->post_alias) ?>"><?= Cleaner::HTML($comment->post_alias) ?></a> on <?= gmdate('r', $comment->post_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></strong>
    <p><?= Cleaner::HTML($comment->body) ?></p>
</div>
<? endforeach ?>

<form action="<?= EventLink('CommentNew') ?>" method="post">
    <input type="hidden" name="bug_id" value="$[bug.bug_id]" />
    <input type="hidden" name="do" value="submit" />
    <textarea name="body" rows="8" cols="40"></textarea>
    <input type="submit" name="submit" value="Add Comment" id="submit" />
</form>
