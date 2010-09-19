<?
  $title = ($this->action == 'insert') ?
      l10n::S('BUG_NEW_TITLE') :
      l10n::F('BUG_EDIT_TITLE', $this->bug->bug_id, Cleaner::HTML($this->bug->title));
  InsertView('header', array('title' => $title, 'disable_wrapper' => TRUE));
?>

<!-- bug-content -->
<div id="bug-content">

<script type="text/javascript" src="<?= WebRoot('js/attributes.js') ?>"></script>

<form action="<?= EventLink('BugEdit') ?>" method="post">
<? if ($this->action == 'update'): ?>
<input type="hidden" name="bug_id" value="$[bug.bug_id]" />
<? endif ?>
<input type="hidden" name="action" value="$[action]" id="action">

<h1 id="bug-title">
  <input type="text" name="title" value="$[bug.title]" id="title">
</h1>

<div id="bug-comments">
  <? if ($this->action != 'insert'): ?>
    <? foreach ($this->comments as $comment): ?>
    <div>
      <strong>Posted by <a href="<?= EventLink('UserView', $comment->post_alias) ?>"><?= Cleaner::HTML($comment->post_alias) ?></a> on <?= gmdate('r', $comment->post_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></strong>
      <p><?= Cleaner::HTML($comment->body) ?></p>
    </div>
    <? endforeach ?>
  <? endif ?>

  <h1><?= ($this->action == 'update' ? 'Add Comment' : 'Description') ?></h1>
  <textarea name="comment_body" rows="8" cols="40"></textarea>
</div>

<div id="metadata">
  <dl id="attributes">
    <? if ($this->bug->bug_id): ?>
      <dt>Bug ID:</dt>
      <dd>$[bug.bug_id]</dd>
    <? endif ?>
    <dt>Reporter:</dt>
    <dd>$[bug_reporter.alias]</dd>

    <? if ($this->bug->reporting_date): ?>
      <dt>Date:</dt>
      <dd><?= gmdate('j M. Y \a\t H:i', $this->bug->reporting_date + (Bugdar::$auth->current_user()->timezone * 3600)) ?></dd>
    <? endif ?>
  </dl>

  <div class="clear"></div>

  <div><a href="javascript:AddAttribute()">Add Attribute</a></div>
  <div><input type="submit" name="submit" value="Save Changes" id="submit" /></div>
</div>

</form>

<script type="text/javascript" charset="utf-8">
<? foreach ((array)$this->attributes as $attr): ?>
  AddAttribute("<?= Cleaner::HTML($attr->attribute_title) ?>", "<?= Cleaner::HTML($attr->value) ?>");
<? endforeach ?>
  AddAttribute();
</script>

</div>
<!-- / bug-content -->

<?= InsertView('footer') ?>