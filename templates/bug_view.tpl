<?
  $title = ($this->action == 'insert') ?
      l10n::S('BUG_NEW_TITLE') :
      l10n::F('BUG_EDIT_TITLE', $this->bug->bug_id, Cleaner::HTML($this->bug->title));
  InsertView('header', array('title' => $title))
?>

<script type="text/javascript" src="<?= WebRoot('js/attributes.js') ?>"></script>

<form action="<?= EventLink('BugEdit') ?>" method="post">
<? if ($this->action == 'update'): ?>
<input type="hidden" name="bug_id" value="$[bug.bug_id]" />
<? endif ?>
<input type="hidden" name="action" value="$[action]" id="action">

<h1>Bug #$[bug.bug_id]: <input type="text" name="title" value="$[bug.title]" id="title"></h1>

<dl id="attributes">
  <dt>Reporter:</dt>
  <dd>$[bug_reporter.alias]</dd>

  <dt>Date:</dt>
  <dd><?= gmdate('r', $this->bug->reporting_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></dd>
</dl>

<div><a href="javascript:AddAttribute()">Add Attribute</a></div>

<? if ($this->action != 'insert'): ?>
  <h2>Comments</h2>
  <? foreach ($this->comments as $comment): ?>
  <div>
    <strong>Posted by <a href="<?= EventLink('UserView', $comment->post_alias) ?>"><?= Cleaner::HTML($comment->post_alias) ?></a> on <?= gmdate('r', $comment->post_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></strong>
    <p><?= Cleaner::HTML($comment->body) ?></p>
  </div>
  <? endforeach ?>
<? endif ?>

<h1><?= ($this->action == 'update' ? 'Add Comment' : 'Description') ?></h1>
<textarea name="comment_body" rows="8" cols="40"></textarea>

<div><input type="submit" name="submit" value="Save Changes" id="submit" /></div>

</form>

<script type="text/javascript" charset="utf-8">
<? foreach ((array)$this->attributes as $attr): ?>
  AddAttribute("<?= Cleaner::HTML($attr->attribute_title) ?>", "<?= Cleaner::HTML($attr->value) ?>");
<? endforeach ?>
  AddAttribute();
</script>

<?= InsertView('footer') ?>