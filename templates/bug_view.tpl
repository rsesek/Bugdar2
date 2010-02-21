<script type="text/javascript" src="<?= WebRoot('js/attributes.js') ?>"></script>

<form action="<?= EventLink('BugEdit') ?>" method="post">
<input type="hidden" name="bug_id" value="$[bug.bug_id]" />

<h1>Bug #$[bug.bug_id]: $[bug.title]</h1>

<dl>
    <dt>Reporter:</dt>
    <dd>$[bug.reporting_alias]</dd>

    <dt>Date:</dt>
    <dd><?= gmdate('r', $this->bug->reporting_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></dd>
</dl>

<div><strong>Attributes</strong></div>
<div><a href="javascript:AddAttribute()">Add Attribute</a></div>

<dl id="attributes">
</dl>

<h2>Comments</h2>

<? foreach ($this->comments as $comment): ?>
<div>
    <strong>Posted by <a href="<?= EventLink('UserView', $comment->post_alias) ?>"><?= Cleaner::HTML($comment->post_alias) ?></a> on <?= gmdate('r', $comment->post_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></strong>
    <p><?= Cleaner::HTML($comment->body) ?></p>
</div>
<? endforeach ?>

<h1>Add Comment</h1>
<textarea name="comment_body" rows="8" cols="40"></textarea>

<div><input type="submit" name="submit" value="Save Changes" id="submit" /></div>

</form>

<script type="text/javascript" charset="utf-8">
<? foreach ($this->bug->attributes as $attr): ?>
    AddAttribute("<?= Cleaner::HTML($attr->attribute_title) ?>", "<?= Cleaner::HTML($attr->value) ?>");
<? endforeach ?>
    AddAttribute();
</script>
